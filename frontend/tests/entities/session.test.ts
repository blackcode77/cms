import { beforeEach, describe, expect, it } from 'vitest'
import { registerEndpoint } from '@nuxt/test-utils/runtime'
import { resolveAdminRoute, useCurrentUser } from '@entities/session'

const me = {
  user: { id: 'u1', email: 'a@b.c', name: 'Ann' },
  role: { id: 'r1', name: 'editor' },
  permissions: ['view_dashboard', 'edit_page'],
  must_change_password: false,
}

let meCalls = 0
let refreshCalls = 0

function meEndpoint(handler: () => unknown) {
  registerEndpoint('/api/v1/auth/me', () => {
    meCalls++
    return handler()
  })
}

function refreshEndpoint(ok: boolean) {
  registerEndpoint('/api/v1/auth/refresh', {
    method: 'POST',
    handler: () => {
      refreshCalls++
      if (!ok) throw createError({ statusCode: 401 })
      return { status: 'success', data: null }
    },
  })
}

describe('session', () => {
  beforeEach(() => {
    meCalls = 0
    refreshCalls = 0
    clearNuxtState('session')
  })

  describe('useCurrentUser().can()', () => {
    it('reflects the permissions from /auth/me', async () => {
      meEndpoint(() => ({ status: 'success', data: me }))
      const current = useCurrentUser()
      await current.load()

      expect(current.can('edit_page')).toBe(true)
      expect(current.can('manage_users')).toBe(false)
      expect(current.user.value?.email).toBe('a@b.c')
    })

    it('denies everything for a guest', async () => {
      meEndpoint(() => {
        throw createError({ statusCode: 401 })
      })
      refreshEndpoint(false)
      const current = useCurrentUser()
      await current.load()

      expect(current.isAuthenticated.value).toBe(false)
      expect(current.can('view_dashboard')).toBe(false)
    })

    it('refreshes once on 401 and retries /auth/me', async () => {
      let first = true
      meEndpoint(() => {
        if (first) {
          first = false
          throw createError({ statusCode: 401 })
        }
        return { status: 'success', data: me }
      })
      refreshEndpoint(true)
      const current = useCurrentUser()
      await current.load()

      expect(refreshCalls).toBe(1)
      expect(meCalls).toBe(2)
      expect(current.isAuthenticated.value).toBe(true)
    })
  })

  describe('resolveAdminRoute (route guard)', () => {
    it('sends a guest from /admin to the login page', async () => {
      meEndpoint(() => {
        throw createError({ statusCode: 401 })
      })
      refreshEndpoint(false)

      expect(await resolveAdminRoute('/admin')).toBe('/admin/login')
      expect(await resolveAdminRoute('/admin/pages')).toBe('/admin/login')
    })

    it('lets an authenticated user through', async () => {
      meEndpoint(() => ({ status: 'success', data: me }))

      expect(await resolveAdminRoute('/admin')).toBeUndefined()
    })

    it('redirects to the dashboard when the route permission is missing', async () => {
      meEndpoint(() => ({ status: 'success', data: me }))

      expect(await resolveAdminRoute('/admin/roles', 'view_roles')).toBe('/admin')
    })

    it('lets a user holding the route permission through', async () => {
      meEndpoint(() => ({ status: 'success', data: me }))

      expect(await resolveAdminRoute('/admin/pages', 'edit_page')).toBeUndefined()
    })

    it('leaves non-admin routes alone without calling the API', async () => {
      meEndpoint(() => {
        throw createError({ statusCode: 401 })
      })

      expect(await resolveAdminRoute('/about')).toBeUndefined()
      expect(meCalls).toBe(0)
    })

    it('keeps the login page public for guests', async () => {
      meEndpoint(() => {
        throw createError({ statusCode: 401 })
      })
      refreshEndpoint(false)

      expect(await resolveAdminRoute('/admin/login')).toBeUndefined()
    })

    it('redirects to the dashboard when the route needs a permission the user lacks', async () => {
      meEndpoint(() => ({ status: 'success', data: me }))

      expect(await resolveAdminRoute('/admin/users', 'view_users')).toBe('/admin')
    })

    it('lets the user in when they hold the route permission', async () => {
      meEndpoint(() => ({ status: 'success', data: me }))

      expect(await resolveAdminRoute('/admin/pages', 'edit_page')).toBeUndefined()
    })

    it('sends a guest to login (not the dashboard) for a permission-gated route', async () => {
      meEndpoint(() => {
        throw createError({ statusCode: 401 })
      })
      refreshEndpoint(false)

      expect(await resolveAdminRoute('/admin/users', 'view_users')).toBe('/admin/login')
    })

    it('never redirects the dashboard to itself', async () => {
      meEndpoint(() => ({ status: 'success', data: me }))

      expect(await resolveAdminRoute('/admin', 'view_users')).toBeUndefined()
    })

    describe('forced password change', () => {
      const flagged = { ...me, must_change_password: true }

      it('redirects a flagged user from any admin page to change-password', async () => {
        meEndpoint(() => ({ status: 'success', data: flagged }))

        expect(await resolveAdminRoute('/admin')).toBe('/admin/change-password')
        expect(await resolveAdminRoute('/admin/pages', 'edit_page')).toBe('/admin/change-password')
      })

      it('lets a flagged user open the change-password page itself', async () => {
        meEndpoint(() => ({ status: 'success', data: flagged }))

        expect(await resolveAdminRoute('/admin/change-password')).toBeUndefined()
      })

      it('sends a flagged user from the login page to change-password', async () => {
        meEndpoint(() => ({ status: 'success', data: flagged }))

        expect(await resolveAdminRoute('/admin/login')).toBe('/admin/change-password')
      })

      it('sends a guest from change-password to login', async () => {
        meEndpoint(() => {
          throw createError({ statusCode: 401 })
        })
        refreshEndpoint(false)

        expect(await resolveAdminRoute('/admin/change-password')).toBe('/admin/login')
      })

      it('does not trap an unflagged user', async () => {
        meEndpoint(() => ({ status: 'success', data: me }))

        expect(await resolveAdminRoute('/admin/change-password')).toBeUndefined()
        expect(await resolveAdminRoute('/admin')).toBeUndefined()
      })
    })

    it('sends a signed-in user away from the login page to the dashboard', async () => {
      meEndpoint(() => ({ status: 'success', data: me }))

      expect(await resolveAdminRoute('/admin/login')).toBe('/admin')
    })
  })

  it('signOut ends the session', async () => {
    meEndpoint(() => ({ status: 'success', data: me }))
    let loggedOut = false
    registerEndpoint('/api/v1/auth/logout', {
      method: 'POST',
      handler: () => {
        loggedOut = true
        return { status: 'success', data: null }
      },
    })
    const current = useCurrentUser()
    await current.load()
    await current.signOut()

    expect(loggedOut).toBe(true)
    expect(current.isAuthenticated.value).toBe(false)
  })
})

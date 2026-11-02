import { readBody } from 'h3'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { ChangePasswordForm } from '@features/change-password'
import { useCurrentUser } from '@entities/session'

const flagged = {
  user: { id: 'u1', email: 'a@b.c', name: 'Ann' },
  role: { id: 'r1', name: 'x' },
  permissions: ['view_dashboard'],
  must_change_password: true,
}

describe('ChangePasswordForm', () => {
  beforeEach(() => clearNuxtState('session'))

  it('sends both passwords, reloads the session and emits changed', async () => {
    let body: unknown
    registerEndpoint('/api/v1/auth/change-password', {
      method: 'POST',
      handler: async (event) => {
        body = await readBody(event)
        return { status: 'success', data: null }
      },
    })
    registerEndpoint('/api/v1/auth/me', () => ({ status: 'success', data: { ...flagged, must_change_password: false } }))
    useCurrentUser().session.value = flagged

    const wrapper = await mountSuspended(ChangePasswordForm)
    const inputs = wrapper.findAll('input')
    await inputs[0]!.setValue('old-pass')
    await inputs[1]!.setValue('new-password')
    await wrapper.find('form').trigger('submit')

    await vi.waitFor(() => expect(wrapper.emitted('changed')).toHaveLength(1))
    expect(body).toEqual({ current_password: 'old-pass', new_password: 'new-password' })
    expect(useCurrentUser().mustChangePassword.value).toBe(false)
  })

  it('shows the server message when the current password is wrong', async () => {
    registerEndpoint('/api/v1/auth/change-password', {
      method: 'POST',
      handler: () => {
        throw createError({ statusCode: 400, data: { status: 'failed', error_message: 'Current password is incorrect.' } })
      },
    })

    const wrapper = await mountSuspended(ChangePasswordForm)
    const inputs = wrapper.findAll('input')
    await inputs[0]!.setValue('bad')
    await inputs[1]!.setValue('new-password')
    await wrapper.find('form').trigger('submit')

    await vi.waitFor(() => expect(wrapper.find('[data-testid="change-password-error"]').exists()).toBe(true))
    expect(wrapper.emitted('changed')).toBeUndefined()
  })
})

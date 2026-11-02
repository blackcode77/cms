import { beforeEach, describe, expect, it } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { AdminUsersPage } from '@pages/admin-users'
import { useCurrentUser } from '@entities/session'
import type { Permission } from '@entities/session'

function signInWith(permissions: Permission[]) {
  useCurrentUser().session.value = {
    user: { id: 'u1', email: 'a@b.c', name: 'Ann' },
    role: { id: 'r1', name: 'x' },
    permissions,
    must_change_password: false,
  }
}

describe('AdminUsersPage', () => {
  beforeEach(() => {
    clearNuxtData('admin-users')
    clearNuxtData('admin-users-roles')
    registerEndpoint('/api/v1/users', () => ({
      status: 'success',
      data: [
        {
          id: 'u9',
          email: 'bob@example.com',
          name: 'Bob',
          role: { id: 'r2', name: 'manager' },
          must_change_password: true,
          created_at: '2026-01-01T00:00:00+00:00',
        },
      ],
    }))
    registerEndpoint('/api/v1/users/assignable-roles', () => ({
      status: 'success',
      data: [{ id: 'r2', name: 'manager' }],
    }))
  })

  it('lists users with their role for a view_users holder', async () => {
    signInWith(['view_users'])

    const wrapper = await mountSuspended(AdminUsersPage)

    expect(wrapper.text()).toContain('bob@example.com')
    expect(wrapper.text()).toContain('manager')
  })

  it('hides the create form without manage_users', async () => {
    signInWith(['view_users'])

    const wrapper = await mountSuspended(AdminUsersPage)

    expect(wrapper.find('[data-testid="create-user-form"]').exists()).toBe(false)
  })

  it('shows the create form with manage_users', async () => {
    signInWith(['view_users', 'manage_users'])

    const wrapper = await mountSuspended(AdminUsersPage)

    expect(wrapper.find('[data-testid="create-user-form"]').exists()).toBe(true)
  })

  it('hides edit and delete actions without manage_users', async () => {
    signInWith(['view_users'])

    const wrapper = await mountSuspended(AdminUsersPage)

    expect(wrapper.find('[data-testid="user-edit"]').exists()).toBe(false)
    expect(wrapper.find('[data-testid="user-delete"]').exists()).toBe(false)
  })

  it('shows edit and delete actions per row with manage_users', async () => {
    signInWith(['view_users', 'manage_users'])

    const wrapper = await mountSuspended(AdminUsersPage)

    expect(wrapper.findAll('[data-testid="user-edit"]')).toHaveLength(1)
    expect(wrapper.findAll('[data-testid="user-delete"]')).toHaveLength(1)
  })
})

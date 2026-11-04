import { beforeEach, describe, expect, it } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { AdminRolesPage } from '@pages/admin-roles'
import { adminMenuItems, visibleMenuItems } from '@widgets/admin-sidebar'

const all = ['view_dashboard', 'edit_page', 'view_users', 'manage_users', 'view_roles', 'manage_roles']

const roles = [
  { id: 'r1', name: 'admin', permissions: all, users_count: 1, read_only: true, deletable: false },
  { id: 'r2', name: 'manager', permissions: ['edit_page'], users_count: 0, read_only: false, deletable: true },
]

function signIn(permissions: string[]) {
  useState('session').value = {
    user: { id: 'u', email: 'a@b.c', name: 'Ann' },
    role: { id: 'r', name: 'x' },
    permissions,
    must_change_password: false,
  }
}

describe('Roles admin section', () => {
  beforeEach(() => {
    clearNuxtData('admin-roles')
    clearNuxtData('admin-permission-catalog')
    registerEndpoint('/api/v1/roles', () => ({ status: 'success', data: roles }))
    registerEndpoint('/api/v1/permissions', () => ({ status: 'success', data: all }))
  })

  it('is listed in the menu only for users with view_roles', () => {
    const labels = (permissions: string[]) =>
      visibleMenuItems(adminMenuItems, p => permissions.includes(p)).map(i => i.label)

    expect(labels(['view_dashboard'])).not.toContain('Роли')
    expect(labels(['view_dashboard', 'view_roles'])).toContain('Роли')
  })

  it('lists roles and disables editing and deleting of the Admin role', async () => {
    signIn(['view_roles', 'manage_roles'])
    const wrapper = await mountSuspended(AdminRolesPage)

    const rows = wrapper.findAll('[data-testid="role-row"]')
    expect(rows).toHaveLength(2)
    expect(rows[0]!.find('[data-testid="role-edit"]').attributes('disabled')).toBeDefined()
    expect(rows[0]!.find('[data-testid="role-delete"]').attributes('disabled')).toBeDefined()
    expect(rows[1]!.find('[data-testid="role-edit"]').attributes('disabled')).toBeUndefined()
  })

  it('hides write controls without manage_roles', async () => {
    signIn(['view_roles'])
    const wrapper = await mountSuspended(AdminRolesPage)

    expect(wrapper.findAll('[data-testid="role-row"]')).toHaveLength(2)
    expect(wrapper.find('[data-testid="role-create"]').exists()).toBe(false)
    expect(wrapper.find('[data-testid="role-edit"]').exists()).toBe(false)
  })
})

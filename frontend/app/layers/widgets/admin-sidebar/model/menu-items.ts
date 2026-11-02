import type { Permission } from '@entities/session'

export interface AdminMenuItem {
  label: string
  to: string
  icon?: string
  /** Item is shown only to Users holding this Permission; omitted means always shown. */
  permission?: Permission
}

/** Register new admin sections here, gated by the Permission that grants access. */
export const adminMenuItems: AdminMenuItem[] = [
  { label: 'Dashboard', icon: 'i-lucide-layout-dashboard', to: '/admin', permission: 'view_dashboard' },
  { label: 'Users', icon: 'i-lucide-users', to: '/admin/users', permission: 'view_users' },
  { label: 'Roles', icon: 'i-lucide-shield', to: '/admin/roles', permission: 'view_roles' },
]

export function visibleMenuItems<T extends { permission?: Permission }>(
  items: T[],
  can: (permission: Permission) => boolean,
): T[] {
  return items.filter(item => !item.permission || can(item.permission))
}

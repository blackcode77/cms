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
  { label: 'Главная', icon: 'i-lucide-layout-dashboard', to: '/admin', permission: 'view_dashboard' },
  { label: 'Заказы', icon: 'i-lucide-clipboard-list', to: '/admin/orders', permission: 'view_orders' },
  { label: 'Клиенты', icon: 'i-lucide-contact', to: '/admin/clients', permission: 'view_clients' },
  { label: 'Пользователи', icon: 'i-lucide-users', to: '/admin/users', permission: 'view_users' },
  { label: 'Роли', icon: 'i-lucide-shield', to: '/admin/roles', permission: 'view_roles' },
]

export function visibleMenuItems<T extends { permission?: Permission }>(
  items: T[],
  can: (permission: Permission) => boolean,
): T[] {
  return items.filter(item => !item.permission || can(item.permission))
}

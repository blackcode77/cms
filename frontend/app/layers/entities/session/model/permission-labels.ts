import type { Permission } from './types'

const labels: Record<Permission, string> = {
  view_dashboard: 'Просмотр главной',
  edit_page: 'Редактирование страниц',
  view_users: 'Просмотр пользователей',
  manage_users: 'Управление пользователями',
  view_roles: 'Просмотр ролей',
  manage_roles: 'Управление ролями',
  view_orders: 'Просмотр заказов',
  manage_orders: 'Управление заказами',
  handle_orders: 'Может быть менеджером заказа',
  view_clients: 'Просмотр клиентов',
  manage_clients: 'Управление клиентами',
}

/** Russian name of a Permission; unknown keys (e.g. added on the backend) fall back to the key. */
export const permissionLabel = (permission: string): string => labels[permission as Permission] ?? permission

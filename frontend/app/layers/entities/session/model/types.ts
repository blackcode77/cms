export type Permission =
  | 'view_dashboard'
  | 'edit_page'
  | 'view_users'
  | 'manage_users'
  | 'view_roles'
  | 'manage_roles'

/** Payload of `GET /auth/me`. */
export interface Session {
  user: { id: string, email: string, name: string }
  role: { id: string, name: string }
  permissions: Permission[]
  must_change_password: boolean
}

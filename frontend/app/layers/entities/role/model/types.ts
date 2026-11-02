import type { Permission } from '@entities/session'

/** Item of `GET /roles`. */
export interface Role {
  id: string
  name: string
  permissions: Permission[]
  users_count: number
  /** The Admin Role: always holds every Permission and cannot be edited or deleted. */
  read_only: boolean
  deletable: boolean
}

export interface RoleInput {
  name: string
  permissions: Permission[]
}

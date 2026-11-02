import { unwrap, useApi } from '@shared/api'
import type { ApiSuccess } from '@shared/api'
import type { CreateUserInput, CreateUserResult, ResetPasswordResult, RoleOption, UserListItem } from '../model/types'

export async function fetchUsers(): Promise<UserListItem[]> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<UserListItem[]>>('/v1/users'))
}

export async function fetchAssignableRoles(): Promise<RoleOption[]> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<RoleOption[]>>('/v1/users/assignable-roles'))
}

export async function createUser(input: CreateUserInput): Promise<CreateUserResult> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<CreateUserResult>>('/v1/users', { method: 'POST', body: input }))
}

export async function resetUserPassword(id: string): Promise<ResetPasswordResult> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<ResetPasswordResult>>(`/v1/users/${id}/reset-password`, { method: 'POST' }))
}

import { unwrap, useApi } from '@shared/api'
import type { ApiSuccess } from '@shared/api'
import type { CreateUserInput, CreateUserResult, RoleOption, UpdateUserInput, UserListItem } from '../model/types'

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

export async function updateUser(id: string, input: UpdateUserInput): Promise<void> {
  const api = useApi()
  unwrap(await api<ApiSuccess<unknown>>(`/v1/users/${id}`, { method: 'PUT', body: input }))
}

export async function deleteUser(id: string): Promise<void> {
  const api = useApi()
  unwrap(await api<ApiSuccess<unknown>>(`/v1/users/${id}`, { method: 'DELETE' }))
}

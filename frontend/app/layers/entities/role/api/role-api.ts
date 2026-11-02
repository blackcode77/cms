import { unwrap, useApi } from '@shared/api'
import type { ApiSuccess } from '@shared/api'
import type { Permission } from '@entities/session'
import type { Role, RoleInput } from '../model/types'

export async function fetchRoles(): Promise<Role[]> {
  return unwrap(await useApi()<ApiSuccess<Role[]>>('/v1/roles'))
}

/** The fixed Permission catalog that drives the checkboxes. */
export async function fetchPermissionCatalog(): Promise<Permission[]> {
  return unwrap(await useApi()<ApiSuccess<Permission[]>>('/v1/permissions'))
}

export async function createRole(input: RoleInput): Promise<void> {
  await useApi()('/v1/roles', { method: 'POST', body: input })
}

export async function updateRole(id: string, input: RoleInput): Promise<void> {
  await useApi()(`/v1/roles/${id}`, { method: 'PUT', body: input })
}

export async function deleteRole(id: string): Promise<void> {
  await useApi()(`/v1/roles/${id}`, { method: 'DELETE' })
}

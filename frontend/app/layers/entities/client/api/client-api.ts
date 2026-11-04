import { unwrap, useApi } from '@shared/api'
import type { ApiSuccess } from '@shared/api'
import type { ClientInput, ClientItem } from '../model/types'

export async function fetchClients(search = ''): Promise<ClientItem[]> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<ClientItem[]>>('/v1/clients', { query: search ? { q: search } : {} }))
}

/** The Client with their Orders; the Orders are typed by the caller (the order entity owns their shape). */
export async function fetchClient<TOrder>(id: string): Promise<ClientItem & { orders: TOrder[] }> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<ClientItem & { orders: TOrder[] }>>(`/v1/clients/${id}`))
}

/** The Client an Order with this phone would go to, or null. */
export async function findClientByPhone(phone: string): Promise<ClientItem | null> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<ClientItem | null>>('/v1/clients/by-phone', { query: { phone } }))
}

export async function createClient(input: ClientInput): Promise<{ id: string }> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<{ id: string }>>('/v1/clients', { method: 'POST', body: input }))
}

export async function updateClient(id: string, input: ClientInput): Promise<void> {
  const api = useApi()
  unwrap(await api<ApiSuccess<unknown>>(`/v1/clients/${id}`, { method: 'PUT', body: input }))
}

/** Deletes the Client together with all their Orders. */
export async function deleteClient(id: string): Promise<void> {
  const api = useApi()
  unwrap(await api<ApiSuccess<unknown>>(`/v1/clients/${id}`, { method: 'DELETE' }))
}

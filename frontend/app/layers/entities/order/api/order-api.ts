import { unwrap, useApi } from '@shared/api'
import type { ApiSuccess } from '@shared/api'
import type {
  CreateOrderInput,
  CreateOrderResult,
  ManagerOption,
  OrderFilter,
  OrderItem,
  OrderRequestInput,
  UpdateOrderInput,
} from '../model/types'

export async function fetchOrders(filter: OrderFilter = {}): Promise<OrderItem[]> {
  const api = useApi()
  const query = Object.fromEntries(Object.entries(filter).filter(([, value]) => value))
  return unwrap(await api<ApiSuccess<OrderItem[]>>('/v1/orders', { query }))
}

export async function fetchManagers(): Promise<ManagerOption[]> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<ManagerOption[]>>('/v1/orders/managers'))
}

export async function createOrder(input: CreateOrderInput): Promise<CreateOrderResult> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<CreateOrderResult>>('/v1/orders', { method: 'POST', body: input }))
}

export async function updateOrder(id: string, input: UpdateOrderInput): Promise<void> {
  const api = useApi()
  unwrap(await api<ApiSuccess<unknown>>(`/v1/orders/${id}`, { method: 'PUT', body: input }))
}

export async function deleteOrder(id: string): Promise<void> {
  const api = useApi()
  unwrap(await api<ApiSuccess<unknown>>(`/v1/orders/${id}`, { method: 'DELETE' }))
}

/** Public Order request from the site form. */
export async function submitOrderRequest(input: OrderRequestInput): Promise<void> {
  const api = useApi()
  unwrap(await api<ApiSuccess<unknown>>('/v1/orders/request', { method: 'POST', body: input }))
}

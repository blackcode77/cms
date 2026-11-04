export type OrderStatus = 'request' | 'in_progress' | 'done'

/** Item of `GET /orders` and payload of `GET /orders/{id}`. */
export interface OrderItem {
  id: string
  number: number
  status: OrderStatus
  client: { id: string, phone: string, first_name: string, last_name: string, middle_name: string }
  manager: { id: string, name: string } | null
  from: string
  to: string
  cargo_type: string
  weight: string
  /** What the Client wrote. */
  message: string
  /** Internal note. */
  comment: string
  created_at: string
  updated_at: string
}

export interface CargoFields {
  from: string
  to: string
  cargo_type: string
  weight: string
}

/** Body of `PUT /orders/{id}`. */
export interface UpdateOrderInput extends CargoFields {
  status: OrderStatus
  manager_id: string | null
  message: string
  comment: string
}

/** Body of `POST /orders`: the Client (by phone) plus the Order. */
export interface CreateOrderInput extends UpdateOrderInput {
  phone: string
  first_name: string
  last_name: string
  middle_name: string
  email: string
}

export interface CreateOrderResult {
  id: string
  number: number
  client_id: string
}

/** Body of the public `POST /orders/request`; only the phone is required. */
export interface OrderRequestInput extends CargoFields {
  phone: string
  name: string
  email: string
  message: string
}

/** Item of `GET /orders/managers`. */
export interface ManagerOption {
  id: string
  name: string
  email: string
}

export interface OrderFilter {
  status?: OrderStatus
  /** `me` for the signed-in User. */
  manager?: string
  client_id?: string
}

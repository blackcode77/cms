import type { PersonName } from '@shared/lib'

/** Item of `GET /clients`. */
export interface ClientItem extends PersonName {
  id: string
  /** Normalized, e.g. `+79991234567`. */
  phone: string
  email: string | null
  comment: string
  orders_count: number
  created_at: string
}

/** Body of `POST /clients` and `PUT /clients/{id}`. */
export interface ClientInput extends PersonName {
  phone: string
  email: string
  comment: string
}

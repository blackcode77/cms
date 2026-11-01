import type { ApiSuccess } from './types'

export function unwrap<T>(response: ApiSuccess<T>): T {
  return response.data
}

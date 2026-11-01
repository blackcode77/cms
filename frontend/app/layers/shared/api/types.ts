/** Response envelope produced by backend `ResponseFactory` (see backend Presentation/Api/Shared). */
export interface ApiSuccess<T> {
  status: 'success'
  data: T
}

export interface ApiFailure {
  status: 'failed'
  error_message: string
  error_code?: number
  error_data?: unknown
}

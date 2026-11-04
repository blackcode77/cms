interface FetchErrorLike {
  statusCode?: number
  data?: { error_message?: string, error_data?: unknown }
}

/**
 * Message for a failed API call: the first field error of a validation failure (422), otherwise the
 * backend's error message, otherwise the fallback.
 */
export function apiErrorMessage(e: unknown, fallback: string): string {
  const err = e as FetchErrorLike | null
  const fields = err?.data?.error_data
  if (err?.statusCode === 422 && fields && typeof fields === 'object') {
    for (const messages of Object.values(fields as Record<string, unknown>)) {
      if (Array.isArray(messages) && typeof messages[0] === 'string') return messages[0]
    }
  }
  return err?.data?.error_message ?? fallback
}

/** Field errors of a validation failure (422): field name => first message. */
export function apiFieldErrors(e: unknown): Record<string, string> {
  const err = e as FetchErrorLike | null
  const fields = err?.data?.error_data
  const result: Record<string, string> = {}
  if (err?.statusCode !== 422 || !fields || typeof fields !== 'object') return result
  for (const [name, messages] of Object.entries(fields as Record<string, unknown>)) {
    if (Array.isArray(messages) && typeof messages[0] === 'string') result[name] = messages[0]
  }
  return result
}

import type { ApiSuccess } from './types'

/**
 * Backend client. During SSR it talks to the backend over the internal docker URL
 * and forwards the visitor's cookies; in the browser it uses the same-origin `/api`.
 */
export function useApi() {
  const config = useRuntimeConfig()
  const baseURL = import.meta.server ? config.apiInternalUrl : config.public.apiBase
  const headers = import.meta.server ? useRequestHeaders(['cookie']) : undefined

  return $fetch.create({ baseURL, headers })
}

export type { ApiSuccess }

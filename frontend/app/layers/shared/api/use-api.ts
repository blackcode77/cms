import type { $Fetch } from 'ofetch'
import type { ApiSuccess } from './types'

export const ADMIN_LOGIN_PATH = '/admin/login'
const REFRESH_PATH = '/v1/auth/refresh'

/** One refresh at a time: the refresh token rotates, so a second concurrent call would use a revoked token. */
let inflightRefresh: Promise<boolean> | null = null

function statusOf(error: unknown): number | undefined {
  const e = error as { statusCode?: number, status?: number } | null
  return e?.statusCode ?? e?.status
}

function isAuthEndpoint(request: unknown): boolean {
  return typeof request === 'string' && request.startsWith('/v1/auth/')
}

function refreshOnce(raw: $Fetch): Promise<boolean> {
  inflightRefresh ??= raw(REFRESH_PATH, { method: 'POST' })
    .then(() => true, () => false)
    .finally(() => {
      inflightRefresh = null
    })
  return inflightRefresh
}

/**
 * Backend client. During SSR it talks to the backend over the internal docker URL
 * and forwards the visitor's cookies; in the browser it uses the same-origin `/api`.
 *
 * In the browser a 401 from a (non-auth) call triggers ONE single-flight refresh and a single retry of the
 * original request; when the refresh fails the visitor is sent to the admin login.
 */
export function useApi(): $Fetch {
  const config = useRuntimeConfig()
  const baseURL = import.meta.server ? config.apiInternalUrl : config.public.apiBase
  const headers = import.meta.server ? useRequestHeaders(['cookie']) : undefined

  const raw = $fetch.create({ baseURL, headers })
  if (import.meta.server) return raw

  const nuxtApp = useNuxtApp()
  const api = (async (request: Parameters<$Fetch>[0], options?: Parameters<$Fetch>[1]) => {
    try {
      return await raw(request, options)
    }
    catch (error) {
      if (statusOf(error) !== 401 || isAuthEndpoint(request)) throw error
      if (!(await refreshOnce(raw))) {
        await nuxtApp.runWithContext(() => {
          if (useRoute().path !== ADMIN_LOGIN_PATH) return navigateTo(ADMIN_LOGIN_PATH)
        })
        throw error
      }
      return raw(request, options)
    }
  }) as $Fetch
  api.raw = raw.raw
  api.native = raw.native
  api.create = raw.create
  return api
}

export type { ApiSuccess }

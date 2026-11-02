import { unwrap, useApi } from '@shared/api'
import type { ApiSuccess } from '@shared/api'
import type { Session } from '../model/types'

function statusOf(error: unknown): number | undefined {
  const e = error as { statusCode?: number, status?: number } | null
  return e?.statusCode ?? e?.status
}

async function me(): Promise<Session | null> {
  const api = useApi()
  try {
    return unwrap(await api<ApiSuccess<Session>>('/v1/auth/me'))
  }
  catch (error) {
    if (statusOf(error) === 401) return null
    throw error
  }
}

async function refresh(): Promise<boolean> {
  const api = useApi()
  try {
    await api('/v1/auth/refresh', { method: 'POST' })
    return true
  }
  catch (error) {
    if (statusOf(error) === 401) return false
    throw error
  }
}

/** Current session, or null for a guest. On 401 the refresh token is tried once. */
export async function fetchSession(): Promise<Session | null> {
  const session = await me()
  if (session) return session
  return (await refresh()) ? await me() : null
}

export async function login(email: string, password: string): Promise<void> {
  const api = useApi()
  await api('/v1/auth/login', { method: 'POST', body: { email, password } })
}

export async function logout(): Promise<void> {
  const api = useApi()
  await api('/v1/auth/logout', { method: 'POST' })
}

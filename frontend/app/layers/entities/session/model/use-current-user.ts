import { fetchSession, login, logout } from '../api/session-api'
import type { Permission, Session } from './types'

/** The signed-in admin User, shared across the app (SSR-safe `useState`). */
export function useCurrentUser() {
  const session = useState<Session | null>('session', () => null)

  const user = computed(() => session.value?.user ?? null)
  const role = computed(() => session.value?.role ?? null)
  const isAuthenticated = computed(() => session.value !== null)
  const mustChangePassword = computed(() => session.value?.must_change_password ?? false)

  function can(permission: Permission): boolean {
    return session.value?.permissions.includes(permission) ?? false
  }

  async function load(): Promise<Session | null> {
    session.value = await fetchSession()
    return session.value
  }

  async function signIn(email: string, password: string): Promise<void> {
    await login(email, password)
    await load()
  }

  async function signOut(): Promise<void> {
    try {
      await logout()
    }
    finally {
      session.value = null
    }
  }

  return { session, user, role, isAuthenticated, mustChangePassword, can, load, signIn, signOut }
}

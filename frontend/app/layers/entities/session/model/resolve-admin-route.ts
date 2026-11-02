import { useCurrentUser } from './use-current-user'

export const ADMIN_LOGIN_PATH = '/admin/login'
export const ADMIN_HOME_PATH = '/admin'

function isAdminPath(path: string): boolean {
  return path === '/admin' || path.startsWith('/admin/')
}

/**
 * Decides where a navigation to `path` must go. Returns a redirect target,
 * or undefined to let the navigation proceed. Only `/admin/login` is public.
 */
export async function resolveAdminRoute(path: string): Promise<string | undefined> {
  if (!isAdminPath(path)) return undefined

  const session = await useCurrentUser().load()

  if (path === ADMIN_LOGIN_PATH) return session ? ADMIN_HOME_PATH : undefined
  return session ? undefined : ADMIN_LOGIN_PATH
}

import { useCurrentUser } from './use-current-user'
import type { Permission } from './types'

export const ADMIN_LOGIN_PATH = '/admin/login'
export const ADMIN_HOME_PATH = '/admin'
export const ADMIN_CHANGE_PASSWORD_PATH = '/admin/change-password'

function isAdminPath(path: string): boolean {
  return path === '/admin' || path.startsWith('/admin/')
}

/**
 * Decides where a navigation to `path` must go. Returns a redirect target,
 * or undefined to let the navigation proceed. Only `/admin/login` is public.
 *
 * A User flagged must-change-password may only reach the change-password page until they change it.
 *
 * `permission` is the route's `definePageMeta({ permission })`: when set, a signed-in User
 * lacking it is sent to the admin home (the menu hiding the item is not enough).
 */
export async function resolveAdminRoute(path: string, permission?: Permission): Promise<string | undefined> {
  if (!isAdminPath(path)) return undefined

  const current = useCurrentUser()
  const session = await current.load()

  if (path === ADMIN_LOGIN_PATH) {
    if (!session) return undefined
    return current.mustChangePassword.value ? ADMIN_CHANGE_PASSWORD_PATH : ADMIN_HOME_PATH
  }
  if (!session) return ADMIN_LOGIN_PATH
  if (current.mustChangePassword.value && path !== ADMIN_CHANGE_PASSWORD_PATH) return ADMIN_CHANGE_PASSWORD_PATH
  if (permission && !current.can(permission) && path !== ADMIN_HOME_PATH) return ADMIN_HOME_PATH
  return undefined
}

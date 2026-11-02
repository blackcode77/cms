import { resolveAdminRoute } from '@entities/session'
import type { Permission } from '@entities/session'

declare module '#app' {
  interface PageMeta {
    /** Permission the User needs to open this admin route; enforced by the global admin guard. */
    permission?: Permission
  }
}

// Guards every /admin/** route; only /admin/login is public (logic lives in the session entity).
export default defineNuxtRouteMiddleware(async (to) => {
  const target = await resolveAdminRoute(to.path, to.meta.permission)
  if (target) return navigateTo(target)
})

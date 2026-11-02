import { resolveAdminRoute } from '@entities/session'

// Guards every /admin/** route; only /admin/login is public (logic lives in the session entity).
export default defineNuxtRouteMiddleware(async (to) => {
  const target = await resolveAdminRoute(to.path)
  if (target) return navigateTo(target)
})

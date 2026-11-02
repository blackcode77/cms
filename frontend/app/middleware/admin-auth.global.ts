import { resolveAdminRoute } from '@entities/session'

// Guards every /admin/** route; only /admin/login is public (logic lives in the session entity).
// A page restricts itself further with `definePageMeta({ permission: '<permission>' })`.
export default defineNuxtRouteMiddleware(async (to) => {
  const target = await resolveAdminRoute(to.path, to.meta.permission)
  if (target) return navigateTo(target)
})

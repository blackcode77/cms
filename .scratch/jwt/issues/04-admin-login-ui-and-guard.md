# 04: Admin login form and guarded `/admin/**`

**What to build:** Opening any `/admin/**` page without a Session redirects to `/admin/login`, a Nuxt UI sign-in form. After success the User lands in the admin dashboard. A session entity exposes `useCurrentUser()` with `can(permission)`, loaded from `/auth/me` via `useApi()`. Route middleware guards `/admin/**` (public: login only). The admin menu shows only items the User has Permissions for; sign-out is available. Admin UI uses Nuxt UI only, never in the public layout (ADR 0001); page files stay thin (FSD). Update the "Auth not implemented" gotcha in `CLAUDE.md`.

**Blocked by:** 02

**Status:** resolved

- [x] Guest visiting `/admin` is redirected to `/admin/login`
- [x] Valid credentials lead to the dashboard; invalid show the generic error
- [x] `can()` reflects the Permissions from `/auth/me`
- [x] Sign-out ends the Session and returns to login
- [x] Menu items are hidden without the matching Permission
- [x] Vitest covers `can()` and the route middleware (`registerEndpoint` for `/auth/me`)
- [x] `make lint` (including steiger) passes

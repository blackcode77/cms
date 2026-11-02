Status: ready-for-agent

# JWT authentication, Users and Roles for the admin area

## Problem Statement

The admin area (`/admin/**`) is open to everyone, and write endpoints such as `POST /pages` have no protection. The site owner cannot sign in, cannot create accounts for colleagues, and cannot control who may do what. They need authentication done through the Yii3 backend, an admin UI built with Nuxt UI, user management (with credentials delivered by email), and an editable Role/Permission system that the frontend can query (`currentUser.can('edit_page')`).

## Solution

The backend issues JWT-based Sessions (short-lived access token + revocable refresh token in httpOnly cookies). `/admin/login` is the only public admin page; every other `/admin/**` route requires a Session and the matching Permission. An admin manages Users (create, edit, delete, reset password) and Roles (create, rename, delete, tick Permissions from a fixed catalog) through the admin UI, all via backend API. New Users receive their login and a generated password by email and must change it on first sign-in. The Initial admin is seeded from backend environment variables. Anonymous site visitors have the implicit Visiter Role. Terms follow `GLOSSARY.md`; decisions follow ADR 0002 (tokens in httpOnly cookies, permissions not in the JWT) and ADR 0003 (one Role per User).

Delivery is split into three vertical slices, each mergeable on its own:
1. Auth core (User/Role/Permission model, seeding, login/refresh/logout/me, route protection, login form, guarded `/admin/**`).
2. Users section (CRUD, email, reset password, change own password).
3. Roles section (CRUD, Permission checkboxes).

## User Stories

1. As the site owner, I want the Initial admin created from `ADMIN_EMAIL` / `ADMIN_PASSWORD` in the backend environment, so that I can sign in right after deployment without manual DB work.
2. As the site owner, I want seeding to be idempotent, so that redeploying never duplicates or overwrites the admin.
3. As an anonymous visitor, I want to be redirected to `/admin/login` when opening any `/admin/**` page, so that the admin area is closed to me.
4. As a User, I want to sign in with my email and password, so that I can reach the admin area.
5. As a User, I want a generic "invalid email or password" message on failure, so that attackers cannot tell which emails exist.
6. As the site owner, I want 5 consecutive failed sign-ins for an email+IP pair to block sign-in for 15 minutes, so that brute-force attacks are impractical.
7. As a User, I want to land in the admin dashboard after a successful sign-in, so that I can start working.
8. As a User, I want my Session to refresh transparently, so that I am not logged out every 15 minutes.
9. As a User, I want refresh tokens rotated on each use, so that a stolen refresh token has limited value.
10. As a User, I want to sign out and have my refresh token revoked, so that my Session cannot be reused.
11. As a User, I want the frontend to know who I am and my Permissions via `GET /auth/me`, so that the UI shows only what I can use.
12. As a developer, I want `useCurrentUser().can(permission)` on the frontend, so that I can hide UI by Permission.
13. As a developer, I want the backend to be the only authority on Permissions, so that hiding UI is never the sole protection.
14. As the site owner, I want Permissions read from the DB on every request, so that Role changes and User deletion take effect immediately.
15. As a visitor of the public site, I want SSR pages unaffected by auth, so that the public site keeps working.
16. As the site owner, I want `POST /pages` to require the `edit_page` Permission, so that only authorized Users can write pages.
17. As an anonymous caller, I want `GET /health` and `GET /pages` to stay public.
18. As an unauthenticated caller of a protected endpoint, I want a 401, so that clients know to sign in.
19. As an authenticated User lacking a Permission, I want a 403, so that clients know access is denied.
20. As an admin, I want a Users section listing all Users with their Role, so that I can see who has access.
21. As an admin, I want to create a User by email, name and Role, so that I can give colleagues access.
22. As a new User, I want an email with my login and generated password, so that I can sign in.
23. As an admin, I want User creation to succeed even if the email fails to send, with `mail_sent: false` returned, so that a mail outage does not block me.
24. As an admin, I want a "resend password" action that generates a new password and emails it, so that I can recover from failed or lost emails.
25. As a new User, I want to be forced to change my password on first sign-in, so that the emailed password is short-lived.
26. As a User, I want to change my own password, so that I can keep my account secure.
27. As a User, I want to be unable to change my own email, so that only admins control identities.
28. As an admin, I want to edit a User's name, email and Role, so that I can keep accounts current.
29. As an admin, I want unique emails enforced, so that logins are unambiguous.
30. As an admin, I want to delete a User, so that former colleagues lose access.
31. As an admin, I want deleting a User to revoke their refresh tokens, so that their Session ends within the access-token lifetime.
32. As an admin, I want to be unable to delete myself, so that I do not lock myself out.
33. As the site owner, I want the last Admin impossible to delete or demote, so that the system always has an administrator.
34. As an admin, I want a Roles section listing Roles with their Permissions, so that I can audit access.
35. As an admin, I want to create a Role with a name, so that I can model new staff types.
36. As an admin, I want to rename a Role.
37. As an admin, I want to tick Permissions from a fixed catalog for a Role, so that I control what the Role can do.
38. As an admin, I want the Admin Role to always hold all Permissions and be read-only, so that it cannot be weakened by mistake.
39. As an admin, I want to delete a Role, but be blocked with a clear message when Users still hold it, so that nobody is left without a Role.
40. As an admin, I want the Visiter Role Permissions editable but the Role undeletable and not assignable to Users, so that guest access is configurable yet safe.
41. As an admin, I want default Roles `admin`, `manager`, `visiter` created on setup, so that the system is usable immediately.
42. As a manager, I want `view_dashboard` and `edit_page` by default, so that I can do content work.
43. As a User without `view_users`/`view_roles`, I want those menu items and routes hidden and closed, so that I only see what I can use.
44. As an admin, I want a catalog of Permissions: `view_dashboard`, `edit_page`, `view_users`, `manage_users`, `view_roles`, `manage_roles`.
45. As a developer, I want passwords stored only as argon2id hashes and refresh tokens only as hashes.
46. As a developer, I want the API to stay JSON-only with `SameSite=Lax` cookies (`Secure` outside dev), so that CSRF is covered.
47. As a developer, I want a mail catcher (Mailpit) in the dev stack, so that I can view sent emails locally.
48. As an admin, I want a forgotten password handled by an admin reset, not a self-service flow, so that scope stays small.

## Implementation Decisions

- **Domain**: new aggregates User and Role in the existing layered layout (Entity/Value/Repository), following the Page aggregate. Permission is a fixed catalog of values defined in code, not a table-driven free-form entity. Role-to-Permission assignments and the Admin Role "all permissions" rule are domain behavior.
- **Rules in the domain/application layer**: last Admin cannot be deleted or demoted; a User cannot delete themselves; a Role with Users cannot be deleted; the Admin Role is immutable; the Visiter Role is undeletable and cannot be assigned to a User; User email is unique.
- **Seeding**: a console command (idempotent) creates the default Roles (`admin`, `manager`, `visiter` with default Permissions: admin = all, manager = `view_dashboard` + `edit_page`, visiter = none) and the Initial admin from `ADMIN_EMAIL`/`ADMIN_PASSWORD`. Generate the dev credentials into the backend `.env`.
- **Schema**: tables for users (email unique, password hash, name, role reference, must-change-password flag), roles, role permissions, refresh tokens (hash, user, expiry, revoked), and login attempts (email, IP, counters/lockout). PostgreSQL `uuid` PKs, hand-written SQL migrations, one Role per User via a single role reference (ADR 0003). Register new tables in the test DB cleanup.
- **Tokens (ADR 0002)**: `firebase/php-jwt`, HS256, secret from `JWT_SECRET`. Access token 15 min containing only the User id; refresh token 7 days, opaque, stored hashed, rotated on use, revoked on logout/User deletion. Both in httpOnly cookies, `SameSite=Lax`, `Secure` outside dev. Token issuing sits behind an interface (`TokenIssuerInterface`).
- **Passwords**: argon2id; generated passwords are 16 characters.
- **Brute force**: 5 failed attempts per email+IP lock sign-in for 15 minutes; failure message identical for unknown email, wrong password, and lockout-safe where appropriate.
- **API** (mounted under `/api/v1`, envelope via `ResponseFactory`, business errors as `#[UserException]` 400):
  - Auth: login, refresh, logout, `GET /auth/me` (User, Role, Permissions, must-change-password), `POST /auth/change-password`.
  - Users: list (read model), create, update, delete, reset/resend password (generates new password and emails it). Create returns `mail_sent`.
  - Roles: list (read model), create, update (name + Permissions), delete; plus the Permission catalog for the checkbox UI.
- **Middleware**: `Authenticate` (cookie → access token → User, with transparent handling of expired access via refresh endpoint on the client) and `RequirePermission(permission)`. Apply `edit_page` to `POST /pages`, `view_*`/`manage_*` to Users/Roles endpoints. 401 for unauthenticated, 403 for missing Permission. `GET /health` and `GET /pages` stay public. Permissions are loaded from the DB per request.
- **Mail**: `MailerInterface` in Application; infrastructure implementation on `symfony/mailer` with SMTP settings from `.env`; Mailpit service added to the dev compose stack. Sending is synchronous, no queue; failure does not roll back User creation.
- **Frontend** (FSD, Nuxt UI admin dashboard per ADR 0001, no Nuxt UI/Tailwind in public layout): a session entity exposing `useCurrentUser()` with `can(permission)`, loaded from `/auth/me` (SSR-safe, forwards the cookie via `useApi()`); public `/admin/login`; route middleware guarding the rest of `/admin/**` (redirect to login, forced password change when flagged, Permission checks per route); admin layout menu filtered by Permissions; Users and Roles pages built from features/entities slices. Page files stay thin, importing from `@pages/<slice>`.
- **Docs**: update `CLAUDE.md` "Auth planned but not implemented" gotcha when slice 1 lands.

## Testing Decisions

- A good test exercises external behavior only (HTTP responses, DB-visible effects, emails sent), never internal call structure.
- **Primary seam: the Codeception Api suite** (`ApiTester::sendApiRequest`, in-process, real PostgreSQL `app_test`), as in the existing page endpoint Cest. Cover: login success/failure/lockout, refresh rotation and reuse of a revoked token, logout, `me`, change password, protected endpoints returning 401/403/200 (including `POST /pages`), Users CRUD and its guard rules (self-delete, last Admin, unique email), Roles CRUD and its guard rules (Admin immutable, Visiter undeletable, Role in use), seeding idempotency, and that user creation and password reset send an email containing login and password.
- **New seam: a fake `MailerInterface`** (in-memory, bound in the test environment) to assert sent emails and to simulate send failure (`mail_sent: false`).
- **Unit** (existing in-memory-fake style) only for pure domain rules not worth routing through HTTP: Permission catalog, Admin Role all-permissions behavior, token expiry and password generation properties.
- **Vitest** for `useCurrentUser().can()` and the route middleware, mocking `/auth/me` with `registerEndpoint` and clearing shared `useAsyncData` keys between tests.
- Prior art: `tests/Api` page Cest, `tests/Unit/Application` and `tests/Unit/Domain` fakes, `frontend/tests/pages`.

## Out of Scope

- Self-service password recovery by emailed link.
- Users editing their own email or profile beyond changing their password.
- Multiple Roles per User (ADR 0003), soft-delete or "blocked" User state.
- Asynchronous mail queue, delivery tracking, retries.
- Audit log of admin actions.
- Two-factor authentication, OAuth/social login.
- Per-resource (row-level) permissions; Permissions are global capabilities.
- Accounts for site visitors; Visiter is only an implicit guest Role.

## Further Notes

- Delivery order is slice 1, then 2, then 3; each slice ships with its tests and should be independently mergeable. Suggest splitting this spec into tickets along those slices with `/to-tickets`.
- The original brief is in `.claude/tasks/jwt/task.md`; glossary in `GLOSSARY.md`; decisions in `docs/adr/0002-*.md` and `docs/adr/0003-*.md`.
- Dev credentials for the Initial admin should be generated and placed in the backend `.env` (and mirrored as placeholders in the example env file).

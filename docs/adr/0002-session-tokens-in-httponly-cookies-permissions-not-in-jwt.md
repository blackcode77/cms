# Session tokens live in httpOnly cookies; permissions are not stored in the JWT

Auth is JWT (HS256, `firebase/php-jwt`) issued by the Yii3 backend: a 15-minute access token and a 7-day refresh token, both in httpOnly cookies. Refresh tokens are stored hashed in the DB and rotated on every use, so logout and user deletion can revoke them. The access token carries only `sub` (user id); the permission middleware loads the User's Role and Permissions from the DB on every request, and the frontend gets them from `GET /auth/me`.

We chose cookies over `Authorization` headers so SSR requests can carry the session (see `useApi()`), and keeping tokens out of JS storage. We chose DB-loaded permissions over claims in the token so role/permission changes and user deletion take effect immediately instead of after the access token expires.

## Consequences

- One extra DB query (user + role + permissions) per authenticated request.
- Cookies mean the API must stay JSON-only with `SameSite=Lax`; this is the only CSRF defence, so don't add form-encoded or GET state-changing endpoints.
- The frontend `can()` is a UX convenience only; the backend is the sole authority.

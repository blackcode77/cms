# 02: Sign-in and Session API

**What to build:** A User can sign in with email and password and get a Session carried in httpOnly cookies (ADR 0002): login, refresh with rotation, logout, and `GET /auth/me` returning the User, Role, Permissions and must-change-password flag. Uses `firebase/php-jwt`, HS256, `JWT_SECRET`; access token 15 min with only the User id, refresh token 7 days, opaque, stored hashed, rotated on use and revoked on logout. Cookies are `SameSite=Lax`, `Secure` outside dev. After 5 consecutive failures per email+IP, sign-in is blocked for 15 minutes; failure message is identical for unknown email and wrong password. Token issuing sits behind `TokenIssuerInterface`.

**Blocked by:** 01

**Status:** ready-for-agent

- [ ] Login with valid credentials sets cookies and `me` returns the User with Role and Permissions
- [ ] Wrong password and unknown email return the same generic error
- [ ] 5 failures lock the email+IP pair for 15 minutes, then sign-in works again
- [ ] Refresh issues a new pair and the old refresh token no longer works
- [ ] Logout revokes the refresh token
- [ ] `me` without a valid access token returns 401
- [ ] Api Cest covers all of the above; new tables added to test cleanup

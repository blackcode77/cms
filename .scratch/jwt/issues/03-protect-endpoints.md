# 03: Authentication and Permission enforcement on the API

**What to build:** `Authenticate` and `RequirePermission` middleware. Permissions are loaded from the DB on every request (not from the token). `POST /pages` requires `edit_page`; `GET /health` and `GET /pages` stay public. This is the mechanism later Users/Roles endpoints reuse.

**Blocked by:** 02

**Status:** resolved

- [x] Unauthenticated `POST /pages` returns 401
- [x] Authenticated User without `edit_page` gets 403
- [x] `admin` and `manager` can `POST /pages`
- [x] Changing a Role's Permissions (via DB in the test) takes effect on the next request
- [x] `GET /health` and `GET /pages` remain public
- [x] Existing `PageCest` is updated to authenticate and still passes

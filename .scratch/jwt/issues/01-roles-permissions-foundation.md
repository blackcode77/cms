# 01: Roles and Permissions foundation with seeding

**What to build:** The User, Role and Permission model exists in the database and domain, and one console command idempotently seeds the default Roles (`admin` = all Permissions, `manager` = `view_dashboard` + `edit_page`, `visiter` = none) and the Initial admin from `ADMIN_EMAIL` / `ADMIN_PASSWORD`. Dev credentials are generated into the backend `.env` (placeholders in the example env file). Terms per `GLOSSARY.md`; one Role per User (ADR 0003); Permission catalog is fixed in code: `view_dashboard`, `edit_page`, `view_users`, `manage_users`, `view_roles`, `manage_roles`.

**Blocked by:** None (can start immediately)

**Status:** resolved

- [x] Migrations create users, roles, role permissions tables (uuid PKs, unique email, single role reference); test cleanup knows the new tables
- [x] Passwords stored as argon2id hashes only
- [x] Running the seed command creates the three Roles and the Initial admin with the Admin Role
- [x] Running it again changes nothing (no duplicates, existing admin not overwritten)
- [x] Console test covers seeding and idempotency
- [x] Unit tests cover the Permission catalog and "Admin Role holds all Permissions"

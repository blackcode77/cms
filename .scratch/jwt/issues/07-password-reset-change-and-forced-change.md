# 07: Passwords: admin reset, own change, forced change on first sign-in

**What to build:** An admin with `manage_users` can trigger "send password again": a new password is generated and emailed (same `mail_sent` behavior as creation). Any User can change their own password via `POST /auth/change-password` (email is not editable by the User). A User flagged must-change-password is forced to the change-password screen after sign-in until they change it. There is no self-service recovery by link.

**Blocked by:** 05

**Status:** resolved

- [x] Reset generates a new password, invalidates the old one, and emails the new one
- [x] Reset with a mail failure reports `mail_sent: false`
- [x] Change-password requires the current password and clears the must-change flag
- [x] A flagged User is redirected to change password and cannot reach other admin pages until done
- [x] Api Cests and Vitest for the redirect

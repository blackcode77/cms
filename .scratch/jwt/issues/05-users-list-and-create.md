# 05: Users section: list and create with emailed credentials

**What to build:** An admin with `view_users` sees all Users with their Role; with `manage_users` they create a User (email, name, Role). A 16-character password is generated, stored as argon2id, and emailed with the login. Creation succeeds even if sending fails and the API returns `mail_sent: false`. Mail goes through `MailerInterface` on `symfony/mailer` (SMTP from `.env`), synchronous, no queue. Mailpit is added to the dev compose stack. The new User must change their password on first sign-in (flag set; enforcement in ticket 07). `visiter` cannot be assigned.

**Blocked by:** 03, 04

**Status:** resolved

- [x] List endpoint (read model) and Users page, gated by `view_users`
- [x] Create endpoint and form, gated by `manage_users`
- [x] Duplicate email returns a business error
- [x] Assigning `visiter` is rejected
- [x] Email containing login and password is sent (asserted through the in-memory fake mailer)
- [x] Simulated send failure still creates the User and returns `mail_sent: false`
- [x] Mailpit works in dev and shows the email
- [x] Api Cests + Vitest for visibility by Permission
- [x] Opening the section's admin URL directly without `view_users` is blocked on the frontend (redirect/403 page), not only hidden from the menu; `useCurrentUser().can()` is used in the route guard (the global admin middleware currently checks only the Session)

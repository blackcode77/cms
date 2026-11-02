# 05: Users section: list and create with emailed credentials

**What to build:** An admin with `view_users` sees all Users with their Role; with `manage_users` they create a User (email, name, Role). A 16-character password is generated, stored as argon2id, and emailed with the login. Creation succeeds even if sending fails and the API returns `mail_sent: false`. Mail goes through `MailerInterface` on `symfony/mailer` (SMTP from `.env`), synchronous, no queue. Mailpit is added to the dev compose stack. The new User must change their password on first sign-in (flag set; enforcement in ticket 07). `visiter` cannot be assigned.

**Blocked by:** 03, 04

**Status:** ready-for-agent

- [ ] List endpoint (read model) and Users page, gated by `view_users`
- [ ] Create endpoint and form, gated by `manage_users`
- [ ] Duplicate email returns a business error
- [ ] Assigning `visiter` is rejected
- [ ] Email containing login and password is sent (asserted through the in-memory fake mailer)
- [ ] Simulated send failure still creates the User and returns `mail_sent: false`
- [ ] Mailpit works in dev and shows the email
- [ ] Api Cests + Vitest for visibility by Permission

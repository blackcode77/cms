# 06: Users section: edit and delete

**What to build:** An admin with `manage_users` edits a User's name, email and Role, and deletes Users. Deletion revokes the User's refresh tokens. Guard rules: a User cannot delete themselves; the last Admin cannot be deleted or demoted.

**Blocked by:** 05

**Status:** ready-for-agent

- [ ] Update endpoint and edit form; email stays unique
- [ ] Delete endpoint and confirm action
- [ ] Deleted User's refresh token stops working
- [ ] Deleting yourself is rejected with a business error
- [ ] Deleting or demoting the last Admin is rejected
- [ ] Api Cests cover each rule and 403 without `manage_users`

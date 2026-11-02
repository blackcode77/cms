# 06: Users section: edit and delete

**What to build:** An admin with `manage_users` edits a User's name, email and Role, and deletes Users. Deletion revokes the User's refresh tokens. Guard rules: a User cannot delete themselves; the last Admin cannot be deleted or demoted.

**Blocked by:** 05

**Status:** resolved

- [x] Update endpoint and edit form; email stays unique
- [x] Delete endpoint and confirm action
- [x] Deleted User's refresh token stops working
- [x] Deleting yourself is rejected with a business error
- [x] Deleting or demoting the last Admin is rejected
- [x] Api Cests cover each rule and 403 without `manage_users`

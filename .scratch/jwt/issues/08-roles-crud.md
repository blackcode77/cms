# 08: Roles section: create, rename, delete, Permissions checkboxes

**What to build:** An admin with `view_roles` / `manage_roles` manages Roles. The UI lists Roles and edits name plus Permissions via checkboxes from the fixed catalog (served by the API). Rules: Admin Role is read-only and always holds all Permissions; Visiter Role is editable but cannot be deleted or assigned to a User; a Role that Users hold cannot be deleted (400 with the user count).

**Blocked by:** 03, 04

**Status:** ready-for-agent

- [ ] List, create, rename, delete endpoints and Roles page, gated by Permissions
- [ ] Permission catalog endpoint drives the checkboxes
- [ ] Editing or deleting the Admin Role is rejected
- [ ] Deleting Visiter is rejected
- [ ] Deleting a Role in use returns a business error naming the count
- [ ] Permission changes affect the next request of Users with that Role
- [ ] Api Cests + Vitest for menu/route visibility

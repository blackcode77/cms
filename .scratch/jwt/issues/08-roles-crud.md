# 08: Roles section: create, rename, delete, Permissions checkboxes

**What to build:** An admin with `view_roles` / `manage_roles` manages Roles. The UI lists Roles and edits name plus Permissions via checkboxes from the fixed catalog (served by the API). Rules: Admin Role is read-only and always holds all Permissions; Visiter Role is editable but cannot be deleted or assigned to a User; a Role that Users hold cannot be deleted (400 with the user count).

**Blocked by:** 03, 04

**Status:** resolved

- [x] List, create, rename, delete endpoints and Roles page, gated by Permissions
- [x] Permission catalog endpoint drives the checkboxes
- [x] Editing or deleting the Admin Role is rejected
- [x] Deleting Visiter is rejected
- [x] Deleting a Role in use returns a business error naming the count
- [x] Permission changes affect the next request of Users with that Role
- [x] Api Cests + Vitest for menu/route visibility
- [x] Opening the section's admin URL directly without `view_roles` is blocked on the frontend (redirect/403 page), not only hidden from the menu; `useCurrentUser().can()` is used in the route guard (the global admin middleware currently checks only the Session)

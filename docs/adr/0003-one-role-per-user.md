# A User has exactly one Role

`user.role_id` is a single foreign key to `role`; there is no user-to-role join table. Permission checks and the role editor UI are simpler, and the three seeded roles (`admin`, `manager`, `visiter`) cover the current needs.

## Consequences

- Needing "manager who can also do X" means creating a new Role with that permission set, not stacking roles.
- Moving to many-to-many later requires a migration and changes to the permission query and `/auth/me`; it is possible but not free.
- `visiter` is the implicit Role of anonymous visitors and is never assigned to a User.

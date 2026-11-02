# Glossary

**User**:
A person with an account in the admin area, identified by a unique email (the email is the login). Has exactly one Role.

**Role**:
A named set of Permissions that can be assigned to Users. Created, renamed and deleted by an admin. Seeded roles: `admin`, `manager`, `visiter`.

**Permission**:
A named capability from a fixed catalog defined by the system (e.g. `edit_page`, `manage_users`, `manage_roles`). Roles are granted Permissions; Permissions cannot be invented by admins.

**Admin**:
The Role that always holds every Permission and cannot be edited or deleted. The system never lets the last User with this Role be deleted or demoted.

**Visiter**:
The implicit Role of an anonymous site visitor who has no account. Its Permissions are editable, but the Role cannot be deleted and cannot be assigned to a User.

**Initial admin**:
The first User with the Admin Role, created idempotently from `ADMIN_EMAIL` / `ADMIN_PASSWORD` in the backend environment. After seeding it is an ordinary User.

**Session**:
A signed-in User's authenticated state: a short-lived access token plus a longer-lived, revocable refresh token, both carried in httpOnly cookies.

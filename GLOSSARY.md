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

**Page**:
A public site page identified by a stable page key (e.g. `home`, `contacts`). Its editable text and media are its Content elements. Not a database record of title/slug/body: the page layout itself is fixed, only its content is editable. The special page key `site` holds content shared by every public page (header, footer, call-to-action banner).

**Content element**:
One editable piece of a Page, addressed by a dotted path within the Page (e.g. `header.phone_number_one`). Has a type (string, text, html, image, icon, link, url, lines, file, color, stops, list), can be switched off (disabled elements are hidden from visitors), and falls back to its default when no value has been saved.

**Content list**:
A Content element whose value is an ordered list of Items of the same shape. Editors add, delete, reorder and edit Items, but cannot change which fields an Item has. Once saved, the list fully replaces its default.

**Item**:
One entry of a Content list: a fixed set of named fields (e.g. icon, title, text), each with its own type.

**Icon**:
A Content element type whose value names a pictogram from the site's single icon collection; editors pick it from that collection instead of supplying drawings.

**Link**:
A Content element type whose value is an address inside the public site (a page, optionally with an anchor). An empty link means the block is not clickable. Addresses of other websites are a separate type, url, and must be secure (https).

**Document**:
A file an editor uploads for visitors to download (PDF, Excel or Word), shown on a Page with its title, a note, its format and size. Visitors always download it rather than open it on the site. Replacing or deleting a Document leaves the old file on the server.

**Page content**:
The saved values of a Page's Content elements. Only values that differ from the defaults are stored.

**Edit mode**:
A switchable state of the public site, available only to a signed-in User whose Role holds the `edit_page` Permission, in which Content elements are highlighted and can be edited in place; each change is saved immediately.

**Splash screen**:
A brief dark translucent overlay with the white company emblem in the centre, shown for about one second when a visitor fully loads the public site. Not shown on navigation between pages or in the admin area.

**Route map**:
A block of a Page showing a schematic map with Routes drawn over it, which play as an animation when a visitor scrolls to it. Editors choose the background picture and place Routes by clicking on it. Not a geographic map: positions are relative to the picture, not coordinates on Earth.

**Route**:
An ordered chain of two or more Stops on a Route map, drawn as a dashed line of one colour running from the first Stop to the last. May have a name, shown in the map's legend. Routes do not share Stops: the same port in two Routes is two separate Stops.

**Stop**:
A labelled point of a Route, usually a port or terminal (e.g. «Владивосток (ВМРП / ВМТП)»). The editor chooses on which side of the point its label is shown.

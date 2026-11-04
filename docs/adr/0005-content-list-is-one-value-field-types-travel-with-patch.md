# A Content list is stored as one value; its field types travel with the patch

A Content list (see `GLOSSARY.md`) is a single Content element of type `list` whose value is the whole ordered array of Items, and every change (add, delete, reorder, edit a field) saves the full array at that one path. We chose this over giving each Item field its own path plus a separate order element: ordering, deletion and orphaned Item values would need their own rules, while a single value keeps ADR 0004's "one change = one `{path, value}`" intact. Once saved, the list fully replaces its code default; later changes to the default do not reach an already edited list.

Because the backend still does not know the frontend schema (ADR 0004), the `PATCH` for a list carries a map of Item field types (`{title: 'string', text: 'html', icon: 'icon', …}`) next to the value. The backend uses it only to validate and sanitize: each field is checked against its type's limits and `html` fields go through the same sanitizer as standalone `html` elements. We rejected skipping backend checks for list HTML, because any User with `edit_page` could then store script that runs for visitors and admins.

## Consequences

- Two editors changing the same list at the same time: the last save wins (unlike different standalone elements, which do not collide).
- The field-type map is trusted only to choose the sanitization; a client lying about a type can at worst get HTML sanitized as HTML or text stored as plain text, never unsanitized HTML stored under an `html` field.
- Items carry a generated `id` so the UI can keep stable identity across reorders.

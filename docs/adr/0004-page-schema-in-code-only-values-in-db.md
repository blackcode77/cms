# Page schema lives in code, only content values live in the database

The structure of a Page (which Content elements exist, their types, their defaults) is declared next to the page component in the frontend. The database stores only the values that editors changed, as `jsonb` keyed by page key and element path, and each edit is saved immediately as a single `{path, value}` change. We chose this over storing the whole structured JSON (type, enabled, content for every element) per page in the database: no seed migration per page, the default text is exactly the current markup, and adding an element in code needs no data migration. Saving per path instead of the whole document means two editors do not overwrite each other.

## Consequences

- The backend does not know the frontend schema, so it validates and sanitizes the value it receives (notably server-side HTML sanitization) rather than checking it against a schema.
- A page renders correctly with an empty table; removing an element from code leaves orphaned values that are simply ignored.
- The old `page` table (title/slug/content) and its aggregate are removed.
- No drafts or version history: an edit is live as soon as it is saved.

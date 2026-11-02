# 01: Page content storage, public read and protected write (text types)

**What to build:** Replace the unused `Page` aggregate and `page` table with `PageContent` (`page_key`, `content jsonb`, `updated_at`). Public `GET` returns saved values for the requested page keys (empty object for unknown keys). `PATCH` sets one value by path (atomic per-path `jsonb` update, so concurrent editors do not overwrite each other) or toggles `enabled` for a path; protected by `edit_page`. Types `string`, `text`, `html`; `html` is sanitized server-side with a tag/attribute allowlist (no `script`, `on*`, `javascript:`). Path format and value size are validated; business errors are `#[UserException]`. Terms per `GLOSSARY.md`, ADR 0004.

**Blocked by:** None (can start immediately)

**Status:** resolved

- [x] Migration drops `page`, creates `page_content`; old Create/List endpoints, Cests, and `PageReader` removed; test cleanup updated
- [x] `GET` returns values for one or several page keys; unknown key gives empty content, no auth needed
- [x] `PATCH` without Session gives 401, without `edit_page` gives 403, with it saves and the next `GET` returns the value
- [x] Two PATCHes to different paths of one page both survive
- [x] `html` containing `<script>`, `onerror=` or `javascript:` is stored sanitized; invalid path or oversized value gives 400
- [x] Setting `enabled=false` for a path is stored and returned
- [x] Cest for every endpoint; unit tests for sanitizer and path validation

## Notes

- Routes: `GET /api/v1/page-content?keys=site,home` (public, 1-10 keys) and `PATCH /api/v1/page-content/{key}` with `{path, type?, value?, enabled?}` (`edit_page`).
- Storage is flat: `content` keys are dotted paths, values are `{type, value, enabled}`; one `INSERT ... ON CONFLICT` merges a single path atomically.
- Types so far: `string` (255), `text` (5000), `html` (50000, sanitized with `symfony/html-sanitizer`); `image` comes with ticket 02.

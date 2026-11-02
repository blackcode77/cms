# 01: Page content storage, public read and protected write (text types)

**What to build:** Replace the unused `Page` aggregate and `page` table with `PageContent` (`page_key`, `content jsonb`, `updated_at`). Public `GET` returns saved values for the requested page keys (empty object for unknown keys). `PATCH` sets one value by path (atomic per-path `jsonb` update, so concurrent editors do not overwrite each other) or toggles `enabled` for a path; protected by `edit_page`. Types `string`, `text`, `html`; `html` is sanitized server-side with a tag/attribute allowlist (no `script`, `on*`, `javascript:`). Path format and value size are validated; business errors are `#[UserException]`. Terms per `GLOSSARY.md`, ADR 0004.

**Blocked by:** None (can start immediately)

**Status:** ready-for-agent

- [ ] Migration drops `page`, creates `page_content`; old Create/List endpoints, Cests, and `PageReader` removed; test cleanup updated
- [ ] `GET` returns values for one or several page keys; unknown key gives empty content, no auth needed
- [ ] `PATCH` without Session gives 401, without `edit_page` gives 403, with it saves and the next `GET` returns the value
- [ ] Two PATCHes to different paths of one page both survive
- [ ] `html` containing `<script>`, `onerror=` or `javascript:` is stored sanitized; invalid path or oversized value gives 400
- [ ] Setting `enabled=false` for a path is stored and returned
- [ ] Cest for every endpoint; unit tests for sanitizer and path validation

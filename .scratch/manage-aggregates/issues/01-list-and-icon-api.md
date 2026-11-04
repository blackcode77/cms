Status: resolved

# 01: Backend accepts `list` and `icon` Content elements

**What to build:** Extend `PATCH /api/v1/page-content/{key}` with types `icon` and `list` per ADR 0005. `icon`: Lucide name format (`^[a-z0-9]+(-[a-z0-9]+)*$`, ≤ 64). `list`: body carries `fields` (Item field name → `string|text|html|image|icon`) and `value` (array of Items, each with `id` plus string values for declared fields only). Validate Item count limit, per-field length/rules by type (image URL rule as standalone images), sanitize `html` fields with the existing sanitizer. `GET` returns the saved array as is.

**Blocked by:** None

- [x] `icon` with a valid name is saved and returned; invalid name gives 400
- [x] `list` with valid fields/value is saved and returned in the same order
- [x] `html` field containing `<script>`/`onerror=`/`javascript:` is stored sanitized
- [x] Unknown field type, field not in `fields`, non-string value, missing `id`, too many Items, oversized field, bad image URL → 400
- [x] `enabled` toggle works for a `list` path
- [x] Cest cases in `tests/Api/`, unit tests for list validation

## Notes

- `value` of a `list` is stored as a JSON array in `jsonb`; `fields` is not stored (only used to validate/sanitize).
- Shape errors (fields map, ids, undeclared/non-string fields) → 422 from `Input` via `ContentList::fromArray`; rules (≤100 Items, per-type length, image URL, icon name, ≤200k encoded) → 400 from the handler.
- Icon name: `^[a-z0-9]+(-[a-z0-9]+)*$`, ≤64, without collection prefix (frontend adds `i-lucide-`).

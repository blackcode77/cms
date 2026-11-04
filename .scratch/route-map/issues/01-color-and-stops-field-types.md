# 01: `color` and `stops` Item field types (backend + frontend types)

**What to build:** Add `color` and `stops` to backend `ContentType` (Item field types only) and to frontend `FieldType`. Backend validates in `UpdateElement\Handler::checkValue`: `color` matches `#[0-9a-f]{6}` (case-insensitive, stored lowercase); `stops` is a JSON array of 2–20 `{x, y, label, side}` with numbers 0–100, trimmed non-empty label ≤ 60 chars with at most one `\n`, `side` in `top|right|bottom|left`; it is stored re-encoded (only those keys). Errors are `#[UserException]` with Russian-ready messages like the existing ones. Spec: `../spec.md`.

**Blocked by:** None

**Status:** resolved

- [x] Value objects in `Domain/PageContent/Value` (`ColorHex`, `RouteStops`) with unit tests
- [x] PATCH of a list with `color`/`stops` fields stores valid values; invalid colour, too few/many Stops, out-of-range coordinates, empty/long label, bad side, broken JSON → 400 (Cest)
- [x] Unknown keys inside a Stop are dropped
- [x] Frontend `FieldType` updated; `FieldInput` does not break on the new types

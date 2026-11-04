Status: resolved

# 05: `link` and `lines` field types; home service cards as a Content list

**What to build:** Two new value types (usable standalone and as Item fields; backend validates, ADR 0005 field-type map):
- `link`: path inside the site, `^/[a-z0-9/_-]*(#[a-z0-9_-]+)?$` (≤ 255). Empty/absent = not clickable. Panel: dropdown of known public pages + manual input for path/anchor. No external URLs.
- `lines`: text with one entry per line (≤ 5000), rendered by the page as `<li>` per non-empty line. Replaces nested lists.
Then home service cards (`services.cards`: title, text, image, link) become a Content list; a card without a link renders as a plain, non-clickable block without "Подробнее".

**Blocked by:** 02

- [x] Backend accepts valid `link`/`lines`, rejects `javascript:`, `https://…`, `//host`, relative paths, oversized values (Cest + unit)
- [x] Frontend types, `FieldInput` for both, link dropdown of known pages
- [x] Home service cards render as before with empty `page_content`; a card with an empty link is not a link
- [x] Tests; `make lint` clean

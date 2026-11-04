Status: resolved

# 02: `CmsList`, `CmsIcon` and list editing in the panel (pilot: home advantages)

**What to build:** Frontend support for `list` and `icon`: schema types (`list` with `fields`, `min`, `max`, default array), `CmsList` (scoped slot, one editable target in Edit mode, empty placeholder only in Edit mode), `CmsIcon` (standalone element and Item-field renderer, Lucide via `@nuxt/icon` with `@iconify-json/lucide` installed locally, SSR inline SVG). Editor panel: list form (Item cards, add/delete/move up/down, `min`/`max`), field editors per type including image upload/picker and an icon picker with search. Extend the `postMessage` protocol. Pilot: home advantages (`why.items`: icon, title, text) become a Content list.

**Blocked by:** 01

- [x] Home advantages render from the default with no saved value; SSR HTML contains inline SVG icons (no CDN request)
- [x] In Edit mode: add, delete, reorder, edit an Item and change its icon; each change is applied on the page and survives reload
- [x] Deleting all Items hides the block for visitors and shows a placeholder in Edit mode; `min` blocks deleting below it
- [x] Items keep stable `id`s; defaults get deterministic ids
- [x] Vitest for `CmsList`, `CmsIcon`, list editing logic; `make lint` clean

## Notes

- Verified: tests (`tests/entities/content-list.test.ts`, `tests/features/list-editor.test.ts`, `icon-search.test.ts`), SSR curl of `/` (inline Lucide SVG, local `/_nuxt_icon` endpoint), API save of `why.items` → SSR shows the saved Items.
- Not verified in a real browser: the panel UI (cards, icon picker) and the host↔iframe round trip with a list. Checked by hand by the user after the icon fix.
- `@nuxt/icon`: `mode: 'svg'`, server bundle `lucide`, endpoint moved to `/_nuxt_icon` (nginx sends `/api` to PHP), `fallbackToApi: false`. Admin icons no longer come from the Iconify CDN.
- Items: `AppIcon` (`@shared/ui`) renders a bare Lucide name; `CmsIcon` is the standalone `icon` element. Field `default` pre-fills added Items; empty fields are dropped before sending.

## Comments

- Manual check found empty icons in the panel and after picking one. Cause: @nuxt/icon replaces Iconify's provider with `/_nuxt_icon` only on the server; in the browser its "is default provider" check fails against @iconify/vue 5 (two CDN hosts), so client-side icons went to the Iconify CDN. Fixed with `app/plugins/iconify-local.client.ts`; verified in headless Chrome (client-only icon loads from `/_nuxt_icon`; without the plugin it renders an empty svg).

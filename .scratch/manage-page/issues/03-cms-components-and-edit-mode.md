# 03: Content components, schema declaration and Edit mode with editor panel

**What to build:** A way to declare a Page's Content elements (path, type, default) in code, and components `CmsText`, `CmsHtml`, `CmsImage` that render the saved value or the default via SSR, using content loaded for `site` plus the current page key. Disabled elements render nothing for visitors. After hydration, if the current User can `edit_page`, a floating "Edit" button toggles Edit mode: elements are highlighted (disabled ones dimmed), a click opens an editor panel as an `<iframe>` on the admin layout (route like `/admin/cms-editor`) with a form per type (text input, html editor, image picker with upload, enabled switch). Panel and page communicate by `postMessage`; saving calls `PATCH`, the page updates the value in place, and an "applied" toast appears at the side. Public styles stay in `site.css`, panel uses Nuxt UI (ADR 0001).

**Blocked by:** 01, 02

**Status:** resolved

- [x] Page renders defaults with an empty backend and saved values when present, in SSR HTML
- [x] Visitor without `edit_page` never sees the Edit button or editor; SSR HTML is identical for everyone
- [x] User with `edit_page` can toggle Edit mode, click an element, edit, save, and see the new value without reload
- [x] "Applied" toast shown after save; a failed save shows an error and keeps the old value
- [x] Disabled element is absent from visitor HTML and dimmed in Edit mode with an enable switch
- [x] Vitest covers components (default vs saved vs disabled) and the message protocol; `make lint` (steiger) passes

## Notes

- Entity `@entities/page-content`: `defineContentSchema(page, {path: {type, default, label?}})`, components `CmsText` (`as`, `string`/`text`), `CmsHtml`, `CmsImage`, `usePageContent()` (saved values + defaults), `useEditMode()`, the postMessage protocol (`editor-protocol.ts`). Use `<CmsText page="home" path="hero.title" as="h1" />`.
- Global middleware `cms-content.global.ts` loads `site` plus `definePageMeta({ cmsPage: '<key>' })` before render; a backend failure falls back to defaults. Routing files must set `cmsPage` (done per page in ticket 04).
- Widget `@widgets/cms-editor` (button, iframe panel, "Применено" toast) is mounted in `layouts/default.vue`; the panel is `/admin/cms-editor` (`layouts/admin-embed.vue`, permission `edit_page`) using feature `@features/edit-content`. Messages are accepted only from the panel window and same origin.
- Plain styles for Edit mode: `shared/styles/cms-edit.css` (public layout only, ADR 0001).
- Guests cost two 401 calls per full page load (`/auth/me` then `/auth/refresh`) because the session is checked client-side only; fine for now.
- Verified with Vitest (components, widget, editor form, protocol) and a dev-server smoke test (SSR HTML has no editing markup). The click-through in a real browser and SSR of saved values on a real page are checked in ticket 04, as no public page uses the components yet.

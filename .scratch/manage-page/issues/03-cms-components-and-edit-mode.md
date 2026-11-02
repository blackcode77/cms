# 03: Content components, schema declaration and Edit mode with editor panel

**What to build:** A way to declare a Page's Content elements (path, type, default) in code, and components `CmsText`, `CmsHtml`, `CmsImage` that render the saved value or the default via SSR, using content loaded for `site` plus the current page key. Disabled elements render nothing for visitors. After hydration, if the current User can `edit_page`, a floating "Edit" button toggles Edit mode: elements are highlighted (disabled ones dimmed), a click opens an editor panel as an `<iframe>` on the admin layout (route like `/admin/cms-editor`) with a form per type (text input, html editor, image picker with upload, enabled switch). Panel and page communicate by `postMessage`; saving calls `PATCH`, the page updates the value in place, and an "applied" toast appears at the side. Public styles stay in `site.css`, panel uses Nuxt UI (ADR 0001).

**Blocked by:** 01, 02

**Status:** ready-for-agent

- [ ] Page renders defaults with an empty backend and saved values when present, in SSR HTML
- [ ] Visitor without `edit_page` never sees the Edit button or editor; SSR HTML is identical for everyone
- [ ] User with `edit_page` can toggle Edit mode, click an element, edit, save, and see the new value without reload
- [ ] "Applied" toast shown after save; a failed save shows an error and keeps the old value
- [ ] Disabled element is absent from visitor HTML and dimmed in Edit mode with an enable switch
- [ ] Vitest covers components (default vs saved vs disabled) and the message protocol; `make lint` (steiger) passes

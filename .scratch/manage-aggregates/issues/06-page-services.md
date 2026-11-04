Status: resolved

# 06: Make `/services` editable (page key `services`)

**What to build:** `definePageMeta({ cmsPage: 'services' })` on the routing file; every visible heading, text, button label and `seo.title`/`seo.description` become Content elements with today's text as default; repeated blocks become Content lists. Navigation (header, footer, subnav) is not editable.

Lists: Services list (cards with icon+link), geography groups (title + lines), process steps, ship/cargo agency checklists, related links (link).

**Blocked by:** 05

- [x] Page renders identically with empty `page_content`
- [x] Every text block and list editable in Edit mode
- [x] Constants moved out of `model/content.ts`; public page tests updated

Status: resolved

# 12: Make `/schedules` editable (page key `schedules`)

**What to build:** `definePageMeta({ cmsPage: 'schedules' })` on the routing file; every visible heading, text, button label and `seo.title`/`seo.description` become Content elements with today's text as default; repeated blocks become Content lists. Navigation (header, footer, subnav) is not editable.

Lists: Booking steps and texts; document lists and filters stay in code (files: issue 13).

**Blocked by:** 05

- [x] Page renders identically with empty `page_content`
- [x] Every text block and list editable in Edit mode
- [x] Constants moved out of `model/content.ts`; public page tests updated

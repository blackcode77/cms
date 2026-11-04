Status: resolved

# 11: Make `/fleet` editable (page key `fleet`)

**What to build:** `definePageMeta({ cmsPage: 'fleet' })` on the routing file; every visible heading, text, button label and `seo.title`/`seo.description` become Content elements with today's text as default; repeated blocks become Content lists. Navigation (header, footer, subnav) is not editable.

Lists: Vessel texts and spec rows editable; route coordinates and the route drawing stay in code.

**Blocked by:** 05

- [x] Page renders identically with empty `page_content`
- [x] Every text block and list editable in Edit mode
- [x] Constants moved out of `model/content.ts`; public page tests updated

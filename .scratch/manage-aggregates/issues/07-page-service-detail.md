Status: resolved

# 07: Make `/services/multimodal` editable (page key `service-multimodal`)

**What to build:** `definePageMeta({ cmsPage: 'service-multimodal' })` on the routing file; every visible heading, text, button label and `seo.title`/`seo.description` become Content elements with today's text as default; repeated blocks become Content lists. Navigation (header, footer, subnav) is not editable.

Lists: Included checklist, order steps, directions (from, to, transport); subnav stays in code.

**Blocked by:** 05

- [x] Page renders identically with empty `page_content`
- [x] Every text block and list editable in Edit mode
- [x] Constants moved out of `model/content.ts`; public page tests updated

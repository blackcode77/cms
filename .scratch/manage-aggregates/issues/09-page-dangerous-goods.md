Status: ready-for-agent

# 09: Make `/dangerous-goods` editable (page key `dangerous-goods`)

**What to build:** `definePageMeta({ cmsPage: 'dangerous-goods' })` on the routing file; every visible heading, text, button label and `seo.title`/`seo.description` become Content elements with today's text as default; repeated blocks become Content lists. Navigation (header, footer, subnav) is not editable.

Lists: Acceptance steps, hazard classes (code, name), forbidden cargo checklist.

**Blocked by:** 05

- [ ] Page renders identically with empty `page_content`
- [ ] Every text block and list editable in Edit mode
- [ ] Constants moved out of `model/content.ts`; public page tests updated

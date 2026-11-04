Status: ready-for-agent

# 08: Make `/about` editable (page key `about`)

**What to build:** `definePageMeta({ cmsPage: 'about' })` on the routing file; every visible heading, text, button label and `seo.title`/`seo.description` become Content elements with today's text as default; repeated blocks become Content lists. Navigation (header, footer, subnav) is not editable.

Lists: Companies (icon, name, text, link, link label), managers, history (year, text), facts; partners move to shared site:partners.items, also used by home.

**Blocked by:** 05

- [ ] Page renders identically with empty `page_content`
- [ ] Every text block and list editable in Edit mode
- [ ] Constants moved out of `model/content.ts`; public page tests updated

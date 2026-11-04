Status: ready-for-agent

# 10: Make `/project-cargo` editable (page key `project-cargo`)

**What to build:** `definePageMeta({ cmsPage: 'project-cargo' })` on the routing file; every visible heading, text, button label and `seo.title`/`seo.description` become Content elements with today's text as default; repeated blocks become Content lists. Navigation (header, footer, subnav) is not editable.

Lists: Sample routes checklist, cargo examples (image, title, text).

**Blocked by:** 05

- [ ] Page renders identically with empty `page_content`
- [ ] Every text block and list editable in Edit mode
- [ ] Constants moved out of `model/content.ts`; public page tests updated

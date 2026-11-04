Status: resolved

# 08: Make `/about` editable (page key `about`)

**What to build:** `definePageMeta({ cmsPage: 'about' })` on the routing file; every visible heading, text, button label and `seo.title`/`seo.description` become Content elements with today's text as default; repeated blocks become Content lists. Navigation (header, footer, subnav) is not editable.

Lists: Companies (icon, name, text, link, link label), managers, history (year, text), facts; partners move to shared site:partners.items, also used by home.

**Blocked by:** 05

- [x] Page renders identically with empty `page_content`
- [x] Every text block and list editable in Edit mode
- [x] Constants moved out of `model/content.ts`; public page tests updated

## Notes

- Decisions Q24–Q26: new type `url` (https only, backend `ExternalUrl`), company card links open in a new tab with `rel="noopener"`, empty url = no link line; the first company is the highlighted head company; managers are Items (photo optional, name, role).
- Partners: one list `site:partners.items` rendered by `@widgets/partners-list` on home and about.

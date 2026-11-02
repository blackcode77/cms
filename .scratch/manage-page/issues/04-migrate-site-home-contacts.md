# 04: Make `site`, home and contacts editable

**What to build:** Move hardcoded text and images of the shared header, footer, CTA banner (`site`), the home page and the contacts page into declared Content elements with today's text as defaults, using the components from 03. `usePageSeo` takes `seo.title` and `seo.description` from elements. Visual output with no saved values is unchanged.

**Blocked by:** 03

**Status:** ready-for-agent

- [ ] With an empty `page_content` table the three areas look and read exactly as before
- [ ] Phone, city, headline, hero image, footer texts and SEO title/description are editable end to end
- [ ] A change in `site` (e.g. phone) shows on every page, a change in home does not leak into contacts
- [ ] JSON-LD and meta tags use the edited values
- [ ] Existing frontend tests updated and passing
- [ ] Carried over from 03: in a real browser, Edit mode on a migrated page opens the panel, saving updates the page without reload, and a reload (SSR) shows the saved value

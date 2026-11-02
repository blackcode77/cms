# 04: Make `site`, home and contacts editable

**What to build:** Move hardcoded text and images of the shared header, footer, CTA banner (`site`), the home page and the contacts page into declared Content elements with today's text as defaults, using the components from 03. `usePageSeo` takes `seo.title` and `seo.description` from elements. Visual output with no saved values is unchanged.

**Blocked by:** 03

**Status:** resolved

- [x] With an empty `page_content` table the three areas look and read exactly as before
- [x] Phone, city, headline, hero image, footer texts and SEO title/description are editable end to end
- [x] A change in `site` (e.g. phone) shows on every page, a change in home does not leak into contacts
- [x] JSON-LD and meta tags use the edited values
- [x] Existing frontend tests updated and passing
- [ ] Carried over from 03: in a real browser, Edit mode on a migrated page opens the panel, saving updates the page without reload, and a reload (SSR) shows the saved value

## Notes

- `site` holds shared content: office details (`office.<vladivostok|moscow|nakhodka>.<address|phone|email|hours>`, defaults from `shared/config` `offices`), `footer.about`, `footer.address_moscow`, `cta.*`. Header, footer, contacts and about all read the same office elements, so one edit shows everywhere.
- `home` has `seo.*`, hero, about, why/services/geo/fleet/projects/partners headings and texts, and three images; `contacts` has `seo.*` and `hero.*` (via `PageHero cms-page`).
- Lists and cards (stats, advantages, service cards, gallery, partners, checklists) stay hardcoded until the dynamic-array stage.
- `usePageSeo` takes refs/getters; JSON-LD comes from `organizationJsonLd()` with the edited phone, email and address.
- Verified against the running stack: after `PATCH`es, SSR HTML of `/` and `/contacts` carried the new headline, `<title>`, phone (header, footer, card, `tel:` link, JSON-LD), and a disabled element was absent. The Edit-mode click-through in a real browser was NOT done (no browser available), so that item stays open.

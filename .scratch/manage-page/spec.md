Status: ready-for-agent

# Live editing of public site content

## Problem Statement

All public site text and images are hardcoded in Vue components and `model/content.ts`. The owner cannot change a phone number, a headline or a picture without a developer. The old `page` table (title/slug/content) and its Create/List endpoints are unused and do not fit this need.

## Solution

A signed-in User whose Role holds `edit_page` can switch on **Edit mode** on the public site, click any **Content element**, edit it in a side panel and see it applied immediately. Terms per `GLOSSARY.md`; architecture per ADR 0004 (schema in code, only values in DB, saved per path, no drafts).

- Each Page declares its Content elements (path, type, default = today's hardcoded text) next to the page component. Types in stage 1: `string`, `text`, `html`, `image`; every element can be disabled.
- Backend table `page_content` (`page_key` PK, `content jsonb`, `updated_at`); the old `page` table and `Page` aggregate are removed.
- Public read: a page requests content for two page keys, `site` (shared header, footer, CTA banner) and its own. Unknown key or missing path falls back to defaults.
- Write: `PATCH` with `{path, type, value}`, protected by `edit_page`. Backend validates path format and value size, sanitizes `html` with an allowlist, and accepts `image` only as a URL from our uploads or `/img/...`. It does not know the frontend schema.
- Image upload (also `edit_page`): jpg/png/webp, max 5 MB, no svg, stored on a docker volume, served by nginx at `/uploads/*`.
- Frontend elements are components (`CmsText`, `CmsHtml`, `CmsImage`), not raw attributes. A disabled element renders nothing for visitors and is dimmed in Edit mode.
- `edit_page` is checked on the client after hydration only, so SSR HTML is identical for everyone.
- The editor panel is an `<iframe>` on the admin layout (Nuxt UI, e.g. `/admin/cms-editor`) talking to the public page via `postMessage`, so ADR 0001 (public site has its own CSS) stays intact. After saving, a "applied" toast appears at the side.
- Stage 1 pages: `site`, home, contacts. `seo.title` / `seo.description` are ordinary elements.

## Out of Scope (stage 2, separate spec)

Dynamic arrays (`aggregate` blocks add/remove), file uploads (tariffs), remaining pages and `services/[slug]`, `icon` type, drafts and version history, multi-language.

## Delivery

Four vertical slices, see `issues/`.

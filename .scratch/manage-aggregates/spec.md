Status: ready-for-agent

# Content lists and icons

## Problem Statement

Stage 1 (`.scratch/manage-page`) made single Content elements editable. Repeated blocks — the home advantages grid (icon + title + text), checklists, stats, gallery, partners — are still hardcoded in `pages/*/model/content.ts`, so the owner cannot add, delete, reorder or edit them. Icons are raw SVG strings drawn through `SvgIcon`, which an editor cannot choose.

## Solution

Terms per `GLOSSARY.md` (**Content list**, **Item**, **Icon**); architecture per ADR 0004 and ADR 0005.

- New Content element type `list`: value is an ordered array of Items. The schema in code declares the Item fields (name → type, label), optional `min`/`max`, and the default array (today's hardcoded data). Item field types: `string`, `text`, `html`, `image`, `icon`. Each Item has an `id` generated on the frontend.
- The whole list is one value at one path; every change sends the full array plus the Item field-type map. Saved list fully replaces the default. Last save wins.
- Backend validates generically: array of objects with string values, max Item count, per-field length by its type, `image` URL rule as for standalone images, `icon` name format; `html` fields sanitized with the existing sanitizer.
- New type `icon`: value is an icon name from Lucide (`@iconify-json/lucide`, installed locally — no CDN at SSR). Usable as a standalone element (`CmsIcon page path`) and as an Item field. Rendered as inline SVG during SSR.
- `CmsList` renders Items via a scoped slot (`v-slot="{ item }"`); the page lays out fields itself. In Edit mode the whole list is one editable target; clicking it opens the panel.
- Editor panel for a list: one card per Item with its fields; add, delete, move up/down; `min`/`max` respected. Structural actions save immediately; field edits save the same way as the existing single-element panel. `image` fields use `POST /uploads/image` and the existing picker. Icon picker with search over the whole Lucide collection.
- Empty list: visitors see nothing (no wrapper); Edit mode shows a placeholder to add Items. No per-Item `enabled`; the list as a whole keeps `enabled`.
- All `SvgIcon` usages across the site move to Lucide icons; `SvgIcon` and the raw SVG strings are deleted.

## Scope of this spec

Home: advantages, stats, both checklists, project gallery, partners, service cards (`link`). Every other public page (issues 06–12), with types `link` and `lines`.

## Out of Scope

- File lists on /schedules (issue 13); vessel route geometry and schedule filters stay in code.
- Navigation (header, footer, subnav) is not editable.
- Drafts, history, concurrent-edit merging of lists.

## Delivery

See `issues/`.

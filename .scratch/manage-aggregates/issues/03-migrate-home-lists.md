Status: resolved

# 03: Make the remaining home lists editable

**What to build:** Convert home stats (icon, value, label), both checklists (about, geo; single `text` field), project gallery (image, alt) and partners (name) to Content lists with today's data as defaults; remove the corresponding constants from `pages/home/model/content.ts`.

**Blocked by:** 02

- [x] Home looks the same as before with an empty `page_content`
- [x] Each list can be edited in Edit mode
- [x] Existing home tests updated

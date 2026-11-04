Status: resolved

# 04: Replace all raw SVG icons with Lucide and delete `SvgIcon`

**What to build:** Every `SvgIcon` usage across the site (7 files) switches to the closest Lucide icon via `CmsIcon`/the icon renderer; `SvgIcon` and the raw SVG strings are deleted. Present the old→new icon mapping to the user for approval before merging.

**Blocked by:** 02

- [x] No `SvgIcon` or inline SVG path strings remain in `app/layers`
- [x] Icon mapping table approved by the user
- [x] `make lint` and `make test-front` pass

## Notes

Mapping (old drawing → Lucide): train → `train-front`, anchor → `anchor`, ship → `ship`, globe → `globe`, port building → `building`, cube → `package`, document → `file-text`, truck → `truck`, storage → `warehouse`, warning → `triangle-exclamation-point`, clock → `clock`, pin → `map-pin`, person → `user`, bell → `bell`; home stats: `calendar`, `users`, `award`, `map-pin`, `anchor`.
Route map (`RouteMap`) and the vessel route drawing in `FleetPage` are illustrations, not icons, and stay inline SVG. Mapping approved by the user.

Status: ready-for-agent

# 04: Replace all raw SVG icons with Lucide and delete `SvgIcon`

**What to build:** Every `SvgIcon` usage across the site (7 files) switches to the closest Lucide icon via `CmsIcon`/the icon renderer; `SvgIcon` and the raw SVG strings are deleted. Present the old→new icon mapping to the user for approval before merging.

**Blocked by:** 02

- [ ] No `SvgIcon` or inline SVG path strings remain in `app/layers`
- [ ] Icon mapping table approved by the user
- [ ] `make lint` and `make test-front` pass

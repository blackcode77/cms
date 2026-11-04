# 04: Editing Routes on the map in the editor panel

**What to build:** In the editor panel, a `color` field input (picker + ~8 presets) and a `stops` field editor: the Route map background (taken from the image element the field declares, e.g. `background: 'routes.map'` on the `FieldDef`, resolved by the host before sending `open`), current Route's Stops on top, other Routes of the list faint. Click on empty space appends a Stop and focuses its label; drag moves a marker; below the map: label, side, delete, insert-between. The panel iframe widens to ~900px while such a list is open. Saving stays the existing whole-list «Сохранить». Spec: `../spec.md`.

**Blocked by:** 03

**Status:** resolved

- [x] Coordinates stored in percent with one decimal; drag clamps to 0–100
- [x] «Сохранить» disabled while any Stop label is empty or a Route has < 2 Stops (with a Russian hint)
- [x] Applied change shows on the page without reload (in Edit mode the map is static, no replay)
- [x] Vitest: click adds Stop at the right percent, delete/insert, colour preset sets value, validation hint

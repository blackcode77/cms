Status: resolved

# Route map on the home page

Source: `.claude/tasks/map_shipping/task.md`. Terms per `GLOSSARY.md` (**Route map**, **Route**, **Stop**); content model per ADR 0004 / 0005.

## Problem Statement

The client wants a modern visualisation of the logistics leg: loading ports in China (Taicang → Ningbo → Shanghai) and unloading ports in Russia (Vladivostok (ВМРП / ВМТП) → Vrangel). The existing «География» map (`widgets/route-map`) is static SVG over a map of Russia only: China is not on it, and editors cannot change routes.

## Solution

A new **Route map** block on the home page, placed between the stats strip and «Чем мы занимаемся».

- Editable heading + lead (`CmsText`), a background picture (`image` Content element, default: a new schematic map of eastern China, Korea, Japan and the Russian Far East drawn in the site style from public-domain coastlines) and a Content list of **Routes**. The whole block obeys the usual «Показывать на сайте» switch.
- A **Route**: optional name, line colour (`#rrggbb`), ordered chain of 2–20 **Stops**. A **Stop**: position in percent of the picture (x, y 0–100), a required label (≤ 60 chars, up to 2 lines), a label side (`top|right|bottom|left`, default `right`). Routes do not share Stops. At most 10 Routes.
- Rendering: the line is a smooth curve through all Stops (it may cross land; editors add intermediate Stops if needed). All Stop markers look alike except the first and last of a Route, which are slightly bigger. Labels are HTML over the picture with a fixed font size. A legend under the map lists colour + name of named Routes. On narrow screens the map keeps a min width of ~640px and scrolls horizontally.
- Animation: starts when the block scrolls into view, once per page view. Routes play one after another: Stop marker + label appear, the dashed line "runs" to the next Stop, and so on. Afterwards the lines stay with the dashes slowly flowing in the direction of travel. `prefers-reduced-motion`: everything is drawn at once, static.
- Default content: one Route with the client's five Stops.
- Editing (panel iframe, widened to ~900px while a Route map list is open): the list of Routes (name, colour picker with ~8 site-colour presets, show/delete/reorder as in `ListEditor`); selecting a Route shows its Stops on the map, other Routes faint. Click on empty space appends a Stop and focuses its label; drag a marker to move it; below the map, the Stop list: label, side, delete, insert between. Saved with one «Сохранить» for the whole list.
- Backend: two new Item field types. `color` = `#rrggbb`. `stops` = a JSON-encoded string (Item fields stay strings, ADR 0005 unchanged) validated and re-encoded canonically: 2–20 Stops, numeric x/y in 0–100, non-empty label ≤ 60 chars (plain text, at most one line break), side from the allowed set.

## Revision 2026-11-05

The whole block is the map: full page width, background is all of Russia (highlighted, borders per Natural Earth `admin_0_countries_rus`) with neighbouring countries and labelled seas, down to Ningbo (`/img/map-russia-routes.svg`, Lambert conformal conic, 1920×1439). Heading, lead and legend are a card over the bottom-left of the map; on narrow screens the card stands above the map and the map scrolls sideways (min width 720px). Routes saved against the previous background must be re-placed.

## Revision 2026-11-05 (2)

The block is 600px high. A camera (`fitCamera` in `@entities/route-map`) zooms by default to the Far East with Korea, Japan and Shanghai (`FOCUS`, percent of the picture) and zooms out just enough to fit every Stop of every Route, keeping room for labels and for the text card on desktop. Labels and line thickness stay the same on screen at any zoom. The background was extended east (Pacific) and south so the Far East can sit mid-view: `/img/map-russia-routes.svg` is now 2400×1525, so Stops saved before must be re-placed. The editor panel still shows the whole picture. The camera is measured in the browser; SSR assumes a 1440px block.

## Out of Scope

Changing or replacing the old «География» `RouteMap` (also on `/services`); shared ports between Routes; unlabeled bends; marker types for loading/unloading ports; geographic coordinates.

## Delivery

Four slices, see `issues/`.

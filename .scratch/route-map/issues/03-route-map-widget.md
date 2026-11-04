# 03: Route map widget with animation on the home page

**What to build:** New widget slice `widgets/shipping-map` (the old `widgets/route-map` stays untouched) rendering the background, Routes as smooth dashed curves through their Stops, HTML labels on the chosen side, bigger first/last markers and a legend of named Routes. Animation per spec (in view → Routes sequentially → flowing dashes; reduced motion = static). Add the block to `HomePage.vue` between the stats strip and «Чем мы занимаемся» with elements `routes.title`, `routes.lead`, `routes.map` (image) and `routes.items` (list: `name` string, `color` color, `stops` stops; `max: 10`, default = the client's Route). Spec: `../spec.md`.

**Blocked by:** 01, 02

**Status:** resolved

- [x] SSR renders the final (static) picture so the map is visible without JS; animation is client-only
- [x] Malformed `stops` value in an Item is skipped, not a crash
- [x] Map keeps min width ~640px with horizontal scroll on narrow screens
- [x] Vitest: renders Stops/labels/legend from list content, skips Routes with < 2 Stops, legend omits unnamed Routes

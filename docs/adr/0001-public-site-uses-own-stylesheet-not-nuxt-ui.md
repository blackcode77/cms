# Public site uses its own plain stylesheet, Nuxt UI/Tailwind only in the admin

The approved mockup is a hand-written stylesheet (`shared/styles/site.css`, same class names as the mockup). We ported it as-is and load it only from `layouts/default.vue`. Tailwind and Nuxt UI (`assets/css/main.css`, `UApp`) are imported only from `layouts/admin.vue`, so the public pages don't ship their CSS and its preflight reset doesn't fight the mockup styles. We chose this over rewriting the design in Nuxt UI to keep the approved look exact and the public CSS small.

## Consequences

- Two styling systems live in one app: new public UI is written in the site's CSS classes, new admin UI in Nuxt UI. Don't mix them in the same layout.
- `@nuxt/ui` is still installed globally, so its runtime plugins end up in the public JS bundle; only its CSS is kept out.
- Site CSS is global within the public layout (it styles `body`, `h1`, `a`, `table`, …), so it must not be imported from admin code.

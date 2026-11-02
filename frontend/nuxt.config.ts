import { fileURLToPath } from "node:url"

// Public pages are static content for now: serve from cache and revalidate in the background.
// Disabled in dev so edits show up immediately.
const publicPage = process.env.NODE_ENV === 'production' ? { swr: 600 } : {}

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  modules: ['@nuxt/ui', '@nuxt/image', '@nuxt/eslint'],

  // Tailwind + Nuxt UI styles are imported by the admin layout only (layouts/admin.vue),
  // the public site ships its own plain stylesheet (layouts/default.vue).
  app: { head: { htmlAttrs: { lang: 'ru' }, charset: 'utf-8', viewport: 'width=device-width, initial-scale=1' } },

  image: {
    format: ['webp'],
    quality: 75,
    screens: { xs: 320, sm: 640, md: 768, lg: 1024, xl: 1280, xxl: 1600, '2xl': 1920 },
  },

  // Nuxt UI would download web fonts from external CDNs at dev/build time; use system fonts instead.
  ui: { fonts: false },

  // Admin is dark-only (Nuxt UI dashboard look); the public site has its own stylesheet.
  colorMode: { preference: 'dark', fallback: 'dark', storageKey: 'cms-color-mode' },

  // FSD slices are imported explicitly through their public API (index.ts), never auto-imported.
  components: { dirs: [] },
  alias: {
    '@pages': fileURLToPath(new URL('./app/layers/pages', import.meta.url)),
    '@widgets': fileURLToPath(new URL('./app/layers/widgets', import.meta.url)),
    '@features': fileURLToPath(new URL('./app/layers/features', import.meta.url)),
    '@entities': fileURLToPath(new URL('./app/layers/entities', import.meta.url)),
    '@shared': fileURLToPath(new URL('./app/layers/shared', import.meta.url)),
  },

  runtimeConfig: {
    // Server-side (SSR) base URL of the backend, reachable only inside the docker network.
    // Overridden by NUXT_API_INTERNAL_URL.
    apiInternalUrl: 'http://nginx/api',
    public: {
      // Canonical origin of the public site (canonical URLs, og:image, sitemap). Overridden by NUXT_PUBLIC_SITE_URL.
      siteUrl: 'https://garant-intermodal.ru',
      // Browser-side base URL (same origin, proxied by nginx). Overridden by NUXT_PUBLIC_API_BASE.
      apiBase: '/api',
    },
  },

  routeRules: {
    // Admin is a SPA, everything else is server-side rendered.
    '/admin/**': { ssr: false },
    '/': publicPage,
    '/about': publicPage,
    '/services/**': publicPage,
    '/services': publicPage,
    '/fleet': publicPage,
    '/schedules': publicPage,
    '/dangerous-goods': publicPage,
    '/contacts': publicPage,
    '/project-cargo': publicPage,
    // Hashed build assets never change; static media and fonts change rarely.
    '/_nuxt/**': { headers: { 'cache-control': 'public, max-age=31536000, immutable' } },
    '/fonts/**': { headers: { 'cache-control': 'public, max-age=31536000, immutable' } },
    '/img/**': { headers: { 'cache-control': 'public, max-age=2592000' } },
  },

  vite: {
    // HMR websocket goes through nginx on the public port.
    server: { allowedHosts: true },
  },
})

import { fileURLToPath } from "node:url"

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  modules: ['@nuxt/ui', '@nuxt/eslint'],
  css: ['~/assets/css/main.css'],

  // Nuxt UI would download web fonts from external CDNs at dev/build time; use system fonts instead.
  ui: { fonts: false },

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
      // Browser-side base URL (same origin, proxied by nginx). Overridden by NUXT_PUBLIC_API_BASE.
      apiBase: '/api',
    },
  },

  routeRules: {
    // Admin is a SPA, everything else is server-side rendered.
    '/admin/**': { ssr: false },
  },

  vite: {
    // HMR websocket goes through nginx on the public port.
    server: { allowedHosts: true },
  },
})

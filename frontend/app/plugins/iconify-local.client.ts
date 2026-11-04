import { addAPIProvider } from '@iconify/vue'

/**
 * Points Iconify in the browser at our own icon endpoint (`icon.localApiEndpoint`, served from the locally
 * installed Lucide collection). @nuxt/icon does this only on the server: in the browser it keeps Iconify's
 * default provider because @iconify/vue 5 ships two default CDN hosts and @nuxt/icon expects one. Without
 * this, every icon outside the client bundle (e.g. one picked in the editor) is requested from the
 * Iconify CDN and stays empty where the CDN is unreachable.
 */
export default defineNuxtPlugin({
  name: 'iconify-local',
  dependsOn: ['@nuxt/icon'],
  setup() {
    const baseURL = useRuntimeConfig().app.baseURL.replace(/\/$/, '')
    const endpoint = useAppConfig().icon.localApiEndpoint ?? '/_nuxt_icon'
    addAPIProvider('', { resources: [baseURL + endpoint] })
  },
})

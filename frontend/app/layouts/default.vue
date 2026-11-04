<script setup lang="ts">
import '@shared/styles'
import { SiteHeader } from '@widgets/site-header'
import { SiteFooter } from '@widgets/site-footer'
import { CmsEditorControls } from '@widgets/cms-editor'

useHead({
  link: [
    { rel: 'preload', as: 'font', type: 'font/woff2', href: '/fonts/roboto-400.woff2', crossorigin: 'anonymous' },
    { rel: 'preload', as: 'font', type: 'font/woff2', href: '/fonts/robotocondensed-700.woff2', crossorigin: 'anonymous' },
    { key: 'favicon', rel: 'icon', type: 'image/png', href: '/favicon.png' },
  ],
  meta: [{ name: 'theme-color', content: '#3A7B34' }],
})

// Splash screen: only on the initial full page load (SSR + hydration), never on in-site navigation.
const nuxtApp = useNuxtApp()
const showSplash = ref(import.meta.server || nuxtApp.isHydrating)
onMounted(() => {
  if (showSplash.value) setTimeout(() => { showSplash.value = false }, 1700)
})
</script>

<template>
  <div class="site">
    <div v-if="showSplash" class="splash" aria-hidden="true">
      <img src="/img/logo-white.png" alt="" width="378" height="190" fetchpriority="high">
    </div>
    <SiteHeader />
    <main id="main">
      <slot />
    </main>
    <SiteFooter />
    <CmsEditorControls />
  </div>
</template>

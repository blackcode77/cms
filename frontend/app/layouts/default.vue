<script setup lang="ts">
import '@shared/styles'
import { SiteHeader } from '@widgets/site-header'
import { SiteFooter } from '@widgets/site-footer'

useHead({
  link: [
    { rel: 'preload', as: 'font', type: 'font/woff2', href: '/fonts/roboto-400.woff2', crossorigin: 'anonymous' },
    { rel: 'preload', as: 'font', type: 'font/woff2', href: '/fonts/robotocondensed-700.woff2', crossorigin: 'anonymous' },
    { rel: 'icon', type: 'image/png', href: '/favicon.png' },
  ],
  meta: [{ name: 'theme-color', content: '#3A7B34' }],
})
</script>

<template>
  <div>
    <SiteHeader />
    <main id="main">
      <slot />
    </main>
    <SiteFooter />
  </div>
</template>

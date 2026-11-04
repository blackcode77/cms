<script setup lang="ts">
// Routing only: the page itself lives in the FSD layer.
import { AboutPage } from '@pages/about'

definePageMeta({ cmsPage: 'about' })
</script>

<template>
  <AboutPage />
</template>

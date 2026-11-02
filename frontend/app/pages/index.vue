<script setup lang="ts">
// Routing only: the page itself lives in the FSD layer.
import { HomePage } from '@pages/home'

definePageMeta({ cmsPage: 'home' })
</script>

<template>
  <HomePage />
</template>

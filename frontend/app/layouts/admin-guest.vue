<script setup lang="ts">
useHead({
  link: [{ key: 'favicon', rel: 'icon', type: 'image/svg+xml', href: '/admin-favicon.svg' }],
})
</script>

<template>
  <UApp>
    <slot />
  </UApp>
</template>

<style>
@import "~/assets/css/main.css";
</style>

<script setup lang="ts">
// `inner` is trusted, hardcoded SVG markup (paths/circles) in a 24x24 viewBox.
defineProps<{ inner: string }>()
</script>

<template>
  <!-- eslint-disable-next-line vue/no-v-html -->
  <svg viewBox="0 0 24 24" aria-hidden="true" v-html="inner" />
</template>

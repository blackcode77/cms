<script setup lang="ts">
/**
 * Image whose source is a Content value: uploads are served by nginx, not by the image optimizer,
 * so they use a plain <img>; bundled `/img/...` images go through NuxtImg. Other attributes pass through.
 */
const props = defineProps<{ src: string, alt?: string }>()

const isUpload = computed(() => props.src.startsWith('/uploads/'))
</script>

<template>
  <img v-if="isUpload" :src="src" :alt="alt ?? ''">
  <NuxtImg v-else :src="src" :alt="alt ?? ''" />
</template>

<script setup lang="ts">
import { useCmsElement } from '../model/use-cms-element'

defineOptions({ inheritAttrs: false })

const props = defineProps<{
  page: string
  path: string
  alt?: string
}>()

const { element, visible, editAttrs } = useCmsElement(() => props.page, () => props.path)
// Uploads are served by nginx, not by the image optimizer, so they use a plain <img>.
const isUpload = computed(() => element.value?.value.startsWith('/uploads/') ?? false)
</script>

<template>
  <template v-if="visible && element">
    <img v-if="isUpload" v-bind="{ ...$attrs, ...editAttrs }" :src="element.value" :alt="alt ?? ''">
    <NuxtImg v-else v-bind="{ ...$attrs, ...editAttrs }" :src="element.value" :alt="alt ?? ''" />
  </template>
</template>

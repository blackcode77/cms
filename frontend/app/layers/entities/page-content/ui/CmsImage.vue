<script setup lang="ts">
import { useCmsElement } from '../model/use-cms-element'
import ContentImage from './ContentImage.vue'

defineOptions({ inheritAttrs: false })

const props = defineProps<{
  page: string
  path: string
  alt?: string
}>()

const { element, visible, editAttrs } = useCmsElement(() => props.page, () => props.path)
</script>

<template>
  <ContentImage v-if="visible && element" v-bind="{ ...$attrs, ...editAttrs }" :src="element.value" :alt="alt" />
</template>

<script setup lang="ts">
import { useCmsElement } from '../model/use-cms-element'

const props = withDefaults(defineProps<{
  page: string
  path: string
  /** Tag to render, e.g. `h1`, so existing markup is kept. */
  as?: string
}>(), { as: 'span' })

const { element, visible, editAttrs } = useCmsElement(() => props.page, () => props.path)

// `text` keeps its line breaks; the style attribute only exists when needed.
const attrs = computed(() => element.value?.type === 'text'
  ? { ...editAttrs.value, style: { whiteSpace: 'pre-line' } }
  : editAttrs.value)
</script>

<template>
  <component :is="as" v-if="visible && element" v-bind="attrs">{{ element.value }}</component>
</template>

<script setup lang="ts">
import { useCmsElement } from '../model/use-cms-element'

const props = withDefaults(defineProps<{
  page: string
  path: string
  /** Tag to render, e.g. `h1`, so existing markup is kept. */
  as?: string
}>(), { as: 'span' })

const { element, visible, editAttrs } = useCmsElement(() => props.page, () => props.path)
</script>

<template>
  <component
    :is="as"
    v-if="visible && element"
    v-bind="editAttrs"
    :style="element.type === 'text' ? { whiteSpace: 'pre-line' } : undefined"
  >{{ element.value }}</component>
</template>

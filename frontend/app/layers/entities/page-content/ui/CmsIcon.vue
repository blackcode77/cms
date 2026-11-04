<script setup lang="ts">
import { AppIcon } from '@shared/ui'
import { useCmsElement } from '../model/use-cms-element'

/** A standalone `icon` Content element. Icons inside Items are rendered with `AppIcon` from `@shared/ui`. */
const props = withDefaults(defineProps<{
  page: string
  path: string
  as?: string
}>(), { as: 'span' })

const { element, visible, editAttrs } = useCmsElement(() => props.page, () => props.path)
</script>

<template>
  <component :is="as" v-if="visible && element" v-bind="editAttrs">
    <AppIcon v-if="element.value" :name="element.value" />
  </component>
</template>

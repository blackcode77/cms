<script setup lang="ts">
import { useCmsList } from '../model/use-cms-element'
import type { ListItem } from '../model/types'

const props = withDefaults(defineProps<{
  page: string
  path: string
  /** Wrapper tag, e.g. `ul`, so existing markup is kept. */
  as?: string
}>(), { as: 'div' })

defineSlots<{
  default(props: { item: ListItem, index: number }): unknown
}>()

const { items, visible, editAttrs, editing } = useCmsList(() => props.page, () => props.path)
// An empty list placeholder must be a valid child of the wrapper.
const placeholderTag = computed(() => ['ul', 'ol'].includes(props.as) ? 'li' : props.as === 'tbody' ? 'tr' : 'div')
</script>

<template>
  <component :is="as" v-if="visible" v-bind="editAttrs">
    <template v-for="(item, index) in items" :key="item.id">
      <slot :item="item" :index="index" />
    </template>
    <component :is="placeholderTag" v-if="editing && items.length === 0" class="cms-list-empty" data-testid="cms-list-empty">
      <td v-if="placeholderTag === 'tr'" colspan="99">Список пуст — нажмите, чтобы добавить</td>
      <template v-else>Список пуст — нажмите, чтобы добавить</template>
    </component>
  </component>
</template>

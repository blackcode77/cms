<script setup lang="ts">
import { Icon } from '#components'

/**
 * An icon of the site's single collection (Lucide), rendered as inline SVG during SSR from the locally
 * installed collection. `name` is the bare icon name, e.g. `map-pin`.
 */
const props = defineProps<{ name: string }>()

const iconName = computed(() => `i-lucide-${props.name}`)
</script>

<template>
  <Icon :name="iconName" aria-hidden="true" />
</template>

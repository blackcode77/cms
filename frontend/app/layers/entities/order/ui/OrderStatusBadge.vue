<script setup lang="ts">
import { orderStatuses } from '../model/status'
import type { OrderStatus } from '../model/types'

defineProps<{ status: OrderStatus }>()
</script>

<template>
  <UBadge :color="orderStatuses[status].color" variant="subtle" :label="orderStatuses[status].label" />
</template>

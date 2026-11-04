<script setup lang="ts">
import { useCurrentUser } from '@entities/session'
import { fetchManagers, fetchOrders, orderNumber, orderStatusOptions } from '@entities/order'
import type { CreateOrderResult, OrderStatus } from '@entities/order'
import { CreateOrderButton } from '@features/create-order'
import { OrdersTable } from '@widgets/orders-table'

const { can } = useCurrentUser()

const ALL = 'all'
const status = ref<OrderStatus | typeof ALL>(ALL)
const mine = ref(false)
const statusItems = [{ label: 'Все статусы', value: ALL }, ...orderStatusOptions]

const filter = computed(() => ({
  status: status.value === ALL ? undefined : status.value,
  manager: mine.value ? 'me' : undefined,
}))
const { data: orders, refresh } = await useAsyncData(
  () => `admin-orders-${filter.value.status ?? ALL}-${filter.value.manager ?? 'any'}`,
  () => fetchOrders(filter.value),
  { default: () => [] },
)
const { data: managers } = await useAsyncData('admin-orders-managers', fetchManagers, { default: () => [] })

const notice = ref('')
async function onCreated(result: CreateOrderResult) {
  notice.value = `Заказ ${orderNumber(result.number)} создан.`
  await refresh()
}
</script>

<template>
  <UDashboardPanel id="orders">
    <template #header>
      <UDashboardNavbar title="Заказы">
        <template #right>
          <CreateOrderButton v-if="can('manage_orders')" :managers="managers" @created="onCreated" />
        </template>
      </UDashboardNavbar>
      <UDashboardToolbar>
        <div class="flex items-center gap-4">
          <USelect v-model="status" :items="statusItems" class="w-48" data-testid="orders-status-filter" />
          <USwitch v-model="mine" label="Только мои" data-testid="orders-mine-filter" />
        </div>
      </UDashboardToolbar>
    </template>
    <template #body>
      <UAlert v-if="notice" color="success" variant="subtle" :description="notice" data-testid="orders-notice" />
      <OrdersTable :orders="orders" :managers="managers" @changed="refresh" />
    </template>
  </UDashboardPanel>
</template>

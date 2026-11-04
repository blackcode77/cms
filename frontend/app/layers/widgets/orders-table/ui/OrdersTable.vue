<script setup lang="ts">
import type { TableColumn, TableRow } from '@nuxt/ui'
import { useCurrentUser } from '@entities/session'
import { OrderStatusBadge, orderNumber, orderStatuses, orderSummary } from '@entities/order'
import type { ManagerOption, OrderItem } from '@entities/order'
import { DeleteOrderButton } from '@features/delete-order'
import { EditOrderButton } from '@features/edit-order'
import { formatDateTime, fullName } from '@shared/lib'

/** Orders, newest first, tinted by status; editing needs `manage_orders`. */
const props = withDefaults(defineProps<{
  orders: OrderItem[]
  managers: ManagerOption[]
  /** Hide the Client column (e.g. on the Client's own page). */
  hideClient?: boolean
}>(), { hideClient: false })
const emit = defineEmits<{ changed: [] }>()

const { can } = useCurrentUser()

const columns = computed<TableColumn<OrderItem>[]>(() => [
  { id: 'number', header: '№' },
  { id: 'created_at', header: 'Поступил' },
  ...(props.hideClient ? [] : [{ id: 'client', header: 'Клиент' }]),
  { id: 'summary', header: 'Описание' },
  { id: 'status', header: 'Статус' },
  { id: 'manager', header: 'Менеджер' },
  { id: 'comment', header: 'Комментарий' },
  ...(can('manage_orders') ? [{ id: 'actions', header: '' }] : []),
])

const meta = {
  class: { tr: (row: TableRow<OrderItem>) => orderStatuses[row.original.status].rowClass },
}
</script>

<template>
  <UTable :data="orders" :columns="columns" :meta="meta" empty="Заказов нет" data-testid="orders-table">
    <template #number-cell="{ row }">
      <span class="font-medium whitespace-nowrap">{{ orderNumber(row.original.number) }}</span>
    </template>
    <template #created_at-cell="{ row }">
      <span class="whitespace-nowrap">{{ formatDateTime(row.original.created_at) }}</span>
    </template>
    <template #client-cell="{ row }">
      <NuxtLink :to="`/admin/clients/${row.original.client.id}`" class="hover:underline">
        <span class="block">{{ fullName(row.original.client) || 'Без имени' }}</span>
        <span class="block text-muted whitespace-nowrap">{{ row.original.client.phone }}</span>
      </NuxtLink>
    </template>
    <template #summary-cell="{ row }">
      <div class="max-w-xs whitespace-normal">
        <span v-if="orderSummary(row.original)" class="block">{{ orderSummary(row.original) }}</span>
        <span v-if="row.original.message" class="block text-muted line-clamp-2" :title="row.original.message">
          {{ row.original.message }}
        </span>
      </div>
    </template>
    <template #status-cell="{ row }">
      <OrderStatusBadge :status="row.original.status" />
    </template>
    <template #manager-cell="{ row }">
      <span v-if="row.original.manager">{{ row.original.manager.name }}</span>
      <span v-else class="text-muted">—</span>
    </template>
    <template #comment-cell="{ row }">
      <p class="max-w-xs whitespace-pre-line line-clamp-3" :title="row.original.comment">{{ row.original.comment }}</p>
    </template>
    <template #actions-cell="{ row }">
      <div class="flex gap-2">
        <EditOrderButton :order="row.original" :managers="managers" @updated="emit('changed')" />
        <DeleteOrderButton :order="row.original" @deleted="emit('changed')" />
      </div>
    </template>
  </UTable>
</template>

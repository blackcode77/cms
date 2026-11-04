<script setup lang="ts">
import { useCurrentUser } from '@entities/session'
import { fetchClient } from '@entities/client'
import { fetchManagers } from '@entities/order'
import type { OrderItem } from '@entities/order'
import { CreateOrderButton } from '@features/create-order'
import { DeleteClientButton } from '@features/delete-client'
import { EditClientButton } from '@features/edit-client'
import { OrdersTable } from '@widgets/orders-table'
import { formatDateTime, fullName } from '@shared/lib'

const props = defineProps<{ id: string }>()

const { can } = useCurrentUser()

const { data: client, refresh, error } = await useAsyncData(
  `admin-client-${props.id}`,
  () => fetchClient<OrderItem>(props.id),
)
const { data: managers } = await useAsyncData(
  'admin-orders-managers',
  () => can('view_orders') ? fetchManagers() : Promise.resolve([]),
  { default: () => [] },
)

async function onDeleted() {
  await navigateTo('/admin/clients')
}
</script>

<template>
  <UDashboardPanel id="client">
    <template #header>
      <UDashboardNavbar :title="client ? (fullName(client) || client.phone) : 'Клиент'">
        <template #leading>
          <UButton icon="i-lucide-arrow-left" color="neutral" variant="ghost" to="/admin/clients" aria-label="К списку клиентов" />
        </template>
        <template v-if="client" #right>
          <template v-if="can('manage_clients')">
            <EditClientButton :client="client" @updated="refresh" />
            <DeleteClientButton :client="client" @deleted="onDeleted" />
          </template>
        </template>
      </UDashboardNavbar>
    </template>
    <template #body>
      <UAlert v-if="error" color="error" variant="subtle" description="Клиент не найден." />
      <template v-if="client">
        <UCard data-testid="client-card">
          <dl class="grid grid-cols-[max-content_1fr] gap-x-6 gap-y-2">
            <dt class="text-muted">Телефон</dt>
            <dd>{{ client.phone }}</dd>
            <dt class="text-muted">Фамилия</dt>
            <dd>{{ client.last_name || '—' }}</dd>
            <dt class="text-muted">Имя</dt>
            <dd>{{ client.first_name || '—' }}</dd>
            <dt class="text-muted">Отчество</dt>
            <dd>{{ client.middle_name || '—' }}</dd>
            <dt class="text-muted">Эл. почта</dt>
            <dd>{{ client.email || '—' }}</dd>
            <dt class="text-muted">Добавлен</dt>
            <dd>{{ formatDateTime(client.created_at) }}</dd>
            <dt class="text-muted">Комментарий</dt>
            <dd class="whitespace-pre-line">{{ client.comment || '—' }}</dd>
          </dl>
        </UCard>
        <div v-if="can('view_orders')" class="space-y-3">
          <div class="flex items-center justify-between">
            <h2 class="font-semibold">Заказы</h2>
            <CreateOrderButton v-if="can('manage_orders')" :managers="managers" :phone="client.phone" @created="() => refresh()" />
          </div>
          <OrdersTable :orders="client.orders" :managers="managers" hide-client @changed="refresh" />
        </div>
      </template>
    </template>
  </UDashboardPanel>
</template>

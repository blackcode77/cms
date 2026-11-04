<script setup lang="ts">
import type { TableColumn } from '@nuxt/ui'
import { useCurrentUser } from '@entities/session'
import { fetchClients } from '@entities/client'
import type { ClientItem } from '@entities/client'
import { CreateClientButton } from '@features/create-client'
import { DeleteClientButton } from '@features/delete-client'
import { EditClientButton } from '@features/edit-client'
import { formatDateTime, fullName } from '@shared/lib'

const { can } = useCurrentUser()

const search = ref('')
const query = ref('')
let debounce: ReturnType<typeof setTimeout> | undefined
watch(search, (value) => {
  clearTimeout(debounce)
  debounce = setTimeout(() => (query.value = value), 300)
})

const { data: clients, refresh } = await useAsyncData(
  () => `admin-clients-${query.value.trim()}`,
  () => fetchClients(query.value.trim()),
  { default: () => [] },
)

const columns = computed<TableColumn<ClientItem>[]>(() => [
  { id: 'name', header: 'ФИО' },
  { accessorKey: 'phone', header: 'Телефон' },
  { id: 'email', header: 'Эл. почта' },
  { accessorKey: 'orders_count', header: 'Заказов' },
  { id: 'created_at', header: 'Добавлен' },
  { id: 'comment', header: 'Комментарий' },
  ...(can('manage_clients') ? [{ id: 'actions', header: '' }] : []),
])

async function onCreated(id: string) {
  await navigateTo(`/admin/clients/${id}`)
}
</script>

<template>
  <UDashboardPanel id="clients">
    <template #header>
      <UDashboardNavbar title="Клиенты">
        <template #right>
          <CreateClientButton v-if="can('manage_clients')" @created="onCreated" />
        </template>
      </UDashboardNavbar>
      <UDashboardToolbar>
        <UInput
          v-model="search"
          icon="i-lucide-search"
          placeholder="Телефон, ФИО или почта"
          class="w-80"
          data-testid="clients-search"
        />
      </UDashboardToolbar>
    </template>
    <template #body>
      <UTable :data="clients" :columns="columns" empty="Клиентов нет" data-testid="clients-table">
        <template #name-cell="{ row }">
          <NuxtLink :to="`/admin/clients/${row.original.id}`" class="font-medium hover:underline">
            {{ fullName(row.original) || 'Без имени' }}
          </NuxtLink>
        </template>
        <template #email-cell="{ row }">
          {{ row.original.email ?? '' }}
        </template>
        <template #created_at-cell="{ row }">
          <span class="whitespace-nowrap">{{ formatDateTime(row.original.created_at) }}</span>
        </template>
        <template #comment-cell="{ row }">
          <p class="max-w-xs whitespace-pre-line line-clamp-2" :title="row.original.comment">{{ row.original.comment }}</p>
        </template>
        <template #actions-cell="{ row }">
          <div class="flex gap-2">
            <EditClientButton :client="row.original" @updated="refresh" />
            <DeleteClientButton :client="row.original" @deleted="refresh" />
          </div>
        </template>
      </UTable>
    </template>
  </UDashboardPanel>
</template>

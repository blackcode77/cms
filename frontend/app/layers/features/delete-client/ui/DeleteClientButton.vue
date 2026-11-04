<script setup lang="ts">
import { deleteClient } from '@entities/client'
import type { ClientItem } from '@entities/client'
import { apiErrorMessage, fullName, plural } from '@shared/lib'

const props = defineProps<{ client: ClientItem }>()
const emit = defineEmits<{ deleted: [] }>()

const open = ref(false)
const error = ref('')

const ordersWarning = computed(() => {
  const count = props.client.orders_count
  return count === 0
    ? 'У клиента нет заказов.'
    : `Вместе с клиентом будут удалены ${count} ${plural(count, ['заказ', 'заказа', 'заказов'])}.`
})

function ask() {
  error.value = ''
  open.value = true
}

async function confirm() {
  error.value = ''
  try {
    await deleteClient(props.client.id)
    open.value = false
    emit('deleted')
  }
  catch (e) {
    error.value = apiErrorMessage(e, 'Не удалось удалить клиента.')
  }
}
</script>

<template>
  <UButton
    icon="i-lucide-trash-2"
    color="error"
    variant="outline"
    size="xs"
    aria-label="Удалить"
    :data-testid="`client-delete-${client.id}`"
    @click="ask"
  />
  <UModal v-model:open="open" title="Удаление клиента">
    <template #body>
      <p>Удалить клиента {{ fullName(client) || client.phone }}?</p>
      <UAlert
        class="mt-3"
        :color="client.orders_count > 0 ? 'error' : 'neutral'"
        variant="subtle"
        icon="i-lucide-triangle-alert"
        :description="`${ordersWarning} Это нельзя отменить.`"
        data-testid="delete-client-warning"
      />
      <p v-if="error" class="mt-3 text-sm text-error">{{ error }}</p>
      <div class="mt-4 flex justify-end gap-2">
        <UButton color="neutral" variant="ghost" label="Отмена" @click="open = false" />
        <UButton color="error" label="Удалить" data-testid="client-delete-confirm" @click="confirm" />
      </div>
    </template>
  </UModal>
</template>

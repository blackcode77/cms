<script setup lang="ts">
import { deleteOrder, orderNumber } from '@entities/order'
import type { OrderItem } from '@entities/order'
import { apiErrorMessage } from '@shared/lib'

const props = defineProps<{ order: OrderItem }>()
const emit = defineEmits<{ deleted: [] }>()

const open = ref(false)
const error = ref('')

function ask() {
  error.value = ''
  open.value = true
}

async function confirm() {
  error.value = ''
  try {
    await deleteOrder(props.order.id)
    open.value = false
    emit('deleted')
  }
  catch (e) {
    error.value = apiErrorMessage(e, 'Не удалось удалить заказ.')
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
    :data-testid="`order-delete-${order.id}`"
    @click="ask"
  />
  <UModal v-model:open="open" title="Удаление заказа">
    <template #body>
      <p>Удалить заказ {{ orderNumber(order.number) }}? Клиент останется. Это нельзя отменить.</p>
      <p v-if="error" class="mt-3 text-sm text-error">{{ error }}</p>
      <div class="mt-4 flex justify-end gap-2">
        <UButton color="neutral" variant="ghost" label="Отмена" @click="open = false" />
        <UButton color="error" label="Удалить" data-testid="order-delete-confirm" @click="confirm" />
      </div>
    </template>
  </UModal>
</template>

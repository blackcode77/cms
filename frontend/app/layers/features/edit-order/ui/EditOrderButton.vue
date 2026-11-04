<script setup lang="ts">
import { CargoFieldsInput, orderNumber, orderStatusOptions, updateOrder } from '@entities/order'
import type { ManagerOption, OrderItem, UpdateOrderInput } from '@entities/order'
import { apiErrorMessage } from '@shared/lib'

const props = defineProps<{ order: OrderItem, managers: ManagerOption[] }>()
const emit = defineEmits<{ updated: [] }>()

const NO_MANAGER = 'none'
const open = ref(false)
const state = reactive<UpdateOrderInput>({
  status: 'request',
  manager_id: null,
  from: '',
  to: '',
  cargo_type: '',
  weight: '',
  message: '',
  comment: '',
})
const managerId = ref(NO_MANAGER)
const error = ref('')
const pending = ref(false)

const managerItems = computed(() => {
  const items = [
    { label: 'Не назначен', value: NO_MANAGER },
    ...props.managers.map(m => ({ label: m.name || m.email, value: m.id })),
  ]
  // a Manager who has lost the Permission stays selectable for this Order
  const current = props.order.manager
  if (current && !items.some(item => item.value === current.id)) {
    items.push({ label: current.name, value: current.id })
  }
  return items
})

function show() {
  const { order } = props
  Object.assign(state, {
    status: order.status,
    from: order.from,
    to: order.to,
    cargo_type: order.cargo_type,
    weight: order.weight,
    message: order.message,
    comment: order.comment,
  })
  managerId.value = order.manager?.id ?? NO_MANAGER
  error.value = ''
  open.value = true
}

async function save() {
  error.value = ''
  pending.value = true
  try {
    await updateOrder(props.order.id, { ...state, manager_id: managerId.value === NO_MANAGER ? null : managerId.value })
    open.value = false
    emit('updated')
  }
  catch (e) {
    error.value = apiErrorMessage(e, 'Не удалось сохранить заказ.')
  }
  finally {
    pending.value = false
  }
}
</script>

<template>
  <UButton
    icon="i-lucide-pencil"
    color="neutral"
    variant="outline"
    size="xs"
    label="Изменить"
    :data-testid="`order-edit-${order.id}`"
    @click="show"
  />
  <UModal v-model:open="open" :title="`Заказ ${orderNumber(order.number)}`" :ui="{ content: 'max-w-2xl' }">
    <template #body>
      <form class="space-y-4" data-testid="edit-order-form" @submit.prevent="save">
        <div class="grid grid-cols-2 gap-4">
          <UFormField label="Статус">
            <USelect v-model="state.status" :items="orderStatusOptions" class="w-full" data-testid="edit-order-status" />
          </UFormField>
          <UFormField label="Менеджер">
            <USelect v-model="managerId" :items="managerItems" class="w-full" data-testid="edit-order-manager" />
          </UFormField>
        </div>
        <UFormField label="Комментарий" help="Виден только в админке">
          <UTextarea v-model="state.comment" :rows="3" class="w-full" data-testid="edit-order-comment" />
        </UFormField>
        <CargoFieldsInput v-model:cargo="state" />
        <UFormField label="Сообщение клиента">
          <UTextarea v-model="state.message" :rows="2" class="w-full" />
        </UFormField>
        <p v-if="error" class="text-sm text-error" data-testid="edit-order-error">{{ error }}</p>
        <div class="flex justify-end gap-2">
          <UButton color="neutral" variant="ghost" label="Отмена" @click="open = false" />
          <UButton type="submit" label="Сохранить" :loading="pending" data-testid="edit-order-save" />
        </div>
      </form>
    </template>
  </UModal>
</template>

<script setup lang="ts">
import { ClientFieldsInput, findClientByPhone } from '@entities/client'
import type { ClientItem } from '@entities/client'
import { CargoFieldsInput, createOrder, orderStatusOptions } from '@entities/order'
import type { CreateOrderInput, CreateOrderResult, ManagerOption } from '@entities/order'
import { apiErrorMessage, apiFieldErrors, fullName } from '@shared/lib'

const props = defineProps<{ managers: ManagerOption[], phone?: string }>()
const emit = defineEmits<{ created: [result: CreateOrderResult] }>()

const NO_MANAGER = 'none'
const blank = (): CreateOrderInput => ({
  phone: props.phone ?? '',
  first_name: '',
  last_name: '',
  middle_name: '',
  email: '',
  status: 'request',
  manager_id: null,
  from: '',
  to: '',
  cargo_type: '',
  weight: '',
  message: '',
  comment: '',
})

const open = ref(false)
const state = reactive(blank())
const managerId = ref(NO_MANAGER)
const existing = ref<ClientItem | null>(null)
const error = ref('')
const fieldErrors = ref<Record<string, string>>({})
const pending = ref(false)

const managerItems = computed(() => [
  { label: 'Не назначен', value: NO_MANAGER },
  ...props.managers.map(m => ({ label: m.name || m.email, value: m.id })),
])

function show() {
  Object.assign(state, blank())
  managerId.value = NO_MANAGER
  existing.value = null
  error.value = ''
  fieldErrors.value = {}
  open.value = true
}

// hint which Client the Order will go to (the phone identifies the Client)
let lookup: ReturnType<typeof setTimeout> | undefined
watch(() => state.phone, (phone) => {
  clearTimeout(lookup)
  existing.value = null
  if (phone.replace(/\D/g, '').length < 10) return
  lookup = setTimeout(async () => {
    try {
      existing.value = await findClientByPhone(phone)
    }
    catch {
      existing.value = null
    }
  }, 400)
})

async function save() {
  error.value = ''
  fieldErrors.value = {}
  pending.value = true
  try {
    const result = await createOrder({ ...state, manager_id: managerId.value === NO_MANAGER ? null : managerId.value })
    open.value = false
    emit('created', result)
  }
  catch (e) {
    fieldErrors.value = apiFieldErrors(e)
    error.value = apiErrorMessage(e, 'Не удалось создать заказ.')
  }
  finally {
    pending.value = false
  }
}
</script>

<template>
  <UButton icon="i-lucide-plus" label="Добавить заказ" data-testid="order-create" @click="show" />
  <UModal v-model:open="open" title="Новый заказ" :ui="{ content: 'max-w-2xl' }">
    <template #body>
      <form class="space-y-4" data-testid="create-order-form" @submit.prevent="save">
        <ClientFieldsInput v-model:client="state" :errors="fieldErrors" />
        <UAlert
          v-if="existing"
          color="info"
          variant="subtle"
          icon="i-lucide-user-check"
          :description="`Найден клиент: ${fullName(existing) || existing.phone}. Заказ будет добавлен к нему, заполнятся только пустые поля клиента.`"
          data-testid="create-order-existing-client"
        />
        <USeparator />
        <div class="grid grid-cols-2 gap-4">
          <UFormField label="Статус">
            <USelect v-model="state.status" :items="orderStatusOptions" class="w-full" />
          </UFormField>
          <UFormField label="Менеджер">
            <USelect v-model="managerId" :items="managerItems" class="w-full" />
          </UFormField>
        </div>
        <CargoFieldsInput v-model:cargo="state" />
        <UFormField label="Сообщение клиента">
          <UTextarea v-model="state.message" :rows="2" class="w-full" />
        </UFormField>
        <UFormField label="Комментарий" help="Виден только в админке">
          <UTextarea v-model="state.comment" :rows="2" class="w-full" />
        </UFormField>
        <p v-if="error" class="text-sm text-error" data-testid="create-order-error">{{ error }}</p>
        <div class="flex justify-end gap-2">
          <UButton color="neutral" variant="ghost" label="Отмена" @click="open = false" />
          <UButton type="submit" label="Создать" :loading="pending" data-testid="create-order-save" />
        </div>
      </form>
    </template>
  </UModal>
</template>

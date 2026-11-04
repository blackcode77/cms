<script setup lang="ts">
import { ClientFieldsInput, updateClient } from '@entities/client'
import type { ClientInput, ClientItem } from '@entities/client'
import { apiErrorMessage, apiFieldErrors } from '@shared/lib'

const props = defineProps<{ client: ClientItem }>()
const emit = defineEmits<{ updated: [] }>()

const open = ref(false)
const state = reactive<ClientInput>({ phone: '', first_name: '', last_name: '', middle_name: '', email: '', comment: '' })
const error = ref('')
const fieldErrors = ref<Record<string, string>>({})
const pending = ref(false)

function show() {
  const { client } = props
  Object.assign(state, {
    phone: client.phone,
    first_name: client.first_name,
    last_name: client.last_name,
    middle_name: client.middle_name,
    email: client.email ?? '',
    comment: client.comment,
  })
  error.value = ''
  fieldErrors.value = {}
  open.value = true
}

async function save() {
  error.value = ''
  fieldErrors.value = {}
  pending.value = true
  try {
    await updateClient(props.client.id, { ...state })
    open.value = false
    emit('updated')
  }
  catch (e) {
    fieldErrors.value = apiFieldErrors(e)
    error.value = apiErrorMessage(e, 'Не удалось сохранить клиента.')
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
    :data-testid="`client-edit-${client.id}`"
    @click="show"
  />
  <UModal v-model:open="open" title="Изменить клиента" :ui="{ content: 'max-w-2xl' }">
    <template #body>
      <form class="space-y-4" data-testid="edit-client-form" @submit.prevent="save">
        <ClientFieldsInput v-model:client="state" :errors="fieldErrors" />
        <UFormField label="Комментарий">
          <UTextarea v-model="state.comment" :rows="3" class="w-full" />
        </UFormField>
        <p v-if="error" class="text-sm text-error" data-testid="edit-client-error">{{ error }}</p>
        <div class="flex justify-end gap-2">
          <UButton color="neutral" variant="ghost" label="Отмена" @click="open = false" />
          <UButton type="submit" label="Сохранить" :loading="pending" data-testid="edit-client-save" />
        </div>
      </form>
    </template>
  </UModal>
</template>

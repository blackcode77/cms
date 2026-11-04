<script setup lang="ts">
import { ClientFieldsInput, createClient } from '@entities/client'
import type { ClientInput } from '@entities/client'
import { apiErrorMessage, apiFieldErrors } from '@shared/lib'

const emit = defineEmits<{ created: [id: string] }>()

const blank = (): ClientInput => ({ phone: '', first_name: '', last_name: '', middle_name: '', email: '', comment: '' })
const open = ref(false)
const state = reactive(blank())
const error = ref('')
const fieldErrors = ref<Record<string, string>>({})
const pending = ref(false)

function show() {
  Object.assign(state, blank())
  error.value = ''
  fieldErrors.value = {}
  open.value = true
}

async function save() {
  error.value = ''
  fieldErrors.value = {}
  pending.value = true
  try {
    const { id } = await createClient({ ...state })
    open.value = false
    emit('created', id)
  }
  catch (e) {
    fieldErrors.value = apiFieldErrors(e)
    error.value = apiErrorMessage(e, 'Не удалось создать клиента.')
  }
  finally {
    pending.value = false
  }
}
</script>

<template>
  <UButton icon="i-lucide-user-plus" label="Добавить клиента" data-testid="client-create" @click="show" />
  <UModal v-model:open="open" title="Новый клиент" :ui="{ content: 'max-w-2xl' }">
    <template #body>
      <form class="space-y-4" data-testid="create-client-form" @submit.prevent="save">
        <ClientFieldsInput v-model:client="state" :errors="fieldErrors" />
        <UFormField label="Комментарий">
          <UTextarea v-model="state.comment" :rows="3" class="w-full" />
        </UFormField>
        <p v-if="error" class="text-sm text-error" data-testid="create-client-error">{{ error }}</p>
        <div class="flex justify-end gap-2">
          <UButton color="neutral" variant="ghost" label="Отмена" @click="open = false" />
          <UButton type="submit" label="Создать" :loading="pending" data-testid="create-client-save" />
        </div>
      </form>
    </template>
  </UModal>
</template>

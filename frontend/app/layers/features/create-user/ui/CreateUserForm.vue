<script setup lang="ts">
import { createUser } from '@entities/user'
import type { CreateUserResult, RoleOption } from '@entities/user'

defineProps<{ roles: RoleOption[] }>()
const emit = defineEmits<{ created: [result: CreateUserResult, email: string] }>()

const state = reactive({ email: '', name: '', role_id: '' })
const error = ref('')
const pending = ref(false)

function messageFor(e: unknown): string {
  const err = e as { statusCode?: number, data?: { error_message?: string } }
  if (err.statusCode === 422) return 'Укажите корректную эл. почту и имя, выберите роль.'
  return err.data?.error_message ?? 'Не удалось создать пользователя. Попробуйте позже.'
}

async function onSubmit() {
  error.value = ''
  pending.value = true
  try {
    const result = await createUser({ ...state })
    emit('created', result, state.email)
    state.email = ''
    state.name = ''
    state.role_id = ''
  }
  catch (e) {
    error.value = messageFor(e)
  }
  finally {
    pending.value = false
  }
}
</script>

<template>
  <UForm :state="state" class="space-y-4" data-testid="create-user-form" @submit="onSubmit">
    <UAlert v-if="error" color="error" variant="subtle" :description="error" data-testid="create-user-error" />
    <UFormField label="Эл. почта" name="email">
      <UInput v-model="state.email" type="email" class="w-full" />
    </UFormField>
    <UFormField label="Имя" name="name">
      <UInput v-model="state.name" class="w-full" />
    </UFormField>
    <UFormField label="Роль" name="role_id">
      <USelect
        v-model="state.role_id"
        :items="roles.map(r => ({ label: r.name, value: r.id }))"
        placeholder="Выберите роль"
        class="w-full"
      />
    </UFormField>
    <UButton type="submit" :loading="pending" label="Создать пользователя" />
  </UForm>
</template>

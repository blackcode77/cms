<script setup lang="ts">
import { useCurrentUser } from '@entities/session'

const emit = defineEmits<{ changed: [] }>()
const { updatePassword } = useCurrentUser()

const state = reactive({ current_password: '', new_password: '' })
const error = ref('')
const pending = ref(false)

function messageFor(e: unknown): string {
  const err = e as { statusCode?: number, data?: { error_message?: string } }
  if (err.statusCode === 422) return 'Введите текущий пароль и новый, не короче 8 символов.'
  return err.data?.error_message ?? 'Не удалось сменить пароль. Попробуйте позже.'
}

async function onSubmit() {
  error.value = ''
  pending.value = true
  try {
    await updatePassword(state.current_password, state.new_password)
    emit('changed')
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
  <UForm :state="state" class="space-y-4" data-testid="change-password-form" @submit="onSubmit">
    <UAlert v-if="error" color="error" variant="subtle" :description="error" data-testid="change-password-error" />
    <UFormField label="Текущий пароль" name="current_password">
      <UInput v-model="state.current_password" type="password" autocomplete="current-password" class="w-full" />
    </UFormField>
    <UFormField label="Новый пароль" name="new_password" hint="Не менее 8 символов">
      <UInput v-model="state.new_password" type="password" autocomplete="new-password" class="w-full" />
    </UFormField>
    <UButton type="submit" block :loading="pending" label="Сменить пароль" />
  </UForm>
</template>

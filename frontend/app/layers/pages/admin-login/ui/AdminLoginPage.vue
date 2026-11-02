<script setup lang="ts">
import { ADMIN_HOME_PATH, useCurrentUser } from '@entities/session'

const { signIn } = useCurrentUser()

const state = reactive({ email: '', password: '' })
const error = ref('')
const pending = ref(false)

function messageFor(e: unknown): string {
  const err = e as { statusCode?: number, data?: { error_message?: string } }
  if (err.statusCode === 422) return 'Enter your email and password.'
  return err.data?.error_message ?? 'Sign-in failed. Try again later.'
}

async function onSubmit() {
  error.value = ''
  pending.value = true
  try {
    await signIn(state.email, state.password)
    await navigateTo(ADMIN_HOME_PATH)
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
  <div class="flex min-h-screen items-center justify-center p-4">
    <UCard class="w-full max-w-sm">
      <template #header>
        <h1 class="text-lg font-semibold">Sign in</h1>
      </template>
      <UForm :state="state" class="space-y-4" @submit="onSubmit">
        <UAlert
          v-if="error"
          color="error"
          variant="subtle"
          :description="error"
          data-testid="login-error"
        />
        <UFormField label="Email" name="email">
          <UInput v-model="state.email" type="email" autocomplete="username" class="w-full" />
        </UFormField>
        <UFormField label="Password" name="password">
          <UInput v-model="state.password" type="password" autocomplete="current-password" class="w-full" />
        </UFormField>
        <UButton type="submit" block :loading="pending" label="Sign in" />
      </UForm>
    </UCard>
  </div>
</template>

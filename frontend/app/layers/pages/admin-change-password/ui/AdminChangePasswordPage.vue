<script setup lang="ts">
import { ADMIN_HOME_PATH, useCurrentUser } from '@entities/session'
import { ChangePasswordForm } from '@features/change-password'

const { mustChangePassword, signOut } = useCurrentUser()

async function onChanged() {
  await navigateTo(ADMIN_HOME_PATH)
}

async function onSignOut() {
  await signOut()
  await navigateTo('/admin/login')
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center p-4">
    <UCard class="w-full max-w-sm">
      <template #header>
        <h1 class="text-lg font-semibold">Change password</h1>
      </template>
      <UAlert
        v-if="mustChangePassword"
        color="warning"
        variant="subtle"
        class="mb-4"
        description="You must choose a new password before you can continue."
        data-testid="forced-change-notice"
      />
      <ChangePasswordForm @changed="onChanged" />
      <template #footer>
        <UButton variant="link" label="Sign out" data-testid="change-password-sign-out" @click="onSignOut" />
      </template>
    </UCard>
  </div>
</template>

<script setup lang="ts">
import { resetUserPassword } from '@entities/user'
import type { ResetPasswordResult, UserListItem } from '@entities/user'

const props = defineProps<{ user: UserListItem }>()
const emit = defineEmits<{ done: [result: ResetPasswordResult], failed: [message: string] }>()

const open = ref(false)
const busy = ref(false)

async function confirm() {
  busy.value = true
  try {
    emit('done', await resetUserPassword(props.user.id))
  }
  catch (e) {
    emit('failed', (e as { data?: { error_message?: string } }).data?.error_message ?? 'Could not reset the password.')
  }
  finally {
    busy.value = false
    open.value = false
  }
}
</script>

<template>
  <UButton
    size="xs"
    variant="outline"
    label="Send password again"
    :data-testid="`reset-password-${user.id}`"
    @click="open = true"
  />
  <UModal v-model:open="open" title="Send a new password">
    <template #body>
      <p>
        Generate a new password for {{ user.email }} and email it to them? Their current password stops working
        immediately and all their sessions are signed out. They must change the new password on next sign-in.
      </p>
      <div class="mt-4 flex justify-end gap-2">
        <UButton color="neutral" variant="ghost" label="Cancel" @click="open = false" />
        <UButton
          color="warning"
          label="Reset password"
          :loading="busy"
          data-testid="reset-password-confirm"
          @click="confirm"
        />
      </div>
    </template>
  </UModal>
</template>

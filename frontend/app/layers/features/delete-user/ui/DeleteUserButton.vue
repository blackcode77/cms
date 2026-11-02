<script setup lang="ts">
import { deleteUser } from '@entities/user'
import type { UserListItem } from '@entities/user'

const props = defineProps<{ user: UserListItem }>()
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
    await deleteUser(props.user.id)
    open.value = false
    emit('deleted')
  }
  catch (e) {
    error.value = (e as { data?: { error_message?: string } }).data?.error_message ?? 'Could not delete the user.'
  }
}
</script>

<template>
  <UButton
    icon="i-lucide-trash-2"
    color="error"
    variant="outline"
    size="xs"
    label="Delete"
    data-testid="user-delete"
    @click="ask"
  />
  <UModal v-model:open="open" title="Delete user">
    <template #body>
      <p>Delete {{ user.email }}? Their sessions will end. This cannot be undone.</p>
      <p v-if="error" class="mt-3 text-sm text-error" data-testid="delete-user-error">{{ error }}</p>
      <div class="mt-4 flex justify-end gap-2">
        <UButton color="neutral" variant="ghost" label="Cancel" @click="open = false" />
        <UButton color="error" label="Delete" data-testid="user-delete-confirm" @click="confirm" />
      </div>
    </template>
  </UModal>
</template>

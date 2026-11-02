<script setup lang="ts">
import { updateUser } from '@entities/user'
import type { RoleOption, UserListItem } from '@entities/user'

const props = defineProps<{ user: UserListItem, roles: RoleOption[] }>()
const emit = defineEmits<{ updated: [] }>()

const open = ref(false)
const state = reactive({ email: '', name: '', role_id: '' })
const error = ref('')
const pending = ref(false)

function show() {
  state.email = props.user.email
  state.name = props.user.name
  state.role_id = props.user.role.id
  error.value = ''
  open.value = true
}

async function save() {
  error.value = ''
  pending.value = true
  try {
    await updateUser(props.user.id, { ...state })
    open.value = false
    emit('updated')
  }
  catch (e) {
    const err = e as { statusCode?: number, data?: { error_message?: string } }
    error.value = err.statusCode === 422
      ? 'Enter a valid email, a name and choose a role.'
      : err.data?.error_message ?? 'Could not save the user.'
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
    label="Edit"
    data-testid="user-edit"
    @click="show"
  />
  <UModal v-model:open="open" title="Edit user">
    <template #body>
      <form class="space-y-4" data-testid="edit-user-form" @submit.prevent="save">
        <UFormField label="Email">
          <UInput v-model="state.email" type="email" class="w-full" data-testid="edit-user-email" />
        </UFormField>
        <UFormField label="Name">
          <UInput v-model="state.name" class="w-full" data-testid="edit-user-name" />
        </UFormField>
        <UFormField label="Role">
          <USelect
            v-model="state.role_id"
            :items="roles.map(r => ({ label: r.name, value: r.id }))"
            class="w-full"
          />
        </UFormField>
        <p v-if="error" class="text-sm text-error" data-testid="edit-user-error">{{ error }}</p>
        <div class="flex justify-end gap-2">
          <UButton color="neutral" variant="ghost" label="Cancel" @click="open = false" />
          <UButton type="submit" label="Save" :loading="pending" data-testid="edit-user-save" />
        </div>
      </form>
    </template>
  </UModal>
</template>

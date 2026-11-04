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
    emit('failed', (e as { data?: { error_message?: string } }).data?.error_message ?? 'Не удалось сбросить пароль.')
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
    label="Отправить пароль заново"
    :data-testid="`reset-password-${user.id}`"
    @click="open = true"
  />
  <UModal v-model:open="open" title="Новый пароль">
    <template #body>
      <p>
        Создать новый пароль для {{ user.email }} и отправить его на почту? Текущий пароль перестанет работать сразу,
        все его сеансы будут завершены. При следующем входе он должен будет сменить пароль.
      </p>
      <div class="mt-4 flex justify-end gap-2">
        <UButton color="neutral" variant="ghost" label="Отмена" @click="open = false" />
        <UButton
          color="warning"
          label="Сбросить пароль"
          :loading="busy"
          data-testid="reset-password-confirm"
          @click="confirm"
        />
      </div>
    </template>
  </UModal>
</template>

<script setup lang="ts">
import { createRole, deleteRole, fetchPermissionCatalog, fetchRoles, updateRole } from '@entities/role'
import type { Role } from '@entities/role'
import { permissionLabel, useCurrentUser } from '@entities/session'
import type { Permission } from '@entities/session'

const { can } = useCurrentUser()
const canManage = computed(() => can('manage_roles'))

const { data: roles, refresh } = await useAsyncData('admin-roles', () => fetchRoles(), { default: () => [] })
const { data: catalog } = await useAsyncData('admin-permission-catalog', () => fetchPermissionCatalog(), {
  default: () => [] as Permission[],
})

const formOpen = ref(false)
const editing = ref<Role | null>(null)
const name = ref('')
const permissions = ref<Permission[]>([])
const formError = ref('')
const saving = ref(false)

const deleting = ref<Role | null>(null)
const deleteOpen = ref(false)
const deleteError = ref('')

function messageOf(error: unknown): string {
  const e = error as { data?: { error_message?: string }, message?: string } | null
  return e?.data?.error_message ?? e?.message ?? 'Что-то пошло не так.'
}

function openCreate() {
  editing.value = null
  name.value = ''
  permissions.value = []
  formError.value = ''
  formOpen.value = true
}

function openEdit(role: Role) {
  editing.value = role
  name.value = role.name
  permissions.value = [...role.permissions]
  formError.value = ''
  formOpen.value = true
}

function toggle(permission: Permission, checked: boolean | 'indeterminate') {
  permissions.value = checked === true
    ? [...permissions.value, permission]
    : permissions.value.filter(p => p !== permission)
}

async function save() {
  saving.value = true
  formError.value = ''
  try {
    const input = { name: name.value, permissions: permissions.value }
    if (editing.value) await updateRole(editing.value.id, input)
    else await createRole(input)
    formOpen.value = false
    await refresh()
  }
  catch (error) {
    formError.value = messageOf(error)
  }
  finally {
    saving.value = false
  }
}

function askDelete(role: Role) {
  deleting.value = role
  deleteError.value = ''
  deleteOpen.value = true
}

async function confirmDelete() {
  if (!deleting.value) return
  deleteError.value = ''
  try {
    await deleteRole(deleting.value.id)
    deleteOpen.value = false
    await refresh()
  }
  catch (error) {
    deleteError.value = messageOf(error)
  }
}
</script>

<template>
  <UDashboardPanel id="roles">
    <template #header>
      <UDashboardNavbar title="Роли">
        <template #right>
          <UButton
            v-if="canManage"
            icon="i-lucide-plus"
            label="Новая роль"
            data-testid="role-create"
            @click="openCreate"
          />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <ul class="divide-y divide-default rounded-md border border-default" data-testid="role-list">
        <li
          v-for="role in roles"
          :key="role.id"
          class="flex flex-wrap items-center justify-between gap-3 p-4"
          data-testid="role-row"
        >
          <div class="min-w-0">
            <div class="flex items-center gap-2">
              <span class="font-medium">{{ role.name }}</span>
              <UBadge v-if="role.read_only" color="neutral" variant="subtle" label="Только чтение" />
              <span class="text-sm text-muted">Пользователей: {{ role.users_count }}</span>
            </div>
            <div class="mt-2 flex flex-wrap gap-1">
              <UBadge
                v-for="permission in role.permissions"
                :key="permission"
                color="primary"
                variant="subtle"
                :label="permissionLabel(permission)"
              />
              <span v-if="!role.permissions.length" class="text-sm text-muted">Нет прав</span>
            </div>
          </div>
          <div v-if="canManage" class="flex gap-2">
            <UButton
              icon="i-lucide-pencil"
              color="neutral"
              variant="outline"
              label="Изменить"
              :disabled="role.read_only"
              data-testid="role-edit"
              @click="openEdit(role)"
            />
            <UButton
              icon="i-lucide-trash-2"
              color="error"
              variant="outline"
              label="Удалить"
              :disabled="role.read_only || role.name === 'visiter'"
              data-testid="role-delete"
              @click="askDelete(role)"
            />
          </div>
        </li>
      </ul>

      <UModal v-model:open="formOpen" :title="editing ? 'Изменить роль' : 'Новая роль'">
        <template #body>
          <form class="space-y-4" @submit.prevent="save">
            <UFormField label="Название">
              <UInput
                v-model="name"
                class="w-full"
                :disabled="editing?.name === 'visiter'"
                data-testid="role-name"
              />
            </UFormField>
            <fieldset class="space-y-2">
              <legend class="text-sm font-medium">Права</legend>
              <UCheckbox
                v-for="permission in catalog"
                :key="permission"
                :label="permissionLabel(permission)"
                :model-value="permissions.includes(permission)"
                :data-testid="`permission-${permission}`"
                @update:model-value="toggle(permission, $event)"
              />
            </fieldset>
            <p v-if="formError" class="text-sm text-error" data-testid="role-form-error">{{ formError }}</p>
            <div class="flex justify-end gap-2">
              <UButton color="neutral" variant="ghost" label="Отмена" @click="formOpen = false" />
              <UButton type="submit" label="Сохранить" :loading="saving" data-testid="role-save" />
            </div>
          </form>
        </template>
      </UModal>

      <UModal v-model:open="deleteOpen" title="Удаление роли">
        <template #body>
          <p>Удалить роль «{{ deleting?.name }}»? Это нельзя отменить.</p>
          <p v-if="deleteError" class="mt-3 text-sm text-error" data-testid="role-delete-error">{{ deleteError }}</p>
          <div class="mt-4 flex justify-end gap-2">
            <UButton color="neutral" variant="ghost" label="Отмена" @click="deleteOpen = false" />
            <UButton color="error" label="Удалить" data-testid="role-delete-confirm" @click="confirmDelete" />
          </div>
        </template>
      </UModal>
    </template>
  </UDashboardPanel>
</template>

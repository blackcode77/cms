<script setup lang="ts">
import { useCurrentUser } from '@entities/session'
import { fetchAssignableRoles, fetchUsers } from '@entities/user'
import type { CreateUserResult, ResetPasswordResult, UserListItem } from '@entities/user'
import { CreateUserForm } from '@features/create-user'
import { DeleteUserButton } from '@features/delete-user'
import { EditUserButton } from '@features/edit-user'
import { ResetPasswordButton } from '@features/reset-password'

const { can, user: currentUser } = useCurrentUser()

const { data: users, refresh } = await useAsyncData('admin-users', fetchUsers, { default: () => [] })
const { data: roles } = await useAsyncData('admin-users-roles', fetchAssignableRoles, { default: () => [] })

const notice = ref<{ color: 'success' | 'warning', text: string } | null>(null)

async function onCreated(result: CreateUserResult, email: string) {
  notice.value = result.mail_sent
    ? { color: 'success', text: `User ${email} created. Login and password were emailed.` }
    : {
        color: 'warning',
        text: `User ${email} created, but the email could not be sent. Their password must be reset to deliver it.`,
      }
  await refresh()
}

function onPasswordReset(user: UserListItem, result: ResetPasswordResult) {
  notice.value = result.mail_sent
    ? { color: 'success', text: `A new password was emailed to ${user.email}.` }
    : {
        color: 'warning',
        text: `A new password was set for ${user.email}, but the email could not be sent. Send it again.`,
      }
}

const columns = [
  { accessorKey: 'name', header: 'Name' },
  { accessorKey: 'email', header: 'Email' },
  { accessorKey: 'role.name', header: 'Role' },
  { id: 'actions', header: '' },
  { id: 'password', header: 'Password' },
]
</script>

<template>
  <UDashboardPanel id="users">
    <template #header>
      <UDashboardNavbar title="Users" />
    </template>
    <template #body>
      <UAlert
        v-if="notice"
        :color="notice.color"
        variant="subtle"
        :description="notice.text"
        data-testid="create-user-notice"
      />
      <UTable :data="users" :columns="columns" data-testid="users-table">
        <template #actions-cell="{ row }">
          <div v-if="can('manage_users')" class="flex gap-2">
            <EditUserButton :user="row.original" :roles="roles" @updated="refresh" />
            <DeleteUserButton :user="row.original" @deleted="refresh" />
          </div>
        </template>
        <template #password-cell="{ row }">
          <!-- own password: use change-password instead of a reset -->
          <ResetPasswordButton
            v-if="can('manage_users') && row.original.id !== currentUser?.id"
            :user="row.original"
            @done="onPasswordReset(row.original, $event)"
            @failed="notice = { color: 'warning', text: $event }"
          />
        </template>
      </UTable>
      <UCard v-if="can('manage_users')" data-testid="create-user-card" class="w-100">
        <template #header>
          <h2 class="font-semibold">Добавить пользователя</h2>
        </template>
        <CreateUserForm :roles="roles" @created="onCreated" />
      </UCard>
    </template>
  </UDashboardPanel>
</template>

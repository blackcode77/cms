<script setup lang="ts">
import { useCurrentUser } from '@entities/session'
import { fetchAssignableRoles, fetchUsers } from '@entities/user'
import type { CreateUserResult } from '@entities/user'
import { CreateUserForm } from '@features/create-user'
import { DeleteUserButton } from '@features/delete-user'
import { EditUserButton } from '@features/edit-user'

const { can } = useCurrentUser()

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

const columns = [
  { accessorKey: 'name', header: 'Name' },
  { accessorKey: 'email', header: 'Email' },
  { accessorKey: 'role.name', header: 'Role' },
  { id: 'actions', header: '' },
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
      </UTable>
      <UCard v-if="can('manage_users')" data-testid="create-user-card">
        <template #header>
          <h2 class="font-semibold">New user</h2>
        </template>
        <CreateUserForm :roles="roles" @created="onCreated" />
      </UCard>
    </template>
  </UDashboardPanel>
</template>

<script setup lang="ts">
import { ADMIN_LOGIN_PATH, useCurrentUser } from '@entities/session'
import { adminMenuItems, visibleMenuItems } from '../model/menu-items'

const { user, can, signOut } = useCurrentUser()

const items = computed(() =>
  visibleMenuItems(adminMenuItems, can).map(({ permission: _permission, ...item }) => item),
)

async function onSignOut() {
  await signOut()
  await navigateTo(ADMIN_LOGIN_PATH)
}
</script>

<template>
  <UDashboardSidebar>
    <template #header>
      <span class="font-semibold">CMS admin</span>
    </template>
    <UNavigationMenu orientation="vertical" :items="items" />
    <template #footer>
      <div class="flex w-full items-center justify-between gap-2">
        <span class="truncate text-sm" data-testid="admin-user">{{ user?.name }}</span>
        <UButton
          icon="i-lucide-log-out"
          color="neutral"
          variant="ghost"
          label="Sign out"
          data-testid="sign-out"
          @click="onSignOut"
        />
      </div>
    </template>
  </UDashboardSidebar>
</template>

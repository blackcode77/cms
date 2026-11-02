<script setup lang="ts">
import { AdminUsersPage } from '@pages/admin-users'

definePageMeta({ layout: 'admin', permission: 'view_users' })
</script>

<template>
  <AdminUsersPage />
</template>

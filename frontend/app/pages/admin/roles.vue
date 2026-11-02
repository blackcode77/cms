<script setup lang="ts">
import { AdminRolesPage } from '@pages/admin-roles'

definePageMeta({ layout: 'admin', permission: 'view_roles' })
</script>

<template>
  <AdminRolesPage />
</template>

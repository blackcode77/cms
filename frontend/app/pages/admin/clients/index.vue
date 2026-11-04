<script setup lang="ts">
import { AdminClientsPage } from '@pages/admin-clients'

definePageMeta({ layout: 'admin', permission: 'view_clients' })
</script>

<template>
  <AdminClientsPage />
</template>

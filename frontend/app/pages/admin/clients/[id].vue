<script setup lang="ts">
import { AdminClientPage } from '@pages/admin-client'

definePageMeta({ layout: 'admin', permission: 'view_clients' })

const route = useRoute()
</script>

<template>
  <AdminClientPage :id="String(route.params.id)" :key="String(route.params.id)" />
</template>

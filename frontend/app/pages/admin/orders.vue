<script setup lang="ts">
import { AdminOrdersPage } from '@pages/admin-orders'

definePageMeta({ layout: 'admin', permission: 'view_orders' })
</script>

<template>
  <AdminOrdersPage />
</template>

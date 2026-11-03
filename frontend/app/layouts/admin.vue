<script setup lang="ts">
import { AdminSidebar } from '@widgets/admin-sidebar'

useHead({
  link: [{ key: 'favicon', rel: 'icon', type: 'image/svg+xml', href: '/admin-favicon.svg' }],
})
</script>

<template>
  <UApp>
    <UDashboardGroup>
      <AdminSidebar />
      <slot />
    </UDashboardGroup>
  </UApp>
</template>

<style>
@import "~/assets/css/main.css";
</style>

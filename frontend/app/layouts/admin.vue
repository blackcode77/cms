<script setup lang="ts">
import { AdminSidebar } from '@widgets/admin-sidebar'
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

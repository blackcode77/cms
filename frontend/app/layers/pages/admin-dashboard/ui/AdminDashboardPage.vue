<script setup lang="ts">
import { HealthStatusCard } from '@widgets/health-status'
</script>

<template>
  <UDashboardPanel id="dashboard">
    <template #header>
      <UDashboardNavbar title="Главная" />
    </template>
    <template #body>
      <HealthStatusCard />
    </template>
  </UDashboardPanel>
</template>

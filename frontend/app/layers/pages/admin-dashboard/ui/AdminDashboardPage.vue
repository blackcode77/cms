<script setup lang="ts">
import { HealthStatusCard } from '@widgets/health-status'
</script>

<template>
  <UDashboardPanel id="dashboard">
    <template #header>
      <UDashboardNavbar title="Dashboard" />
    </template>
    <template #body>
      <HealthStatusCard />
    </template>
  </UDashboardPanel>
</template>

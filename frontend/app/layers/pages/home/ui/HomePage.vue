<script setup lang="ts">
import { HealthStatusCard } from '@widgets/health-status'

useSeoMeta({ title: 'CMS' })
</script>

<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold">CMS</h1>
    <HealthStatusCard />
  </div>
</template>

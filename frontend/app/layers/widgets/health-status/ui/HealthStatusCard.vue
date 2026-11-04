<script setup lang="ts">
import { fetchHealth } from '@entities/health'

// Runs on the server during SSR; the result is serialized into the page payload.
const { data, error } = await useAsyncData('health', () => fetchHealth())
</script>

<template>
  <UCard>
    <template #header>
      <h2 class="font-semibold">Состояние сервера</h2>
    </template>
    <p v-if="error" data-testid="health-error" class="text-error">
      API недоступен: {{ error.message }}
    </p>
    <dl v-else-if="data" data-testid="health-ok" class="grid grid-cols-2 gap-2">
      <dt>Приложение</dt>
      <dd>{{ data.app }}</dd>
      <dt>База данных</dt>
      <dd>{{ data.database }}</dd>
    </dl>
  </UCard>
</template>

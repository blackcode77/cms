<script setup lang="ts">
import type { PersonName } from '@shared/lib'

/** Phone, name and email of a Client in admin forms (`v-model:client`, edited in place). */
const client = defineModel<PersonName & { phone: string, email: string }>('client', { required: true })
defineProps<{ errors?: Record<string, string> }>()
</script>

<template>
  <div class="space-y-4">
    <UFormField label="Телефон" required :error="errors?.phone">
      <UInput
        v-model="client.phone"
        type="tel"
        class="w-full"
        placeholder="+7 999 123-45-67"
        data-testid="client-phone"
      />
    </UFormField>
    <div class="grid grid-cols-3 gap-4">
      <UFormField label="Фамилия" :error="errors?.last_name">
        <UInput v-model="client.last_name" class="w-full" />
      </UFormField>
      <UFormField label="Имя" :error="errors?.first_name">
        <UInput v-model="client.first_name" class="w-full" />
      </UFormField>
      <UFormField label="Отчество" :error="errors?.middle_name">
        <UInput v-model="client.middle_name" class="w-full" />
      </UFormField>
    </div>
    <UFormField label="Эл. почта" :error="errors?.email">
      <UInput v-model="client.email" type="email" class="w-full" />
    </UFormField>
  </div>
</template>

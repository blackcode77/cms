<script setup lang="ts">
import { cargoTypes } from '../model/cargo-types'
import type { CargoFields } from '../model/types'

/** Cargo fields of an Order in admin forms (`v-model:cargo`, edited in place). */
const cargo = defineModel<CargoFields>('cargo', { required: true })
</script>

<template>
  <div class="grid grid-cols-2 gap-4">
    <UFormField label="Откуда">
      <UInput v-model="cargo.from" class="w-full" placeholder="Например, Шанхай" />
    </UFormField>
    <UFormField label="Куда">
      <UInput v-model="cargo.to" class="w-full" placeholder="Например, Москва" />
    </UFormField>
    <UFormField label="Тип груза">
      <UInput v-model="cargo.cargo_type" class="w-full" list="cargo-types" />
      <datalist id="cargo-types">
        <option v-for="type in cargoTypes" :key="type" :value="type" />
      </datalist>
    </UFormField>
    <UFormField label="Масса, т">
      <UInput v-model="cargo.weight" class="w-full" inputmode="decimal" />
    </UFormField>
  </div>
</template>

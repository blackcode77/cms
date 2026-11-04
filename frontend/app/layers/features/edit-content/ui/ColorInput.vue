<script setup lang="ts">
import { colorPresets } from '@entities/route-map'

/** Colour (`#rrggbb`): presets of the site palette plus a free picker. */
const value = defineModel<string>({ required: true })
</script>

<template>
  <div class="flex flex-wrap items-center gap-2" data-testid="color-input">
    <button
      v-for="preset in colorPresets"
      :key="preset"
      type="button"
      class="size-7 rounded-full border-2"
      :class="value.toLowerCase() === preset ? 'border-highlighted ring-2 ring-primary' : 'border-default'"
      :style="{ background: preset }"
      :aria-label="`Цвет ${preset}`"
      :aria-pressed="value.toLowerCase() === preset"
      data-testid="color-preset"
      @click="value = preset"
    />
    <label class="flex items-center gap-1 text-xs text-muted">
      <input
        type="color"
        :value="/^#[0-9a-f]{6}$/i.test(value) ? value : '#3a7b34'"
        class="size-7 cursor-pointer rounded border-0 bg-transparent p-0"
        data-testid="color-picker"
        @input="value = ($event.target as HTMLInputElement).value"
      >
      Другой
    </label>
  </div>
</template>

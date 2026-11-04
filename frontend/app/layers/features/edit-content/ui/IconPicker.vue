<script setup lang="ts">
import { searchIcons } from '../model/icon-names'

/** Picks an icon of the site collection (Lucide) by searching its names. `v-model` is the bare name. */
const value = defineModel<string>({ required: true })

const open = ref(false)
const query = ref('')
const results = ref<string[]>([])
const loading = ref(false)

watch([open, query], async ([isOpen, q]) => {
  if (!isOpen) return
  loading.value = true
  try {
    results.value = await searchIcons(q)
  }
  finally {
    loading.value = false
  }
}, { immediate: true })

function pick(name: string) {
  value.value = name
  open.value = false
}
</script>

<template>
  <div class="flex flex-col gap-2" data-testid="icon-input">
    <div class="flex items-center gap-2">
      <span class="grid size-10 place-items-center rounded-md border border-default">
        <UIcon v-if="value" :name="`i-lucide-${value}`" class="size-6" />
      </span>
      <span class="flex-1 truncate text-xs text-muted">{{ value || 'не выбрана' }}</span>
      <UButton
        :label="open ? 'Скрыть' : 'Выбрать'"
        color="neutral"
        variant="subtle"
        size="sm"
        data-testid="icon-toggle"
        @click="open = !open"
      />
    </div>
    <template v-if="open">
      <UInput v-model="query" placeholder="Поиск: truck, ship, map…" icon="i-lucide-search" data-testid="icon-search" />
      <p v-if="!loading && results.length === 0" class="text-xs text-muted">Ничего не найдено.</p>
      <div class="grid max-h-64 grid-cols-6 gap-1 overflow-y-auto">
        <button
          v-for="name in results"
          :key="name"
          type="button"
          class="grid aspect-square place-items-center rounded-md hover:bg-elevated"
          :class="{ 'ring-2 ring-primary': name === value }"
          :title="name"
          :data-testid="`icon-option-${name}`"
          @click="pick(name)"
        >
          <UIcon :name="`i-lucide-${name}`" class="size-5" />
        </button>
      </div>
    </template>
  </div>
</template>

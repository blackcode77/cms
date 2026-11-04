<script setup lang="ts">
import { labelSides, LABEL_MAX_LENGTH, MAX_STOPS, parseStops, RouteMapCanvas, serializeStops } from '@entities/route-map'
import type { Route, Stop } from '@entities/route-map'

/**
 * Editor of a Route's Stops on the Route map picture: a click on empty space appends a Stop, dragging moves one;
 * the list below edits labels and label sides, inserts and deletes Stops. Other Routes are drawn faint.
 */
const props = defineProps<{
  background: string
  color: string
  /** The other Routes of the list, for reference. */
  others: Route[]
}>()

const value = defineModel<string>({ required: true })

const stops = ref<Stop[]>(parseStops(value.value) ?? [])
const selected = ref<number | null>(null)
const labelInputs = ref<Array<{ $el: HTMLElement } | null>>([])

watch(value, (next) => {
  if (next !== serializeStops(stops.value)) stops.value = parseStops(next) ?? []
})

function commit(next: Stop[]) {
  stops.value = next
  value.value = next.length ? serializeStops(next) : ''
}

const canAdd = computed(() => stops.value.length < MAX_STOPS)

const routes = computed<Route[]>(() => [
  ...props.others,
  { id: 'current', name: '', color: props.color, stops: stops.value },
])
const faded = computed(() => props.others.map(route => route.id))

const overlay = ref<HTMLElement | null>(null)

function percentAt(event: PointerEvent | MouseEvent): { x: number, y: number } {
  const box = overlay.value!.getBoundingClientRect()
  const clamp = (v: number) => Math.round(Math.min(100, Math.max(0, v)) * 10) / 10
  return { x: clamp((event.clientX - box.left) / box.width * 100), y: clamp((event.clientY - box.top) / box.height * 100) }
}

async function focusLabel(index: number) {
  selected.value = index
  await nextTick()
  labelInputs.value[index]?.$el.querySelector('textarea')?.focus()
}

function onMapClick(event: MouseEvent) {
  if (!canAdd.value) return
  commit([...stops.value, { ...percentAt(event), label: '', side: 'right' }])
  void focusLabel(stops.value.length - 1)
}

let dragging: number | null = null

function onHandleDown(index: number, event: PointerEvent) {
  dragging = index
  selected.value = index;
  (event.currentTarget as HTMLElement).setPointerCapture?.(event.pointerId)
}

function onHandleMove(event: PointerEvent) {
  if (dragging === null) return
  const index = dragging
  commit(stops.value.map((stop, i) => i === index ? { ...stop, ...percentAt(event) } : stop))
}

function onHandleUp() {
  dragging = null
}

function update(index: number, patch: Partial<Stop>) {
  commit(stops.value.map((stop, i) => i === index ? { ...stop, ...patch } : stop))
}

function remove(index: number) {
  commit(stops.value.filter((_, i) => i !== index))
  selected.value = null
}

/** Inserts a Stop halfway to the next one. */
function insertAfter(index: number) {
  const a = stops.value[index]!
  const b = stops.value[index + 1]!
  const next = [...stops.value]
  next.splice(index + 1, 0, { x: (a.x + b.x) / 2, y: (a.y + b.y) / 2, label: '', side: 'right' })
  commit(next)
  void focusLabel(index + 1)
}
</script>

<template>
  <div class="flex flex-col gap-3" data-testid="stops-editor">
    <p class="text-xs text-muted">
      Нажмите на карту, чтобы добавить точку в конец маршрута; перетащите точку, чтобы передвинуть её.
    </p>
    <div class="overflow-hidden rounded-md border border-default">
      <RouteMapCanvas :background="background" :routes="routes" :faded="faded">
        <div
          ref="overlay"
          class="absolute inset-0"
          :class="canAdd ? 'cursor-crosshair' : 'cursor-not-allowed'"
          data-testid="stops-map"
          @click.self="onMapClick"
        >
          <button
            v-for="(stop, i) in stops"
            :key="i"
            type="button"
            class="absolute size-6 -translate-x-1/2 -translate-y-1/2 cursor-move rounded-full border-2"
            :class="selected === i ? 'border-primary bg-primary/30' : 'border-transparent'"
            :style="{ left: `${stop.x}%`, top: `${stop.y}%`, touchAction: 'none' }"
            :aria-label="`Точка ${i + 1}: ${stop.label || 'без подписи'}`"
            data-testid="stop-handle"
            @click.stop="focusLabel(i)"
            @pointerdown="onHandleDown(i, $event)"
            @pointermove="onHandleMove"
            @pointerup="onHandleUp"
            @pointercancel="onHandleUp"
          />
        </div>
      </RouteMapCanvas>
    </div>

    <p v-if="stops.length === 0" class="text-sm text-muted">Точек пока нет.</p>
    <ol class="flex flex-col gap-2">
      <li
        v-for="(stop, i) in stops"
        :key="i"
        class="flex items-start gap-2 rounded-md p-1"
        :class="selected === i ? 'bg-elevated' : ''"
        data-testid="stop-row"
      >
        <span class="w-5 pt-1.5 text-right text-xs text-muted">{{ i + 1 }}</span>
        <UTextarea
          :ref="(el: unknown) => { labelInputs[i] = el as { $el: HTMLElement } | null }"
          :model-value="stop.label"
          :rows="1"
          :maxlength="LABEL_MAX_LENGTH"
          autoresize
          placeholder="Подпись (вторая строка — через Enter)"
          class="flex-1"
          data-testid="stop-label"
          @focus="selected = i"
          @update:model-value="(v: string) => update(i, { label: v })"
        />
        <USelect
          :model-value="stop.side"
          :items="labelSides.map(s => ({ label: s.label, value: s.value }))"
          class="w-28"
          aria-label="Где подпись"
          data-testid="stop-side"
          @update:model-value="(v: Stop['side']) => update(i, { side: v })"
        />
        <UButton
          icon="i-lucide-between-horizontal-start"
          color="neutral"
          variant="ghost"
          size="sm"
          aria-label="Вставить точку после этой"
          :disabled="i === stops.length - 1 || !canAdd"
          data-testid="stop-insert"
          @click="insertAfter(i)"
        />
        <UButton
          icon="i-lucide-trash-2"
          color="error"
          variant="ghost"
          size="sm"
          aria-label="Удалить точку"
          data-testid="stop-delete"
          @click="remove(i)"
        />
      </li>
    </ol>
  </div>
</template>

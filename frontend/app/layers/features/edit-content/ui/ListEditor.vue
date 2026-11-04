<script setup lang="ts">
import { cleanItems, fieldTypes, moveItem, newItem, patchElement } from '@entities/page-content'
import type { ListItem, ResolvedList, SavedElement } from '@entities/page-content'
import { parseStops, stopsProblem, toRoutes } from '@entities/route-map'
import FieldInput from './FieldInput.vue'
import StopsEditor from './StopsEditor.vue'

/**
 * Editor of a Content list: one card per Item. Adding, deleting and moving Items save at once;
 * field edits are saved with the "Save" button. Every save sends the whole list (ADR 0005).
 */
const props = defineProps<{
  page: string
  path: string
  element: ResolvedList
}>()

const emit = defineEmits<{
  saved: [patch: SavedElement]
}>()

const copy = (items: ListItem[]): ListItem[] => items.map(item => ({ ...item }))

const items = ref<ListItem[]>(copy(props.element.value))
const saving = ref(false)
const uploading = ref(false)
const error = ref('')

watch(() => props.element.value, (value) => {
  items.value = copy(value)
  error.value = ''
})

const serialize = (list: ListItem[]) => JSON.stringify(cleanItems(list, props.element.fields))
const dirty = computed(() => serialize(items.value) !== serialize(props.element.value))
const isDefault = computed(() => serialize(items.value) === serialize(props.element.default))
const canAdd = computed(() => items.value.length < props.element.max)
const canDelete = computed(() => items.value.length > props.element.min)
const fields = computed(() => Object.entries(props.element.fields).filter(([, def]) => !def.hidden))

/** `stops` fields are edited on a map; only one Item's map is open at a time. */
const stopsFields = computed(() => fields.value.filter(([, def]) => def.type === 'stops').map(([name]) => name))
const openMap = ref<string | null>(items.value[0]?.id ?? null)

/** Routes of the other Items, drawn faint under the edited one. */
function otherRoutes(item: ListItem) {
  return toRoutes(items.value.filter(other => other.id !== item.id))
}

function stopsSummary(value: string | undefined): string {
  const stops = parseStops(value) ?? []
  return stops.length ? stops.map(stop => stop.label.split('\n')[0] || '…').join(' → ') : 'Точек нет'
}

/** Why the list cannot be saved yet (a Route's Stops are incomplete), in Russian. */
const problem = computed(() => {
  for (const [index, item] of items.value.entries()) {
    for (const name of stopsFields.value) {
      const issue = stopsProblem(parseStops(item[name]) ?? [])
      if (issue) return `Пункт ${index + 1}: ${issue}`
    }
  }
  return null
})

async function save(next: ListItem[] = items.value) {
  error.value = ''
  saving.value = true
  const value = cleanItems(next, props.element.fields)
  try {
    await patchElement(props.page, props.path, { type: 'list', value, fields: fieldTypes(props.element.fields) })
    items.value = copy(value)
    emit('saved', { type: 'list', value })
  }
  catch (e) {
    const data = (e as { data?: { error_message?: string } } | null)?.data
    error.value = data?.error_message ?? 'Не удалось сохранить. Попробуйте ещё раз.'
  }
  finally {
    saving.value = false
  }
}

function add() {
  if (!canAdd.value) return
  const item = newItem(props.element.fields)
  openMap.value = item.id
  void save([...items.value, item])
}

function remove(index: number) {
  if (canDelete.value) void save(items.value.filter((_, i) => i !== index))
}

function move(index: number, delta: number) {
  void save(moveItem(items.value, index, delta))
}
</script>

<template>
  <div class="flex flex-col gap-3" data-testid="list-editor">
    <p class="text-xs text-muted">
      Пунктов: {{ items.length }}<template v-if="element.max < 100"> из {{ element.max }}</template>
    </p>

    <div
      v-for="(item, index) in items"
      :key="item.id"
      class="flex flex-col gap-3 rounded-md border border-default p-3"
      data-testid="list-item"
    >
      <div class="flex items-center gap-1">
        <span class="flex-1 text-sm font-semibold">Пункт {{ index + 1 }}</span>
        <UButton
          icon="i-lucide-arrow-up"
          color="neutral"
          variant="ghost"
          size="sm"
          aria-label="Выше"
          :disabled="saving || index === 0"
          data-testid="item-up"
          @click="move(index, -1)"
        />
        <UButton
          icon="i-lucide-arrow-down"
          color="neutral"
          variant="ghost"
          size="sm"
          aria-label="Ниже"
          :disabled="saving || index === items.length - 1"
          data-testid="item-down"
          @click="move(index, 1)"
        />
        <UButton
          icon="i-lucide-trash-2"
          color="error"
          variant="ghost"
          size="sm"
          aria-label="Удалить"
          :disabled="saving || !canDelete"
          data-testid="item-delete"
          @click="remove(index)"
        />
      </div>
      <UFormField v-for="[name, def] in fields" :key="name" :label="def.label ?? name">
        <template v-if="def.type === 'stops'">
          <StopsEditor
            v-if="openMap === item.id"
            :model-value="item[name] ?? ''"
            :background="def.backgroundUrl ?? ''"
            :color="item.color || '#3a7b34'"
            :others="otherRoutes(item)"
            @update:model-value="(v: string) => { item[name] = v }"
          />
          <div v-else class="flex items-center gap-2">
            <span class="flex-1 truncate text-sm" data-testid="stops-summary">{{ stopsSummary(item[name]) }}</span>
            <UButton
              label="Изменить на карте"
              icon="i-lucide-map"
              color="neutral"
              variant="subtle"
              size="sm"
              data-testid="stops-open"
              @click="openMap = item.id"
            />
          </div>
        </template>
        <FieldInput
          v-else
          :model-value="item[name] ?? ''"
          :type="def.type"
          :data-field="name"
          @update:model-value="(v: string) => { item[name] = v }"
          @update:uploading="(v: boolean) => { uploading = v }"
          @error="(m: string) => { error = m }"
          @uploaded="(size: string) => { if (def.sizeField) item[def.sizeField] = size }"
        />
      </UFormField>
    </div>

    <UButton
      icon="i-lucide-plus"
      label="Добавить пункт"
      color="neutral"
      variant="subtle"
      :disabled="saving || !canAdd"
      data-testid="item-add"
      @click="add"
    />

    <UAlert v-if="error" color="error" variant="subtle" :description="error" data-testid="editor-error" />

    <p v-if="problem" class="text-sm text-warning" data-testid="list-problem">{{ problem }}</p>

    <div class="flex gap-2">
      <UButton label="Сохранить" :loading="saving || uploading" :disabled="!dirty || !!problem" data-testid="save-button" @click="save()" />
      <UButton
        v-if="!isDefault"
        label="Вернуть исходное"
        color="neutral"
        variant="subtle"
        data-testid="reset-button"
        @click="items = copy(element.default)"
      />
    </div>
  </div>
</template>

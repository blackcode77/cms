<script setup lang="ts">
import { cleanItems, fieldTypes, moveItem, newItem, patchElement } from '@entities/page-content'
import type { ListItem, ResolvedList, SavedElement } from '@entities/page-content'
import FieldInput from './FieldInput.vue'

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
  if (canAdd.value) void save([...items.value, newItem(props.element.fields)])
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
        <FieldInput
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

    <div class="flex gap-2">
      <UButton label="Сохранить" :loading="saving || uploading" :disabled="!dirty" data-testid="save-button" @click="save()" />
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

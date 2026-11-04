<script setup lang="ts">
import { patchElement } from '@entities/page-content'
import type { ResolvedElement, ResolvedValue, SavedElement } from '@entities/page-content'
import FieldInput from './FieldInput.vue'
import ListEditor from './ListEditor.vue'

const props = defineProps<{
  page: string
  path: string
  element: ResolvedElement
}>()

const emit = defineEmits<{
  saved: [patch: SavedElement]
}>()

/** The single-value element, or `null` for a list (edited by `ListEditor`). */
const single = computed<ResolvedValue | null>(() => props.element.type === 'list' ? null : props.element)

const value = ref(single.value?.value ?? '')
const enabled = ref(props.element.enabled)
const saving = ref(false)
const uploading = ref(false)
const error = ref('')

// A different element (or a fresh value after saving) resets the form.
watch(() => [props.page, props.path, single.value?.value, props.element.enabled] as const, () => {
  value.value = single.value?.value ?? ''
  enabled.value = props.element.enabled
  error.value = ''
})

const dirty = computed(() => single.value !== null && value.value !== single.value.value)

function messageOf(e: unknown): string {
  const data = (e as { data?: { error_message?: string } } | null)?.data
  return data?.error_message ?? 'Не удалось сохранить. Попробуйте ещё раз.'
}

async function run(action: () => Promise<void>) {
  error.value = ''
  saving.value = true
  try {
    await action()
  }
  catch (e) {
    error.value = messageOf(e)
  }
  finally {
    saving.value = false
  }
}

function save() {
  const element = single.value
  if (!element) return
  return run(async () => {
    await patchElement(props.page, props.path, { type: element.type, value: value.value })
    emit('saved', { type: element.type, value: value.value })
  })
}

function toggleEnabled(next: boolean) {
  const previous = !next
  return run(async () => {
    try {
      await patchElement(props.page, props.path, { enabled: next })
    }
    catch (e) {
      enabled.value = previous
      throw e
    }
    emit('saved', { enabled: next })
  })
}
</script>

<template>
  <div class="flex flex-col gap-4" data-testid="element-editor">
    <div>
      <p class="text-sm font-semibold" data-testid="element-label">{{ element.label }}</p>
      <p class="text-xs text-muted">{{ page }} · {{ path }}</p>
    </div>

    <USwitch
      :model-value="enabled"
      label="Показывать на сайте"
      :disabled="saving"
      data-testid="enabled-switch"
      @update:model-value="(next: boolean) => { enabled = next; toggleEnabled(next) }"
    />

    <ListEditor
      v-if="element.type === 'list'"
      :page="page"
      :path="path"
      :element="element"
      @saved="(patch: SavedElement) => emit('saved', patch)"
    />

    <template v-else-if="single">
      <FieldInput v-model="value" v-model:uploading="uploading" :type="single.type" @error="(m: string) => { error = m }" />

      <UAlert v-if="error" color="error" variant="subtle" :description="error" data-testid="editor-error" />

      <div class="flex gap-2">
        <UButton label="Сохранить" :loading="saving || uploading" :disabled="!dirty" data-testid="save-button" @click="save" />
        <UButton
          v-if="value !== single.default"
          label="Вернуть исходное"
          color="neutral"
          variant="subtle"
          data-testid="reset-button"
          @click="value = single.default"
        />
      </div>
    </template>
    <UAlert v-if="error && element.type === 'list'" color="error" variant="subtle" :description="error" data-testid="editor-error" />
  </div>
</template>

<script setup lang="ts">
import { patchElement, uploadImage } from '@entities/page-content'
import type { OpenMessage, SavedElement } from '@entities/page-content'

const props = defineProps<{
  page: string
  path: string
  element: OpenMessage['element']
}>()

const emit = defineEmits<{
  saved: [patch: SavedElement]
}>()

const value = ref(props.element.value)
const enabled = ref(props.element.enabled)
const saving = ref(false)
const uploading = ref(false)
const error = ref('')

// A different element (or a fresh value after saving) resets the form.
watch(() => [props.page, props.path, props.element.value, props.element.enabled] as const, () => {
  value.value = props.element.value
  enabled.value = props.element.enabled
  error.value = ''
})

const dirty = computed(() => value.value !== props.element.value)

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
  return run(async () => {
    await patchElement(props.page, props.path, { type: props.element.type, value: value.value })
    emit('saved', { type: props.element.type, value: value.value })
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

async function onFile(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file) return
  error.value = ''
  uploading.value = true
  try {
    value.value = await uploadImage(file)
  }
  catch (e) {
    error.value = messageOf(e)
  }
  finally {
    uploading.value = false
  }
}

const toolbarItems = [
  [{ kind: 'mark', mark: 'bold', icon: 'i-lucide-bold' }, { kind: 'mark', mark: 'italic', icon: 'i-lucide-italic' }, { kind: 'link', icon: 'i-lucide-link' }],
  [{ kind: 'bulletList', icon: 'i-lucide-list' }, { kind: 'orderedList', icon: 'i-lucide-list-ordered' }],
] as const
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

    <UInput v-if="element.type === 'string'" v-model="value" data-testid="string-input" />
    <UTextarea v-else-if="element.type === 'text'" v-model="value" :rows="6" autoresize data-testid="text-input" />
    <UEditor
      v-else-if="element.type === 'html'"
      v-slot="{ editor }"
      v-model="value"
      content-type="html"
      class="min-h-40 rounded-md border border-default p-2"
      data-testid="html-input"
    >
      <UEditorToolbar :editor="editor" :items="toolbarItems as never" />
    </UEditor>
    <div v-else class="flex flex-col gap-2" data-testid="image-input">
      <img v-if="value" :src="value" alt="" class="max-h-40 rounded-md border border-default object-contain">
      <p class="break-all text-xs text-muted">{{ value }}</p>
      <input type="file" accept="image/jpeg,image/png,image/webp" :disabled="uploading" data-testid="image-file" @change="onFile">
    </div>

    <UAlert v-if="error" color="error" variant="subtle" :description="error" data-testid="editor-error" />

    <div class="flex gap-2">
      <UButton label="Сохранить" :loading="saving || uploading" :disabled="!dirty" data-testid="save-button" @click="save" />
      <UButton
        v-if="value !== element.default"
        label="Вернуть исходное"
        color="neutral"
        variant="subtle"
        data-testid="reset-button"
        @click="value = element.default"
      />
    </div>
  </div>
</template>

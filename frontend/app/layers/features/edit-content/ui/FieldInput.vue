<script setup lang="ts">
import { documentFileName, formatFileSize, uploadDocument, uploadImage } from '@entities/page-content'
import type { FieldType } from '@entities/page-content'
import { linkTargets } from '@shared/config'
import IconPicker from './IconPicker.vue'
import ColorInput from './ColorInput.vue'

/** Input for one value of the given type: a standalone element's value or one field of an Item. */
const props = defineProps<{ type: FieldType }>()
const value = defineModel<string>({ required: true })
const emit = defineEmits<{
  error: [message: string]
  /** A Document was uploaded; its size, formatted («210 КБ»). */
  uploaded: [size: string]
}>()

const uploading = defineModel<boolean>('uploading', { default: false })

async function onFile(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file) return
  uploading.value = true
  try {
    if (props.type === 'file') {
      const uploaded = await uploadDocument(file)
      value.value = uploaded.url
      emit('uploaded', formatFileSize(uploaded.size))
    }
    else {
      value.value = await uploadImage(file)
    }
  }
  catch (e) {
    const data = (e as { data?: { error_message?: string } } | null)?.data
    emit('error', data?.error_message ?? 'Не удалось загрузить файл.')
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
  <UInput v-if="props.type === 'string'" v-model="value" class="w-full" data-testid="string-input" />
  <UTextarea v-else-if="props.type === 'text'" v-model="value" class="w-full" :rows="4" autoresize data-testid="text-input" />
  <UEditor
    v-else-if="props.type === 'html'"
    v-slot="{ editor }"
    v-model="value"
    content-type="html"
    class="min-h-40 rounded-md border border-default p-2"
    data-testid="html-input"
  >
    <UEditorToolbar :editor="editor" :items="toolbarItems as never" />
  </UEditor>
  <IconPicker v-else-if="props.type === 'icon'" v-model="value" />
  <ColorInput v-else-if="props.type === 'color'" v-model="value" />
  <div v-else-if="props.type === 'link'" class="flex flex-col gap-2" data-testid="link-input">
    <USelect
      :model-value="linkTargets.some(t => t.to === value) ? value : undefined"
      :items="linkTargets.map(t => ({ label: `${t.label} (${t.to})`, value: t.to }))"
      placeholder="Страница сайта"
      class="w-full"
      data-testid="link-select"
      @update:model-value="(v: string) => { value = v }"
    />
    <UInput v-model="value" placeholder="/services#agent — пусто, если ссылка не нужна" class="w-full" data-testid="link-path" />
  </div>
  <UInput
    v-else-if="props.type === 'url'"
    v-model="value"
    type="url"
    placeholder="https://example.com — пусто, если ссылки нет"
    class="w-full"
    data-testid="url-input"
  />
  <div v-else-if="props.type === 'lines'" class="flex flex-col gap-1">
    <UTextarea v-model="value" class="w-full" :rows="4" autoresize data-testid="lines-input" />
    <p class="text-xs text-muted">Каждая строка — отдельный пункт.</p>
  </div>
  <div v-else-if="props.type === 'file'" class="flex flex-col gap-2" data-testid="file-input">
    <p class="break-all text-xs" :class="value ? '' : 'text-muted'">{{ value ? documentFileName(value) : 'Файл не загружен' }}</p>
    <input
      type="file"
      accept=".pdf,.xlsx,.xls,.docx,.doc,application/pdf"
      :disabled="uploading"
      data-testid="document-file"
      @change="onFile"
    >
    <p class="text-xs text-muted">PDF, Excel или Word, до 20 МБ.</p>
  </div>
  <div v-else-if="props.type === 'image'" class="flex flex-col gap-2" data-testid="image-input">
    <img v-if="value" :src="value" alt="" class="max-h-40 rounded-md border border-default object-contain">
    <p class="break-all text-xs text-muted">{{ value }}</p>
    <input type="file" accept="image/jpeg,image/png,image/webp" :disabled="uploading" data-testid="image-file" @change="onFile">
  </div>
</template>

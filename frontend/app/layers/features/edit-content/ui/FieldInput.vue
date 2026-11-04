<script setup lang="ts">
import { uploadImage } from '@entities/page-content'
import type { FieldType } from '@entities/page-content'
import IconPicker from './IconPicker.vue'

/** Input for one value of the given type: a standalone element's value or one field of an Item. */
const props = defineProps<{ type: FieldType }>()
const value = defineModel<string>({ required: true })
const emit = defineEmits<{ error: [message: string] }>()

const uploading = defineModel<boolean>('uploading', { default: false })

async function onFile(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file) return
  uploading.value = true
  try {
    value.value = await uploadImage(file)
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
  <div v-else class="flex flex-col gap-2" data-testid="image-input">
    <img v-if="value" :src="value" alt="" class="max-h-40 rounded-md border border-default object-contain">
    <p class="break-all text-xs text-muted">{{ value }}</p>
    <input type="file" accept="image/jpeg,image/png,image/webp" :disabled="uploading" data-testid="image-file" @change="onFile">
  </div>
</template>

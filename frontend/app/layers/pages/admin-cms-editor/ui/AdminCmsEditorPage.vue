<script setup lang="ts">
import { isHostMessage } from '@entities/page-content'
import type { EditorMessage, OpenMessage, SavedElement } from '@entities/page-content'
import { ElementEditor } from '@features/edit-content'

/**
 * Editor panel shown inside an iframe on the public site. It talks to the page via postMessage:
 * the page sends the element to edit, this panel saves it and reports the change back.
 */
const current = ref<OpenMessage | null>(null)

function post(message: EditorMessage) {
  window.parent.postMessage(message, window.location.origin)
}

function onMessage(event: MessageEvent) {
  if (event.origin !== window.location.origin || event.source !== window.parent) return
  if (isHostMessage(event.data)) current.value = event.data
}

function onSaved(patch: SavedElement) {
  if (!current.value) return
  const { page, path } = current.value
  current.value = { ...current.value, element: { ...current.value.element, ...patch } }
  post({ source: 'cms-editor', type: 'saved', page, path, patch })
}

onMounted(() => {
  window.addEventListener('message', onMessage)
  post({ source: 'cms-editor', type: 'ready' })
})
onBeforeUnmount(() => window.removeEventListener('message', onMessage))
</script>

<template>
  <div class="flex h-screen flex-col">
    <header class="flex items-center justify-between border-b border-default px-4 py-3">
      <h1 class="text-sm font-semibold">Редактирование</h1>
      <UButton
        icon="i-lucide-x"
        color="neutral"
        variant="ghost"
        aria-label="Закрыть"
        data-testid="close-button"
        @click="post({ source: 'cms-editor', type: 'close' })"
      />
    </header>
    <div class="flex-1 overflow-y-auto p-4">
      <ElementEditor
        v-if="current"
        :page="current.page"
        :path="current.path"
        :element="current.element"
        @saved="onSaved"
      />
      <p v-else class="text-sm text-muted">Выберите элемент на странице.</p>
    </div>
  </div>
</template>

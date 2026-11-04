<script setup lang="ts">
import { EDITOR_PATH, isEditorMessage, useEditMode, usePageContent } from '@entities/page-content'
import type { HostMessage } from '@entities/page-content'
import { useCurrentUser } from '@entities/session'

/**
 * Floating "Edit" button, the editor panel (an iframe on the admin layout, so public and admin styles never mix)
 * and the "applied" toast. Everything is decided on the client after hydration, so SSR HTML is the same for all.
 */
const { can, load } = useCurrentUser()
const { active, selected, toggle, deselect } = useEditMode()
const { resolveElement, apply } = usePageContent()

const checked = ref(false)
const canEdit = computed(() => checked.value && can('edit_page'))
const frame = ref<HTMLIFrameElement | null>(null)
const frameReady = ref(false)
const toast = ref(false)
let toastTimer: ReturnType<typeof setTimeout> | undefined

onMounted(async () => {
  try {
    await load()
  }
  finally {
    checked.value = true
  }
  window.addEventListener('message', onMessage)
  window.addEventListener('keydown', onKeydown)
})

onBeforeUnmount(() => {
  window.removeEventListener('message', onMessage)
  window.removeEventListener('keydown', onKeydown)
  clearTimeout(toastTimer)
})

function sendSelection() {
  const target = selected.value
  const element = target ? resolveElement(target.page, target.path) : null
  if (!target || !element || !frameReady.value || !frame.value?.contentWindow) return
  // Saved list Items are reactive proxies, which postMessage cannot clone.
  const plain = JSON.parse(JSON.stringify(element)) as typeof element
  const message: HostMessage = { source: 'cms-host', type: 'open', page: target.page, path: target.path, element: plain }
  frame.value.contentWindow.postMessage(message, window.location.origin)
}

watch(selected, sendSelection)
watch(active, (on) => {
  if (!on) frameReady.value = false
})

function onMessage(event: MessageEvent) {
  const panel = frame.value?.contentWindow
  if (!panel || event.origin !== window.location.origin || event.source !== panel) return
  const data: unknown = event.data
  if (!isEditorMessage(data)) return
  if (data.type === 'ready') {
    frameReady.value = true
    sendSelection()
  }
  else if (data.type === 'saved') {
    apply(data.page, data.path, data.patch)
    toast.value = true
    clearTimeout(toastTimer)
    toastTimer = setTimeout(() => {
      toast.value = false
    }, 2500)
  }
  else {
    deselect()
  }
}

function onKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape' && selected.value) deselect()
}
</script>

<template>
  <div v-if="canEdit" class="cms-controls">
    <button type="button" class="cms-controls__toggle" :aria-pressed="active" data-testid="edit-toggle" @click="toggle">
      {{ active ? 'Готово' : 'Редактировать' }}
    </button>
    <iframe
      v-if="active"
      ref="frame"
      class="cms-controls__panel"
      :class="{ 'is-open': selected }"
      :src="EDITOR_PATH"
      title="Редактор содержимого"
      data-testid="editor-frame"
    />
    <div v-if="toast" class="cms-toast" role="status" data-testid="cms-toast">Применено</div>
  </div>
</template>

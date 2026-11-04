import type { ComputedRef } from 'vue'
import { usePageContent } from './use-page-content'
import { useEditMode } from './use-edit-mode'

/**
 * Attributes that make a rendered Content element editable: highlight, address, and a click that opens it
 * in the panel. Outside Edit mode there are none, so the SSR HTML is identical for everyone.
 */
function useEditAttrs(page: () => string, path: () => string, element: ComputedRef<{ enabled: boolean } | null>) {
  const edit = useEditMode()

  return computed(() => {
    if (!edit.active.value || !element.value) return {}
    return {
      'class': ['cms-editable', { 'cms-disabled': !element.value.enabled }],
      'data-cms-path': `${page()}:${path()}`,
      'onClickCapture': (event: MouseEvent) => {
        event.preventDefault()
        event.stopPropagation()
        edit.select(page(), path())
      },
    }
  })
}

/**
 * Shared logic of the single-value Cms* components: the resolved element, whether it renders, and the
 * attributes that make it editable. Outside Edit mode a disabled element renders nothing.
 */
export function useCmsElement(page: () => string, path: () => string) {
  const { resolve } = usePageContent()
  const edit = useEditMode()

  const element = computed(() => resolve(page(), path()))
  const visible = computed(() => element.value !== null && (element.value.enabled || edit.active.value))
  const editAttrs = useEditAttrs(page, path, element)

  return { element, visible, editAttrs }
}

/**
 * Same for a Content list. Visitors see nothing for a disabled or empty list; in Edit mode it always renders,
 * so an empty list can still be clicked and filled.
 */
export function useCmsList(page: () => string, path: () => string) {
  const { resolveList } = usePageContent()
  const edit = useEditMode()

  const element = computed(() => resolveList(page(), path()))
  const items = computed(() => element.value?.value ?? [])
  const visible = computed(() => element.value !== null
    && (edit.active.value || (element.value.enabled && items.value.length > 0)))
  const editAttrs = useEditAttrs(page, path, element)

  return { element, items, visible, editAttrs, editing: edit.active }
}

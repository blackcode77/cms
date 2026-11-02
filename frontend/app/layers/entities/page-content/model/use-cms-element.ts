import { usePageContent } from './use-page-content'
import { useEditMode } from './use-edit-mode'

/**
 * Shared logic of the Cms* components: the resolved element, whether it renders, and the attributes
 * that make it editable. Outside Edit mode a disabled element renders nothing and no editing attributes exist,
 * so the SSR HTML is identical for everyone.
 */
export function useCmsElement(page: () => string, path: () => string) {
  const { resolve } = usePageContent()
  const edit = useEditMode()

  const element = computed(() => resolve(page(), path()))
  const visible = computed(() => element.value !== null && (element.value.enabled || edit.active.value))

  const editAttrs = computed(() => {
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

  return { element, visible, editAttrs }
}

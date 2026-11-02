export interface SelectedElement {
  page: string
  path: string
}

/** Edit mode of the public site: whether Content elements are editable and which one is open in the panel. */
export function useEditMode() {
  const active = useState<boolean>('cms-edit-mode', () => false)
  const selected = useState<SelectedElement | null>('cms-selected', () => null)

  function toggle(): void {
    active.value = !active.value
    if (!active.value) selected.value = null
  }

  function select(page: string, path: string): void {
    selected.value = { page, path }
  }

  function deselect(): void {
    selected.value = null
  }

  return { active, selected, toggle, select, deselect }
}

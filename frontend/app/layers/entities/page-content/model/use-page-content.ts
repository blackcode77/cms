import { fetchPageContent } from '../api/page-content-api'
import { getElementDef } from './schema'
import type { ResolvedElement, SavedElement, SavedPages } from './types'

/**
 * Saved Content element values of the visited pages, shared between SSR and the browser (`useState`).
 * Missing values fall back to the defaults declared in code, so an empty backend still renders the full page.
 */
export function usePageContent() {
  const content = useState<SavedPages>('cms-content', () => ({}))
  const loadedPages = useState<string[]>('cms-content-loaded', () => [])

  function resolve(page: string, path: string): ResolvedElement | null {
    const def = getElementDef(page, path)
    if (!def) return null
    const saved = content.value[page]?.[path]
    return {
      type: def.type,
      value: saved?.value ?? def.default,
      default: def.default,
      label: def.label ?? path,
      enabled: saved?.enabled ?? true,
    }
  }

  /** Loads pages not loaded yet. A backend failure is swallowed: the site must render with defaults. */
  async function ensureLoaded(pages: string[]): Promise<void> {
    const missing = pages.filter(page => !loadedPages.value.includes(page))
    if (missing.length === 0) return
    try {
      const fetched = await fetchPageContent(missing)
      content.value = { ...content.value, ...fetched }
      loadedPages.value = [...loadedPages.value, ...missing]
    }
    catch {
      // Defaults are shown; the next navigation retries.
    }
  }

  /** Applies a saved change locally so the page updates without a reload. */
  function apply(page: string, path: string, patch: SavedElement): void {
    const pageContent = content.value[page] ?? {}
    content.value = { ...content.value, [page]: { ...pageContent, [path]: { ...pageContent[path], ...patch } } }
  }

  return { content, resolve, ensureLoaded, apply }
}

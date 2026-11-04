import { fetchPageContent } from '../api/page-content-api'
import { getElementDef } from './schema'
import { withDefaultIds } from './list-items'
import type { ResolvedElement, ResolvedList, ResolvedValue, SavedElement, SavedPages } from './types'

/**
 * Saved Content element values of the visited pages, shared between SSR and the browser (`useState`).
 * Missing values fall back to the defaults declared in code, so an empty backend still renders the full page.
 */
export function usePageContent() {
  const content = useState<SavedPages>('cms-content', () => ({}))
  const loadedPages = useState<string[]>('cms-content-loaded', () => [])

  /** Any Content element (single value or list); `null` if the page does not declare the path. */
  function resolveElement(page: string, path: string): ResolvedElement | null {
    const def = getElementDef(page, path)
    if (!def) return null
    const saved = content.value[page]?.[path]
    const enabled = saved?.enabled ?? true
    const label = def.label ?? path

    if (def.type === 'list') {
      const defaults = withDefaultIds(def.default)
      // A value saved under another type (the schema changed since) is ignored, like an orphan.
      const value = saved?.type === 'list' && Array.isArray(saved.value) ? saved.value : defaults
      const fields = Object.fromEntries(Object.entries(def.fields).map(([name, field]) => {
        const background = field.background ? resolveElement(page, field.background) : null
        return [name, background && background.type !== 'list' ? { ...field, backgroundUrl: background.value } : field]
      }))
      return { type: 'list', value, default: defaults, label, enabled, fields, min: def.min ?? 0, max: def.max ?? 100 }
    }

    const value = typeof saved?.value === 'string' ? saved.value : def.default
    return { type: def.type, value, default: def.default, label, enabled }
  }

  /** A single-value Content element; `null` if not declared or declared as a list. */
  function resolve(page: string, path: string): ResolvedValue | null {
    const element = resolveElement(page, path)
    return element && element.type !== 'list' ? element : null
  }

  /** A Content list; `null` if not declared or not a list. */
  function resolveList(page: string, path: string): ResolvedList | null {
    const element = resolveElement(page, path)
    return element?.type === 'list' ? element : null
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

  return { content, resolve, resolveList, resolveElement, ensureLoaded, apply }
}

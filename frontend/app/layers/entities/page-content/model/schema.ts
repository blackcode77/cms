import type { ContentSchema, ElementDef } from './types'

/** Static, code-defined schemas per page key; filled when the modules declaring them are imported. */
const registry = new Map<string, ContentSchema>()

/**
 * Declares Content elements of a Page (path, type, default) next to the page that renders them and
 * returns the declaration. May be called several times for one page key, e.g. `site` from header and footer.
 */
export function defineContentSchema<T extends ContentSchema>(page: string, schema: T): T {
  registry.set(page, { ...registry.get(page), ...schema })
  return schema
}

export function getElementDef(page: string, path: string): ElementDef | undefined {
  return registry.get(page)?.[path]
}

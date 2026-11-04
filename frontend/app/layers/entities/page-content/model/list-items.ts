import type { FieldDef, FieldType, ListItem } from './types'

/** Default Items get deterministic ids, so SSR and the browser agree and the first save keeps them. */
export function withDefaultIds(items: Array<Record<string, string>>): ListItem[] {
  return items.map((item, index) => ({ ...item, id: `d${index + 1}` }))
}

export function newItemId(): string {
  return `i${Date.now().toString(36)}${Math.random().toString(36).slice(2, 8)}`
}

/** A fresh Item with each field's declared default. */
export function newItem(fields: Record<string, FieldDef>): ListItem {
  const item: ListItem = { id: newItemId() }
  for (const [name, def] of Object.entries(fields)) {
    if (def.default) item[name] = def.default
  }
  return item
}

/** Field name → type, sent with every list change so the backend can validate and sanitize (ADR 0005). */
export function fieldTypes(fields: Record<string, FieldDef>): Record<string, FieldType> {
  return Object.fromEntries(Object.entries(fields).map(([name, def]) => [name, def.type]))
}

/**
 * Items as the backend accepts them: only declared fields, and empty values dropped
 * (an empty image or icon is not a valid value, an absent one is).
 */
export function cleanItems(items: ListItem[], fields: Record<string, FieldDef>): ListItem[] {
  return items.map((item) => {
    const clean: ListItem = { id: item.id }
    for (const name of Object.keys(fields)) {
      const value = item[name]
      if (value) clean[name] = value
    }
    return clean
  })
}

/** Returns a copy with the Item at `index` moved by `delta` (-1 up, +1 down); out-of-range moves are no-ops. */
export function moveItem<T>(items: T[], index: number, delta: number): T[] {
  const target = index + delta
  if (target < 0 || target >= items.length) return items
  const copy = [...items]
  const [item] = copy.splice(index, 1) as [T]
  copy.splice(target, 0, item)
  return copy
}

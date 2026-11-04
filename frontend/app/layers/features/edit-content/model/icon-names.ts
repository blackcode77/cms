/** How many matches the picker shows at once; the user narrows down by typing. */
export const ICON_RESULTS_LIMIT = 120

let names: Promise<string[]> | null = null

/** Names of every Lucide icon. The collection is large, so it is loaded lazily, only in the editor panel. */
export function loadIconNames(): Promise<string[]> {
  names ??= import('@iconify-json/lucide/icons.json').then((m) => {
    const data = (m.default ?? m) as { icons: Record<string, unknown> }
    return Object.keys(data.icons).sort()
  })
  return names
}

/** Icons whose name contains every word of the query (words are separated by spaces or dashes). */
export async function searchIcons(query: string): Promise<string[]> {
  const words = query.toLowerCase().split(/[\s-]+/).filter(Boolean)
  const all = await loadIconNames()
  return all.filter(name => words.every(word => name.includes(word))).slice(0, ICON_RESULTS_LIMIT)
}

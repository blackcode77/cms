/** Types a single value can have: a standalone Content element or a field of an Item. */
export type FieldType = 'string' | 'text' | 'html' | 'image' | 'icon'
export type ContentType = FieldType | 'list'

/** One Item of a Content list: a stable `id` plus string values of its fields (absent field = empty). */
export type ListItem = { id: string } & Record<string, string>

/** Declaration of one Item field. `default` pre-fills Items an editor adds. */
export interface FieldDef {
  type: FieldType
  label?: string
  default?: string
}

/** Declaration of one Content element in code: its type and the default shown until an editor saves a value. */
export interface ValueElementDef {
  type: FieldType
  default: string
  /** Shown in the editor panel; falls back to the path. */
  label?: string
}

/** Declaration of a Content list: its Item fields, Item count limits and default Items (without ids). */
export interface ListElementDef {
  type: 'list'
  fields: Record<string, FieldDef>
  default: Array<Record<string, string>>
  label?: string
  min?: number
  max?: number
}

export type ElementDef = ValueElementDef | ListElementDef

export type ContentSchema = Record<string, ElementDef>

/** What the backend stores for a path (`GET /page-content`); every field is optional. */
export interface SavedElement {
  type?: ContentType
  value?: string | ListItem[]
  enabled?: boolean
}

export type SavedPages = Record<string, Record<string, SavedElement>>

/** A single-value Content element with the saved value (or the default) applied. */
export interface ResolvedValue {
  type: FieldType
  value: string
  default: string
  label: string
  enabled: boolean
}

/** A Content list with the saved Items (or the default ones) applied. */
export interface ResolvedList {
  type: 'list'
  value: ListItem[]
  default: ListItem[]
  label: string
  enabled: boolean
  fields: Record<string, FieldDef>
  min: number
  max: number
}

export type ResolvedElement = ResolvedValue | ResolvedList

/** A change sent to `PATCH /page-content/{page}`; a list carries its field types (ADR 0005). */
export type ElementPatch =
  | { type: FieldType, value: string, enabled?: boolean }
  | { type: 'list', value: ListItem[], fields: Record<string, FieldType>, enabled?: boolean }
  | { enabled: boolean, type?: never, value?: never }

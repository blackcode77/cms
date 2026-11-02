export type ContentType = 'string' | 'text' | 'html' | 'image'

/** Declaration of one Content element in code: its type and the default shown until an editor saves a value. */
export interface ElementDef {
  type: ContentType
  default: string
  /** Shown in the editor panel; falls back to the path. */
  label?: string
}

export type ContentSchema = Record<string, ElementDef>

/** What the backend stores for a path (`GET /page-content`); every field is optional. */
export interface SavedElement {
  type?: ContentType
  value?: string
  enabled?: boolean
}

export type SavedPages = Record<string, Record<string, SavedElement>>

/** A Content element with the saved value (or the default) applied. */
export interface ResolvedElement {
  type: ContentType
  value: string
  default: string
  label: string
  enabled: boolean
}

/** A change sent to `PATCH /page-content/{page}`. */
export type ElementPatch = { type: ContentType, value: string, enabled?: boolean } | { enabled: boolean, type?: never, value?: never }

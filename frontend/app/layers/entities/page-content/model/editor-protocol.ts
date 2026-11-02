import type { ContentType, SavedElement } from './types'

/**
 * `postMessage` protocol between the public page (host) and the editor panel iframe (`/admin/cms-editor`).
 * Both sides must also check `event.origin` and `event.source`.
 */
export interface OpenMessage {
  source: 'cms-host'
  type: 'open'
  page: string
  path: string
  element: { type: ContentType, value: string, default: string, label: string, enabled: boolean }
}

export type HostMessage = OpenMessage

export type EditorMessage =
  | { source: 'cms-editor', type: 'ready' }
  | { source: 'cms-editor', type: 'saved', page: string, path: string, patch: SavedElement }
  | { source: 'cms-editor', type: 'close' }

function hasSource(data: unknown, source: string): data is { source: string, type: string } {
  return typeof data === 'object' && data !== null && (data as { source?: unknown }).source === source
}

export function isHostMessage(data: unknown): data is HostMessage {
  return hasSource(data, 'cms-host') && data.type === 'open'
}

export function isEditorMessage(data: unknown): data is EditorMessage {
  return hasSource(data, 'cms-editor') && ['ready', 'saved', 'close'].includes(data.type)
}

export const EDITOR_PATH = '/admin/cms-editor'

import { unwrap, useApi } from '@shared/api'
import type { ApiSuccess } from '@shared/api'
import type { ElementPatch, SavedPages } from '../model/types'

export async function fetchPageContent(pages: string[]): Promise<SavedPages> {
  const api = useApi()
  return unwrap(await api<ApiSuccess<SavedPages>>('/v1/page-content', { query: { keys: pages.join(',') } }))
}

export async function patchElement(page: string, path: string, patch: ElementPatch): Promise<void> {
  const api = useApi()
  await api(`/v1/page-content/${page}`, { method: 'PATCH', body: { path, ...patch } })
}

/** Uploads an image and returns its public path. */
export async function uploadImage(file: File): Promise<string> {
  const api = useApi()
  const body = new FormData()
  body.append('file', file)
  return unwrap(await api<ApiSuccess<{ url: string }>>('/v1/uploads/image', { method: 'POST', body })).url
}

/** Uploads a Document (PDF, Excel, Word) and returns its public path and size in bytes. */
export async function uploadDocument(file: File): Promise<{ url: string, size: number }> {
  const api = useApi()
  const body = new FormData()
  body.append('file', file)
  return unwrap(await api<ApiSuccess<{ url: string, size: number }>>('/v1/uploads/file', { method: 'POST', body }))
}

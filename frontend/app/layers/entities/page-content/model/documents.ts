/** Format label of a Document from its path, e.g. `/uploads/files/<id>/tarify.xlsx` → `XLSX`; empty without a file. */
export function documentFormat(path: string | undefined): string {
  const match = /\.([a-z0-9]+)$/i.exec(path ?? '')
  return match ? match[1]!.toUpperCase() : ''
}

/** Human-readable size in Russian units: «74 КБ», «1,5 МБ». */
export function formatFileSize(bytes: number): string {
  if (bytes < 1024 * 1024) return `${Math.max(1, Math.round(bytes / 1024))} КБ`
  return `${(bytes / (1024 * 1024)).toFixed(1).replace('.', ',').replace(/,0$/, '')} МБ`
}

/** Name a visitor's browser saves the Document under (the last path segment). */
export function documentFileName(path: string): string {
  return path.split('/').pop() ?? ''
}

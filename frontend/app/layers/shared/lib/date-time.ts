const formatter = new Intl.DateTimeFormat('ru-RU', {
  day: '2-digit',
  month: '2-digit',
  year: 'numeric',
  hour: '2-digit',
  minute: '2-digit',
})

/** `05.11.2026, 14:32` in the browser's time zone. */
export function formatDateTime(iso: string): string {
  return formatter.format(new Date(iso))
}

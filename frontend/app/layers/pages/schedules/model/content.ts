export type DocCategory = 'sched' | 'tariff' | 'local' | 'guide'
export type DocFilter = 'all' | DocCategory

/** Document categories: fixed in code, with their filter buttons. Their titles and documents are Content of the page. */
export const docCategories: DocCategory[] = ['sched', 'tariff', 'local', 'guide']

export const docFilters: { value: DocFilter, label: string }[] = [
  { value: 'all', label: 'Все' },
  { value: 'sched', label: 'Расписания' },
  { value: 'tariff', label: 'Тарифы' },
  { value: 'local', label: 'Локальные сборы' },
  { value: 'guide', label: 'Инструкции' },
]

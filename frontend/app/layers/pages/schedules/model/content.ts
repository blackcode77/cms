export type DocCategory = 'sched' | 'tariff' | 'local' | 'guide'
export type DocFilter = 'all' | DocCategory

export interface DocItem { ext: string, title: string, note: string, size: string }
export interface DocGroup { category: DocCategory, title: string, items: DocItem[] }
export interface Step { title: string, text: string }

export const docFilters: { value: DocFilter, label: string }[] = [
  { value: 'all', label: 'Все' },
  { value: 'sched', label: 'Расписания' },
  { value: 'tariff', label: 'Тарифы' },
  { value: 'local', label: 'Локальные сборы' },
  { value: 'guide', label: 'Инструкции' },
]

export const docGroups: DocGroup[] = [
  {
    category: 'sched',
    title: 'Расписания движения флота',
    items: [
      { ext: 'PDF', title: 'Расписание NEWSTRIVER · UNITY LINE', note: 'Ноябрь 2026', size: '210 КБ' },
      { ext: 'PDF', title: 'Расписание HE XING 7 · UNITY LINE', note: 'Ноябрь 2026', size: '198 КБ' },
      { ext: 'PDF', title: 'Каботажные линии: Владивосток — Магадан / Корсаков', note: 'IV квартал 2026', size: '156 КБ' },
    ],
  },
  {
    category: 'tariff',
    title: 'Публичные тарифы',
    items: [
      { ext: 'PDF', title: 'SOC-тарифы: Тайцан, Нинбо, Шанхай (местная доставка)', note: 'Действует с 01.11.2026', size: '340 КБ' },
      { ext: 'PDF', title: 'SOC-ставки на импорт', note: 'Действует с 01.11.2026', size: '285 КБ' },
      { ext: 'PDF', title: 'SOC: Китай → станции Врангель', note: 'Действует с 01.11.2026', size: '190 КБ' },
      { ext: 'XLSX', title: 'Каботаж: тарифы на контейнерные перевозки', note: '2026', size: '74 КБ' },
    ],
  },
  {
    category: 'local',
    title: 'Локальные сборы в портах погрузки',
    items: [
      { ext: 'PDF', title: 'Локальные сборы: порты Китая', note: 'Актуально на 01.11.2026', size: '120 КБ' },
      { ext: 'PDF', title: 'Локальные сборы: порты Кореи и Японии', note: 'Актуально на 01.11.2026', size: '98 КБ' },
    ],
  },
  {
    category: 'guide',
    title: 'Инструкции',
    items: [
      { ext: 'PDF', title: 'Contact Guide', note: 'Контакты подразделений', size: '410 КБ' },
      { ext: 'PDF', title: 'Памятка по перевозке опасных грузов', note: '2026', size: '230 КБ' },
    ],
  },
]

export const bookingSteps: Step[] = [
  { title: 'Регистрация', text: 'Свяжитесь с менеджером и передайте реквизиты для заключения договора.' },
  { title: 'Выберите тариф', text: 'Найдите ставку на маршрут в разделе «Публичные тарифы».' },
  { title: 'Заявка на букинг', text: 'Отправьте заявку: груз, количество и тип контейнеров, порты, даты.' },
  { title: 'Подтверждение', text: 'Получите подтверждение, номер букинга и инструкции по подаче контейнера.' },
  { title: 'Документы и отгрузка', text: 'Передайте документы на груз, отследите статус в личном ежедневном отчёте.' },
]

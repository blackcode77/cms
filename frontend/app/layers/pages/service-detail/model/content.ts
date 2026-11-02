export interface Step { title: string, text: string }
export interface Direction { from: string, to: string, transport: string }
export interface SubnavLink { label: string, to: string }

export const included: string[] = [
  'Морская перевозка из Китая, Кореи, Японии во Владивосток, Восточный, Находку',
  'Терминальные и внутрипортовые услуги, таможенное оформление',
  'Железнодорожная доставка: Москва, Новосибирск, Екатеринбург',
  'Автодоставка «последней мили» собственным транспортом',
  'Предоставление контейнеров (собственных и арендованных)',
  'Ежедневное отслеживание груза',
]

export const orderSteps: Step[] = [
  { title: 'Заявка', text: 'Маршрут, груз, сроки.' },
  { title: 'Расчёт', text: 'Ставка за рабочий день.' },
  { title: 'Договор', text: 'Подписание условий.' },
  { title: 'Перевозка', text: 'Организация и контроль.' },
]

export const directions: Direction[] = [
  { from: 'Китай, Корея, Япония', to: 'Москва', transport: 'Море + ЖД' },
  { from: 'Китай', to: 'Новосибирск, Екатеринбург', transport: 'Море + ЖД' },
  { from: 'Владивосток', to: 'Магадан, Корсаков, Камчатка', transport: 'Море (каботаж)' },
]

export const subnav: SubnavLink[] = [
  { label: 'Мультимодальные перевозки', to: '/services/multimodal' },
  { label: 'Агентирование', to: '/services#agent' },
  { label: 'Проектные перевозки', to: '/project-cargo' },
  { label: 'Опасные грузы', to: '/dangerous-goods' },
]

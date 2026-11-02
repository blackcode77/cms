export interface Company {
  name: string
  text: string
  icon: string
  href: string | null
  linkLabel: string
  main?: boolean
}

const orgIcon = '<circle cx="12" cy="5" r="2"/><path d="M12 7v14M5 13c0 4 3 8 7 8s7-4 7-8M3 13h4M17 13h4M8 10h8"/>'
const shipIcon = '<path d="M3 17l2 3h14l2-3M5 17l-1-6h16l-1 6M8 11V6h8v5M12 3v3"/>'

export const pinIcon = '<path d="M12 21s7-6 7-11a7 7 0 10-14 0c0 5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>'
export const personIcon = '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.5 3.5-7 8-7s8 2.5 8 7"/>'

export const companies: Company[] = [
  { name: 'ООО «Гарант Интермодал»', text: 'Головная компания. Мультимодальные перевозки, экспедирование, агентирование, управление линиями.', icon: orgIcon, href: 'https://garant-intermodal.com', linkLabel: 'garant-intermodal.com →', main: true },
  { name: 'ООО «Восточное судоходное товарищество»', text: 'Судовладелец-оператор двух МПП типа «Сокол» и каботажных линий.', icon: shipIcon, href: null, linkLabel: 'ссылка уточняется →' },
  { name: 'ООО «Акватракинг»', text: 'Автотранспортные услуги: контейнерные перевозки. Создана в 2021 г., доля Гарант Интермодал — 50%.', icon: shipIcon, href: null, linkLabel: 'ссылка уточняется →' },
  { name: 'ООО «Терминал Патрокл»', text: 'Площадка хранения и ремонта порожних контейнеров. Создана в 2023 г.', icon: shipIcon, href: null, linkLabel: 'ссылка уточняется →' },
]

export const managers: string[] = [
  'Генеральный директор',
  'Коммерческий директор',
  'Операционный директор',
  'Директор московского офиса',
]

export const history: { year: string, text: string }[] = [
  { year: '2014', text: 'Открыты каботажные линии Владивосток — Магадан и Владивосток — Корсаков' },
  { year: '2015', text: 'Контейнерная линия Владивосток — Шанхай совместно с САСКО' },
  { year: '2017', text: 'Магадан: сервис совместно с «Оборонлогистикой»' },
  { year: '2020', text: 'Агентирование MSC' },
  { year: '2021', text: 'Агент Dong Young Shipping; генеральный экспедитор Русской рыбопромышленной компании' },
  { year: '2022', text: 'Агентирование CSTAR, Metro Shipping, Eurosib/SUNSCO; автосервис Пусан — Владивосток' },
  { year: '2023', text: 'Открыта площадка хранения контейнеров, создан «Терминал Патрокл»' },
]

export const partners: string[] = ['MSC', 'Transcontainer', 'Dong Young', 'Eurosib', 'SUNSCO', 'Tenpoint', 'ISACO']

export const facts: string[] = [
  '49 сотрудников',
  'Офисы во Владивостоке, Москве и Находке (Восточный)',
  'Прямые договоры с РЖД и железными дорогами СНГ',
  'Сеть агентов в России, СНГ и Юго-Восточной Азии',
]

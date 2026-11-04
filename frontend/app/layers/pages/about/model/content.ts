export interface Company {
  name: string
  text: string
  icon: string
  href: string | null
  linkLabel: string
  main?: boolean
}

export const companies: Company[] = [
  { name: 'ООО «Гарант Интермодал»', text: 'Головная компания. Мультимодальные перевозки, экспедирование, агентирование, управление линиями.', icon: 'anchor', href: 'https://garant-intermodal.com', linkLabel: 'garant-intermodal.com →', main: true },
  { name: 'ООО «Восточное судоходное товарищество»', text: 'Судовладелец-оператор двух МПП типа «Сокол» и каботажных линий.', icon: 'ship', href: null, linkLabel: 'ссылка уточняется →' },
  { name: 'ООО «Акватракинг»', text: 'Автотранспортные услуги: контейнерные перевозки. Создана в 2021 г., доля Гарант Интермодал — 50%.', icon: 'ship', href: null, linkLabel: 'ссылка уточняется →' },
  { name: 'ООО «Терминал Патрокл»', text: 'Площадка хранения и ремонта порожних контейнеров. Создана в 2023 г.', icon: 'ship', href: null, linkLabel: 'ссылка уточняется →' },
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

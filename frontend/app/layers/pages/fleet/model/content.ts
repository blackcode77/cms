export interface SpecRow { label: string, value: string }
export interface RoutePoint { name: string, x: number, y: number, labelY: number }
export interface Vessel {
  id: string
  tab: string
  title: string
  image: string
  width: number
  height: number
  specs: SpecRow[]
  routeLabel: string
  route: RoutePoint[]
}

export const vessels: Vessel[] = [
  {
    id: 'sokol',
    tab: 'МПП «Сокол» (два судна)',
    title: 'МПП «Сокол» (два судна)',
    image: '/img/cabotage.jpg',
    width: 1000,
    height: 750,
    specs: [
      { label: 'Тип', value: 'Многоцелевое судно (МПП), проект «Сокол»' },
      { label: 'Год постройки', value: '2014' },
      { label: 'Оператор', value: 'ООО «Восточное судоходное товарищество»' },
      { label: 'Длина / ширина', value: '—' },
      { label: 'Дедвейт', value: '—' },
      { label: 'Вместимость', value: '—' },
      { label: 'Грузы', value: 'Контейнеры, генеральные, проектные, тяжеловесные' },
    ],
    routeLabel: 'Схема маршрута: Владивосток — Магадан — Петропавловск-К. — Анадырь',
    route: [
      { name: 'ВЛАДИВОСТОК', x: 60, y: 60, labelY: 94 },
      { name: 'МАГАДАН', x: 286.66666666666663, y: 72, labelY: 106 },
      { name: 'ПЕТРОПАВЛОВСК-К.', x: 513.3333333333333, y: 48, labelY: 82 },
      { name: 'АНАДЫРЬ', x: 740, y: 72, labelY: 106 },
    ],
  },
  {
    id: 'nagaevo',
    tab: 'т/х «Гарант Нагаево»',
    title: 'т/х «Гарант Нагаево»',
    image: '/img/garant-nagaevo.jpg',
    width: 506,
    height: 570,
    specs: [
      { label: 'Тип', value: 'Контейнеровоз' },
      { label: 'Условия приобретения', value: 'Бербоут-чартер, 2015 г.' },
      { label: 'Оператор', value: 'Каботажные линии Гарант Интермодал' },
      { label: 'Длина / ширина', value: '—' },
      { label: 'Дедвейт', value: '—' },
      { label: 'Вместимость, TEU', value: '—' },
      { label: 'Грузы', value: 'Контейнерные и генеральные грузы' },
    ],
    routeLabel: 'Схема маршрута: Владивосток — Корсаков — Магадан',
    route: [
      { name: 'ВЛАДИВОСТОК', x: 60, y: 60, labelY: 94 },
      { name: 'КОРСАКОВ', x: 400, y: 72, labelY: 106 },
      { name: 'МАГАДАН', x: 740, y: 48, labelY: 82 },
    ],
  },
]

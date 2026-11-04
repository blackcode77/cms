export const siteName = 'Гарант Интермодал'

export interface NavItem {
  label: string
  to: string
}

/** Main navigation, also reused by the footer and the sitemap. */
export const navigation: NavItem[] = [
  { label: 'Главная', to: '/' },
  { label: 'О компании', to: '/about' },
  { label: 'Услуги', to: '/services' },
  { label: 'Флот', to: '/fleet' },
  { label: 'Расписания и тарифы', to: '/schedules' },
  { label: 'Опасные грузы', to: '/dangerous-goods' },
  { label: 'Контакты', to: '/contacts' },
]

/** Pages that exist but are not in the main menu. Used by the sitemap. */
export const extraRoutes: string[] = ['/project-cargo', '/services/multimodal']

/** Pages offered in the editor's Link dropdown: the menu plus the pages outside it. */
export const linkTargets: NavItem[] = [
  ...navigation,
  { label: 'Проектные перевозки', to: '/project-cargo' },
  { label: 'Мультимодальные перевозки', to: '/services/multimodal' },
]

export interface Office {
  /** Stable id used in Content element paths, e.g. `office.vladivostok.phone`. */
  id: string
  city: string
  address: string
  phone?: string
  email: string
  hours: string
}

export const offices: Office[] = [
  {
    id: 'vladivostok',
    city: 'Владивосток',
    address: 'ул. Запорожская, д. 77, офис 640',
    phone: '+7 (423) 230-20-20',
    email: 'office@garant-intermodal.com',
    hours: 'Пн–Пт 09:00–18:00',
  },
  {
    id: 'moscow',
    city: 'Москва',
    address: 'Киевское шоссе, д. 1, БП «Румянцево», стр. 1, корп. А, подъезд 8, офис А-532',
    phone: '+7 (499) 649-46-79',
    email: 'info-MOW@garant-intermodal.com',
    hours: 'Пн–Пт 09:00–18:00',
  },
  {
    id: 'nakhodka',
    city: 'Находка (Восточный)',
    address: 'Врангель, ул. Внутрипортовая, 19А, каб. 16',
    email: 'vdurnovskiy@garant-intermodal.com',
    hours: 'Пн–Пт 09:00–18:00',
  },
]

/** Digits-only form for tel: links. */
export const telHref = (phone: string): string => `tel:+${phone.replace(/\D/g, '')}`

/** Declaration of one editable string, structurally compatible with the page-content schema. */
export interface OfficeElement {
  type: 'string'
  default: string
  label: string
}

/**
 * Editable office details, shared by the header, footer, contacts and about pages
 * (they live in the `site` content so one edit shows everywhere). Defaults are the values above.
 */
export const officeElements: Record<string, OfficeElement> = Object.fromEntries(
  offices.flatMap(office => ([
    ['address', 'Адрес', office.address],
    ['phone', 'Телефон', office.phone],
    ['email', 'Email', office.email],
    ['hours', 'Часы работы', office.hours],
  ] as const).flatMap(([field, label, value]) => (
    value === undefined
      ? []
      : [[`office.${office.id}.${field}`, { type: 'string' as const, default: value, label: `${label}: ${office.city}` }]]
  ))),
)

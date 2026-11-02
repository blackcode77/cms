import { describe, expect, it } from 'vitest'
import { mountSuspended } from '@nuxt/test-utils/runtime'
import { HomePage } from '@pages/home'
import { AboutPage } from '@pages/about'
import { ServicesPage } from '@pages/services'
import { FleetPage } from '@pages/fleet'
import { SchedulesPage } from '@pages/schedules'
import { DangerousGoodsPage } from '@pages/dangerous-goods'
import { ContactsPage } from '@pages/contacts'
import { ProjectCargoPage } from '@pages/project-cargo'

const pages = [
  ['home', HomePage, 'Кратчайший путь вашего груза'],
  ['about', AboutPage, 'О компании'],
  ['services', ServicesPage, 'Услуги'],
  ['fleet', FleetPage, 'Флот'],
  ['schedules', SchedulesPage, 'Расписания и тарифы'],
  ['dangerous-goods', DangerousGoodsPage, 'Перевозка опасных грузов'],
  ['contacts', ContactsPage, 'Контакты'],
  ['project-cargo', ProjectCargoPage, 'Проектные перевозки'],
] as const

describe('public pages', () => {
  it.each(pages)('%s renders one h1 with its title', async (_name, page, h1) => {
    const wrapper = await mountSuspended(page)
    const headings = wrapper.findAll('h1')

    expect(headings).toHaveLength(1)
    expect(headings[0]!.text().toLowerCase()).toContain(h1.toLowerCase())
  })
})

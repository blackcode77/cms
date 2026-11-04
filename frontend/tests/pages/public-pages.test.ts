import { beforeEach, describe, expect, it } from 'vitest'
import { mountSuspended } from '@nuxt/test-utils/runtime'
import { HomePage } from '@pages/home'
import { AboutPage } from '@pages/about'
import { ServicesPage } from '@pages/services'
import { FleetPage } from '@pages/fleet'
import { SchedulesPage } from '@pages/schedules'
import { DangerousGoodsPage } from '@pages/dangerous-goods'
import { ContactsPage } from '@pages/contacts'
import { ProjectCargoPage } from '@pages/project-cargo'
import { SiteHeader } from '@widgets/site-header'
import { CtaBanner } from '@widgets/cta-banner'
import { usePageContent } from '@entities/page-content'

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

describe('editable content on public pages', () => {
  beforeEach(() => {
    useState('cms-content').value = {}
    useState('cms-content-loaded').value = []
  })

  it('home shows the saved headline instead of the default', async () => {
    usePageContent().apply('home', 'hero.title', { type: 'string', value: 'Edited headline' })

    const wrapper = await mountSuspended(HomePage)

    expect(wrapper.get('h1').text()).toBe('Edited headline')
    expect(wrapper.text()).not.toContain('Кратчайший путь вашего груза')
  })

  it('home lists render their defaults', async () => {
    const wrapper = await mountSuspended(HomePage)

    expect(wrapper.findAll('.stats li')).toHaveLength(5)
    expect(wrapper.findAll('article.adv')).toHaveLength(6)
    expect(wrapper.findAll('ul.checklist')).toHaveLength(2)
    expect(wrapper.findAll('.gallery img')).toHaveLength(4)
    expect(wrapper.findAll('ul.partners li').map(li => li.text())).toContain('ISACO')
  })

  it('home lists follow saved content, and an emptied list disappears', async () => {
    const content = usePageContent()
    content.apply('home', 'partners.items', { type: 'list', value: [{ id: 'x', name: 'New Partner' }] })
    content.apply('home', 'geo.checklist', { type: 'list', value: [] })
    content.apply('home', 'projects.gallery', { type: 'list', value: [{ id: 'u', image: '/uploads/a.webp', alt: 'Uploaded' }] })

    const wrapper = await mountSuspended(HomePage)

    expect(wrapper.findAll('ul.partners li').map(li => li.text())).toEqual(['New Partner'])
    expect(wrapper.findAll('ul.checklist')).toHaveLength(1)
    expect(wrapper.get('.gallery img').attributes()).toMatchObject({ src: '/uploads/a.webp', alt: 'Uploaded' })
  })

  it('contacts hero and the shared header phone follow saved content', async () => {
    const content = usePageContent()
    content.apply('contacts', 'hero.title', { type: 'string', value: 'Edited contacts' })
    content.apply('site', 'office.vladivostok.phone', { type: 'string', value: '+7 (423) 111-22-33' })

    const contacts = await mountSuspended(ContactsPage)
    const header = await mountSuspended(SiteHeader)

    expect(contacts.get('h1').text()).toBe('Edited contacts')
    expect(header.find('a[href="tel:+74231112233"]').text()).toContain('+7 (423) 111-22-33')
  })

  it('a disabled shared element disappears for visitors', async () => {
    usePageContent().apply('site', 'cta.title', { enabled: false })

    const wrapper = await mountSuspended(CtaBanner)

    expect(wrapper.find('h2').exists()).toBe(false)
    expect(wrapper.text()).toContain('Запросить расчёт')
  })
})

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

  it('home service cards link where saved, and a card without a link is a plain block', async () => {
    usePageContent().apply('home', 'services.cards', {
      type: 'list',
      value: [{ id: 'a', title: 'Linked', link: '/fleet' }, { id: 'b', title: 'Plain' }],
    })

    const wrapper = await mountSuspended(HomePage)
    const cards = wrapper.findAll('.grid--4 .card')

    expect(cards[0]!.element.tagName).toBe('A')
    expect(cards[0]!.attributes('href')).toBe('/fleet')
    expect(cards[1]!.element.tagName).toBe('DIV')
    expect(cards[1]!.text()).not.toContain('Подробнее')
  })

  it('services geography groups render one list entry per saved line', async () => {
    usePageContent().apply('services', 'geo.groups', {
      type: 'list',
      value: [{ id: 'g', title: 'Порты', items: 'Владивосток\n\n Находка ' }],
    })

    const wrapper = await mountSuspended(ServicesPage)
    const group = wrapper.get('#geography')

    expect(group.get('h4').text()).toBe('Порты')
    expect(group.findAll('.checklist li').map(li => li.text())).toEqual(['Владивосток', 'Находка'])
  })

  it('schedules notice disappears when its text is switched off', async () => {
    expect((await mountSuspended(SchedulesPage)).find('.alert').exists()).toBe(true)

    usePageContent().apply('schedules', 'notice.text', { enabled: false })

    expect((await mountSuspended(SchedulesPage)).find('.alert').exists()).toBe(false)
  })

  it('fleet vessel specs and the dangerous goods table come from Page content', async () => {
    const content = usePageContent()
    content.apply('fleet', 'vessel.sokol.specs', { type: 'list', value: [{ id: 's', label: 'Дедвейт', value: '5000 т' }] })
    content.apply('dangerous-goods', 'classes.items', { type: 'list', value: [{ id: 'c', code: '9', name: 'Прочие' }] })

    expect((await mountSuspended(FleetPage)).find('table.spec').text()).toContain('5000 т')
    expect((await mountSuspended(DangerousGoodsPage)).findAll('tbody tr')).toHaveLength(1)
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

import { beforeEach, describe, expect, it } from 'vitest'
import { mountSuspended } from '@nuxt/test-utils/runtime'
import { defineContentSchema, usePageContent } from '@entities/page-content'
import { serializeStops } from '@entities/route-map'
import { ShippingMap } from '@widgets/shipping-map'

const stops = serializeStops([
  { x: 10, y: 80, label: 'Шанхай', side: 'left' },
  { x: 60, y: 20, label: 'Владивосток\nВМРП', side: 'right' },
])

defineContentSchema('sm', {
  'routes.title': { type: 'string', default: 'Схема маршрутов' },
  'routes.lead': { type: 'text', default: 'Порты' },
  'routes.map': { type: 'image', default: '/img/map-east-asia.svg' },
  'routes.items': {
    type: 'list',
    fields: { name: { type: 'string' }, color: { type: 'color' }, stops: { type: 'stops', background: 'routes.map' } },
    default: [
      { name: 'Китай → Россия', color: '#e4572e', stops },
      { name: '', color: '#000000', stops },
      { name: 'Сломанный', stops: '[' },
    ],
  },
})

beforeEach(() => {
  useState('cms-content').value = {}
  useState('cms-edit-mode').value = false
})

describe('ShippingMap', () => {
  it('draws drawable Routes with their Stops and lists named ones in the legend', async () => {
    const wrapper = await mountSuspended(ShippingMap, { props: { page: 'sm' } })

    expect(wrapper.text()).toContain('Схема маршрутов')
    expect(wrapper.findAll('[data-testid="route-line"]')).toHaveLength(2)
    expect(wrapper.findAll('[data-testid="route-stop"]')).toHaveLength(4)
    expect(wrapper.text()).toContain('Владивосток\nВМРП')
    const legend = wrapper.find('[data-testid="route-legend"]')
    expect(legend.text()).toBe('Китай → Россия')
    expect(wrapper.find('img').attributes('src')).toBe('/img/map-east-asia.svg')
  })

  it('renders nothing for visitors when the Routes list is switched off', async () => {
    useState('cms-content').value = { sm: { 'routes.items': { enabled: false } } }
    const wrapper = await mountSuspended(ShippingMap, { props: { page: 'sm' } })

    expect(wrapper.find('section').exists()).toBe(false)
  })

  it('passes the current background URL to the stops field of the resolved list', () => {
    useState('cms-content').value = { sm: { 'routes.map': { type: 'image', value: '/uploads/map.png' } } }
    const list = usePageContent().resolveList('sm', 'routes.items')

    expect(list?.fields.stops?.backgroundUrl).toBe('/uploads/map.png')
  })
})

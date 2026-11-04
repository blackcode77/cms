import { readBody } from 'h3'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { ElementEditor } from '@features/edit-content'
import type { ResolvedList } from '@entities/page-content'
import { parseStops, serializeStops } from '@entities/route-map'

const patches: Array<Record<string, unknown>> = []
registerEndpoint('/api/v1/page-content/home', {
  method: 'PATCH',
  handler: async (event) => {
    patches.push(await readBody(event))
    return { status: 'success', data: null }
  },
})

const twoStops = serializeStops([
  { x: 10, y: 80, label: 'Шанхай', side: 'left' },
  { x: 60, y: 20, label: 'Владивосток', side: 'right' },
])

function routes(items = [{ id: 'r1', name: 'Китай → Россия', color: '#3a7b34', stops: twoStops }]): ResolvedList {
  return {
    type: 'list',
    value: items,
    default: items,
    label: 'Маршруты',
    enabled: true,
    fields: {
      name: { type: 'string' },
      color: { type: 'color', default: '#3a7b34' },
      stops: { type: 'stops', background: 'routes.map', backgroundUrl: '/img/map-east-asia.svg' },
    },
    min: 0,
    max: 10,
  }
}

beforeEach(() => {
  patches.length = 0
})

async function mountEditor(element = routes()) {
  const wrapper = await mountSuspended(ElementEditor, { props: { page: 'home', path: 'routes.items', element } })
  const map = wrapper.find('[data-testid="stops-map"]')
  if (map.exists()) {
    vi.spyOn(map.element, 'getBoundingClientRect').mockReturnValue({ left: 0, top: 0, width: 200, height: 100 } as DOMRect)
  }
  return wrapper
}

function savedStops(): unknown {
  const value = patches.at(-1)?.value as Array<Record<string, string>>
  return parseStops(value[0]!.stops)
}

describe('Route editing in the panel', () => {
  it('a click on the map appends a Stop at that percent; saving needs its label', async () => {
    const wrapper = await mountEditor()

    await wrapper.get('[data-testid="stops-map"]').trigger('click', { clientX: 50, clientY: 25 })

    expect(wrapper.findAll('[data-testid="stop-row"]')).toHaveLength(3)
    expect(wrapper.get('[data-testid="list-problem"]').text()).toContain('подпись')
    expect(wrapper.get('[data-testid="save-button"]').attributes('disabled')).toBeDefined()

    await wrapper.findAll('textarea[data-testid="stop-label"]')[2]!.setValue('Врангель')
    expect(wrapper.find('[data-testid="list-problem"]').exists()).toBe(false)
    await wrapper.get('[data-testid="save-button"]').trigger('click')

    await vi.waitFor(() => expect(patches).toHaveLength(1))
    expect(patches[0]!.fields).toEqual({ name: 'string', color: 'color', stops: 'stops' })
    expect(savedStops()).toEqual([
      { x: 10, y: 80, label: 'Шанхай', side: 'left' },
      { x: 60, y: 20, label: 'Владивосток', side: 'right' },
      { x: 25, y: 25, label: 'Врангель', side: 'right' },
    ])
  })

  it('inserts a Stop halfway to the next one and deletes Stops', async () => {
    const wrapper = await mountEditor()

    await wrapper.findAll('[data-testid="stop-insert"]')[0]!.trigger('click')
    await wrapper.findAll('textarea[data-testid="stop-label"]')[1]!.setValue('Пусан')
    await wrapper.findAll('[data-testid="stop-delete"]')[2]!.trigger('click')

    expect(wrapper.findAll('[data-testid="stop-row"]')).toHaveLength(2)
    await wrapper.get('[data-testid="save-button"]').trigger('click')
    await vi.waitFor(() => expect(patches).toHaveLength(1))
    expect(savedStops()).toEqual([
      { x: 10, y: 80, label: 'Шанхай', side: 'left' },
      { x: 35, y: 50, label: 'Пусан', side: 'right' },
    ])
  })

  it('a colour preset sets the line colour', async () => {
    const wrapper = await mountEditor()

    await wrapper.findAll('[data-testid="color-preset"]')[4]!.trigger('click')
    await wrapper.get('[data-testid="save-button"]').trigger('click')

    await vi.waitFor(() => expect(patches).toHaveLength(1))
    expect((patches[0]!.value as Array<Record<string, string>>)[0]!.color).toBe('#b7791f')
  })

  it('shows only one Route on the map at a time', async () => {
    const wrapper = await mountEditor(routes([
      { id: 'r1', name: 'A', color: '#3a7b34', stops: twoStops },
      { id: 'r2', name: 'B', color: '#e4572e', stops: twoStops },
    ]))

    expect(wrapper.findAll('[data-testid="stops-editor"]')).toHaveLength(1)
    expect(wrapper.get('[data-testid="stops-summary"]').text()).toBe('Шанхай → Владивосток')

    await wrapper.get('[data-testid="stops-open"]').trigger('click')
    expect(wrapper.findAll('[data-testid="stops-summary"]')).toHaveLength(1)
  })
})

import { readBody } from 'h3'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { ElementEditor } from '@features/edit-content'
import type { ResolvedList } from '@entities/page-content'

const patches: Array<Record<string, unknown>> = []
registerEndpoint('/api/v1/page-content/home', {
  method: 'PATCH',
  handler: async (event) => {
    patches.push(await readBody(event))
    return { status: 'success', data: null }
  },
})

function list(overrides: Partial<ResolvedList> = {}): ResolvedList {
  const items = [{ id: 'a', icon: 'users', title: 'A' }, { id: 'b', icon: 'ship', title: 'B' }]
  return {
    type: 'list',
    value: items,
    default: items,
    label: 'Advantages',
    enabled: true,
    fields: { icon: { type: 'icon', default: 'circle-check' }, title: { type: 'string', label: 'Title' } },
    min: 0,
    max: 100,
    ...overrides,
  }
}

const fieldTypes = { icon: 'icon', title: 'string' }

beforeEach(() => {
  patches.length = 0
})

async function mountEditor(element: ResolvedList) {
  return mountSuspended(ElementEditor, { props: { page: 'home', path: 'why.items', element } })
}

async function lastSaved(wrapper: Awaited<ReturnType<typeof mountEditor>>) {
  await vi.waitFor(() => expect(wrapper.emitted('saved')).toBeDefined())
  return patches.at(-1)
}

describe('ElementEditor for a Content list', () => {
  it('shows one card per Item', async () => {
    const wrapper = await mountEditor(list())

    expect(wrapper.findAll('[data-testid="list-item"]')).toHaveLength(2)
  })

  it('moving an Item saves the whole reordered list with field types', async () => {
    const wrapper = await mountEditor(list())

    await wrapper.findAll('[data-testid="item-down"]')[0]!.trigger('click')

    expect(await lastSaved(wrapper)).toEqual({
      path: 'why.items',
      type: 'list',
      fields: fieldTypes,
      value: [{ id: 'b', icon: 'ship', title: 'B' }, { id: 'a', icon: 'users', title: 'A' }],
    })
    expect(wrapper.emitted('saved')![0]).toEqual([{ type: 'list', value: [{ id: 'b', icon: 'ship', title: 'B' }, { id: 'a', icon: 'users', title: 'A' }] }])
  })

  it('deleting an Item saves at once', async () => {
    const wrapper = await mountEditor(list())

    await wrapper.findAll('[data-testid="item-delete"]')[0]!.trigger('click')

    expect((await lastSaved(wrapper))?.value).toEqual([{ id: 'b', icon: 'ship', title: 'B' }])
  })

  it('adding an Item saves a new Item with field defaults and a fresh id', async () => {
    const wrapper = await mountEditor(list())

    await wrapper.get('[data-testid="item-add"]').trigger('click')

    const value = (await lastSaved(wrapper))?.value as Array<Record<string, string>>
    expect(value).toHaveLength(3)
    expect(value[2]).toMatchObject({ icon: 'circle-check' })
    expect(value[2]!.id).not.toMatch(/^(a|b)$/)
    expect(value[2]!.title).toBeUndefined()
  })

  it('field edits are saved with the Save button', async () => {
    const wrapper = await mountEditor(list())
    expect(wrapper.get('[data-testid="save-button"]').attributes('disabled')).toBeDefined()

    await wrapper.findAll('input[data-testid="string-input"], [data-testid="string-input"] input')[1]!.setValue('B2')
    expect(patches).toHaveLength(0)
    await wrapper.get('[data-testid="save-button"]').trigger('click')

    expect((await lastSaved(wrapper))?.value).toEqual([{ id: 'a', icon: 'users', title: 'A' }, { id: 'b', icon: 'ship', title: 'B2' }])
  })

  it('respects min and max', async () => {
    const atMin = await mountEditor(list({ min: 2 }))
    expect(atMin.findAll('[data-testid="item-delete"]').every(b => b.attributes('disabled') !== undefined)).toBe(true)

    const atMax = await mountEditor(list({ max: 2 }))
    expect(atMax.get('[data-testid="item-add"]').attributes('disabled')).toBeDefined()
  })
})

describe('link field', () => {
  it('a typed path with an anchor ends up in the saved Item with the link type', async () => {
    const wrapper = await mountEditor(list({
      value: [{ id: 'a', title: 'A' }],
      fields: { title: { type: 'string' }, link: { type: 'link' } },
    }))

    await wrapper.get('[data-testid="link-path"] input, input[data-testid="link-path"]').setValue('/services#agent')
    await wrapper.get('[data-testid="save-button"]').trigger('click')

    expect((await lastSaved(wrapper))?.value).toEqual([{ id: 'a', title: 'A', link: '/services#agent' }])
    expect((await lastSaved(wrapper))?.fields).toEqual({ title: 'string', link: 'link' })
  })
})

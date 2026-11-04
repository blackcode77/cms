import { beforeEach, describe, expect, it } from 'vitest'
import { h } from 'vue'
import { mountSuspended } from '@nuxt/test-utils/runtime'
import {
  CmsIcon,
  CmsList,
  cleanItems,
  defineContentSchema,
  moveItem,
  newItem,
  splitLines,
  useEditMode,
  usePageContent,
} from '@entities/page-content'
import type { ListItem } from '@entities/page-content'

const fields = {
  icon: { type: 'icon' as const, default: 'circle-check' },
  title: { type: 'string' as const },
}

defineContentSchema('l', {
  'adv.items': {
    type: 'list',
    fields,
    default: [{ icon: 'users', title: 'One' }, { icon: 'ship', title: 'Two' }],
    max: 3,
  },
  'adv.icon': { type: 'icon', default: 'truck' },
  'adv.title': { type: 'string', default: 'Title' },
})

beforeEach(() => {
  useState('cms-content').value = {}
  useState('cms-edit-mode').value = false
  useState('cms-selected').value = null
})

function mountList() {
  return mountSuspended(CmsList, {
    props: { page: 'l', path: 'adv.items', as: 'ul' },
    slots: { default: ({ item }: { item: ListItem }) => h('li', item.title) },
  })
}

describe('Content list resolution', () => {
  it('gives default Items deterministic ids and the declared limits', () => {
    expect(usePageContent().resolveList('l', 'adv.items')).toMatchObject({
      type: 'list',
      value: [{ id: 'd1', title: 'One' }, { id: 'd2', title: 'Two' }],
      min: 0,
      max: 3,
      enabled: true,
    })
  })

  it('a saved list fully replaces the default', () => {
    const content = usePageContent()
    content.apply('l', 'adv.items', { type: 'list', value: [{ id: 'x', title: 'Only' }] })

    expect(content.resolveList('l', 'adv.items')?.value).toEqual([{ id: 'x', title: 'Only' }])
  })

  it('ignores a value saved under another type', () => {
    const content = usePageContent()
    content.apply('l', 'adv.items', { type: 'string', value: 'oops' })

    expect(content.resolveList('l', 'adv.items')?.value).toHaveLength(2)
  })

  it('resolve() and resolveList() do not mix single values and lists', () => {
    const content = usePageContent()

    expect(content.resolve('l', 'adv.items')).toBeNull()
    expect(content.resolveList('l', 'adv.title')).toBeNull()
    expect(content.resolveElement('l', 'adv.items')?.type).toBe('list')
  })
})

describe('CmsList', () => {
  it('renders Items in order through the slot inside the wrapper tag', async () => {
    usePageContent().apply('l', 'adv.items', { type: 'list', value: [{ id: 'b', title: 'B' }, { id: 'a', title: 'A' }] })

    const wrapper = await mountList()

    expect(wrapper.element.tagName).toBe('UL')
    expect(wrapper.findAll('li').map(li => li.text())).toEqual(['B', 'A'])
    expect(wrapper.html()).not.toContain('cms-')
  })

  it('renders nothing for visitors when empty or disabled', async () => {
    const content = usePageContent()
    content.apply('l', 'adv.items', { type: 'list', value: [] })
    expect((await mountList()).html()).toBe('<!--v-if-->')

    content.apply('l', 'adv.items', { type: 'list', value: [{ id: 'a', title: 'A' }], enabled: false })
    expect((await mountList()).html()).toBe('<!--v-if-->')
  })

  it('in Edit mode an empty list shows a placeholder and a click selects the whole list', async () => {
    usePageContent().apply('l', 'adv.items', { type: 'list', value: [] })
    const edit = useEditMode()
    edit.toggle()

    const wrapper = await mountList()

    expect(wrapper.get('[data-testid="cms-list-empty"]').element.tagName).toBe('LI')
    expect(wrapper.attributes('data-cms-path')).toBe('l:adv.items')
    wrapper.element.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }))
    expect(edit.selected.value).toEqual({ page: 'l', path: 'adv.items' })
  })
})

describe('CmsIcon', () => {
  // The SVG body is loaded from the local icon endpoint; real SSR output is covered by the public page check.
  it('renders an inline svg for a standalone icon element, and nothing for an empty one', async () => {
    const wrapper = await mountSuspended(CmsIcon, { props: { page: 'l', path: 'adv.icon' } })
    expect(wrapper.find('svg').exists()).toBe(true)

    usePageContent().apply('l', 'adv.icon', { type: 'icon', value: '' })
    await nextTick()
    expect(wrapper.find('svg').exists()).toBe(false)
  })
})

describe('list Item helpers', () => {
  it('a new Item gets a fresh id and the field defaults', () => {
    const a = newItem(fields)
    const b = newItem(fields)

    expect(a).toMatchObject({ icon: 'circle-check' })
    expect(a.title).toBeUndefined()
    expect(a.id).not.toBe(b.id)
  })

  it('cleanItems keeps only declared, non-empty fields', () => {
    expect(cleanItems([{ id: 'a', title: '', icon: 'x', extra: 'y' }], fields)).toEqual([{ id: 'a', icon: 'x' }])
  })

  it('moveItem moves within bounds and ignores moves past the ends', () => {
    expect(moveItem([1, 2, 3], 0, 1)).toEqual([2, 1, 3])
    expect(moveItem([1, 2, 3], 2, -1)).toEqual([1, 3, 2])
    expect(moveItem([1, 2, 3], 0, -1)).toEqual([1, 2, 3])
    expect(moveItem([1, 2, 3], 2, 1)).toEqual([1, 2, 3])
  })
})

describe('splitLines', () => {
  it('gives one trimmed entry per non-empty line', () => {
    expect(splitLines(' one \n\n two\r\n')).toEqual(['one', 'two'])
    expect(splitLines(undefined)).toEqual([])
  })
})

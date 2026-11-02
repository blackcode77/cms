import { beforeEach, describe, expect, it } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import {
  CmsHtml,
  CmsImage,
  CmsText,
  defineContentSchema,
  isEditorMessage,
  isHostMessage,
  useEditMode,
  usePageContent,
} from '@entities/page-content'

defineContentSchema('t', {
  'hero.title': { type: 'string', default: 'Default title' },
  'hero.text': { type: 'text', default: 'Line one\nLine two' },
  'hero.html': { type: 'html', default: '<p>Default <b>html</b></p>' },
  'hero.image': { type: 'image', default: '/img/hero.jpg' },
})

function resetCmsState() {
  useState('cms-content').value = {}
  useState('cms-content-loaded').value = []
  useState('cms-edit-mode').value = false
  useState('cms-selected').value = null
}

let pageCalls = 0
let backendDown = false
registerEndpoint('/api/v1/page-content', () => {
  pageCalls++
  if (backendDown) throw createError({ statusCode: 500 })
  return {
    status: 'success',
    data: { site: {}, t: { 'hero.title': { type: 'string', value: 'Saved title' } } },
  }
})

beforeEach(() => {
  resetCmsState()
  pageCalls = 0
  backendDown = false
})

describe('usePageContent', () => {
  it('resolves the default when nothing is saved', () => {
    expect(usePageContent().resolve('t', 'hero.title')).toMatchObject({
      type: 'string',
      value: 'Default title',
      enabled: true,
    })
  })

  it('resolves the saved value and enabled flag over the default', () => {
    const content = usePageContent()
    content.apply('t', 'hero.title', { type: 'string', value: 'Saved' })
    content.apply('t', 'hero.title', { enabled: false })

    expect(content.resolve('t', 'hero.title')).toMatchObject({ value: 'Saved', enabled: false })
  })

  it('returns null for an element that is not declared', () => {
    expect(usePageContent().resolve('t', 'nope')).toBeNull()
  })

  it('loads missing pages once and merges them', async () => {
    const content = usePageContent()

    await content.ensureLoaded(['site', 't'])
    await content.ensureLoaded(['site', 't'])

    expect(pageCalls).toBe(1)
    expect(content.resolve('t', 'hero.title')?.value).toBe('Saved title')
  })

  it('keeps the defaults when the backend fails, and retries next time', async () => {
    const content = usePageContent()
    backendDown = true

    await content.ensureLoaded(['t'])
    expect(content.resolve('t', 'hero.title')?.value).toBe('Default title')

    backendDown = false
    await content.ensureLoaded(['t'])
    expect(content.resolve('t', 'hero.title')?.value).toBe('Saved title')
  })
})

describe('Cms components', () => {
  it('CmsText renders the default, the saved value, and keeps the requested tag', async () => {
    const wrapper = await mountSuspended(CmsText, { props: { page: 't', path: 'hero.title', as: 'h1' } })
    expect(wrapper.html()).toBe('<h1>Default title</h1>')

    usePageContent().apply('t', 'hero.title', { value: 'Saved' })
    await nextTick()
    expect(wrapper.html()).toBe('<h1>Saved</h1>')
  })

  it('CmsText keeps line breaks of the text type', async () => {
    const wrapper = await mountSuspended(CmsText, { props: { page: 't', path: 'hero.text' } })

    expect(wrapper.attributes('style')).toContain('white-space: pre-line')
  })

  it('CmsHtml renders html', async () => {
    const wrapper = await mountSuspended(CmsHtml, { props: { page: 't', path: 'hero.html' } })

    expect(wrapper.find('b').text()).toBe('html')
  })

  it('CmsImage renders the bundled image and a plain img for uploads', async () => {
    const wrapper = await mountSuspended(CmsImage, { props: { page: 't', path: 'hero.image', alt: 'x' } })
    expect(wrapper.find('img').attributes('src')).toContain('hero.jpg')

    usePageContent().apply('t', 'hero.image', { value: '/uploads/a.png' })
    await nextTick()
    expect(wrapper.find('img').attributes('src')).toBe('/uploads/a.png')
  })

  it('a disabled element renders nothing for visitors', async () => {
    usePageContent().apply('t', 'hero.title', { enabled: false })

    const wrapper = await mountSuspended(CmsText, { props: { page: 't', path: 'hero.title' } })

    expect(wrapper.html()).toBe('<!--v-if-->')
  })

  it('renders no editing attributes outside Edit mode', async () => {
    const wrapper = await mountSuspended(CmsText, { props: { page: 't', path: 'hero.title' } })

    expect(wrapper.html()).not.toContain('cms-')
  })

  it('in Edit mode a disabled element is dimmed, and a click selects it instead of following links', async () => {
    usePageContent().apply('t', 'hero.title', { enabled: false })
    const edit = useEditMode()
    edit.toggle()

    const wrapper = await mountSuspended(CmsText, { props: { page: 't', path: 'hero.title' } })

    expect(wrapper.classes()).toEqual(expect.arrayContaining(['cms-editable', 'cms-disabled']))
    expect(wrapper.attributes('data-cms-path')).toBe('t:hero.title')

    const event = new MouseEvent('click', { bubbles: true, cancelable: true })
    wrapper.element.dispatchEvent(event)
    expect(event.defaultPrevented).toBe(true)
    expect(edit.selected.value).toEqual({ page: 't', path: 'hero.title' })
  })

  it('leaving Edit mode clears the selection', () => {
    const edit = useEditMode()
    edit.toggle()
    edit.select('t', 'hero.title')

    edit.toggle()

    expect(edit.selected.value).toBeNull()
  })
})

describe('editor message protocol', () => {
  it('recognizes only well-formed messages from the right side', () => {
    expect(isEditorMessage({ source: 'cms-editor', type: 'ready' })).toBe(true)
    expect(isEditorMessage({ source: 'cms-editor', type: 'saved', page: 't', path: 'a', patch: {} })).toBe(true)
    expect(isEditorMessage({ source: 'cms-host', type: 'open' })).toBe(false)
    expect(isEditorMessage({ source: 'cms-editor', type: 'evil' })).toBe(false)
    expect(isEditorMessage('ready')).toBe(false)
    expect(isEditorMessage(null)).toBe(false)

    expect(isHostMessage({ source: 'cms-host', type: 'open' })).toBe(true)
    expect(isHostMessage({ source: 'cms-editor', type: 'open' })).toBe(false)
  })
})

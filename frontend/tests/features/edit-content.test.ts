import { readBody, setResponseStatus } from 'h3'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { ElementEditor } from '@features/edit-content'

const patches: Array<Record<string, unknown>> = []
let failNext = false
registerEndpoint('/api/v1/page-content/home', {
  method: 'PATCH',
  handler: async (event) => {
    if (failNext) {
      setResponseStatus(event, 400)
      return { status: 'failed', error_message: 'Value is too long.' }
    }
    patches.push(await readBody(event))
    return { status: 'success', data: null }
  },
})

const element = { type: 'string' as const, value: 'Old', default: 'Default', label: 'Title', enabled: true }

beforeEach(() => {
  patches.length = 0
  failNext = false
})

describe('ElementEditor', () => {
  it('saves a changed value and reports it', async () => {
    const wrapper = await mountSuspended(ElementEditor, { props: { page: 'home', path: 'hero.title', element } })

    expect(wrapper.get('[data-testid="save-button"]').attributes('disabled')).toBeDefined()
    await wrapper.get('[data-testid="string-input"] input, input[data-testid="string-input"]').setValue('New')
    await wrapper.get('[data-testid="save-button"]').trigger('click')
    await vi.waitFor(() => expect(wrapper.emitted('saved')).toBeDefined())

    expect(patches).toEqual([{ path: 'hero.title', type: 'string', value: 'New' }])
    expect(wrapper.emitted('saved')).toEqual([[{ type: 'string', value: 'New' }]])
  })

  it('shows the backend error and does not report a save when it fails', async () => {
    failNext = true
    const wrapper = await mountSuspended(ElementEditor, { props: { page: 'home', path: 'hero.title', element } })

    await wrapper.get('[data-testid="string-input"] input, input[data-testid="string-input"]').setValue('New')
    await wrapper.get('[data-testid="save-button"]').trigger('click')
    await vi.waitFor(() => expect(wrapper.find('[data-testid="editor-error"]').exists()).toBe(true))

    expect(wrapper.get('[data-testid="editor-error"]').text()).toContain('Value is too long.')
    expect(wrapper.emitted('saved')).toBeUndefined()
  })

  it('offers the default value back', async () => {
    const wrapper = await mountSuspended(ElementEditor, { props: { page: 'home', path: 'hero.title', element } })

    await wrapper.get('[data-testid="reset-button"]').trigger('click')

    expect((wrapper.get('input[data-testid="string-input"], [data-testid="string-input"] input').element as HTMLInputElement).value).toBe('Default')
  })
})

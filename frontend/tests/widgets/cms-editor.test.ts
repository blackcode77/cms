import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { CmsEditorControls } from '@widgets/cms-editor'
import { defineContentSchema, usePageContent, useEditMode } from '@entities/page-content'
import { useCurrentUser } from '@entities/session'
import type { Permission } from '@entities/session'

defineContentSchema('w', { 'a.title': { type: 'string', default: 'Default' } })

let permissions: Permission[] | null = null
registerEndpoint('/api/v1/auth/me', () => {
  if (!permissions) throw createError({ statusCode: 401 })
  return {
    status: 'success',
    data: {
      user: { id: 'u1', email: 'a@b.c', name: 'Ann' },
      role: { id: 'r1', name: 'manager' },
      permissions,
      must_change_password: false,
    },
  }
})
registerEndpoint('/api/v1/auth/refresh', {
  method: 'POST',
  handler: () => {
    throw createError({ statusCode: 401 })
  },
})

async function mountControls() {
  const wrapper = await mountSuspended(CmsEditorControls)
  await vi.waitFor(() => expect(useCurrentUser().session.value !== null || permissions === null).toBe(true))
  await nextTick()
  await nextTick()
  return wrapper
}

beforeEach(() => {
  useState('session').value = null
  useState('cms-content').value = {}
  useState('cms-content-loaded').value = []
  useState('cms-edit-mode').value = false
  useState('cms-selected').value = null
  permissions = null
})

describe('CmsEditorControls', () => {
  it('shows nothing to a guest', async () => {
    const wrapper = await mountControls()

    expect(wrapper.find('[data-testid="edit-toggle"]').exists()).toBe(false)
  })

  it('shows nothing to a user without edit_page', async () => {
    permissions = ['view_dashboard']
    const wrapper = await mountControls()

    expect(wrapper.find('[data-testid="edit-toggle"]').exists()).toBe(false)
  })

  it('lets an edit_page holder switch Edit mode on and off', async () => {
    permissions = ['view_dashboard', 'edit_page']
    const wrapper = await mountControls()

    expect(wrapper.find('[data-testid="editor-frame"]').exists()).toBe(false)
    await wrapper.get('[data-testid="edit-toggle"]').trigger('click')
    expect(useEditMode().active.value).toBe(true)
    expect(wrapper.get('[data-testid="editor-frame"]').attributes('src')).toBe('/admin/cms-editor')

    await wrapper.get('[data-testid="edit-toggle"]').trigger('click')
    expect(wrapper.find('[data-testid="editor-frame"]').exists()).toBe(false)
  })

  it('sends the selected element to the panel and applies a saved change with a toast', async () => {
    permissions = ['edit_page']
    const wrapper = await mountControls()
    await wrapper.get('[data-testid="edit-toggle"]').trigger('click')

    const frame = wrapper.get('[data-testid="editor-frame"]').element as HTMLIFrameElement
    const posted: unknown[] = []
    const panel = { postMessage: (message: unknown) => posted.push(message) } as unknown as Window
    Object.defineProperty(frame, 'contentWindow', { value: panel })
    const fromPanel = (data: unknown, source: Window | null = panel, origin = window.location.origin) =>
      window.dispatchEvent(new MessageEvent('message', { data, source, origin }))

    fromPanel({ source: 'cms-editor', type: 'ready' })
    useEditMode().select('w', 'a.title')
    await nextTick()

    expect(posted).toEqual([
      expect.objectContaining({ source: 'cms-host', type: 'open', page: 'w', path: 'a.title' }),
    ])

    // Messages from another window or origin are ignored.
    fromPanel({ source: 'cms-editor', type: 'saved', page: 'w', path: 'a.title', patch: { value: 'Evil' } }, null)
    fromPanel({ source: 'cms-editor', type: 'saved', page: 'w', path: 'a.title', patch: { value: 'Evil' } }, panel, 'https://evil.example')
    await nextTick()
    expect(usePageContent().resolve('w', 'a.title')?.value).toBe('Default')

    fromPanel({ source: 'cms-editor', type: 'saved', page: 'w', path: 'a.title', patch: { type: 'string', value: 'New' } })
    await nextTick()

    expect(usePageContent().resolve('w', 'a.title')?.value).toBe('New')
    expect(wrapper.get('[data-testid="cms-toast"]').text()).toBe('Применено')

    fromPanel({ source: 'cms-editor', type: 'close' })
    await nextTick()
    expect(useEditMode().selected.value).toBeNull()
  })
})

import { readBody } from 'h3'
import { describe, expect, it, vi } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { ElementEditor } from '@features/edit-content'
import { documentFormat, formatFileSize } from '@entities/page-content'
import type { ResolvedList } from '@entities/page-content'

const patches: Array<Record<string, unknown>> = []
registerEndpoint('/api/v1/page-content/schedules', {
  method: 'PATCH',
  handler: async (event) => {
    patches.push(await readBody(event))
    return { status: 'success', data: null }
  },
})
registerEndpoint('/api/v1/uploads/file', {
  method: 'POST',
  handler: () => ({ status: 'success', data: { url: '/uploads/files/0193a1b2-c3d4-7e5f-8a9b-0c1d2e3f4a5b/tarify.xlsx', size: 215040 } }),
})

describe('document helpers', () => {
  it('formats sizes in Russian units and reads the format from the path', () => {
    expect(formatFileSize(500)).toBe('1 КБ')
    expect(formatFileSize(215040)).toBe('210 КБ')
    expect(formatFileSize(1.5 * 1024 * 1024)).toBe('1,5 МБ')
    expect(formatFileSize(2 * 1024 * 1024)).toBe('2 МБ')
    expect(documentFormat('/uploads/files/x/tarify.xlsx')).toBe('XLSX')
    expect(documentFormat('')).toBe('')
  })
})

describe('uploading a Document in the list editor', () => {
  it('stores the path, fills the hidden size field and does not show it to the editor', async () => {
    const element: ResolvedList = {
      type: 'list',
      value: [{ id: 'a', title: 'Тарифы' }],
      default: [],
      label: 'Docs',
      enabled: true,
      fields: {
        title: { type: 'string' },
        file: { type: 'file', sizeField: 'size' },
        size: { type: 'string', hidden: true },
      },
      min: 0,
      max: 100,
    }
    const wrapper = await mountSuspended(ElementEditor, { props: { page: 'schedules', path: 'docs.tariff.items', element } })

    expect(wrapper.findAll('[data-testid="string-input"]')).toHaveLength(1)
    const input = wrapper.get('[data-testid="document-file"]')
    Object.defineProperty(input.element, 'files', { value: [new File(['x'], 'Тарифы.xlsx')] })
    await input.trigger('change')
    await vi.waitFor(() => expect(wrapper.get('[data-testid="file-input"]').text()).toContain('tarify.xlsx'))
    await wrapper.get('[data-testid="save-button"]').trigger('click')
    await vi.waitFor(() => expect(patches).toHaveLength(1))

    expect(patches[0]).toMatchObject({
      type: 'list',
      fields: { title: 'string', file: 'file', size: 'string' },
      value: [{ id: 'a', title: 'Тарифы', file: '/uploads/files/0193a1b2-c3d4-7e5f-8a9b-0c1d2e3f4a5b/tarify.xlsx', size: '210 КБ' }],
    })
  })
})

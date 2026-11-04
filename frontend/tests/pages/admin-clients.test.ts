import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { AdminClientsPage } from '@pages/admin-clients'
import { useCurrentUser } from '@entities/session'
import type { Permission } from '@entities/session'

function signInWith(permissions: Permission[]) {
  useCurrentUser().session.value = {
    user: { id: 'u1', email: 'a@b.c', name: 'Ann' },
    role: { id: 'r1', name: 'x' },
    permissions,
    must_change_password: false,
  }
}

let deleted = 0

describe('AdminClientsPage', () => {
  beforeEach(() => {
    clearNuxtData(key => key.startsWith('admin-clients'))
    deleted = 0
    registerEndpoint('/api/v1/clients', () => ({
      status: 'success',
      data: [{
        id: 'c1',
        phone: '+79991234567',
        first_name: 'Иван',
        last_name: 'Иванов',
        middle_name: 'Иванович',
        email: 'ivan@example.com',
        comment: 'Постоянный',
        orders_count: 3,
        created_at: '2026-11-05T10:00:00+03:00',
      }],
    }))
    registerEndpoint('/api/v1/clients/c1', {
      method: 'DELETE',
      handler: () => {
        deleted++
        return { status: 'success', data: { id: 'c1' } }
      },
    })
  })

  it('lists clients with full name, phone and orders count', async () => {
    signInWith(['view_clients'])

    const wrapper = await mountSuspended(AdminClientsPage)
    const text = wrapper.get('[data-testid="clients-table"]').text()

    expect(text).toContain('Иванов Иван Иванович')
    expect(text).toContain('+79991234567')
    expect(text).toContain('ivan@example.com')
    expect(text).toContain('3')
    expect(wrapper.find('[data-testid="client-create"]').exists()).toBe(false)
  })

  it('warns that the client orders are deleted too before deleting', async () => {
    signInWith(['view_clients', 'manage_clients'])

    const wrapper = await mountSuspended(AdminClientsPage)
    await wrapper.get('[data-testid="client-delete-c1"]').trigger('click')

    const confirm = await vi.waitFor(() => {
      const el = document.body.querySelector<HTMLElement>('[data-testid="client-delete-confirm"]')
      expect(el).not.toBeNull()
      return el!
    })
    expect(document.body.textContent).toContain('Вместе с клиентом будут удалены 3 заказа.')
    expect(deleted).toBe(0)
    confirm.click()
    await vi.waitFor(() => expect(deleted).toBe(1))
  })
})

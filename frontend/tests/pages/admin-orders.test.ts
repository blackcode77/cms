import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { getQuery } from 'h3'
import { AdminOrdersPage } from '@pages/admin-orders'
import { useCurrentUser } from '@entities/session'
import type { Permission } from '@entities/session'
import type { OrderItem } from '@entities/order'

function signInWith(permissions: Permission[]) {
  useCurrentUser().session.value = {
    user: { id: 'u1', email: 'a@b.c', name: 'Ann' },
    role: { id: 'r1', name: 'x' },
    permissions,
    must_change_password: false,
  }
}

const order = (over: Partial<OrderItem>): OrderItem => ({
  id: 'o1',
  number: 1000,
  status: 'request',
  client: { id: 'c1', phone: '+79991234567', first_name: 'Иван', last_name: 'Иванов', middle_name: '' },
  manager: null,
  from: 'Шанхай',
  to: 'Москва',
  cargo_type: 'Контейнер 40\'',
  weight: '20',
  message: 'Нужен расчёт',
  comment: 'Перезвонить',
  created_at: '2026-11-05T14:32:00+03:00',
  updated_at: '2026-11-05T14:32:00+03:00',
  ...over,
})

let queries: Record<string, string>[] = []

describe('AdminOrdersPage', () => {
  beforeEach(() => {
    clearNuxtData(key => key.startsWith('admin-orders'))
    clearNuxtData('admin-orders-managers')
    queries = []
    registerEndpoint('/api/v1/orders', (event) => {
      queries.push(getQuery(event) as Record<string, string>)
      return {
        status: 'success',
        data: [
          order({}),
          order({ id: 'o2', number: 1001, status: 'done', manager: { id: 'u1', name: 'Ann' }, comment: '' }),
        ],
      }
    })
    registerEndpoint('/api/v1/orders/managers', () => ({
      status: 'success',
      data: [{ id: 'u1', name: 'Ann', email: 'a@b.c' }],
    }))
  })

  it('shows number, client, short description, status, manager and comment', async () => {
    signInWith(['view_orders'])

    const wrapper = await mountSuspended(AdminOrdersPage)
    const text = wrapper.get('[data-testid="orders-table"]').text()

    expect(text).toContain('№1000')
    expect(text).toContain('Иванов Иван')
    expect(text).toContain('+79991234567')
    expect(text).toContain('Шанхай → Москва, Контейнер 40\', 20 т')
    expect(text).toContain('Нужен расчёт')
    expect(text).toContain('Заявка')
    expect(text).toContain('Обработана')
    expect(text).toContain('Ann')
    expect(text).toContain('Перезвонить')
    expect(text).toMatch(/05\.11\.2026/)
  })

  it('tints rows by status', async () => {
    signInWith(['view_orders'])

    const wrapper = await mountSuspended(AdminOrdersPage)
    const rows = wrapper.findAll('tbody tr')

    expect(rows[0]!.classes()).toContain('bg-info/5')
    expect(rows[1]!.classes()).toContain('bg-success/5')
  })

  it('hides create, edit and delete without manage_orders', async () => {
    signInWith(['view_orders'])

    const wrapper = await mountSuspended(AdminOrdersPage)

    expect(wrapper.find('[data-testid="order-create"]').exists()).toBe(false)
    expect(wrapper.find('[data-testid="order-edit-o1"]').exists()).toBe(false)
  })

  it('offers create, edit and delete with manage_orders', async () => {
    signInWith(['view_orders', 'manage_orders'])

    const wrapper = await mountSuspended(AdminOrdersPage)

    expect(wrapper.find('[data-testid="order-create"]').exists()).toBe(true)
    expect(wrapper.find('[data-testid="order-edit-o1"]').exists()).toBe(true)
    expect(wrapper.find('[data-testid="order-delete-o2"]').exists()).toBe(true)
  })

  it('asks the backend for my orders only when the switch is on', async () => {
    signInWith(['view_orders'])

    const wrapper = await mountSuspended(AdminOrdersPage)
    expect(queries.some(q => q.manager)).toBe(false)
    await wrapper.get('button[role="switch"]').trigger('click')

    await vi.waitFor(() => expect(queries.some(q => q.manager === 'me')).toBe(true))
  })
})

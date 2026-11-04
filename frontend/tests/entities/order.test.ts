import { describe, expect, it } from 'vitest'
import { orderNumber, orderSummary } from '@entities/order'
import { fullName, plural } from '@shared/lib'

describe('orderSummary', () => {
  it('joins route, cargo type and weight in tonnes', () => {
    expect(orderSummary({ from: 'Шанхай', to: 'Москва', cargo_type: 'Контейнер 40\'', weight: '20' }))
      .toBe('Шанхай → Москва, Контейнер 40\', 20 т')
  })

  it('skips what is unknown and keeps a weight that already has units', () => {
    expect(orderSummary({ from: '', to: 'Москва', cargo_type: '', weight: '20 кг' })).toBe('Москва, 20 кг')
    expect(orderSummary({ from: '', to: '', cargo_type: '', weight: '' })).toBe('')
  })

  it('formats the order number', () => {
    expect(orderNumber(1000)).toBe('№1000')
  })
})

describe('person helpers', () => {
  it('builds the full name from known parts', () => {
    expect(fullName({ last_name: 'Иванов', first_name: 'Иван', middle_name: '' })).toBe('Иванов Иван')
  })

  it.each([[1, 'заказ'], [2, 'заказа'], [5, 'заказов'], [11, 'заказов'], [21, 'заказ'], [24, 'заказа']])(
    'plural(%i)', (count, form) => {
      expect(plural(count, ['заказ', 'заказа', 'заказов'])).toBe(form)
    },
  )
})

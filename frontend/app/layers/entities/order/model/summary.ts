import type { CargoFields } from './types'

/** Short description of an Order: «Шанхай → Москва, Контейнер 40', 20 т». */
export function orderSummary(order: CargoFields): string {
  const route = [order.from, order.to].filter(part => part !== '').join(' → ')
  const weight = order.weight === '' ? '' : /\d$/.test(order.weight) ? `${order.weight} т` : order.weight

  return [route, order.cargo_type, weight].filter(part => part !== '').join(', ')
}

/** `№1000`. */
export const orderNumber = (number: number): string => `№${number}`

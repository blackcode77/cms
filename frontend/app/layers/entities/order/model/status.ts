import type { OrderStatus } from './types'

interface StatusLook {
  label: string
  color: 'info' | 'warning' | 'success'
  /** Light tint of the table row. */
  rowClass: string
}

export const orderStatuses: Record<OrderStatus, StatusLook> = {
  request: { label: 'Заявка', color: 'info', rowClass: 'bg-info/5' },
  in_progress: { label: 'В обработке', color: 'warning', rowClass: 'bg-warning/5' },
  done: { label: 'Обработана', color: 'success', rowClass: 'bg-success/5' },
}

export const orderStatusOptions = (Object.keys(orderStatuses) as OrderStatus[])
  .map(value => ({ value, label: orderStatuses[value].label }))

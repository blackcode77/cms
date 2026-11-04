import { describe, expect, it } from 'vitest'
import { visibleMenuItems } from '@widgets/admin-sidebar'

const items = [
  { label: 'Главная', to: '/admin', permission: 'view_dashboard' as const },
  { label: 'Пользователи', to: '/admin/users', permission: 'view_users' as const },
  { label: 'Open', to: '/admin/open' },
]

describe('visibleMenuItems', () => {
  it('hides items without the matching permission', () => {
    const can = (p: string) => p === 'view_dashboard'

    expect(visibleMenuItems(items, can).map(i => i.label)).toEqual(['Главная', 'Open'])
  })
})

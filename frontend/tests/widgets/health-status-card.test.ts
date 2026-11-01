import { beforeEach, describe, expect, it } from 'vitest'
import { mountSuspended, registerEndpoint } from '@nuxt/test-utils/runtime'
import { HealthStatusCard } from '@widgets/health-status'

describe('HealthStatusCard', () => {
  // useAsyncData caches by key in the shared Nuxt app between tests.
  beforeEach(() => clearNuxtData('health'))

  it('renders backend status from the API', async () => {
    registerEndpoint('/api/v1/health', () => ({
      status: 'success',
      data: { app: 'ok', database: 'ok' },
    }))

    const wrapper = await mountSuspended(HealthStatusCard)

    expect(wrapper.find('[data-testid="health-ok"]').exists()).toBe(true)
    expect(wrapper.text()).toContain('Database')
  })

  it('shows an error when the API fails', async () => {
    registerEndpoint('/api/v1/health', () => {
      throw createError({ statusCode: 500 })
    })

    const wrapper = await mountSuspended(HealthStatusCard)

    expect(wrapper.find('[data-testid="health-error"]').exists()).toBe(true)
  })
})

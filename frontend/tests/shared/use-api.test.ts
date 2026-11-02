import { beforeEach, describe, expect, it } from 'vitest'
import { registerEndpoint } from '@nuxt/test-utils/runtime'
import { useApi } from '@shared/api'

let refreshCalls = 0
let usersCalls = 0
let refreshed = false
let refreshWorks = true

registerEndpoint('/api/v1/users', () => {
  usersCalls++
  if (!refreshed) throw createError({ statusCode: 401 })
  return { status: 'success', data: ['ok'] }
})
registerEndpoint('/api/v1/auth/refresh', {
  method: 'POST',
  handler: async () => {
    refreshCalls++
    await new Promise(resolve => setTimeout(resolve, 20))
    if (!refreshWorks) throw createError({ statusCode: 401 })
    refreshed = true
    return { status: 'success', data: null }
  },
})
registerEndpoint('/api/v1/auth/me', () => {
  throw createError({ statusCode: 401 })
})

describe('useApi 401 recovery', () => {
  beforeEach(() => {
    refreshCalls = 0
    usersCalls = 0
    refreshed = false
    refreshWorks = true
  })

  it('refreshes once and retries the original request', async () => {
    const result = await useApi()<{ data: string[] }>('/v1/users')

    expect(result.data).toEqual(['ok'])
    expect(refreshCalls).toBe(1)
    expect(usersCalls).toBe(2)
  })

  it('shares a single refresh between concurrent callers', async () => {
    const api = useApi()
    const results = await Promise.all([api('/v1/users'), api('/v1/users'), api('/v1/users')])

    expect(results).toHaveLength(3)
    expect(refreshCalls).toBe(1)
  })

  it('gives up when the refresh fails: the original error is thrown and nothing is retried', async () => {
    refreshWorks = false

    await expect(useApi()('/v1/users')).rejects.toMatchObject({ statusCode: 401 })
    expect(usersCalls).toBe(1)
    expect(useRoute().path).toBe('/admin/login')
  })

  it('does not try to refresh for auth endpoints', async () => {
    await expect(useApi()('/v1/auth/me')).rejects.toMatchObject({ statusCode: 401 })
    expect(refreshCalls).toBe(0)
  })
})

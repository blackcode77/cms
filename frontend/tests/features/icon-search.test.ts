import { describe, expect, it } from 'vitest'
import { searchIcons } from '../../app/layers/features/edit-content/model/icon-names'

describe('icon search', () => {
  it('finds Lucide icons by every word of the query', async () => {
    const results = await searchIcons('map pin')

    expect(results).toContain('map-pin')
    expect(results.every(name => name.includes('map') && name.includes('pin'))).toBe(true)
  })

  it('caps the number of results', async () => {
    expect((await searchIcons('')).length).toBe(120)
  })
})

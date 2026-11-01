import { describe, expect, it } from 'vitest'
import { unwrap } from '@shared/api'

describe('unwrap', () => {
  it('returns the data of a success envelope', () => {
    expect(unwrap({ status: 'success', data: { a: 1 } })).toEqual({ a: 1 })
  })
})

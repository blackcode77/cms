import { describe, expect, it } from 'vitest'
import { parseStops, routeSegments, serializeStops, stopsProblem, toRoutes } from '@entities/route-map'
import type { Stop } from '@entities/route-map'

const a: Stop = { x: 10, y: 20, label: 'A', side: 'right' }
const b: Stop = { x: 50, y: 60, label: 'B', side: 'left' }

describe('Route map model', () => {
  it('round-trips Stops, rounding and clamping coordinates', () => {
    const value = serializeStops([{ ...a, x: 10.04 }, { ...b, y: 100.7 }])
    expect(value).toBe('[{"x":10,"y":20,"label":"A","side":"right"},{"x":50,"y":100,"label":"B","side":"left"}]')
    expect(parseStops(value)).toEqual([a, { ...b, y: 100 }])
  })

  it('treats broken values as invalid and empty ones as no Stops', () => {
    expect(parseStops('')).toEqual([])
    expect(parseStops('[')).toBeNull()
    expect(parseStops('[{"x":"1","y":1,"label":"A","side":"top"}]')).toBeNull()
    expect(parseStops('[{"x":1,"y":1,"label":"A","side":"middle"}]')).toBeNull()
  })

  it('skips Routes that cannot be drawn and falls back to the default colour', () => {
    const routes = toRoutes([
      { id: 'ok', stops: serializeStops([a, b]) },
      { id: 'short', stops: serializeStops([a]), color: '#000000' },
      { id: 'broken', stops: '{' },
    ])
    expect(routes).toEqual([{ id: 'ok', name: '', color: '#3a7b34', stops: [a, b] }])
  })

  it('builds one Segment per pair of neighbouring Stops', () => {
    const segments = routeSegments([a, b, { ...a, x: 90 }])
    expect(segments).toHaveLength(2)
    expect(segments[0]!.d).toMatch(/^M144\.0 219\.8C/)
    expect(segments[0]!.length).toBeGreaterThan(0)
  })

  it('explains why Stops cannot be saved', () => {
    expect(stopsProblem([a])).toContain('не меньше 2')
    expect(stopsProblem([a, { ...b, label: ' ' }])).toContain('подпись')
    expect(stopsProblem([a, { ...b, label: 'x\ny\nz' }])).toContain('двух строк')
    expect(stopsProblem([a, b])).toBeNull()
  })
})

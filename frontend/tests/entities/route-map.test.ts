import { describe, expect, it } from 'vitest'
import { fitCamera, FOCUS, MAP_HEIGHT, MAP_WIDTH, parseStops, routeSegments, serializeStops, stopsProblem, toRoutes } from '@entities/route-map'
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
    expect(segments[0]!.d).toMatch(/^M240\.0 305\.0C/)
    expect(segments[0]!.length).toBeGreaterThan(0)
  })

  it('explains why Stops cannot be saved', () => {
    expect(stopsProblem([a])).toContain('не меньше 2')
    expect(stopsProblem([a, { ...b, label: ' ' }])).toContain('подпись')
    expect(stopsProblem([a, { ...b, label: 'x\ny\nz' }])).toContain('двух строк')
    expect(stopsProblem([a, b])).toBeNull()
  })
})

describe('Route map camera', () => {
  const view = { width: 1440, height: 600, padding: { top: 40, right: 120, bottom: 40, left: 600 } }
  const shown = (camera: ReturnType<typeof fitCamera>, x: number, y: number) => ({
    x: x / 100 * MAP_WIDTH * camera.scale + camera.x,
    y: y / 100 * MAP_HEIGHT * camera.scale + camera.y,
  })

  it('keeps the Far East in the free part of the block by default', () => {
    const camera = fitCamera([], view)
    for (const [x, y] of [[FOCUS.x0, FOCUS.y0], [FOCUS.x1, FOCUS.y1]] as const) {
      const point = shown(camera, x, y)
      expect(point.x).toBeGreaterThanOrEqual(600 - 0.5)
      expect(point.x).toBeLessThanOrEqual(1320 + 0.5)
      expect(point.y).toBeGreaterThanOrEqual(40 - 0.5)
      expect(point.y).toBeLessThanOrEqual(560 + 0.5)
    }
  })

  it('zooms out so that a Stop far away still fits', () => {
    const near = fitCamera([], view)
    const moscow: Stop = { x: 14.2, y: 29.7, label: 'Москва', side: 'top' }
    const far = fitCamera([moscow], view)

    expect(far.scale).toBeLessThan(near.scale)
    const point = shown(far, moscow.x, moscow.y)
    expect(point.x).toBeGreaterThanOrEqual(0)
    expect(point.y).toBeGreaterThanOrEqual(0)
  })

  it('never leaves an empty margin when the picture covers the block', () => {
    const camera = fitCamera([], view)
    expect(camera.x).toBeLessThanOrEqual(0)
    expect(camera.y).toBeLessThanOrEqual(0)
    expect(MAP_WIDTH * camera.scale + camera.x).toBeGreaterThanOrEqual(1440)
    expect(MAP_HEIGHT * camera.scale + camera.y).toBeGreaterThanOrEqual(600)
  })
})

describe('Route map camera with a Stop near the text card', () => {
  it('keeps the Stop clear of the card even if that leaves an empty edge', () => {
    const view = { width: 1440, height: 600, padding: { top: 40, right: 120, bottom: 40, left: 600 } }
    const west: Stop = { x: 22.9, y: 71.2, label: 'Точка А', side: 'left' }
    const camera = fitCamera([west], view)

    expect(west.x / 100 * MAP_WIDTH * camera.scale + camera.x).toBeGreaterThanOrEqual(600 - 0.5)
  })
})

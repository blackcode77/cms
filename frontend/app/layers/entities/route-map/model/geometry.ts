import type { Stop } from './stops'

/** Drawing units of the Route map; the picture keeps this aspect ratio (`/img/map-east-asia.svg`). */
export const MAP_WIDTH = 1440
export const MAP_HEIGHT = 1099

interface Point { x: number, y: number }

const toPoint = (stop: Stop): Point => ({ x: stop.x / 100 * MAP_WIDTH, y: stop.y / 100 * MAP_HEIGHT })

/** One piece of a Route's line between two neighbouring Stops. */
export interface Segment {
  /** SVG path data of the piece. */
  d: string
  /** Approximate length in drawing units, used to pace the animation. */
  length: number
}

/**
 * The smooth curve through all Stops (Catmull-Rom turned into cubic Béziers), split into one Segment per
 * pair of neighbouring Stops so each piece can be drawn in turn.
 */
export function routeSegments(stops: Stop[]): Segment[] {
  const points = stops.map(toPoint)
  const segments: Segment[] = []
  for (let i = 0; i < points.length - 1; i++) {
    const p0 = points[i - 1] ?? points[i]!
    const p1 = points[i]!
    const p2 = points[i + 1]!
    const p3 = points[i + 2] ?? p2
    const span = Math.hypot(p2.x - p1.x, p2.y - p1.y)
    const t1 = limit({ x: (p2.x - p0.x) / 6, y: (p2.y - p0.y) / 6 }, span * MAX_TANGENT)
    const t2 = limit({ x: (p3.x - p1.x) / 6, y: (p3.y - p1.y) / 6 }, span * MAX_TANGENT)
    const c1 = { x: p1.x + t1.x, y: p1.y + t1.y }
    const c2 = { x: p2.x - t2.x, y: p2.y - t2.y }
    segments.push({
      d: `M${f(p1.x)} ${f(p1.y)}C${f(c1.x)} ${f(c1.y)} ${f(c2.x)} ${f(c2.y)} ${f(p2.x)} ${f(p2.y)}`,
      length: bezierLength(p1, c1, c2, p2),
    })
  }
  return segments
}

/** Longest control arm relative to its Segment: keeps a short leg next to a long one from looping. */
const MAX_TANGENT = 0.35

function limit(vector: Point, max: number): Point {
  const length = Math.hypot(vector.x, vector.y)
  return length > max && length > 0 ? { x: vector.x * max / length, y: vector.y * max / length } : vector
}

function f(value: number): string {
  return value.toFixed(1)
}

function bezierLength(p0: Point, p1: Point, p2: Point, p3: Point): number {
  let length = 0
  let prev = p0
  for (let i = 1; i <= 16; i++) {
    const t = i / 16
    const u = 1 - t
    const point = {
      x: u * u * u * p0.x + 3 * u * u * t * p1.x + 3 * u * t * t * p2.x + t * t * t * p3.x,
      y: u * u * u * p0.y + 3 * u * u * t * p1.y + 3 * u * t * t * p2.y + t * t * t * p3.y,
    }
    length += Math.hypot(point.x - prev.x, point.y - prev.y)
    prev = point
  }
  return length
}

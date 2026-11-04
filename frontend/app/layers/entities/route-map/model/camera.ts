import { MAP_HEIGHT, MAP_WIDTH } from './geometry'
import type { Stop } from './stops'

/** Area always kept in view, in percent of the picture: the Russian Far East, Korea, Japan and Shanghai. */
export const FOCUS = { x0: 58, y0: 56, x1: 80, y1: 93 }

/** The block the map is shown in, in CSS pixels; `padding` is room kept free (labels, the text card). */
export interface Viewport {
  width: number
  height: number
  padding: { top: number, right: number, bottom: number, left: number }
}

/** How the picture is placed: a map point `(u, v)` (drawing units) is shown at `(u * scale + x, v * scale + y)`. */
export interface Camera {
  scale: number
  x: number
  y: number
}

/**
 * The largest zoom at which the focus area and every Stop fit into the free part of the viewport, shifted so
 * that the picture leaves no empty margins where it can avoid them.
 */
export function fitCamera(stops: Stop[], view: Viewport, focus = FOCUS): Camera {
  const xs = [focus.x0, focus.x1, ...stops.map(stop => stop.x)].map(v => v / 100 * MAP_WIDTH)
  const ys = [focus.y0, focus.y1, ...stops.map(stop => stop.y)].map(v => v / 100 * MAP_HEIGHT)
  const [left, right, top, bottom] = [Math.min(...xs), Math.max(...xs), Math.min(...ys), Math.max(...ys)]

  const { padding: p } = view
  const freeWidth = Math.max(1, view.width - p.left - p.right)
  const freeHeight = Math.max(1, view.height - p.top - p.bottom)
  const scale = Math.min(freeWidth / Math.max(1, right - left), freeHeight / Math.max(1, bottom - top))

  const x = p.left + freeWidth / 2 - (left + right) / 2 * scale
  const y = p.top + freeHeight / 2 - (top + bottom) / 2 * scale

  return {
    scale,
    x: place(x, view.width, MAP_WIDTH * scale, [p.left - left * scale, view.width - p.right - right * scale]),
    y: place(y, view.height, MAP_HEIGHT * scale, [p.top - top * scale, view.height - p.bottom - bottom * scale]),
  }
}

/**
 * Shifts the picture along one axis so it covers the viewport (or, if smaller, stays whole inside it),
 * but never so far that the fitted area leaves the free part: fitting the Stops wins over filling the edges.
 */
function place(offset: number, viewSize: number, mapSize: number, [fitMin, fitMax]: [number, number]): number {
  const a = viewSize - mapSize
  const lo = Math.max(Math.min(0, a), fitMin)
  const hi = Math.min(Math.max(0, a), fitMax)
  return lo <= hi ? Math.min(Math.max(offset, lo), hi) : offset
}

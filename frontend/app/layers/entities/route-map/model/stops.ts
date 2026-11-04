/** Side of its point on which a Stop's label is shown. */
export type LabelSide = 'top' | 'right' | 'bottom' | 'left'

export const labelSides: ReadonlyArray<{ value: LabelSide, label: string }> = [
  { value: 'top', label: 'Сверху' },
  { value: 'right', label: 'Справа' },
  { value: 'bottom', label: 'Снизу' },
  { value: 'left', label: 'Слева' },
]

/** A labelled point of a Route; `x`/`y` are percent of the Route map picture. */
export interface Stop {
  x: number
  y: number
  label: string
  side: LabelSide
}

/** A Route ready to draw: its colour, optional name and at least two Stops. */
export interface Route {
  id: string
  name: string
  color: string
  stops: Stop[]
}

export const MIN_STOPS = 2
export const MAX_STOPS = 20
export const LABEL_MAX_LENGTH = 60
export const DEFAULT_COLOR = '#3a7b34'

/** Presets of the line colour picker, matching the site palette. */
export const colorPresets = ['#3a7b34', '#1f4a1e', '#2c7da0', '#1d3557', '#b7791f', '#e4572e', '#b3261e', '#231f20']

const sides = new Set<string>(['top', 'right', 'bottom', 'left'])

/** Stops from a stored `stops` value; `null` if it is not valid JSON of Stops (an empty value is `[]`). */
export function parseStops(value: string | undefined): Stop[] | null {
  if (!value) return []
  let raw: unknown
  try {
    raw = JSON.parse(value)
  }
  catch {
    return null
  }
  if (!Array.isArray(raw)) return null
  const stops: Stop[] = []
  for (const item of raw) {
    const { x, y, label, side } = (item ?? {}) as Record<string, unknown>
    if (typeof x !== 'number' || typeof y !== 'number' || typeof label !== 'string' || typeof side !== 'string' || !sides.has(side)) {
      return null
    }
    stops.push({ x, y, label, side: side as LabelSide })
  }
  return stops
}

/** The stored `stops` value: coordinates rounded to 0.1 %, as the backend keeps them. */
export function serializeStops(stops: Stop[]): string {
  return JSON.stringify(stops.map(stop => ({ x: round(stop.x), y: round(stop.y), label: stop.label, side: stop.side })))
}

function round(value: number): number {
  return Math.round(Math.min(100, Math.max(0, value)) * 10) / 10
}

/** Routes worth drawing from list Items: broken or shorter-than-two Stop chains are skipped. */
export function toRoutes(items: ReadonlyArray<{ id: string } & Record<string, string>>): Route[] {
  const routes: Route[] = []
  for (const item of items) {
    const stops = parseStops(item.stops)
    if (!stops || stops.length < MIN_STOPS) continue
    routes.push({ id: item.id, name: item.name ?? '', color: item.color || DEFAULT_COLOR, stops })
  }
  return routes
}

/** Why a Route's Stops cannot be saved, in Russian; `null` when they can. */
export function stopsProblem(stops: Stop[]): string | null {
  if (stops.length < MIN_STOPS) return `В маршруте должно быть не меньше ${MIN_STOPS} точек.`
  if (stops.length > MAX_STOPS) return `В маршруте может быть не больше ${MAX_STOPS} точек.`
  if (stops.some(stop => stop.label.trim() === '')) return 'У каждой точки должна быть подпись.'
  if (stops.some(stop => stop.label.trim().length > LABEL_MAX_LENGTH)) return `Подпись — не длиннее ${LABEL_MAX_LENGTH} символов.`
  if (stops.some(stop => stop.label.trim().split('\n').length > 2)) return 'Подпись — не больше двух строк.'
  return null
}

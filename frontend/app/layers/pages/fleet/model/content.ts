export interface RoutePoint { name: string, x: number, y: number, labelY: number }

/**
 * Vessels shown on the fleet page. Only what is not editable lives here: the stable id (used in Content element
 * paths `vessel.<id>.*`) and the route drawing. Names, photos and specs are Content elements of the `fleet` page.
 */
export interface VesselRoute {
  id: string
  routeLabel: string
  route: RoutePoint[]
}

export const vessels: VesselRoute[] = [
  {
    id: 'sokol',
    routeLabel: 'Схема маршрута: Владивосток — Магадан — Петропавловск-К. — Анадырь',
    route: [
      { name: 'ВЛАДИВОСТОК', x: 60, y: 60, labelY: 94 },
      { name: 'МАГАДАН', x: 286.66666666666663, y: 72, labelY: 106 },
      { name: 'ПЕТРОПАВЛОВСК-К.', x: 513.3333333333333, y: 48, labelY: 82 },
      { name: 'АНАДЫРЬ', x: 740, y: 72, labelY: 106 },
    ],
  },
  {
    id: 'nagaevo',
    routeLabel: 'Схема маршрута: Владивосток — Корсаков — Магадан',
    route: [
      { name: 'ВЛАДИВОСТОК', x: 60, y: 60, labelY: 94 },
      { name: 'КОРСАКОВ', x: 400, y: 72, labelY: 106 },
      { name: 'МАГАДАН', x: 740, y: 48, labelY: 82 },
    ],
  },
]

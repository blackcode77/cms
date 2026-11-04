export { default as RouteMapCanvas } from './ui/RouteMapCanvas.vue'
export {
  colorPresets,
  DEFAULT_COLOR,
  LABEL_MAX_LENGTH,
  labelSides,
  MAX_STOPS,
  MIN_STOPS,
  parseStops,
  serializeStops,
  stopsProblem,
  toRoutes,
} from './model/stops'
export type { LabelSide, Route, Stop } from './model/stops'
export { MAP_HEIGHT, MAP_WIDTH, routeSegments } from './model/geometry'
export { fitCamera, FOCUS } from './model/camera'
export type { Camera, Viewport } from './model/camera'

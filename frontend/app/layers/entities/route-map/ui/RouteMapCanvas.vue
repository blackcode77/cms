<script setup lang="ts">
import { MAP_HEIGHT, MAP_WIDTH, routeSegments } from '../model/geometry'
import type { Route } from '../model/stops'

/**
 * The Route map picture: background, Routes as dashed curves, Stop markers and labels (fixed size, so they stay
 * readable on any width). `phase` drives the animation: `static` draws everything, `armed` hides it waiting
 * for `playing`, which reveals Routes one after another. Used on the public site and in the editor panel.
 */
const props = withDefaults(defineProps<{
  background: string
  routes: Route[]
  phase?: 'static' | 'armed' | 'playing'
  /** Dashes flow along the Routes (off for reduced motion and in the editor). */
  flowing?: boolean
  /** Routes drawn faint, e.g. the ones not being edited. */
  faded?: string[]
  /** CSS pixels per drawing unit when the picture is zoomed: keeps lines as thick on screen at any zoom. */
  scale?: number
}>(), { phase: 'static', flowing: false, faded: () => [], scale: undefined })

const STOP_APPEAR = 0.35
const LINE_SPEED = 700
const ROUTE_GAP = 0.3

const uid = useId()

/** Every Route with its Segments and the moments its Stops and Segments appear. */
const drawn = computed(() => {
  let time = 0
  return props.routes.map((route, routeIndex) => {
    const segments = routeSegments(route.stops)
    const stopDelays: number[] = []
    const lines = segments.map((segment, i) => {
      stopDelays.push(time)
      const duration = Math.min(1.6, Math.max(0.5, segment.length / LINE_SPEED))
      const line = { ...segment, delay: time + STOP_APPEAR * 0.7, duration, mask: `${uid}-${routeIndex}-${i}` }
      time = line.delay + duration
      return line
    })
    stopDelays.push(time)
    time += STOP_APPEAR + ROUTE_GAP
    return { route, lines, stopDelays, faded: props.faded.includes(route.id) }
  })
})

const s = (value: number) => `${value.toFixed(2)}s`
</script>

<template>
  <div class="route-map-canvas" :class="[`is-${phase}`, { 'is-flowing': flowing }]" :style="{ 'aspectRatio': `${MAP_WIDTH} / ${MAP_HEIGHT}`, '--u': scale ? (1 / scale).toFixed(3) : 1 }">
    <img class="route-map-canvas__bg" :src="background" alt="" draggable="false">
    <svg :viewBox="`0 0 ${MAP_WIDTH} ${MAP_HEIGHT}`" aria-hidden="true">
      <g v-for="item in drawn" :key="item.route.id" :class="{ 'is-faded': item.faded }" data-testid="route-line">
        <template v-for="line in item.lines" :key="line.mask">
          <mask :id="line.mask" maskUnits="userSpaceOnUse" x="0" y="0" :width="MAP_WIDTH" :height="MAP_HEIGHT">
            <path
              class="reveal"
              :d="line.d"
              pathLength="1"
              :style="{ animationDelay: s(line.delay), animationDuration: s(line.duration) }"
            />
          </mask>
          <path class="line" :d="line.d" :stroke="item.route.color" :mask="`url(#${line.mask})`" />
        </template>
      </g>
    </svg>
    <template v-for="item in drawn" :key="item.route.id">
      <div
        v-for="(stop, i) in item.route.stops"
        :key="i"
        class="stop"
        :class="[`stop--${stop.side}`, { 'stop--end': i === 0 || i === item.route.stops.length - 1, 'is-faded': item.faded }]"
        :style="{ left: `${stop.x}%`, top: `${stop.y}%`, '--color': item.route.color, 'animationDelay': s(item.stopDelays[i] ?? 0) }"
        data-testid="route-stop"
      >
        <span class="stop__dot" />
        <span class="stop__label">{{ stop.label }}</span>
      </div>
    </template>
    <slot />
  </div>
</template>

<style scoped>
.route-map-canvas {
  position: relative;
  overflow: hidden;
  user-select: none;
}

.route-map-canvas__bg,
.route-map-canvas svg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
}

.route-map-canvas__bg {
  object-fit: cover;
}

.reveal {
  fill: none;
  stroke: #fff;
  stroke-width: calc(40px * var(--u));
  stroke-dasharray: 1 1;
  stroke-dashoffset: 0;
}

.line {
  fill: none;
  stroke-width: calc(3.5px * var(--u));
  stroke-linecap: round;
  stroke-dasharray: calc(12px * var(--u)) calc(9px * var(--u));
}

.is-flowing .line {
  animation: route-flow 1.4s linear infinite;
}

.stop {
  position: absolute;
  width: 0;
  height: 0;
}

.stop__dot {
  position: absolute;
  left: -6px;
  top: -6px;
  width: 12px;
  height: 12px;
  border: 3px solid #fff;
  border-radius: 50%;
  background: var(--color);
  box-shadow: 0 1px 3px rgba(0, 0, 0, .3);
}

.stop--end .stop__dot {
  left: -8px;
  top: -8px;
  width: 16px;
  height: 16px;
}

.stop__label {
  position: absolute;
  width: max-content;
  max-width: 180px;
  padding: 2px 6px;
  border-radius: 4px;
  background: rgba(255, 255, 255, .88);
  color: #231f20;
  font: 700 13px/1.25 "Roboto Condensed", "Arial Narrow", Arial, sans-serif;
  white-space: pre-line;
  box-shadow: 0 1px 2px rgba(0, 0, 0, .12);
}

.stop--right .stop__label { left: 14px; transform: translateY(-50%); }
.stop--left .stop__label { right: 14px; transform: translateY(-50%); text-align: right; }
.stop--top .stop__label { bottom: 14px; transform: translateX(-50%); text-align: center; }
.stop--bottom .stop__label { top: 14px; transform: translateX(-50%); text-align: center; }

.is-faded {
  opacity: .3;
}

.is-armed .reveal,
.is-playing .reveal {
  stroke-dashoffset: 1;
}

.is-playing .reveal {
  animation-name: route-reveal;
  animation-timing-function: ease-in-out;
  animation-fill-mode: forwards;
}

.is-armed .stop,
.is-playing .stop {
  opacity: 0;
}

.is-playing .stop {
  animation: route-stop-in .35s ease-out forwards;
}

@keyframes route-reveal {
  to { stroke-dashoffset: 0; }
}

@keyframes route-stop-in {
  from { opacity: 0; transform: scale(.6); }
  to { opacity: 1; transform: scale(1); }
}

@keyframes route-flow {
  to { stroke-dashoffset: calc(-21px * var(--u)); }
}
</style>

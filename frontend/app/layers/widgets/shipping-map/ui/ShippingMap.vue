<script setup lang="ts">
import { CmsText, useCmsElement, useCmsList } from '@entities/page-content'
import { fitCamera, MAP_WIDTH, RouteMapCanvas, toRoutes } from '@entities/route-map'

/**
 * Route map block: heading, lead, the map with Routes and a legend of named Routes. Reads the Content elements
 * `<prefix>.title`, `.lead`, `.map` (background image) and `.items` (Routes list) of `page`. Visitors see
 * the final picture in SSR HTML; in the browser it is hidden until the block scrolls into view and then plays
 * once (static for reduced motion). The map is zoomed to the Far East and zooms out just enough to fit every Stop.
 */
const props = withDefaults(defineProps<{ page: string, prefix?: string }>(), { prefix: 'routes' })

const list = useCmsList(() => props.page, () => `${props.prefix}.items`)
const background = useCmsElement(() => props.page, () => `${props.prefix}.map`)

const routes = computed(() => toRoutes(list.items.value))
const named = computed(() => routes.value.filter(route => route.name.trim() !== ''))
const visible = computed(() => list.visible.value)

const root = ref<HTMLElement | null>(null)
const phase = ref<'static' | 'armed' | 'playing'>('static')
const flowing = ref(false)
let observer: IntersectionObserver | undefined

/** Viewport size and the part covered by the text card; SSR assumes a desktop block. */
const card = ref<HTMLElement | null>(null)
const size = ref({ width: 1440, height: 600, cardRight: 480 })
let resize: ResizeObserver | undefined
/** Camera moves animate only after the first measurement, so the page does not fly from the SSR guess. */
const settled = ref(false)

function measure() {
  const box = root.value?.getBoundingClientRect()
  if (!box) return
  const overlay = card.value && getComputedStyle(card.value).position === 'absolute'
  const cardRight = overlay ? card.value!.getBoundingClientRect().right - box.left : 0
  size.value = { width: box.width, height: box.height, cardRight }
}

const camera = computed(() => {
  const { width, height, cardRight } = size.value
  const side = Math.min(120, width * 0.12)
  return fitCamera(routes.value.flatMap(route => route.stops), {
    width,
    height,
    padding: { top: 40, bottom: 40, right: side, left: cardRight ? cardRight + side : side },
  })
})

onMounted(() => {
  measure()
  requestAnimationFrame(() => {
    settled.value = true
  })
  if (typeof ResizeObserver !== 'undefined' && root.value) {
    resize = new ResizeObserver(measure)
    resize.observe(root.value)
  }
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || typeof IntersectionObserver === 'undefined') return
  flowing.value = true
  phase.value = 'armed'
  observer = new IntersectionObserver((entries) => {
    if (entries.some(entry => entry.isIntersecting)) {
      phase.value = 'playing'
      observer?.disconnect()
    }
  }, { threshold: 0.35 })
  if (root.value) observer.observe(root.value)
})

onBeforeUnmount(() => {
  observer?.disconnect()
  resize?.disconnect()
})

// In Edit mode the result of an edit must be seen at once, not after replaying.
watch(list.editing, (on) => {
  if (on && phase.value === 'armed') phase.value = 'static'
})
</script>

<template>
  <section v-if="visible" class="shipping" aria-labelledby="routes-title">
    <div ref="root" class="shipping__viewport">
      <div
        class="shipping__map"
        :class="{ 'is-settled': settled }"
        :style="{ width: `${MAP_WIDTH * camera.scale}px`, transform: `translate(${camera.x}px, ${camera.y}px)` }"
        data-testid="route-map-camera"
        v-bind="list.editAttrs.value"
      >
        <RouteMapCanvas
          :scale="camera.scale"
          :background="background.element.value?.value ?? ''"
          :routes="routes"
          :phase="list.editing.value ? 'static' : phase"
          :flowing="flowing"
          role="img"
          :aria-label="routes.map(route => route.stops.map(stop => stop.label.replace('\n', ' ')).join(' → ')).join('; ')"
        />
      </div>
      <button
        v-if="list.editing.value"
        type="button"
        class="shipping__bg-edit"
        v-bind="background.editAttrs.value"
        data-testid="route-map-background"
      >
        Фон карты
      </button>
    </div>
    <div ref="card" class="shipping__card">
      <div class="section-head">
        <CmsText id="routes-title" as="h2" :page="page" :path="`${prefix}.title`" />
        <CmsText as="p" :page="page" :path="`${prefix}.lead`" />
      </div>
      <ul v-if="named.length" class="shipping__legend" data-testid="route-legend">
        <li v-for="route in named" :key="route.id">
          <i :style="{ borderTopColor: route.color }" />{{ route.name }}
        </li>
      </ul>
    </div>
  </section>
</template>

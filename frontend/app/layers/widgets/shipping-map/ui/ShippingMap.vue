<script setup lang="ts">
import { CmsText, useCmsElement, useCmsList } from '@entities/page-content'
import { RouteMapCanvas, toRoutes } from '@entities/route-map'

/**
 * Route map block: heading, lead, the map with Routes and a legend of named Routes. Reads the Content elements
 * `<prefix>.title`, `.lead`, `.map` (background image) and `.items` (Routes list) of `page`. Visitors see
 * the final picture in SSR HTML; in the browser it is hidden until the block scrolls into view and then plays
 * once (static for reduced motion).
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

onMounted(() => {
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

onBeforeUnmount(() => observer?.disconnect())

// In Edit mode the result of an edit must be seen at once, not after replaying.
watch(list.editing, (on) => {
  if (on && phase.value === 'armed') phase.value = 'static'
})
</script>

<template>
  <section v-if="visible" class="section--tint" aria-labelledby="routes-title">
    <div class="container">
      <div class="shipping">
        <div>
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
        <div ref="root" class="shipping__scroll">
          <div class="shipping__map" v-bind="list.editAttrs.value">
            <RouteMapCanvas
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
      </div>
    </div>
  </section>
</template>

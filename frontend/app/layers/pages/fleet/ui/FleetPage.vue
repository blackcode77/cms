<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { CmsImage, CmsList, CmsText, defineContentSchema, useCmsElement, usePageContent } from '@entities/page-content'
import { imageSizes } from '@shared/config'
import { usePageSeo } from '@shared/seo'
import { AppIcon } from '@shared/ui'
import { vessels } from '../model/content'

const PAGE = 'fleet'
const specFields = { label: { type: 'string', label: 'Параметр' }, value: { type: 'string', label: 'Значение' } } as const

defineContentSchema(PAGE, {
  'seo.title': { type: 'string', default: 'Флот — Гарант Интермодал', label: 'SEO: заголовок страницы' },
  'seo.description': { type: 'string', default: 'Суда группы Гарант Интермодал: МПП типа «Сокол» и контейнеровоз «Гарант Нагаево», ТТХ, маршруты, слежение.', label: 'SEO: описание' },
  'hero.image': { type: 'image', default: '/img/ship-wide.jpg', label: 'Первый экран: фон' },
  'hero.title': { type: 'string', default: 'Флот', label: 'Первый экран: заголовок' },
  'hero.lead': { type: 'string', default: 'Собственные суда для каботажных линий и тяжёлых грузов.', label: 'Первый экран: текст' },
  'vessels.title': { type: 'string', default: 'Суда группы', label: 'Суда: заголовок' },
  'vessels.lead': { type: 'string', default: 'Выберите судно, чтобы увидеть технические данные и карту маршрута.', label: 'Суда: подзаголовок' },
  'vessel.sokol.tab': { type: 'string', default: 'МПП «Сокол» (два судна)', label: '«Сокол»: кнопка' },
  'vessel.sokol.title': { type: 'string', default: 'МПП «Сокол» (два судна)', label: '«Сокол»: название' },
  'vessel.sokol.image': { type: 'image', default: '/img/cabotage.jpg', label: '«Сокол»: фото' },
  'vessel.sokol.specs': {
    type: 'list',
    label: '«Сокол»: характеристики',
    fields: specFields,
    default: [
      { label: 'Тип', value: 'Многоцелевое судно (МПП), проект «Сокол»' },
      { label: 'Год постройки', value: '2014' },
      { label: 'Оператор', value: 'ООО «Восточное судоходное товарищество»' },
      { label: 'Длина / ширина', value: '—' },
      { label: 'Дедвейт', value: '—' },
      { label: 'Вместимость', value: '—' },
      { label: 'Грузы', value: 'Контейнеры, генеральные, проектные, тяжеловесные' },
    ],
  },
  'vessel.nagaevo.tab': { type: 'string', default: 'т/х «Гарант Нагаево»', label: '«Гарант Нагаево»: кнопка' },
  'vessel.nagaevo.title': { type: 'string', default: 'т/х «Гарант Нагаево»', label: '«Гарант Нагаево»: название' },
  'vessel.nagaevo.image': { type: 'image', default: '/img/garant-nagaevo.jpg', label: '«Гарант Нагаево»: фото' },
  'vessel.nagaevo.specs': {
    type: 'list',
    label: '«Гарант Нагаево»: характеристики',
    fields: specFields,
    default: [
      { label: 'Тип', value: 'Контейнеровоз' },
      { label: 'Условия приобретения', value: 'Бербоут-чартер, 2015 г.' },
      { label: 'Оператор', value: 'Каботажные линии Гарант Интермодал' },
      { label: 'Длина / ширина', value: '—' },
      { label: 'Дедвейт', value: '—' },
      { label: 'Вместимость, TEU', value: '—' },
      { label: 'Грузы', value: 'Контейнерные и генеральные грузы' },
    ],
  },
  'route.title': { type: 'string', default: 'Маршрут', label: 'Маршрут: заголовок' },
  'tracking.title': { type: 'string', default: 'Слежение за судами', label: 'Слежение: заголовок' },
  'tracking.text': { type: 'text', default: 'Планируем подключить онлайн-карту движения теплоходов (AIS), чтобы клиент видел положение судна в реальном времени.', label: 'Слежение: текст' },
  'tracking.vessel': { type: 'string', default: '«Гарант Нагаево»', label: 'Слежение: судно' },
  'tracking.status': { type: 'string', default: 'в рейсе', label: 'Слежение: статус' },
  'tracking.route': { type: 'string', default: 'Владивосток → Магадан', label: 'Слежение: маршрут' },
  'lines.title': { type: 'string', default: 'Другие линии', label: 'Другие линии: заголовок' },
  'lines.card.title': { type: 'string', default: 'UNITY LINE', label: 'Другие линии: название' },
  'lines.card.text': { type: 'text', default: 'На линии работают суда NEWSTRIVER и HE XING 7. Расписания — в разделе «Расписания и тарифы».', label: 'Другие линии: текст' },
  'lines.card.link': { type: 'string', default: 'К расписаниям →', label: 'Другие линии: текст ссылки' },
  'lines.image': { type: 'image', default: '/img/ship-night.jpg', label: 'Другие линии: картинка' },
})

/** The vessel name doubles as its photo's alt text. */
const { resolve } = usePageContent()
const vesselTitle = (id: string) => resolve(PAGE, `vessel.${id}.title`)?.value ?? ''

const seoTitle = useCmsElement(() => PAGE, () => 'seo.title')
const seoDescription = useCmsElement(() => PAGE, () => 'seo.description')

usePageSeo({
  title: () => seoTitle.element.value?.value ?? '',
  description: () => seoDescription.element.value?.value ?? '',
  path: '/fleet',
  image: '/img/ship-wide.jpg',
  jsonLd: {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    'name': 'ООО «Гарант Интермодал»',
    'url': 'https://garant-intermodal.ru/',
    'logo': 'https://garant-intermodal.ru/img/logo.png',
    'foundingDate': '2014',
    'telephone': '+7 423 230-20-20',
    'email': 'office@garant-intermodal.com',
    'address': {
      '@type': 'PostalAddress',
      'addressLocality': 'Владивосток',
      'streetAddress': 'ул. Запорожская, 77, офис 640',
      'addressCountry': 'RU',
    },
  },
})


const activeVessel = ref(vessels[0]!.id)

function routePath(points: { x: number, y: number }[]) {
  return points.map((p, i) => `${i === 0 ? 'M' : 'L'}${p.x} ${p.y}`).join(' ')
}
</script>

<template>
  <div>
    <PageHero
      :cms-page="PAGE"
      :crumbs="[{ label: 'Флот' }]"
    />
    <section>
      <div class="container">
        <div class="section-head">
          <CmsText as="h2" :page="PAGE" path="vessels.title" />
          <CmsText as="p" :page="PAGE" path="vessels.lead" />
        </div>
        <div style="margin-bottom:24px">
          <button
            v-for="vessel in vessels"
            :key="vessel.id"
            type="button"
            class="btn btn--ghost"
            style="margin-right:8px"
            :aria-pressed="activeVessel === vessel.id"
            @click="activeVessel = vessel.id"
          >
            <CmsText :page="PAGE" :path="`vessel.${vessel.id}.tab`" />
          </button>
        </div>
        <div v-for="vessel in vessels" v-show="activeVessel === vessel.id" :key="vessel.id">
          <div class="grid grid--2" style="align-items:start">
            <figure>
              <CmsImage
                :page="PAGE"
                :path="`vessel.${vessel.id}.image`"
                :alt="vesselTitle(vessel.id)"
                width="1000"
                height="750"
                :sizes="imageSizes.half"
                format="webp"
                loading="lazy"
                style="aspect-ratio:4/3"
              />
            </figure>
            <div>
              <CmsText as="h3" :page="PAGE" :path="`vessel.${vessel.id}.title`" />
              <div class="table-wrap">
                <table class="spec">
                  <CmsList v-slot="{ item }" as="tbody" :page="PAGE" :path="`vessel.${vessel.id}.specs`">
                    <tr>
                      <th scope="row">{{ item.label }}</th>
                      <td>{{ item.value }}</td>
                    </tr>
                  </CmsList>
                </table>
              </div>
            </div>
          </div>
          <CmsText as="h4" style="margin-top:28px" :page="PAGE" path="route.title" />
          <svg
            viewBox="0 0 800 130"
            role="img"
            :aria-label="vessel.routeLabel"
            style="width:100%;height:auto;background:var(--green-tint);border:1px solid var(--line);border-radius:6px"
          >
            <path :d="routePath(vessel.route)" fill="none" stroke="#3A7B34" stroke-width="3" stroke-dasharray="8 6" />
            <template v-for="point in vessel.route" :key="point.name">
              <circle :cx="point.x" :cy="point.y" r="9" fill="#3A7B34" stroke="#fff" stroke-width="3" />
              <text
                :x="point.x"
                :y="point.labelY"
                text-anchor="middle"
                font-family="Roboto Condensed,Arial Narrow,sans-serif"
                font-weight="700"
                font-size="14"
              >{{ point.name }}</text>
            </template>
          </svg>
        </div>
      </div>
    </section>
    <section class="section--tint">
      <div class="container">
        <div class="split">
          <div>
            <div class="section-head"><CmsText as="h2" :page="PAGE" path="tracking.title" /></div>
            <CmsText as="p" :page="PAGE" path="tracking.text" />
          </div>
          <div class="tracker">
            <span class="dot" />
            <div>
              <CmsText as="b" :page="PAGE" path="tracking.vessel" /> — <CmsText :page="PAGE" path="tracking.status" /><br>
              <CmsText style="color:var(--grey);font-size:.9rem" :page="PAGE" path="tracking.route" />
            </div>
          </div>
        </div>
      </div>
    </section>
    <section>
      <div class="container">
        <div class="section-head"><CmsText as="h2" :page="PAGE" path="lines.title" /></div>
        <div class="grid grid--2">
          <article class="card card--plain">
            <span class="icon" aria-hidden="true">
              <AppIcon name="ship" />
            </span>
            <CmsText as="h3" :page="PAGE" path="lines.card.title" />
            <CmsText as="p" :page="PAGE" path="lines.card.text" />
            <NuxtLink to="/schedules"><CmsText :page="PAGE" path="lines.card.link" /></NuxtLink>
          </article>
          <figure>
            <CmsImage
              :page="PAGE"
              path="lines.image"
              alt="NEWSTRIVER в порту ночью"
              width="1400"
              height="1050"
              :sizes="imageSizes.half"
              format="webp"
              loading="lazy"
              style="aspect-ratio:16/9"
            />
          </figure>
        </div>
      </div>
    </section>
    <CtaBanner />
  </div>
</template>

<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { imageSizes } from '@shared/config'
import { usePageSeo } from '@shared/seo'
import { SvgIcon } from '@shared/ui'
import { vessels } from '../model/content'

const description = 'Суда группы Гарант Интермодал: МПП типа «Сокол» и контейнеровоз «Гарант Нагаево», ТТХ, маршруты, слежение.'

usePageSeo({
  title: 'Флот — Гарант Интермодал',
  description,
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

const shipIcon = '<path d="M3 17l2 3h14l2-3M5 17l-1-6h16l-1 6M8 11V6h8v5M12 3v3"/>'

const activeVessel = ref(vessels[0]!.id)

function routePath(points: { x: number, y: number }[]) {
  return points.map((p, i) => `${i === 0 ? 'M' : 'L'}${p.x} ${p.y}`).join(' ')
}
</script>

<template>
  <div>
    <PageHero
      title="Флот"
      lead="Собственные суда для каботажных линий и тяжёлых грузов."
      image="/img/ship-wide.jpg"
      :crumbs="[{ label: 'Флот' }]"
    />
    <section>
      <div class="container">
        <div class="section-head">
          <h2>Суда группы</h2>
          <p>Выберите судно, чтобы увидеть технические данные и карту маршрута.</p>
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
            {{ vessel.tab }}
          </button>
        </div>
        <div v-for="vessel in vessels" v-show="activeVessel === vessel.id" :key="vessel.id">
          <div class="grid grid--2" style="align-items:start">
            <figure>
              <NuxtImg
                :src="vessel.image"
                :alt="vessel.title"
                :width="vessel.width"
                :height="vessel.height"
                :sizes="imageSizes.half"
                format="webp"
                loading="lazy"
                style="aspect-ratio:4/3"
              />
            </figure>
            <div>
              <h3>{{ vessel.title }}</h3>
              <div class="table-wrap">
                <table class="spec">
                  <tbody>
                    <tr v-for="row in vessel.specs" :key="row.label">
                      <th scope="row">{{ row.label }}</th>
                      <td>{{ row.value }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <h4 style="margin-top:28px">Маршрут</h4>
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
            <div class="section-head"><h2>Слежение за судами</h2></div>
            <p>Планируем подключить онлайн-карту движения теплоходов (AIS), чтобы клиент видел положение судна в реальном времени.</p>
          </div>
          <div class="tracker">
            <span class="dot" />
            <div>
              <b>«Гарант Нагаево»</b> — в рейсе<br>
              <span style="color:var(--grey);font-size:.9rem">Владивосток → Магадан</span>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section>
      <div class="container">
        <div class="section-head"><h2>Другие линии</h2></div>
        <div class="grid grid--2">
          <article class="card card--plain">
            <span class="icon" aria-hidden="true">
              <SvgIcon :inner="shipIcon" />
            </span>
            <h3>UNITY LINE</h3>
            <p>На линии работают суда NEWSTRIVER и HE XING 7. Расписания — в разделе «Расписания и тарифы».</p>
            <NuxtLink to="/schedules">К расписаниям →</NuxtLink>
          </article>
          <figure>
            <NuxtImg
              src="/img/ship-night.jpg"
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

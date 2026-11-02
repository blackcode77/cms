<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { usePageSeo } from '@shared/seo'
import { SvgIcon } from '@shared/ui'
import { bookingSteps, docFilters, docGroups } from '../model/content'
import type { DocFilter } from '../model/content'

const title = 'Расписания и тарифы — Гарант Интермодал'
const description = 'Расписания движения флота, публичные тарифы, локальные сборы в портах и инструкция по букингу для новых клиентов Гарант Интермодал.'

usePageSeo({
  title,
  description,
  path: '/schedules',
  image: '/img/ship-night.jpg',
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

const activeFilter = ref<DocFilter>('all')
</script>

<template>
  <div>
    <PageHero
      title="Расписания и тарифы"
      lead="Актуальные расписания, публичные тарифы и порядок бронирования."
      image="/img/ship-night.jpg"
      :crumbs="[{ label: 'Главная', to: '/' }, { label: 'Расписания и тарифы' }]"
    />
    <section>
      <div class="container">
        <div class="alert" role="status">
          <span class="icon" aria-hidden="true"><SvgIcon inner="<path d='M6 16V11a6 6 0 0112 0v5l2 2H4zM10 21h4'/>" /></span>
          <div><b>Уведомление об изменении расписания</b>С 15.11.2026 заход судна HE XING 7 в порт Владивосток переносится на 2 дня. Подробности — в расписании.</div>
        </div>
        <div class="section-head"><h2>Документы для скачивания</h2></div>
        <div class="filters" role="group" aria-label="Фильтр документов">
          <button
            v-for="filter in docFilters"
            :key="filter.value"
            type="button"
            :aria-pressed="activeFilter === filter.value"
            @click="activeFilter = filter.value"
          >
            {{ filter.label }}
          </button>
        </div>
        <div
          v-for="group in docGroups"
          v-show="activeFilter === 'all' || activeFilter === group.category"
          :key="group.category"
        >
          <h3 style="margin-top:32px">{{ group.title }}</h3>
          <ul class="docs">
            <li v-for="doc in group.items" :key="doc.title">
              <span class="ext">{{ doc.ext }}</span>
              <div><b>{{ doc.title }}</b><small>{{ doc.note }}</small></div>
              <span class="size">{{ doc.size }}</span>
              <a class="btn btn--ghost" href="#" style="padding:10px 16px">Скачать</a>
            </li>
          </ul>
        </div>
      </div>
    </section>
    <section id="booking" class="section--tint">
      <div class="container">
        <div class="section-head"><h2>Как забронировать: инструкция для новых клиентов</h2></div>
        <ol class="steps">
          <li v-for="step in bookingSteps" :key="step.title"><b>{{ step.title }}</b><p>{{ step.text }}</p></li>
        </ol>
        <p style="margin-top:20px"><NuxtLink class="btn" to="/contacts#request">Отправить заявку</NuxtLink></p>
      </div>
    </section>
    <CtaBanner />
  </div>
</template>

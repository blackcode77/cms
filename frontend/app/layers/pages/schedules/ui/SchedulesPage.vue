<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { CmsList, CmsText, defineContentSchema, useCmsElement } from '@entities/page-content'
import { usePageSeo } from '@shared/seo'
import { AppIcon } from '@shared/ui'
import { docFilters, docGroups } from '../model/content'
import type { DocFilter } from '../model/content'

const PAGE = 'schedules'

defineContentSchema(PAGE, {
  'seo.title': { type: 'string', default: 'Расписания и тарифы — Гарант Интермодал', label: 'SEO: заголовок страницы' },
  'seo.description': { type: 'string', default: 'Расписания движения флота, публичные тарифы, локальные сборы в портах и инструкция по букингу для новых клиентов Гарант Интермодал.', label: 'SEO: описание' },
  'hero.image': { type: 'image', default: '/img/ship-night.jpg', label: 'Первый экран: фон' },
  'hero.title': { type: 'string', default: 'Расписания и тарифы', label: 'Первый экран: заголовок' },
  'hero.lead': { type: 'string', default: 'Актуальные расписания, публичные тарифы и порядок бронирования.', label: 'Первый экран: текст' },
  'notice.title': { type: 'string', default: 'Уведомление об изменении расписания', label: 'Уведомление: заголовок' },
  'notice.text': { type: 'text', default: 'С 15.11.2026 заход судна HE XING 7 в порт Владивосток переносится на 2 дня. Подробности — в расписании.', label: 'Уведомление: текст (выключите, чтобы скрыть уведомление)' },
  'docs.title': { type: 'string', default: 'Документы для скачивания', label: 'Документы: заголовок' },
  'booking.title': { type: 'string', default: 'Как забронировать: инструкция для новых клиентов', label: 'Бронирование: заголовок' },
  'booking.steps': {
    type: 'list',
    label: 'Бронирование: шаги',
    fields: { title: { type: 'string', label: 'Шаг' }, text: { type: 'text', label: 'Текст' } },
    default: [
      { title: 'Регистрация', text: 'Свяжитесь с менеджером и передайте реквизиты для заключения договора.' },
      { title: 'Выберите тариф', text: 'Найдите ставку на маршрут в разделе «Публичные тарифы».' },
      { title: 'Заявка на букинг', text: 'Отправьте заявку: груз, количество и тип контейнеров, порты, даты.' },
      { title: 'Подтверждение', text: 'Получите подтверждение, номер букинга и инструкции по подаче контейнера.' },
      { title: 'Документы и отгрузка', text: 'Передайте документы на груз, отследите статус в личном ежедневном отчёте.' },
    ],
  },
  'booking.button': { type: 'string', default: 'Отправить заявку', label: 'Бронирование: кнопка' },
})

const seoTitle = useCmsElement(() => PAGE, () => 'seo.title')
const seoDescription = useCmsElement(() => PAGE, () => 'seo.description')
// The notice block exists only while its text is switched on (or in Edit mode, dimmed).
const notice = useCmsElement(() => PAGE, () => 'notice.text')

usePageSeo({
  title: () => seoTitle.element.value?.value ?? '',
  description: () => seoDescription.element.value?.value ?? '',
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
      :cms-page="PAGE"
      :crumbs="[{ label: 'Главная', to: '/' }, { label: 'Расписания и тарифы' }]"
    />
    <section>
      <div class="container">
        <div v-if="notice.visible.value" class="alert" role="status">
          <span class="icon" aria-hidden="true"><AppIcon name="bell" /></span>
          <div><CmsText as="b" :page="PAGE" path="notice.title" /><CmsText :page="PAGE" path="notice.text" /></div>
        </div>
        <div class="section-head"><CmsText as="h2" :page="PAGE" path="docs.title" /></div>
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
        <div class="section-head"><CmsText as="h2" :page="PAGE" path="booking.title" /></div>
        <CmsList v-slot="{ item }" as="ol" class="steps" :page="PAGE" path="booking.steps">
          <li><b>{{ item.title }}</b><p>{{ item.text }}</p></li>
        </CmsList>
        <p style="margin-top:20px"><NuxtLink class="btn" to="/contacts#request"><CmsText :page="PAGE" path="booking.button" /></NuxtLink></p>
      </div>
    </section>
    <CtaBanner />
  </div>
</template>

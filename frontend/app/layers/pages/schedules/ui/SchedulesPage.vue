<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { CmsList, CmsText, defineContentSchema, documentFormat, useCmsElement } from '@entities/page-content'
import { usePageSeo } from '@shared/seo'
import { AppIcon } from '@shared/ui'
import { docCategories, docFilters } from '../model/content'
import type { DocFilter } from '../model/content'

const PAGE = 'schedules'

/** A Document: the size is filled in by the panel on upload, the format comes from the file name. */
const documentFields = {
  title: { type: 'string', label: 'Название' },
  note: { type: 'string', label: 'Примечание' },
  file: { type: 'file', label: 'Файл', sizeField: 'size' },
  size: { type: 'string', hidden: true },
} as const

defineContentSchema(PAGE, {
  'seo.title': { type: 'string', default: 'Расписания и тарифы — Гарант Интермодал', label: 'SEO: заголовок страницы' },
  'seo.description': { type: 'string', default: 'Расписания движения флота, публичные тарифы, локальные сборы в портах и инструкция по букингу для новых клиентов Гарант Интермодал.', label: 'SEO: описание' },
  'hero.image': { type: 'image', default: '/img/ship-night.jpg', label: 'Первый экран: фон' },
  'hero.title': { type: 'string', default: 'Расписания и тарифы', label: 'Первый экран: заголовок' },
  'hero.lead': { type: 'string', default: 'Актуальные расписания, публичные тарифы и порядок бронирования.', label: 'Первый экран: текст' },
  'notice.title': { type: 'string', default: 'Уведомление об изменении расписания', label: 'Уведомление: заголовок' },
  'notice.text': { type: 'text', default: 'С 15.11.2026 заход судна HE XING 7 в порт Владивосток переносится на 2 дня. Подробности — в расписании.', label: 'Уведомление: текст (выключите, чтобы скрыть уведомление)' },
  'docs.title': { type: 'string', default: 'Документы для скачивания', label: 'Документы: заголовок' },
  'docs.sched.title': { type: 'string', default: 'Расписания движения флота', label: 'Документы, Расписания движения флота: заголовок' },
  'docs.sched.items': {
    type: 'list',
    label: 'Документы, Расписания движения флота: список',
    fields: documentFields,
    default: [
      { title: 'Расписание NEWSTRIVER · UNITY LINE', note: 'Ноябрь 2026' },
      { title: 'Расписание HE XING 7 · UNITY LINE', note: 'Ноябрь 2026' },
      { title: 'Каботажные линии: Владивосток — Магадан / Корсаков', note: 'IV квартал 2026' },
    ],
  },
  'docs.tariff.title': { type: 'string', default: 'Публичные тарифы', label: 'Документы, Публичные тарифы: заголовок' },
  'docs.tariff.items': {
    type: 'list',
    label: 'Документы, Публичные тарифы: список',
    fields: documentFields,
    default: [
      { title: 'SOC-тарифы: Тайцан, Нинбо, Шанхай (местная доставка)', note: 'Действует с 01.11.2026' },
      { title: 'SOC-ставки на импорт', note: 'Действует с 01.11.2026' },
      { title: 'SOC: Китай → станции Врангель', note: 'Действует с 01.11.2026' },
      { title: 'Каботаж: тарифы на контейнерные перевозки', note: '2026' },
    ],
  },
  'docs.local.title': { type: 'string', default: 'Локальные сборы в портах погрузки', label: 'Документы, Локальные сборы в портах погрузки: заголовок' },
  'docs.local.items': {
    type: 'list',
    label: 'Документы, Локальные сборы в портах погрузки: список',
    fields: documentFields,
    default: [
      { title: 'Локальные сборы: порты Китая', note: 'Актуально на 01.11.2026' },
      { title: 'Локальные сборы: порты Кореи и Японии', note: 'Актуально на 01.11.2026' },
    ],
  },
  'docs.guide.title': { type: 'string', default: 'Инструкции', label: 'Документы, Инструкции: заголовок' },
  'docs.guide.items': {
    type: 'list',
    label: 'Документы, Инструкции: список',
    fields: documentFields,
    default: [
      { title: 'Contact Guide', note: 'Контакты подразделений' },
      { title: 'Памятка по перевозке опасных грузов', note: '2026' },
    ],
  },
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
          v-for="category in docCategories"
          v-show="activeFilter === 'all' || activeFilter === category"
          :key="category"
        >
          <CmsText as="h3" style="margin-top:32px" :page="PAGE" :path="`docs.${category}.title`" />
          <CmsList v-slot="{ item }" as="ul" class="docs" :page="PAGE" :path="`docs.${category}.items`">
            <li>
              <span class="ext">{{ documentFormat(item.file) }}</span>
              <div><b>{{ item.title }}</b><small>{{ item.note }}</small></div>
              <span class="size">{{ item.file ? item.size : '' }}</span>
              <a v-if="item.file" class="btn btn--ghost" :href="item.file" download style="padding:10px 16px">Скачать</a>
            </li>
          </CmsList>
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

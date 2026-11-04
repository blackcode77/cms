<script setup lang="ts">
import { PageHero } from '@widgets/page-hero'
import { CmsImage, CmsList, CmsText, defineContentSchema, useCmsElement } from '@entities/page-content'
import { imageSizes } from '@shared/config'
import { usePageSeo } from '@shared/seo'
import { subnav } from '../model/content'

/** Only one service has its own page so far; its content lives under this page key. */
const PAGE = 'service-multimodal'

defineContentSchema(PAGE, {
  'seo.title': { type: 'string', default: 'Мультимодальные перевозки — Гарант Интермодал', label: 'SEO: заголовок страницы' },
  'seo.description': { type: 'string', default: 'Мультимодальные перевозки морем, по железной дороге и автотранспортом по единой ставке: Китай, Корея, Япония — Россия.', label: 'SEO: описание' },
  'hero.image': { type: 'image', default: '/img/train.jpg', label: 'Первый экран: фон' },
  'hero.title': { type: 'string', default: 'Мультимодальные перевозки', label: 'Первый экран: заголовок' },
  'hero.lead': { type: 'string', default: 'Сквозная перевозка груза морем, по железной дороге и автотранспортом по единой ставке и под единым контролем.', label: 'Первый экран: текст' },
  'about.title': { type: 'string', default: 'Описание', label: 'Описание: заголовок' },
  'about.text': { type: 'text', default: 'Мультимодальная перевозка — организация доставки «от двери до двери» несколькими видами транспорта. Вы получаете один договор, одну ставку и одного ответственного менеджера вместо нескольких подрядчиков.', label: 'Описание: текст' },
  'included.title': { type: 'string', default: 'Что входит', label: 'Что входит: заголовок' },
  'included.items': {
    type: 'list',
    label: 'Что входит: список',
    fields: { text: { type: 'string', label: 'Пункт' } },
    default: [
      { text: 'Морская перевозка из Китая, Кореи, Японии во Владивосток, Восточный, Находку' },
      { text: 'Терминальные и внутрипортовые услуги, таможенное оформление' },
      { text: 'Железнодорожная доставка: Москва, Новосибирск, Екатеринбург' },
      { text: 'Автодоставка «последней мили» собственным транспортом' },
      { text: 'Предоставление контейнеров (собственных и арендованных)' },
      { text: 'Ежедневное отслеживание груза' },
    ],
  },
  'figure.image': { type: 'image', default: '/img/train.jpg', label: 'Иллюстрация: картинка' },
  'figure.caption': { type: 'string', default: 'Контейнерный поезд на маршруте из порта вглубь России', label: 'Иллюстрация: подпись' },
  'order.title': { type: 'string', default: 'Как заказать', label: 'Как заказать: заголовок' },
  'order.steps': {
    type: 'list',
    label: 'Как заказать: шаги',
    fields: { title: { type: 'string', label: 'Шаг' }, text: { type: 'text', label: 'Текст' } },
    default: [
      { title: 'Заявка', text: 'Маршрут, груз, сроки.' },
      { title: 'Расчёт', text: 'Ставка за рабочий день.' },
      { title: 'Договор', text: 'Подписание условий.' },
      { title: 'Перевозка', text: 'Организация и контроль.' },
    ],
  },
  'directions.title': { type: 'string', default: 'Типовые направления', label: 'Направления: заголовок' },
  'directions.rows': {
    type: 'list',
    label: 'Направления: строки таблицы',
    fields: {
      from: { type: 'string', label: 'Откуда' },
      to: { type: 'string', label: 'Куда' },
      transport: { type: 'string', label: 'Транспорт' },
    },
    default: [
      { from: 'Китай, Корея, Япония', to: 'Москва', transport: 'Море + ЖД' },
      { from: 'Китай', to: 'Новосибирск, Екатеринбург', transport: 'Море + ЖД' },
      { from: 'Владивосток', to: 'Магадан, Корсаков, Камчатка', transport: 'Море (каботаж)' },
    ],
  },
  'directions.note': { type: 'string', default: 'Таблица иллюстративная, актуальные направления — в тарифах.', label: 'Направления: примечание' },
  'aside.title': { type: 'string', default: 'Нужен расчёт?', label: 'Боковая колонка: заголовок' },
  'aside.text': { type: 'string', default: 'Ответим в рабочее время.', label: 'Боковая колонка: текст' },
  'aside.button': { type: 'string', default: 'Запросить расчёт', label: 'Боковая колонка: кнопка' },
})

const route = useRoute()

if (route.params.slug !== 'multimodal') {
  throw createError({ statusCode: 404, statusMessage: 'Page Not Found' })
}

const seoTitle = useCmsElement(() => PAGE, () => 'seo.title')
const seoDescription = useCmsElement(() => PAGE, () => 'seo.description')

usePageSeo({
  title: () => seoTitle.element.value?.value ?? '',
  description: () => seoDescription.element.value?.value ?? '',
  path: '/services/multimodal',
  image: '/img/train.jpg',
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
</script>

<template>
  <div>
    <PageHero
      :cms-page="PAGE"
      :crumbs="[{ label: 'Услуги', to: '/services' }, { label: 'Мультимодальные перевозки' }]"
    />
    <section>
      <div class="container">
        <div class="layout">
          <article class="prose">
            <CmsText as="h2" :page="PAGE" path="about.title" />
            <CmsText as="p" :page="PAGE" path="about.text" />
            <CmsText as="h2" :page="PAGE" path="included.title" />
            <CmsList v-slot="{ item }" as="ul" class="checklist" :page="PAGE" path="included.items">
              <li>{{ item.text }}</li>
            </CmsList>
            <figure style="margin:28px 0">
              <CmsImage
                :page="PAGE"
                path="figure.image"
                alt="Контейнерный поезд"
                width="563"
                height="398"
                :sizes="imageSizes.half"
                format="webp"
                loading="lazy"
              />
              <CmsText as="figcaption" :page="PAGE" path="figure.caption" />
            </figure>
            <CmsText as="h2" :page="PAGE" path="order.title" />
            <CmsList v-slot="{ item }" as="ol" class="steps" :page="PAGE" path="order.steps">
              <li>
                <b>{{ item.title }}</b>
                <p>{{ item.text }}</p>
              </li>
            </CmsList>
            <CmsText as="h2" :page="PAGE" path="directions.title" />
            <div class="table-wrap">
              <table>
                <thead>
                  <tr><th>Откуда</th><th>Куда</th><th>Транспорт</th></tr>
                </thead>
                <CmsList v-slot="{ item }" as="tbody" :page="PAGE" path="directions.rows">
                  <tr>
                    <td>{{ item.from }}</td>
                    <td>{{ item.to }}</td>
                    <td>{{ item.transport }}</td>
                  </tr>
                </CmsList>
              </table>
            </div>
            <CmsText as="p" style="margin-top:8px" :page="PAGE" path="directions.note" />
          </article>
          <aside class="aside-box">
            <h3>Другие услуги</h3>
            <ul class="subnav">
              <li v-for="link in subnav" :key="link.to">
                <NuxtLink :to="link.to" :aria-current="link.to === route.path ? 'page' : undefined">{{ link.label }}</NuxtLink>
              </li>
            </ul>
            <CmsText as="h3" :page="PAGE" path="aside.title" />
            <CmsText as="p" :page="PAGE" path="aside.text" />
            <NuxtLink class="btn" to="/contacts#request"><CmsText :page="PAGE" path="aside.button" /></NuxtLink>
          </aside>
        </div>
      </div>
    </section>
  </div>
</template>

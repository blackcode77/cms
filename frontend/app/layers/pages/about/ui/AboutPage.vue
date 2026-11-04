<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { PartnersList } from '@widgets/partners-list'
import { CmsHtml, CmsList, CmsText, ContentImage, defineContentSchema, useCmsElement, usePageContent } from '@entities/page-content'
import { offices, officeElements } from '@shared/config'
import { usePageSeo } from '@shared/seo'
import { AppIcon } from '@shared/ui'

const PAGE = 'about'

defineContentSchema('site', officeElements)
defineContentSchema(PAGE, {
  'seo.title': { type: 'string', default: 'О компании — Гарант Интермодал', label: 'SEO: заголовок страницы' },
  'seo.description': { type: 'string', default: 'Группа компаний Гарант Интермодал: структура, офисы во Владивостоке, Москве и Находке, руководство, история и партнёры.', label: 'SEO: описание' },
  'hero.image': { type: 'image', default: '/img/ship-night.jpg', label: 'Первый экран: фон' },
  'hero.title': { type: 'string', default: 'О компании', label: 'Первый экран: заголовок' },
  'hero.lead': { type: 'string', default: 'Профессиональный мультимодальный оператор: 49 сотрудников, три офиса и группа из четырёх компаний.', label: 'Первый экран: текст' },
  'who.title': { type: 'string', default: 'Кто мы', label: 'Кто мы: заголовок' },
  'who.text': {
    type: 'html',
    default: '<p>«Гарант Интермодал» основана в 2014 году командой с более чем 25-летним опытом в морских перевозках, управлении контейнерными линиями (каботаж и внешняя торговля), экспедировании и агентировании.</p><p>Наш девиз — <b>Created to lead</b>. Мы строим самый короткий, простой и эффективный маршрут для груза клиента.</p>',
    label: 'Кто мы: текст',
  },
  'who.facts': {
    type: 'list',
    label: 'Кто мы: факты',
    fields: { text: { type: 'string', label: 'Пункт' } },
    default: [
      { text: '49 сотрудников' },
      { text: 'Офисы во Владивостоке, Москве и Находке (Восточный)' },
      { text: 'Прямые договоры с РЖД и железными дорогами СНГ' },
      { text: 'Сеть агентов в России, СНГ и Юго-Восточной Азии' },
    ],
  },
  'group.title': { type: 'string', default: 'Структура группы', label: 'Структура группы: заголовок' },
  'group.lead': { type: 'string', default: 'Четыре компании закрывают всю цепочку: от судна до двери клиента.', label: 'Структура группы: подзаголовок' },
  'group.companies': {
    type: 'list',
    label: 'Структура группы: компании (первая выделена как головная)',
    fields: {
      icon: { type: 'icon', label: 'Иконка', default: 'building' },
      name: { type: 'string', label: 'Название' },
      text: { type: 'text', label: 'Описание' },
      url: { type: 'url', label: 'Сайт компании' },
      url_label: { type: 'string', label: 'Текст ссылки на сайт' },
    },
    default: [
      { name: 'ООО «Гарант Интермодал»', text: 'Головная компания. Мультимодальные перевозки, экспедирование, агентирование, управление линиями.', icon: 'anchor', url: 'https://garant-intermodal.com', url_label: 'garant-intermodal.com →' },
      { name: 'ООО «Восточное судоходное товарищество»', text: 'Судовладелец-оператор двух МПП типа «Сокол» и каботажных линий.', icon: 'ship' },
      { name: 'ООО «Акватракинг»', text: 'Автотранспортные услуги: контейнерные перевозки. Создана в 2021 г., доля Гарант Интермодал — 50%.', icon: 'ship' },
      { name: 'ООО «Терминал Патрокл»', text: 'Площадка хранения и ремонта порожних контейнеров. Создана в 2023 г.', icon: 'ship' },
    ],
  },
  'offices.title': { type: 'string', default: 'Офисы', label: 'Офисы: заголовок' },
  'management.title': { type: 'string', default: 'Руководство', label: 'Руководство: заголовок' },
  'management.people': {
    type: 'list',
    label: 'Руководство: люди',
    fields: {
      photo: { type: 'image', label: 'Фото (необязательно)' },
      name: { type: 'string', label: 'ФИО' },
      role: { type: 'string', label: 'Должность' },
    },
    default: [
      { name: 'ФИО', role: 'Генеральный директор' },
      { name: 'ФИО', role: 'Коммерческий директор' },
      { name: 'ФИО', role: 'Операционный директор' },
      { name: 'ФИО', role: 'Директор московского офиса' },
    ],
  },
  'history.title': { type: 'string', default: 'История', label: 'История: заголовок' },
  'history.items': {
    type: 'list',
    label: 'История: события',
    fields: { year: { type: 'string', label: 'Год' }, text: { type: 'text', label: 'Событие' } },
    default: [
      { year: '2014', text: 'Открыты каботажные линии Владивосток — Магадан и Владивосток — Корсаков' },
      { year: '2015', text: 'Контейнерная линия Владивосток — Шанхай совместно с САСКО' },
      { year: '2017', text: 'Магадан: сервис совместно с «Оборонлогистикой»' },
      { year: '2020', text: 'Агентирование MSC' },
      { year: '2021', text: 'Агент Dong Young Shipping; генеральный экспедитор Русской рыбопромышленной компании' },
      { year: '2022', text: 'Агентирование CSTAR, Metro Shipping, Eurosib/SUNSCO; автосервис Пусан — Владивосток' },
      { year: '2023', text: 'Открыта площадка хранения контейнеров, создан «Терминал Патрокл»' },
    ],
  },
  'partners.title': { type: 'string', default: 'С кем мы работали', label: 'Партнёры: заголовок' },
  'partners.lead': { type: 'string', default: 'Клиенты и партнёры за годы работы.', label: 'Партнёры: подзаголовок' },
})

/** Current value of an office detail, for href attributes. */
const { resolve } = usePageContent()
const officeValue = (id: string, field: string) => resolve('site', `office.${id}.${field}`)?.value ?? ''

const seoTitle = useCmsElement(() => PAGE, () => 'seo.title')
const seoDescription = useCmsElement(() => PAGE, () => 'seo.description')

usePageSeo({
  title: () => seoTitle.element.value?.value ?? '',
  description: () => seoDescription.element.value?.value ?? '',
  path: '/about',
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
</script>

<template>
  <PageHero :cms-page="PAGE" :crumbs="[{ label: 'Главная', to: '/' }, { label: 'О компании' }]" />

  <section>
    <div class="container">
      <div class="split">
        <div>
          <div class="section-head"><CmsText as="h2" :page="PAGE" path="who.title" /></div>
          <CmsHtml :page="PAGE" path="who.text" />
        </div>
        <CmsList v-slot="{ item }" as="ul" class="checklist" :page="PAGE" path="who.facts">
          <li>{{ item.text }}</li>
        </CmsList>
      </div>
    </div>
  </section>

  <section id="group" class="section--tint">
    <div class="container">
      <div class="section-head">
        <CmsText as="h2" :page="PAGE" path="group.title" />
        <CmsText as="p" :page="PAGE" path="group.lead" />
      </div>
      <CmsList v-slot="{ item, index }" class="org" :page="PAGE" path="group.companies">
        <article class="card card--plain" :class="{ main: index === 0 }">
          <span class="icon" aria-hidden="true"><AppIcon v-if="item.icon" :name="item.icon" /></span>
          <h3>{{ item.name }}</h3>
          <p>{{ item.text }}</p>
          <p v-if="item.url">
            <a :href="item.url" target="_blank" rel="noopener">{{ item.url_label || item.url }}</a>
          </p>
        </article>
      </CmsList>
    </div>
  </section>

  <section id="offices">
    <div class="container">
      <div class="section-head"><CmsText as="h2" :page="PAGE" path="offices.title" /></div>
      <div class="grid grid--3">
        <article v-for="office in offices" :key="office.city" class="card card--plain">
          <span class="icon" aria-hidden="true"><AppIcon name="map-pin" /></span>
          <h3>{{ office.city }}</h3>
          <p>
            <CmsText page="site" :path="`office.${office.id}.address`" /><br>
            <template v-if="office.phone"><CmsText page="site" :path="`office.${office.id}.phone`" /><br></template>
            <a :href="`mailto:${officeValue(office.id, 'email')}`"><CmsText page="site" :path="`office.${office.id}.email`" /></a>
          </p>
        </article>
      </div>
    </div>
  </section>

  <section id="management" class="section--grey">
    <div class="container">
      <div class="section-head"><CmsText as="h2" :page="PAGE" path="management.title" /></div>
      <CmsList v-slot="{ item }" class="grid grid--4" :page="PAGE" path="management.people">
        <div class="person card card--plain">
          <div class="ph">
            <ContentImage v-if="item.photo" :src="item.photo" :alt="item.name" width="240" height="240" format="webp" loading="lazy" />
            <AppIcon v-else name="user" />
          </div>
          <h4>{{ item.name }}</h4>
          <p style="color:var(--grey);margin:0">{{ item.role }}</p>
        </div>
      </CmsList>
    </div>
  </section>

  <section id="history">
    <div class="container">
      <div class="section-head"><CmsText as="h2" :page="PAGE" path="history.title" /></div>
      <div class="table-wrap">
        <table>
          <CmsList v-slot="{ item }" as="tbody" :page="PAGE" path="history.items">
            <tr>
              <th scope="row" style="width:90px">{{ item.year }}</th>
              <td>{{ item.text }}</td>
            </tr>
          </CmsList>
        </table>
      </div>
    </div>
  </section>

  <section class="section--tint">
    <div class="container">
      <div class="section-head">
        <CmsText as="h2" :page="PAGE" path="partners.title" />
        <CmsText as="p" :page="PAGE" path="partners.lead" />
      </div>
      <PartnersList />
    </div>
  </section>

  <CtaBanner />
</template>

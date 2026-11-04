<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { CmsList, CmsText, ContentImage, defineContentSchema, useCmsElement } from '@entities/page-content'
import { imageSizes } from '@shared/config'
import { usePageSeo } from '@shared/seo'

const PAGE = 'project-cargo'

defineContentSchema(PAGE, {
  'seo.title': { type: 'string', default: 'Проектные перевозки — Гарант Интермодал', label: 'SEO: заголовок страницы' },
  'seo.description': { type: 'string', default: 'Негабаритные и тяжеловесные грузы, схемы крепления, ж/д и морские перевозки. Примеры сложных перевозок Гарант Интермодал.', label: 'SEO: описание' },
  'hero.image': { type: 'image', default: '/img/p-pipe.jpg', label: 'Первый экран: фон' },
  'hero.title': { type: 'string', default: 'Проектные перевозки', label: 'Первый экран: заголовок' },
  'hero.lead': { type: 'string', default: 'Контейнерные и конвенциональные грузы, включая тяжеловесные и негабаритные.', label: 'Первый экран: текст' },
  'intro.title': { type: 'string', default: 'Индивидуальная схема для каждого груза', label: 'Вступление: заголовок' },
  'intro.text': { type: 'text', default: 'Организуем перевозки на основании договоров с РЖД и железными дорогами СНГ. Разрабатываем схемы крепления, привлекаем сюрвейеров, обеспечиваем сопровождение, охрану и отслеживание груза.', label: 'Вступление: текст' },
  'intro.checklist': {
    type: 'list',
    label: 'Вступление: список',
    fields: { text: { type: 'string', label: 'Пункт' } },
    default: [
      { text: 'Негабарит и тяжеловесы' },
      { text: 'RO-RO, танк-контейнеры' },
      { text: 'Целые заводы и нефтегазовые объекты' },
      { text: 'Опасные грузы' },
    ],
  },
  'routes.title': { type: 'string', default: 'Пример маршрута', label: 'Маршруты: заголовок таблицы' },
  'routes.items': {
    type: 'list',
    label: 'Маршруты: строки таблицы',
    fields: { text: { type: 'string', label: 'Маршрут' } },
    default: [
      { text: 'Сахалин → ОАЭ' },
      { text: 'Китай → Тобольск (СИБУР)' },
      { text: 'Трансформаторы 140–180 т → Магадан' },
      { text: 'Пусан → Москва' },
      { text: 'Корея → Кострома (через Санкт-Петербург)' },
      { text: 'Эстония, Рига → Кабул; Афганистан → Бишкек' },
    ],
  },
  'examples.title': { type: 'string', default: 'Примеры перевозок', label: 'Примеры: заголовок' },
  'examples.items': {
    type: 'list',
    label: 'Примеры: карточки',
    fields: {
      image: { type: 'image', label: 'Картинка' },
      title: { type: 'string', label: 'Заголовок' },
      text: { type: 'text', label: 'Текст' },
    },
    default: [
      { title: 'Трубные колена на платформе', text: 'Крупногабаритные трубные изделия: схема крепления и железнодорожная доставка.', image: '/img/p-pipe.jpg' },
      { title: 'Бульдозеры и спецтехника', text: 'Перевалка гусеничной техники с судна на платформу.', image: '/img/p-dozer.jpg' },
      { title: 'Рулонная сталь', text: 'Перевозка рулонов в полувагонах с индивидуальной схемой крепления.', image: '/img/p-coil.jpg' },
      { title: 'Негабаритное оборудование', text: 'Погрузка многотонного оборудования с использованием кранов.', image: '/img/p-crane.jpg' },
      { title: 'Ёмкости и цистерны', text: 'Крупные ёмкости на платформах, транспортировка с сопровождением.', image: '/img/p-tank.jpg' },
      { title: 'Тяжеловесные грузы в полувагонах', text: 'Перегрузка и доставка тяжеловесов по железной дороге.', image: '/img/p-rail.jpg' },
      { title: 'Автомобили', text: 'Партионная перевозка автомобилей: Корея — Россия.', image: '/img/p-cars.jpg' },
      { title: 'Авто в контейнере', text: 'Технология: 3–4 автомобиля в 40HC, погрузка и крепление вне контейнера.', image: '/img/p-carbox.jpg' },
    ],
  },
})

const seoTitle = useCmsElement(() => PAGE, () => 'seo.title')
const seoDescription = useCmsElement(() => PAGE, () => 'seo.description')

usePageSeo({
  title: () => seoTitle.element.value?.value ?? '',
  description: () => seoDescription.element.value?.value ?? '',
  path: '/project-cargo',
  image: '/img/p-pipe.jpg',
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
      :crumbs="[{ label: 'Услуги', to: '/services' }, { label: 'Проектные перевозки' }]"
    />
    <section>
      <div class="container">
        <div class="split">
          <div>
            <div class="section-head"><CmsText as="h2" :page="PAGE" path="intro.title" /></div>
            <CmsText as="p" :page="PAGE" path="intro.text" />
            <CmsList v-slot="{ item }" as="ul" class="checklist" :page="PAGE" path="intro.checklist">
              <li>{{ item.text }}</li>
            </CmsList>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr><CmsText as="th" :page="PAGE" path="routes.title" /></tr>
              </thead>
              <CmsList v-slot="{ item }" as="tbody" :page="PAGE" path="routes.items">
                <tr>
                  <td>{{ item.text }}</td>
                </tr>
              </CmsList>
            </table>
          </div>
        </div>
      </div>
    </section>
    <section class="section--tint">
      <div class="container">
        <div class="section-head"><CmsText as="h2" :page="PAGE" path="examples.title" /></div>
        <CmsList v-slot="{ item }" class="grid grid--4" :page="PAGE" path="examples.items">
          <article class="card">
            <ContentImage
              v-if="item.image"
              class="card__img"
              :src="item.image"
              :alt="item.title"
              width="600"
              height="375"
              :sizes="imageSizes.quarter"
              format="webp"
              loading="lazy"
            />
            <div class="card__body">
              <h3 style="font-size:1.05rem">{{ item.title }}</h3>
              <p>{{ item.text }}</p>
            </div>
          </article>
        </CmsList>
      </div>
    </section>
    <CtaBanner />
  </div>
</template>

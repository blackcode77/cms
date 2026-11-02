<script setup lang="ts">
import { PageHero } from '@widgets/page-hero'
import { imageSizes } from '@shared/config'
import { usePageSeo } from '@shared/seo'
import { directions, included, orderSteps, subnav } from '../model/content'

const route = useRoute()

if (route.params.slug !== 'multimodal') {
  throw createError({ statusCode: 404, statusMessage: 'Page Not Found' })
}

usePageSeo({
  title: 'Мультимодальные перевозки — Гарант Интермодал',
  description: 'Мультимодальные перевозки морем, по железной дороге и автотранспортом по единой ставке: Китай, Корея, Япония — Россия.',
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
      title="Мультимодальные перевозки"
      lead="Сквозная перевозка груза морем, по железной дороге и автотранспортом по единой ставке и под единым контролем."
      image="/img/train.jpg"
      :crumbs="[{ label: 'Услуги', to: '/services' }, { label: 'Мультимодальные перевозки' }]"
    />
    <section>
      <div class="container">
        <div class="layout">
          <article class="prose">
            <h2>Описание</h2>
            <p>Мультимодальная перевозка — организация доставки «от двери до двери» несколькими видами транспорта. Вы получаете один договор, одну ставку и одного ответственного менеджера вместо нескольких подрядчиков.</p>
            <h2>Что входит</h2>
            <ul class="checklist">
              <li v-for="item in included" :key="item">{{ item }}</li>
            </ul>
            <figure style="margin:28px 0">
              <NuxtImg
                src="/img/train.jpg"
                alt="Контейнерный поезд"
                width="563"
                height="398"
                :sizes="imageSizes.half"
                format="webp"
                loading="lazy"
              />
              <figcaption>Контейнерный поезд на маршруте из порта вглубь России</figcaption>
            </figure>
            <h2>Как заказать</h2>
            <ol class="steps">
              <li v-for="step in orderSteps" :key="step.title">
                <b>{{ step.title }}</b>
                <p>{{ step.text }}</p>
              </li>
            </ol>
            <h2>Типовые направления</h2>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr><th>Откуда</th><th>Куда</th><th>Транспорт</th></tr>
                </thead>
                <tbody>
                  <tr v-for="row in directions" :key="row.from + row.to">
                    <td>{{ row.from }}</td>
                    <td>{{ row.to }}</td>
                    <td>{{ row.transport }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p style="margin-top:8px">Таблица иллюстративная, актуальные направления — в тарифах.</p>
          </article>
          <aside class="aside-box">
            <h3>Другие услуги</h3>
            <ul class="subnav">
              <li v-for="link in subnav" :key="link.to">
                <NuxtLink :to="link.to" :aria-current="link.to === route.path ? 'page' : undefined">{{ link.label }}</NuxtLink>
              </li>
            </ul>
            <h3>Нужен расчёт?</h3>
            <p>Ответим в рабочее время.</p>
            <NuxtLink class="btn" to="/contacts#request">Запросить расчёт</NuxtLink>
          </aside>
        </div>
      </div>
    </section>
  </div>
</template>

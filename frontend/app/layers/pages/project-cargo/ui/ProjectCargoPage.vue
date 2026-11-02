<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { imageSizes } from '@shared/config'
import { usePageSeo } from '@shared/seo'
import { cargoExamples, sampleRoutes } from '../model/content'

usePageSeo({
  title: 'Проектные перевозки — Гарант Интермодал',
  description: 'Негабаритные и тяжеловесные грузы, схемы крепления, ж/д и морские перевозки. Примеры сложных перевозок Гарант Интермодал.',
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
      title="Проектные перевозки"
      lead="Контейнерные и конвенциональные грузы, включая тяжеловесные и негабаритные."
      image="/img/p-pipe.jpg"
      :crumbs="[{ label: 'Услуги', to: '/services' }, { label: 'Проектные перевозки' }]"
    />
    <section>
      <div class="container">
        <div class="split">
          <div>
            <div class="section-head"><h2>Индивидуальная схема для каждого груза</h2></div>
            <p>Организуем перевозки на основании договоров с РЖД и железными дорогами СНГ. Разрабатываем схемы крепления, привлекаем сюрвейеров, обеспечиваем сопровождение, охрану и отслеживание груза.</p>
            <ul class="checklist">
              <li>Негабарит и тяжеловесы</li>
              <li>RO-RO, танк-контейнеры</li>
              <li>Целые заводы и нефтегазовые объекты</li>
              <li>Опасные грузы</li>
            </ul>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr><th>Пример маршрута</th></tr>
              </thead>
              <tbody>
                <tr v-for="route in sampleRoutes" :key="route">
                  <td>{{ route }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
    <section class="section--tint">
      <div class="container">
        <div class="section-head"><h2>Примеры перевозок</h2></div>
        <div class="grid grid--4">
          <article v-for="item in cargoExamples" :key="item.title" class="card">
            <NuxtImg
              class="card__img"
              :src="item.image"
              :alt="item.title"
              :width="item.width"
              :height="item.height"
              :sizes="imageSizes.quarter"
              format="webp"
              loading="lazy"
            />
            <div class="card__body">
              <h3 style="font-size:1.05rem">{{ item.title }}</h3>
              <p>{{ item.text }}</p>
            </div>
          </article>
        </div>
      </div>
    </section>
    <CtaBanner />
  </div>
</template>

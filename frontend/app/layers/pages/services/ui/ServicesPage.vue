<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { usePageSeo } from '@shared/seo'
import { SvgIcon } from '@shared/ui'
import { cargoAgency, geography, processSteps, relatedLinks, services, shipAgency } from '../model/content'
import { RouteMap } from '@widgets/route-map'

usePageSeo({
  title: 'Услуги — Гарант Интермодал',
  description: 'Мультимодальные, каботажные и проектные перевозки, агентирование, таможенные услуги, хранение контейнеров, автотранспорт — полный перечень услуг Гарант Интермодал.',
  path: '/services',
  image: '/img/hero.jpg',
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
      title="Услуги группы компаний"
      lead="Полный цикл логистики: от погрузки у отправителя до выдачи груза получателю."
      image="/img/hero.jpg"
      :crumbs="[{ label: 'Услуги' }]"
    />
    <section>
      <div class="container">
        <div class="section-head">
          <h2>Что мы делаем</h2>
          <p>Услуги можно заказывать целиком или по звеньям цепочки.</p>
        </div>
        <div class="grid grid--3">
          <NuxtLink
            v-for="service in services"
            :key="service.title"
            class="card card--plain"
            :to="service.to"
            style="color:inherit;text-decoration:none"
          >
            <span class="icon" aria-hidden="true"><SvgIcon :inner="service.icon" /></span>
            <h3>{{ service.title }}</h3>
            <p style="color:#4a4647;font-size:.95rem">{{ service.text }}</p>
            <span class="card__more">Подробнее</span>
          </NuxtLink>
        </div>
      </div>
    </section>

    <section id="geography" class="section--tint">
      <div class="container">
        <div class="geo">
          <div>
            <RouteMap />
            <div class="legend">
              <span><i class="sea" />Море</span>
              <span><i class="rail" />Железная дорога</span>
            </div>
          </div>
          <div>
            <div class="section-head"><h2>География сервисов</h2></div>
            <template v-for="group in geography" :key="group.title">
              <h4>{{ group.title }}</h4>
              <ul class="checklist">
                <li v-for="item in group.items" :key="item">{{ item }}</li>
              </ul>
            </template>
          </div>
        </div>
      </div>
    </section>

    <section id="process">
      <div class="container">
        <div class="section-head">
          <h2>Как получить услугу</h2>
          <p>Простая процедура из пяти шагов.</p>
        </div>
        <ol class="steps">
          <li v-for="step in processSteps" :key="step.title">
            <b>{{ step.title }}</b>
            <p>{{ step.text }}</p>
          </li>
        </ol>
      </div>
    </section>

    <section id="agent" class="section--grey">
      <div class="container">
        <div class="grid grid--2">
          <article class="card card--plain">
            <h3>Судовое агентирование</h3>
            <ul class="checklist">
              <li v-for="item in shipAgency" :key="item">{{ item }}</li>
            </ul>
          </article>
          <article id="customs" class="card card--plain">
            <h3>Грузовое агентирование и таможня</h3>
            <ul class="checklist">
              <li v-for="item in cargoAgency" :key="item">{{ item }}</li>
            </ul>
          </article>
        </div>
      </div>
    </section>

    <section>
      <div class="container">
        <div class="grid grid--2">
          <NuxtLink
            v-for="link in relatedLinks"
            :key="link.to"
            class="card card--plain"
            :to="link.to"
            style="color:inherit;text-decoration:none"
          >
            <span class="icon" aria-hidden="true"><SvgIcon :inner="link.icon" /></span>
            <h3>{{ link.title }}</h3>
            <p>{{ link.text }}</p>
            <span class="card__more">{{ link.more }}</span>
          </NuxtLink>
        </div>
      </div>
    </section>
    <CtaBanner />
  </div>
</template>

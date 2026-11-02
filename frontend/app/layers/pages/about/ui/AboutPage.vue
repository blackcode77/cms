<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { offices } from '@shared/config'
import { usePageSeo } from '@shared/seo'
import { SvgIcon } from '@shared/ui'
import { companies, facts, history, managers, partners, personIcon, pinIcon } from '../model/content'

const title = 'О компании — Гарант Интермодал'
const description = 'Группа компаний Гарант Интермодал: структура, офисы во Владивостоке, Москве и Находке, руководство, история и партнёры.'

usePageSeo({
  title,
  description,
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
  <PageHero
    title="О компании"
    lead="Профессиональный мультимодальный оператор: 49 сотрудников, три офиса и группа из четырёх компаний."
    image="/img/ship-night.jpg"
    :crumbs="[{ label: 'Главная', to: '/' }, { label: 'О компании' }]"
  />

  <section>
    <div class="container">
      <div class="split">
        <div>
          <div class="section-head"><h2>Кто мы</h2></div>
          <p>«Гарант Интермодал» основана в 2014 году командой с более чем 25-летним опытом в морских перевозках, управлении контейнерными линиями (каботаж и внешняя торговля), экспедировании и агентировании.</p>
          <p>Наш девиз — <b>Created to lead</b>. Мы строим самый короткий, простой и эффективный маршрут для груза клиента.</p>
        </div>
        <ul class="checklist">
          <li v-for="fact in facts" :key="fact">{{ fact }}</li>
        </ul>
      </div>
    </div>
  </section>

  <section id="group" class="section--tint">
    <div class="container">
      <div class="section-head">
        <h2>Структура группы</h2>
        <p>Четыре компании закрывают всю цепочку: от судна до двери клиента.</p>
      </div>
      <div class="org">
        <article v-for="company in companies" :key="company.name" class="card card--plain" :class="{ main: company.main }">
          <span class="icon" aria-hidden="true"><SvgIcon :inner="company.icon" /></span>
          <h3>{{ company.name }}</h3>
          <p>{{ company.text }}</p>
          <p><a :href="company.href ?? '#'">{{ company.linkLabel }}</a></p>
        </article>
      </div>
    </div>
  </section>

  <section id="offices">
    <div class="container">
      <div class="section-head"><h2>Офисы</h2></div>
      <div class="grid grid--3">
        <article v-for="office in offices" :key="office.city" class="card card--plain">
          <span class="icon" aria-hidden="true"><SvgIcon :inner="pinIcon" /></span>
          <h3>{{ office.city }}</h3>
          <p>
            {{ office.address }}<br>
            <template v-if="office.phone">{{ office.phone }}<br></template>
            <a :href="`mailto:${office.email}`">{{ office.email }}</a>
          </p>
        </article>
      </div>
    </div>
  </section>

  <section id="management" class="section--grey">
    <div class="container">
      <div class="section-head"><h2>Руководство</h2></div>
      <div class="grid grid--4">
        <div v-for="role in managers" :key="role" class="person card card--plain">
          <div class="ph"><SvgIcon :inner="personIcon" /></div>
          <h4>ФИО</h4>
          <p style="color:var(--grey);margin:0">{{ role }}</p>
        </div>
      </div>
    </div>
  </section>

  <section id="history">
    <div class="container">
      <div class="section-head"><h2>История</h2></div>
      <div class="table-wrap">
        <table>
          <tbody>
            <tr v-for="item in history" :key="item.year">
              <th scope="row" style="width:90px">{{ item.year }}</th>
              <td>{{ item.text }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section class="section--tint">
    <div class="container">
      <div class="section-head">
        <h2>С кем мы работали</h2>
        <p>Клиенты и партнёры за годы работы.</p>
      </div>
      <ul class="partners">
        <li v-for="partner in partners" :key="partner">{{ partner }}</li>
      </ul>
    </div>
  </section>

  <CtaBanner />
</template>

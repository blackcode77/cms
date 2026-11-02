<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { usePageSeo } from '@shared/seo'
import { acceptanceSteps, forbiddenCargo, hazardClasses } from '../model/content'

const title = 'Перевозка опасных грузов — Гарант Интермодал'
const description = 'Порядок приёма, классы опасности и список запрещённых к приёму опасных грузов. Гарант Интермодал.'

usePageSeo({
  title,
  description,
  path: '/dangerous-goods',
  image: '/img/p-tank.jpg',
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
      title="Перевозка опасных грузов"
      lead="Принимаем опасные грузы в контейнерах и тарных местах по правилам МОПОГ/IMDG и требованиям перевозчиков."
      image="/img/p-tank.jpg"
      :crumbs="[{ label: 'Главная', to: '/' }, { label: 'Опасные грузы' }]"
    />
    <section>
      <div class="container">
        <div class="layout">
          <article class="prose">
            <h2>Порядок приёма</h2>
            <ol class="steps" style="grid-template-columns:1fr 1fr">
              <li v-for="step in acceptanceSteps" :key="step.title"><b>{{ step.title }}</b><p>{{ step.text }}</p></li>
            </ol>
            <h2>Классы опасности</h2>
            <div class="table-wrap">
              <table>
                <thead><tr><th>Класс</th><th>Название</th></tr></thead>
                <tbody>
                  <tr v-for="item in hazardClasses" :key="item.code">
                    <td><b>{{ item.code }}</b></td>
                    <td>{{ item.name }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <h2 id="forbidden">Запрещены к приёму</h2>
            <ul class="checklist checklist--no">
              <li v-for="item in forbiddenCargo" :key="item">{{ item }}</li>
            </ul>
            <p style="color:var(--grey)">Полный список запрещённых грузов будет загружен документом.</p>
          </article>
          <aside class="aside-box">
            <h3>Консультация по грузу</h3>
            <p>Опишите груз — подскажем, возможна ли перевозка, и рассчитаем стоимость.</p>
            <NuxtLink class="btn" to="/contacts#request">Задать вопрос</NuxtLink>
            <hr style="border:0;border-top:1px solid var(--line);margin:20px 0">
            <p><b>Документы</b></p>
            <ul class="docs">
              <li style="grid-template-columns:44px 1fr">
                <span class="ext">PDF</span>
                <div><b>Памятка по ОГ</b></div>
              </li>
            </ul>
          </aside>
        </div>
      </div>
    </section>
    <CtaBanner />
  </div>
</template>

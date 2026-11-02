<script setup lang="ts">
import { PageHero } from '@widgets/page-hero'
import { offices, telHref } from '@shared/config'
import { usePageSeo } from '@shared/seo'
import { SvgIcon } from '@shared/ui'
import { cargoTypes, pinIcon, requestBenefits } from '../model/content'

const title = 'Контакты — Гарант Интермодал'
const description = 'Контакты Гарант Интермодал: офисы во Владивостоке, Москве и Находке, телефоны, email, форма запроса расчёта.'

usePageSeo({
  title,
  description,
  path: '/contacts',
  image: '/img/ship-closeup.jpg',
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
    title="Контакты"
    lead="Свяжитесь с нами по телефону, почте или оставьте заявку."
    image="/img/ship-closeup.jpg"
    :crumbs="[{ label: 'Главная', to: '/' }, { label: 'Контакты' }]"
  />

  <section>
    <div class="container">
      <div class="grid grid--3">
        <article v-for="office in offices" :key="office.city" class="card card--plain">
          <span class="icon" aria-hidden="true"><SvgIcon :inner="pinIcon" /></span>
          <h3>{{ office.city }}</h3>
          <p>{{ office.address }}</p>
          <p>
            <template v-if="office.phone">
              <a :href="telHref(office.phone)">{{ office.phone }}</a><br>
            </template>
            <br v-else>
            <a :href="`mailto:${office.email}`">{{ office.email }}</a>
          </p>
          <p style="color:var(--grey);font-size:.9rem">{{ office.hours }}</p>
          <div style="background:var(--green-tint);border:1px dashed var(--grey);border-radius:6px;padding:28px;text-align:center;color:var(--grey);font-size:.9rem">Карта</div>
        </article>
      </div>
    </div>
  </section>

  <section id="request" class="section--tint">
    <div class="container">
      <div class="split" style="align-items:start">
        <div>
          <div class="section-head"><h2>Запросить расчёт</h2></div>
          <p>Заполните форму — менеджер свяжется с вами и подготовит ставку.</p>
          <ul class="checklist">
            <li v-for="benefit in requestBenefits" :key="benefit">{{ benefit }}</li>
          </ul>
        </div>
        <form class="form" @submit.prevent>
          <div class="row">
            <label>Откуда<input name="from" placeholder="Например, Шанхай"></label>
            <label>Куда<input name="to" placeholder="Например, Москва"></label>
          </div>
          <div class="row">
            <label>
              Тип груза
              <select name="cargo">
                <option v-for="cargo in cargoTypes" :key="cargo">{{ cargo }}</option>
              </select>
            </label>
            <label>Масса, т<input name="weight" inputmode="decimal"></label>
          </div>
          <div class="row">
            <label>Имя<input name="name" autocomplete="name" required></label>
            <label>Телефон<input name="phone" type="tel" autocomplete="tel" required></label>
          </div>
          <label>Email<input name="email" type="email" autocomplete="email"></label>
          <label>Комментарий<textarea name="msg" rows="3" /></label>
          <button class="btn" type="submit">Отправить заявку</button>
        </form>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="section-head"><h2>Реквизиты</h2></div>
      <p>ООО «Гарант Интермодал».</p>
    </div>
  </section>
</template>

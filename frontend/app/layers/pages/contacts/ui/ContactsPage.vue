<script setup lang="ts">
import { PageHero } from '@widgets/page-hero'
import { CmsText, defineContentSchema, useCmsElement, usePageContent } from '@entities/page-content'
import { offices, officeElements, telHref } from '@shared/config'
import { organizationJsonLd, usePageSeo } from '@shared/seo'
import { AppIcon } from '@shared/ui'
import { cargoTypes, requestBenefits } from '../model/content'

defineContentSchema('site', officeElements)
defineContentSchema('contacts', {
  'seo.title': { type: 'string', default: 'Контакты — Гарант Интермодал', label: 'SEO: заголовок страницы' },
  'seo.description': { type: 'string', default: 'Контакты Гарант Интермодал: офисы во Владивостоке, Москве и Находке, телефоны, email, форма запроса расчёта.', label: 'SEO: описание' },
  'hero.image': { type: 'image', default: '/img/ship-closeup.jpg', label: 'Первый экран: фон' },
  'hero.title': { type: 'string', default: 'Контакты', label: 'Первый экран: заголовок' },
  'hero.lead': { type: 'string', default: 'Свяжитесь с нами по телефону, почте или оставьте заявку.', label: 'Первый экран: текст' },
})

const seoTitle = useCmsElement(() => 'contacts', () => 'seo.title')
const seoDescription = useCmsElement(() => 'contacts', () => 'seo.description')
const phone = useCmsElement(() => 'site', () => 'office.vladivostok.phone')
const email = useCmsElement(() => 'site', () => 'office.vladivostok.email')
const address = useCmsElement(() => 'site', () => 'office.vladivostok.address')

usePageSeo({
  title: () => seoTitle.element.value?.value ?? '',
  description: () => seoDescription.element.value?.value ?? '',
  path: '/contacts',
  image: '/img/ship-closeup.jpg',
  jsonLd: () => organizationJsonLd({
    phone: phone.element.value?.value ?? '',
    email: email.element.value?.value ?? '',
    address: address.element.value?.value ?? '',
  }),
})

/** Current value of an office detail, for href attributes. */
const { resolve } = usePageContent()
const officeValue = (id: string, field: string) => resolve('site', `office.${id}.${field}`)?.value ?? ''
</script>

<template>
  <PageHero
    cms-page="contacts"
    :crumbs="[{ label: 'Главная', to: '/' }, { label: 'Контакты' }]"
  />

  <section>
    <div class="container">
      <div class="grid grid--3">
        <article v-for="office in offices" :key="office.city" class="card card--plain">
          <span class="icon" aria-hidden="true"><AppIcon name="map-pin" /></span>
          <h3>{{ office.city }}</h3>
          <CmsText as="p" page="site" :path="`office.${office.id}.address`" />
          <p>
            <template v-if="office.phone">
              <a :href="telHref(officeValue(office.id, 'phone'))"><CmsText page="site" :path="`office.${office.id}.phone`" /></a><br>
            </template>
            <br v-else>
            <a :href="`mailto:${officeValue(office.id, 'email')}`"><CmsText page="site" :path="`office.${office.id}.email`" /></a>
          </p>
          <CmsText as="p" page="site" :path="`office.${office.id}.hours`" style="color:var(--grey);font-size:.9rem" />
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

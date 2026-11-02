<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { imageSizes } from '@shared/config'
import { usePageSeo } from '@shared/seo'
import { SvgIcon } from '@shared/ui'
import { advantages, partners, projectGallery, serviceCards, stats } from '../model/content'
import { RouteMap } from '@widgets/route-map'

const title = 'Мультимодальные перевозки на Дальнем Востоке — Гарант Интермодал'
const description = 'Гарант Интермодал — мультимодальный оператор: морские, железнодорожные и автоперевозки контейнеров, проектных и опасных грузов. Владивосток, Москва, Находка.'

usePageSeo({
  title,
  description,
  path: '/',
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
    <div class="hero">
      <NuxtImg
        class="hero__bg"
        src="/img/hero.jpg"
        alt=""
        width="1600"
        height="800"
        :sizes="imageSizes.full"
        format="webp"
        quality="70"
        preload
        fetchpriority="high"
        loading="eager"
      />
      <div class="container">
        <span class="eyebrow">Мультимодальный оператор · с 2014 года</span>
        <h1>Кратчайший путь вашего груза</h1>
        <p class="hero__lead">
          Море, железная дорога и автотранспорт — в одном окне. Организуем перевозки контейнерных, генеральных и проектных грузов в Россию, из неё и транзитом через Дальний Восток.
        </p>
        <div class="hero__actions">
          <NuxtLink class="btn btn--light" to="/contacts#request">Запросить расчёт</NuxtLink>
          <NuxtLink class="btn btn--outline-light" to="/schedules">Расписания и тарифы</NuxtLink>
        </div>
      </div>
    </div>

    <div class="stats">
      <div class="container">
        <ul>
          <li v-for="stat in stats" :key="stat.label">
            <SvgIcon class="stat-ico" :inner="stat.icon" />
            <b>{{ stat.value }}</b>
            <span>{{ stat.label }}</span>
          </li>
        </ul>
      </div>
    </div>

    <section>
      <div class="container">
        <div class="split">
          <div>
            <div class="section-head"><h2>Чем мы занимаемся</h2></div>
            <p>«Гарант Интермодал» — профессиональный мультимодальный оператор. Мы организуем перевозки морем, по железной дороге и автотранспортом, управляем контейнерными линиями и оказываем агентские услуги в портах Дальнего Востока.</p>
            <p>Подход модульный: можно заказать перевозку «под ключ» или отдельные звенья цепочки — выбор за вами.</p>
            <ul class="checklist">
              <li>Контейнерные, генеральные, насыпные и опасные грузы</li>
              <li>Проектные грузы: негабарит и тяжеловесы</li>
              <li>Направления: Китай, Корея, Япония, Россия, СНГ, Европа</li>
            </ul>
            <NuxtLink class="btn btn--ghost" to="/about">О компании</NuxtLink>
          </div>
          <figure>
            <NuxtImg
              src="/img/ship-closeup.jpg"
              alt="Контейнеровоз NEWSTRIVER у причала порта"
              width="1400"
              height="1050"
              :sizes="imageSizes.half"
              format="webp"
              loading="lazy"
            />
            <figcaption>Контейнеровоз NEWSTRIVER линии UNITY LINE в порту Китая</figcaption>
          </figure>
        </div>
      </div>
    </section>

    <section class="section--tint" aria-labelledby="why">
      <div class="container">
        <div class="section-head">
          <h2 id="why">Почему выбирают Гарант Интермодал</h2>
          <p>Шесть причин, по которым клиенты остаются с нами годами.</p>
        </div>
        <div class="grid grid--3">
          <article v-for="item in advantages" :key="item.title" class="adv">
            <span class="icon" aria-hidden="true"><SvgIcon :inner="item.icon" /></span>
            <h3>{{ item.title }}</h3>
            <p>{{ item.text }}</p>
          </article>
        </div>
      </div>
    </section>

    <section aria-labelledby="svc">
      <div class="container">
        <div class="section-head">
          <h2 id="svc">Услуги</h2>
          <p>Основные направления. Полный перечень и подробности — на странице услуг.</p>
        </div>
        <div class="grid grid--4">
          <NuxtLink
            v-for="card in serviceCards"
            :key="card.title"
            class="card"
            :to="card.to"
            style="color:inherit;text-decoration:none"
          >
            <NuxtImg
              class="card__img"
              :src="card.image"
              alt=""
              width="600"
              height="375"
              :sizes="imageSizes.quarter"
              format="webp"
              loading="lazy"
            />
            <div class="card__body">
              <h3>{{ card.title }}</h3>
              <p>{{ card.text }}</p>
              <span class="card__more">Подробнее</span>
            </div>
          </NuxtLink>
        </div>
        <p style="margin-top:28px"><NuxtLink class="btn btn--ghost" to="/services">Все услуги</NuxtLink></p>
      </div>
    </section>

    <section class="section--tint" aria-labelledby="geo">
      <div class="container">
        <div class="geo">
          <div>
            <RouteMap />
            <div class="legend">
              <span><i class="sea" />Море</span>
              <span><i class="rail" />Железная дорога</span>
              <span>схема, не в масштабе</span>
            </div>
          </div>
          <div>
            <div class="section-head"><h2 id="geo">География сервисов</h2></div>
            <p>Порты Владивостока, Восточный (терминал ВСК) и Находка (терминал НМРП) — наши основные узлы. Отсюда идут морские каботажные линии на север и Сахалин, а контейнерные поезда — в Москву, Новосибирск и Екатеринбург.</p>
            <ul class="checklist">
              <li>Каботаж: Магадан, Корсаков, Петропавловск-Камчатский, Анадырь</li>
              <li>Импорт: Китай, Корея, Япония → Россия</li>
              <li>Погранпереходы: Наушки, Забайкальск, Достык, Гродеково</li>
            </ul>
            <NuxtLink class="btn btn--ghost" to="/services#geography">Подробнее о географии</NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <section aria-labelledby="fl">
      <div class="container">
        <div class="split">
          <figure>
            <NuxtImg
              src="/img/cabotage.jpg"
              alt="Судно «Гарант Нагаево» в ледовых условиях"
              width="1000"
              height="750"
              :sizes="imageSizes.half"
              format="webp"
              loading="lazy"
            />
          </figure>
          <div>
            <div class="section-head"><h2 id="fl">Собственный флот</h2></div>
            <p>Два многоцелевых судна типа «Сокол» (2014 г.) и контейнеровоз «Гарант Нагаево» обеспечивают регулярные каботажные линии. Суда эксплуатирует «Восточное судоходное товарищество».</p>
            <div class="tracker" style="margin-bottom:20px">
              <span class="dot" />
              <div>
                <b>Где наши суда сейчас</b><br>
                <span style="color:var(--grey);font-size:.9rem">Карта слежения будет подключена позже</span>
              </div>
            </div>
            <NuxtLink class="btn btn--ghost" to="/fleet">Флот и маршруты</NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <section class="section--grey" aria-labelledby="pr">
      <div class="container">
        <div class="section-head">
          <h2 id="pr">Сложные перевозки — наша специализация</h2>
          <p>Трубные колена, бульдозеры, рулонная сталь, краны, автомобили: берёмся за грузы, где нужна индивидуальная схема.</p>
        </div>
        <div class="gallery">
          <NuxtImg
            v-for="img in projectGallery"
            :key="img.src"
            :src="img.src"
            :alt="img.alt"
            width="480"
            height="360"
            :sizes="imageSizes.quarter"
            format="webp"
            loading="lazy"
          />
        </div>
        <p style="margin-top:28px"><NuxtLink class="btn" to="/project-cargo">Примеры проектных перевозок</NuxtLink></p>
      </div>
    </section>

    <section aria-labelledby="pt">
      <div class="container">
        <div class="section-head">
          <h2 id="pt">С кем мы работаем</h2>
          <p>Клиенты и партнёры, с которыми у нас выстроено сотрудничество.</p>
        </div>
        <ul class="partners">
          <li v-for="partner in partners" :key="partner">{{ partner }}</li>
        </ul>
      </div>
    </section>

    <CtaBanner />
  </div>
</template>

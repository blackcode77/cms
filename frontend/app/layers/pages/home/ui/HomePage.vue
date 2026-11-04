<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { CmsImage, CmsList, CmsText, ContentImage, defineContentSchema, useCmsElement } from '@entities/page-content'
import { imageSizes, officeElements } from '@shared/config'
import { organizationJsonLd, usePageSeo } from '@shared/seo'
import { AppIcon } from '@shared/ui'
import { NuxtLink } from '#components'
import { RouteMap } from '@widgets/route-map'
import { PartnersList } from '@widgets/partners-list'

defineContentSchema('site', officeElements)
defineContentSchema('home', {
  'seo.title': { type: 'string', default: 'Мультимодальные перевозки на Дальнем Востоке — Гарант Интермодал', label: 'SEO: заголовок страницы' },
  'seo.description': { type: 'string', default: 'Гарант Интермодал — мультимодальный оператор: морские, железнодорожные и автоперевозки контейнеров, проектных и опасных грузов. Владивосток, Москва, Находка.', label: 'SEO: описание' },
  'hero.image': { type: 'image', default: '/img/hero.jpg', label: 'Первый экран: фон' },
  'hero.eyebrow': { type: 'string', default: 'Мультимодальный оператор · с 2014 года', label: 'Первый экран: надпись над заголовком' },
  'hero.title': { type: 'string', default: 'Кратчайший путь вашего груза', label: 'Первый экран: заголовок' },
  'hero.lead': { type: 'text', default: 'Море, железная дорога и автотранспорт — в одном окне. Организуем перевозки контейнерных, генеральных и проектных грузов в Россию, из неё и транзитом через Дальний Восток.', label: 'Первый экран: текст' },
  'hero.cta_primary': { type: 'string', default: 'Запросить расчёт', label: 'Первый экран: основная кнопка' },
  'hero.cta_secondary': { type: 'string', default: 'Расписания и тарифы', label: 'Первый экран: вторая кнопка' },
  'stats.items': {
    type: 'list',
    label: 'Цифры: пункты',
    fields: {
      icon: { type: 'icon', label: 'Иконка', default: 'circle-check' },
      value: { type: 'string', label: 'Число' },
      label: { type: 'string', label: 'Подпись' },
    },
    default: [
      { icon: 'calendar', value: '2014', label: 'год основания' },
      { icon: 'users', value: '49', label: 'сотрудников' },
      { icon: 'award', value: '25+', label: 'лет опыта в морской отрасли' },
      { icon: 'map-pin', value: '3', label: 'офиса: Владивосток, Москва, Находка' },
      { icon: 'anchor', value: '4', label: 'компании в группе' },
    ],
  },
  'about.title': { type: 'string', default: 'Чем мы занимаемся', label: 'О нас: заголовок' },
  'about.text_1': { type: 'text', default: '«Гарант Интермодал» — профессиональный мультимодальный оператор. Мы организуем перевозки морем, по железной дороге и автотранспортом, управляем контейнерными линиями и оказываем агентские услуги в портах Дальнего Востока.', label: 'О нас: первый абзац' },
  'about.text_2': { type: 'text', default: 'Подход модульный: можно заказать перевозку «под ключ» или отдельные звенья цепочки — выбор за вами.', label: 'О нас: второй абзац' },
  'about.checklist': {
    type: 'list',
    label: 'О нас: список',
    fields: { text: { type: 'string', label: 'Пункт' } },
    default: [
      { text: 'Контейнерные, генеральные, насыпные и опасные грузы' },
      { text: 'Проектные грузы: негабарит и тяжеловесы' },
      { text: 'Направления: Китай, Корея, Япония, Россия, СНГ, Европа' },
    ],
  },
  'about.image': { type: 'image', default: '/img/ship-closeup.jpg', label: 'О нас: картинка' },
  'why.title': { type: 'string', default: 'Почему выбирают Гарант Интермодал', label: 'Преимущества: заголовок' },
  'why.lead': { type: 'string', default: 'Шесть причин, по которым клиенты остаются с нами годами.', label: 'Преимущества: подзаголовок' },
  'why.items': {
    type: 'list',
    label: 'Преимущества: карточки',
    fields: {
      icon: { type: 'icon', label: 'Иконка', default: 'circle-check' },
      title: { type: 'string', label: 'Заголовок' },
      text: { type: 'text', label: 'Текст' },
    },
    default: [
      { icon: 'users', title: 'Опыт 25+ лет', text: 'Каждый специалист команды — минимум 15 лет в отрасли. Знаем, как решить нестандартную задачу.' },
      { icon: 'train-front', title: 'Прямые договоры с РЖД', text: 'Договоры с РЖД и железными дорогами СНГ — гибкие тарифы и предсказуемые сроки.' },
      { icon: 'ship', title: 'Собственный флот', text: 'Два МПП типа «Сокол» и контейнеровоз «Гарант Нагаево» для каботажных линий.' },
      { icon: 'truck', title: 'Свой автопарк и площадка', text: 'Тягачи Scania, собственная площадка хранения порожних контейнеров.' },
      { icon: 'globe', title: 'Сеть агентов', text: 'Агенты в России, СНГ и Юго-Восточной Азии для сквозной организации перевозки.' },
      { icon: 'map-pin', title: 'Отслеживание груза', text: 'Ежедневные обновления статуса: вы всегда знаете, где находится груз.' },
    ],
  },
  'services.title': { type: 'string', default: 'Услуги', label: 'Услуги: заголовок' },
  'services.lead': { type: 'string', default: 'Основные направления. Полный перечень и подробности — на странице услуг.', label: 'Услуги: подзаголовок' },
  'services.cards': {
    type: 'list',
    label: 'Услуги: карточки',
    fields: {
      image: { type: 'image', label: 'Картинка' },
      title: { type: 'string', label: 'Заголовок' },
      text: { type: 'text', label: 'Текст' },
      link: { type: 'link', label: 'Ссылка «Подробнее»' },
    },
    default: [
      { title: 'Мультимодальные перевозки', text: 'Море, железная дорога и авто под одним договором или отдельными звеньями.', image: '/img/train.jpg', link: '/services/multimodal' },
      { title: 'Каботажные перевозки', text: 'Владивосток — Магадан, Корсаков, Петропавловск-Камчатский, Анадырь.', image: '/img/cabotage.jpg', link: '/services/multimodal' },
      { title: 'Проектные перевозки', text: 'Негабарит, тяжеловесы, целые заводы и объекты нефтегаза.', image: '/img/p-pipe.jpg', link: '/services/multimodal' },
      { title: 'Агентирование', text: 'Судовое и грузовое агентирование в портах Дальнего Востока.', image: '/img/port.jpg', link: '/services/multimodal' },
    ],
  },
  'geo.title': { type: 'string', default: 'География сервисов', label: 'География: заголовок' },
  'geo.text': { type: 'text', default: 'Порты Владивостока, Восточный (терминал ВСК) и Находка (терминал НМРП) — наши основные узлы. Отсюда идут морские каботажные линии на север и Сахалин, а контейнерные поезда — в Москву, Новосибирск и Екатеринбург.', label: 'География: текст' },
  'geo.checklist': {
    type: 'list',
    label: 'География: список',
    fields: { text: { type: 'string', label: 'Пункт' } },
    default: [
      { text: 'Каботаж: Магадан, Корсаков, Петропавловск-Камчатский, Анадырь' },
      { text: 'Импорт: Китай, Корея, Япония → Россия' },
      { text: 'Погранпереходы: Наушки, Забайкальск, Достык, Гродеково' },
    ],
  },
  'fleet.title': { type: 'string', default: 'Собственный флот', label: 'Флот: заголовок' },
  'fleet.text': { type: 'text', default: 'Два многоцелевых судна типа «Сокол» (2014 г.) и контейнеровоз «Гарант Нагаево» обеспечивают регулярные каботажные линии. Суда эксплуатирует «Восточное судоходное товарищество».', label: 'Флот: текст' },
  'fleet.image': { type: 'image', default: '/img/cabotage.jpg', label: 'Флот: картинка' },
  'projects.title': { type: 'string', default: 'Сложные перевозки — наша специализация', label: 'Проектные перевозки: заголовок' },
  'projects.lead': { type: 'string', default: 'Трубные колена, бульдозеры, рулонная сталь, краны, автомобили: берёмся за грузы, где нужна индивидуальная схема.', label: 'Проектные перевозки: подзаголовок' },
  'projects.gallery': {
    type: 'list',
    label: 'Проектные перевозки: галерея',
    fields: {
      image: { type: 'image', label: 'Картинка' },
      alt: { type: 'string', label: 'Описание картинки' },
    },
    default: [
      { image: '/img/p-pipe.jpg', alt: 'Трубные колена на железнодорожной платформе' },
      { image: '/img/p-dozer.jpg', alt: 'Бульдозер на платформе' },
      { image: '/img/p-crane.jpg', alt: 'Погрузка негабаритного оборудования' },
      { image: '/img/p-carbox.jpg', alt: 'Автомобиль в рамной конструкции для контейнера' },
    ],
  },
  'partners.title': { type: 'string', default: 'С кем мы работаем', label: 'Партнёры: заголовок' },
  'partners.lead': { type: 'string', default: 'Клиенты и партнёры, с которыми у нас выстроено сотрудничество.', label: 'Партнёры: подзаголовок' },
})

const seoTitle = useCmsElement(() => 'home', () => 'seo.title')
const seoDescription = useCmsElement(() => 'home', () => 'seo.description')
const phone = useCmsElement(() => 'site', () => 'office.vladivostok.phone')
const email = useCmsElement(() => 'site', () => 'office.vladivostok.email')
const address = useCmsElement(() => 'site', () => 'office.vladivostok.address')

usePageSeo({
  title: () => seoTitle.element.value?.value ?? '',
  description: () => seoDescription.element.value?.value ?? '',
  path: '/',
  image: '/img/hero.jpg',
  jsonLd: () => organizationJsonLd({
    phone: phone.element.value?.value ?? '',
    email: email.element.value?.value ?? '',
    address: address.element.value?.value ?? '',
  }),
})
</script>

<template>
  <div>
    <div class="hero">
      <CmsImage
        class="hero__bg"
        page="home"
        path="hero.image"
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
        <CmsText class="eyebrow" page="home" path="hero.eyebrow" />
        <CmsText as="h1" page="home" path="hero.title" />
        <CmsText as="p" class="hero__lead" page="home" path="hero.lead" />
        <div class="hero__actions">
          <NuxtLink class="btn btn--light" to="/contacts#request"><CmsText page="home" path="hero.cta_primary" /></NuxtLink>
          <NuxtLink class="btn btn--outline-light" to="/schedules"><CmsText page="home" path="hero.cta_secondary" /></NuxtLink>
        </div>
      </div>
    </div>

    <div class="stats">
      <div class="container">
        <CmsList v-slot="{ item }" as="ul" page="home" path="stats.items">
          <li>
            <AppIcon v-if="item.icon" class="stat-ico" :name="item.icon" />
            <b>{{ item.value }}</b>
            <span>{{ item.label }}</span>
          </li>
        </CmsList>
      </div>
    </div>

    <section>
      <div class="container">
        <div class="split">
          <div>
            <div class="section-head"><CmsText as="h2" page="home" path="about.title" /></div>
            <CmsText as="p" page="home" path="about.text_1" />
            <CmsText as="p" page="home" path="about.text_2" />
            <CmsList v-slot="{ item }" as="ul" class="checklist" page="home" path="about.checklist">
              <li>{{ item.text }}</li>
            </CmsList>
            <NuxtLink class="btn btn--ghost" to="/about">О компании</NuxtLink>
          </div>
          <figure>
            <CmsImage
              page="home"
              path="about.image"
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
          <CmsText id="why" as="h2" page="home" path="why.title" />
          <CmsText as="p" page="home" path="why.lead" />
        </div>
        <CmsList v-slot="{ item }" class="grid grid--3" page="home" path="why.items">
          <article class="adv">
            <span class="icon" aria-hidden="true"><AppIcon v-if="item.icon" :name="item.icon" /></span>
            <h3>{{ item.title }}</h3>
            <p>{{ item.text }}</p>
          </article>
        </CmsList>
      </div>
    </section>

    <section aria-labelledby="svc">
      <div class="container">
        <div class="section-head">
          <CmsText id="svc" as="h2" page="home" path="services.title" />
          <CmsText as="p" page="home" path="services.lead" />
        </div>
        <CmsList v-slot="{ item }" class="grid grid--4" page="home" path="services.cards">
          <component
            :is="item.link ? NuxtLink : 'div'"
            class="card"
            :to="item.link || undefined"
            style="color:inherit;text-decoration:none"
          >
            <ContentImage
              v-if="item.image"
              class="card__img"
              :src="item.image"
              alt=""
              width="600"
              height="375"
              :sizes="imageSizes.quarter"
              format="webp"
              loading="lazy"
            />
            <div class="card__body">
              <h3>{{ item.title }}</h3>
              <p>{{ item.text }}</p>
              <span v-if="item.link" class="card__more">Подробнее</span>
            </div>
          </component>
        </CmsList>
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
            <div class="section-head"><CmsText id="geo" as="h2" page="home" path="geo.title" /></div>
            <CmsText as="p" page="home" path="geo.text" />
            <CmsList v-slot="{ item }" as="ul" class="checklist" page="home" path="geo.checklist">
              <li>{{ item.text }}</li>
            </CmsList>
            <NuxtLink class="btn btn--ghost" to="/services#geography">Подробнее о географии</NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <section aria-labelledby="fl">
      <div class="container">
        <div class="split">
          <figure>
            <CmsImage
              page="home"
              path="fleet.image"
              alt="Судно «Гарант Нагаево» в ледовых условиях"
              width="1000"
              height="750"
              :sizes="imageSizes.half"
              format="webp"
              loading="lazy"
            />
          </figure>
          <div>
            <div class="section-head"><CmsText id="fl" as="h2" page="home" path="fleet.title" /></div>
            <CmsText as="p" page="home" path="fleet.text" />
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
          <CmsText id="pr" as="h2" page="home" path="projects.title" />
          <CmsText as="p" page="home" path="projects.lead" />
        </div>
        <CmsList v-slot="{ item }" class="gallery" page="home" path="projects.gallery">
          <ContentImage
            v-if="item.image"
            :src="item.image"
            :alt="item.alt"
            width="480"
            height="360"
            :sizes="imageSizes.quarter"
            format="webp"
            loading="lazy"
          />
        </CmsList>
        <p style="margin-top:28px"><NuxtLink class="btn" to="/project-cargo">Примеры проектных перевозок</NuxtLink></p>
      </div>
    </section>

    <section aria-labelledby="pt">
      <div class="container">
        <div class="section-head">
          <CmsText id="pt" as="h2" page="home" path="partners.title" />
          <CmsText as="p" page="home" path="partners.lead" />
        </div>
        <PartnersList />
      </div>
    </section>

    <CtaBanner />
  </div>
</template>

<style>
/* Hero photo drifts slower than the page while scrolling (CSS scroll-driven, no JS). Static on mobile,
   with reduced motion, or where animation-timeline is unsupported. */
.site .hero{overflow:hidden}
@keyframes hero-parallax{to{transform:translate3d(0,10%,0)}}
@media (min-width:768px) and (prefers-reduced-motion:no-preference){
  @supports (animation-timeline:scroll()){
    .site .hero .hero__bg{
      top:-12%;bottom:auto;height:124%;
      will-change:transform;
      animation:hero-parallax linear both;
      animation-timeline:scroll(root block);
      animation-range:0 700px;
    }
  }
}
</style>

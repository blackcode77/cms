<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { RouteMap } from '@widgets/route-map'
import { CmsList, CmsText, defineContentSchema, splitLines, useCmsElement } from '@entities/page-content'
import { usePageSeo } from '@shared/seo'
import { AppIcon } from '@shared/ui'
import { NuxtLink } from '#components'

const checklist = { text: { type: 'string', label: 'Пункт' } } as const

defineContentSchema('services', {
  'seo.title': { type: 'string', default: 'Услуги — Гарант Интермодал', label: 'SEO: заголовок страницы' },
  'seo.description': { type: 'string', default: 'Мультимодальные, каботажные и проектные перевозки, агентирование, таможенные услуги, хранение контейнеров, автотранспорт — полный перечень услуг Гарант Интермодал.', label: 'SEO: описание' },
  'hero.image': { type: 'image', default: '/img/hero.jpg', label: 'Первый экран: фон' },
  'hero.title': { type: 'string', default: 'Услуги группы компаний', label: 'Первый экран: заголовок' },
  'hero.lead': { type: 'string', default: 'Полный цикл логистики: от погрузки у отправителя до выдачи груза получателю.', label: 'Первый экран: текст' },
  'list.title': { type: 'string', default: 'Что мы делаем', label: 'Услуги: заголовок' },
  'list.lead': { type: 'string', default: 'Услуги можно заказывать целиком или по звеньям цепочки.', label: 'Услуги: подзаголовок' },
  'list.items': {
    type: 'list',
    label: 'Услуги: карточки',
    fields: {
      icon: { type: 'icon', label: 'Иконка', default: 'circle-check' },
      title: { type: 'string', label: 'Заголовок' },
      text: { type: 'text', label: 'Текст' },
      link: { type: 'link', label: 'Ссылка «Подробнее»' },
    },
    default: [
      { title: 'Мультимодальные перевозки', text: 'Сквозная перевозка море + железная дорога + авто. Один договор, одна ставка, один ответственный менеджер.', icon: 'train-front', link: '/services/multimodal' },
      { title: 'Грузовое агентирование', text: 'Экспортные документы, контроль и отслеживание контейнеров, тарифы подрядчиков, доставка до двери.', icon: 'anchor', link: '/services#agent' },
      { title: 'Судовое агентирование', text: 'Заход судов в порт, портовые сборы, буксиры, бункеровка, снабжение, ремонт, репатриация экипажей.', icon: 'ship', link: '/services#agent' },
      { title: 'Оперирование морскими контейнерными линиями', text: 'Управление линиями во внешней торговле и каботаже, регистрация линейных сервисов в Росморпорте.', icon: 'globe', link: '/fleet' },
      { title: 'Внутрипортовое экспедирование', text: 'Терминальные услуги и экспедирование во Владивостоке, Восточном и Находке.', icon: 'building' },
      { title: 'Экспедирование ЖД перевозок', text: 'Контейнерные поезда в Москву, Новосибирск, Екатеринбург; повагонные и групповые отправки.', icon: 'train-front' },
      { title: 'Каботажные перевозки', text: 'Владивосток/Восточный — Магадан, Корсаков, Петропавловск-Камчатский, Анадырь.', icon: 'ship' },
      { title: 'Проектные перевозки', text: 'Негабарит, тяжеловесы, RO-RO, танк-контейнеры, целые заводы и нефтегазовые объекты.', icon: 'package', link: '/project-cargo' },
      { title: 'Таможенные услуги', text: 'Услуги таможенного представителя, оформление импорта и экспорта.', icon: 'file-text', link: '/services#customs' },
      { title: 'Автотранспортные услуги', text: 'Собственные тягачи Scania и полуприцепы, доставка «последней мили».', icon: 'truck' },
      { title: 'Хранение и ремонт контейнеров', text: 'Собственная площадка хранения порожних контейнеров («Терминал Патрокл»).', icon: 'warehouse' },
      { title: 'Перевозка опасных грузов', text: 'Приём, оформление и перевозка опасных грузов, список запрещённых к приёму.', icon: 'triangle-exclamation-point', link: '/dangerous-goods' },
    ],
  },
  'geo.title': { type: 'string', default: 'География сервисов', label: 'География: заголовок' },
  'geo.groups': {
    type: 'list',
    label: 'География: группы',
    fields: { title: { type: 'string', label: 'Заголовок' }, items: { type: 'lines', label: 'Пункты' } },
    default: [
      { title: 'Порты и терминалы', items: 'Владивосток — все порты и терминалы\nВосточный — терминал ВСК\nНаходка — терминал НМРП' },
      { title: 'Каботаж', items: 'Магадан, Корсаков (Сахалин)\nПетропавловск-Камчатский, Анадырь' },
      { title: 'Международные направления', items: 'Китай, Корея, Япония → Россия\nПогранпереходы: Наушки, Забайкальск, Достык, Гродеково' },
    ],
  },
  'process.title': { type: 'string', default: 'Как получить услугу', label: 'Процесс: заголовок' },
  'process.lead': { type: 'string', default: 'Простая процедура из пяти шагов.', label: 'Процесс: подзаголовок' },
  'process.steps': {
    type: 'list',
    label: 'Процесс: шаги',
    fields: { title: { type: 'string', label: 'Шаг' }, text: { type: 'text', label: 'Текст' } },
    default: [
      { title: 'Заявка', text: 'Опишите груз, маршрут и сроки — через форму или по телефону.' },
      { title: 'Расчёт', text: 'Подготовим ставку и схему перевозки в течение рабочего дня.' },
      { title: 'Договор', text: 'Согласуем условия, подпишем договор или присоединим к действующему.' },
      { title: 'Перевозка', text: 'Организуем все звенья, документы и таможню.' },
      { title: 'Контроль', text: 'Ежедневно сообщаем статус и сопровождаем до выдачи груза.' },
    ],
  },
  'agent.ship.title': { type: 'string', default: 'Судовое агентирование', label: 'Судовое агентирование: заголовок' },
  'agent.ship.items': {
    type: 'list',
    label: 'Судовое агентирование: список',
    fields: checklist,
    default: [
      { text: 'Заход в порт, портовые сборы, буксиры, швартовка' },
      { text: 'Бункер, вода, провизия, ремонт судов' },
      { text: 'Визы, билеты, отели, медпомощь экипажам' },
      { text: 'Репатриация экипажей, смета на заход' },
    ],
  },
  'agent.cargo.title': { type: 'string', default: 'Грузовое агентирование и таможня', label: 'Грузовое агентирование: заголовок' },
  'agent.cargo.items': {
    type: 'list',
    label: 'Грузовое агентирование: список',
    fields: checklist,
    default: [
      { text: 'Экспортные документы, букинг, контроль контейнеров' },
      { text: 'Тарифы подрядчиков, доставка до двери' },
      { text: 'Таможенный досмотр, перегрузка' },
      { text: 'Услуги таможенного представителя' },
    ],
  },
  'related.items': {
    type: 'list',
    label: 'Ссылки внизу страницы',
    fields: {
      icon: { type: 'icon', label: 'Иконка', default: 'circle-check' },
      title: { type: 'string', label: 'Заголовок' },
      text: { type: 'text', label: 'Текст' },
      link: { type: 'link', label: 'Ссылка' },
      more: { type: 'string', label: 'Текст ссылки', default: 'Подробнее' },
    },
    default: [
      { title: 'Расписания и тарифы', text: 'Расписания движения флота, публичные тарифы, локальные сборы в портах, инструкция по букингу.', icon: 'clock', link: '/schedules', more: 'Перейти' },
      { title: 'Примеры сложных перевозок', text: 'Контейнерные и конвенциональные грузы, включая тяжеловесные и негабаритные.', icon: 'package', link: '/project-cargo', more: 'Смотреть' },
    ],
  },
})

const seoTitle = useCmsElement(() => 'services', () => 'seo.title')
const seoDescription = useCmsElement(() => 'services', () => 'seo.description')

usePageSeo({
  title: () => seoTitle.element.value?.value ?? '',
  description: () => seoDescription.element.value?.value ?? '',
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
    <PageHero cms-page="services" :crumbs="[{ label: 'Услуги' }]" />
    <section>
      <div class="container">
        <div class="section-head">
          <CmsText as="h2" page="services" path="list.title" />
          <CmsText as="p" page="services" path="list.lead" />
        </div>
        <CmsList v-slot="{ item }" class="grid grid--3" page="services" path="list.items">
          <component
            :is="item.link ? NuxtLink : 'div'"
            class="card card--plain"
            :to="item.link || undefined"
            style="color:inherit;text-decoration:none"
          >
            <span class="icon" aria-hidden="true"><AppIcon v-if="item.icon" :name="item.icon" /></span>
            <h3>{{ item.title }}</h3>
            <p style="color:#4a4647;font-size:.95rem">{{ item.text }}</p>
            <span v-if="item.link" class="card__more">Подробнее</span>
          </component>
        </CmsList>
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
            <div class="section-head"><CmsText as="h2" page="services" path="geo.title" /></div>
            <CmsList v-slot="{ item }" page="services" path="geo.groups">
              <h4>{{ item.title }}</h4>
              <ul class="checklist">
                <li v-for="line in splitLines(item.items)" :key="line">{{ line }}</li>
              </ul>
            </CmsList>
          </div>
        </div>
      </div>
    </section>

    <section id="process">
      <div class="container">
        <div class="section-head">
          <CmsText as="h2" page="services" path="process.title" />
          <CmsText as="p" page="services" path="process.lead" />
        </div>
        <CmsList v-slot="{ item }" as="ol" class="steps" page="services" path="process.steps">
          <li>
            <b>{{ item.title }}</b>
            <p>{{ item.text }}</p>
          </li>
        </CmsList>
      </div>
    </section>

    <section id="agent" class="section--grey">
      <div class="container">
        <div class="grid grid--2">
          <article class="card card--plain">
            <CmsText as="h3" page="services" path="agent.ship.title" />
            <CmsList v-slot="{ item }" as="ul" class="checklist" page="services" path="agent.ship.items">
              <li>{{ item.text }}</li>
            </CmsList>
          </article>
          <article id="customs" class="card card--plain">
            <CmsText as="h3" page="services" path="agent.cargo.title" />
            <CmsList v-slot="{ item }" as="ul" class="checklist" page="services" path="agent.cargo.items">
              <li>{{ item.text }}</li>
            </CmsList>
          </article>
        </div>
      </div>
    </section>

    <section>
      <div class="container">
        <CmsList v-slot="{ item }" class="grid grid--2" page="services" path="related.items">
          <component
            :is="item.link ? NuxtLink : 'div'"
            class="card card--plain"
            :to="item.link || undefined"
            style="color:inherit;text-decoration:none"
          >
            <span class="icon" aria-hidden="true"><AppIcon v-if="item.icon" :name="item.icon" /></span>
            <h3>{{ item.title }}</h3>
            <p>{{ item.text }}</p>
            <span v-if="item.link && item.more" class="card__more">{{ item.more }}</span>
          </component>
        </CmsList>
      </div>
    </section>
    <CtaBanner />
  </div>
</template>

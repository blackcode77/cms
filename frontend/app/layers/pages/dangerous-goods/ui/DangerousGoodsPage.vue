<script setup lang="ts">
import { CtaBanner } from '@widgets/cta-banner'
import { PageHero } from '@widgets/page-hero'
import { CmsList, CmsText, defineContentSchema, documentFormat, useCmsElement } from '@entities/page-content'
import { usePageSeo } from '@shared/seo'

const PAGE = 'dangerous-goods'

defineContentSchema(PAGE, {
  'seo.title': { type: 'string', default: 'Перевозка опасных грузов — Гарант Интермодал', label: 'SEO: заголовок страницы' },
  'seo.description': { type: 'string', default: 'Порядок приёма, классы опасности и список запрещённых к приёму опасных грузов. Гарант Интермодал.', label: 'SEO: описание' },
  'hero.image': { type: 'image', default: '/img/p-tank.jpg', label: 'Первый экран: фон' },
  'hero.title': { type: 'string', default: 'Перевозка опасных грузов', label: 'Первый экран: заголовок' },
  'hero.lead': { type: 'string', default: 'Принимаем опасные грузы в контейнерах и тарных местах по правилам МОПОГ/IMDG и требованиям перевозчиков.', label: 'Первый экран: текст' },
  'steps.title': { type: 'string', default: 'Порядок приёма', label: 'Порядок приёма: заголовок' },
  'steps.items': {
    type: 'list',
    label: 'Порядок приёма: шаги',
    fields: { title: { type: 'string', label: 'Шаг' }, text: { type: 'text', label: 'Текст' } },
    default: [
      { title: 'Запрос', text: 'Наименование, номер ООН, класс, масса, упаковка.' },
      { title: 'Согласование', text: 'Проверяем допуск груза на линии и в портах.' },
      { title: 'Документы', text: 'Паспорт безопасности, декларация, сертификат упаковки.' },
      { title: 'Букинг', text: 'Подтверждение ставки и условий.' },
    ],
  },
  'classes.title': { type: 'string', default: 'Классы опасности', label: 'Классы опасности: заголовок' },
  'classes.items': {
    type: 'list',
    label: 'Классы опасности: таблица',
    fields: { code: { type: 'string', label: 'Класс' }, name: { type: 'string', label: 'Название' } },
    default: [
      { code: '1', name: 'Взрывчатые вещества' },
      { code: '2', name: 'Газы' },
      { code: '3', name: 'Легковоспламеняющиеся жидкости' },
      { code: '4', name: 'Легковоспламеняющиеся твёрдые вещества' },
      { code: '5', name: 'Окисляющие вещества и органические пероксиды' },
      { code: '6', name: 'Токсичные и инфекционные вещества' },
      { code: '7', name: 'Радиоактивные материалы' },
      { code: '8', name: 'Коррозионные вещества' },
      { code: '9', name: 'Прочие опасные вещества' },
    ],
  },
  'forbidden.title': { type: 'string', default: 'Запрещены к приёму', label: 'Запрещённые грузы: заголовок' },
  'forbidden.items': {
    type: 'list',
    label: 'Запрещённые грузы: список',
    fields: { text: { type: 'string', label: 'Пункт' } },
    default: [
      { text: 'Взрывчатые вещества класса 1' },
      { text: 'Радиоактивные материалы класса 7' },
      { text: 'Инфекционные вещества категории А' },
      { text: 'Грузы без паспорта безопасности и маркировки' },
      { text: 'Грузы в повреждённой или неутверждённой таре' },
    ],
  },
  'forbidden.note': { type: 'string', default: 'Полный список запрещённых грузов будет загружен документом.', label: 'Запрещённые грузы: примечание' },
  'aside.title': { type: 'string', default: 'Консультация по грузу', label: 'Боковая колонка: заголовок' },
  'aside.text': { type: 'text', default: 'Опишите груз — подскажем, возможна ли перевозка, и рассчитаем стоимость.', label: 'Боковая колонка: текст' },
  'aside.button': { type: 'string', default: 'Задать вопрос', label: 'Боковая колонка: кнопка' },
  'aside.docs.title': { type: 'string', default: 'Документы', label: 'Документы: заголовок' },
  'aside.docs': {
    type: 'list',
    label: 'Документы: список',
    fields: {
      title: { type: 'string', label: 'Название' },
      file: { type: 'file', label: 'Файл', sizeField: 'size' },
      size: { type: 'string', hidden: true },
    },
    default: [{ title: 'Памятка по ОГ' }],
  },
})

const seoTitle = useCmsElement(() => PAGE, () => 'seo.title')
const seoDescription = useCmsElement(() => PAGE, () => 'seo.description')

usePageSeo({
  title: () => seoTitle.element.value?.value ?? '',
  description: () => seoDescription.element.value?.value ?? '',
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
    <PageHero :cms-page="PAGE" :crumbs="[{ label: 'Главная', to: '/' }, { label: 'Опасные грузы' }]" />
    <section>
      <div class="container">
        <div class="layout">
          <article class="prose">
            <CmsText as="h2" :page="PAGE" path="steps.title" />
            <CmsList v-slot="{ item }" as="ol" class="steps" style="grid-template-columns:1fr 1fr" :page="PAGE" path="steps.items">
              <li><b>{{ item.title }}</b><p>{{ item.text }}</p></li>
            </CmsList>
            <CmsText as="h2" :page="PAGE" path="classes.title" />
            <div class="table-wrap">
              <table>
                <thead><tr><th>Класс</th><th>Название</th></tr></thead>
                <CmsList v-slot="{ item }" as="tbody" :page="PAGE" path="classes.items">
                  <tr>
                    <td><b>{{ item.code }}</b></td>
                    <td>{{ item.name }}</td>
                  </tr>
                </CmsList>
              </table>
            </div>
            <CmsText id="forbidden" as="h2" :page="PAGE" path="forbidden.title" />
            <CmsList v-slot="{ item }" as="ul" class="checklist checklist--no" :page="PAGE" path="forbidden.items">
              <li>{{ item.text }}</li>
            </CmsList>
            <CmsText as="p" style="color:var(--grey)" :page="PAGE" path="forbidden.note" />
          </article>
          <aside class="aside-box">
            <CmsText as="h3" :page="PAGE" path="aside.title" />
            <CmsText as="p" :page="PAGE" path="aside.text" />
            <NuxtLink class="btn" to="/contacts#request"><CmsText :page="PAGE" path="aside.button" /></NuxtLink>
            <hr style="border:0;border-top:1px solid var(--line);margin:20px 0">
            <p><CmsText as="b" :page="PAGE" path="aside.docs.title" /></p>
            <CmsList v-slot="{ item }" as="ul" class="docs" :page="PAGE" path="aside.docs">
              <li style="grid-template-columns:44px 1fr">
                <span class="ext">{{ documentFormat(item.file) }}</span>
                <div>
                  <a v-if="item.file" :href="item.file" download><b>{{ item.title }}</b></a>
                  <b v-else>{{ item.title }}</b>
                  <small v-if="item.file && item.size">{{ item.size }}</small>
                </div>
              </li>
            </CmsList>
          </aside>
        </div>
      </div>
    </section>
    <CtaBanner />
  </div>
</template>

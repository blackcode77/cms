<script setup lang="ts">
import { CmsText, defineContentSchema } from '@entities/page-content'

defineContentSchema('site', {
  'cta.title': { type: 'string', default: 'Нужен расчёт ставки?', label: 'Баннер: заголовок' },
  'cta.text': { type: 'text', default: 'Оставьте маршрут и параметры груза — менеджер ответит в рабочее время.', label: 'Баннер: текст' },
  'cta.button': { type: 'string', default: 'Запросить расчёт', label: 'Баннер: кнопка' },
})
</script>

<template>
  <section class="cta">
    <div class="container">
      <div>
        <CmsText as="h2" page="site" path="cta.title" />
        <CmsText as="p" page="site" path="cta.text" />
      </div>
      <NuxtLink class="btn btn--light" to="/contacts#request"><CmsText page="site" path="cta.button" /></NuxtLink>
    </div>
  </section>
</template>

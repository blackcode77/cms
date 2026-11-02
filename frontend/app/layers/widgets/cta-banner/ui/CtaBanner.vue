<script setup lang="ts">
withDefaults(defineProps<{ title?: string, text?: string }>(), {
  title: 'Нужен расчёт ставки?',
  text: 'Оставьте маршрут и параметры груза — менеджер ответит в рабочее время.',
})
</script>

<template>
  <section class="cta">
    <div class="container">
      <div>
        <h2>{{ title }}</h2>
        <p>{{ text }}</p>
      </div>
      <NuxtLink class="btn btn--light" to="/contacts#request">Запросить расчёт</NuxtLink>
    </div>
  </section>
</template>

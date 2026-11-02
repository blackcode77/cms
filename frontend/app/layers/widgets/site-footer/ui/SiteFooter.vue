<script setup lang="ts">
import { navigation, offices, telHref } from '@shared/config'

const sections = navigation.filter(item => item.to !== '/')
const services = [
  { label: 'Мультимодальные перевозки', to: '/services/multimodal' },
  { label: 'Агентирование', to: '/services#agent' },
  { label: 'Проектные перевозки', to: '/project-cargo' },
  { label: 'Таможенные услуги', to: '/services#customs' },
  { label: 'Расписания', to: '/schedules' },
]
const [vladivostok, moscow] = offices
const year = new Date().getFullYear()
</script>

<template>
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-logo">
          <img src="/img/logo-white.png" alt="Garant Intermodal" width="378" height="190" loading="lazy">
          <p>Профессиональный мультимодальный оператор. Кратчайший, простой и эффективный маршрут для вашего груза.</p>
        </div>
        <div>
          <h3>Разделы</h3>
          <ul>
            <li v-for="item in sections" :key="item.to">
              <NuxtLink :to="item.to">{{ item.label }}</NuxtLink>
            </li>
          </ul>
        </div>
        <div>
          <h3>Услуги</h3>
          <ul>
            <li v-for="item in services" :key="item.to">
              <NuxtLink :to="item.to">{{ item.label }}</NuxtLink>
            </li>
          </ul>
        </div>
        <div>
          <h3>Контакты</h3>
          <address style="font-style:normal">
            <p>
              Владивосток: {{ vladivostok!.address }}<br>
              <a :href="telHref(vladivostok!.phone!)">{{ vladivostok!.phone }}</a>
            </p>
            <p>
              Москва: Киевское ш., 1, БП «Румянцево»<br>
              <a :href="telHref(moscow!.phone!)">{{ moscow!.phone }}</a>
            </p>
            <p>{{ vladivostok!.hours }}</p>
          </address>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2014–{{ year }} ООО «Гарант Интермодал»</span>
        <span>Created to lead</span>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { CmsText, defineContentSchema, useCmsElement } from '@entities/page-content'
import { navigation, officeElements, telHref } from '@shared/config'

defineContentSchema('site', {
  ...officeElements,
  'footer.about': { type: 'text', default: 'Профессиональный мультимодальный оператор. Кратчайший, простой и эффективный маршрут для вашего груза.', label: 'Футер: о компании' },
  'footer.address_moscow': { type: 'string', default: 'Киевское ш., 1, БП «Румянцево»', label: 'Футер: адрес в Москве' },
})

const sections = navigation.filter(item => item.to !== '/')
const services = [
  { label: 'Мультимодальные перевозки', to: '/services/multimodal' },
  { label: 'Агентирование', to: '/services#agent' },
  { label: 'Проектные перевозки', to: '/project-cargo' },
  { label: 'Таможенные услуги', to: '/services#customs' },
  { label: 'Расписания', to: '/schedules' },
]
const vladivostokPhone = useCmsElement(() => 'site', () => 'office.vladivostok.phone')
const moscowPhone = useCmsElement(() => 'site', () => 'office.moscow.phone')
const telOf = (el: typeof vladivostokPhone) => telHref(el.element.value?.value ?? '')
const year = new Date().getFullYear()
</script>

<template>
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-logo">
          <img src="/img/logo-white.png" alt="Garant Intermodal" width="378" height="190" loading="lazy">
          <CmsText as="p" page="site" path="footer.about" />
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
              Владивосток: <CmsText page="site" path="office.vladivostok.address" /><br>
              <a v-if="vladivostokPhone.visible.value" :href="telOf(vladivostokPhone)"><CmsText page="site" path="office.vladivostok.phone" /></a>
            </p>
            <p>
              Москва: <CmsText page="site" path="footer.address_moscow" /><br>
              <a v-if="moscowPhone.visible.value" :href="telOf(moscowPhone)"><CmsText page="site" path="office.moscow.phone" /></a>
            </p>
            <p><CmsText page="site" path="office.vladivostok.hours" /></p>
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

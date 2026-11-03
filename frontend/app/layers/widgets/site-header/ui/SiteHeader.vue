<script setup lang="ts">
import { CmsText, defineContentSchema, useCmsElement } from '@entities/page-content'
import { navigation, officeElements, telHref } from '@shared/config'

defineContentSchema('site', officeElements)

const route = useRoute()
const open = ref(false)

const vladivostokPhone = useCmsElement(() => 'site', () => 'office.vladivostok.phone')
const moscowPhone = useCmsElement(() => 'site', () => 'office.moscow.phone')
const email = useCmsElement(() => 'site', () => 'office.vladivostok.email')
const telOf = (el: typeof email) => telHref(el.element.value?.value ?? '')
const isCurrent = (to: string) => (to === '/' ? route.path === '/' : route.path === to || route.path.startsWith(`${to}/`))

watch(() => route.path, () => {
  open.value = false
})
</script>

<template>
  <div>
    <a class="skip" href="#main">Перейти к содержимому</a>
    <div class="topbar">
      <div class="container">
        <div class="topbar__contacts">
          <a v-if="vladivostokPhone.visible.value" :href="telOf(vladivostokPhone)">
            <CmsText page="site" path="office.vladivostok.phone" /> · Владивосток
          </a>
          
          <a v-if="moscowPhone.visible.value" class="hide-sm" :href="telOf(moscowPhone)"><CmsText page="site" path="office.moscow.phone" /> · Москва</a>
          <a v-if="email.visible.value" class="hide-sm" :href="`mailto:${email.element.value?.value}`"><CmsText page="site" path="office.vladivostok.email" /></a>
        </div>
        <div class="lang" aria-label="Язык">
          <span class="is-active" aria-current="true">RU</span>
          <span title="Английская версия — позже">EN</span>
        </div>
      </div>
    </div>
    <header class="site-header">
      <div class="container">
        <NuxtLink class="logo" to="/" aria-label="Гарант Интермодал — на главную">
          <img src="/img/logo.png" alt="Garant Intermodal" width="378" height="190">
        </NuxtLink>
        <button
          class="nav-toggle"
          type="button"
          :aria-expanded="open"
          aria-controls="nav"
          @click="open = !open"
        >
          Меню
        </button>
        <nav id="nav" class="nav" :class="{ 'is-open': open }" aria-label="Основная навигация">
          <ul>
            <li v-for="item in navigation" :key="item.to">
              <NuxtLink :to="item.to" :aria-current="isCurrent(item.to) ? 'page' : undefined">
                {{ item.label }}
              </NuxtLink>
            </li>
          </ul>
        </nav>
      </div>
    </header>
  </div>
</template>

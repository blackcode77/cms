<script setup lang="ts">
import { navigation, offices, telHref } from '@shared/config'

const route = useRoute()
const open = ref(false)

const [vladivostok, moscow] = offices
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
          <a :href="telHref(vladivostok!.phone!)">{{ vladivostok!.phone }} · Владивосток</a>
          <a class="hide-sm" :href="telHref(moscow!.phone!)">{{ moscow!.phone }} · Москва</a>
          <a class="hide-sm" :href="`mailto:${vladivostok!.email}`">{{ vladivostok!.email }}</a>
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

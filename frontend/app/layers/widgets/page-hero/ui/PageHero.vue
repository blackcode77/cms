<script setup lang="ts">
import { imageSizes } from '@shared/config'

defineProps<{
  title: string
  lead?: string
  /** Site-relative image path, e.g. `/img/train.jpg`. */
  image: string
  /** Breadcrumb trail after "Главная"; the last item is the current page. */
  crumbs: { label: string, to?: string }[]
}>()
</script>

<template>
  <div class="page-hero">
    <NuxtImg
      class="page-hero__bg"
      :src="image"
      alt=""
      width="1600"
      height="600"
      :sizes="imageSizes.full"
      format="webp"
      quality="70"
      fetchpriority="high"
      loading="eager"
    />
    <div class="container">
      <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <NuxtLink to="/">Главная</NuxtLink>
        <template v-for="crumb in crumbs" :key="crumb.label">
          <span aria-hidden="true"> / </span>
          <NuxtLink v-if="crumb.to" :to="crumb.to">{{ crumb.label }}</NuxtLink>
          <span v-else>{{ crumb.label }}</span>
        </template>
      </nav>
      <h1>{{ title }}</h1>
      <p v-if="lead">{{ lead }}</p>
    </div>
  </div>
</template>

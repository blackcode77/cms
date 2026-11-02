<script setup lang="ts">
import { CmsImage, CmsText } from '@entities/page-content'
import { imageSizes } from '@shared/config'

/**
 * With `cmsPage` the title, lead and image are editable Content elements of that page
 * (`hero.title`, `hero.lead`, `hero.image`, declared by the page); otherwise the props are used.
 */
defineProps<{
  title?: string
  lead?: string
  /** Site-relative image path, e.g. `/img/train.jpg`. */
  image?: string
  /** Page key whose `hero.*` Content elements to render instead of the props. */
  cmsPage?: string
  /** Breadcrumb trail after "Главная"; the last item is the current page. */
  crumbs: { label: string, to?: string }[]
}>()
</script>

<template>
  <div class="page-hero">
    <CmsImage
      v-if="cmsPage"
      class="page-hero__bg"
      :page="cmsPage"
      path="hero.image"
      width="1600"
      height="600"
      :sizes="imageSizes.full"
      format="webp"
      quality="70"
      fetchpriority="high"
      loading="eager"
    />
    <NuxtImg
      v-else
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
      <CmsText v-if="cmsPage" as="h1" :page="cmsPage" path="hero.title" />
      <h1 v-else>{{ title }}</h1>
      <template v-if="cmsPage">
        <CmsText as="p" :page="cmsPage" path="hero.lead" />
      </template>
      <p v-else-if="lead">{{ lead }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { CmsList, defineContentSchema } from '@entities/page-content'

/** Partner names shared by every page that shows them (one list under the `site` page key). */
defineContentSchema('site', {
  'partners.items': {
    type: 'list',
    label: 'Партнёры (общий список для всех страниц)',
    fields: { name: { type: 'string', label: 'Название' } },
    default: ['MSC', 'Transcontainer', 'Dong Young', 'Eurosib', 'SUNSCO', 'Tenpoint', 'ISACO'].map(name => ({ name })),
  },
})
</script>

<template>
  <CmsList v-slot="{ item }" as="ul" class="partners" page="site" path="partners.items">
    <li>{{ item.name }}</li>
  </CmsList>
</template>

<script setup lang="ts">
// Routing only: the page itself lives in the FSD layer.
import { DangerousGoodsPage } from '@pages/dangerous-goods'

definePageMeta({ cmsPage: 'dangerous-goods' })
</script>

<template>
  <DangerousGoodsPage />
</template>

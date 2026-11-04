<script setup lang="ts">
// Routing only: the page itself lives in the FSD layer.
import { ServiceDetailPage } from '@pages/service-detail'

// Only /services/multimodal exists (other slugs are 404), so its page key is fixed.
definePageMeta({ cmsPage: 'service-multimodal' })
</script>

<template>
  <ServiceDetailPage />
</template>

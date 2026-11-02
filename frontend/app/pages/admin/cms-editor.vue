<script setup lang="ts">
import { AdminCmsEditorPage } from '@pages/admin-cms-editor'

// Embedded as an iframe on the public site, so it uses the chrome-less layout.
definePageMeta({ layout: 'admin-embed', permission: 'edit_page' })
</script>

<template>
  <AdminCmsEditorPage />
</template>

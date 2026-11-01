<template>
  <UDashboardGroup>
    <UDashboardSidebar>
      <template #header>
        <span class="font-semibold">CMS admin</span>
      </template>
      <UNavigationMenu
        orientation="vertical"
        :items="[{ label: 'Dashboard', icon: 'i-lucide-layout-dashboard', to: '/admin' }]"
      />
    </UDashboardSidebar>
    <slot />
  </UDashboardGroup>
</template>

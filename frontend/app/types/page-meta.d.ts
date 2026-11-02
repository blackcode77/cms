import type { Permission } from '@entities/session'

declare module '#app' {
  interface PageMeta {
    /** Admin pages: the Permission a User needs to open the route (checked by the admin route guard). */
    permission?: Permission
  }
}

declare module 'vue-router' {
  interface RouteMeta {
    permission?: Permission
  }
}

export {}

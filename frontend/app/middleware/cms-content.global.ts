import { usePageContent } from '@entities/page-content'

declare module '#app' {
  interface PageMeta {
    /** Page key whose editable content this public route renders (the shared `site` content is always loaded). */
    cmsPage?: string
  }
}

// Loads saved content before the page renders, so SSR output already contains the edited values.
export default defineNuxtRouteMiddleware(async (to) => {
  if (to.path.startsWith('/admin')) return
  const pages = to.meta.cmsPage ? ['site', to.meta.cmsPage] : ['site']
  await usePageContent().ensureLoaded(pages)
})

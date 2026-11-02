export interface PageSeo {
  title: string
  description: string
  /** Site-relative path, e.g. `/about`. */
  path: string
  /** Site-relative image path used for og:image, e.g. `/img/hero.jpg`. */
  image?: string
  /** schema.org structured data rendered as JSON-LD. */
  jsonLd?: Record<string, unknown>
}

/** Sets title, description, canonical, Open Graph and JSON-LD for a public page. */
export function usePageSeo(seo: PageSeo): void {
  const siteUrl = useRuntimeConfig().public.siteUrl.replace(/\/$/, '')
  const url = `${siteUrl}${seo.path === '/' ? '/' : seo.path}`
  const image = `${siteUrl}${seo.image ?? '/img/hero.jpg'}`

  useSeoMeta({
    title: seo.title,
    description: seo.description,
    ogType: 'website',
    ogLocale: 'ru_RU',
    ogTitle: seo.title,
    ogDescription: seo.description,
    ogUrl: url,
    ogImage: image,
    twitterCard: 'summary_large_image',
  })

  useHead({
    link: [{ rel: 'canonical', href: url }],
    script: seo.jsonLd
      ? [{ type: 'application/ld+json', innerHTML: JSON.stringify(seo.jsonLd) }]
      : [],
  })
}

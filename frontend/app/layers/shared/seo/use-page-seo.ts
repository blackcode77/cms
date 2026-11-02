import type { MaybeRefOrGetter } from 'vue'

export interface PageSeo {
  /** May be a ref or getter so edited content is picked up. */
  title: MaybeRefOrGetter<string>
  description: MaybeRefOrGetter<string>
  /** Site-relative path, e.g. `/about`. */
  path: string
  /** Site-relative image path used for og:image, e.g. `/img/hero.jpg`. */
  image?: string
  /** schema.org structured data rendered as JSON-LD. */
  jsonLd?: MaybeRefOrGetter<Record<string, unknown>>
}

/** schema.org Organization built from the (possibly edited) contact details. */
export function organizationJsonLd(contact: { phone: string, email: string, address: string }): Record<string, unknown> {
  return {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    'name': 'ООО «Гарант Интермодал»',
    'url': 'https://garant-intermodal.ru/',
    'logo': 'https://garant-intermodal.ru/img/logo.png',
    'foundingDate': '2014',
    'telephone': contact.phone,
    'email': contact.email,
    'address': {
      '@type': 'PostalAddress',
      'addressLocality': 'Владивосток',
      'streetAddress': contact.address,
      'addressCountry': 'RU',
    },
  }
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
      ? [{ type: 'application/ld+json', innerHTML: () => JSON.stringify(toValue(seo.jsonLd)) }]
      : [],
  })
}

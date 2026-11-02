/**
 * `sizes` presets for <NuxtImg> in @nuxt/image syntax (`<screen>:<size>`, mobile-first).
 * Screen names come from `image.screens` in nuxt.config.ts. Keep these in sync with the CSS layout.
 */
export const imageSizes = {
  /** Full-bleed images (hero backgrounds). */
  full: 'xs:100vw sm:100vw md:100vw lg:100vw xl:100vw xxl:100vw 2xl:100vw',
  /** One half of a two-column `.split` / `.geo` block, full width below `lg`. */
  half: 'xs:100vw sm:100vw md:100vw lg:560px',
  /** Third of the container (`.grid--3`): 1 col mobile, 2 cols tablet. */
  third: 'xs:100vw sm:100vw md:50vw lg:380px',
  /** Quarter of the container (`.grid--4`, `.gallery`). */
  quarter: 'xs:100vw sm:50vw md:50vw lg:285px',
  /** Narrow sidebar / small thumbnail. */
  thumb: 'xs:100vw sm:320px md:320px lg:320px',
} as const

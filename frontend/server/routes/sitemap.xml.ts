import { extraRoutes, navigation } from '../../app/layers/shared/config/site'

export default defineEventHandler((event) => {
  const siteUrl = useRuntimeConfig(event).public.siteUrl.replace(/\/$/, '')
  const paths = [...navigation.map(item => item.to), ...extraRoutes]
  const urls = paths.map(path => `  <url><loc>${siteUrl}${path === '/' ? '/' : path}</loc></url>`).join('\n')
  setHeader(event, 'content-type', 'application/xml; charset=utf-8')
  return `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${urls}\n</urlset>\n`
})

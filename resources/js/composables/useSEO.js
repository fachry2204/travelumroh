import { useAppStore } from '@/stores/app'

export function useSEO() {
  const setMeta = (title, description, image = null, type = 'article') => {
    const appStore = useAppStore()
    const appName = appStore.settings.app_name || 'Travel Umroh'
    const fullTitle = title ? `${title} | ${appName}` : appName
    const cleanDesc = (description || `Layanan keberangkatan umroh terpercaya bersama ${appName}.`).replace(/<[^>]*>?/gm, '').trim()

    // 1. Title
    document.title = fullTitle

    // Helper to set or create meta tag
    const updateTag = (selector, attrName, attrVal, contentVal) => {
      let el = document.querySelector(selector)
      if (!el) {
        el = document.createElement('meta')
        el.setAttribute(attrName, attrVal)
        document.head.appendChild(el)
      }
      el.setAttribute('content', contentVal)
    }

    // 2. Standard Meta Description
    updateTag('meta[name="description"]', 'name', 'description', cleanDesc)

    // 3. Open Graph (Facebook, WhatsApp, LinkedIn, Telegram)
    updateTag('meta[property="og:site_name"]', 'property', 'og:site_name', appName)
    updateTag('meta[property="og:type"]', 'property', 'og:type', type)
    updateTag('meta[property="og:title"]', 'property', 'og:title', fullTitle)
    updateTag('meta[property="og:description"]', 'property', 'og:description', cleanDesc)
    updateTag('meta[property="og:url"]', 'property', 'og:url', window.location.href)

    // 4. Twitter Card
    updateTag('meta[name="twitter:card"]', 'name', 'twitter:card', 'summary_large_image')
    updateTag('meta[name="twitter:title"]', 'name', 'twitter:title', fullTitle)
    updateTag('meta[name="twitter:description"]', 'name', 'twitter:description', cleanDesc)

    // 5. Image (OG Image & Twitter Image)
    const imgPath = image || appStore.settings.app_logo
    if (imgPath) {
      const fullImgUrl = imgPath.startsWith('http') 
        ? imgPath 
        : `${window.location.origin}/storage/${imgPath.replace(/^\//, '')}`
      
      updateTag('meta[property="og:image"]', 'property', 'og:image', fullImgUrl)
      updateTag('meta[name="twitter:image"]', 'name', 'twitter:image', fullImgUrl)
    }
  }

  return { setMeta }
}

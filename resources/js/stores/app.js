import { defineStore } from 'pinia'
import api from '@/plugins/axios'

export const useAppStore = defineStore('app', {
  state: () => ({
    settings: {
      app_name: 'Travel Umroh',
      app_logo: null,
      app_favicon: null,
      app_phone: '',
      app_email: '',
      app_address: '',
    },
    isLoaded: false
  }),
  actions: {
    async fetchSettings(force = false) {
      if (this.isLoaded && !force) return
      try {
        const { data } = await api.get('/public/settings')
        if (data.success && data.data) {
          this.settings = { ...this.settings, ...data.data }
          
          // Update Document Title
          if (this.settings.app_name) {
            document.title = this.settings.app_name
          }
          // Update Favicon dynamically
          if (this.settings.app_favicon) {
            let link = document.querySelector("link[rel~='icon']")
            if (!link) {
              link = document.createElement('link')
              link.rel = 'icon'
              document.getElementsByTagName('head')[0].appendChild(link)
            }
            const favPath = this.settings.app_favicon.replace(/^\//, '')
            link.href = `/storage/${favPath}`
          }
        }
      } catch (error) {
        console.error('Failed to load global app settings', error)
      } finally {
        this.isLoaded = true
      }
    }
  }
})

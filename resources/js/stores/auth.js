import { defineStore } from 'pinia'
import api from '@/plugins/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('auth_user') || 'null'),
    token: localStorage.getItem('auth_token') || null,
    loading: false,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    isAdmin: (state) => state.user?.role_type === 'admin',
    isMember: (state) => state.user?.role_type === 'member',
    isAgent: (state) => state.user?.role_type === 'agent',
    isRepresentative: (state) => state.user?.role_type === 'representative',
    dashboardRoute: (state) => {
      const routes = {
        admin: '/admin/dashboard',
        member: '/member/dashboard',
        agent: '/agen/dashboard',
        representative: '/perwakilan/dashboard',
      }
      return routes[state.user?.role_type] || '/'
    },
  },

  actions: {
    async login(credentials) {
      this.loading = true
      try {
        const { data } = await api.post('/auth/login', credentials)
        this.token = data.data.token
        this.user = data.data.user
        localStorage.setItem('auth_token', this.token)
        localStorage.setItem('auth_user', JSON.stringify(this.user))
        return data
      } finally {
        this.loading = false
      }
    },

    async register(payload) {
      this.loading = true
      try {
        const { data } = await api.post('/auth/register', payload)
        this.token = data.data.token
        this.user = data.data.user
        localStorage.setItem('auth_token', this.token)
        localStorage.setItem('auth_user', JSON.stringify(this.user))
        return data
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await api.post('/auth/logout')
      } catch (_) {}
      this.token = null
      this.user = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
    },

    async fetchMe() {
      const { data } = await api.get('/auth/me')
      this.user = data.data
      localStorage.setItem('auth_user', JSON.stringify(this.user))
    },
  },
})

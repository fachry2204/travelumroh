<template>
  <div class="flex h-screen overflow-hidden bg-sky-50">
    <aside class="w-60 bg-white border-r border-sky-100 flex flex-col fixed lg:relative h-full z-20 shadow-sm">
      <div class="p-4 border-b border-sky-100">
        <router-link to="/" class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg gradient-primary flex items-center justify-center">
            <span class="text-sm">🕌</span>
          </div>
          <div class="font-bold text-slate-900 text-sm">Member Area</div>
        </router-link>
      </div>
      <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        <router-link v-for="item in navItems" :key="item.to" :to="item.to" class="sidebar-link" active-class="active">
          <span class="text-base w-5">{{ item.icon }}</span>
          <span class="text-sm">{{ item.label }}</span>
        </router-link>
      </nav>
      <div class="p-4 border-t border-sky-100">
        <div class="flex items-center gap-2.5 mb-3">
          <div class="w-8 h-8 rounded-full gradient-primary flex items-center justify-center text-white text-xs font-bold">
            {{ auth.user?.name?.charAt(0) }}
          </div>
          <div class="flex-1 min-w-0">
            <div class="font-semibold text-slate-900 text-sm truncate">{{ auth.user?.name }}</div>
          </div>
        </div>
        <button @click="handleLogout" class="w-full text-sm py-2 px-4 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl font-medium transition-colors">Logout</button>
      </div>
    </aside>
    <div class="flex-1 flex flex-col overflow-hidden ml-60 lg:ml-0">
      <header class="bg-white border-b border-sky-100 px-6 py-4">
        <h1 class="text-lg font-semibold text-slate-900">{{ currentPageTitle }}</h1>
      </header>
      <main class="flex-1 overflow-y-auto p-6"><router-view /></main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const navItems = [
  { to: '/member/dashboard', icon: '🏠', label: 'Dashboard' },
  { to: '/member/booking', icon: '📋', label: 'Booking Saya' },
  { to: '/member/data-jamaah', icon: '👤', label: 'Data Jamaah' },
  { to: '/member/dokumen', icon: '📄', label: 'Dokumen' },
  { to: '/member/pembayaran', icon: '💳', label: 'Pembayaran' },
]

const titles = { 'member-dashboard': 'Dashboard', 'member-bookings': 'Booking Saya', 'member-booking-detail': 'Detail Booking', 'member-pilgrims': 'Data Jamaah', 'member-documents': 'Dokumen', 'member-payments': 'Pembayaran' }
const currentPageTitle = computed(() => titles[route.name] || 'Member Area')

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>

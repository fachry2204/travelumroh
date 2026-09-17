<template>
  <div class="flex h-screen overflow-hidden bg-sky-50">
    <!-- Sidebar -->
    <aside :class="['w-64 bg-white border-r border-sky-100 flex flex-col transition-all duration-300 shadow-sm', sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']"
      class="fixed lg:relative z-30 h-full">
      <!-- Logo -->
      <div class="p-5 border-b border-sky-100">
        <router-link to="/" class="flex items-center gap-2.5">
          <img v-if="appStore.settings.app_logo" :src="storageUrl(appStore.settings.app_logo)" class="h-14 sm:h-16 w-auto max-w-[200px] object-contain rounded-lg" alt="Logo" />
          <div v-else class="w-9 h-9 rounded-xl gradient-primary flex items-center justify-center shadow-md">
            <span class="text-white">🕌</span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="font-bold text-slate-900 text-sm truncate">{{ appStore.settings.app_name || 'Travel Umroh' }}</div>
            <div class="text-xs text-sky-500">Admin Panel</div>
          </div>
        </router-link>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <router-link v-for="item in navItems" :key="item.to" :to="item.to"
          class="sidebar-link" active-class="active">
          <span class="text-lg w-6">{{ item.icon }}</span>
          <span class="text-sm">{{ item.label }}</span>
        </router-link>
      </nav>

      <!-- User info -->
      <div class="p-4 border-t border-sky-100">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-9 h-9 rounded-full gradient-primary flex items-center justify-center text-white font-bold text-sm">
            {{ auth.user?.name?.charAt(0) }}
          </div>
          <div class="flex-1 min-w-0">
            <div class="font-semibold text-slate-900 text-sm truncate">{{ auth.user?.name }}</div>
            <div class="text-xs text-slate-400 capitalize">{{ auth.user?.role_type }}</div>
          </div>
        </div>
        <button @click="handleLogout" class="btn-danger w-full justify-center text-sm py-2">
          Logout
        </button>
      </div>
    </aside>

    <!-- Overlay -->
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-20 lg:hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top bar -->
      <header class="bg-white border-b border-sky-100 px-6 py-4 flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-sky-50">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <h1 class="text-lg font-semibold text-slate-900 flex-1">{{ currentPageTitle }}</h1>
        <a href="https://wa.me/6281234567890" target="_blank" class="text-sm text-sky-600 hover:text-sky-700 font-medium flex items-center gap-1">
          <span>💬</span> Support
        </a>
      </header>

      <!-- Page content -->
      <main class="flex-1 overflow-y-auto p-6">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useAppStore } from '@/stores/app'

const auth = useAuthStore()
const appStore = useAppStore()
const router = useRouter()
const route = useRoute()
const sidebarOpen = ref(false)

const storageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `/storage/${path.replace(/^\//, '')}`
}

const navItems = [
  { to: '/admin/dashboard', icon: '📊', label: 'Dashboard' },
  { to: '/admin/paket', icon: '📦', label: 'Paket Umroh' },
  { to: '/admin/booking', icon: '📋', label: 'Booking' },
  { to: '/admin/jamaah', icon: '👥', label: 'Data Jamaah' },
  { to: '/admin/dokumen', icon: '📄', label: 'Dokumen' },
  { to: '/admin/pembayaran', icon: '💳', label: 'Pembayaran' },
  { to: '/admin/agen', icon: '🤝', label: 'Agen' },
  { to: '/admin/perwakilan', icon: '🏢', label: 'Perwakilan' },
  { to: '/admin/komisi', icon: '💰', label: 'Komisi' },
  { to: '/admin/cms', icon: '✏️', label: 'CMS' },
  { to: '/admin/laporan', icon: '📈', label: 'Laporan' },
  { to: '/admin/laporan-khusus', icon: '📊', label: 'Laporan Khusus' },
  { to: '/admin/settings', icon: '⚙️', label: 'Pengaturan' },
  { to: '/admin/audit-logs', icon: '🛡️', label: 'Audit Logs' },
]

const pageTitles = {
  'admin-dashboard': 'Dashboard',
  'admin-packages': 'Paket Umroh',
  'admin-bookings': 'Booking',
  'admin-pilgrims': 'Data Jamaah',
  'admin-documents': 'Dokumen',
  'admin-payments': 'Pembayaran',
  'admin-agents': 'Manajemen Agen',
  'admin-representatives': 'Perwakilan',
  'admin-commissions': 'Komisi',
  'admin-cms': 'CMS Landing Page',
  'admin-reports': 'Laporan',
  'admin-custom-reports': 'Laporan Khusus',
  'admin-settings': 'Pengaturan Sistem',
  'admin-audit-logs': 'Audit Logs',
}

const currentPageTitle = computed(() => pageTitles[route.name] || 'Admin Panel')

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>

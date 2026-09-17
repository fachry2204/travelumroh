<template>
  <div class="min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-sky-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 sm:h-22">
          <!-- Logo -->
          <router-link to="/" class="flex items-center gap-3 group py-1">
            <img v-if="appStore.settings.app_logo" :src="storageUrl(appStore.settings.app_logo)" class="h-16 sm:h-20 md:h-24 w-auto max-w-[340px] sm:max-w-[480px] object-contain transition-all" alt="Logo" />
            <div v-else class="w-11 h-11 rounded-xl gradient-primary flex items-center justify-center shadow-md group-hover:shadow-sky-300 transition-shadow">
              <span class="text-white text-2xl">🕌</span>
            </div>
            <div v-if="!appStore.settings.app_logo">
              <div class="font-bold text-slate-900 text-lg leading-tight">{{ appStore.settings.app_name || 'Travel Umroh' }}</div>
            </div>
          </router-link>

          <!-- Desktop Menu -->
          <div class="hidden lg:flex items-center gap-1">
            <router-link v-for="link in navLinks" :key="link.to" :to="link.to"
              class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition-all">
              {{ link.label }}
            </router-link>
          </div>

          <!-- CTA Buttons -->
          <div class="hidden md:flex items-center gap-3">
            <a v-if="!auth.isLoggedIn" :href="`https://wa.me/${appStore.settings.app_phone?.replace(/\D/g, '') || '6281234567890'}`" target="_blank"
              class="btn-gold text-sm px-4 py-2">
              <span>💬</span> Konsultasi
            </a>
            <router-link v-if="!auth.isLoggedIn" to="/login" class="btn-primary text-sm px-4 py-2">
              Login
            </router-link>
            <template v-else>
              <router-link :to="auth.dashboardRoute" class="btn-primary text-sm px-4 py-2">
                Dashboard
              </router-link>
            </template>
          </div>

          <!-- Mobile menu button -->
          <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-sky-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <transition name="slide-down">
        <div v-if="mobileOpen" class="lg:hidden bg-white border-t border-sky-100 px-4 py-3 space-y-1">
          <router-link v-for="link in navLinks" :key="link.to" :to="link.to" @click="mobileOpen = false"
            class="block px-4 py-3 rounded-xl text-slate-600 hover:bg-sky-50 hover:text-sky-600 font-medium transition-colors">
            {{ link.label }}
          </router-link>
          <div class="pt-2 flex flex-col gap-2">
            <router-link to="/login" class="btn-primary justify-center">Login</router-link>
            <a :href="`https://wa.me/${appStore.settings.app_phone?.replace(/\D/g, '') || '6281234567890'}`" target="_blank" class="btn-gold justify-center">Konsultasi WhatsApp</a>
          </div>
        </div>
      </transition>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Brand -->
          <div>
            <div class="flex items-center gap-2.5 mb-4">
              <img v-if="appStore.settings.app_logo" :src="storageUrl(appStore.settings.app_logo)" class="h-12 sm:h-14 w-auto max-w-[260px] object-contain rounded-lg bg-white p-1.5" alt="Logo" />
              <div v-else class="w-11 h-11 rounded-xl gradient-primary flex items-center justify-center">
                <span class="text-white text-2xl">🕌</span>
              </div>
              <div v-if="!appStore.settings.app_logo">
                <div class="font-bold text-white text-base">{{ appStore.settings.app_name || 'Travel Umroh' }}</div>
              </div>
            </div>
            <p class="text-sm text-slate-400 leading-relaxed">
              Perjalanan umroh terpercaya dengan layanan profesional dari pendaftaran hingga kepulangan.
            </p>
            <div class="flex gap-3 mt-4">
              <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-sky-600 transition-colors text-sm">ig</a>
              <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-blue-600 transition-colors text-sm">fb</a>
              <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-red-600 transition-colors text-sm">yt</a>
            </div>
          </div>

          <!-- Links -->
          <div>
            <h4 class="font-semibold text-white mb-4">Menu Utama</h4>
            <ul class="space-y-2 text-sm">
              <li v-for="link in navLinks" :key="link.to">
                <router-link :to="link.to" class="hover:text-sky-400 transition-colors">{{ link.label }}</router-link>
              </li>
            </ul>
          </div>

          <!-- Quick links -->
          <div>
            <h4 class="font-semibold text-white mb-4">Layanan</h4>
            <ul class="space-y-2 text-sm">
              <li><router-link to="/daftar" class="hover:text-sky-400 transition-colors">Daftar Umroh</router-link></li>
              <li><router-link to="/login" class="hover:text-sky-400 transition-colors">Member Area</router-link></li>
              <li><a href="#" class="hover:text-sky-400 transition-colors">Syarat & Ketentuan</a></li>
              <li><a href="#" class="hover:text-sky-400 transition-colors">Kebijakan Privasi</a></li>
            </ul>
          </div>

          <!-- Contact -->
          <div>
            <h4 class="font-semibold text-white mb-4">Hubungi Kami</h4>
            <ul class="space-y-3 text-sm">
              <li class="flex items-start gap-2">
                <span class="mt-0.5">📍</span>
                <span>{{ appStore.settings.app_address || 'Jl. Raya Umroh No. 1, Jakarta' }}</span>
              </li>
              <li class="flex items-center gap-2">
                <span>💬</span>
                <a :href="`https://wa.me/${appStore.settings.app_phone?.replace(/\D/g, '') || '6281234567890'}`" target="_blank" class="hover:text-sky-400">{{ appStore.settings.app_phone || '+62 812-3456-7890' }}</a>
              </li>
              <li class="flex items-center gap-2">
                <span>✉️</span>
                <a :href="`mailto:${appStore.settings.app_email}`" class="hover:text-sky-400">{{ appStore.settings.app_email || 'info@travelumroh.com' }}</a>
              </li>
            </ul>
          </div>
        </div>

        <!-- PARTNERS & ACCREDITATIONS BADGES ROW -->
        <div class="border-t border-slate-800/80 pt-8 mt-8 text-center space-y-3">
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Terakreditasi & Terdaftar Resmi Kemenag RI</p>
          <div class="bg-white rounded-2xl p-3 sm:p-4 shadow-md max-w-xl mx-auto inline-block">
            <img src="/images/footer-partners.png" alt="Akreditasi & Asosiasi Travel Umroh Resmi (5 Pasti Umrah, Siskopatuh, HIMPUH, KAN, IATA, ASITA)" class="h-9 sm:h-11 md:h-12 w-auto object-contain mx-auto transition-transform hover:scale-105" />
          </div>
        </div>

        <div class="border-t border-slate-800 mt-8 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-sm text-slate-500">
          <p>&copy; {{ new Date().getFullYear() }} {{ appStore.settings.app_name || 'Travel Umroh' }}. Hak cipta dilindungi.</p>
          <p>Berizin resmi Kemenag RI</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useAppStore } from '@/stores/app'

const auth = useAuthStore()
const appStore = useAppStore()
const mobileOpen = ref(false)

const storageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `/storage/${path.replace(/^\//, '')}`
}

const navLinks = [
  { to: '/', label: 'Beranda' },
  { to: '/paket-umroh', label: 'Paket Umroh' },
  { to: '/galeri', label: 'Galeri' },
  { to: '/artikel', label: 'Artikel' },
  { to: '/faq', label: 'FAQ' },
  { to: '/kontak', label: 'Kontak' },
]
</script>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-10px); }
</style>

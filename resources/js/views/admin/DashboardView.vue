<template>
  <div class="space-y-6">
    <!-- Welcome -->
    <div class="gradient-primary rounded-2xl p-6 text-white">
      <h2 class="text-xl font-bold">Selamat Datang, {{ auth.user?.name }}! 👋</h2>
      <p class="text-sky-100 text-sm mt-1">Pantau seluruh aktivitas sistem Travel Umroh</p>
    </div>

    <!-- KPI Cards -->
    <div v-if="loading" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="i in 8" :key="i" class="card p-6 animate-pulse">
        <div class="h-4 bg-slate-100 rounded w-20 mb-3"></div>
        <div class="h-8 bg-slate-100 rounded w-16"></div>
      </div>
    </div>
    <div v-else class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="kpi in kpis" :key="kpi.label" class="kpi-card">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0" :class="kpi.bg">{{ kpi.icon }}</div>
        <div>
          <div class="text-2xl font-extrabold text-slate-900">{{ kpi.value }}</div>
          <div class="text-xs text-slate-500">{{ kpi.label }}</div>
        </div>
      </div>
    </div>

    <!-- Charts & Recent -->
    <div class="grid lg:grid-cols-2 gap-6">
      <!-- Recent Bookings -->
      <div class="card p-6">
        <h3 class="font-bold text-slate-900 mb-4">Booking Terbaru</h3>
        <div v-if="recentBookings.length === 0" class="text-center py-8 text-slate-400">Belum ada booking</div>
        <div v-else class="space-y-3">
          <div v-for="b in recentBookings.slice(0, 6)" :key="b.id"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-sky-50 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600 font-bold text-xs">
              {{ b.booking_number.slice(-4) }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="font-semibold text-slate-900 text-sm truncate">{{ b.package?.name }}</div>
              <div class="text-xs text-slate-400">{{ formatDate(b.created_at) }}</div>
            </div>
            <StatusBadge :status="b.booking_status" type="booking" />
          </div>
        </div>
        <router-link to="/admin/booking" class="btn-outline w-full justify-center mt-4 text-sm">Lihat Semua Booking</router-link>
      </div>

      <!-- Quick Actions -->
      <div class="card p-6">
        <h3 class="font-bold text-slate-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 gap-3">
          <router-link v-for="action in quickActions" :key="action.to" :to="action.to"
            class="p-4 rounded-xl border-2 border-dashed border-sky-200 hover:border-sky-400 hover:bg-sky-50 text-center transition-all group">
            <div class="text-2xl mb-2 group-hover:scale-110 transition-transform">{{ action.icon }}</div>
            <div class="text-xs font-semibold text-slate-600 group-hover:text-sky-600">{{ action.label }}</div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/plugins/axios'
import StatusBadge from '@/components/StatusBadge.vue'

const auth = useAuthStore()
const loading = ref(true)
const stats = ref({})
const recentBookings = ref([])

const kpis = computed(() => [
  { icon: '📋', label: 'Total Booking', value: stats.value.total_bookings ?? 0, bg: 'bg-sky-100 text-sky-600' },
  { icon: '👥', label: 'Total Jamaah', value: stats.value.total_pilgrims ?? 0, bg: 'bg-violet-100 text-violet-600' },
  { icon: '⏳', label: 'Menunggu DP', value: stats.value.pending_bookings ?? 0, bg: 'bg-amber-100 text-amber-600' },
  { icon: '✅', label: 'Lunas', value: stats.value.paid_bookings ?? 0, bg: 'bg-emerald-100 text-emerald-600' },
  { icon: '📄', label: 'Dokumen Kurang', value: stats.value.incomplete_docs ?? 0, bg: 'bg-red-100 text-red-500' },
  { icon: '📦', label: 'Paket Aktif', value: stats.value.active_packages ?? 0, bg: 'bg-sky-100 text-sky-600' },
  { icon: '💰', label: 'Total Pendapatan', value: formatCurrency(stats.value.total_revenue ?? 0), bg: 'bg-emerald-100 text-emerald-600' },
  { icon: '🤝', label: 'Komisi Pending', value: formatCurrency(stats.value.pending_commissions ?? 0), bg: 'bg-orange-100 text-orange-500' },
])

const quickActions = [
  { to: '/admin/paket/tambah', icon: '➕', label: 'Tambah Paket' },
  { to: '/admin/pembayaran', icon: '✅', label: 'Validasi Bayar' },
  { to: '/admin/dokumen', icon: '📄', label: 'Validasi Dokumen' },
  { to: '/admin/agen', icon: '🤝', label: 'Kelola Agen' },
]

function formatCurrency(val) {
  if (typeof val === 'string') return val
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0, notation: 'compact' }).format(val)
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}

onMounted(async () => {
  try {
    const { data } = await api.get('/admin/dashboard')
    stats.value = data.data.stats
    recentBookings.value = data.data.recent_bookings
  } finally {
    loading.value = false
  }
})
</script>

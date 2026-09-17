<template>
  <div class="space-y-6">
    <!-- Welcome banner -->
    <div class="gradient-primary rounded-2xl p-6 text-white">
      <h2 class="text-xl font-bold">Assalamu'alaikum, {{ auth.user?.name }}! 🕌</h2>
      <p class="text-sky-100 text-sm mt-1">Pantau status perjalanan umroh Anda</p>
    </div>

    <!-- Stats -->
    <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i" class="card p-5 animate-pulse"><div class="h-4 bg-slate-100 rounded w-16 mb-2"></div><div class="h-8 bg-slate-100 rounded w-10"></div></div>
    </div>
    <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="s in statCards" :key="s.label" class="kpi-card">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0" :class="s.bg">{{ s.icon }}</div>
        <div><div class="text-2xl font-extrabold text-slate-900">{{ s.value }}</div><div class="text-xs text-slate-500">{{ s.label }}</div></div>
      </div>
    </div>

    <!-- Recent Bookings -->
    <div class="card p-6">
      <h3 class="font-bold text-slate-900 mb-4">Booking Terbaru</h3>
      <div v-if="!recentBookings.length" class="text-center py-8">
        <div class="text-5xl mb-3">📋</div>
        <p class="text-slate-400 text-sm">Belum ada booking</p>
        <router-link to="/paket-umroh" class="btn-primary mt-4">Lihat Paket Umroh</router-link>
      </div>
      <div v-else class="space-y-3">
        <router-link v-for="b in recentBookings" :key="b.id" :to="`/member/booking/${b.id}`"
          class="flex items-center gap-4 p-4 rounded-xl hover:bg-sky-50 transition-colors group">
          <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600 font-bold text-sm group-hover:bg-sky-200 transition-colors">
            {{ b.booking_number.slice(-4) }}
          </div>
          <div class="flex-1 min-w-0">
            <div class="font-semibold text-slate-900 group-hover:text-sky-600 transition-colors text-sm line-clamp-1">{{ b.package?.name }}</div>
            <div class="text-xs text-slate-400">{{ b.total_pilgrims }} jamaah • {{ formatDate(b.created_at) }}</div>
          </div>
          <StatusBadge :status="b.booking_status" type="booking" />
        </router-link>
      </div>
    </div>

    <!-- CTA if no bookings -->
    <div v-if="!loading && !recentBookings.length" class="card p-6 gradient-primary text-center">
      <h3 class="font-bold text-white mb-2">Siap Berangkat Umroh?</h3>
      <p class="text-sky-100 text-sm mb-4">Pilih paket yang sesuai dan daftarkan diri Anda sekarang</p>
      <router-link to="/paket-umroh" class="btn-gold">Lihat Paket Umroh</router-link>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/plugins/axios'
import StatusBadge from '@/components/StatusBadge.vue'
const auth = useAuthStore()
const loading = ref(true), stats = ref({}), recentBookings = ref([])
const statCards = computed(() => [
  { icon: '📋', label: 'Total Booking', value: stats.value.total_bookings ?? 0, bg: 'bg-sky-100 text-sky-600' },
  { icon: '⏳', label: 'Pending', value: stats.value.pending ?? 0, bg: 'bg-amber-100 text-amber-600' },
  { icon: '💳', label: 'Sudah DP', value: stats.value.dp ?? 0, bg: 'bg-blue-100 text-blue-600' },
  { icon: '✅', label: 'Lunas', value: stats.value.paid ?? 0, bg: 'bg-emerald-100 text-emerald-600' },
])
function formatDate(d) { return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }
onMounted(async () => {
  try { const { data } = await api.get('/member/dashboard'); stats.value = data.data.stats; recentBookings.value = data.data.recent_bookings }
  finally { loading.value = false }
})
</script>

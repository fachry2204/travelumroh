<template>
  <div class="space-y-6">
    <div class="gradient-primary rounded-2xl p-6 text-white">
      <h2 class="text-xl font-bold">Selamat Datang Agen, {{ auth.user?.name }}! 👋</h2>
    </div>
    <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i" class="card p-5 animate-pulse"><div class="h-4 bg-slate-100 rounded w-16 mb-2"></div><div class="h-8 bg-slate-100 rounded w-10"></div></div>
    </div>
    <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="s in statCards" :key="s.label" class="kpi-card">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0" :class="s.bg">{{ s.icon }}</div>
        <div><div class="text-2xl font-extrabold text-slate-900">{{ s.value }}</div><div class="text-xs text-slate-500">{{ s.label }}</div></div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/plugins/axios'
const auth = useAuthStore(), loading = ref(true), stats = ref({})
const statCards = computed(() => [
  { icon: '📋', label: 'Total Booking', value: stats.value.total_bookings ?? 0, bg: 'bg-sky-100 text-sky-600' },
  { icon: '👥', label: 'Total Jamaah', value: stats.value.total_pilgrims ?? 0, bg: 'bg-violet-100 text-violet-600' },
  { icon: '💰', label: 'Komisi Pending', value: formatCurrency(stats.value.pending_commissions ?? 0), bg: 'bg-amber-100 text-amber-600' },
  { icon: '✅', label: 'Komisi Cair', value: formatCurrency(stats.value.paid_commissions ?? 0), bg: 'bg-emerald-100 text-emerald-600' },
])
function formatCurrency(val) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0, notation: 'compact' }).format(val) }
onMounted(async () => { try { const { data } = await api.get('/agent/dashboard'); stats.value = data.data.stats } finally { loading.value = false } })
</script>

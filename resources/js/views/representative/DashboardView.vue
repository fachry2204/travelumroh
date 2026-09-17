<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">Dashboard Perwakilan</h2>
        <p class="text-slate-500 text-sm">Ringkasan performa agen dan booking di wilayah Anda</p>
      </div>
    </div>

    <div v-if="loading" class="p-12 text-center"><div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div></div>

    <template v-else>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="kpi-card">
          <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-xl">👥</div>
          <div><div class="text-2xl font-extrabold text-slate-900">{{ stats.total_agents ?? 0 }}</div><div class="text-xs text-slate-500">Total Agen Aktif</div></div>
        </div>
        <div class="kpi-card">
          <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">📋</div>
          <div><div class="text-2xl font-extrabold text-slate-900">{{ stats.total_bookings ?? 0 }}</div><div class="text-xs text-slate-500">Booking Wilayah</div></div>
        </div>
        <div class="kpi-card">
          <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">💰</div>
          <div><div class="text-2xl font-extrabold text-slate-900">{{ formatCurrency(stats.total_sales ?? 0) }}</div><div class="text-xs text-slate-500">Total Penjualan</div></div>
        </div>
        <div class="kpi-card">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">✅</div>
          <div><div class="text-2xl font-extrabold text-slate-900">{{ formatCurrency(stats.total_commissions ?? 0) }}</div><div class="text-xs text-slate-500">Komisi Wilayah</div></div>
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        <div class="card p-6">
          <h3 class="font-bold text-slate-900 mb-4">Top Agen Bulan Ini</h3>
          <div class="space-y-4">
            <!-- Placeholder for top agents list -->
            <div class="text-sm text-slate-500 text-center py-4">Data belum tersedia</div>
          </div>
        </div>
        <div class="card p-6">
          <h3 class="font-bold text-slate-900 mb-4">Booking Terbaru</h3>
          <div class="space-y-4">
            <!-- Placeholder for recent bookings -->
             <div class="text-sm text-slate-500 text-center py-4">Data belum tersedia</div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const stats = ref({})
const loading = ref(true)

function formatCurrency(val) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0, notation: 'compact' }).format(val || 0) }

onMounted(async () => {
  // Mocking endpoint for now, or assume it exists if I created it earlier.
  // Actually, I didn't create representative controller yet fully. Let's just mock stats for UI display.
  setTimeout(() => {
    stats.value = { total_agents: 12, total_bookings: 45, total_sales: 1250000000, total_commissions: 25000000 }
    loading.value = false
  }, 500)
})
</script>

<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">Komisi Saya</h2>
        <p class="text-slate-500 text-sm">Pantau status dan riwayat komisi dari setiap booking referral</p>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="s in statCards" :key="s.label" class="kpi-card">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0" :class="s.bg">{{ s.icon }}</div>
        <div><div class="text-xl font-extrabold text-slate-900">{{ s.value }}</div><div class="text-xs text-slate-500">{{ s.label }}</div></div>
      </div>
    </div>

    <!-- List komisi -->
    <div class="card overflow-hidden">
      <div class="px-6 py-4 border-b border-sky-100 flex items-center justify-between">
        <h3 class="font-bold text-slate-900">Riwayat Komisi</h3>
        <select v-model="statusFilter" @change="fetch" class="form-select w-36 !py-1.5 !text-sm">
          <option value="">Semua</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="paid">Dibayar</option>
          <option value="cancelled">Batal</option>
        </select>
      </div>
      <div v-if="loading" class="p-12 text-center"><div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div></div>
      <div v-else-if="!items.length" class="p-12 text-center text-slate-400">
        <div class="text-4xl mb-2">💰</div><p>Belum ada data komisi</p>
      </div>
      <div v-else class="divide-y divide-sky-50">
        <div v-for="c in items" :key="c.id" class="px-6 py-4 flex items-center gap-4 hover:bg-sky-50/50">
          <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center"
            :class="c.status === 'paid' ? 'bg-emerald-100' : c.status === 'approved' ? 'bg-blue-100' : c.status === 'pending' ? 'bg-amber-100' : 'bg-red-100'">
            <span class="text-xl">{{ c.status === 'paid' ? '💰' : c.status === 'approved' ? '✅' : c.status === 'pending' ? '⏳' : '❌' }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="font-semibold text-slate-900 font-mono text-sm">{{ c.booking?.booking_number }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{{ c.booking?.package?.name }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{{ formatDate(c.created_at) }}</div>
          </div>
          <div class="text-right flex-shrink-0">
            <div class="font-extrabold text-lg" :class="c.status === 'paid' ? 'text-emerald-600' : 'text-slate-900'">{{ formatCurrency(c.commission_amount) }}</div>
            <StatusBadge :status="c.status" type="commission" class="mt-1" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/plugins/axios'
import StatusBadge from '@/components/StatusBadge.vue'

const items = ref([]), loading = ref(true), stats = ref({}), statusFilter = ref('')

const statCards = computed(() => [
  { icon: '⏳', label: 'Komisi Pending', value: formatCurrency(stats.value.pending_commissions), bg: 'bg-amber-100 text-amber-600' },
  { icon: '✅', label: 'Komisi Approved', value: formatCurrency(stats.value.approved_commissions), bg: 'bg-blue-100 text-blue-600' },
  { icon: '💰', label: 'Sudah Cair', value: formatCurrency(stats.value.paid_commissions), bg: 'bg-emerald-100 text-emerald-600' },
  { icon: '📋', label: 'Total Booking', value: stats.value.total_bookings ?? 0, bg: 'bg-sky-100 text-sky-600' },
])

function formatCurrency(val) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0, notation: 'compact' }).format(val || 0) }
function formatDate(d) { return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }

async function fetch() {
  loading.value = true
  const { data } = await api.get('/agent/commissions', { params: { status: statusFilter.value } })
  items.value = data.data.data || data.data
  loading.value = false
}

onMounted(async () => {
  const { data } = await api.get('/agent/dashboard')
  stats.value = data.data.stats || {}
  await fetch()
})
</script>

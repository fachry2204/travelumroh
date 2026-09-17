<template>
  <div class="space-y-6">
    <div class="page-header"><h2 class="page-title">Laporan Komisi</h2></div>
    <div class="card p-4 flex gap-3">
      <select v-model="filters.status" @change="fetch" class="form-select w-40">
        <option value="">Semua Status</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="paid">Dibayar</option>
      </select>
    </div>
    <div class="card overflow-hidden">
      <div v-if="loading" class="p-12 text-center"><div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div></div>
      <table v-else class="w-full">
        <thead class="bg-sky-50 border-b border-sky-100"><tr><th class="table-th">Agen</th><th class="table-th hidden md:table-cell">Booking</th><th class="table-th">Komisi</th><th class="table-th">Status</th><th class="table-th">Aksi</th></tr></thead>
        <tbody class="divide-y divide-sky-50">
          <tr v-if="items.length === 0"><td colspan="5" class="table-td text-center py-12 text-slate-400">Tidak ada data komisi</td></tr>
          <tr v-for="c in items" :key="c.id" class="hover:bg-sky-50/50">
            <td class="table-td font-semibold text-slate-900 text-sm">{{ c.agent?.user?.name }}</td>
            <td class="table-td hidden md:table-cell font-mono text-sm">{{ c.booking?.booking_number }}</td>
            <td class="table-td font-bold text-emerald-600">{{ formatCurrency(c.commission_amount) }}</td>
            <td class="table-td"><StatusBadge :status="c.status" type="commission" /></td>
            <td class="table-td">
              <button v-if="c.status === 'approved'" @click="pay(c.id)" class="text-xs px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg font-medium">💰 Tandai Dibayar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'
import StatusBadge from '@/components/StatusBadge.vue'
const items = ref([]), loading = ref(true), filters = ref({ status: '' })
function formatCurrency(val) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val) }
async function fetch() { loading.value = true; const { data } = await api.get('/admin/commissions', { params: filters.value }); items.value = data.data.data || data.data; loading.value = false }
async function pay(id) { await api.post(`/admin/commissions/${id}/pay`); fetch() }
onMounted(fetch)
</script>

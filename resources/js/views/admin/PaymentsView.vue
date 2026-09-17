<template>
  <div class="space-y-6">
    <div class="page-header"><h2 class="page-title">Manajemen Pembayaran</h2></div>
    <div class="card p-4 flex gap-3 flex-wrap">
      <select v-model="filters.status" @change="fetch" class="form-select w-40">
        <option value="">Semua Status</option><option value="pending">Pending</option><option value="review">Review</option><option value="approved">Approved</option><option value="rejected">Ditolak</option>
      </select>
    </div>
    <div class="card overflow-hidden">
      <div v-if="loading" class="p-12 text-center"><div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div></div>
      <table v-else class="w-full">
        <thead class="bg-sky-50 border-b border-sky-100"><tr><th class="table-th">No. Pembayaran</th><th class="table-th hidden md:table-cell">Booking</th><th class="table-th">Jumlah</th><th class="table-th">Status</th><th class="table-th">Aksi</th></tr></thead>
        <tbody class="divide-y divide-sky-50">
          <tr v-if="items.length === 0"><td colspan="5" class="table-td text-center py-12 text-slate-400">Tidak ada pembayaran</td></tr>
          <tr v-for="pay in items" :key="pay.id" class="hover:bg-sky-50/50">
            <td class="table-td"><div class="font-mono text-sm font-semibold">{{ pay.payment_number }}</div><div class="text-xs text-slate-400 capitalize">{{ pay.payment_type }} • {{ pay.method }}</div></td>
            <td class="table-td hidden md:table-cell font-mono text-sm">{{ pay.booking?.booking_number }}</td>
            <td class="table-td font-bold text-sky-600">{{ formatCurrency(pay.amount) }}</td>
            <td class="table-td"><StatusBadge :status="pay.status" type="payment" /></td>
            <td class="table-td">
              <div v-if="pay.status === 'review'" class="flex gap-2">
                <button @click="validate(pay.id, 'approved')" class="text-xs px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg font-medium">✅ Approve</button>
                <button @click="validate(pay.id, 'rejected')" class="text-xs px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium">❌ Tolak</button>
              </div>
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
async function fetch() { loading.value = true; const { data } = await api.get('/admin/payments', { params: filters.value }); items.value = data.data.data || data.data; loading.value = false }
async function validate(id, status) { await api.post(`/admin/payments/${id}/validate`, { status }); fetch() }
onMounted(fetch)
</script>

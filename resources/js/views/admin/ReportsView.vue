<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">Laporan Penjualan & Komisi</h2>
        <p class="text-slate-500 text-sm">Ringkasan transaksi dan performa agen secara keseluruhan</p>
      </div>
    </div>

    <!-- Stats summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="kpi-card">
        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-xl">🛒</div>
        <div><div class="text-2xl font-extrabold text-slate-900">{{ sales.reduce((sum, item) => sum + parseInt(item.total_bookings), 0) }}</div><div class="text-xs text-slate-500">Total Booking 30 Hari</div></div>
      </div>
      <div class="kpi-card">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">💰</div>
        <div><div class="text-xl font-extrabold text-slate-900">{{ formatCurrency(sales.reduce((sum, item) => sum + parseInt(item.total_paid), 0)) }}</div><div class="text-xs text-slate-500">Pendapatan 30 Hari</div></div>
      </div>
      <div class="kpi-card">
        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">🤝</div>
        <div><div class="text-xl font-extrabold text-slate-900">{{ formatCurrency(commissions.reduce((sum, item) => sum + parseInt(item.commission_amount), 0)) }}</div><div class="text-xs text-slate-500">Total Beban Komisi</div></div>
      </div>
      <div class="kpi-card">
        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">📄</div>
        <div>
           <button @click="printReport" class="text-sm font-semibold text-blue-600 hover:underline">Print / Export Laporan PDF</button>
        </div>
      </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
      <!-- Sales report -->
      <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4 border-b border-sky-100"><h3 class="font-bold text-slate-900">Penjualan Harian (30 Hari Terakhir)</h3></div>
        <div class="p-0 overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-sky-50/50 border-b border-sky-100">
                <th class="table-th">Tanggal</th>
                <th class="table-th">Booking</th>
                <th class="table-th">Nilai Transaksi</th>
                <th class="table-th">Terbayar</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
              <tr v-if="!sales.length"><td colspan="4" class="p-8 text-center text-slate-500">Belum ada data penjualan</td></tr>
              <tr v-for="item in sales" :key="item.date" class="hover:bg-sky-50/50">
                <td class="table-td">{{ formatDate(item.date) }}</td>
                <td class="table-td text-center font-semibold">{{ item.total_bookings }}</td>
                <td class="table-td">{{ formatCurrency(item.total_sales) }}</td>
                <td class="table-td text-emerald-600 font-medium">{{ formatCurrency(item.total_paid) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Commissions report -->
      <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4 border-b border-sky-100"><h3 class="font-bold text-slate-900">Riwayat Komisi Terakhir</h3></div>
        <div class="p-0 overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-sky-50/50 border-b border-sky-100">
                <th class="table-th">Agen</th>
                <th class="table-th">Booking</th>
                <th class="table-th">Komisi</th>
                <th class="table-th">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
              <tr v-if="!commissions.length"><td colspan="4" class="p-8 text-center text-slate-500">Belum ada data komisi</td></tr>
              <tr v-for="item in commissions.slice(0, 15)" :key="item.id" class="hover:bg-sky-50/50">
                <td class="table-td font-medium">{{ item.agent?.user?.name }}<br><span class="text-xs font-normal text-slate-500">{{ item.agent?.agent_code }}</span></td>
                <td class="table-td">{{ item.booking?.booking_number }}</td>
                <td class="table-td font-bold">{{ formatCurrency(item.commission_amount) }}</td>
                <td class="table-td">
                  <span class="px-2 py-1 rounded-full text-xs font-semibold capitalize"
                    :class="item.status === 'paid' ? 'bg-emerald-100 text-emerald-700' : (item.status === 'approved' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')">
                    {{ item.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const sales = ref([])
const commissions = ref([])
const loading = ref(true)

function formatCurrency(val) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0, notation: 'compact' }).format(val || 0) }
function formatDate(d) { return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }

function printReport() {
  window.print()
}

onMounted(async () => {
  try {
    const [resSales, resComm] = await Promise.all([
      api.get('/admin/reports/sales'),
      api.get('/admin/reports/commissions')
    ])
    sales.value = resSales.data.data
    commissions.value = resComm.data.data
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="space-y-6">
    <div class="page-header print:hidden">
      <div>
        <h2 class="page-title">Laporan Khusus</h2>
        <p class="text-slate-500 text-sm">Filter data berdasarkan tanggal, tipe, lalu cetak (Print / Export to PDF)</p>
      </div>
      <div>
        <button @click="printReport" class="btn-primary flex items-center gap-2" :disabled="!reportData.length">
          🖨️ Cetak / Export PDF
        </button>
      </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-6 print:hidden">
      <form @submit.prevent="generateReport" class="grid md:grid-cols-4 gap-4 items-end">
        <div>
          <label class="form-label">Tipe Laporan</label>
          <select v-model="filter.type" class="form-select" required>
            <option value="sales">Data Penjualan (Booking)</option>
            <option value="pilgrims">Data Jamaah Terdaftar</option>
            <option value="commissions">Data Komisi Agen</option>
          </select>
        </div>
        <div>
          <label class="form-label">Tanggal Mulai</label>
          <input v-model="filter.start_date" type="date" class="form-input" required />
        </div>
        <div>
          <label class="form-label">Tanggal Akhir</label>
          <input v-model="filter.end_date" type="date" class="form-input" required />
        </div>
        <div>
          <button type="submit" class="btn-primary w-full" :disabled="loading">
            <span v-if="loading">Memproses...</span>
            <span v-else>🔍 Generate</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Print Header (Hanya tampil saat diprint) -->
    <div class="hidden print:block text-center mb-8 border-b-2 border-slate-900 pb-4">
      <h1 class="text-3xl font-bold text-slate-900 mb-1">LAPORAN {{ filter.type.toUpperCase() }}</h1>
      <p class="text-slate-600">Periode: {{ filter.start_date }} s/d {{ filter.end_date }}</p>
    </div>

    <!-- Report Table -->
    <div class="card p-6" v-if="reportData.length > 0 || hasSearched">
      <div v-if="loading" class="text-center py-10 text-slate-500">Memuat data...</div>
      
      <div v-else-if="!reportData.length" class="text-center py-10 text-slate-500">
        Tidak ada data pada periode ini.
      </div>
      
      <div v-else class="overflow-x-auto">
        <!-- Tabel Sales -->
        <table v-if="filter.type === 'sales'" class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-500 text-sm border-b">
              <th class="p-4 font-semibold">TGL BOOKING</th>
              <th class="p-4 font-semibold">NO BOOKING</th>
              <th class="p-4 font-semibold">JEMAAH</th>
              <th class="p-4 font-semibold">PAKET</th>
              <th class="p-4 font-semibold text-right">TOTAL (Rp)</th>
              <th class="p-4 font-semibold">STATUS</th>
            </tr>
          </thead>
          <tbody class="text-sm">
            <tr v-for="item in reportData" :key="item.id" class="border-b hover:bg-slate-50">
              <td class="p-4">{{ item.created_at.substring(0, 10) }}</td>
              <td class="p-4 font-medium text-slate-900">{{ item.booking_number }}</td>
              <td class="p-4">{{ item.user?.name }} ({{ item.total_pilgrims }} Pax)</td>
              <td class="p-4">{{ item.package?.name }}</td>
              <td class="p-4 text-right font-medium">{{ formatCurrency(item.total_amount) }}</td>
              <td class="p-4 capitalize">{{ item.booking_status }}</td>
            </tr>
            <tr class="bg-slate-100 font-bold">
              <td colspan="4" class="p-4 text-right">TOTAL PENJUALAN</td>
              <td class="p-4 text-right text-emerald-600">{{ formatCurrency(summary.totalAmount) }}</td>
              <td></td>
            </tr>
          </tbody>
        </table>

        <!-- Tabel Jamaah -->
        <table v-if="filter.type === 'pilgrims'" class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-500 text-sm border-b">
              <th class="p-4 font-semibold">NAMA JAMAAH</th>
              <th class="p-4 font-semibold">NIK</th>
              <th class="p-4 font-semibold">NO HP</th>
              <th class="p-4 font-semibold">PAKET</th>
              <th class="p-4 font-semibold">TGL DAFTAR</th>
            </tr>
          </thead>
          <tbody class="text-sm">
            <tr v-for="item in reportData" :key="item.id" class="border-b hover:bg-slate-50">
              <td class="p-4 font-medium text-slate-900">{{ item.full_name }}</td>
              <td class="p-4 font-mono">{{ item.nik }}</td>
              <td class="p-4">{{ item.phone }}</td>
              <td class="p-4">{{ item.booking?.package?.name }}</td>
              <td class="p-4">{{ item.created_at.substring(0, 10) }}</td>
            </tr>
          </tbody>
        </table>

        <!-- Tabel Komisi -->
        <table v-if="filter.type === 'commissions'" class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-500 text-sm border-b">
              <th class="p-4 font-semibold">TANGGAL</th>
              <th class="p-4 font-semibold">AGEN</th>
              <th class="p-4 font-semibold">PAKET/BOOKING</th>
              <th class="p-4 font-semibold text-right">KOMISI (Rp)</th>
              <th class="p-4 font-semibold">STATUS</th>
            </tr>
          </thead>
          <tbody class="text-sm">
            <tr v-for="item in reportData" :key="item.id" class="border-b hover:bg-slate-50">
              <td class="p-4">{{ item.created_at.substring(0, 10) }}</td>
              <td class="p-4 font-medium">{{ item.agent?.user?.name }}</td>
              <td class="p-4">{{ item.booking?.package?.name }}</td>
              <td class="p-4 text-right font-medium">{{ formatCurrency(item.commission_amount) }}</td>
              <td class="p-4 capitalize">{{ item.status }}</td>
            </tr>
            <tr class="bg-slate-100 font-bold">
              <td colspan="3" class="p-4 text-right">TOTAL KOMISI</td>
              <td class="p-4 text-right text-emerald-600">{{ formatCurrency(summary.totalAmount) }}</td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import api from '@/plugins/axios'

const filter = ref({
  type: 'sales',
  start_date: new Date(new Date().setDate(1)).toISOString().split('T')[0], // 1st of month
  end_date: new Date().toISOString().split('T')[0] // today
})

const reportData = ref([])
const loading = ref(false)
const hasSearched = ref(false)

const summary = computed(() => {
  let totalAmount = 0
  if (filter.value.type === 'sales') {
    totalAmount = reportData.value.reduce((sum, item) => sum + Number(item.total_amount), 0)
  } else if (filter.value.type === 'commissions') {
    totalAmount = reportData.value.reduce((sum, item) => sum + Number(item.commission_amount), 0)
  }
  return { totalAmount }
})

function formatCurrency(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0)
}

async function generateReport() {
  loading.value = true
  hasSearched.value = true
  try {
    const { data } = await api.post('/admin/reports/custom', filter.value)
    if (data.success) {
      reportData.value = data.data
    }
  } catch (e) {
    console.error(e)
    alert('Gagal mengambil laporan.')
  } finally {
    loading.value = false
  }
}

function printReport() {
  window.print()
}
</script>

<style>
@media print {
  body { background: white !important; }
  .card { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; }
  table { width: 100% !important; border-collapse: collapse !important; }
  th, td { border: 1px solid #e2e8f0 !important; padding: 8px !important; }
  .sidebar, .navbar { display: none !important; }
  .main-content { margin-left: 0 !important; padding: 0 !important; }
}
</style>

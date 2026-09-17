<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">Paket Umroh</h2>
        <p class="text-slate-500 text-sm">Kelola paket perjalanan umroh</p>
      </div>
      <router-link to="/admin/paket/tambah" class="btn-primary">+ Tambah Paket</router-link>
    </div>

    <!-- Filters -->
    <div class="card p-4 flex flex-wrap gap-3">
      <input v-model="search" @input="fetchPackages" type="text" class="form-input w-64" placeholder="Cari nama paket..." />
      <select v-model="statusFilter" @change="fetchPackages" class="form-select w-40">
        <option value="">Semua Status</option>
        <option value="active">Aktif</option>
        <option value="inactive">Nonaktif</option>
      </select>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
      <div v-if="loading" class="p-12 text-center">
        <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
      </div>
      <table v-else class="w-full">
        <thead class="bg-sky-50 border-b border-sky-100">
          <tr>
            <th class="table-th">Paket</th>
            <th class="table-th hidden md:table-cell">Keberangkatan</th>
            <th class="table-th hidden lg:table-cell">Harga Mulai</th>
            <th class="table-th hidden md:table-cell">Seat</th>
            <th class="table-th">Status</th>
            <th class="table-th">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sky-50">
          <tr v-if="packages.length === 0">
            <td colspan="6" class="table-td text-center py-12 text-slate-400">Belum ada paket</td>
          </tr>
          <tr v-for="pkg in packages" :key="pkg.id" class="hover:bg-sky-50/50 transition-colors">
            <td class="table-td">
              <div class="font-semibold text-slate-900">{{ pkg.name }}</div>
              <div class="text-xs text-slate-400">{{ pkg.code }}</div>
            </td>
            <td class="table-td hidden md:table-cell">{{ formatDate(pkg.departure_date) }}</td>
            <td class="table-td hidden lg:table-cell font-semibold text-sky-600">{{ formatCurrency(minPrice(pkg)) }}</td>
            <td class="table-td hidden md:table-cell">
              <span :class="pkg.remaining_seat <= 5 ? 'text-red-500' : 'text-emerald-600'" class="font-semibold">{{ pkg.remaining_seat }}</span>/{{ pkg.quota }}
            </td>
            <td class="table-td">
              <span :class="pkg.status === 'active' ? 'badge-success' : 'badge-secondary'" class="badge-status">
                {{ pkg.status === 'active' ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td class="table-td">
              <div class="flex gap-2">
                <router-link :to="`/admin/paket/${pkg.id}/edit`" class="text-xs px-3 py-1.5 bg-sky-100 hover:bg-sky-200 text-sky-700 rounded-lg font-medium transition-colors">Edit</router-link>
                <button @click="deletePackage(pkg)" class="text-xs px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition-colors">Hapus</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-4 border-t border-sky-100 flex justify-between items-center">
        <span class="text-sm text-slate-500">{{ pagination.from }}-{{ pagination.to }} dari {{ pagination.total }}</span>
        <div class="flex gap-2">
          <button @click="page--; fetchPackages()" :disabled="page <= 1" class="px-3 py-1.5 text-sm rounded-lg border border-sky-200 disabled:opacity-40">←</button>
          <button @click="page++; fetchPackages()" :disabled="page >= pagination.last_page" class="px-3 py-1.5 text-sm rounded-lg border border-sky-200 disabled:opacity-40">→</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const packages = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const page = ref(1)
const pagination = ref({})

function minPrice(pkg) {
  const prices = [pkg.price_quad, pkg.price_triple, pkg.price_double].filter(Boolean)
  return prices.length ? Math.min(...prices) : 0
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatCurrency(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0, notation: 'compact' }).format(val)
}

async function fetchPackages() {
  loading.value = true
  try {
    const { data } = await api.get('/admin/packages', {
      params: { search: search.value, status: statusFilter.value, page: page.value }
    })
    packages.value = data.data.data || data.data
    pagination.value = data.data.meta || {}
  } finally {
    loading.value = false
  }
}

async function deletePackage(pkg) {
  if (!confirm(`Hapus paket "${pkg.name}"?`)) return
  try {
    await api.delete(`/admin/packages/${pkg.id}`)
    fetchPackages()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menghapus paket')
  }
}

onMounted(fetchPackages)
</script>

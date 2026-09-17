<template>
  <div class="py-16 bg-sky-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-10">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-sky-100 rounded-full text-sky-600 text-sm font-medium mb-3">
          📦 Pilihan Terbaik
        </div>
        <h1 class="section-title">Daftar Paket Umroh</h1>
        <p class="section-subtitle">Temukan paket perjalanan ibadah umroh terbaik yang sesuai dengan kebutuhan dan jadwal Anda.</p>
      </div>

      <!-- Search & Filter Bar -->
      <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-sky-100 mb-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Cari Paket</label>
          <input v-model="search" @input="fetchPackages" type="text" placeholder="Nama paket..." class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Bulan Keberangkatan</label>
          <select v-model="selectedMonth" @change="fetchPackages" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
            <option value="">Semua Bulan</option>
            <option v-for="(m, idx) in months" :key="idx" :value="idx + 1">{{ m }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Tahun</label>
          <select v-model="selectedYear" @change="fetchPackages" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
            <option value="">Semua Tahun</option>
            <option value="2025">2025</option>
            <option value="2026">2026</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="resetFilters" class="w-full btn-outline py-2 text-sm justify-center">
            Reset Filter
          </button>
        </div>
      </div>
      
      <div v-if="loading" class="flex justify-center py-20">
        <div class="text-center">
          <div class="w-12 h-12 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <p class="text-slate-500">Memuat paket umroh...</p>
        </div>
      </div>
      
      <div v-else-if="packages.length === 0" class="text-center py-16 bg-white rounded-2xl border border-sky-100">
        <div class="text-5xl mb-3">🕋</div>
        <h3 class="font-bold text-slate-800 text-lg mb-1">Belum ada paket umroh</h3>
        <p class="text-slate-500 text-sm">Tidak ada paket umroh yang sesuai dengan kriteria pencarian Anda.</p>
      </div>
      
      <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <PackageCard v-for="pkg in packages" :key="pkg.id" :package="pkg" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'
import PackageCard from '@/components/PackageCard.vue'
import { useSEO } from '@/composables/useSEO'

const { setMeta } = useSEO()
const packages = ref([])
const loading = ref(true)
const search = ref('')
const selectedMonth = ref('')
const selectedYear = ref('')

const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

async function fetchPackages() {
  loading.value = true
  try {
    const params = {}
    if (search.value) params.search = search.value
    if (selectedMonth.value) params.month = selectedMonth.value
    if (selectedYear.value) params.year = selectedYear.value

    const { data } = await api.get('/public/packages', { params })
    packages.value = data.data?.data || data.data || []
  } catch (e) {
    console.error(e)
    packages.value = []
  } finally {
    loading.value = false
  }
}

function resetFilters() {
  search.value = ''
  selectedMonth.value = ''
  selectedYear.value = ''
  fetchPackages()
}

onMounted(() => {
  setMeta('Paket Umroh', 'Pilihan paket umroh terbaik dan terpercaya dengan fasilitas terbaik.')
  fetchPackages()
})
</script>

<template>
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-sky-100 space-y-5 sticky top-24">
    <div class="flex items-center justify-between border-b border-sky-100 pb-3">
      <div class="flex items-center gap-2">
        <span class="text-xl">🕋</span>
        <h3 class="font-extrabold text-slate-900 text-base">Paket Umroh Pilihan</h3>
      </div>
      <span class="text-xs text-sky-600 font-bold bg-sky-50 px-2.5 py-1 rounded-full">Terpopuler</span>
    </div>

    <div v-if="loading" class="flex justify-center py-6">
      <div class="w-8 h-8 border-3 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else-if="packages.length === 0" class="text-center py-4 text-xs text-slate-400">
      Belum ada paket umroh tersedia.
    </div>

    <div v-else class="space-y-4">
      <div 
        v-for="pkg in packages.slice(0, 4)" 
        :key="pkg.id" 
        class="group bg-slate-50 hover:bg-sky-50/70 p-3.5 rounded-2xl border border-slate-100 hover:border-sky-200 transition-all flex flex-col justify-between"
      >
        <div class="flex gap-3 items-start">
          <div class="w-16 h-16 rounded-xl bg-sky-100 overflow-hidden flex-shrink-0 relative">
            <img v-if="pkg.image_path" :src="storageUrl(pkg.image_path)" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
            <div v-else class="w-full h-full flex items-center justify-center text-xl">🕋</div>
          </div>
          <div class="flex-1 min-w-0">
            <span class="text-[10px] font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-md inline-block mb-1">
              {{ pkg.duration_days || 9 }} Hari
            </span>
            <h4 class="font-bold text-slate-900 text-xs leading-snug group-hover:text-sky-600 transition-colors line-clamp-2">
              {{ pkg.name }}
            </h4>
            <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
              <span>📅</span> {{ formatDate(pkg.departure_date) }}
            </p>
          </div>
        </div>

        <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between">
          <div>
            <span class="text-[10px] text-slate-400 block">Mulai dari</span>
            <span class="text-xs font-extrabold text-amber-600">Rp {{ formatNumber(pkg.price_quad || pkg.price_double || pkg.price) }}</span>
          </div>
          <router-link :to="`/paket-umroh/${pkg.id}`" class="btn-primary text-[11px] px-3 py-1.5 rounded-xl font-bold">
            Detail &rarr;
          </router-link>
        </div>
      </div>
    </div>

    <div class="pt-2">
      <router-link to="/paket-umroh" class="btn-outline w-full text-center text-xs py-2.5 block">
        Lihat Semua Paket Umroh &rarr;
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const packages = ref([])
const loading = ref(true)

function storageUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `/storage/${path.replace(/^\//, '')}`
}

function formatNumber(val) {
  if (!val) return '0'
  return Number(val).toLocaleString('id-ID')
}

function formatDate(d) {
  if (!d) return 'Tiba di Makkah'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  try {
    const { data } = await api.get('/public/packages')
    packages.value = data.data.data || data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

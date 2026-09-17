<template>
  <div class="py-10 bg-sky-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="flex justify-center py-20">
        <div class="w-12 h-12 border-4 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
      </div>
      
      <div v-else-if="!packageData" class="text-center py-20">
        <h2 class="text-2xl font-bold text-slate-900 mb-2">Paket Tidak Ditemukan</h2>
        <router-link to="/paket-umroh" class="text-sky-600 hover:underline">Kembali ke Daftar Paket</router-link>
      </div>

      <template v-else>
        <!-- Breadcrumb -->
        <div class="text-sm text-slate-500 mb-6 flex gap-2">
          <router-link to="/" class="hover:text-sky-600">Beranda</router-link> / 
          <router-link to="/paket-umroh" class="hover:text-sky-600">Paket Umroh</router-link> / 
          <span class="text-slate-900 font-medium">{{ packageData.name }}</span>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-8">
            <div class="card p-8">
              <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                  <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ packageData.name }}</h1>
                  <div class="flex flex-wrap gap-4 text-sm text-slate-500">
                    <span class="flex items-center gap-1">📅 Berangkat: {{ formatDate(packageData.departure_date) }}</span>
                    <span class="flex items-center gap-1">⏳ Durasi: {{ packageData.duration_days }} Hari</span>
                    <span class="flex items-center gap-1">✈️ Maskapai: {{ packageData.airline || '-' }}</span>
                  </div>
                </div>
                <div class="text-right flex-shrink-0">
                  <div class="text-xs text-slate-500">Mulai dari</div>
                  <div class="text-2xl font-bold text-sky-600">{{ formatCurrency(minPrice) }}</div>
                </div>
              </div>

              <div class="prose max-w-none text-slate-600 mb-8 whitespace-pre-line">
                {{ packageData.description || 'Deskripsi paket belum tersedia.' }}
              </div>

              <!-- Flyer / Featured Image -->
              <div v-if="packageData.featured_image" class="mb-8">
                <img :src="storageUrl(packageData.featured_image)" class="w-full rounded-2xl shadow-sm border border-sky-100" alt="Flyer Paket" />
              </div>

              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <h3 class="font-bold text-slate-900 mb-3 flex items-center gap-2">✅ Fasilitas Termasuk</h3>
                  <ul class="space-y-2 text-sm text-slate-600">
                    <li v-for="(item, i) in splitText(packageData.facilities)" :key="'f'+i" class="flex gap-2">
                      <span class="text-emerald-500">✓</span> {{ item }}
                    </li>
                  </ul>
                </div>
                <div>
                  <h3 class="font-bold text-slate-900 mb-3 flex items-center gap-2">❌ Belum Termasuk</h3>
                  <ul class="space-y-2 text-sm text-slate-600">
                    <li v-for="(item, i) in splitText(packageData.excluded)" :key="'e'+i" class="flex gap-2">
                      <span class="text-red-400">✗</span> {{ item }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Itinerary -->
            <div v-if="packageData.itineraries?.length" class="card p-8">
              <h3 class="font-bold text-slate-900 mb-6 text-xl">Itinerary Perjalanan</h3>
              <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-sky-200 before:to-transparent">
                <div v-for="it in packageData.itineraries" :key="it.id" class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                  <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-sky-500 text-white font-bold text-sm shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                    {{ it.day_number }}
                  </div>
                  <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-xl border border-sky-100 bg-white shadow-sm">
                    <h4 class="font-bold text-slate-900 mb-1">{{ it.title }}</h4>
                    <p class="text-sm text-slate-600">{{ it.description }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar Booking -->
          <div class="space-y-6">
            <div class="card p-6 sticky top-24">
              <h3 class="font-bold text-slate-900 mb-4 text-lg">Pesan Sekarang</h3>
              <div class="space-y-4 mb-6">
                <div class="flex justify-between items-center text-sm">
                  <span class="text-slate-500">Sisa Kuota</span>
                  <span class="font-bold px-2 py-1 bg-sky-100 text-sky-700 rounded-md">{{ packageData.remaining_seat }} Seat</span>
                </div>
                <div class="border-t border-sky-100 pt-4">
                  <p class="text-sm font-semibold text-slate-900 mb-2">Rincian Harga (per orang):</p>
                  <div class="space-y-2 text-sm">
                    <div class="flex justify-between" v-if="packageData.price_quad">
                      <span class="text-slate-500">Quad (Sekamar ber-4)</span>
                      <span class="font-medium text-slate-900">{{ formatCurrency(packageData.price_quad) }}</span>
                    </div>
                    <div class="flex justify-between" v-if="packageData.price_triple">
                      <span class="text-slate-500">Triple (Sekamar ber-3)</span>
                      <span class="font-medium text-slate-900">{{ formatCurrency(packageData.price_triple) }}</span>
                    </div>
                    <div class="flex justify-between" v-if="packageData.price_double">
                      <span class="text-slate-500">Double (Sekamar ber-2)</span>
                      <span class="font-medium text-slate-900">{{ formatCurrency(packageData.price_double) }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <router-link :to="`/daftar/${packageData.slug}`" class="btn-gold w-full justify-center py-3 text-base">
                Daftar Paket Ini
              </router-link>
              <a :href="`https://wa.me/${appStore.settings.app_phone?.replace(/\D/g, '') || '6281234567890'}?text=Halo,%20saya%20tertarik%20dengan%20paket%20${packageData.name}`" target="_blank" class="btn-secondary w-full justify-center mt-3 py-3 text-sm">
                💬 Tanya via WhatsApp
              </a>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { useSEO } from '@/composables/useSEO'
import api from '@/plugins/axios'

const route = useRoute()
const appStore = useAppStore()
const { setMeta } = useSEO()
const packageData = ref(null)
const loading = ref(true)

const storageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `/storage/${path.replace(/^\//, '')}`
}

const minPrice = computed(() => {
  if (!packageData.value) return 0
  const prices = [packageData.value.price_quad, packageData.value.price_triple, packageData.value.price_double].filter(Boolean)
  return prices.length ? Math.min(...prices) : 0
})

function formatCurrency(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function splitText(text) {
  if (!text) return []
  return text.split('\n').filter(t => t.trim() !== '')
}

onMounted(async () => {
  try {
    const { data } = await api.get(`/public/packages/${route.params.slug}`)
    packageData.value = data.data
    if (packageData.value) {
      setMeta(
        packageData.value.name, 
        `Paket Umroh ${packageData.value.name} berangkat tanggal ${formatDate(packageData.value.departure_date)} selama ${packageData.value.duration_days} hari.`
      )
    }
  } catch (e) {
    console.error(e)
    setMeta('Paket Tidak Ditemukan', 'Informasi paket umroh tidak ditemukan.')
  } finally {
    loading.value = false
  }
})
</script>

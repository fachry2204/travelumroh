<template>
  <div class="card overflow-hidden group hover:-translate-y-1 transition-all duration-200 flex flex-col">
    <!-- Image -->
    <div class="relative h-48 bg-gradient-to-br from-sky-100 to-sky-200 overflow-hidden">
      <img v-if="package.featured_image" :src="storageUrl(package.featured_image)" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" />
      <div v-else class="w-full h-full flex items-center justify-center text-6xl group-hover:scale-110 transition-transform duration-300">🕌</div>
      <!-- Badge sisa seat -->
      <div class="absolute top-3 right-3">
        <span v-if="package.remaining_seat <= 5" class="badge-danger">Hampir Penuh!</span>
        <span v-else class="badge-success">{{ package.remaining_seat }} Seat Tersisa</span>
      </div>
      <!-- Duration badge -->
      <div class="absolute top-3 left-3 px-3 py-1 bg-sky-600/80 backdrop-blur text-white text-xs font-semibold rounded-full">
        {{ package.duration_days }} Hari
      </div>
    </div>

    <!-- Content -->
    <div class="p-5 flex flex-col flex-1">
      <h3 class="font-bold text-slate-900 text-base mb-3 line-clamp-2 group-hover:text-sky-600 transition-colors">
        {{ package.name }}
      </h3>

      <!-- Details -->
      <div class="space-y-2 mb-4 text-sm text-slate-500 flex-1">
        <div class="flex items-center gap-2">
          <span class="w-4">✈️</span>
          <span>{{ package.airline || 'Maskapai Terkemuka' }}</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4">📅</span>
          <span>{{ formatDate(package.departure_date) }}</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4">🏨</span>
          <span class="line-clamp-1">{{ package.makkah_hotel || 'Hotel Bintang 5' }}</span>
        </div>
      </div>

      <!-- Price -->
      <div class="border-t border-sky-100 pt-4 flex items-end justify-between">
        <div>
          <div class="text-xs text-slate-400 mb-0.5">Mulai dari</div>
          <div class="text-xl font-extrabold text-sky-600">
            {{ formatCurrency(minPrice) }}
          </div>
          <div class="text-xs text-slate-400">/orang</div>
        </div>
        <router-link :to="`/paket-umroh/${package.slug}`" class="btn-primary text-sm px-4 py-2">
          Detail →
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ package: { type: Object, required: true } })

const storageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `/storage/${path.replace(/^\//, '')}`
}

const minPrice = computed(() => {
  const prices = [props.package.price_quad, props.package.price_triple, props.package.price_double].filter(Boolean)
  return prices.length ? Math.min(...prices) : 0
})

function formatCurrency(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}
</script>

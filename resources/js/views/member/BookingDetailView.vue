<template>
  <div class="space-y-6">
    <div class="page-header"><h2 class="page-title">Detail Booking</h2><router-link to="/member/booking" class="btn-secondary">← Kembali</router-link></div>
    <div v-if="loading" class="card p-12 text-center"><div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div></div>
    <template v-else-if="booking">
      <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
          <div class="card p-6">
            <h3 class="font-bold text-slate-900 mb-4">Informasi Booking</h3>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
              <div><span class="text-slate-500">No. Booking:</span> <span class="font-mono font-bold ml-2">{{ booking.booking_number }}</span></div>
              <div><span class="text-slate-500">Paket:</span> <span class="font-semibold ml-2">{{ booking.package?.name }}</span></div>
              <div><span class="text-slate-500">Total Jamaah:</span> <span class="font-semibold ml-2">{{ booking.total_pilgrims }}</span></div>
              <div><span class="text-slate-500">Tipe Kamar:</span> <span class="font-semibold ml-2 capitalize">{{ booking.room_type }}</span></div>
              <div><span class="text-slate-500">Total:</span> <span class="font-bold text-sky-600 ml-2">{{ formatCurrency(booking.total_amount) }}</span></div>
              <div><span class="text-slate-500">Sudah Bayar:</span> <span class="font-bold text-emerald-600 ml-2">{{ formatCurrency(booking.paid_amount) }}</span></div>
            </div>
          </div>
          <div class="card p-6">
            <h3 class="font-bold text-slate-900 mb-4">Data Jamaah</h3>
            <div v-for="p in booking.pilgrims" :key="p.id" class="border border-sky-100 rounded-xl p-4 mb-3">
              <div class="font-semibold text-slate-900">{{ p.full_name }}</div>
              <div class="text-sm text-slate-500 mt-1">{{ p.phone }} • {{ p.email }}</div>
            </div>
          </div>
        </div>
        <div class="space-y-4">
          <div class="card p-6">
            <h3 class="font-bold text-slate-900 mb-4">Status</h3>
            <div class="space-y-3">
              <div class="flex justify-between items-center"><span class="text-sm text-slate-500">Booking:</span><StatusBadge :status="booking.booking_status" type="booking" /></div>
              <div class="flex justify-between items-center"><span class="text-sm text-slate-500">Dokumen:</span><StatusBadge :status="booking.document_status" type="document" /></div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/plugins/axios'
import StatusBadge from '@/components/StatusBadge.vue'
const route = useRoute()
const booking = ref(null), loading = ref(true)
function formatCurrency(val) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val) }
onMounted(async () => { try { const { data } = await api.get(`/member/bookings/${route.params.id}`); booking.value = data.data } finally { loading.value = false } })
</script>

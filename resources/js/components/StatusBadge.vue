<template>
  <span class="badge-status" :class="badgeClass">{{ label }}</span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: String,
  type: { type: String, default: 'booking' }
})

const config = {
  booking: {
    pending: { class: 'bg-amber-100 text-amber-700', label: 'Pending' },
    dp: { class: 'bg-blue-100 text-blue-700', label: 'Sudah DP' },
    paid: { class: 'bg-emerald-100 text-emerald-700', label: 'Lunas' },
    cancelled: { class: 'bg-red-100 text-red-700', label: 'Batal' },
  },
  document: {
    pending: { class: 'bg-amber-100 text-amber-700', label: 'Menunggu' },
    valid: { class: 'bg-emerald-100 text-emerald-700', label: 'Valid' },
    rejected: { class: 'bg-red-100 text-red-700', label: 'Ditolak' },
  },
  document_ext: {
    not_uploaded: { class: 'bg-slate-100 text-slate-500', label: 'Belum Upload' },
    pending: { class: 'bg-amber-100 text-amber-700', label: 'Menunggu Validasi' },
    valid: { class: 'bg-emerald-100 text-emerald-700', label: '✅ Valid' },
    rejected: { class: 'bg-red-100 text-red-700', label: '❌ Ditolak' },
  },
  payment: {
    pending: { class: 'bg-amber-100 text-amber-700', label: 'Pending' },
    review: { class: 'bg-blue-100 text-blue-700', label: 'Review' },
    approved: { class: 'bg-emerald-100 text-emerald-700', label: 'Approved' },
    rejected: { class: 'bg-red-100 text-red-700', label: 'Ditolak' },
    refunded: { class: 'bg-purple-100 text-purple-700', label: 'Refund' },
  },
  commission: {
    pending: { class: 'bg-amber-100 text-amber-700', label: 'Pending' },
    approved: { class: 'bg-blue-100 text-blue-700', label: 'Approved' },
    paid: { class: 'bg-emerald-100 text-emerald-700', label: 'Dibayar' },
    cancelled: { class: 'bg-red-100 text-red-700', label: 'Batal' },
  },
  user: {
    active: { class: 'bg-emerald-100 text-emerald-700', label: 'Aktif' },
    inactive: { class: 'bg-slate-100 text-slate-600', label: 'Nonaktif' },
    suspended: { class: 'bg-red-100 text-red-700', label: 'Suspended' },
  },
}

const badgeClass = computed(() => config[props.type]?.[props.status]?.class || 'bg-slate-100 text-slate-600')
const label = computed(() => config[props.type]?.[props.status]?.label || props.status || '-')
</script>

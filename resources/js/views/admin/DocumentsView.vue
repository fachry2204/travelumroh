<template>
  <div class="space-y-6">
    <div class="page-header"><h2 class="page-title">Validasi Dokumen</h2></div>
    <div class="card p-4 flex gap-3">
      <select v-model="filters.status" @change="fetch" class="form-select w-40">
        <option value="">Semua Status</option><option value="pending">Pending</option><option value="valid">Valid</option><option value="rejected">Ditolak</option>
      </select>
    </div>
    <div class="card overflow-hidden">
      <div v-if="loading" class="p-12 text-center"><div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div></div>
      <table v-else class="w-full">
        <thead class="bg-sky-50 border-b border-sky-100"><tr><th class="table-th">Jamaah</th><th class="table-th">Tipe Dokumen</th><th class="table-th">Status</th><th class="table-th">Aksi</th></tr></thead>
        <tbody class="divide-y divide-sky-50">
          <tr v-if="items.length === 0"><td colspan="4" class="table-td text-center py-12 text-slate-400">Tidak ada dokumen</td></tr>
          <tr v-for="doc in items" :key="doc.id" class="hover:bg-sky-50/50">
            <td class="table-td"><div class="font-semibold text-slate-900 text-sm">{{ doc.pilgrim?.full_name }}</div><div class="text-xs text-slate-400">{{ doc.pilgrim?.booking?.booking_number }}</div></td>
            <td class="table-td uppercase text-sm font-medium">{{ doc.document_type }}</td>
            <td class="table-td"><StatusBadge :status="doc.status" type="document" /></td>
            <td class="table-td">
              <div v-if="doc.status === 'pending'" class="flex gap-2">
                <button @click="validate(doc.id, 'valid')" class="text-xs px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg font-medium">✅ Valid</button>
                <button @click="validate(doc.id, 'rejected')" class="text-xs px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium">❌ Tolak</button>
              </div>
              <span v-else class="text-xs text-slate-400">—</span>
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
async function fetch() { loading.value = true; const { data } = await api.get('/admin/documents', { params: filters.value }); items.value = data.data.data || data.data; loading.value = false }
async function validate(id, status) { await api.post(`/admin/documents/${id}/validate`, { status }); fetch() }
onMounted(fetch)
</script>

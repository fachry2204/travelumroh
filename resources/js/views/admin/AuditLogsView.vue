<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">Audit Logs</h2>
        <p class="text-slate-500 text-sm">Rekam jejak aktivitas sistem dan keamanan.</p>
      </div>
    </div>

    <div class="card p-6">
      <div v-if="loading" class="text-center py-10">
        <div class="w-8 h-8 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-slate-500">Memuat log...</p>
      </div>

      <div v-else-if="!logs.length" class="text-center py-10 text-slate-500">
        Belum ada riwayat aktivitas.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-slate-50 text-slate-500 text-sm border-b border-slate-100">
              <th class="p-4 font-medium rounded-tl-xl">WAKTU</th>
              <th class="p-4 font-medium">PENGGUNA</th>
              <th class="p-4 font-medium">AKSI</th>
              <th class="p-4 font-medium">TABEL</th>
              <th class="p-4 font-medium">DETAIL (NEW VALUES)</th>
              <th class="p-4 font-medium rounded-tr-xl">IP ADDRESS</th>
            </tr>
          </thead>
          <tbody class="text-sm divide-y divide-slate-100">
            <tr v-for="log in logs" :key="log.id" class="hover:bg-slate-50">
              <td class="p-4 whitespace-nowrap text-slate-500">{{ formatDateTime(log.created_at) }}</td>
              <td class="p-4">
                <div class="font-medium text-slate-900">{{ log.user?.name || 'System / Guest' }}</div>
                <div class="text-xs text-slate-400 capitalize">{{ log.user?.role_type || '-' }}</div>
              </td>
              <td class="p-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-emerald-100 text-emerald-700': log.action === 'created',
                    'bg-amber-100 text-amber-700': log.action === 'updated',
                    'bg-red-100 text-red-700': log.action === 'deleted'
                  }">
                  {{ log.action.toUpperCase() }}
                </span>
              </td>
              <td class="p-4 text-slate-700 font-mono text-xs">{{ log.table_name }} #{{ log.record_id }}</td>
              <td class="p-4">
                <button @click="showDetails(log)" class="text-sky-600 hover:underline text-xs">Lihat Perubahan</button>
              </td>
              <td class="p-4 text-xs text-slate-500 font-mono">{{ log.ip_address }}</td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination Controls -->
        <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-100">
          <button class="btn-secondary px-3 py-1 text-sm" :disabled="currentPage === 1" @click="fetchLogs(currentPage - 1)">
            &larr; Prev
          </button>
          <span class="text-sm text-slate-500">Halaman {{ currentPage }} dari {{ totalPages }}</span>
          <button class="btn-secondary px-3 py-1 text-sm" :disabled="currentPage === totalPages" @click="fetchLogs(currentPage + 1)">
            Next &rarr;
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Detail Perubahan -->
    <div v-if="selectedLog" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
          <h3 class="text-lg font-bold text-slate-900">Detail Perubahan Data</h3>
          <button @click="selectedLog = null" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>
        <div class="p-6 overflow-y-auto bg-slate-50">
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <div class="text-xs font-bold text-slate-500 mb-2">NILAI LAMA (OLD VALUES)</div>
              <pre class="bg-slate-100 p-3 rounded-lg text-xs font-mono text-slate-700 overflow-x-auto">{{ formatJSON(selectedLog.old_values) }}</pre>
            </div>
            <div>
              <div class="text-xs font-bold text-slate-500 mb-2">NILAI BARU (NEW VALUES)</div>
              <pre class="bg-sky-50 border border-sky-100 p-3 rounded-lg text-xs font-mono text-sky-900 overflow-x-auto">{{ formatJSON(selectedLog.new_values) }}</pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const logs = ref([])
const loading = ref(true)
const selectedLog = ref(null)

const currentPage = ref(1)
const totalPages = ref(1)

function formatDateTime(d) {
  return new Date(d).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })
}

function formatJSON(val) {
  if (!val) return 'Tidak ada data / Kosong'
  return JSON.stringify(val, null, 2)
}

function showDetails(log) {
  selectedLog.value = log
}

async function fetchLogs(page = 1) {
  loading.value = true
  try {
    const { data } = await api.get(`/admin/audit-logs?page=${page}`)
    if (data.success) {
      logs.value = data.data.data
      currentPage.value = data.data.current_page
      totalPages.value = data.data.last_page
    }
  } catch (error) {
    console.error('Failed fetching logs', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchLogs()
})
</script>

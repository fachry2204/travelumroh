<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">Grup Keberangkatan</h2>
        <p class="text-slate-500 text-sm">Kelola jadwal keberangkatan dan manifest jamaah</p>
      </div>
      <button @click="openModal()" class="btn-primary">
        + Buat Grup Baru
      </button>
    </div>

    <div class="card p-0">
      <div v-if="loading" class="p-12 text-center"><div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div></div>
      <table v-else class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-sky-50/50 border-b border-sky-100">
            <th class="table-th">Grup / Paket</th>
            <th class="table-th">Tgl Berangkat</th>
            <th class="table-th">Pesawat</th>
            <th class="table-th">Pembimbing</th>
            <th class="table-th">Status</th>
            <th class="table-th text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sky-50">
          <tr v-if="!items.length"><td colspan="6" class="p-8 text-center text-slate-500">Belum ada grup keberangkatan</td></tr>
          <tr v-for="item in items" :key="item.id" class="hover:bg-sky-50/50 transition-colors">
            <td class="table-td">
              <div class="font-semibold text-slate-900">{{ item.group_code }}</div>
              <div class="text-xs text-slate-500">{{ item.package?.name }}</div>
            </td>
            <td class="table-td text-sm font-medium">{{ formatDate(item.departure_date) }}</td>
            <td class="table-td text-sm text-slate-500">{{ item.airline || '-' }}<br><span class="text-xs">{{ item.flight_number_departure }}</span></td>
            <td class="table-td text-sm text-slate-500">{{ item.guide_name || '-' }}</td>
            <td class="table-td">
              <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize"
                :class="item.status === 'departed' ? 'bg-emerald-100 text-emerald-700' : (item.status === 'ready' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600')">
                {{ item.status }}
              </span>
            </td>
            <td class="table-td text-right">
              <button @click="downloadManifest(item)" class="text-emerald-600 hover:text-emerald-800 text-sm font-semibold mr-3">Manifest PDF</button>
              <button @click="openModal(item)" class="text-sky-600 hover:text-sky-800 text-sm font-semibold mr-3">Edit</button>
              <button @click="deleteItem(item.id)" class="text-red-500 hover:text-red-700 text-sm font-semibold">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-sky-100 flex justify-between items-center bg-slate-50">
          <h3 class="font-bold text-slate-900 text-lg">{{ form.id ? 'Edit' : 'Tambah' }} Grup Keberangkatan</h3>
          <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>
        <div class="p-6 overflow-y-auto">
          <form id="departureForm" @submit.prevent="saveItem" class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
              <div class="md:col-span-2">
                <label class="form-label">Paket Umroh *</label>
                <select v-model="form.package_id" class="form-select" required>
                  <option v-for="p in packages" :key="p.id" :value="p.id">{{ p.name }} ({{ p.code }})</option>
                </select>
              </div>
              <div><label class="form-label">Kode Grup *</label><input v-model="form.group_code" type="text" class="form-input" required placeholder="GRP-001" /></div>
              <div>
                <label class="form-label">Status *</label>
                <select v-model="form.status" class="form-select" required>
                  <option value="preparation">Persiapan</option>
                  <option value="ready">Siap Berangkat</option>
                  <option value="departed">Berangkat</option>
                  <option value="returned">Kembali</option>
                  <option value="cancelled">Batal</option>
                </select>
              </div>
              <div><label class="form-label">Tanggal Berangkat *</label><input v-model="form.departure_date" type="date" class="form-input" required /></div>
              <div><label class="form-label">Tanggal Pulang *</label><input v-model="form.return_date" type="date" class="form-input" required /></div>
              <div><label class="form-label">Pembimbing / Muthawif</label><input v-model="form.guide_name" type="text" class="form-input" /></div>
              <div><label class="form-label">Maskapai</label><input v-model="form.airline" type="text" class="form-input" placeholder="Garuda Indonesia" /></div>
              <div><label class="form-label">No. Penerbangan (Berangkat)</label><input v-model="form.flight_number_departure" type="text" class="form-input" placeholder="GA-980" /></div>
              <div><label class="form-label">No. Penerbangan (Pulang)</label><input v-model="form.flight_number_return" type="text" class="form-input" placeholder="GA-981" /></div>
            </div>
          </form>
        </div>
        <div class="px-6 py-4 border-t border-sky-100 flex justify-end gap-3 bg-slate-50">
          <button type="button" @click="showModal = false" class="btn-secondary">Batal</button>
          <button type="submit" form="departureForm" class="btn-primary" :disabled="saving">
            {{ saving ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const items = ref([])
const packages = ref([])
const loading = ref(true)
const showModal = ref(false)
const saving = ref(false)
const form = ref({})

function formatDate(d) { return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }

async function fetch() {
  loading.value = true
  const { data } = await api.get('/admin/departures')
  items.value = data.data
  loading.value = false
}

async function fetchPackages() {
  const { data } = await api.get('/admin/packages')
  packages.value = data.data.data || data.data
}

function openModal(item = null) {
  if (item) {
    form.value = { ...item, departure_date: item.departure_date.split('T')[0], return_date: item.return_date.split('T')[0] }
  } else {
    form.value = { package_id: '', group_code: '', departure_date: '', return_date: '', guide_name: '', airline: '', flight_number_departure: '', flight_number_return: '', status: 'preparation' }
  }
  showModal.value = true
}

async function saveItem() {
  saving.value = true
  try {
    if (form.value.id) await api.put(`/admin/departures/${form.value.id}`, form.value)
    else await api.post('/admin/departures', form.value)
    showModal.value = false
    fetch()
  } catch (e) {
    alert(e.response?.data?.message || 'Error saving data')
  } finally {
    saving.value = false
  }
}

async function deleteItem(id) {
  if (!confirm('Yakin ingin menghapus grup ini?')) return
  await api.delete(`/admin/departures/${id}`)
  fetch()
}

async function downloadManifest(item) {
  try {
    const response = await api.get(`/export/manifest/${item.id}`, { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `manifest-${item.group_code}.pdf`)
    document.body.appendChild(link)
    link.click()
  } catch (e) {
    alert('Gagal mendownload manifest.')
  }
}

onMounted(() => {
  fetch()
  fetchPackages()
})
</script>

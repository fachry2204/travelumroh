<template>
  <div class="space-y-6">
    <div class="page-header flex flex-wrap justify-between items-center gap-4">
      <div>
        <h2 class="page-title text-2xl font-extrabold text-slate-900">Manajemen Agen</h2>
        <p class="text-sm text-slate-500 mt-1">Kelola data mitra agen, alamat lokasi, link Google Maps, komisi, dan status keagenan.</p>
      </div>
      <button @click="openAddModal" class="btn-primary">+ Tambah Agen</button>
    </div>

    <div class="card overflow-hidden">
      <div v-if="loading" class="p-12 text-center">
        <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
      </div>

      <table v-else class="w-full text-left border-collapse">
        <thead class="bg-sky-50 border-b border-sky-100">
          <tr>
            <th class="table-th">Agen</th>
            <th class="table-th hidden md:table-cell">Kontak & Alamat</th>
            <th class="table-th hidden lg:table-cell">Kode Referral</th>
            <th class="table-th hidden sm:table-cell">Komisi</th>
            <th class="table-th">Status</th>
            <th class="table-th text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sky-50">
          <tr v-if="items.length === 0">
            <td colspan="6" class="table-td text-center py-12 text-slate-400">Belum ada agen terdaftar.</td>
          </tr>
          <tr v-for="a in items" :key="a.id" class="hover:bg-sky-50/50 transition-colors">
            <td class="table-td">
              <div class="font-bold text-slate-900">{{ a.user?.name }}</div>
              <div class="text-xs text-sky-600 font-mono font-semibold">{{ a.agent_code }}</div>
            </td>
            <td class="table-td hidden md:table-cell">
              <div class="text-xs text-slate-700 font-medium">{{ a.user?.email }}</div>
              <div class="text-xs text-slate-500">{{ a.user?.phone || '-' }}</div>
              <div v-if="a.address" class="text-xs text-slate-500 truncate max-w-xs mt-0.5" :title="a.address">📍 {{ a.address }}</div>
              <a v-if="a.google_maps_url" :href="a.google_maps_url" target="_blank" class="text-[11px] text-sky-600 hover:underline font-bold inline-flex items-center gap-1 mt-0.5">
                🗺️ Lihat di Google Maps &rarr;
              </a>
            </td>
            <td class="table-td hidden lg:table-cell">
              <span class="font-mono text-xs bg-sky-100 text-sky-800 px-2.5 py-1 rounded-full font-bold">{{ a.referral_code }}</span>
            </td>
            <td class="table-td hidden sm:table-cell text-sm font-semibold text-amber-700">
              {{ a.commission_type === 'percentage' ? a.commission_value + '%' : formatCurrency(a.commission_value) }}
            </td>
            <td class="table-td">
              <StatusBadge :status="a.status" type="user" />
            </td>
            <td class="table-td text-right">
              <button @click="openEditModal(a)" class="btn-outline text-xs px-3 py-1.5 font-bold">
                ✏️ Edit
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add / Edit Agent Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="showForm = false">
      <div class="bg-white rounded-3xl p-6 md:p-8 w-full max-w-2xl shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-sky-100 pb-3">
          <h3 class="font-extrabold text-slate-900 text-lg">
            {{ isEdit ? 'Edit Data Agen' : 'Tambah Agen Baru' }}
          </h3>
          <button @click="showForm = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold">&times;</button>
        </div>

        <div v-if="formError" class="p-3 bg-red-50 text-red-700 text-xs rounded-xl font-semibold border border-red-200">
          {{ formError }}
        </div>

        <form @submit.prevent="saveAgent" class="space-y-4">
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label text-xs">Nama Lengkap *</label>
              <input v-model="form.name" type="text" class="form-input" required placeholder="Nama Agen" />
            </div>
            <div>
              <label class="form-label text-xs">Email *</label>
              <input v-model="form.email" type="email" class="form-input" :required="!isEdit" :disabled="isEdit" placeholder="email@domain.com" />
            </div>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div v-if="!isEdit">
              <label class="form-label text-xs">Password *</label>
              <input v-model="form.password" type="password" class="form-input" required placeholder="Min 8 karakter" />
            </div>
            <div>
              <label class="form-label text-xs">No. Handphone / WhatsApp</label>
              <input v-model="form.phone" type="tel" class="form-input" placeholder="08123456789" />
            </div>
          </div>

          <div>
            <label class="form-label text-xs">Alamat Lengkap Agen</label>
            <textarea v-model="form.address" rows="2" class="form-input" placeholder="Jl. Raya Umroh No. 45, Kecamatan, Kota, Provinsi..."></textarea>
          </div>

          <div>
            <label class="form-label text-xs">Pin / Lokasi Google Maps</label>
            <input v-model="form.google_maps_url" type="text" class="form-input" placeholder="https://maps.app.goo.gl/xxx atau https://google.com/maps/..." />
            <span class="text-[11px] text-slate-400 mt-1 block">Tempelkan link Share Pin Lokasi Google Maps toko / kantor / rumah agen.</span>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label text-xs">Tipe Komisi</label>
              <select v-model="form.commission_type" class="form-select">
                <option value="percentage">Persentase (%)</option>
                <option value="fixed">Nominal Tetap (Rp)</option>
              </select>
            </div>
            <div>
              <label class="form-label text-xs">Nilai Komisi</label>
              <input v-model="form.commission_value" type="number" step="0.01" class="form-input" required />
            </div>
          </div>

          <div v-if="isEdit">
            <label class="form-label text-xs">Status Keagenan</label>
            <select v-model="form.status" class="form-select">
              <option value="active">Aktif</option>
              <option value="inactive">Nonaktif</option>
            </select>
          </div>

          <div class="flex gap-3 pt-4 border-t border-sky-100">
            <button type="submit" class="btn-primary flex-1 justify-center py-2.5" :disabled="submitting">
              {{ submitting ? 'Menyimpan...' : (isEdit ? '💾 Update Agen' : '➕ Simpan Agen Baru') }}
            </button>
            <button type="button" @click="showForm = false; formError = ''" class="btn-secondary px-6 py-2.5">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'
import StatusBadge from '@/components/StatusBadge.vue'

const items = ref([])
const loading = ref(true)
const showForm = ref(false)
const isEdit = ref(false)
const editId = ref(null)
const submitting = ref(false)
const formError = ref('')

const form = ref({
  name: '',
  email: '',
  password: '',
  phone: '',
  address: '',
  google_maps_url: '',
  commission_type: 'percentage',
  commission_value: 5,
  status: 'active'
})

function formatCurrency(val) { 
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val) 
}

function openAddModal() {
  isEdit.value = false
  editId.value = null
  formError.value = ''
  form.value = {
    name: '',
    email: '',
    password: '',
    phone: '',
    address: '',
    google_maps_url: '',
    commission_type: 'percentage',
    commission_value: 5,
    status: 'active'
  }
  showForm.value = true
}

function openEditModal(agent) {
  isEdit.value = true
  editId.value = agent.id
  formError.value = ''
  form.value = {
    name: agent.user?.name || '',
    email: agent.user?.email || '',
    password: '',
    phone: agent.user?.phone || '',
    address: agent.address || '',
    google_maps_url: agent.google_maps_url || '',
    commission_type: agent.commission_type || 'percentage',
    commission_value: agent.commission_value || 5,
    status: agent.status || 'active'
  }
  showForm.value = true
}

async function fetch() { 
  loading.value = true
  try {
    const { data } = await api.get('/admin/agents')
    items.value = data.data.data || data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function saveAgent() {
  submitting.value = true
  formError.value = ''
  try {
    if (isEdit.value) {
      await api.put(`/admin/agents/${editId.value}`, form.value)
    } else {
      await api.post('/admin/agents', form.value)
    }
    showForm.value = false
    fetch()
  } catch (e) { 
    formError.value = e.response?.data?.message || 'Gagal menyimpan data agen' 
  } finally { 
    submitting.value = false 
  }
}

onMounted(fetch)
</script>

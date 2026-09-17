<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header flex flex-wrap justify-between items-center gap-4">
      <div>
        <h2 class="page-title text-2xl font-extrabold text-slate-900">Manajemen Perwakilan</h2>
        <p class="text-sm text-slate-500 mt-1">Kelola cabang kantor perwakilan daerah, penanggung jawab wilayah, dan agen binaan.</p>
      </div>
      <button @click="openAddModal" class="btn-primary flex items-center gap-2">
        <span>🏢</span> + Tambah Perwakilan
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="card p-5 bg-white border border-sky-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-2xl font-bold">
          🏢
        </div>
        <div>
          <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Perwakilan</div>
          <div class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ items.length }}</div>
        </div>
      </div>
      <div class="card p-5 bg-white border border-sky-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl font-bold">
          ✅
        </div>
        <div>
          <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Perwakilan Aktif</div>
          <div class="text-2xl font-extrabold text-emerald-700 mt-0.5">
            {{ items.filter(r => r.status === 'active').length }}
          </div>
        </div>
      </div>
      <div class="card p-5 bg-white border border-sky-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl font-bold">
          👥
        </div>
        <div>
          <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Agen Binaan</div>
          <div class="text-2xl font-extrabold text-amber-700 mt-0.5">
            {{ items.reduce((acc, r) => acc + (r.agents_count || 0), 0) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table -->
    <div class="card overflow-hidden bg-white border border-sky-100 shadow-sm">
      <div v-if="loading" class="p-12 text-center">
        <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
      </div>

      <table v-else class="w-full text-left border-collapse">
        <thead class="bg-sky-50 border-b border-sky-100">
          <tr>
            <th class="table-th">Penanggung Jawab</th>
            <th class="table-th">Wilayah / Region</th>
            <th class="table-th hidden md:table-cell">Kontak & Alamat Kantor</th>
            <th class="table-th text-center">Agen Binaan</th>
            <th class="table-th">Status</th>
            <th class="table-th text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sky-50">
          <tr v-if="items.length === 0">
            <td colspan="6" class="table-td text-center py-12 text-slate-400">
              Belum ada data perwakilan wilayah. Klik "+ Tambah Perwakilan" untuk menambahkan cabang baru.
            </td>
          </tr>
          <tr v-for="r in items" :key="r.id" class="hover:bg-sky-50/50 transition-colors">
            <td class="table-td">
              <div class="font-bold text-slate-900">{{ r.user?.name }}</div>
              <div class="text-xs text-slate-400">{{ r.user?.email }}</div>
            </td>
            <td class="table-td">
              <span class="font-bold text-sky-700 bg-sky-100 px-3 py-1 rounded-full text-xs inline-block">
                📍 {{ r.region_name }}
              </span>
            </td>
            <td class="table-td hidden md:table-cell">
              <div class="text-xs text-slate-700 font-medium">📞 {{ r.user?.phone || '-' }}</div>
              <div v-if="r.office_address" class="text-xs text-slate-500 truncate max-w-xs mt-0.5" :title="r.office_address">
                🏢 {{ r.office_address }}
              </div>
            </td>
            <td class="table-td text-center">
              <span class="text-xs font-extrabold text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg">
                👥 {{ r.agents_count || 0 }} Agen
              </span>
            </td>
            <td class="table-td">
              <StatusBadge :status="r.status" type="user" />
            </td>
            <td class="table-td text-right space-x-2">
              <button @click="openEditModal(r)" class="btn-outline text-xs px-3 py-1.5 font-bold">
                ✏️ Edit
              </button>
              <button @click="deleteRepresentative(r)" class="btn-danger text-xs px-3 py-1.5 font-bold">
                🗑️ Hapus
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add / Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="showModal = false">
      <div class="bg-white rounded-3xl p-6 md:p-8 w-full max-w-xl shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-sky-100 pb-3">
          <h3 class="font-extrabold text-slate-900 text-lg">
            {{ isEdit ? 'Edit Data Perwakilan' : 'Tambah Perwakilan Wilayah Baru' }}
          </h3>
          <button @click="showModal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold">&times;</button>
        </div>

        <div v-if="formError" class="p-3 bg-red-50 text-red-700 text-xs rounded-xl font-semibold border border-red-200">
          {{ formError }}
        </div>

        <form @submit.prevent="saveRepresentative" class="space-y-4">
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label text-xs">Nama Penanggung Jawab *</label>
              <input v-model="form.name" type="text" class="form-input" required placeholder="H. Ahmad Syafi'i" />
            </div>
            <div>
              <label class="form-label text-xs">Nama Wilayah / Region *</label>
              <input v-model="form.region_name" type="text" class="form-input" required placeholder="Jawa Barat / Bandung" />
            </div>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label text-xs">Email Akun *</label>
              <input v-model="form.email" type="email" class="form-input" :required="!isEdit" :disabled="isEdit" placeholder="perwakilan.bdg@domain.com" />
            </div>
            <div v-if="!isEdit">
              <label class="form-label text-xs">Password Akun *</label>
              <input v-model="form.password" type="password" class="form-input" required placeholder="Min 8 karakter" />
            </div>
            <div v-else>
              <label class="form-label text-xs">No. Handphone / WhatsApp</label>
              <input v-model="form.phone" type="tel" class="form-input" placeholder="08123456789" />
            </div>
          </div>

          <div v-if="!isEdit">
            <label class="form-label text-xs">No. Handphone / WhatsApp</label>
            <input v-model="form.phone" type="tel" class="form-input" placeholder="08123456789" />
          </div>

          <div>
            <label class="form-label text-xs">Alamat Kantor Perwakilan</label>
            <textarea v-model="form.office_address" rows="3" class="form-input" placeholder="Jl. Asia Afrika No. 123, Bandung..."></textarea>
          </div>

          <div v-if="isEdit">
            <label class="form-label text-xs">Status Perwakilan</label>
            <select v-model="form.status" class="form-select">
              <option value="active">Aktif</option>
              <option value="inactive">Nonaktif</option>
            </select>
          </div>

          <div class="flex gap-3 pt-4 border-t border-sky-100">
            <button type="submit" class="btn-primary flex-1 justify-center py-2.5" :disabled="submitting">
              {{ submitting ? 'Menyimpan...' : (isEdit ? '💾 Update Perwakilan' : '🏢 Simpan Perwakilan Baru') }}
            </button>
            <button type="button" @click="showModal = false" class="btn-secondary px-6 py-2.5">Batal</button>
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
const showModal = ref(false)
const isEdit = ref(false)
const editId = ref(null)
const submitting = ref(false)
const formError = ref('')

const form = ref({
  name: '',
  region_name: '',
  email: '',
  password: '',
  phone: '',
  office_address: '',
  status: 'active'
})

function openAddModal() {
  isEdit.value = false
  editId.value = null
  formError.value = ''
  form.value = {
    name: '',
    region_name: '',
    email: '',
    password: '',
    phone: '',
    office_address: '',
    status: 'active'
  }
  showModal.value = true
}

function openEditModal(rep) {
  isEdit.value = true
  editId.value = rep.id
  formError.value = ''
  form.value = {
    name: rep.user?.name || '',
    region_name: rep.region_name || '',
    email: rep.user?.email || '',
    password: '',
    phone: rep.user?.phone || '',
    office_address: rep.office_address || '',
    status: rep.status || 'active'
  }
  showModal.value = true
}

async function fetch() {
  loading.value = true
  try {
    const { data } = await api.get('/admin/representatives')
    items.value = data.data.data || data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function saveRepresentative() {
  submitting.value = true
  formError.value = ''
  try {
    if (isEdit.value) {
      await api.put(`/admin/representatives/${editId.value}`, form.value)
    } else {
      await api.post('/admin/representatives', form.value)
    }
    showModal.value = false
    fetch()
  } catch (e) {
    formError.value = e.response?.data?.message || 'Gagal menyimpan perwakilan'
  } finally {
    submitting.value = false
  }
}

async function deleteRepresentative(rep) {
  if (!confirm(`Yakin ingin menghapus perwakilan "${rep.region_name}" (${rep.user?.name})?`)) return
  try {
    await api.delete(`/admin/representatives/${rep.id}`)
    fetch()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menghapus perwakilan')
  }
}

onMounted(fetch)
</script>

<template>
  <div class="space-y-6">
    <div class="page-header flex justify-between items-center">
      <h2 class="page-title">Manajemen Booking</h2>
      <button @click="openCreateModal" class="btn-primary flex items-center gap-2 text-sm px-4 py-2">
        <span>➕</span> Tambah Booking
      </button>
    </div>

    <div class="card p-4 flex flex-wrap gap-3">
      <input v-model="search" @input="fetch" type="text" class="form-input w-64" placeholder="Cari nomor booking, pemesan..." />
      <select v-model="filters.booking_status" @change="fetch" class="form-select w-40">
        <option value="">Semua Status</option>
        <option value="pending">Pending</option>
        <option value="dp">Sudah DP</option>
        <option value="paid">Lunas</option>
        <option value="cancelled">Batal</option>
      </select>
    </div>

    <div class="card overflow-hidden">
      <div v-if="loading" class="p-12 text-center">
        <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
      </div>
      <table v-else class="w-full">
        <thead class="bg-sky-50 border-b border-sky-100">
          <tr>
            <th class="table-th">No. Booking</th>
            <th class="table-th hidden md:table-cell">Paket</th>
            <th class="table-th hidden lg:table-cell">Pemesan</th>
            <th class="table-th hidden lg:table-cell">Total</th>
            <th class="table-th">Status</th>
            <th class="table-th text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sky-50">
          <tr v-if="items.length === 0">
            <td colspan="6" class="table-td text-center py-12 text-slate-400">Belum ada booking</td>
          </tr>
          <tr v-for="b in items" :key="b.id" class="hover:bg-sky-50/50">
            <td class="table-td">
              <div class="font-mono font-semibold text-slate-900 text-sm">{{ b.booking_number }}</div>
              <div class="text-xs text-slate-400">{{ formatDate(b.created_at) }}</div>
            </td>
            <td class="table-td hidden md:table-cell">
              <div class="text-sm font-medium text-slate-900 line-clamp-1">{{ b.package?.name }}</div>
              <div class="text-xs text-slate-400">{{ b.total_pilgrims }} jamaah ({{ b.room_type }})</div>
            </td>
            <td class="table-td hidden lg:table-cell">
              <div class="text-sm font-medium text-slate-900">{{ b.user?.name || '-' }}</div>
              <div class="text-xs text-slate-400">{{ b.user?.phone || '-' }}</div>
            </td>
            <td class="table-td hidden lg:table-cell font-semibold text-sky-600">{{ formatCurrency(b.total_amount) }}</td>
            <td class="table-td"><StatusBadge :status="b.booking_status" type="booking" /></td>
            <td class="table-td text-right space-x-1">
              <router-link :to="`/admin/booking/${b.id}`" class="text-xs px-2.5 py-1.5 bg-sky-100 hover:bg-sky-200 text-sky-700 rounded-lg font-medium">Detail</router-link>
              <button @click="openEditModal(b)" class="text-xs px-2.5 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg font-medium">Edit</button>
              <button @click="deleteBooking(b)" class="text-xs px-2.5 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create / Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-sky-100 pb-3">
          <h3 class="font-bold text-slate-900 text-lg">{{ isEdit ? 'Edit Booking' : 'Tambah Booking Baru' }}</h3>
          <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form @submit.prevent="saveBooking" class="space-y-4">
          <div>
            <label class="form-label">Paket Umroh *</label>
            <select v-model="form.package_id" class="form-select" required :disabled="isEdit">
              <option value="" disabled>-- Pilih Paket --</option>
              <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">
                {{ pkg.name }} (Sisa: {{ pkg.remaining_seat }})
              </option>
            </select>
          </div>

          <div v-if="!isEdit" class="space-y-3">
            <h4 class="font-semibold text-slate-700 text-sm border-b pb-1">Data Pemesan / Jamaah Utama</h4>
            <div>
              <label class="form-label">Nama Lengkap *</label>
              <input v-model="form.full_name" type="text" class="form-input" required placeholder="Nama Pemesan" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="form-label">Email *</label>
                <input v-model="form.email" type="email" class="form-input" required placeholder="email@domain.com" />
              </div>
              <div>
                <label class="form-label">No. WhatsApp *</label>
                <input v-model="form.phone" type="tel" class="form-input" required placeholder="081234567890" />
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Tipe Kamar *</label>
              <select v-model="form.room_type" class="form-select" required>
                <option value="quad">Quad (4 Orang)</option>
                <option value="triple">Triple (3 Orang)</option>
                <option value="double">Double (2 Orang)</option>
              </select>
            </div>
            <div>
              <label class="form-label">Total Jamaah *</label>
              <input v-model.number="form.total_pilgrims" type="number" min="1" max="20" class="form-input" required />
            </div>
          </div>

          <div v-if="isEdit" class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Total Tagihan (Rp)</label>
              <input v-model.number="form.total_amount" type="number" class="form-input" />
            </div>
            <div>
              <label class="form-label">Sudah Dibayar (Rp)</label>
              <input v-model.number="form.paid_amount" type="number" class="form-input" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Status Booking</label>
              <select v-model="form.booking_status" class="form-select">
                <option value="pending">Pending</option>
                <option value="dp">Sudah DP</option>
                <option value="paid">Lunas</option>
                <option value="cancelled">Batal</option>
              </select>
            </div>
            <div>
              <label class="form-label">Status Dokumen</label>
              <select v-model="form.document_status" class="form-select">
                <option value="incomplete">Belum Lengkap</option>
                <option value="review">Review</option>
                <option value="complete">Lengkap</option>
              </select>
            </div>
          </div>

          <div>
            <label class="form-label">Catatan</label>
            <textarea v-model="form.notes" class="form-input" rows="2" placeholder="Catatan tambahan..."></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-3 border-t border-sky-100">
            <button type="button" @click="showModal = false" class="btn-secondary text-sm">Batal</button>
            <button type="submit" class="btn-primary text-sm px-5" :disabled="saving">
              {{ saving ? 'Saving...' : 'Simpan' }}
            </button>
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
const packages = ref([])
const loading = ref(true)
const saving = ref(false)
const search = ref('')
const filters = ref({ booking_status: '' })

const showModal = ref(false)
const isEdit = ref(false)
const editId = ref(null)

const form = ref({
  package_id: '',
  full_name: '',
  email: '',
  phone: '',
  room_type: 'quad',
  total_pilgrims: 1,
  total_amount: 0,
  paid_amount: 0,
  booking_status: 'pending',
  document_status: 'incomplete',
  visa_status: 'not_submitted',
  notes: ''
})

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatCurrency(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0)
}

async function fetch() {
  loading.value = true
  try {
    const { data } = await api.get('/admin/bookings', { params: { search: search.value, ...filters.value } })
    items.value = data.data.data || data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function fetchPackages() {
  try {
    const { data } = await api.get('/admin/packages')
    packages.value = data.data.data || data.data
  } catch (e) {
    console.error(e)
  }
}

function openCreateModal() {
  isEdit.value = false
  editId.value = null
  form.value = {
    package_id: packages.value[0]?.id || '',
    full_name: '',
    email: '',
    phone: '',
    room_type: 'quad',
    total_pilgrims: 1,
    booking_status: 'pending',
    document_status: 'incomplete',
    visa_status: 'not_submitted',
    notes: ''
  }
  showModal.value = true
}

function openEditModal(b) {
  isEdit.value = true
  editId.value = b.id
  form.value = {
    package_id: b.package_id,
    room_type: b.room_type,
    total_pilgrims: b.total_pilgrims,
    total_amount: b.total_amount,
    paid_amount: b.paid_amount,
    booking_status: b.booking_status,
    document_status: b.document_status,
    visa_status: b.visa_status,
    notes: b.notes || ''
  }
  showModal.value = true
}

async function saveBooking() {
  saving.value = true
  try {
    if (isEdit.value) {
      await api.put(`/admin/bookings/${editId.value}`, form.value)
    } else {
      await api.post('/admin/bookings', form.value)
    }
    showModal.value = false
    await fetch()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menyimpan booking')
  } finally {
    saving.value = false
  }
}

async function deleteBooking(b) {
  if (!confirm(`Apakah Anda yakin ingin menghapus booking ${b.booking_number}?`)) return
  try {
    await api.delete(`/admin/bookings/${b.id}`)
    await fetch()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menghapus booking')
  }
}

onMounted(() => {
  fetch()
  fetchPackages()
})
</script>

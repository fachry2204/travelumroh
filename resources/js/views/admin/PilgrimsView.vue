<template>
  <div class="space-y-6">
    <div class="page-header flex justify-between items-center">
      <h2 class="page-title">Data Jamaah</h2>
      <button @click="openCreateModal" class="btn-primary flex items-center gap-2 text-sm px-4 py-2">
        <span>➕</span> Tambah Jamaah
      </button>
    </div>

    <div class="card p-4 flex flex-wrap gap-3">
      <input v-model="search" @input="fetch" type="text" class="form-input w-72" placeholder="Cari nama, NIK, nomor paspor..." />
    </div>

    <div class="card overflow-hidden">
      <div v-if="loading" class="p-12 text-center">
        <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
      </div>
      <table v-else class="w-full">
        <thead class="bg-sky-50 border-b border-sky-100">
          <tr>
            <th class="table-th">Nama Jamaah</th>
            <th class="table-th hidden md:table-cell">NIK / Paspor</th>
            <th class="table-th hidden lg:table-cell">No. Booking & Paket</th>
            <th class="table-th hidden md:table-cell">Dokumen</th>
            <th class="table-th text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-sky-50">
          <tr v-if="items.length === 0">
            <td colspan="5" class="table-td text-center py-12 text-slate-400">Belum ada data jamaah</td>
          </tr>
          <tr v-for="p in items" :key="p.id" class="hover:bg-sky-50/50">
            <td class="table-td">
              <div class="font-semibold text-slate-900 flex items-center gap-2">
                {{ p.full_name }}
                <span v-if="p.gender" class="text-xs px-2 py-0.5 bg-sky-50 text-sky-700 rounded-full capitalize">
                  {{ p.gender === 'male' ? 'L' : 'P' }}
                </span>
              </div>
              <div class="text-xs text-slate-400">{{ p.phone || '-' }} • {{ p.email || '-' }}</div>
            </td>
            <td class="table-td hidden md:table-cell text-xs">
              <div>NIK: <span class="font-mono text-slate-700">{{ p.nik || '-' }}</span></div>
              <div>Paspor: <span class="font-mono text-slate-700">{{ p.passport_number || '-' }}</span></div>
            </td>
            <td class="table-td hidden lg:table-cell">
              <router-link v-if="p.booking" :to="`/admin/booking/${p.booking.id}`" class="font-mono text-sm text-sky-600 font-semibold hover:underline">
                {{ p.booking.booking_number }}
              </router-link>
              <div class="text-xs text-slate-400 line-clamp-1">{{ p.booking?.package?.name || '-' }}</div>
            </td>
            <td class="table-td hidden md:table-cell">
              <span class="text-xs font-semibold px-2.5 py-1 bg-sky-50 text-sky-700 rounded-full">
                {{ p.documents?.length || 0 }} file
              </span>
            </td>
            <td class="table-td text-right space-x-1">
              <button @click="openEditModal(p)" class="text-xs px-2.5 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg font-medium">Edit</button>
              <button @click="deletePilgrim(p)" class="text-xs px-2.5 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create / Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
      <div class="bg-white rounded-3xl max-w-5xl w-full p-6 md:p-8 shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-sky-100 pb-3">
          <div>
            <div class="flex items-center gap-3">
              <h3 class="font-extrabold text-slate-900 text-xl">{{ isEdit ? 'Edit Data Jamaah' : 'Tambah Jamaah Baru' }}</h3>
              <button v-if="!isEdit && isDraftFilled" type="button" @click="clearPilgrimDraft" class="text-xs text-amber-700 hover:text-amber-900 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg font-bold transition-all">
                🧹 Reset Draft Form
              </button>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Lengkapi biodata dan dokumen kelengkapan SISKOPATUH Kemenag RI.</p>
          </div>
          <button @click="showModal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-lg transition-colors" title="Tutup modal (Draft tetap tersimpan)">&times;</button>
        </div>

        <form @submit.prevent="savePilgrim" class="space-y-6">
          <div v-if="!isEdit" class="p-4 bg-sky-50/70 border border-sky-200 rounded-2xl space-y-1">
            <label class="form-label text-xs font-bold text-sky-900">Pilih Pemesanan / Booking Paket *</label>
            <select v-model="form.booking_id" class="form-select bg-white" required>
              <option value="" disabled>-- Pilih Booking --</option>
              <option v-for="b in bookings" :key="b.id" :value="b.id">
                {{ b.booking_number }} - {{ b.user?.name || 'Tanpa Pemesan' }} ({{ b.package?.name }})
              </option>
            </select>
          </div>

          <!-- SEKSI A: BIODATA UTAMA -->
          <div class="space-y-3">
            <h4 class="font-extrabold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-2">
              <span>👤</span> A. Biodata Identitas Diri Jamaah
            </h4>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
              <div class="md:col-span-2">
                <label class="form-label text-xs">Nama Lengkap Jamaah (Sesuai KTP/Paspor) *</label>
                <input v-model="form.full_name" type="text" class="form-input" required placeholder="H. Ahmad Syafi'i" />
              </div>
              <div>
                <label class="form-label text-xs">Jenis Kelamin</label>
                <select v-model="form.gender" class="form-select">
                  <option value="">-- Pilih --</option>
                  <option value="male">Laki-Laki</option>
                  <option value="female">Perempuan</option>
                </select>
              </div>
              <div>
                <label class="form-label text-xs">No. NIK (KTP)</label>
                <input v-model="form.nik" type="text" inputmode="numeric" maxlength="16" @input="form.nik = form.nik.replace(/\D/g, '').slice(0, 16)" class="form-input" placeholder="16 digit NIK KTP" />
                <span v-if="form.nik && form.nik.length < 16" class="text-[11px] text-amber-600 font-semibold mt-1 block">⚠️ Harus 16 angka (saat ini {{ form.nik.length }})</span>
              </div>
              <div>
                <label class="form-label text-xs">No. Kartu Keluarga (KK)</label>
                <input v-model="form.family_card_number" type="text" inputmode="numeric" maxlength="16" @input="form.family_card_number = form.family_card_number.replace(/\D/g, '').slice(0, 16)" class="form-input" placeholder="16 digit Nomor KK" />
              </div>
              <div>
                <label class="form-label text-xs">No. HP / WA</label>
                <input v-model="form.phone" type="tel" class="form-input" placeholder="081234567890" />
              </div>
              <div>
                <label class="form-label text-xs">Email</label>
                <input v-model="form.email" type="email" class="form-input" placeholder="jamaah@domain.com" />
              </div>
              <div>
                <label class="form-label text-xs">Tempat Lahir</label>
                <input v-model="form.birth_place" type="text" class="form-input" placeholder="Kota Kelahiran" />
              </div>
              <div>
                <label class="form-label text-xs">Tanggal Lahir</label>
                <input v-model="form.birth_date" type="date" class="form-input" />
              </div>
              <div class="sm:col-span-2 md:col-span-3">
                <label class="form-label text-xs">Alamat Lengkap Domisili</label>
                <textarea v-model="form.address" class="form-input" rows="2" placeholder="Jl. Raya Umroh No. 123..."></textarea>
              </div>
            </div>
          </div>

          <!-- SEKSI B: PASPOR & OPERASIONAL -->
          <div class="space-y-3 pt-2">
            <h4 class="font-extrabold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-2">
              <span>🛂</span> B. Paspor & Data Perjalanan Umroh
            </h4>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
              <div>
                <label class="form-label text-xs">No. Paspor Asli</label>
                <input v-model="form.passport_number" type="text" class="form-input" placeholder="Nomor Paspor" />
              </div>
              <div>
                <label class="form-label text-xs">Tipe Kamar</label>
                <select v-model="form.room_type" class="form-select">
                  <option value="quad">Quad (4 Orang)</option>
                  <option value="triple">Triple (3 Orang)</option>
                  <option value="double">Double (2 Orang)</option>
                  <option value="single">Single (1 Orang)</option>
                </select>
              </div>
              <div>
                <label class="form-label text-xs">No. Kamar Hotel</label>
                <input v-model="form.room_number" type="text" class="form-input" placeholder="Contoh: 304" />
              </div>
              <div>
                <label class="form-label text-xs">Kontak Darurat (Nama)</label>
                <input v-model="form.emergency_contact_name" type="text" class="form-input" placeholder="Nama Keluarga" />
              </div>
              <div>
                <label class="form-label text-xs">Kontak Darurat (No. HP)</label>
                <input v-model="form.emergency_contact_phone" type="tel" class="form-input" placeholder="No HP Keluarga" />
              </div>
            </div>
          </div>

          <!-- SEKSI C: DOKUMEN SISKOPATUH KEMENAG RI (Tampil baik Tambah Jamaah maupun Edit Jamaah) -->
          <div class="space-y-3 pt-2">
            <div class="flex justify-between items-center border-b border-sky-100 pb-2">
              <div>
                <h4 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                  <span>🏛️</span> C. Dokumen Syarat SISKOPATUH Kemenag RI
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Upload kelengkapan dokumen resmi siskopatuh untuk pendaftaran umroh jamaah.</p>
              </div>
            </div>

            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
              <div v-for="docSlot in siskopatuhDocTypes" :key="docSlot.key"
                   class="p-3 bg-slate-50 hover:bg-sky-50/50 border border-slate-200 hover:border-sky-300 rounded-2xl transition-all space-y-2 flex flex-col justify-between">
                <div>
                  <div class="flex justify-between items-start gap-2">
                    <div>
                      <div class="font-bold text-slate-900 text-xs">{{ docSlot.label }}</div>
                      <div class="text-[10px] text-slate-500">{{ docSlot.desc }}</div>
                    </div>

                    <!-- EDIT MODE STATUS -->
                    <template v-if="isEdit">
                      <span v-if="getPilgrimDocStatus(docSlot.key)" 
                            :class="getPilgrimDocStatus(docSlot.key) === 'valid' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200'"
                            class="text-[10px] font-extrabold px-2 py-0.5 rounded-full uppercase flex-shrink-0">
                        {{ getPilgrimDocStatus(docSlot.key) === 'valid' ? '✅ Valid' : '⏳ Review' }}
                      </span>
                      <span v-else class="text-[10px] font-bold px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full flex-shrink-0">
                        ❌ Belum Ada
                      </span>
                    </template>

                    <!-- CREATE MODE PENDING STATUS -->
                    <template v-else>
                      <span v-if="pendingDocs[docSlot.key]" class="text-[10px] font-extrabold px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-full flex-shrink-0">
                        📎 Siap Upload
                      </span>
                      <span v-else class="text-[10px] font-bold px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full flex-shrink-0">
                        Opsional
                      </span>
                    </template>
                  </div>
                </div>

                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-200/60">
                  <!-- EDIT MODE LINK -->
                  <template v-if="isEdit">
                    <a v-if="getPilgrimDocPath(docSlot.key)" :href="storageUrl(getPilgrimDocPath(docSlot.key))" target="_blank" class="text-[11px] text-sky-600 hover:underline font-bold flex items-center gap-1">
                      🔍 Lihat Dokumen
                    </a>
                    <span v-else class="text-[11px] text-slate-400 italic">Pilih file...</span>

                    <label class="btn-outline text-[11px] px-2.5 py-1 cursor-pointer font-bold flex items-center gap-1">
                      <span>📤</span> {{ getPilgrimDocPath(docSlot.key) ? 'Ganti' : 'Upload' }}
                      <input type="file" @change="uploadSiskopatuhDoc(docSlot.key, $event)" accept=".jpg,.jpeg,.png,.pdf" class="hidden" :disabled="uploadingDocType === docSlot.key" />
                    </label>
                  </template>

                  <!-- CREATE MODE PENDING FILE HANDLER -->
                  <template v-else>
                    <span v-if="pendingDocs[docSlot.key]" class="text-[11px] text-emerald-700 font-bold truncate max-w-[130px]" :title="pendingDocs[docSlot.key].name">
                      📄 {{ pendingDocs[docSlot.key].name }}
                    </span>
                    <span v-else class="text-[11px] text-slate-400 italic">Belum ada file</span>

                    <div class="flex items-center gap-1">
                      <button v-if="pendingDocs[docSlot.key]" type="button" @click="delete pendingDocs[docSlot.key]" class="text-red-500 hover:text-red-700 text-xs font-bold px-1" title="Hapus file">✕</button>
                      <label class="btn-outline text-[11px] px-2.5 py-1 cursor-pointer font-bold flex items-center gap-1">
                        <span>📤</span> {{ pendingDocs[docSlot.key] ? 'Ganti' : 'Pilih File' }}
                        <input type="file" @change="handlePendingDocSelect(docSlot.key, $event)" accept=".jpg,.jpeg,.png,.pdf" class="hidden" />
                      </label>
                    </div>
                  </template>
                </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-sky-100">
            <button type="button" @click="showModal = false" class="btn-secondary text-sm px-6 py-2.5">Batal</button>
            <button type="submit" class="btn-primary text-sm px-6 py-2.5 font-bold" :disabled="saving">
              {{ saving ? 'Menyimpan...' : (isEdit ? '💾 Update Data Jamaah' : '➕ Simpan Jamaah Baru') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/plugins/axios'

const items = ref([])
const bookings = ref([])
const loading = ref(true)
const saving = ref(false)
const search = ref('')

const showModal = ref(false)
const isEdit = ref(false)
const editId = ref(null)

const siskopatuhDocTypes = [
  { key: 'ktp', label: '📄 KTP Jamaah', desc: 'Kartu Tanda Penduduk Asli' },
  { key: 'kk', label: '📜 Kartu Keluarga (KK)', desc: 'Kartu Keluarga Terkini' },
  { key: 'passport', label: '🛂 Paspor Asli (Biodata)', desc: 'Halaman 2-3 Paspor RI' },
  { key: 'vaccine_meningitis', label: '💉 Sertifikat Vaksin Meningitis (ICV)', desc: 'Buku Kuning Meningitis' },
  { key: 'photo', label: '📸 Pas Foto (Latar Putih 4x6)', desc: 'Latar Belakang Putih 80% Wajah' },
]

const uploadingDocType = ref(null)
const pendingDocs = ref({})

function handlePendingDocSelect(key, event) {
  const file = event.target.files[0]
  if (file) {
    pendingDocs.value[key] = file
  }
}

const isDraftFilled = computed(() => {
  return Boolean(
    form.value.full_name || 
    form.value.nik || 
    form.value.phone || 
    form.value.passport_number || 
    Object.keys(pendingDocs.value).length > 0
  )
})

function clearPilgrimDraft() {
  pendingDocs.value = {}
  form.value = {
    booking_id: bookings.value[0]?.id || '',
    full_name: '',
    gender: '',
    nik: '',
    family_card_number: '',
    passport_number: '',
    phone: '',
    email: '',
    birth_place: '',
    birth_date: '',
    marital_status: '',
    job: '',
    education: '',
    address: '',
    room_type: 'quad',
    room_number: '',
    emergency_contact_name: '',
    emergency_contact_phone: ''
  }
}

const currentPilgrimDocs = computed(() => {
  if (!editId.value || !items.value) return []
  const pilgrim = items.value.find(p => p.id === editId.value)
  return pilgrim?.documents || []
})

function getPilgrimDocStatus(type) {
  if (!currentPilgrimDocs.value) return null
  const doc = currentPilgrimDocs.value.find(d => d.document_type === type)
  return doc ? doc.status : null
}

function getPilgrimDocPath(type) {
  if (!currentPilgrimDocs.value) return null
  const doc = currentPilgrimDocs.value.find(d => d.document_type === type)
  return doc ? doc.file_path : null
}

const storageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `/storage/${path.replace(/^\//, '')}`
}

async function uploadSiskopatuhDoc(docType, event) {
  const file = event.target.files[0]
  if (!file) return
  if (!editId.value) return

  uploadingDocType.value = docType
  try {
    const formData = new FormData()
    formData.append('document_type', docType)
    formData.append('file', file)
    await api.post(`/admin/documents/pilgrims/${editId.value}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    await fetch()
    alert('Dokumen Siskopatuh berhasil diupload!')
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal upload dokumen Siskopatuh')
  } finally {
    uploadingDocType.value = null
  }
}

const form = ref({
  booking_id: '',
  full_name: '',
  gender: '',
  nik: '',
  family_card_number: '',
  passport_number: '',
  phone: '',
  email: '',
  birth_place: '',
  birth_date: '',
  marital_status: '',
  job: '',
  education: '',
  address: '',
  room_type: 'quad',
  room_number: '',
  emergency_contact_name: '',
  emergency_contact_phone: ''
})

async function fetch() {
  loading.value = true
  try {
    const { data } = await api.get('/admin/pilgrims', { params: { search: search.value } })
    items.value = data.data.data || data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function fetchBookings() {
  try {
    const { data } = await api.get('/admin/bookings', { params: { per_page: 100 } })
    bookings.value = data.data.data || data.data
  } catch (e) {
    console.error(e)
  }
}

function openCreateModal() {
  if (isEdit.value) {
    isEdit.value = false
    editId.value = null
    clearPilgrimDraft()
  } else {
    isEdit.value = false
    editId.value = null
    if (!form.value.booking_id) {
      form.value.booking_id = bookings.value[0]?.id || ''
    }
  }
  showModal.value = true
}

function openEditModal(p) {
  isEdit.value = true
  editId.value = p.id
  form.value = {
    booking_id: p.booking_id,
    full_name: p.full_name || '',
    gender: p.gender || '',
    nik: p.nik || '',
    family_card_number: p.family_card_number || '',
    passport_number: p.passport_number || '',
    phone: p.phone || '',
    email: p.email || '',
    birth_place: p.birth_place || '',
    birth_date: p.birth_date ? p.birth_date.split('T')[0] : '',
    marital_status: p.marital_status || '',
    job: p.job || '',
    education: p.education || '',
    address: p.address || '',
    room_type: p.room_type || 'quad',
    room_number: p.room_number || '',
    emergency_contact_name: p.emergency_contact_name || '',
    emergency_contact_phone: p.emergency_contact_phone || ''
  }
  showModal.value = true
}

async function savePilgrim() {
  if (form.value.nik && form.value.nik.length !== 16) {
    alert('NIK harus 16 angka.')
    return
  }
  saving.value = true
  try {
    if (isEdit.value) {
      await api.put(`/admin/pilgrims/${editId.value}`, form.value)
    } else {
      const res = await api.post('/admin/pilgrims', form.value)
      const newPilgrimId = res.data?.data?.id

      if (newPilgrimId && Object.keys(pendingDocs.value).length > 0) {
        for (const docKey of Object.keys(pendingDocs.value)) {
          const file = pendingDocs.value[docKey]
          if (file) {
            const formData = new FormData()
            formData.append('document_type', docKey)
            formData.append('file', file)
            await api.post(`/admin/documents/pilgrims/${newPilgrimId}`, formData, {
              headers: { 'Content-Type': 'multipart/form-data' }
            })
          }
        }
      }
      clearPilgrimDraft()
    }
    showModal.value = false
    await fetch()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menyimpan data jamaah')
  } finally {
    saving.value = false
  }
}

async function deletePilgrim(p) {
  if (!confirm(`Apakah Anda yakin ingin menghapus data jamaah ${p.full_name}?`)) return
  try {
    await api.delete(`/admin/pilgrims/${p.id}`)
    await fetch()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menghapus data jamaah')
  }
}

onMounted(() => {
  fetch()
  fetchBookings()
})
</script>

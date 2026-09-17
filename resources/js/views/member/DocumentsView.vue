<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">Dokumen Jamaah</h2>
        <p class="text-slate-500 text-sm">Upload dan pantau status dokumen untuk setiap jamaah</p>
      </div>
    </div>

    <div v-if="loading" class="card p-12 text-center">
      <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
    </div>

    <template v-else-if="!booking">
      <div class="card p-12 text-center">
        <div class="text-5xl mb-3">📄</div>
        <p class="text-slate-500">Tidak ada booking aktif. <router-link to="/paket-umroh" class="text-sky-600">Daftar paket dulu.</router-link></p>
      </div>
    </template>

    <template v-else>
      <!-- Select booking -->
      <div v-if="bookings.length > 1" class="card p-4">
        <label class="form-label">Pilih Booking</label>
        <select v-model="selectedBookingId" @change="loadBooking" class="form-select w-full md:w-96">
          <option v-for="b in bookings" :key="b.id" :value="b.id">
            {{ b.booking_number }} — {{ b.package?.name }}
          </option>
        </select>
      </div>

      <!-- Pilgrim tabs -->
      <div class="flex gap-2 flex-wrap mb-2">
        <button v-for="(p, i) in booking.pilgrims" :key="p.id"
          @click="activePilgrim = i"
          class="px-4 py-2 rounded-xl text-sm font-semibold transition-all"
          :class="activePilgrim === i ? 'gradient-primary text-white shadow-md' : 'bg-white border border-sky-200 text-slate-600'">
          {{ p.full_name || `Jamaah ${i + 1}` }}
        </button>
      </div>

      <div v-if="currentPilgrim" class="grid md:grid-cols-2 gap-4">
        <!-- Document cards -->
        <div v-for="docType in documentTypes" :key="docType.key" class="card p-5">
          <div class="flex items-start justify-between gap-3 mb-4">
            <div>
              <div class="font-bold text-slate-900">{{ docType.label }}</div>
              <div class="text-xs text-slate-400 mt-0.5">{{ docType.desc }}</div>
            </div>
            <StatusBadge :status="getDoc(docType.key)?.status || 'not_uploaded'" type="document_ext" />
          </div>

          <!-- Existing file -->
          <div v-if="getDoc(docType.key)" class="mb-3 p-3 bg-sky-50 rounded-xl flex items-center gap-3">
            <div class="text-2xl">📎</div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium text-slate-900 truncate">File diupload</div>
              <div class="text-xs text-slate-400">{{ formatDate(getDoc(docType.key).updated_at) }}</div>
            </div>
            <a :href="`/storage/${getDoc(docType.key).file_path}`" target="_blank"
              class="text-xs px-3 py-1.5 bg-sky-100 hover:bg-sky-200 text-sky-700 rounded-lg font-medium">Lihat</a>
          </div>

          <!-- Rejection note -->
          <div v-if="getDoc(docType.key)?.status === 'rejected' && getDoc(docType.key)?.note"
            class="mb-3 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs">
            <strong>Catatan Admin:</strong> {{ getDoc(docType.key).note }}
          </div>

          <!-- Upload -->
          <div>
            <label class="block cursor-pointer">
              <div class="border-2 border-dashed border-sky-200 hover:border-sky-400 rounded-xl p-4 text-center transition-colors"
                :class="uploading[docType.key] ? 'bg-sky-50' : 'hover:bg-sky-50/50'">
                <div v-if="uploading[docType.key]" class="flex items-center justify-center gap-2 text-sky-600">
                  <div class="w-4 h-4 border-2 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
                  <span class="text-sm font-medium">Mengupload...</span>
                </div>
                <div v-else class="text-sm text-slate-500">
                  <span class="font-semibold text-sky-600">Klik untuk upload</span> atau drag & drop<br>
                  <span class="text-xs">JPG, PNG, PDF (maks. 5MB)</span>
                </div>
              </div>
              <input type="file" class="hidden" accept=".jpg,.jpeg,.png,.pdf"
                @change="uploadDocument($event, docType.key)" :disabled="uploading[docType.key]" />
            </label>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/plugins/axios'
import StatusBadge from '@/components/StatusBadge.vue'

const bookings = ref([])
const booking = ref(null)
const loading = ref(true)
const activePilgrim = ref(0)
const uploading = ref({})
const selectedBookingId = ref(null)

const documentTypes = [
  { key: 'ktp', label: 'KTP', desc: 'Kartu Tanda Penduduk yang masih berlaku' },
  { key: 'kk', label: 'Kartu Keluarga', desc: 'Kartu Keluarga (KK) terbaru' },
  { key: 'passport', label: 'Paspor', desc: 'Halaman data diri paspor' },
  { key: 'photo', label: 'Pas Foto 4×6', desc: 'Foto terbaru background putih/merah' },
  { key: 'marriage_book', label: 'Buku Nikah', desc: 'Untuk jamaah yang sudah menikah' },
  { key: 'vaccine', label: 'Sertifikat Vaksin', desc: 'Sertifikat vaksin meningitis & COVID' },
  { key: 'mahram', label: 'Dokumen Mahram', desc: 'Untuk jamaah wanita tanpa mahram' },
  { key: 'other', label: 'Dokumen Lain', desc: 'Dokumen tambahan jika diperlukan' },
]

const currentPilgrim = computed(() => booking.value?.pilgrims?.[activePilgrim.value])

function getDoc(type) {
  return currentPilgrim.value?.documents?.find(d => d.document_type === type)
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

async function loadBooking() {
  const { data } = await api.get(`/member/bookings/${selectedBookingId.value}`)
  booking.value = data.data
}

async function uploadDocument(event, docType) {
  const file = event.target.files?.[0]
  if (!file) return
  uploading.value[docType] = true
  try {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('document_type', docType)
    await api.post(`/member/pilgrims/${currentPilgrim.value.id}/documents`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    await loadBooking()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal upload. Pastikan file tidak melebihi 5MB.')
  } finally {
    uploading.value[docType] = false
    event.target.value = ''
  }
}

onMounted(async () => {
  try {
    const { data } = await api.get('/member/bookings')
    bookings.value = data.data.data || data.data
    if (bookings.value.length > 0) {
      selectedBookingId.value = bookings.value[0].id
      await loadBooking()
    }
  } finally {
    loading.value = false
  }
})
</script>

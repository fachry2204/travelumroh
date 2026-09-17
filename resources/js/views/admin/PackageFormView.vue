<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">{{ isEdit ? 'Edit Paket' : 'Tambah Paket Umroh' }}</h2>
        <p class="text-slate-500 text-sm">{{ isEdit ? 'Perbarui informasi paket' : 'Buat paket umroh baru' }}</p>
      </div>
      <router-link to="/admin/paket" class="btn-secondary">← Kembali</router-link>
    </div>

    <form @submit.prevent="handleSubmit" class="grid lg:grid-cols-3 gap-6">
      <!-- Main form -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Basic Info -->
        <div class="card p-6">
          <h3 class="font-bold text-slate-900 mb-4">Informasi Dasar</h3>
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Kode Paket *</label>
              <input v-model="form.code" type="text" class="form-input" placeholder="UMR-2025-01" required :disabled="isEdit" />
            </div>
            <div>
              <label class="form-label">Nama Paket *</label>
              <input v-model="form.name" type="text" class="form-input" placeholder="Paket Umroh Reguler 9 Hari" required />
            </div>
            <div>
              <label class="form-label">Tanggal Berangkat *</label>
              <input v-model="form.departure_date" type="date" class="form-input" required />
            </div>
            <div>
              <label class="form-label">Tanggal Pulang *</label>
              <input v-model="form.return_date" type="date" class="form-input" required />
            </div>
            <div>
              <label class="form-label">Durasi (Hari) *</label>
              <input v-model="form.duration_days" type="number" class="form-input" min="1" required />
            </div>
            <div>
              <label class="form-label">Maskapai</label>
              <input v-model="form.airline" type="text" class="form-input" placeholder="Garuda Indonesia" />
            </div>
            <div class="md:col-span-2">
              <label class="form-label">Bandara Keberangkatan</label>
              <input v-model="form.departure_airport" type="text" class="form-input" placeholder="Soekarno-Hatta (CGK)" />
            </div>
            <div>
              <label class="form-label">Hotel Makkah</label>
              <input v-model="form.makkah_hotel" type="text" class="form-input" placeholder="Pullman ZamZam Makkah" />
            </div>
            <div>
              <label class="form-label">Hotel Madinah</label>
              <input v-model="form.madinah_hotel" type="text" class="form-input" placeholder="Movenpick Madinah" />
            </div>
          </div>
        </div>

        <!-- Pricing -->
        <div class="card p-6">
          <h3 class="font-bold text-slate-900 mb-4">Harga & Kuota</h3>
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Harga Quad (4 orang/kamar)</label>
              <input v-model="form.price_quad" type="number" class="form-input" placeholder="28000000" min="0" />
            </div>
            <div>
              <label class="form-label">Harga Triple (3 orang/kamar)</label>
              <input v-model="form.price_triple" type="number" class="form-input" placeholder="32000000" min="0" />
            </div>
            <div>
              <label class="form-label">Harga Double (2 orang/kamar)</label>
              <input v-model="form.price_double" type="number" class="form-input" placeholder="38000000" min="0" />
            </div>
            <div>
              <label class="form-label">DP Minimum</label>
              <input v-model="form.minimum_dp" type="number" class="form-input" placeholder="5000000" min="0" />
            </div>
            <div>
              <label class="form-label">Kuota Seat *</label>
              <input v-model="form.quota" type="number" class="form-input" min="1" required />
            </div>
            <div>
              <label class="form-label">Status</label>
              <select v-model="form.status" class="form-select">
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="card p-6">
          <h3 class="font-bold text-slate-900 mb-4">Deskripsi</h3>
          <div class="space-y-4">
            <div>
              <label class="form-label">Fasilitas (satu per baris)</label>
              <textarea v-model="form.facilities" rows="4" class="form-input" placeholder="Tiket pesawat PP&#10;Visa Umroh&#10;Hotel bintang 5"></textarea>
            </div>
            <div>
              <label class="form-label">Tidak Termasuk (satu per baris)</label>
              <textarea v-model="form.excluded" rows="3" class="form-input" placeholder="Biaya paspor&#10;Pengeluaran pribadi"></textarea>
            </div>
            <div>
              <label class="form-label">Deskripsi Paket</label>
              <textarea v-model="form.description" rows="4" class="form-input" placeholder="Deskripsi lengkap paket..."></textarea>
            </div>
          </div>
        </div>

        <!-- Media & Image -->
        <div class="card p-6">
          <h3 class="font-bold text-slate-900 mb-4">Flyer / Thumbnail Paket</h3>
          <div>
            <label class="form-label">Upload Gambar (JPG/PNG)</label>
            
            <!-- Custom File Input UI -->
            <div class="mt-2 flex justify-center rounded-xl border border-dashed border-sky-300 px-6 py-8 hover:bg-sky-50 transition-colors relative" :class="{'bg-sky-50 border-sky-500': imageFile}">
              <div class="text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-sky-100 text-sky-600 text-2xl mb-4">
                  🖼️
                </span>
                <div class="mt-4 flex text-sm leading-6 text-slate-600 justify-center">
                  <label class="relative cursor-pointer rounded-md bg-white font-semibold text-sky-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-sky-600 focus-within:ring-offset-2 hover:text-sky-500 px-3 py-1 border border-sky-200 shadow-sm transition-all">
                    <span>Pilih file gambar</span>
                    <input type="file" class="sr-only" accept="image/png, image/jpeg, image/jpg" @change="handleImageChange" />
                  </label>
                  <p class="pl-2 pt-1">atau drag & drop</p>
                </div>
                <p class="text-xs leading-5 text-slate-500 mt-2">
                  {{ imageFile ? `File terpilih: ${imageFile.name}` : 'PNG, JPG maksimal 2MB' }}
                </p>
              </div>
            </div>

            <div v-if="form.featured_image && !imageFile" class="mt-4">
              <p class="text-xs text-slate-500 mb-2">Gambar saat ini:</p>
              <img :src="storageUrl(form.featured_image)" class="w-full max-w-sm rounded-lg border border-sky-100 shadow-sm" />
            </div>
          </div>
        </div>

        <!-- Itinerary -->
        <div class="card p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900">Itinerary Harian</h3>
            <button type="button" @click="addItinerary" class="btn-outline text-sm px-3 py-1.5">+ Tambah Hari</button>
          </div>
          <div class="space-y-3">
            <div v-for="(item, i) in form.itineraries" :key="i" class="border border-sky-100 rounded-xl p-4">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full gradient-primary text-white text-sm font-bold flex items-center justify-center">{{ item.day_number }}</div>
                <input v-model="item.title" type="text" class="form-input flex-1" :placeholder="`Judul Hari ke-${item.day_number}`" />
                <button type="button" @click="form.itineraries.splice(i, 1)" class="text-red-500 hover:text-red-700 text-xl">×</button>
              </div>
              <textarea v-model="item.description" rows="2" class="form-input" :placeholder="`Deskripsi kegiatan hari ke-${item.day_number}`"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <div class="card p-6">
          <h3 class="font-bold text-slate-900 mb-4">Ringkasan</h3>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Kode</span><span class="font-medium">{{ form.code || '-' }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Durasi</span><span class="font-medium">{{ form.duration_days || '-' }} hari</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Kuota</span><span class="font-medium">{{ form.quota || '-' }} orang</span></div>
          </div>
          <div class="mt-6 space-y-2">
            <button type="submit" class="btn-primary w-full justify-center" :disabled="submitting">
              <span v-if="submitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span v-else>{{ isEdit ? 'Simpan Perubahan' : 'Buat Paket' }}</span>
            </button>
            <router-link to="/admin/paket" class="btn-secondary w-full justify-center">Batal</router-link>
          </div>
        </div>

        <!-- Alert -->
        <div v-if="successMsg" class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">✅ {{ successMsg }}</div>
        <div v-if="errorMsg" class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">⚠️ {{ errorMsg }}</div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/plugins/axios'

const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)
const submitting = ref(false)
const successMsg = ref('')
const errorMsg = ref('')
const imageFile = ref(null)

const storageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `/storage/${path.replace(/^\//, '')}`
}

const form = ref({
  code: '', name: '', departure_date: '', return_date: '', duration_days: '',
  airline: '', departure_airport: '', makkah_hotel: '', madinah_hotel: '',
  price_quad: '', price_triple: '', price_double: '', minimum_dp: '',
  quota: '', facilities: '', excluded: '', description: '', status: 'active',
  itineraries: [], featured_image: ''
})

function handleImageChange(e) {
  imageFile.value = e.target.files[0]
}

function addItinerary() {
  form.value.itineraries.push({ day_number: form.value.itineraries.length + 1, title: '', description: '' })
}

async function handleSubmit() {
  submitting.value = true
  errorMsg.value = ''
  successMsg.value = ''
  try {
    let packageId = route.params.id

    if (isEdit.value) {
      await api.put(`/admin/packages/${packageId}`, form.value)
    } else {
      const res = await api.post('/admin/packages', form.value)
      packageId = res.data.data.id
    }

    // Upload Image if exists
    if (imageFile.value && packageId) {
      const fd = new FormData()
      fd.append('image', imageFile.value)
      await api.post(`/admin/packages/${packageId}/image`, fd, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    successMsg.value = isEdit.value ? 'Paket & Gambar berhasil diupdate!' : 'Paket berhasil dibuat!'
    if (!isEdit.value) {
      setTimeout(() => router.push('/admin/paket'), 1500)
    }
  } catch (e) {
    const errors = e.response?.data?.errors
    errorMsg.value = errors ? Object.values(errors).flat().join(', ') : (e.response?.data?.message || 'Terjadi kesalahan')
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  if (isEdit.value) {
    const { data } = await api.get(`/admin/packages/${route.params.id}`)
    const pkg = data.data
    Object.assign(form.value, pkg)
    form.value.departure_date = pkg.departure_date?.split('T')[0]
    form.value.return_date = pkg.return_date?.split('T')[0]
    form.value.itineraries = pkg.itineraries || []
  }
})
</script>

<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">Data Jamaah</h2>
        <p class="text-slate-500 text-sm">Lengkapi data seluruh jamaah dalam booking Anda</p>
      </div>
    </div>

    <div v-if="loading" class="card p-12 text-center">
      <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
    </div>

    <template v-else-if="bookings.length === 0">
      <div class="card p-12 text-center">
        <div class="text-5xl mb-3">📋</div>
        <h3 class="font-bold text-slate-900 mb-2">Belum Ada Booking</h3>
        <p class="text-slate-500 text-sm mb-4">Lakukan booking paket umroh terlebih dahulu untuk mengisi data jamaah.</p>
        <router-link to="/paket-umroh" class="btn-primary">Lihat Paket Umroh</router-link>
      </div>
    </template>

    <template v-else>
      <!-- Select booking -->
      <div v-if="bookings.length > 1" class="card p-4">
        <label class="form-label">Pilih Booking</label>
        <select v-model="selectedBookingId" @change="loadPilgrims" class="form-select">
          <option v-for="b in bookings" :key="b.id" :value="b.id">
            {{ b.booking_number }} - {{ b.package?.name }}
          </option>
        </select>
      </div>

      <!-- Pilgrim tabs -->
      <div v-if="selectedBooking">
        <div class="flex gap-2 mb-4 flex-wrap">
          <button v-for="(pilgrim, i) in selectedBooking.pilgrims" :key="pilgrim.id"
            @click="selectedPilgrimIndex = i"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition-all"
            :class="selectedPilgrimIndex === i ? 'gradient-primary text-white shadow-md' : 'bg-white border border-sky-200 text-slate-600 hover:border-sky-400'">
            Jamaah {{ i + 1 }}: {{ pilgrim.full_name || '(Belum diisi)' }}
          </button>
        </div>

        <!-- Pilgrim Form -->
        <div v-if="currentPilgrim" class="space-y-6">
          <!-- Step Tabs -->
          <div class="flex gap-1 p-1 bg-sky-50 rounded-2xl border border-sky-100">
            <button v-for="(step, i) in steps" :key="i" @click="currentStep = i"
              class="flex-1 py-2.5 text-sm font-semibold rounded-xl transition-all"
              :class="currentStep === i ? 'bg-white text-sky-600 shadow-sm' : 'text-slate-500 hover:text-sky-600'">
              {{ step }}
            </button>
          </div>

          <form @submit.prevent="savePilgrim" class="card p-6">
            <!-- Step 1: Identitas -->
            <div v-show="currentStep === 0">
              <h3 class="font-bold text-slate-900 text-lg mb-5 flex items-center gap-2">👤 Identitas Diri</h3>
              <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                  <label class="form-label">Nama Lengkap (Sesuai KTP) *</label>
                  <input v-model="form.full_name" type="text" class="form-input" required />
                </div>
                <div>
                  <label class="form-label">NIK *</label>
                  <input v-model="form.nik" type="text" inputmode="numeric" maxlength="16" @input="form.nik = form.nik.replace(/\D/g, '').slice(0, 16)" class="form-input" placeholder="16 digit NIK" />
                  <span v-if="form.nik && form.nik.length < 16" class="text-xs text-amber-600 font-medium mt-1 block">⚠️ NIK harus 16 angka (saat ini {{ form.nik.length }} angka)</span>
                </div>
                <div>
                  <label class="form-label">No. Kartu Keluarga</label>
                  <input v-model="form.family_card_number" type="text" inputmode="numeric" maxlength="16" @input="form.family_card_number = form.family_card_number.replace(/\D/g, '').slice(0, 16)" class="form-input" placeholder="16 digit KK" />
                </div>
                <div>
                  <label class="form-label">Tempat Lahir</label>
                  <input v-model="form.birth_place" type="text" class="form-input" placeholder="Jakarta" />
                </div>
                <div>
                  <label class="form-label">Tanggal Lahir</label>
                  <input v-model="form.birth_date" type="date" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Jenis Kelamin</label>
                  <select v-model="form.gender" class="form-select">
                    <option value="">Pilih</option>
                    <option value="male">Laki-laki</option>
                    <option value="female">Perempuan</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Status Pernikahan</label>
                  <select v-model="form.marital_status" class="form-select">
                    <option value="">Pilih</option>
                    <option value="single">Belum Menikah</option>
                    <option value="married">Menikah</option>
                    <option value="divorced">Cerai Hidup</option>
                    <option value="widowed">Cerai Mati</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Pekerjaan</label>
                  <input v-model="form.job" type="text" class="form-input" placeholder="Karyawan Swasta" />
                </div>
                <div>
                  <label class="form-label">Pendidikan Terakhir</label>
                  <select v-model="form.education" class="form-select">
                    <option value="">Pilih</option>
                    <option value="SD">SD</option><option value="SMP">SMP</option>
                    <option value="SMA/SMK">SMA/SMK</option><option value="D3">D3</option>
                    <option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">No. HP / WhatsApp</label>
                  <input v-model="form.phone" type="tel" class="form-input" placeholder="08xxxxxxxxxx" />
                </div>
                <div>
                  <label class="form-label">Email</label>
                  <input v-model="form.email" type="email" class="form-input" placeholder="email@domain.com" />
                </div>
                <div class="md:col-span-2">
                  <label class="form-label">Alamat Lengkap</label>
                  <textarea v-model="form.address" rows="2" class="form-input" placeholder="Jl. Contoh No. 1 RT 01/RW 01"></textarea>
                </div>
                <div>
                  <label class="form-label">Provinsi</label>
                  <input v-model="form.province" type="text" class="form-input" placeholder="Jawa Barat" />
                </div>
                <div>
                  <label class="form-label">Kota/Kabupaten</label>
                  <input v-model="form.city" type="text" class="form-input" placeholder="Bandung" />
                </div>
                <div>
                  <label class="form-label">Kecamatan</label>
                  <input v-model="form.district" type="text" class="form-input" placeholder="Coblong" />
                </div>
                <div>
                  <label class="form-label">Kelurahan</label>
                  <input v-model="form.village" type="text" class="form-input" placeholder="Lebakgede" />
                </div>
                <div>
                  <label class="form-label">Kode Pos</label>
                  <input v-model="form.postal_code" type="text" class="form-input" maxlength="5" placeholder="40132" />
                </div>
              </div>
            </div>

            <!-- Step 2: Paspor -->
            <div v-show="currentStep === 1">
              <h3 class="font-bold text-slate-900 text-lg mb-5 flex items-center gap-2">🛂 Data Paspor</h3>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="form-label">Nomor Paspor</label>
                  <input v-model="form.passport_number" type="text" class="form-input" placeholder="A1234567" />
                </div>
                <div>
                  <label class="form-label">Nama Sesuai Paspor</label>
                  <input v-model="form.passport_name" type="text" class="form-input" placeholder="Nama tanpa gelar" />
                </div>
                <div>
                  <label class="form-label">Tempat Terbit Paspor</label>
                  <input v-model="form.passport_issued_place" type="text" class="form-input" placeholder="JAKARTA" />
                </div>
                <div>
                  <label class="form-label">Tanggal Terbit Paspor</label>
                  <input v-model="form.passport_issued_date" type="date" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Tanggal Expired Paspor</label>
                  <input v-model="form.passport_expired_date" type="date" class="form-input" />
                </div>
              </div>
              <div v-if="form.passport_expired_date" class="mt-4 p-4 rounded-xl" :class="isPassportValid ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200'">
                <p class="text-sm font-semibold" :class="isPassportValid ? 'text-emerald-700' : 'text-red-700'">
                  {{ isPassportValid ? '✅ Paspor masih berlaku' : '⚠️ Paspor akan/sudah expired' }}
                  ({{ daysUntilExpiry }} hari)
                </p>
              </div>
            </div>

            <!-- Step 3: Keluarga & Darurat -->
            <div v-show="currentStep === 2">
              <h3 class="font-bold text-slate-900 text-lg mb-5 flex items-center gap-2">👨‍👩‍👦 Kontak Darurat</h3>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="form-label">Nama Kontak Darurat</label>
                  <input v-model="form.emergency_contact_name" type="text" class="form-input" placeholder="Nama lengkap" />
                </div>
                <div>
                  <label class="form-label">Hubungan</label>
                  <select v-model="form.emergency_contact_relation" class="form-select">
                    <option value="">Pilih</option>
                    <option value="Suami">Suami</option><option value="Istri">Istri</option>
                    <option value="Ayah">Ayah</option><option value="Ibu">Ibu</option>
                    <option value="Anak">Anak</option><option value="Saudara">Saudara</option>
                    <option value="Lainnya">Lainnya</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">No. HP Kontak Darurat</label>
                  <input v-model="form.emergency_contact_phone" type="tel" class="form-input" placeholder="08xxxxxxxxxx" />
                </div>
                <div class="md:col-span-2">
                  <label class="form-label">Alamat Kontak Darurat</label>
                  <textarea v-model="form.emergency_contact_address" rows="2" class="form-input"></textarea>
                </div>
              </div>
            </div>

            <!-- Step 4: Kesehatan -->
            <div v-show="currentStep === 3">
              <h3 class="font-bold text-slate-900 text-lg mb-5 flex items-center gap-2">🏥 Informasi Kesehatan</h3>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="form-label">Golongan Darah</label>
                  <select v-model="form.blood_type" class="form-select">
                    <option value="">Pilih</option>
                    <option value="A">A</option><option value="B">B</option>
                    <option value="AB">AB</option><option value="O">O</option>
                  </select>
                </div>
                <div class="md:col-span-2">
                  <label class="form-label">Riwayat Penyakit</label>
                  <textarea v-model="form.medical_history" rows="3" class="form-input" placeholder="Tidak ada / Diabetes / Hipertensi / dll"></textarea>
                </div>
                <div class="md:col-span-2">
                  <label class="form-label">Alergi</label>
                  <textarea v-model="form.allergy" rows="2" class="form-input" placeholder="Tidak ada / Alergi obat / dll"></textarea>
                </div>
                <div class="md:col-span-2">
                  <label class="form-label">Kebutuhan Khusus</label>
                  <textarea v-model="form.special_needs" rows="2" class="form-input" placeholder="Kursi roda / Makanan khusus / dll"></textarea>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center mt-6 pt-4 border-t border-sky-100">
              <button v-if="currentStep > 0" type="button" @click="currentStep--" class="btn-secondary">← Sebelumnya</button>
              <div v-else></div>
              <div class="flex gap-3">
                <button v-if="currentStep < steps.length - 1" type="button" @click="currentStep++" class="btn-primary">Selanjutnya →</button>
                <button v-else type="submit" class="btn-primary" :disabled="saving">
                  <span v-if="saving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                  <span v-else>💾 Simpan Data</span>
                </button>
              </div>
            </div>

            <!-- Success/Error -->
            <div v-if="successMsg" class="mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">✅ {{ successMsg }}</div>
            <div v-if="errorMsg" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">⚠️ {{ errorMsg }}</div>
          </form>

          <!-- Completion status -->
          <div class="card p-5">
            <h4 class="font-bold text-slate-900 mb-3 text-sm">Kelengkapan Data Jamaah {{ selectedPilgrimIndex + 1 }}</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              <div v-for="check in completionChecks" :key="check.label"
                class="flex items-center gap-2 text-xs p-2 rounded-lg"
                :class="check.done ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">
                {{ check.done ? '✅' : '⏳' }} {{ check.label }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/plugins/axios'

const bookings = ref([])
const loading = ref(true)
const saving = ref(false)
const selectedBookingId = ref(null)
const selectedPilgrimIndex = ref(0)
const currentStep = ref(0)
const successMsg = ref('')
const errorMsg = ref('')

const steps = ['Identitas', 'Paspor', 'Kontak Darurat', 'Kesehatan']

const defaultForm = () => ({
  full_name: '', nik: '', family_card_number: '', birth_place: '', birth_date: '',
  gender: '', marital_status: '', job: '', education: '', address: '',
  province: '', city: '', district: '', village: '', postal_code: '',
  phone: '', email: '', passport_number: '', passport_name: '',
  passport_issued_place: '', passport_issued_date: '', passport_expired_date: '',
  blood_type: '', medical_history: '', allergy: '', special_needs: '',
  emergency_contact_name: '', emergency_contact_relation: '',
  emergency_contact_phone: '', emergency_contact_address: '',
})

const form = ref(defaultForm())

const selectedBooking = computed(() => bookings.value.find(b => b.id === selectedBookingId.value))
const currentPilgrim = computed(() => selectedBooking.value?.pilgrims?.[selectedPilgrimIndex.value])

const isPassportValid = computed(() => {
  if (!form.value.passport_expired_date) return false
  return new Date(form.value.passport_expired_date) > new Date(Date.now() + 6 * 30 * 24 * 60 * 60 * 1000)
})

const daysUntilExpiry = computed(() => {
  if (!form.value.passport_expired_date) return 0
  return Math.floor((new Date(form.value.passport_expired_date) - new Date()) / (1000 * 60 * 60 * 24))
})

const completionChecks = computed(() => {
  const f = form.value
  return [
    { label: 'Identitas', done: !!(f.full_name && f.nik && f.birth_date) },
    { label: 'Paspor', done: !!(f.passport_number && f.passport_expired_date) },
    { label: 'Kontak Darurat', done: !!(f.emergency_contact_name && f.emergency_contact_phone) },
    { label: 'Kesehatan', done: !!(f.blood_type) },
  ]
})

function fillForm(pilgrim) {
  if (!pilgrim) return
  const f = defaultForm()
  Object.keys(f).forEach(k => { if (pilgrim[k] !== undefined) f[k] = pilgrim[k] ?? '' })
  form.value = f
}

watch(currentPilgrim, (pilgrim) => {
  fillForm(pilgrim)
  currentStep.value = 0
  successMsg.value = ''
  errorMsg.value = ''
})

async function loadPilgrims() {
  const booking = bookings.value.find(b => b.id === selectedBookingId.value)
  if (booking && !booking.pilgrims) {
    const { data } = await api.get(`/member/bookings/${booking.id}`)
    const idx = bookings.value.findIndex(b => b.id === selectedBookingId.value)
    bookings.value[idx] = data.data
  }
  selectedPilgrimIndex.value = 0
}

async function savePilgrim() {
  if (form.value.nik && form.value.nik.length !== 16) {
    errorMsg.value = 'NIK harus 16 angka.'
    return
  }
  saving.value = true
  successMsg.value = ''
  errorMsg.value = ''
  try {
    await api.put(`/member/pilgrims/${currentPilgrim.value.id}`, form.value)
    successMsg.value = 'Data jamaah berhasil disimpan!'
    // Refresh booking data
    const { data } = await api.get(`/member/bookings/${selectedBookingId.value}`)
    const idx = bookings.value.findIndex(b => b.id === selectedBookingId.value)
    bookings.value[idx] = data.data
    setTimeout(() => successMsg.value = '', 3000)
  } catch (e) {
    errorMsg.value = e.response?.data?.message || 'Gagal menyimpan data.'
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  try {
    const { data } = await api.get('/member/bookings')
    bookings.value = data.data.data || data.data
    if (bookings.value.length > 0) {
      selectedBookingId.value = bookings.value[0].id
      const { data: detail } = await api.get(`/member/bookings/${selectedBookingId.value}`)
      bookings.value[0] = detail.data
      fillForm(detail.data.pilgrims?.[0])
    }
  } finally {
    loading.value = false
  }
})
</script>

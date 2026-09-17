<template>
  <div class="py-10 bg-sky-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Pendaftaran Umroh</h1>
        <p class="text-slate-600">Isi formulir di bawah ini untuk mendaftar paket umroh pilihan Anda.</p>
      </div>

      <div v-if="loading" class="flex justify-center py-20">
        <div class="w-12 h-12 border-4 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
      </div>

      <div v-else class="card p-8 shadow-xl">
        <div v-if="successMsg" class="text-center py-10">
          <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center text-4xl mx-auto mb-4">✅</div>
          <h2 class="text-2xl font-bold text-slate-900 mb-2">Pendaftaran Berhasil!</h2>
          <p class="text-slate-600 mb-6">{{ successMsg }}</p>
          <router-link to="/member/dashboard" class="btn-primary">Ke Member Area →</router-link>
        </div>

        <form v-else @submit.prevent="submitBooking" class="space-y-8">
          <div v-if="errorMsg" class="p-4 bg-red-50 text-red-700 rounded-xl text-sm mb-6 border border-red-200">
            ⚠️ {{ errorMsg }}
          </div>

          <!-- Package Selection -->
          <div class="space-y-4">
            <h3 class="font-bold text-lg text-slate-900 border-b border-sky-100 pb-2">1. Pilih Paket & Kamar</h3>
            
            <div>
              <label class="form-label">Paket Umroh *</label>
              <select v-model="form.package_id" class="form-select" required>
                <option value="" disabled>-- Pilih Paket --</option>
                <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">
                  {{ pkg.name }} (Berangkat: {{ formatDate(pkg.departure_date) }}) - Sisa: {{ pkg.remaining_seat }} Seat
                </option>
              </select>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="form-label">Jumlah Jamaah *</label>
                <input v-model.number="form.total_pilgrims" type="number" min="1" max="10" class="form-input" required />
              </div>
              <div>
                <label class="form-label">Tipe Kamar *</label>
                <select v-model="form.room_type" class="form-select" required>
                  <option value="quad">Quad (4 Orang) - Harga Standar</option>
                  <option value="triple">Triple (3 Orang) - +Biaya</option>
                  <option value="double">Double (2 Orang) - +Biaya</option>
                </select>
              </div>
            </div>
            
            <div v-if="route.query.ref" class="p-3 bg-sky-50 rounded-lg text-sm text-sky-700 font-medium">
              🏷️ Anda mendaftar menggunakan referral: {{ route.query.ref }}
            </div>
          </div>

          <!-- Contact Person -->
          <div class="space-y-4">
            <h3 class="font-bold text-lg text-slate-900 border-b border-sky-100 pb-2">2. Data Pemesan (Contact Person)</h3>
            
            <div v-if="!auth.isLoggedIn" class="p-4 bg-amber-50 border border-amber-200 rounded-xl mb-4">
              <p class="text-sm text-amber-800 font-medium mb-2">Sudah punya akun member?</p>
              <router-link :to="{ name: 'login', query: { redirect: route.fullPath } }" class="text-sm font-bold text-amber-600 hover:underline">
                Login di sini
              </router-link> 
              <span class="text-amber-800 text-sm">agar tidak perlu mengisi data ulang.</span>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="form-label">Nama Lengkap *</label>
                <input v-model="form.contact_name" type="text" class="form-input" :disabled="auth.isLoggedIn" required />
              </div>
              <div>
                <label class="form-label">Email *</label>
                <input v-model="form.contact_email" type="email" class="form-input" :disabled="auth.isLoggedIn" required />
              </div>
              <div>
                <label class="form-label">No. WhatsApp / HP *</label>
                <input v-model="form.contact_phone" type="tel" class="form-input" :disabled="auth.isLoggedIn" required />
              </div>
              <div v-if="!auth.isLoggedIn">
                <label class="form-label">Buat Password *</label>
                <input v-model="form.password" type="password" class="form-input" placeholder="Untuk login ke member area" required minlength="8" />
              </div>
            </div>
          </div>

          <div class="pt-4 border-t border-sky-100">
            <button type="submit" class="btn-primary w-full justify-center py-4 text-lg" :disabled="submitting">
              <span v-if="submitting" class="w-6 h-6 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span v-else>Daftar & Buat Booking →</span>
            </button>
            <p class="text-xs text-center text-slate-500 mt-4">
              Dengan menekan tombol di atas, Anda menyetujui Syarat dan Ketentuan yang berlaku.
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/plugins/axios'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const loading = ref(true)
const submitting = ref(false)
const packages = ref([])
const errorMsg = ref('')
const successMsg = ref('')

const form = ref({
  package_id: '',
  total_pilgrims: 1,
  room_type: 'quad',
  contact_name: '',
  contact_email: '',
  contact_phone: '',
  full_name: '',
  email: '',
  phone: '',
  password: '',
  referral_code: route.query.ref || '',
})

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

// Pre-fill if logged in
watch(() => auth.user, (user) => {
  if (user) {
    const name = user.name || ''
    const email = user.email || ''
    const phone = user.phone || ''
    form.value.contact_name = name
    form.value.contact_email = email
    form.value.contact_phone = phone
    form.value.full_name = name
    form.value.email = email
    form.value.phone = phone
  }
}, { immediate: true })

onMounted(async () => {
  try {
    const { data } = await api.get('/public/packages')
    packages.value = data.data.data || data.data
    
    // Auto select package if slug provided in URL
    if (route.params.packageSlug) {
      const selected = packages.value.find(p => p.slug === route.params.packageSlug)
      if (selected) {
        form.value.package_id = selected.id
      }
    }
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

async function submitBooking() {
  submitting.value = true
  errorMsg.value = ''
  
  const payload = {
    ...form.value,
    full_name: form.value.contact_name || form.value.full_name,
    email: form.value.contact_email || form.value.email,
    phone: form.value.contact_phone || form.value.phone,
    contact_name: form.value.contact_name || form.value.full_name,
    contact_email: form.value.contact_email || form.value.email,
    contact_phone: form.value.contact_phone || form.value.phone,
  }
  
  try {
    const { data } = await api.post('/public/register-booking', payload)
    
    const bookingNum = data.data?.booking_number || data.data?.booking?.booking_number || ''
    
    // Auto login if token returned
    if (!auth.isLoggedIn && data.data?.token) {
      auth.token = data.data.token
      auth.user = data.data.user
      localStorage.setItem('auth_token', auth.token)
      localStorage.setItem('auth_user', JSON.stringify(auth.user))
    }
    
    successMsg.value = 'Pendaftaran booking umroh Anda berhasil dibuat! Nomor Booking: ' + bookingNum
  } catch (e) {
    const errors = e.response?.data?.errors
    errorMsg.value = errors ? Object.values(errors).flat().join(', ') : (e.response?.data?.message || 'Pendaftaran gagal.')
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } finally {
    submitting.value = false
  }
}
</script>

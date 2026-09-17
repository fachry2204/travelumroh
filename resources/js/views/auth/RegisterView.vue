<template>
  <div class="min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-slate-900 flex items-center justify-center p-4">
    <div class="relative w-full max-w-md">
      <div class="text-center mb-6">
        <router-link to="/" class="inline-flex items-center gap-3">
          <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-xl text-2xl">🕌</div>
          <div class="font-bold text-white text-lg">Travel Umroh Indonesia</div>
        </router-link>
      </div>
      <div class="bg-white rounded-3xl p-8 shadow-2xl">
        <h2 class="text-2xl font-bold text-slate-900 mb-1">Daftar Akun</h2>
        <p class="text-slate-500 text-sm mb-6">Buat akun member untuk kelola perjalanan umroh Anda</p>

        <div v-if="errorMsg" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">⚠️ {{ errorMsg }}</div>

        <form @submit.prevent="handleRegister" class="space-y-4">
          <div>
            <label class="form-label">Nama Lengkap</label>
            <input v-model="form.name" type="text" class="form-input" placeholder="Nama sesuai KTP" required />
          </div>
          <div>
            <label class="form-label">Email</label>
            <input v-model="form.email" type="email" class="form-input" placeholder="email@domain.com" required />
          </div>
          <div>
            <label class="form-label">No. WhatsApp</label>
            <input v-model="form.phone" type="tel" class="form-input" placeholder="08xxxxxxxxxx" />
          </div>
          <div>
            <label class="form-label">Password</label>
            <input v-model="form.password" type="password" class="form-input" placeholder="Minimal 8 karakter" required />
          </div>
          <div>
            <label class="form-label">Konfirmasi Password</label>
            <input v-model="form.password_confirmation" type="password" class="form-input" placeholder="Ulangi password" required />
          </div>
          <button type="submit" class="btn-primary w-full justify-center py-3" :disabled="auth.loading">
            <span v-if="auth.loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span v-else>Daftar Sekarang →</span>
          </button>
        </form>

        <div class="mt-4 text-center text-sm text-slate-500">
          Sudah punya akun? <router-link to="/login" class="text-sky-600 font-semibold hover:text-sky-700">Login di sini</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const form = ref({ name: '', email: '', phone: '', password: '', password_confirmation: '' })
const errorMsg = ref('')

async function handleRegister() {
  errorMsg.value = ''
  try {
    await auth.register(form.value)
    router.push('/member/dashboard')
  } catch (e) {
    const errors = e.response?.data?.errors
    errorMsg.value = errors ? Object.values(errors).flat().join(', ') : (e.response?.data?.message || 'Registrasi gagal.')
  }
}
</script>

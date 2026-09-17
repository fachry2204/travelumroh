<template>
  <!-- Wrapper (AuthLayout tidak dipakai langsung, login standalone) -->
  <div class="min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-slate-900 flex items-center justify-center p-4">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-sky-400/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative w-full max-w-md">
      <div class="text-center mb-8">
        <router-link to="/" class="inline-flex items-center gap-3">
          <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-xl text-3xl">🕌</div>
          <div class="text-left">
            <div class="font-bold text-white text-xl">Travel Umroh</div>
            <div class="text-sky-300 text-sm">Indonesia</div>
          </div>
        </router-link>
      </div>

      <div class="bg-white rounded-3xl p-8 shadow-2xl">
        <h2 class="text-2xl font-bold text-slate-900 mb-1">Selamat Datang</h2>
        <p class="text-slate-500 text-sm mb-6">Login ke akun Anda untuk melanjutkan</p>

        <!-- Alert -->
        <div v-if="errorMsg" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2">
          <span>⚠️</span> {{ errorMsg }}
        </div>

        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label class="form-label">Email</label>
            <input v-model="form.email" type="email" class="form-input" placeholder="admin@travelumroh.com" required />
          </div>
          <div>
            <label class="form-label">Password</label>
            <div class="relative">
              <input v-model="form.password" :type="showPass ? 'text' : 'password'" class="form-input pr-10" placeholder="••••••••" required />
              <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                {{ showPass ? '🙈' : '👁️' }}
              </button>
            </div>
          </div>

          <button type="submit" class="btn-primary w-full justify-center py-3" :disabled="auth.loading">
            <span v-if="auth.loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span v-else>Login →</span>
          </button>
        </form>

        <div class="mt-4 text-center text-sm text-slate-500">
          Belum punya akun?
          <router-link to="/register" class="text-sky-600 font-semibold hover:text-sky-700">Daftar di sini</router-link>
        </div>

        <!-- Demo accounts -->
        <div class="mt-6 p-4 bg-sky-50 rounded-xl border border-sky-100">
          <p class="text-xs font-semibold text-slate-500 mb-2">Demo Akun:</p>
          <div class="grid grid-cols-2 gap-2 text-xs text-slate-600">
            <button @click="fillDemo('admin@travelumroh.com')" class="text-left hover:text-sky-600 transition-colors">
              👑 Admin
            </button>
            <button @click="fillDemo('member@travelumroh.com')" class="text-left hover:text-sky-600 transition-colors">
              👤 Member
            </button>
            <button @click="fillDemo('agen@travelumroh.com')" class="text-left hover:text-sky-600 transition-colors">
              🤝 Agen
            </button>
            <button @click="fillDemo('perwakilan@travelumroh.com')" class="text-left hover:text-sky-600 transition-colors">
              🏢 Perwakilan
            </button>
          </div>
          <p class="text-xs text-slate-400 mt-2">Password semua: <code class="bg-white px-1 rounded">password</code></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const form = ref({ email: '', password: '' })
const errorMsg = ref('')
const showPass = ref(false)

function fillDemo(email) {
  form.value.email = email
  form.value.password = 'password'
}

async function handleLogin() {
  errorMsg.value = ''
  try {
    await auth.login(form.value)
    const redirect = route.query.redirect || auth.dashboardRoute
    router.push(redirect)
  } catch (e) {
    errorMsg.value = e.response?.data?.message || 'Login gagal. Cek email dan password Anda.'
  }
}
</script>

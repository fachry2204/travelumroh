<template>
  <div class="space-y-6 max-w-4xl">
    <div class="page-header">
      <div>
        <h2 class="page-title">Pengaturan Sistem</h2>
        <p class="text-slate-500 text-sm">Konfigurasi umum, logo & branding, API eksternal, dan email SMTP</p>
      </div>
      <div>
        <button @click="saveSettings" class="btn-primary" :disabled="saving">
          <span v-if="saving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin inline-block align-middle mr-2"></span>
          💾 Simpan Pengaturan
        </button>
      </div>
    </div>

    <div v-if="loading" class="card p-12 text-center">
      <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
    </div>

    <template v-else>
      <div v-if="successMsg" class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center gap-2">
        <span class="text-lg">✅</span> {{ successMsg }}
      </div>

      <!-- Tabs Navigation -->
      <div class="flex space-x-1 bg-white p-1.5 rounded-2xl shadow-sm border border-sky-100 mb-6 w-full overflow-x-auto">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-5 py-2.5 text-sm font-semibold rounded-xl transition-all whitespace-nowrap flex items-center gap-2',
            activeTab === tab.id ? 'bg-sky-500 text-white shadow-md shadow-sky-200' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60'
          ]">
          <span>{{ tab.icon }}</span>
          <span>{{ tab.label }}</span>
        </button>
      </div>

      <!-- General Settings -->
      <div v-if="activeTab === 'general'" class="card p-6 space-y-6">
        <div>
          <h3 class="font-bold text-slate-900 text-lg mb-1">Informasi Aplikasi & Branding</h3>
          <p class="text-xs text-slate-500 mb-4">Pengaturan nama brand travel, kontak, serta logo & icon aplikasi.</p>
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Nama Aplikasi (Brand)</label>
              <input v-model="form.app_name" type="text" class="form-input" placeholder="PT Travel Umroh Indonesia" />
            </div>
            <div>
              <label class="form-label">Email Kontak</label>
              <input v-model="form.app_email" type="email" class="form-input" placeholder="cs@travelumroh.com" />
            </div>
            <div>
              <label class="form-label">Nomor WhatsApp CS</label>
              <input v-model="form.app_phone" type="text" class="form-input" placeholder="08123456789" />
            </div>
            <div>
              <label class="form-label">Alamat Lengkap</label>
              <textarea v-model="form.app_address" class="form-input" rows="2" placeholder="Jl. Sudirman No. 1, Jakarta"></textarea>
            </div>
          </div>
        </div>

        <div class="border-t border-slate-100 pt-5">
          <h4 class="font-bold text-slate-900 text-base mb-3">Logo & Icon Aplikasi</h4>
          <div class="grid md:grid-cols-2 gap-6">
            <FileUpload 
              v-model="files.logo" 
              label="Logo (Dashboard, Header & PDF)" 
              hint="Format PNG, JPG, WebP atau SVG" 
              accept="image/*" 
              :currentFileUrl="form.app_logo" 
            />

            <FileUpload 
              v-model="files.favicon" 
              label="Favicon (PWA & Tab Browser)" 
              hint="Format PNG, ICO atau SVG" 
              accept="image/png,image/x-icon,image/svg+xml" 
              :currentFileUrl="form.app_favicon" 
            />
          </div>
        </div>
      </div>

      <!-- MPWA Settings -->
      <div v-if="activeTab === 'mpwa'" class="card p-6">
        <h3 class="font-bold text-slate-900 text-lg mb-1">WhatsApp Gateway (MPWA)</h3>
        <p class="text-xs text-slate-500 mb-4">Integrasikan API MPWA untuk pengiriman notifikasi WhatsApp otomatis ke jamaah.</p>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="form-label">MPWA URL Endpoint</label>
            <input v-model="form.mpwa_url" type="text" class="form-input" placeholder="http://ip-mpwa:3000" />
          </div>
          <div>
            <label class="form-label">MPWA API Key</label>
            <input v-model="form.mpwa_api_key" type="password" class="form-input" placeholder="secret-key" />
          </div>
        </div>
      </div>

      <!-- Duitku Settings -->
      <div v-if="activeTab === 'duitku'" class="card p-6">
        <h3 class="font-bold text-slate-900 text-lg mb-1">Payment Gateway (Duitku)</h3>
        <p class="text-xs text-slate-500 mb-4">Pengaturan API Duitku untuk otomatisasi pembayaran via Virtual Account, QRIS, e-Wallet.</p>
        <div class="grid md:grid-cols-3 gap-4">
          <div>
            <label class="form-label">Lingkungan (Environment)</label>
            <select v-model="form.duitku_env" class="form-select">
              <option value="sandbox">Sandbox (Testing)</option>
              <option value="production">Production (Live)</option>
            </select>
          </div>
          <div>
            <label class="form-label">Merchant Code</label>
            <input v-model="form.duitku_merchant_code" type="text" class="form-input" placeholder="DXXXXX" />
          </div>
          <div>
            <label class="form-label">Merchant Key</label>
            <input v-model="form.duitku_merchant_key" type="password" class="form-input" placeholder="secret-key" />
          </div>
        </div>
      </div>

      <!-- Email Settings -->
      <div v-if="activeTab === 'smtp'" class="card p-6">
        <h3 class="font-bold text-slate-900 text-lg mb-1">SMTP Email</h3>
        <p class="text-xs text-slate-500 mb-4">Pengaturan server SMTP jika Anda akan mengirim email ke jamaah.</p>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="form-label">Mail Host</label>
            <input v-model="form.mail_host" type="text" class="form-input" placeholder="smtp.mailtrap.io" />
          </div>
          <div>
            <label class="form-label">Mail Port</label>
            <input v-model="form.mail_port" type="text" class="form-input" placeholder="2525" />
          </div>
          <div>
            <label class="form-label">Mail Username</label>
            <input v-model="form.mail_username" type="text" class="form-input" />
          </div>
          <div>
            <label class="form-label">Mail Password</label>
            <input v-model="form.mail_password" type="password" class="form-input" />
          </div>
          <div>
            <label class="form-label">Mail Encryption</label>
            <select v-model="form.mail_encryption" class="form-select">
              <option value="tls">TLS</option>
              <option value="ssl">SSL</option>
              <option value="">None</option>
            </select>
          </div>
          <div>
            <label class="form-label">Mail From Address</label>
            <input v-model="form.mail_from_address" type="email" class="form-input" placeholder="no-reply@domain.com" />
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'
import FileUpload from '@/components/FileUpload.vue'
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()
const loading = ref(true)
const saving = ref(false)
const successMsg = ref('')
const activeTab = ref('general')

const tabs = [
  { id: 'general', label: 'Informasi Umum', icon: '🏠' },
  { id: 'mpwa', label: 'WhatsApp Gateway', icon: '💬' },
  { id: 'duitku', label: 'Payment Gateway', icon: '💳' },
  { id: 'smtp', label: 'Email SMTP', icon: '📧' }
]

const form = ref({
  app_name: '', app_email: '', app_phone: '', app_address: '', app_logo: '', app_favicon: '',
  mpwa_url: '', mpwa_api_key: '',
  duitku_env: 'sandbox', duitku_merchant_code: '', duitku_merchant_key: '',
  mail_host: '', mail_port: '', mail_username: '', mail_password: '', mail_encryption: '', mail_from_address: ''
})

const files = ref({ logo: null, favicon: null })

onMounted(async () => {
  try {
    const { data } = await api.get('/admin/settings')
    if (data.success && data.data) {
      Object.assign(form.value, data.data)
    }
  } catch (e) {
    console.error('Failed to load settings', e)
  } finally {
    loading.value = false
  }
})

async function saveSettings() {
  saving.value = true
  successMsg.value = ''
  try {
    const fd = new FormData()
    Object.keys(form.value).forEach(key => {
      fd.append(key, form.value[key] || '')
    })
    
    if (files.value.logo) fd.append('logo', files.value.logo)
    if (files.value.favicon) fd.append('favicon', files.value.favicon)

    const { data } = await api.post('/admin/settings', fd, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    if (data.success) {
      successMsg.value = 'Pengaturan berhasil disimpan!'
      // Reset selected files input
      files.value.logo = null
      files.value.favicon = null

      // Refetch settings globally in appStore to instantly update sidebar, header, and logo across entire app
      await appStore.fetchSettings(true)

      // Reload local settings form values
      const refetched = await api.get('/admin/settings')
      if (refetched.data?.success) {
        Object.assign(form.value, refetched.data.data)
      }

      setTimeout(() => successMsg.value = '', 3500)
    }
  } catch (e) {
    console.error(e)
    alert(e.response?.data?.message || 'Gagal menyimpan pengaturan.')
  } finally {
    saving.value = false
  }
}
</script>

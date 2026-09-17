<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">Link Referral Agen</h2>
        <p class="text-slate-500 text-sm">Bagikan link unik Anda untuk mendapatkan komisi dari setiap pendaftaran</p>
      </div>
    </div>

    <div v-if="loading" class="card p-12 text-center">
      <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
    </div>

    <template v-else-if="agent">
      <!-- Agent Info -->
      <div class="gradient-primary rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <div>
            <div class="text-sky-200 text-sm mb-1">Kode Agen Anda</div>
            <div class="text-3xl font-extrabold">{{ agent.agent_code }}</div>
          </div>
          <div class="text-right">
            <div class="text-sky-200 text-sm mb-1">Tipe Komisi</div>
            <div class="text-xl font-bold">
              {{ agent.commission_type === 'percentage' ? agent.commission_value + '%' : formatCurrency(agent.commission_value) }}
              <span class="text-sm text-sky-200">/ booking</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Referral Link -->
      <div class="card p-6">
        <h3 class="font-bold text-slate-900 mb-4">🔗 Link Referral Anda</h3>
        <div class="flex gap-2">
          <div class="flex-1 flex items-center gap-3 bg-sky-50 border border-sky-200 rounded-xl px-4 py-3">
            <span class="text-sky-400">🔗</span>
            <span class="text-slate-700 text-sm font-mono flex-1 break-all select-all">{{ referralUrl }}</span>
          </div>
          <button @click="copyLink" class="btn-primary px-4 flex-shrink-0">
            {{ copied ? '✅ Tersalin!' : '📋 Salin' }}
          </button>
        </div>
        <p class="text-xs text-slate-400 mt-2">Link ini akan otomatis mencatat referral Anda saat calon jamaah mendaftar.</p>
      </div>

      <!-- Share options -->
      <div class="card p-6">
        <h3 class="font-bold text-slate-900 mb-4">📱 Bagikan via</h3>
        <div class="grid grid-cols-3 md:grid-cols-5 gap-3">
          <a :href="`https://wa.me/?text=${encodeURIComponent(waMessage)}`" target="_blank"
            class="flex flex-col items-center gap-2 p-4 rounded-xl bg-green-50 hover:bg-green-100 text-green-600 transition-colors">
            <div class="text-3xl">💬</div>
            <span class="text-xs font-semibold">WhatsApp</span>
          </a>
          <a :href="`https://t.me/share/url?url=${encodeURIComponent(referralUrl)}`" target="_blank"
            class="flex flex-col items-center gap-2 p-4 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors">
            <div class="text-3xl">✈️</div>
            <span class="text-xs font-semibold">Telegram</span>
          </a>
          <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(referralUrl)}`" target="_blank"
            class="flex flex-col items-center gap-2 p-4 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-600 transition-colors">
            <div class="text-3xl">📘</div>
            <span class="text-xs font-semibold">Facebook</span>
          </a>
          <a :href="`https://twitter.com/intent/tweet?text=${encodeURIComponent(waMessage)}`" target="_blank"
            class="flex flex-col items-center gap-2 p-4 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 transition-colors">
            <div class="text-3xl">🐦</div>
            <span class="text-xs font-semibold">Twitter/X</span>
          </a>
          <button @click="copyLink"
            class="flex flex-col items-center gap-2 p-4 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-600 transition-colors">
            <div class="text-3xl">📋</div>
            <span class="text-xs font-semibold">Salin Link</span>
          </button>
        </div>
      </div>

      <!-- Template pesan -->
      <div class="card p-6">
        <h3 class="font-bold text-slate-900 mb-4">💬 Template Pesan Promosi</h3>
        <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-700 whitespace-pre-line font-mono border border-slate-200">{{ promoTemplate }}</div>
        <button @click="copyTemplate" class="mt-3 btn-secondary text-sm px-4 py-2">
          {{ copiedTemplate ? '✅ Template Tersalin!' : '📋 Salin Template' }}
        </button>
      </div>

      <!-- Stats ringkas -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="kpi-card">
          <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-xl">📋</div>
          <div><div class="text-2xl font-extrabold text-slate-900">{{ stats.total_bookings ?? 0 }}</div><div class="text-xs text-slate-500">Total Booking</div></div>
        </div>
        <div class="kpi-card">
          <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">⏳</div>
          <div><div class="text-2xl font-extrabold text-slate-900">{{ formatCurrency(stats.pending_commissions ?? 0) }}</div><div class="text-xs text-slate-500">Komisi Pending</div></div>
        </div>
        <div class="kpi-card">
          <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">✅</div>
          <div><div class="text-2xl font-extrabold text-slate-900">{{ formatCurrency(stats.approved_commissions ?? 0) }}</div><div class="text-xs text-slate-500">Komisi Approved</div></div>
        </div>
        <div class="kpi-card">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">💰</div>
          <div><div class="text-2xl font-extrabold text-slate-900">{{ formatCurrency(stats.paid_commissions ?? 0) }}</div><div class="text-xs text-slate-500">Komisi Cair</div></div>
        </div>
      </div>
    </template>

    <template v-else>
      <div class="card p-12 text-center">
        <div class="text-5xl mb-3">🤝</div>
        <h3 class="font-bold text-slate-900 mb-2">Akun Agen Belum Aktif</h3>
        <p class="text-slate-500 text-sm">Hubungi admin untuk mengaktifkan akun agen Anda.</p>
        <a href="https://wa.me/6281234567890" target="_blank" class="btn-primary mt-4">💬 Hubungi Admin</a>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/plugins/axios'

const agent = ref(null)
const stats = ref({})
const loading = ref(true)
const copied = ref(false)
const copiedTemplate = ref(false)

const referralUrl = computed(() =>
  agent.value ? `${window.location.origin}/daftar?ref=${agent.value.referral_code}` : ''
)

const waMessage = computed(() =>
  `Assalamu'alaikum! Yuk daftar umroh bersama kami 🕌\n\nDapatkan paket umroh terbaik, pelayanan profesional, hotel bintang 5!\n\n✅ Harga terjangkau\n✅ Pembimbing berpengalaman\n✅ Dokumen dibantu\n\nDaftar sekarang: ${referralUrl.value}`
)

const promoTemplate = computed(() => waMessage.value)

function formatCurrency(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0, notation: 'compact' }).format(val || 0)
}

async function copyLink() {
  await navigator.clipboard.writeText(referralUrl.value)
  copied.value = true; setTimeout(() => copied.value = false, 2000)
}

async function copyTemplate() {
  await navigator.clipboard.writeText(promoTemplate.value)
  copiedTemplate.value = true; setTimeout(() => copiedTemplate.value = false, 2000)
}

onMounted(async () => {
  try {
    const { data } = await api.get('/agent/dashboard')
    agent.value = data.data.agent
    stats.value = data.data.stats || {}
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

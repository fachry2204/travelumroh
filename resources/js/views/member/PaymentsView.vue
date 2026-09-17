<template>
  <div class="space-y-6">
    <div class="page-header">
      <div>
        <h2 class="page-title">Pembayaran</h2>
        <p class="text-slate-500 text-sm">Upload bukti transfer dan pantau status pembayaran Anda</p>
      </div>
    </div>

    <div v-if="loading" class="card p-12 text-center">
      <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
    </div>

    <template v-else-if="!booking">
      <div class="card p-12 text-center"><div class="text-5xl mb-3">💳</div><p class="text-slate-500">Tidak ada booking aktif.</p></div>
    </template>

    <template v-else>
      <!-- Payment summary -->
      <div class="grid md:grid-cols-3 gap-4 mb-4">
        <div class="card p-5">
          <div class="text-xs text-slate-500 mb-1">Total Tagihan</div>
          <div class="text-2xl font-extrabold text-slate-900">{{ formatCurrency(booking.total_amount) }}</div>
        </div>
        <div class="card p-5">
          <div class="text-xs text-slate-500 mb-1">Sudah Dibayar</div>
          <div class="text-2xl font-extrabold text-emerald-600">{{ formatCurrency(booking.paid_amount) }}</div>
        </div>
        <div class="card p-5">
          <div class="text-xs text-slate-500 mb-1">Sisa Tagihan</div>
          <div class="text-2xl font-extrabold text-amber-500">{{ formatCurrency(booking.total_amount - booking.paid_amount) }}</div>
        </div>
      </div>
      
      <div class="flex justify-end mb-6">
        <button @click="downloadInvoice" class="btn-secondary text-sm px-4 py-2 flex items-center gap-2">
          📄 Download Invoice PDF
        </button>
      </div>

      <!-- Upload payment proof -->
      <div v-if="booking.booking_status !== 'paid'" class="card p-6 mb-6">
        <h3 class="font-bold text-slate-900 text-lg mb-5">💳 Bayar Online (Otomatis)</h3>
        <p class="text-sm text-slate-500 mb-4">Bayar menggunakan Virtual Account, QRIS, atau e-Wallet melalui Duitku. Verifikasi otomatis.</p>
        
        <form @submit.prevent="payOnline" class="flex gap-4 items-end mb-6 pb-6 border-b border-slate-100">
          <div class="flex-1">
            <label class="form-label">Jenis Pembayaran</label>
            <select v-model="onlineForm.payment_type" class="form-select" required>
              <option value="dp">DP / Uang Muka</option>
              <option value="installment">Cicilan</option>
              <option value="final">Pelunasan</option>
            </select>
          </div>
          <div class="flex-1">
            <label class="form-label">Jumlah Bayar (Rp)</label>
            <input v-model.number="onlineForm.amount" type="number" class="form-input" min="10000" required />
          </div>
          <div>
            <button type="submit" class="btn-primary" :disabled="processingOnline">
              <span v-if="processingOnline">Memproses...</span>
              <span v-else>Bayar Sekarang ➔</span>
            </button>
          </div>
        </form>

        <h3 class="font-bold text-slate-900 text-lg mb-5 mt-6">📤 Atau Upload Bukti Transfer (Manual)</h3>

        <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 mb-5 text-sm text-sky-800">
          <p class="font-semibold mb-2">Rekening Pembayaran:</p>
          <div class="space-y-1">
            <div>🏦 BCA: <strong>1234-5678-90</strong> a/n PT Travel Umroh Indonesia</div>
            <div>🏦 Mandiri: <strong>1234-0000-5678-9</strong> a/n PT Travel Umroh Indonesia</div>
          </div>
        </div>

        <div v-if="successMsg" class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">✅ {{ successMsg }}</div>
        <div v-if="errorMsg" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">⚠️ {{ errorMsg }}</div>

        <form @submit.prevent="submitPayment" class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="form-label">Jenis Pembayaran *</label>
            <select v-model="payForm.payment_type" class="form-select" required>
              <option value="dp">DP / Uang Muka</option>
              <option value="installment">Cicilan</option>
              <option value="final">Pelunasan</option>
            </select>
          </div>
          <div>
            <label class="form-label">Jumlah Transfer (Rp) *</label>
            <input v-model.number="payForm.amount" type="number" class="form-input" placeholder="5000000" min="1" required />
          </div>
          <div>
            <label class="form-label">Bank Pengirim</label>
            <input v-model="payForm.bank_name" type="text" class="form-input" placeholder="BCA / Mandiri / BNI" />
          </div>
          <div>
            <label class="form-label">Bukti Transfer *</label>
            <div class="border-2 border-dashed border-sky-200 hover:border-sky-400 rounded-xl overflow-hidden transition-colors">
              <label class="flex items-center gap-3 p-4 cursor-pointer">
                <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center text-xl flex-shrink-0">📎</div>
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-medium text-slate-900 truncate">{{ payForm.file?.name || 'Pilih file bukti transfer' }}</div>
                  <div class="text-xs text-slate-400">JPG, PNG, PDF (maks. 5MB)</div>
                </div>
                <input type="file" class="hidden" accept=".jpg,.jpeg,.png,.pdf" @change="e => payForm.file = e.target.files[0]" required />
              </label>
            </div>
          </div>
          <div class="md:col-span-2">
            <button type="submit" class="btn-primary" :disabled="uploading">
              <span v-if="uploading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span v-else>📤 Kirim Bukti Transfer</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Payment history -->
      <div class="card p-6">
        <h3 class="font-bold text-slate-900 mb-5">📋 Riwayat Pembayaran</h3>
        <div v-if="!booking.payments?.length" class="text-center py-8 text-slate-400">
          <div class="text-4xl mb-2">💳</div>
          <p>Belum ada pembayaran yang tercatat</p>
        </div>
        <div v-else class="space-y-3">
          <div v-for="pay in booking.payments" :key="pay.id" class="border border-sky-100 rounded-xl p-4">
            <div class="flex items-start justify-between gap-4">
              <div>
                <div class="flex items-center gap-2 mb-1">
                  <span class="font-mono text-sm font-semibold text-slate-900">{{ pay.payment_number }}</span>
                  <StatusBadge :status="pay.status" type="payment" />
                </div>
                <div class="text-sm text-slate-500 capitalize">{{ payTypeLabel(pay.payment_type) }} • {{ pay.method }}</div>
                <div class="text-xs text-slate-400 mt-1">{{ formatDate(pay.created_at) }}</div>
                <div v-if="pay.note && pay.status === 'rejected'" class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded-lg">
                  ❌ Ditolak: {{ pay.note }}
                </div>
              </div>
              <div class="text-right flex-shrink-0">
                <div class="text-lg font-extrabold text-slate-900">{{ formatCurrency(pay.amount) }}</div>
                <a v-if="pay.proof_file" :href="`/storage/${pay.proof_file}`" target="_blank"
                  class="text-xs text-sky-600 hover:underline mt-1 block">Lihat Bukti →</a>
                <button v-if="pay.status === 'approved'" @click="downloadReceipt(pay.id)"
                  class="text-xs text-emerald-600 hover:underline mt-1 block">📄 Unduh Kwitansi</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'
import StatusBadge from '@/components/StatusBadge.vue'

const booking = ref(null)
const bookings = ref([])
const loading = ref(true)
const uploading = ref(false)
const successMsg = ref('')
const errorMsg = ref('')

const payForm = ref({ payment_type: 'dp', amount: null, bank_name: '', file: null })
const onlineForm = ref({ payment_type: 'dp', amount: 5000000 })
const processingOnline = ref(false)

function formatCurrency(val) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0) }
function formatDate(d) { return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }
function payTypeLabel(type) { return { dp: 'DP / Uang Muka', installment: 'Cicilan', final: 'Pelunasan', refund: 'Refund' }[type] || type }

async function loadBooking(id) {
  const { data } = await api.get(`/member/bookings/${id}`)
  booking.value = data.data
}

async function submitPayment() {
  uploading.value = true; successMsg.value = ''; errorMsg.value = ''
  try {
    const fd = new FormData()
    fd.append('payment_type', payForm.value.payment_type)
    fd.append('amount', payForm.value.amount)
    fd.append('bank_name', payForm.value.bank_name)
    fd.append('proof_file', payForm.value.file)
    await api.post(`/member/payments/${booking.value.id}/upload-proof`, fd, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    successMsg.value = 'Bukti pembayaran berhasil dikirim. Menunggu validasi admin.'
    payForm.value = { payment_type: 'dp', amount: null, bank_name: '', file: null }
    await loadBooking(booking.value.id)
  } catch (e) {
    errorMsg.value = e.response?.data?.message || 'Upload gagal. Coba lagi.'
  } finally {
    uploading.value = false
  }
}

async function payOnline() {
  processingOnline.value = true
  try {
    const { data } = await api.post('/duitku/create-invoice', {
      booking_id: booking.value.id,
      amount: onlineForm.value.amount,
      payment_type: onlineForm.value.payment_type
    })
    
    if (data.success && data.paymentUrl) {
      window.location.href = data.paymentUrl
    } else {
      alert('Gagal membuat invoice. Coba lagi.')
    }
  } catch (e) {
    alert(e.response?.data?.message || 'Terjadi kesalahan sistem.')
  } finally {
    processingOnline.value = false
  }
}

async function downloadInvoice() {
  try {
    const response = await api.get(`/export/invoice/${booking.value.id}`, { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `invoice-${booking.value.booking_number}.pdf`)
    document.body.appendChild(link)
    link.click()
  } catch (e) {
    alert('Gagal mendownload invoice.')
  }
}

async function downloadReceipt(paymentId) {
  try {
    const response = await api.get(`/export/receipt/${paymentId}`, { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `kwitansi-${paymentId}.pdf`)
    document.body.appendChild(link)
    link.click()
  } catch (e) {
    alert('Gagal mendownload kwitansi.')
  }
}

onMounted(async () => {
  try {
    const { data } = await api.get('/member/bookings')
    bookings.value = data.data.data || data.data
    if (bookings.value.length) await loadBooking(bookings.value[0].id)
  } finally {
    loading.value = false
  }
})
</script>

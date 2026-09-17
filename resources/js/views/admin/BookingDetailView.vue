<template>
  <div class="space-y-6">
    <div class="page-header flex justify-between items-center">
      <h2 class="page-title">Detail Booking</h2>
      <div class="flex gap-2">
        <button v-if="booking" @click="deleteBooking" class="btn-danger text-sm px-3 py-1.5">Hapus Booking</button>
        <router-link to="/admin/booking" class="btn-secondary text-sm">← Kembali</router-link>
      </div>
    </div>

    <div v-if="loading" class="card p-12 text-center">
      <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
    </div>

    <template v-else-if="booking">
      <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
          <!-- Booking Info -->
          <div class="card p-6">
            <h3 class="font-bold text-slate-900 mb-4 flex justify-between items-center">
              <span>Informasi Booking</span>
              <span class="font-mono text-sky-600 font-bold text-base">{{ booking.booking_number }}</span>
            </h3>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
              <div><span class="text-slate-500">Paket:</span> <span class="font-semibold ml-2 text-slate-900">{{ booking.package?.name }}</span></div>
              <div><span class="text-slate-500">Pemesan:</span> <span class="font-semibold ml-2 text-slate-900">{{ booking.user?.name }} ({{ booking.user?.phone }})</span></div>
              <div><span class="text-slate-500">Total Jamaah:</span> <span class="font-semibold ml-2 text-slate-900">{{ booking.total_pilgrims }} Org</span></div>
              <div><span class="text-slate-500">Tipe Kamar:</span> <span class="font-semibold ml-2 capitalize text-slate-900">{{ booking.room_type }}</span></div>
              <div><span class="text-slate-500">Total Tagihan:</span> <span class="font-bold text-sky-600 ml-2">{{ formatCurrency(booking.total_amount) }}</span></div>
              <div><span class="text-slate-500">Sudah Bayar:</span> <span class="font-bold text-emerald-600 ml-2">{{ formatCurrency(booking.paid_amount) }}</span></div>
              <div><span class="text-slate-500">Sisa Tagihan:</span> <span class="font-bold text-amber-600 ml-2">{{ formatCurrency(booking.outstanding_amount) }}</span></div>
              <div><span class="text-slate-500">Sumber:</span> <span class="font-semibold ml-2 uppercase text-xs px-2 py-0.5 bg-slate-100 rounded">{{ booking.source }}</span></div>
            </div>
            <div v-if="booking.notes" class="mt-4 pt-3 border-t text-xs text-slate-500">
              <strong>Catatan:</strong> {{ booking.notes }}
            </div>
          </div>

          <!-- Pilgrims -->
          <div class="card p-6">
            <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
              <div>
                <h3 class="font-bold text-slate-900 text-base">
                  Data Jamaah ({{ booking.pilgrims?.length || 0 }} / {{ booking.total_pilgrims }} Org)
                </h3>
                <span v-if="isPilgrimLimitReached" class="text-xs text-amber-600 font-bold block mt-0.5">
                  ⚠️ Kuota Jamaah Sudah Penuh ({{ booking.total_pilgrims }} dari {{ booking.total_pilgrims }} Orang)
                </span>
              </div>
              <button 
                @click="openPilgrimModal(null)" 
                :disabled="isPilgrimLimitReached"
                :class="isPilgrimLimitReached ? 'opacity-50 cursor-not-allowed bg-slate-200 text-slate-500 border border-slate-300' : 'btn-primary'"
                class="text-xs px-3 py-1.5 flex items-center gap-1 font-bold rounded-xl transition-all"
                :title="isPilgrimLimitReached ? 'Kuota jamaah sudah penuh' : 'Tambah Jamaah'"
              >
                <span>{{ isPilgrimLimitReached ? '🔒 Kuota Penuh' : '➕ Tambah Jamaah' }}</span>
              </button>
            </div>

            <div v-if="!booking.pilgrims?.length" class="text-center py-8 text-slate-400 text-sm">
              Belum ada data jamaah. Klik tombol di atas untuk menambah jamaah.
            </div>

            <div v-for="p in booking.pilgrims" :key="p.id" class="border border-sky-100 rounded-xl p-4 mb-3 hover:border-sky-200 transition-colors">
              <div class="flex justify-between items-start">
                <div>
                  <div class="font-bold text-slate-900 text-base flex items-center gap-2">
                    {{ p.full_name }}
                    <span v-if="p.gender" class="text-xs font-normal px-2 py-0.5 bg-sky-50 text-sky-700 rounded-full capitalize">{{ p.gender === 'male' ? 'Laki-Laki' : 'Perempuan' }}</span>
                  </div>
                  <div class="text-sm text-slate-500 mt-0.5">{{ p.phone || '-' }} • {{ p.email || '-' }}</div>
                </div>
                <div class="flex gap-1">
                  <button @click="openPilgrimModal(p)" class="text-xs px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg font-medium">Edit</button>
                  <button @click="deletePilgrim(p)" class="text-xs px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium">Hapus</button>
                </div>
              </div>

              <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-2 mt-3 pt-3 border-t border-sky-50 text-xs text-slate-600">
                <div><span class="text-slate-400">NIK:</span> {{ p.nik || '-' }}</div>
                <div><span class="text-slate-400">No. Paspor:</span> {{ p.passport_number || '-' }}</div>
                <div><span class="text-slate-400">Kamar:</span> {{ p.room_type || booking.room_type }} {{ p.room_number ? `(No: ${p.room_number})` : '' }}</div>
                <div><span class="text-slate-400">TTL:</span> {{ p.birth_place || '-' }}, {{ p.birth_date ? formatDate(p.birth_date) : '-' }}</div>
                <div><span class="text-slate-400">Kontak Darurat:</span> {{ p.emergency_contact_name || '-' }}</div>
                <div><span class="text-slate-400">Bus:</span> {{ p.bus_number || '-' }}</div>
              </div>

              <!-- Documents -->
              <div v-if="p.documents?.length" class="mt-3 flex flex-wrap gap-2 pt-2 border-t border-sky-50">
                <div v-for="doc in p.documents" :key="doc.id" class="flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full" :class="doc.status === 'valid' ? 'bg-emerald-100 text-emerald-700' : doc.status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'">
                  {{ doc.document_type.toUpperCase() }}
                  <span>{{ doc.status === 'valid' ? '✅' : doc.status === 'rejected' ? '❌' : '⏳' }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Payments -->
          <div class="card p-6">
            <h3 class="font-bold text-slate-900 mb-4">Riwayat Pembayaran</h3>
            <div v-if="!booking.payments?.length" class="text-slate-400 text-sm">Belum ada pembayaran</div>
            <div v-for="pay in booking.payments" :key="pay.id" class="border border-sky-100 rounded-xl p-4 mb-2 flex justify-between items-center">
              <div>
                <div class="font-semibold text-slate-900">{{ formatCurrency(pay.amount) }}</div>
                <div class="text-xs text-slate-400 capitalize">{{ pay.payment_type }} • {{ pay.method }}</div>
              </div>
              <div class="flex items-center gap-3">
                <StatusBadge :status="pay.status" type="payment" />
                <button v-if="pay.status === 'review'" @click="validatePayment(pay.id, 'approved')" class="text-xs px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg font-medium">✅ Approve</button>
                <button v-if="pay.status === 'review'" @click="validatePayment(pay.id, 'rejected')" class="text-xs px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium">❌ Tolak</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar Status -->
        <div class="space-y-4">
          <div class="card p-6">
            <h3 class="font-bold text-slate-900 mb-4">Update Status Booking</h3>
            <div class="space-y-3">
              <div>
                <label class="form-label">Status Booking</label>
                <select v-model="booking.booking_status" @change="updateStatus" class="form-select">
                  <option value="pending">Pending</option>
                  <option value="dp">Sudah DP</option>
                  <option value="paid">Lunas</option>
                  <option value="cancelled">Batal</option>
                </select>
              </div>
              <div>
                <label class="form-label">Status Dokumen</label>
                <select v-model="booking.document_status" @change="updateStatus" class="form-select">
                  <option value="incomplete">Belum Lengkap</option>
                  <option value="review">Review</option>
                  <option value="complete">Lengkap</option>
                </select>
              </div>
              <div>
                <label class="form-label">Status Visa</label>
                <select v-model="booking.visa_status" @change="updateStatus" class="form-select">
                  <option value="not_submitted">Belum Diajukan</option>
                  <option value="process">Proses</option>
                  <option value="issued">Terbit</option>
                </select>
              </div>
            </div>
            <div v-if="updateMsg" class="mt-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-xs">✅ {{ updateMsg }}</div>
          </div>
        </div>
      </div>
    </template>

    <!-- Pilgrim Modal (Add / Edit) -->
    <div v-if="showPilgrimModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
      <div class="bg-white rounded-3xl max-w-5xl w-full p-6 md:p-8 shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-sky-100 pb-3">
          <div>
            <div class="flex items-center gap-3">
              <h3 class="font-extrabold text-slate-900 text-xl">{{ isEditPilgrim ? 'Edit Data Jamaah' : 'Tambah Jamaah Baru' }}</h3>
              <button v-if="!isEditPilgrim && isDraftFilled" type="button" @click="clearPilgrimDraft" class="text-xs text-amber-700 hover:text-amber-900 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg font-bold transition-all">
                🧹 Reset Draft Form
              </button>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Lengkapi biodata dan dokumen kelengkapan SISKOPATUH Kemenag RI.</p>
          </div>
          <button @click="showPilgrimModal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-lg transition-colors" title="Tutup modal (Draft tetap tersimpan)">&times;</button>
        </div>

        <div v-if="pilgrimFormError" class="p-3.5 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-2xl space-y-1">
          <div class="font-bold text-red-800">⚠️ Gagal Menyimpan Data Jamaah:</div>
          <div>{{ pilgrimFormError }}</div>
        </div>

        <form @submit.prevent="savePilgrim" class="space-y-6">
          <!-- SEKSI A: BIODATA UTAMA JAMAAH -->
          <div class="space-y-3">
            <h4 class="font-extrabold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-2">
              <span>👤</span> A. Biodata Identitas Diri Jamaah
            </h4>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
              <div class="md:col-span-2">
                <label class="form-label text-xs">Nama Lengkap Jamaah (Sesuai KTP/Paspor) *</label>
                <input v-model="pilgrimForm.full_name" type="text" class="form-input" required placeholder="H. Ahmad Syafi'i" />
              </div>
              <div>
                <label class="form-label text-xs">Jenis Kelamin</label>
                <select v-model="pilgrimForm.gender" class="form-select">
                  <option value="">-- Pilih Jenis Kelamin --</option>
                  <option value="male">Laki-Laki</option>
                  <option value="female">Perempuan</option>
                </select>
              </div>
              <div>
                <label class="form-label text-xs">No. NIK (KTP)</label>
                <input v-model="pilgrimForm.nik" type="text" inputmode="numeric" maxlength="16" @input="pilgrimForm.nik = pilgrimForm.nik.replace(/\D/g, '').slice(0, 16)" class="form-input" placeholder="16 digit NIK KTP" />
                <span v-if="pilgrimForm.nik && pilgrimForm.nik.length < 16" class="text-[11px] text-amber-600 font-semibold mt-1 block">⚠️ Harus 16 angka (saat ini {{ pilgrimForm.nik.length }})</span>
              </div>
              <div>
                <label class="form-label text-xs">No. Kartu Keluarga (KK)</label>
                <input v-model="pilgrimForm.family_card_number" type="text" inputmode="numeric" maxlength="16" @input="pilgrimForm.family_card_number = pilgrimForm.family_card_number.replace(/\D/g, '').slice(0, 16)" class="form-input" placeholder="16 digit Nomor KK" />
              </div>
              <div>
                <label class="form-label text-xs">No. Handphone / WhatsApp</label>
                <input v-model="pilgrimForm.phone" type="tel" class="form-input" placeholder="081234567890" />
              </div>
              <div>
                <label class="form-label text-xs">Email Jamaah</label>
                <input v-model="pilgrimForm.email" type="email" class="form-input" placeholder="jamaah@domain.com" />
              </div>
              <div>
                <label class="form-label text-xs">Tempat Lahir</label>
                <input v-model="pilgrimForm.birth_place" type="text" class="form-input" placeholder="Kota Kelahiran" />
              </div>
              <div>
                <label class="form-label text-xs">Tanggal Lahir</label>
                <input v-model="pilgrimForm.birth_date" type="date" class="form-input" />
              </div>
              <div>
                <label class="form-label text-xs">Status Pernikahan</label>
                <select v-model="pilgrimForm.marital_status" class="form-select">
                  <option value="">-- Pilih --</option>
                  <option value="single">Belum Menikah</option>
                  <option value="married">Menikah</option>
                  <option value="divorced">Cerai Hidup</option>
                  <option value="widowed">Cerai Mati</option>
                </select>
              </div>
              <div>
                <label class="form-label text-xs">Pekerjaan</label>
                <input v-model="pilgrimForm.job" type="text" class="form-input" placeholder="Karyawan Swasta / PNS / Wiraswasta" />
              </div>
              <div>
                <label class="form-label text-xs">Pendidikan Terakhir</label>
                <select v-model="pilgrimForm.education" class="form-select">
                  <option value="">-- Pilih --</option>
                  <option value="SD">SD</option>
                  <option value="SMP">SMP</option>
                  <option value="SMA/SMK">SMA/SMK</option>
                  <option value="D3">D3</option>
                  <option value="S1">S1</option>
                  <option value="S2">S2</option>
                  <option value="S3">S3</option>
                </select>
              </div>
              <div class="sm:col-span-2 md:col-span-3">
                <label class="form-label text-xs">Alamat Lengkap Domisili</label>
                <textarea v-model="pilgrimForm.address" class="form-input" rows="2" placeholder="Jl. Raya Umroh No. 123, RT/RW, Desa/Kelurahan, Kecamatan, Kota/Kabupaten..."></textarea>
              </div>
            </div>
          </div>

          <!-- SEKSI B: PASPOR & OPERASIONAL PERJALANAN -->
          <div class="space-y-3 pt-2">
            <h4 class="font-extrabold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-2">
              <span>🛂</span> B. Paspor & Data Perjalanan Umroh
            </h4>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
              <div>
                <label class="form-label text-xs">No. Paspor Asli</label>
                <input v-model="pilgrimForm.passport_number" type="text" class="form-input" placeholder="Contoh: C1234567" />
              </div>
              <div>
                <label class="form-label text-xs">Tipe Kamar</label>
                <select v-model="pilgrimForm.room_type" class="form-select">
                  <option value="quad">Quad (4 Orang / Kamar)</option>
                  <option value="triple">Triple (3 Orang / Kamar)</option>
                  <option value="double">Double (2 Orang / Kamar)</option>
                  <option value="single">Single (1 Orang / Kamar)</option>
                </select>
              </div>
              <div>
                <label class="form-label text-xs">No. Kamar Hotel</label>
                <input v-model="pilgrimForm.room_number" type="text" class="form-input" placeholder="Contoh: 502" />
              </div>
              <div>
                <label class="form-label text-xs">No. Bus Rombongan</label>
                <input v-model="pilgrimForm.bus_number" type="text" class="form-input" placeholder="Contoh: Bus 01" />
              </div>
              <div>
                <label class="form-label text-xs">Kontak Darurat (Nama)</label>
                <input v-model="pilgrimForm.emergency_contact_name" type="text" class="form-input" placeholder="Nama Anggota Keluarga" />
              </div>
              <div>
                <label class="form-label text-xs">Kontak Darurat (No. HP)</label>
                <input v-model="pilgrimForm.emergency_contact_phone" type="tel" class="form-input" placeholder="081234567890" />
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
                    <template v-if="isEditPilgrim">
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
                  <template v-if="isEditPilgrim">
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
            <button type="button" @click="showPilgrimModal = false" class="btn-secondary text-sm px-6 py-2.5">Batal</button>
            <button type="submit" class="btn-primary text-sm px-6 py-2.5 font-bold" :disabled="savingPilgrim">
              {{ savingPilgrim ? 'Menyimpan...' : (isEditPilgrim ? '💾 Update Data Jamaah' : '➕ Simpan Jamaah Baru') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/plugins/axios'
import StatusBadge from '@/components/StatusBadge.vue'

const route = useRoute()
const router = useRouter()
const booking = ref(null)
const loading = ref(true)
const updateMsg = ref('')

const showPilgrimModal = ref(false)
const isEditPilgrim = ref(false)
const pilgrimEditId = ref(null)
const savingPilgrim = ref(false)
const pilgrimFormError = ref('')

const isPilgrimLimitReached = computed(() => {
  if (!booking.value) return false
  const currentCount = booking.value.pilgrims?.length || 0
  const maxQuota = booking.value.total_pilgrims || 0
  return currentCount >= maxQuota
})

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
    pilgrimForm.value.full_name || 
    pilgrimForm.value.nik || 
    pilgrimForm.value.phone || 
    pilgrimForm.value.passport_number || 
    Object.keys(pendingDocs.value).length > 0
  )
})

function clearPilgrimDraft() {
  pendingDocs.value = {}
  pilgrimForm.value = {
    booking_id: booking.value?.id || '',
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
    room_type: booking.value?.room_type || 'quad',
    room_number: '',
    bus_number: '',
    emergency_contact_name: '',
    emergency_contact_phone: ''
  }
}

const currentPilgrimDocs = computed(() => {
  if (!booking.value || !pilgrimEditId.value) return []
  const pilgrim = booking.value.pilgrims?.find(p => p.id === pilgrimEditId.value)
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
  if (!pilgrimEditId.value) return

  uploadingDocType.value = docType
  try {
    const formData = new FormData()
    formData.append('document_type', docType)
    formData.append('file', file)
    await api.post(`/admin/documents/pilgrims/${pilgrimEditId.value}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    await loadBooking()
    alert('Dokumen Siskopatuh berhasil diupload!')
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal upload dokumen Siskopatuh')
  } finally {
    uploadingDocType.value = null
  }
}

const pilgrimForm = ref({
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
  bus_number: '',
  emergency_contact_name: '',
  emergency_contact_phone: ''
})

function formatCurrency(val) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0)
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

async function validatePayment(id, status) {
  await api.post(`/admin/payments/${id}/validate`, { status })
  await loadBooking()
}

async function updateStatus() {
  await api.put(`/admin/bookings/${route.params.id}`, {
    booking_status: booking.value.booking_status,
    document_status: booking.value.document_status,
    visa_status: booking.value.visa_status
  })
  updateMsg.value = 'Status berhasil diupdate'
  setTimeout(() => updateMsg.value = '', 2000)
}

async function deleteBooking() {
  if (!confirm(`Yakin ingin menghapus booking ${booking.value.booking_number}?`)) return
  try {
    await api.delete(`/admin/bookings/${route.params.id}`)
    router.push('/admin/booking')
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menghapus booking')
  }
}

function openPilgrimModal(p) {
  pilgrimFormError.value = ''
  if (!p && isPilgrimLimitReached.value) {
    alert(`Jumlah jamaah sudah mencapai batas kuota maksimal pemesanan (${booking.value.total_pilgrims} orang).`)
    return
  }
  if (p) {
    isEditPilgrim.value = true
    pilgrimEditId.value = p.id
    pilgrimForm.value = {
      booking_id: booking.value.id,
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
      room_type: p.room_type || booking.value.room_type || 'quad',
      room_number: p.room_number || '',
      bus_number: p.bus_number || '',
      emergency_contact_name: p.emergency_contact_name || '',
      emergency_contact_phone: p.emergency_contact_phone || ''
    }
  } else {
    // If opening create mode, keep previous draft unless switching from edit mode
    if (isEditPilgrim.value) {
      isEditPilgrim.value = false
      pilgrimEditId.value = null
      clearPilgrimDraft()
    } else {
      isEditPilgrim.value = false
      pilgrimEditId.value = null
      pilgrimForm.value.booking_id = booking.value.id
    }
  }
  showPilgrimModal.value = true
}

async function savePilgrim() {
  if (pilgrimForm.value.nik && pilgrimForm.value.nik.length !== 16) {
    pilgrimFormError.value = 'NIK harus 16 angka.'
    return
  }
  savingPilgrim.value = true
  pilgrimFormError.value = ''
  try {
    const payload = { ...pilgrimForm.value }
    Object.keys(payload).forEach(k => {
      if (payload[k] === '') payload[k] = null
    })

    if (isEditPilgrim.value) {
      await api.put(`/admin/pilgrims/${pilgrimEditId.value}`, payload)
    } else {
      const res = await api.post('/admin/pilgrims', payload)
      const newPilgrimId = res.data?.data?.id

      // Auto-upload any pending Siskopatuh documents attached during Create Mode!
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
    showPilgrimModal.value = false
    await loadBooking()
  } catch (e) {
    if (e.response?.data?.errors) {
      const errMsgs = Object.values(e.response.data.errors).flat()
      pilgrimFormError.value = errMsgs.join(' • ')
    } else {
      pilgrimFormError.value = e.response?.data?.message || 'Gagal menyimpan data jamaah'
    }
  } finally {
    savingPilgrim.value = false
  }
}

async function deletePilgrim(p) {
  if (!confirm(`Apakah Anda yakin ingin menghapus jamaah ${p.full_name}?`)) return
  try {
    await api.delete(`/admin/pilgrims/${p.id}`)
    await loadBooking()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menghapus jamaah')
  }
}

async function loadBooking() {
  const { data } = await api.get(`/admin/bookings/${route.params.id}`)
  booking.value = data.data
}

onMounted(async () => {
  try {
    await loadBooking()
  } finally {
    loading.value = false
  }
})
</script>

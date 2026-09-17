<template>
  <div class="py-20 bg-sky-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h1 class="section-title">{{ contactInfo.title || 'Hubungi Kami' }}</h1>
        <p class="section-subtitle">{{ contactInfo.subtitle || 'Kami siap membantu dan melayani segala kebutuhan perjalanan ibadah umroh Anda.' }}</p>
      </div>

      <div class="grid md:grid-cols-2 gap-8">
        <!-- Contact Info -->
        <div class="space-y-6">
          <div class="card p-6 flex gap-4 items-start">
            <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center text-white text-2xl flex-shrink-0">📍</div>
            <div>
              <h3 class="font-bold text-slate-900 mb-1">{{ contactInfo.office_name || 'Kantor Pusat' }}</h3>
              <p class="text-slate-600 text-sm whitespace-pre-line">{{ contactInfo.address || 'Jl. Raya Umroh No. 123, Kebayoran Baru, Jakarta Selatan, 12345, Indonesia' }}</p>
            </div>
          </div>

          <div class="card p-6 flex gap-4 items-start">
            <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center text-white text-2xl flex-shrink-0">📞</div>
            <div>
              <h3 class="font-bold text-slate-900 mb-1">Telepon & WhatsApp</h3>
              <p v-if="contactInfo.phone" class="text-slate-600 text-sm mb-1">
                Telepon: <a :href="`tel:${contactInfo.phone.replace(/\D/g, '')}`" class="text-sky-600 hover:underline">{{ contactInfo.phone }}</a>
              </p>
              <p v-if="contactInfo.whatsapp" class="text-slate-600 text-sm">
                WA: <a :href="`https://wa.me/${contactInfo.whatsapp.replace(/\D/g, '')}`" target="_blank" class="text-sky-600 hover:underline">{{ contactInfo.whatsapp }}</a>
              </p>
            </div>
          </div>

          <div class="card p-6 flex gap-4 items-start">
            <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center text-white text-2xl flex-shrink-0">✉️</div>
            <div>
              <h3 class="font-bold text-slate-900 mb-1">Email</h3>
              <p class="text-slate-600 text-sm">
                <a :href="`mailto:${contactInfo.email}`" class="text-sky-600 hover:underline">{{ contactInfo.email || 'info@travelumroh.com' }}</a>
              </p>
            </div>
          </div>

          <div class="card p-6 flex gap-4 items-start">
            <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center text-white text-2xl flex-shrink-0">⏰</div>
            <div>
              <h3 class="font-bold text-slate-900 mb-1">Jam Operasional</h3>
              <p class="text-slate-600 text-sm whitespace-pre-line">{{ contactInfo.operating_hours || 'Senin - Jumat: 08.00 - 17.00 WIB\nSabtu: 08.00 - 13.00 WIB' }}</p>
            </div>
          </div>

          <!-- Google Maps Embed if available -->
          <div v-if="contactInfo.google_maps_embed" class="card overflow-hidden rounded-2xl h-64 border border-sky-100 shadow-sm">
            <iframe :src="contactInfo.google_maps_embed" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="card p-8">
          <h3 class="font-bold text-slate-900 text-xl mb-6">Kirim Pesan</h3>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="form-label">Nama Lengkap</label>
              <input v-model="form.name" type="text" class="form-input" required />
            </div>
            <div>
              <label class="form-label">Email</label>
              <input v-model="form.email" type="email" class="form-input" required />
            </div>
            <div>
              <label class="form-label">No. WhatsApp</label>
              <input v-model="form.phone" type="tel" class="form-input" required />
            </div>
            <div>
              <label class="form-label">Pesan / Pertanyaan</label>
              <textarea v-model="form.message" rows="4" class="form-input" required></textarea>
            </div>
            <button type="submit" class="btn-primary w-full justify-center py-3">Kirim Pesan</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const contactInfo = ref({
  title: 'Hubungi Kami',
  subtitle: 'Kami siap membantu dan melayani segala kebutuhan perjalanan ibadah umroh Anda.',
  office_name: 'Kantor Pusat',
  address: 'Jl. Raya Umroh No. 123, Kebayoran Baru, Jakarta Selatan, 12345, Indonesia',
  phone: '021-12345678',
  whatsapp: '+62 812-3456-7890',
  email: 'info@travelumroh.com',
  operating_hours: 'Senin - Jumat: 08.00 - 17.00 WIB\nSabtu: 08.00 - 13.00 WIB',
  google_maps_embed: ''
})

const form = ref({
  name: '',
  email: '',
  phone: '',
  message: ''
})

function submit() {
  alert('Terima kasih, pesan Anda telah terkirim. Tim kami akan segera menghubungi Anda.')
  form.value = { name: '', email: '', phone: '', message: '' }
}

onMounted(async () => {
  try {
    const { data } = await api.get('/public/pages/contact')
    if (data.data && data.data.content) {
      contactInfo.value = { ...contactInfo.value, ...data.data.content }
      if (data.data.title) contactInfo.value.title = data.data.title
    }
  } catch (e) {
    console.error(e)
  }
})
</script>

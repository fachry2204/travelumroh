<template>
  <div>
    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-sky-900 via-sky-800 to-slate-900">
      <!-- Background pattern -->
      <div class="absolute inset-0">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PHBhdGggZD0iTTM2IDM0djZoNnYtNmgtNnptNiAwaDZ2LTZoLTZ2NnptLTYtNnYtNmgtNnY2aDZ6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-40"></div>
        <div class="absolute top-20 left-10 w-72 h-72 bg-sky-400/20 rounded-full blur-3xl animation-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl animation-float" style="animation-delay: 1.5s;"></div>
      </div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 grid lg:grid-cols-2 gap-12 items-center">
        <!-- Left: Text Content -->
        <div>
          <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-400/20 border border-amber-400/30 rounded-full text-amber-300 text-sm font-medium mb-6">
            <span>⭐</span> {{ heroData.badge }}
          </div>
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
            {{ heroData.title_start }}
            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-300">
              {{ heroData.title_highlight }}
            </span>
          </h1>
          <p class="text-sky-200 text-lg leading-relaxed mb-8 max-w-xl">
            {{ heroData.subtitle }}
          </p>
          <div class="flex flex-wrap gap-4">
            <router-link to="/paket-umroh" class="btn-gold text-base px-8 py-4">
              📦 {{ heroData.btn_primary }}
            </router-link>
            <a :href="`https://wa.me/${appStore.settings.app_phone?.replace(/\D/g, '') || '6281234567890'}`" target="_blank" class="btn-secondary text-base px-8 py-4">
              💬 {{ heroData.btn_secondary }}
            </a>
          </div>

          <!-- Stats -->
          <div class="flex flex-wrap gap-8 mt-10">
            <div class="text-center">
              <div class="text-3xl font-extrabold text-amber-400">{{ heroData.stat1_val }}</div>
              <div class="text-sky-300 text-sm mt-0.5">{{ heroData.stat1_label }}</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-extrabold text-amber-400">{{ heroData.stat2_val }}</div>
              <div class="text-sky-300 text-sm mt-0.5">{{ heroData.stat2_label }}</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-extrabold text-amber-400">{{ heroData.stat3_val }}</div>
              <div class="text-sky-300 text-sm mt-0.5">{{ heroData.stat3_label }}</div>
            </div>
          </div>
        </div>

        <!-- Right: Floating Card -->
        <div class="hidden lg:block">
          <div class="relative">
            <!-- Main visual -->
            <div class="w-full h-96 rounded-3xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center shadow-2xl animation-float overflow-hidden">
              <img v-if="heroData.hero_image" :src="storageUrl(heroData.hero_image)" class="w-full h-full object-cover rounded-3xl" alt="Hero Banner" />
              <span v-else class="text-8xl">🕌</span>
            </div>
            <!-- Floating mini cards -->
            <div class="absolute -bottom-6 -left-8 card p-4 flex items-center gap-3 shadow-xl">
              <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-xl">✅</div>
              <div>
                <div class="font-bold text-slate-900 text-sm">{{ heroData.card1_title }}</div>
                <div class="text-xs text-slate-500">{{ heroData.card1_sub }}</div>
              </div>
            </div>
            <div class="absolute -top-4 -right-6 card p-4 flex items-center gap-3 shadow-xl">
              <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-xl">⭐</div>
              <div>
                <div class="font-bold text-slate-900 text-sm">{{ heroData.card2_title }}</div>
                <div class="text-xs text-slate-500">{{ heroData.card2_sub }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Wave -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 80H1440V30C1200 60 960 70 720 50C480 30 240 10 0 30V80Z" fill="#f0f9ff"/>
        </svg>
      </div>
    </section>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="text-center">
        <div class="w-12 h-12 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-slate-500">Memuat data...</p>
      </div>
    </div>

    <template v-else>
      <!-- Packages Section -->
      <section class="py-20 bg-sky-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-sky-100 rounded-full text-sky-600 text-sm font-medium mb-4">
              📦 Pilihan Terbaik
            </div>
            <h2 class="section-title">Paket Umroh Pilihan</h2>
            <p class="section-subtitle">Temukan paket umroh yang sesuai dengan kebutuhan dan budget Anda</p>
          </div>

          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <PackageCard v-for="pkg in packages" :key="pkg.id" :package="pkg" />
          </div>

          <div class="text-center mt-10">
            <router-link to="/paket-umroh" class="btn-primary px-8 py-3">
              Lihat Semua Paket →
            </router-link>
          </div>
        </div>
      </section>

      <!-- Excellence Section -->
      <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="section-title">{{ excellenceData.title }}</h2>
            <p class="section-subtitle">{{ excellenceData.subtitle }}</p>
          </div>
          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="item in excellenceData.items" :key="item.title"
              class="card p-6 group hover:border-sky-300 transition-all">
              <div class="w-14 h-14 rounded-2xl bg-sky-50 group-hover:bg-sky-100 flex items-center justify-center text-3xl mb-4 transition-colors">
                {{ item.icon }}
              </div>
              <h3 class="font-bold text-slate-900 mb-2">{{ item.title }}</h3>
              <p class="text-slate-500 text-sm leading-relaxed">{{ item.desc }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Testimonials Auto-Slider (3 cards per group, 15 seconds interval) -->
      <section v-if="testimonials.length > 0" 
               class="py-20 bg-gradient-to-br from-sky-900 via-sky-850 to-slate-900 relative overflow-hidden"
               @mouseenter="stopTestimonialAutoSlide"
               @mouseleave="startTestimonialAutoSlide">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white">Testimoni Jamaah</h2>
            <p class="text-sky-200 mt-3">Ribuan jamaah telah mempercayakan perjalanan umroh mereka kepada kami</p>
          </div>

          <div class="relative px-2 sm:px-6">
            <!-- Testimonial Cards Grid (3 cards per view) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 transition-all duration-700 min-h-[220px]">
              <div v-for="t in visibleTestimonials" :key="t.id"
                class="bg-white/10 backdrop-blur border border-white/20 rounded-3xl p-6 flex flex-col justify-between shadow-xl hover:border-white/40 transition-all hover:-translate-y-1">
                <div>
                  <div class="flex gap-1 mb-3">
                    <span v-for="i in t.rating" :key="i" class="text-amber-400 text-sm">⭐</span>
                  </div>
                  <p class="text-sky-100 text-sm leading-relaxed mb-4 line-clamp-5">"{{ t.content }}"</p>
                </div>
                <div class="flex items-center gap-3 pt-3 border-t border-white/10">
                  <div class="w-11 h-11 rounded-full bg-white/20 overflow-hidden flex-shrink-0 border border-white/30 flex items-center justify-center font-bold text-white shadow-sm">
                    <img v-if="t.photo" :src="storageUrl(t.photo)" class="w-full h-full object-cover" />
                    <span v-else>{{ t.name?.charAt(0) || '👤' }}</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <div class="font-bold text-white text-sm truncate">{{ t.name }}</div>
                    <div class="text-sky-300 text-xs truncate">{{ t.package_name || 'Jamaah Umroh' }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Navigation Arrow Buttons (Only if > 3 items) -->
            <template v-if="totalTestimonialPages > 1">
              <button type="button" @click="prevTestimonialSlide" 
                      class="absolute -left-2 sm:-left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-slate-900/80 hover:bg-sky-600 backdrop-blur text-white flex items-center justify-center font-bold text-2xl shadow-2xl transition-all border border-white/20">
                ‹
              </button>
              <button type="button" @click="nextTestimonialSlide" 
                      class="absolute -right-2 sm:-right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-slate-900/80 hover:bg-sky-600 backdrop-blur text-white flex items-center justify-center font-bold text-2xl shadow-2xl transition-all border border-white/20">
                ›
              </button>
            </template>
          </div>

          <!-- Indicator Dots -->
          <div v-if="totalTestimonialPages > 1" class="flex justify-center items-center gap-2 mt-8">
            <button 
              v-for="page in totalTestimonialPages" 
              :key="page"
              type="button"
              @click="goToTestimonialPage(page - 1)"
              class="h-2.5 rounded-full transition-all duration-300"
              :class="currentTestimonialPage === (page - 1) ? 'w-8 bg-sky-300 shadow-md' : 'w-2.5 bg-white/30 hover:bg-white/50'"
              :title="`Grup ${page}`"
            ></button>
          </div>
        </div>
      </section>

      <!-- Gallery Section -->
      <section class="py-20 bg-sky-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="section-title">Galeri Dokumentasi</h2>
            <p class="section-subtitle">Album foto momen indah & perjalanan khusyuk jamaah kami di Tanah Suci</p>
          </div>
          
          <div v-if="galleryAlbums.length === 0" class="text-center py-12 text-slate-400 font-medium">
            Belum ada album foto galeri.
          </div>
          
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div v-for="album in galleryAlbums.slice(0, 6)" :key="album.title"
              @click="selectedHomeAlbum = album"
              class="group bg-white rounded-2xl overflow-hidden border border-sky-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer flex flex-col justify-between">
              <div>
                <div class="aspect-[4/3] bg-sky-100 overflow-hidden relative">
                  <img v-if="album.cover" :src="storageUrl(album.cover)" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                  <div v-else class="w-full h-full flex items-center justify-center text-4xl bg-sky-100">🖼️</div>
                  
                  <div class="absolute top-3 right-3 bg-slate-900/70 backdrop-blur-md text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1.5 shadow-md">
                    <span>📷</span> {{ album.photos.length }} Foto
                  </div>
                </div>
                
                <div class="p-5 space-y-2">
                  <span class="text-[11px] font-extrabold tracking-wide uppercase px-2.5 py-0.5 bg-sky-100 text-sky-800 rounded-full inline-block">
                    {{ album.category }}
                  </span>
                  <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-sky-600 transition-colors line-clamp-2">
                    {{ album.title }}
                  </h3>
                </div>
              </div>
              
              <div class="px-5 pb-5 pt-0">
                <span class="text-xs font-bold text-sky-600 group-hover:underline flex items-center gap-1">
                  Buka Album Ini &rarr;
                </span>
              </div>
            </div>
          </div>
          
          <div class="text-center mt-10">
            <router-link to="/galeri" class="btn-outline">Lihat Semua Album Galeri</router-link>
          </div>
        </div>
      </section>

      <!-- Articles -->
      <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="section-title">Artikel & Panduan</h2>
            <p class="section-subtitle">Informasi berguna untuk mempersiapkan perjalanan umroh Anda</p>
          </div>
          <div class="grid md:grid-cols-3 gap-6">
            <router-link v-for="article in articles" :key="article.id"
              :to="`/artikel/${article.slug}`"
              class="card overflow-hidden group hover:-translate-y-1 transition-all duration-200">
              <div class="h-40 bg-gradient-to-br from-sky-100 to-sky-200 flex items-center justify-center text-5xl overflow-hidden">
                <img v-if="article.featured_image || article.image_path" :src="storageUrl(article.featured_image || article.image_path)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                <span v-else>📖</span>
              </div>
              <div class="p-5">
                <span class="badge-info text-xs mb-2">{{ article.category }}</span>
                <h3 class="font-bold text-slate-900 group-hover:text-sky-600 transition-colors line-clamp-2 mt-2 mb-2">{{ article.title }}</h3>
                <p class="text-slate-500 text-sm line-clamp-2">{{ article.excerpt }}</p>
              </div>
            </router-link>
          </div>
          <div class="text-center mt-8">
            <router-link to="/artikel" class="btn-outline">Lihat Semua Artikel</router-link>
          </div>
        </div>
      </section>

      <!-- CTA Banner -->
      <section class="py-16 gradient-primary">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Siap Berangkat Umroh?</h2>
          <p class="text-sky-100 text-lg mb-8">Konsultasikan rencana ibadah umroh Anda dengan tim kami sekarang</p>
          <div class="flex flex-wrap justify-center gap-4">
            <router-link to="/daftar" class="btn-gold text-lg px-10 py-4">Daftar Sekarang</router-link>
            <a href="https://wa.me/6281234567890" target="_blank" class="btn-secondary text-lg px-10 py-4">
              💬 WhatsApp Kami
            </a>
          </div>
        </div>
      </section>

      <!-- HOME PAGE ALBUM LIGHTBOX MODAL -->
      <div v-if="selectedHomeAlbum" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" @click.self="selectedHomeAlbum = null">
        <div class="bg-white rounded-3xl max-w-5xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
          <div class="flex justify-between items-center border-b border-sky-100 pb-4">
            <div>
              <h2 class="font-extrabold text-slate-900 text-xl">{{ selectedHomeAlbum.title }}</h2>
              <p class="text-xs text-sky-600 font-semibold mt-0.5">
                🏷️ {{ selectedHomeAlbum.category }} • {{ selectedHomeAlbum.photos.length }} Foto Dokumentasi
              </p>
            </div>
            <button @click="selectedHomeAlbum = null" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xl transition-colors">&times;</button>
          </div>

          <!-- Photos Grid inside Album -->
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 py-2">
            <div 
              v-for="photo in selectedHomeAlbum.photos" 
              :key="photo.id"
              @click="homePreviewPhoto = photo"
              class="aspect-square bg-slate-100 rounded-2xl overflow-hidden cursor-pointer relative group border border-slate-200 shadow-sm hover:shadow-md"
            >
              <img :src="storageUrl(photo.image_path)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-2xl">
                🔍
              </div>
            </div>
          </div>

          <div class="flex justify-between items-center pt-3 border-t border-sky-100">
            <router-link :to="`/galeri?album=${encodeURIComponent(selectedHomeAlbum.title)}`" class="text-xs text-sky-600 hover:underline font-bold">
              🌐 Buka di Halaman Galeri Utama &rarr;
            </router-link>
            <button @click="selectedHomeAlbum = null" class="btn-primary text-xs px-6 py-2">Tutup Album</button>
          </div>
        </div>
      </div>

      <!-- FULL PHOTO PREVIEW MODAL -->
      <div v-if="homePreviewPhoto" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md" @click.self="homePreviewPhoto = null">
        <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center">
          <button @click="homePreviewPhoto = null" class="absolute -top-12 right-0 text-white text-3xl font-bold hover:text-sky-400">&times;</button>
          <img :src="storageUrl(homePreviewPhoto.image_path)" class="max-w-full max-h-[80vh] rounded-2xl shadow-2xl object-contain" />
          <p v-if="homePreviewPhoto.title" class="text-white text-sm font-semibold mt-3 bg-black/60 px-4 py-1.5 rounded-full">{{ homePreviewPhoto.title }}</p>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useAppStore } from '@/stores/app'
import { useSEO } from '@/composables/useSEO'
import PackageCard from '@/components/PackageCard.vue'
import api from '@/plugins/axios'

const auth = useAuthStore()
const appStore = useAppStore()
const { setMeta } = useSEO()
const loading = ref(true)
const packages = ref([])
const testimonials = ref([])
const galleries = ref([])
const articles = ref([])
const faqs = ref([])
const seo = ref(null)

const selectedHomeAlbum = ref(null)
const homePreviewPhoto = ref(null)
const openFaq = ref(null)

// Testimonial 15-Seconds Group Auto-Slider Logic
const currentTestimonialPage = ref(0)
let testimonialTimer = null

const visibleTestimonials = computed(() => {
  if (!testimonials.value || testimonials.value.length === 0) return []
  if (testimonials.value.length <= 3) return testimonials.value

  const pageSize = 3
  const totalPages = Math.ceil(testimonials.value.length / pageSize)
  const pageIndex = currentTestimonialPage.value % totalPages
  const start = pageIndex * pageSize
  
  return testimonials.value.slice(start, start + pageSize)
})

const totalTestimonialPages = computed(() => {
  if (!testimonials.value || testimonials.value.length === 0) return 1
  return Math.ceil(testimonials.value.length / 3)
})

function nextTestimonialSlide() {
  if (totalTestimonialPages.value <= 1) return
  currentTestimonialPage.value = (currentTestimonialPage.value + 1) % totalTestimonialPages.value
  resetTestimonialTimer()
}

function prevTestimonialSlide() {
  if (totalTestimonialPages.value <= 1) return
  currentTestimonialPage.value = (currentTestimonialPage.value - 1 + totalTestimonialPages.value) % totalTestimonialPages.value
  resetTestimonialTimer()
}

function goToTestimonialPage(pageIdx) {
  currentTestimonialPage.value = pageIdx
  resetTestimonialTimer()
}

function startTestimonialAutoSlide() {
  stopTestimonialAutoSlide()
  if (totalTestimonialPages.value > 1) {
    testimonialTimer = setInterval(() => {
      currentTestimonialPage.value = (currentTestimonialPage.value + 1) % totalTestimonialPages.value
    }, 15000)
  }
}

function stopTestimonialAutoSlide() {
  if (testimonialTimer) {
    clearInterval(testimonialTimer)
    testimonialTimer = null
  }
}

function resetTestimonialTimer() {
  startTestimonialAutoSlide()
}

onUnmounted(() => {
  stopTestimonialAutoSlide()
})

const galleryAlbums = computed(() => {
  const map = {}
  galleries.value.forEach(g => {
    const titleKey = (g.title || 'Galeri Foto').replace(/\s*\(\d+\)$/, '').trim()
    if (!map[titleKey]) {
      map[titleKey] = {
        title: titleKey,
        category: g.category || 'Dokumentasi',
        photos: []
      }
    }
    map[titleKey].photos.push(g)
  })

  // Pick a random photo from each album for its cover thumbnail!
  return Object.values(map).map(album => {
    const randomIdx = Math.floor(Math.random() * album.photos.length)
    const randomPhoto = album.photos[randomIdx]
    return {
      ...album,
      cover: randomPhoto ? randomPhoto.image_path : null
    }
  })
})
const storageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `/storage/${path.replace(/^\//, '')}`
}

const heroData = ref({
  hero_image: '',
  badge: 'Terpercaya & Berpengalaman',
  title_start: 'Perjalanan Umroh',
  title_highlight: 'Nyaman & Terpercaya',
  subtitle: 'Pilih paket umroh terbaik dengan layanan profesional dari pendaftaran sampai kepulangan. Didampingi pembimbing berpengalaman dan fasilitas terbaik.',
  btn_primary: 'Lihat Paket Umroh',
  btn_secondary: 'Konsultasi Gratis',
  stat1_val: '5000+',
  stat1_label: 'Jamaah Diberangkatkan',
  stat2_val: '10+',
  stat2_label: 'Tahun Pengalaman',
  stat3_val: '4.9★',
  stat3_label: 'Rating Kepuasan',
  card1_title: 'Visa Terjamin',
  card1_sub: 'Proses cepat & aman',
  card2_title: 'Hotel Bintang 5',
  card2_sub: 'Dekat Masjidil Haram'
})

const excellenceData = ref({
  title: 'Mengapa Memilih Kami?',
  subtitle: 'Kami hadir dengan keunggulan yang telah terbukti melayani ribuan jamaah',
  items: [
    { icon: '👨‍💼', title: 'Pembimbing Berpengalaman', desc: 'Dibimbing oleh ustaz bersertifikat dengan pengalaman bertahun-tahun di Makkah dan Madinah.' },
    { icon: '🏨', title: 'Hotel Nyaman Bintang 5', desc: 'Menginap di hotel bintang 5 yang berlokasi strategis dekat Masjidil Haram dan Masjid Nabawi.' },
    { icon: '📅', title: 'Jadwal Teratur & Jelas', desc: 'Itinerary perjalanan yang terstruktur dan transparan dari hari pertama hingga kepulangan.' },
    { icon: '📋', title: 'Bantuan Dokumen Lengkap', desc: 'Tim kami membantu proses visa, paspor, dan seluruh dokumen yang dibutuhkan.' },
    { icon: '🎓', title: 'Manasik Komprehensif', desc: 'Pelatihan manasik sebelum keberangkatan agar ibadah berjalan khusyuk dan benar.' },
    { icon: '🛡️', title: 'Asuransi & Pendampingan Penuh', desc: 'Dilengkapi asuransi perjalanan dan pendampingan 24 jam selama di tanah suci.' },
  ]
})

function toggleFaq(i) { openFaq.value = openFaq.value === i ? null : i }

onMounted(async () => {
  try {
    const { data } = await api.get('/public/home')
    if (data.data.hero) {
      const h = typeof data.data.hero === 'string' ? JSON.parse(data.data.hero) : data.data.hero
      heroData.value = { ...heroData.value, ...h }
    }
    if (data.data.excellence) {
      const ex = typeof data.data.excellence === 'string' ? JSON.parse(data.data.excellence) : data.data.excellence
      excellenceData.value = { ...excellenceData.value, ...ex }
    }
    packages.value = data.data.packages || []
    testimonials.value = data.data.testimonials || []
    galleries.value = data.data.galleries || []
    articles.value = data.data.articles || []
    faqs.value = data.data.faqs || []
    seo.value = data.data.seo || null

    startTestimonialAutoSlide()

    if (seo.value) {
      setMeta(seo.value.meta_title, seo.value.meta_description)
    } else {
      setMeta('Beranda', 'Aplikasi Manajemen Travel Umroh Terpadu')
    }
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

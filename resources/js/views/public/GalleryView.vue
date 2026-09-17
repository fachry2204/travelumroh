<template>
  <div class="py-20 bg-gradient-to-b from-sky-50 to-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h1 class="section-title">Galeri Dokumentasi</h1>
        <p class="section-subtitle">Album foto momen indah dan perjalanan khusyuk jamaah kami di Tanah Suci.</p>
      </div>
      
      <div v-if="loading" class="flex justify-center py-16">
        <div class="w-12 h-12 border-4 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
      </div>
      
      <div v-else-if="albums.length === 0" class="text-center py-16 text-slate-400 font-medium">
        Belum ada album foto galeri.
      </div>
      
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div 
          v-for="album in albums" 
          :key="album.title" 
          @click="openAlbumModal(album)"
          class="group bg-white rounded-2xl overflow-hidden border border-sky-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer flex flex-col justify-between"
        >
          <div>
            <div class="aspect-[4/3] bg-sky-100 overflow-hidden relative">
              <img v-if="album.cover" :src="`/storage/${album.cover}`" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
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
    </div>

    <!-- PUBLIC ALBUM LIGHTBOX MODAL -->
    <div v-if="selectedAlbum" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
      <div class="bg-white rounded-3xl max-w-5xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-sky-100 pb-4">
          <div>
            <h2 class="font-extrabold text-slate-900 text-xl">{{ selectedAlbum.title }}</h2>
            <p class="text-xs text-sky-600 font-semibold mt-0.5">
              🏷️ {{ selectedAlbum.category }} • {{ selectedAlbum.photos.length }} Foto Dokumentasi
            </p>
          </div>
          <button @click="selectedAlbum = null" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xl transition-colors">&times;</button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 py-2">
          <div 
            v-for="photo in selectedAlbum.photos" 
            :key="photo.id"
            @click="previewPhoto = photo"
            class="aspect-square bg-slate-100 rounded-2xl overflow-hidden cursor-pointer relative group border border-slate-200 shadow-sm hover:shadow-md"
          >
            <img :src="`/storage/${photo.image_path}`" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-2xl">
              🔍
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-3 border-t border-sky-100">
          <button @click="selectedAlbum = null" class="btn-primary text-xs px-6 py-2">Tutup Album</button>
        </div>
      </div>
    </div>

    <!-- FULL PHOTO PREVIEW MODAL -->
    <div v-if="previewPhoto" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md" @click.self="previewPhoto = null">
      <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center">
        <button @click="previewPhoto = null" class="absolute -top-12 right-0 text-white text-3xl font-bold hover:text-sky-400">&times;</button>
        <img :src="`/storage/${previewPhoto.image_path}`" class="max-w-full max-h-[80vh] rounded-2xl shadow-2xl object-contain" />
        <p v-if="previewPhoto.title" class="text-white text-sm font-semibold mt-3 bg-black/60 px-4 py-1.5 rounded-full">{{ previewPhoto.title }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/plugins/axios'

const route = useRoute()
const items = ref([])
const loading = ref(true)
const selectedAlbum = ref(null)
const previewPhoto = ref(null)

const albums = computed(() => {
  const map = {}
  items.value.forEach(g => {
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

function openAlbumModal(album) {
  selectedAlbum.value = album
}

onMounted(async () => {
  try { 
    const { data } = await api.get('/public/galleries')
    items.value = data.data.data || data.data

    if (route.query.album) {
      const matchName = decodeURIComponent(route.query.album).trim()
      const found = albums.value.find(a => a.title.toLowerCase() === matchName.toLowerCase())
      if (found) {
        selectedAlbum.value = found
      }
    }
  } finally { 
    loading.value = false 
  }
})
</script>

<template>
  <div class="py-16 bg-sky-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h1 class="section-title">Artikel & Panduan</h1>
        <p class="section-subtitle">Informasi berguna seputar ibadah umroh, tips, dan panduan perjalanan.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Articles Main Column -->
        <div class="lg:col-span-2">
          <div v-if="loading" class="flex justify-center py-12">
            <div class="w-12 h-12 border-4 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
          </div>
          
          <div v-else-if="articles.length === 0" class="text-center py-12 text-slate-500 bg-white rounded-3xl p-8 border border-sky-100">
            Belum ada artikel yang tersedia saat ini.
          </div>
          
          <div v-else class="grid sm:grid-cols-2 gap-6">
            <router-link v-for="article in articles" :key="article.id" :to="`/artikel/${article.slug}`" class="card overflow-hidden group hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
              <div>
                <div class="h-48 bg-sky-200 flex items-center justify-center text-5xl overflow-hidden relative">
                  <img v-if="article.featured_image || article.image_path" :src="`/storage/${(article.featured_image || article.image_path).replace(/^\//, '')}`" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                  <span v-else>📖</span>
                </div>
                <div class="p-5 space-y-2">
                  <div class="flex items-center gap-2">
                    <span class="badge-info text-xs">{{ article.category || 'Berita' }}</span>
                    <span class="text-xs text-slate-400">{{ formatDate(article.published_at || article.created_at) }}</span>
                  </div>
                  <h3 class="text-base font-bold text-slate-900 group-hover:text-sky-600 transition-colors line-clamp-2">
                    {{ article.title }}
                  </h3>
                  <p class="text-xs text-slate-500 line-clamp-3">
                    {{ article.excerpt || (article.content ? article.content.replace(/<[^>]*>?/gm, '').substring(0, 100) + '...' : '') }}
                  </p>
                </div>
              </div>
              <div class="px-5 pb-5 pt-0">
                <span class="text-xs font-bold text-sky-600 group-hover:underline flex items-center gap-1">
                  Baca Selengkapnya &rarr;
                </span>
              </div>
            </router-link>
          </div>
        </div>

        <!-- Sidebar Column -->
        <div class="lg:col-span-1">
          <PackagesSidebar />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'
import PackagesSidebar from '@/components/PackagesSidebar.vue'

const articles = ref([])
const loading = ref(true)

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  try {
    const { data } = await api.get('/public/articles')
    articles.value = data.data.data || data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>

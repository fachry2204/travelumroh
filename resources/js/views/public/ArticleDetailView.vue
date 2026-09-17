<template>
  <div class="py-12 bg-sky-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="flex justify-center py-20">
        <div class="w-12 h-12 border-4 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
      </div>
      
      <div v-else-if="!article" class="text-center py-20 bg-white rounded-3xl p-8 border border-sky-100">
        <h2 class="text-2xl font-bold text-slate-900 mb-2">Artikel Tidak Ditemukan</h2>
        <router-link to="/artikel" class="text-sky-600 hover:underline font-bold">Kembali ke Daftar Artikel</router-link>
      </div>

      <template v-else>
        <!-- Breadcrumb -->
        <div class="text-sm text-slate-500 mb-6 flex gap-2 items-center flex-wrap">
          <router-link to="/" class="hover:text-sky-600">Beranda</router-link> / 
          <router-link to="/artikel" class="hover:text-sky-600">Artikel</router-link> / 
          <span class="text-slate-900 font-medium line-clamp-1">{{ article.title }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
          <!-- Main Article Content Column -->
          <div class="lg:col-span-2">
            <div class="card p-6 md:p-10 shadow-lg bg-white rounded-3xl border border-sky-100">
              <div class="mb-8 border-b border-sky-100 pb-6">
                <div class="flex items-center gap-3 mb-4">
                  <span class="badge-info">{{ article.category || 'Berita' }}</span>
                  <span class="text-sm text-slate-500">{{ formatDate(article.published_at || article.created_at) }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-slate-900 leading-tight">{{ article.title }}</h1>
                <div class="mt-6 flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full gradient-primary text-white flex items-center justify-center font-bold">
                    {{ article.author?.name?.charAt(0) || 'A' }}
                  </div>
                  <div class="text-sm">
                    <div class="font-semibold text-slate-900">{{ article.author?.name || 'Admin' }}</div>
                    <div class="text-slate-500">Penulis</div>
                  </div>
                </div>
              </div>

              <!-- Featured Image -->
              <div v-if="article.featured_image || article.image_path" class="w-full h-72 md:h-[380px] bg-sky-100 rounded-2xl mb-8 overflow-hidden shadow-md">
                <img :src="`/storage/${(article.featured_image || article.image_path).replace(/^\//, '')}`" class="w-full h-full object-cover" />
              </div>

              <div class="prose prose-sky max-w-none text-slate-700 leading-relaxed" v-html="article.content"></div>
            </div>
          </div>

          <!-- Sidebar Column -->
          <div class="lg:col-span-1">
            <PackagesSidebar />
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useSEO } from '@/composables/useSEO'
import api from '@/plugins/axios'
import PackagesSidebar from '@/components/PackagesSidebar.vue'

const route = useRoute()
const { setMeta } = useSEO()
const article = ref(null)
const loading = ref(true)

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

onMounted(async () => {
  try {
    const { data } = await api.get(`/public/articles/${route.params.slug}`)
    article.value = data.data
    if (article.value) {
      const summaryText = article.value.excerpt || article.value.content.replace(/<[^>]*>?/gm, '').substring(0, 160)
      const coverImage = article.value.featured_image || article.value.image_path
      setMeta(article.value.title, summaryText, coverImage, 'article')
    }
  } catch (e) {
    console.error(e)
    setMeta('Artikel Tidak Ditemukan', 'Informasi artikel tidak ditemukan.')
  } finally {
    loading.value = false
  }
})
</script>

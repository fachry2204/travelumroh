<template>
  <div class="py-16 bg-sky-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h1 class="section-title">Tanya Jawab (FAQ)</h1>
        <p class="section-subtitle">Jawaban atas pertanyaan yang sering diajukan seputar layanan kami.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Main FAQ Column -->
        <div class="lg:col-span-2 space-y-6">
          <div v-if="loading" class="flex justify-center py-12">
            <div class="w-12 h-12 border-4 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
          </div>
          
          <div v-else class="space-y-4">
            <div v-for="(faq, i) in items" :key="faq.id" class="card overflow-hidden">
              <button @click="openFaq = openFaq === i ? null : i" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 font-bold text-slate-900 hover:text-sky-700 hover:bg-sky-50/50 transition-colors">
                {{ faq.question }}
                <span class="text-sky-500 flex-shrink-0 transition-transform" :class="openFaq === i ? 'rotate-180' : ''">▼</span>
              </button>
              <div v-show="openFaq === i" class="px-6 pb-5 text-slate-600 leading-relaxed border-t border-sky-100 pt-4">
                {{ faq.answer }}
              </div>
            </div>
          </div>
          
          <div class="mt-8 text-center p-8 bg-white rounded-3xl shadow-sm border border-sky-100">
            <h3 class="font-bold text-slate-900 mb-2">Masih punya pertanyaan?</h3>
            <p class="text-slate-500 text-sm mb-4">Tim CS kami siap membantu menjawab pertanyaan Anda.</p>
            <a href="https://wa.me/6281234567890" target="_blank" class="btn-primary">💬 Hubungi via WhatsApp</a>
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

const items = ref([])
const loading = ref(true)
const openFaq = ref(0)

onMounted(async () => {
  try { 
    const { data } = await api.get('/public/faqs')
    items.value = data.data 
  } finally { 
    loading.value = false 
  }
})
</script>

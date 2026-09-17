<template>
  <div class="space-y-1.5">
    <label v-if="label" class="form-label flex items-center justify-between">
      <span>{{ label }}</span>
      <span v-if="hint" class="text-xs font-normal text-slate-400">{{ hint }}</span>
    </label>

    <div 
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
      @click="triggerFileInput"
      :class="[
        'relative border-2 border-dashed rounded-2xl p-4 transition-all duration-200 cursor-pointer text-center flex flex-col items-center justify-center min-h-[120px]',
        isDragging ? 'border-sky-500 bg-sky-50 shadow-md ring-4 ring-sky-100' : 'border-sky-200 hover:border-sky-400 bg-slate-50/50 hover:bg-sky-50/50',
        error ? 'border-red-300 bg-red-50/30' : ''
      ]"
    >
      <input 
        ref="fileInput" 
        type="file" 
        class="hidden" 
        :accept="accept" 
        @change="handleFileChange" 
      />

      <!-- New File Selected Preview -->
      <div v-if="previewUrl" class="w-full flex items-center justify-between gap-3 p-2 bg-white rounded-xl border border-sky-100 shadow-sm">
        <div class="flex items-center gap-3 min-w-0">
          <img v-if="isImage" :src="previewUrl" class="w-12 h-12 object-contain rounded-lg border border-slate-100 bg-slate-50 p-1" alt="Preview" />
          <div v-else class="w-10 h-10 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-xl font-bold">📄</div>
          <div class="text-left truncate">
            <div class="text-xs font-semibold text-slate-800 truncate">{{ fileName }}</div>
            <div class="text-[10px] text-slate-400">{{ fileSizeFormatted }}</div>
          </div>
        </div>
        <button 
          type="button" 
          @click.stop="clearFile" 
          class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors text-xs font-medium flex items-center gap-1"
        >
          <span>✕</span> Batal
        </button>
      </div>

      <!-- Current Saved File Display -->
      <div v-else-if="currentFileUrl" class="w-full flex items-center justify-between gap-3 p-2 bg-white rounded-xl border border-sky-100 shadow-sm">
        <div class="flex items-center gap-3 min-w-0">
          <img v-if="isCurrentFileImage" :src="fullCurrentFileUrl" class="w-12 h-12 object-contain rounded-lg border border-slate-100 bg-slate-50 p-1" alt="Current Logo" />
          <div v-else class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl font-bold">✓</div>
          <div class="text-left truncate">
            <div class="text-xs font-semibold text-emerald-700 flex items-center gap-1">
              <span>✓</span> File Terpasang
            </div>
            <div class="text-[10px] text-slate-400">Klik untuk mengganti file</div>
          </div>
        </div>
        <span class="text-xs font-semibold text-sky-600 bg-sky-50 px-2.5 py-1 rounded-lg hover:bg-sky-100">Ganti</span>
      </div>

      <!-- Default Upload Prompt -->
      <div v-else class="py-2 space-y-1.5">
        <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center mx-auto transition-transform group-hover:scale-110">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
        </div>
        <div class="text-xs text-slate-600 font-medium">
          <span class="text-sky-600 font-semibold underline decoration-sky-300">Pilih file</span> atau seret ke sini
        </div>
      </div>
    </div>

    <div v-if="error" class="text-xs text-red-500 font-medium">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: { type: [File, Object, String], default: null },
  label: { type: String, default: '' },
  hint: { type: String, default: '' },
  accept: { type: String, default: 'image/*' },
  currentFileUrl: { type: String, default: '' },
  error: { type: String, default: '' }
})

const emit = defineEmits(['update:modelValue', 'change'])

const fileInput = ref(null)
const isDragging = ref(false)
const selectedFile = ref(null)
const previewUrl = ref('')

const fileName = computed(() => selectedFile.value?.name || '')
const fileSizeFormatted = computed(() => {
  if (!selectedFile.value?.size) return ''
  const kb = selectedFile.value.size / 1024
  return kb > 1024 ? `${(kb / 1024).toFixed(2)} MB` : `${Math.round(kb)} KB`
})

const isImage = computed(() => selectedFile.value?.type?.startsWith('image/'))

const fullCurrentFileUrl = computed(() => {
  if (!props.currentFileUrl) return ''
  if (props.currentFileUrl.startsWith('http')) return props.currentFileUrl
  return `/storage/${props.currentFileUrl.replace(/^\//, '')}`
})

const isCurrentFileImage = computed(() => {
  if (!props.currentFileUrl) return false
  return /\.(jpg|jpeg|png|gif|webp|svg)($|\?)/i.test(props.currentFileUrl)
})

function triggerFileInput() {
  fileInput.value?.click()
}

function handleFileChange(event) {
  const file = event.target.files?.[0]
  if (file) {
    processFile(file)
  }
}

function handleDrop(event) {
  isDragging.value = false
  const file = event.dataTransfer.files?.[0]
  if (file) {
    processFile(file)
  }
}

function processFile(file) {
  selectedFile.value = file
  if (file.type.startsWith('image/')) {
    previewUrl.value = URL.createObjectURL(file)
  } else {
    previewUrl.value = ''
  }
  emit('update:modelValue', file)
  emit('change', file)
}

function clearFile() {
  selectedFile.value = null
  previewUrl.value = ''
  if (fileInput.value) fileInput.value.value = ''
  emit('update:modelValue', null)
  emit('change', null)
}

watch(() => props.modelValue, (newVal) => {
  if (!newVal) {
    selectedFile.value = null
    previewUrl.value = ''
  }
})
</script>

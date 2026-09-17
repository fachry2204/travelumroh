<template>
  <div class="border border-slate-300 rounded-xl overflow-hidden shadow-sm bg-white focus-within:border-sky-500 focus-within:ring-2 focus-within:ring-sky-200 transition-all">
    <!-- WordPress Style Toolbar -->
    <div class="bg-slate-50 border-b border-slate-200 p-2 flex flex-wrap items-center gap-1 text-slate-700 select-none">
      <!-- Mode Switcher (Visual vs Code) -->
      <div class="flex bg-slate-200 rounded-lg p-0.5 mr-2">
        <button type="button" @click="mode = 'visual'"
          class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors"
          :class="mode === 'visual' ? 'bg-white text-sky-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
          ✏️ Visual
        </button>
        <button type="button" @click="mode = 'code'"
          class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors"
          :class="mode === 'code' ? 'bg-white text-sky-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
          &lt;/&gt; HTML Code
        </button>
      </div>

      <div v-if="mode === 'visual'" class="flex flex-wrap items-center gap-1">
        <!-- Paragraph / Heading Selector -->
        <select @change="execBlock($event.target.value); $event.target.value=''"
          class="text-xs font-semibold bg-white border border-slate-300 rounded px-2 py-1 hover:border-slate-400 focus:outline-none">
          <option value="">Judul / Format...</option>
          <option value="p">Paragraph</option>
          <option value="h2">Heading 2 (Besar)</option>
          <option value="h3">Heading 3 (Sedang)</option>
          <option value="h4">Heading 4 (Kecil)</option>
          <option value="blockquote">Kutipan / Quote</option>
        </select>

        <span class="w-px h-5 bg-slate-300 mx-1"></span>

        <!-- Bold, Italic, Underline, Strike -->
        <button type="button" @click="exec('bold')" title="Tebal (Ctrl+B)" class="btn-tb font-bold">B</button>
        <button type="button" @click="exec('italic')" title="Miring (Ctrl+I)" class="btn-tb italic font-serif">I</button>
        <button type="button" @click="exec('underline')" title="Garis Bawah (Ctrl+U)" class="btn-tb underline">U</button>
        <button type="button" @click="exec('strikeThrough')" title="Coret" class="btn-tb line-through">S</button>

        <span class="w-px h-5 bg-slate-300 mx-1"></span>

        <!-- Lists -->
        <button type="button" @click="exec('insertUnorderedList')" title="Bullet List" class="btn-tb">• List</button>
        <button type="button" @click="exec('insertOrderedList')" title="Numbered List" class="btn-tb">1. List</button>

        <span class="w-px h-5 bg-slate-300 mx-1"></span>

        <!-- Alignment -->
        <button type="button" @click="exec('justifyLeft')" title="Rata Kiri" class="btn-tb">⬅️</button>
        <button type="button" @click="exec('justifyCenter')" title="Rata Tengah" class="btn-tb">↔️</button>
        <button type="button" @click="exec('justifyRight')" title="Rata Kanan" class="btn-tb">➡️</button>

        <span class="w-px h-5 bg-slate-300 mx-1"></span>

        <!-- Link & Image -->
        <button type="button" @click="insertLink" title="Sisipkan Link" class="btn-tb">🔗 Link</button>
        <button type="button" @click="triggerImageUpload" title="Sisipkan Gambar dalam Artikel" class="btn-tb bg-sky-50 text-sky-700 border-sky-200 hover:bg-sky-100">🖼️ Sisipkan Gambar</button>
        <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="uploadContentImage" />

        <span class="w-px h-5 bg-slate-300 mx-1"></span>

        <!-- Clear Format & Undo/Redo -->
        <button type="button" @click="exec('removeFormat')" title="Hapus Format" class="btn-tb text-amber-700">🧹 Clear</button>
        <button type="button" @click="exec('undo')" title="Undo (Ctrl+Z)" class="btn-tb">↩️</button>
        <button type="button" @click="exec('redo')" title="Redo (Ctrl+Y)" class="btn-tb">↪️</button>
      </div>
    </div>

    <!-- Visual Editor Area -->
    <div v-show="mode === 'visual'"
      ref="editor"
      contenteditable="true"
      @input="onInput"
      @blur="onInput"
      class="p-4 min-h-[260px] max-h-[500px] overflow-y-auto prose prose-sky max-w-none text-slate-800 focus:outline-none text-sm leading-relaxed"
      placeholder="Tulis artikel di sini...">
    </div>

    <!-- HTML Source Code View -->
    <textarea v-show="mode === 'code'"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      rows="12"
      class="w-full p-4 font-mono text-xs text-slate-800 bg-slate-900 text-sky-300 focus:outline-none leading-relaxed"
      placeholder="HTML Content...">
    </textarea>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import api from '@/plugins/axios'

const props = defineProps({
  modelValue: { type: String, default: '' }
})

const emit = defineEmits(['update:modelValue'])

const mode = ref('visual')
const editor = ref(null)
const fileInput = ref(null)

onMounted(() => {
  if (editor.value) {
    editor.value.innerHTML = props.modelValue || ''
  }
})

watch(() => props.modelValue, (newVal) => {
  if (editor.value && editor.value.innerHTML !== newVal) {
    editor.value.innerHTML = newVal || ''
  }
})

function onInput() {
  if (editor.value) {
    emit('update:modelValue', editor.value.innerHTML)
  }
}

function exec(command, value = null) {
  document.execCommand(command, false, value)
  onInput()
}

function execBlock(tag) {
  if (!tag) return
  document.execCommand('formatBlock', false, tag)
  onInput()
}

function insertLink() {
  const url = prompt('Masukkan URL Link (misal: https://example.com):')
  if (url) {
    exec('createLink', url)
  }
}

function triggerImageUpload() {
  if (fileInput.value) {
    fileInput.value.click()
  }
}

async function uploadContentImage(e) {
  const file = e.target.files[0]
  if (!file) return
  try {
    const formData = new FormData()
    formData.append('image', file)
    const { data } = await api.post('/admin/cms/articles/upload-image', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    const imgUrl = data.data.url
    exec('insertImage', imgUrl)
  } catch (err) {
    alert('Gagal mengunggah gambar ke artikel')
  }
}
</script>

<style scoped>
.btn-tb {
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  line-height: 1rem;
  font-weight: 500;
  border: 1px solid transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 26px;
  background-color: transparent;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-tb:hover {
  background-color: #e2e8f0;
  border-color: #cbd5e1;
}
</style>

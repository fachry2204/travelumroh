<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header flex flex-wrap justify-between items-center gap-4">
      <div>
        <h2 class="page-title text-2xl font-extrabold text-slate-900">CMS Editor & Management</h2>
        <p class="text-sm text-slate-500 mt-1">Kelola konten banner hero, seksi keunggulan, halaman statis, artikel, galeri, FAQ, dan testimoni jamaah.</p>
      </div>
      <div class="flex gap-2">
        <a href="/" target="_blank" class="btn-outline text-xs px-3 py-2 flex items-center gap-1">
          <span>🌐</span> Lihat Website
        </a>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card p-2 flex flex-wrap gap-1 bg-white border border-sky-100 shadow-sm">
      <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
        class="px-4 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center gap-2"
        :class="activeTab === tab.id ? 'bg-sky-600 text-white shadow-md' : 'text-slate-600 hover:bg-sky-50 hover:text-sky-700'">
        <span>{{ tab.icon }}</span>
        <span>{{ tab.label }}</span>
      </button>
    </div>

    <!-- TAB 1: HALAMAN CMS & LANDING PAGE -->
    <div v-if="activeTab === 'pages'" class="space-y-6">
      <div class="card p-6">
        <div class="flex justify-between items-center border-b border-sky-100 pb-4 mb-6">
          <h3 class="font-bold text-slate-900 text-lg">Editor Halaman & Seksi Website</h3>
          <select v-model="selectedPageType" @change="loadPageData" class="form-select w-64 text-sm font-semibold">
            <option value="hero">🚀 Banner Hero (Header Utama)</option>
            <option value="excellence">⭐ Mengapa Memilih Kami?</option>
            <option value="contact">📞 Halaman Kontak (Alamat, Telp, Email, Maps)</option>
            <option value="about">Tentang Kami</option>
            <option value="terms">Syarat & Ketentuan</option>
            <option value="privacy">Kebijakan Privasi</option>
          </select>
        </div>

        <!-- 1. HERO BANNER EDITOR -->
        <form v-if="selectedPageType === 'hero'" @submit.prevent="saveHero" class="space-y-6">
          <!-- HERO IMAGE UPLOAD SECTION -->
          <div class="p-4 bg-white rounded-xl border border-sky-200 shadow-sm space-y-3">
            <h4 class="font-bold text-slate-900 text-base flex items-center gap-2">
              <span>🖼️</span> Gambar Banner Hero (Sisi Kanan Halaman Depan)
            </h4>
            <p class="text-xs text-slate-500">Upload foto banner untuk menggantikan ilustrasi ikon Ka'bah/Masjid di banner depan.</p>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pt-2">
              <!-- Preview -->
              <div class="w-40 h-28 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center relative group">
                <img v-if="heroForm.hero_image" :src="storageUrl(heroForm.hero_image)" class="w-full h-full object-cover" />
                <div v-else class="text-center text-slate-400 p-2 text-xs">
                  <span class="text-2xl block mb-1">🕌</span>
                  <span>(Default Emoji)</span>
                </div>
              </div>

              <!-- Upload input -->
              <div class="flex-1 space-y-2 w-full">
                <label class="flex items-center justify-center gap-3 px-4 py-3 bg-sky-50/60 hover:bg-sky-100/80 border-2 border-dashed border-sky-200 hover:border-sky-400 rounded-xl cursor-pointer transition-all">
                  <span class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-sm">📤</span>
                  <div class="text-left">
                    <span class="text-xs font-bold text-sky-900 block">Pilih / Drag Foto Banner Baru</span>
                    <span class="text-[11px] text-slate-400">Format: PNG, JPG, WEBP (Maks 5MB)</span>
                  </div>
                  <input type="file" @change="uploadHeroImageFile" accept="image/*" class="hidden" :disabled="uploadingHeroImg" />
                </label>
                <div v-if="uploadingHeroImg" class="text-xs text-sky-600 font-semibold animate-pulse">⏳ Uploading gambar banner...</div>
                <button v-if="heroForm.hero_image" type="button" @click="heroForm.hero_image = ''" class="text-xs text-red-600 hover:underline font-medium block">
                  🗑️ Hapus Foto (Gunakan Default Emoji)
                </button>
              </div>
            </div>
          </div>

          <div class="p-4 bg-sky-50 rounded-xl border border-sky-100 space-y-4">
            <h4 class="font-bold text-slate-900 text-base">Teks Utama Banner Hero</h4>
            <div>
              <label class="form-label">Teks Badge Atas</label>
              <input v-model="heroForm.badge" type="text" class="form-input" placeholder="Terpercaya & Berpengalaman" />
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="form-label">Judul Utama (Teks Putih) *</label>
                <input v-model="heroForm.title_start" type="text" class="form-input" required placeholder="Perjalanan Umroh" />
              </div>
              <div>
                <label class="form-label">Judul Highlight (Teks Kuning Emas) *</label>
                <input v-model="heroForm.title_highlight" type="text" class="form-input" required placeholder="Nyaman & Terpercaya" />
              </div>
            </div>

            <div>
              <label class="form-label">Deskripsi / Subjudul Hero *</label>
              <textarea v-model="heroForm.subtitle" class="form-input" rows="3" required placeholder="Pilih paket umroh terbaik dengan layanan profesional..."></textarea>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="form-label">Teks Tombol CTA 1 (Kuning)</label>
                <input v-model="heroForm.btn_primary" type="text" class="form-input" placeholder="Lihat Paket Umroh" />
              </div>
              <div>
                <label class="form-label">Teks Tombol CTA 2 (Konsultasi)</label>
                <input v-model="heroForm.btn_secondary" type="text" class="form-input" placeholder="Konsultasi Gratis" />
              </div>
            </div>
          </div>

          <!-- STATS EDITOR -->
          <div class="p-4 bg-amber-50/50 rounded-xl border border-amber-100 space-y-4">
            <h4 class="font-bold text-slate-900 text-base">Statistik di Bawah Banner</h4>
            <div class="grid md:grid-cols-3 gap-4">
              <div class="card p-3 space-y-2">
                <label class="form-label">Angka Stat 1</label>
                <input v-model="heroForm.stat1_val" type="text" class="form-input" placeholder="5000+" />
                <label class="form-label">Label Stat 1</label>
                <input v-model="heroForm.stat1_label" type="text" class="form-input" placeholder="Jamaah Diberangkatkan" />
              </div>
              <div class="card p-3 space-y-2">
                <label class="form-label">Angka Stat 2</label>
                <input v-model="heroForm.stat2_val" type="text" class="form-input" placeholder="10+" />
                <label class="form-label">Label Stat 2</label>
                <input v-model="heroForm.stat2_label" type="text" class="form-input" placeholder="Tahun Pengalaman" />
              </div>
              <div class="card p-3 space-y-2">
                <label class="form-label">Angka Stat 3</label>
                <input v-model="heroForm.stat3_val" type="text" class="form-input" placeholder="4.9★" />
                <label class="form-label">Label Stat 3</label>
                <input v-model="heroForm.stat3_label" type="text" class="form-input" placeholder="Rating Kepuasan" />
              </div>
            </div>
          </div>

          <!-- FLOATING CARDS EDITOR -->
          <div class="p-4 bg-emerald-50/50 rounded-xl border border-emerald-100 space-y-4">
            <h4 class="font-bold text-slate-900 text-base">Kartu Melayang (Floating Cards Visual)</h4>
            <div class="grid md:grid-cols-2 gap-4">
              <div class="card p-3 space-y-2">
                <label class="form-label">Kartu 1 (Kiri Bawah) - Judul</label>
                <input v-model="heroForm.card1_title" type="text" class="form-input" placeholder="Visa Terjamin" />
                <label class="form-label">Sub-Teks Kartu 1</label>
                <input v-model="heroForm.card1_sub" type="text" class="form-input" placeholder="Proses cepat & aman" />
              </div>
              <div class="card p-3 space-y-2">
                <label class="form-label">Kartu 2 (Kanan Atas) - Judul</label>
                <input v-model="heroForm.card2_title" type="text" class="form-input" placeholder="Hotel Bintang 5" />
                <label class="form-label">Sub-Teks Kartu 2</label>
                <input v-model="heroForm.card2_sub" type="text" class="form-input" placeholder="Dekat Masjidil Haram" />
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-3">
            <button type="submit" class="btn-primary text-sm px-6 py-2.5" :disabled="savingPage">
              {{ savingPage ? 'Menyimpan...' : '💾 Simpan Banner Hero' }}
            </button>
          </div>
        </form>

        <!-- 2. EXCELLENCE EDITOR (MENGAPA MEMILIH KAMI) -->
        <form v-else-if="selectedPageType === 'excellence'" @submit.prevent="saveExcellence" class="space-y-6">
          <div class="space-y-4">
            <div>
              <label class="form-label">Judul Seksi *</label>
              <input v-model="excellenceForm.title" type="text" class="form-input" required placeholder="Mengapa Memilih Kami?" />
            </div>
            <div>
              <label class="form-label">Subjudul Seksi *</label>
              <input v-model="excellenceForm.subtitle" type="text" class="form-input" required placeholder="Kami hadir dengan keunggulan yang telah terbukti melayani ribuan jamaah" />
            </div>
          </div>

          <div class="space-y-4 pt-4 border-t border-sky-100">
            <div class="flex justify-between items-center">
              <h4 class="font-bold text-slate-900 text-base">Poin Keunggulan ({{ excellenceForm.items.length }})</h4>
              <button type="button" @click="addExcellenceItem" class="btn-secondary text-xs px-3 py-1.5 flex items-center gap-1">
                <span>➕</span> Tambah Poin
              </button>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div v-for="(item, index) in excellenceForm.items" :key="index" class="card p-4 border border-sky-100 space-y-3 relative group">
                <div class="flex items-center gap-2">
                  <input v-model="item.icon" type="text" class="form-input w-16 text-center text-xl" placeholder="Emoji" />
                  <input v-model="item.title" type="text" class="form-input font-bold" placeholder="Judul Keunggulan" />
                  <button type="button" @click="removeExcellenceItem(index)" class="text-red-500 hover:text-red-700 text-lg px-2 font-bold" title="Hapus">&times;</button>
                </div>
                <div>
                  <textarea v-model="item.desc" class="form-input text-xs" rows="2" placeholder="Deskripsi keunggulan..."></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-3">
            <button type="submit" class="btn-primary text-sm px-6 py-2.5" :disabled="savingPage">
              {{ savingPage ? 'Menyimpan...' : '💾 Simpan Pengaturan Keunggulan' }}
            </button>
          </div>
        </form>

        <!-- 3. CONTACT PAGE EDITOR -->
        <form v-else-if="selectedPageType === 'contact'" @submit.prevent="saveContactPage" class="space-y-6">
          <div class="p-5 bg-sky-50/60 rounded-2xl border border-sky-100 space-y-4">
            <h4 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
              <span>📞</span> Informasi & Kontak Utama Halaman Kontak
            </h4>
            <p class="text-xs text-slate-500">Kelola judul header, alamat kantor, nomor telepon, WhatsApp, email, jam kerja, dan embed Google Maps.</p>

            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label class="form-label text-xs">Judul Halaman Header</label>
                <input v-model="contactForm.title" type="text" class="form-input" placeholder="Hubungi Kami" required />
              </div>
              <div>
                <label class="form-label text-xs">Sub-Judul / Deskripsi Singkat</label>
                <input v-model="contactForm.subtitle" type="text" class="form-input" placeholder="Kami siap membantu..." />
              </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label class="form-label text-xs">Nama Kantor</label>
                <input v-model="contactForm.office_name" type="text" class="form-input" placeholder="Kantor Pusat" />
              </div>
              <div>
                <label class="form-label text-xs">Email Layanan / CS</label>
                <input v-model="contactForm.email" type="email" class="form-input" placeholder="info@travelumroh.com" />
              </div>
            </div>

            <div>
              <label class="form-label text-xs">Alamat Lengkap Kantor</label>
              <textarea v-model="contactForm.address" rows="2" class="form-input" placeholder="Jl. Raya Umroh No. 123..."></textarea>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label class="form-label text-xs">Nomor Telepon Kantor (Landline)</label>
                <input v-model="contactForm.phone" type="text" class="form-input" placeholder="021-12345678" />
              </div>
              <div>
                <label class="form-label text-xs">Nomor WhatsApp CS</label>
                <input v-model="contactForm.whatsapp" type="text" class="form-input" placeholder="+62 812-3456-7890" />
              </div>
            </div>

            <div>
              <label class="form-label text-xs">Jam Operasional (Pisahkan baris baru untuk Sabtu/Minggu)</label>
              <textarea v-model="contactForm.operating_hours" rows="3" class="form-input" placeholder="Senin - Jumat: 08.00 - 17.00 WIB&#10;Sabtu: 08.00 - 13.00 WIB"></textarea>
            </div>

            <div>
              <label class="form-label text-xs">URL / Link Embed Google Maps (Optional)</label>
              <input v-model="contactForm.google_maps_embed" type="text" class="form-input" placeholder="https://www.google.com/maps/embed?pb=..." />
              <span class="text-[11px] text-slate-400 mt-1 block">Salin atribut src dari kode Iframe Google Maps Share lokasi kantor Anda.</span>
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button type="submit" :disabled="savingContact" class="btn-primary text-sm px-6 py-2.5 flex items-center gap-2">
              <span v-if="savingContact" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span>💾 Simpan Pengaturan Halaman Kontak</span>
            </button>
          </div>
        </form>

        <!-- 4. GENERIC STATIC PAGE EDITOR -->
        <form v-else @submit.prevent="savePage" class="space-y-4">
          <div>
            <label class="form-label">Judul Halaman *</label>
            <input v-model="pageForm.title" type="text" class="form-input" required placeholder="Judul Halaman" />
          </div>

          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Meta Title (SEO)</label>
              <input v-model="pageForm.meta_title" type="text" class="form-input" placeholder="Title tag untuk Google SEO" />
            </div>
            <div>
              <label class="form-label">Status Halaman</label>
              <select v-model="pageForm.status" class="form-select">
                <option value="active">Aktif / Dipublikasikan</option>
                <option value="inactive">Nonaktif / Draft</option>
              </select>
            </div>
          </div>

          <div>
            <label class="form-label">Meta Description (SEO)</label>
            <textarea v-model="pageForm.meta_description" class="form-input" rows="2" placeholder="Deskripsi singkat untuk pencarian Google..."></textarea>
          </div>

          <div>
            <label class="form-label">Konten Halaman (HTML / Text)</label>
            <textarea v-model="pageForm.content" class="form-input font-mono text-sm" rows="10" placeholder="Isi konten halaman..."></textarea>
          </div>

          <div class="flex justify-end pt-3">
            <button type="submit" class="btn-primary text-sm px-6 py-2.5" :disabled="savingPage">
              {{ savingPage ? 'Menyimpan...' : '💾 Simpan Perubahan Halaman' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- TAB 2: ARTIKEL & BLOG -->
    <div v-if="activeTab === 'articles'" class="space-y-6">
      <div class="flex justify-between items-center">
        <h3 class="font-bold text-slate-900 text-lg">Kelola Artikel & Panduan</h3>
        <button @click="openArticleModal(null)" class="btn-primary text-sm px-4 py-2 flex items-center gap-1.5">
          <span>✍️</span> Tulis Artikel Baru
        </button>
      </div>

      <div class="card overflow-hidden">
        <div v-if="loadingArticles" class="p-12 text-center">
          <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
        </div>
        <table v-else class="w-full">
          <thead class="bg-sky-50 border-b border-sky-100">
            <tr>
              <th class="table-th">Judul Artikel</th>
              <th class="table-th hidden md:table-cell">Kategori</th>
              <th class="table-th">Status</th>
              <th class="table-th text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-sky-50">
            <tr v-if="articles.length === 0">
              <td colspan="4" class="table-td text-center py-12 text-slate-400">Belum ada artikel</td>
            </tr>
            <tr v-for="a in articles" :key="a.id" class="hover:bg-sky-50/50">
              <td class="table-td max-w-md">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-10 h-10 rounded-lg bg-sky-100 flex-shrink-0 overflow-hidden flex items-center justify-center text-sm font-bold text-sky-700 border border-sky-200">
                    <img v-if="a.featured_image || a.image_path" :src="storageUrl(a.featured_image || a.image_path)" class="w-full h-full object-cover" />
                    <span v-else>📖</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <div class="font-bold text-slate-900 text-sm truncate" :title="a.title">{{ a.title }}</div>
                    <div class="text-xs text-slate-400 truncate mt-0.5" :title="stripTags(a.excerpt)">{{ stripTags(a.excerpt) || 'Tanpa ringkasan' }}</div>
                  </div>
                </div>
              </td>
              <td class="table-td hidden md:table-cell whitespace-nowrap">
                <span class="text-xs font-semibold px-2.5 py-1 bg-sky-100 text-sky-700 rounded-full">
                  {{ a.category || 'Umum' }}
                </span>
              </td>
              <td class="table-td whitespace-nowrap">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full" :class="a.status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                  {{ a.status === 'published' ? 'Published' : 'Draft' }}
                </span>
              </td>
              <td class="table-td text-right whitespace-nowrap">
                <div class="flex justify-end gap-1.5">
                  <button @click="openArticleModal(a)" class="text-xs px-2.5 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg font-medium">Edit</button>
                  <button @click="deleteArticle(a)" class="text-xs px-2.5 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium">Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: GALERI FOTO & ALBUM -->
    <div v-if="activeTab === 'galleries'" class="space-y-6">
      <div class="flex justify-between items-center">
        <div>
          <h3 class="font-bold text-slate-900 text-lg">Album Dokumentasi Galeri</h3>
          <p class="text-xs text-slate-500">Kelola album foto kegiatan jamaah, manasik, dan ziarah.</p>
        </div>
        <button @click="showGalleryModal = true" class="btn-primary text-sm px-4 py-2 flex items-center gap-1.5 shadow-md">
          <span>🖼️</span> Tambahkan Gallery Baru
        </button>
      </div>

      <div v-if="loadingGalleries" class="p-12 text-center card">
        <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
      </div>
      <div v-else-if="albums.length === 0" class="card p-12 text-center text-slate-400">
        Belum ada album galeri. Klik tombol "Tambahkan Gallery Baru" di atas untuk membuat album.
      </div>
      
      <!-- ALBUM GRID CARDS -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="album in albums" :key="album.title" class="card overflow-hidden group hover:border-sky-300 transition-all flex flex-col justify-between shadow-sm">
          <div>
            <!-- Cover image with photo count badge -->
            <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative cursor-pointer" @click="viewAlbumDetails(album)">
              <img :src="storageUrl(album.cover)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              <div class="absolute top-2 right-2 bg-black/60 backdrop-blur text-white text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1">
                <span>📷</span> {{ album.photos.length }} Foto
              </div>
            </div>
            <div class="p-4 space-y-1">
              <span class="text-[11px] font-bold px-2 py-0.5 bg-sky-100 text-sky-700 rounded-full inline-block">
                {{ album.category }}
              </span>
              <h4 class="font-bold text-slate-900 text-sm line-clamp-2 leading-snug pt-1">{{ album.title }}</h4>
            </div>
          </div>
          <div class="p-4 pt-0 flex gap-2 border-t border-slate-100 mt-2">
            <button @click="viewAlbumDetails(album)" class="btn-secondary text-xs py-1.5 flex-1 justify-center">
              👁️ Lihat Foto ({{ album.photos.length }})
            </button>
            <button @click="deleteAlbumGroup(album)" class="text-xs px-2.5 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium" title="Hapus Album">
              🗑️
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 4: PERTANYAAN UMUM (FAQ) -->
    <div v-if="activeTab === 'faqs'" class="space-y-6">
      <div class="flex justify-between items-center">
        <h3 class="font-bold text-slate-900 text-lg">Kelola Pertanyaan Umum (FAQ)</h3>
        <button @click="openFaqModal(null)" class="btn-primary text-sm px-4 py-2 flex items-center gap-1.5">
          <span>❓</span> Tambah FAQ
        </button>
      </div>

      <div class="card overflow-hidden">
        <div v-if="loadingFaqs" class="p-12 text-center">
          <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
        </div>
        <div v-else-if="faqs.length === 0" class="p-12 text-center text-slate-400">
          Belum ada data FAQ.
        </div>
        <div v-else class="divide-y divide-sky-50">
          <div v-for="f in faqs" :key="f.id" class="p-4 hover:bg-sky-50/50 flex justify-between items-start gap-4">
            <div class="space-y-1 flex-1">
              <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                {{ f.question }}
                <span class="text-xs font-semibold px-2 py-0.5 bg-sky-100 text-sky-700 rounded-full">{{ f.category || 'Umum' }}</span>
              </div>
              <p class="text-slate-600 text-xs leading-relaxed whitespace-pre-line">{{ f.answer }}</p>
            </div>
            <div class="flex gap-1 flex-shrink-0">
              <button @click="openFaqModal(f)" class="text-xs px-2.5 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg font-medium">Edit</button>
              <button @click="deleteFaq(f)" class="text-xs px-2.5 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium">Hapus</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 5: TESTIMONI JAMAAH -->
    <div v-if="activeTab === 'testimonials'" class="space-y-6">
      <div class="flex justify-between items-center">
        <h3 class="font-bold text-slate-900 text-lg">Testimoni Jamaah</h3>
        <button @click="openAddTestimonialModal" class="btn-primary text-sm px-4 py-2 flex items-center gap-1.5">
          <span>⭐</span> Tambah Testimoni
        </button>
      </div>

      <div v-if="loadingTestimonials" class="p-12 text-center card">
        <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
      </div>
      <div v-else-if="testimonials.length === 0" class="card p-12 text-center text-slate-400">
        Belum ada testimoni jamaah.
      </div>
      <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="t in testimonials" :key="t.id" class="card p-5 space-y-3 relative group border border-sky-100 flex flex-col justify-between">
          <div class="space-y-3">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-sky-100 overflow-hidden flex-shrink-0 border border-sky-200 shadow-sm flex items-center justify-center">
                <img v-if="t.photo" :src="storageUrl(t.photo)" class="w-full h-full object-cover" />
                <span v-else class="text-xl font-bold text-sky-600">{{ t.name?.charAt(0) || '👤' }}</span>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex justify-between items-center">
                  <div class="font-bold text-slate-900 text-sm truncate">{{ t.name }}</div>
                  <div class="text-amber-400 text-xs">
                    <span v-for="i in t.rating" :key="i">⭐</span>
                  </div>
                </div>
                <div class="text-xs text-sky-600 font-medium truncate">{{ t.package_name || 'Jamaah Umroh' }}</div>
              </div>
            </div>
            <p class="text-xs text-slate-600 italic leading-relaxed">"{{ t.content }}"</p>
          </div>

          <div class="pt-3 flex justify-end gap-2 border-t border-sky-100">
            <button @click="openEditTestimonialModal(t)" class="text-xs px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-md font-bold">Edit</button>
            <button @click="deleteTestimonial(t)" class="text-xs px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-md font-bold">Hapus</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ARTICLE MODAL -->
    <div v-if="showArticleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl max-w-5xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-sky-100 pb-3">
          <h3 class="font-bold text-slate-900 text-lg">{{ isEditArticle ? 'Edit Artikel' : 'Tulis Artikel Baru' }}</h3>
          <button @click="showArticleModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form @submit.prevent="saveArticle" class="space-y-4">
          <div>
            <label class="form-label">Judul Artikel *</label>
            <input v-model="articleForm.title" type="text" class="form-input" required placeholder="Judul Artikel..." />
          </div>

          <div class="grid sm:grid-cols-2 gap-3">
            <div>
              <label class="form-label">Kategori</label>
              <input v-model="articleForm.category" type="text" class="form-input" placeholder="Misal: Tips, Panduan, Berita" />
            </div>
            <div>
              <label class="form-label">Status</label>
              <select v-model="articleForm.status" class="form-select">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
            </div>
          </div>

          <div>
            <label class="form-label mb-2">Gambar Sampul Artikel (Featured Image)</label>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
              <div v-if="articleImagePreview || articleForm.featured_image" class="w-32 h-20 rounded-xl bg-slate-100 overflow-hidden border-2 border-sky-200 flex-shrink-0 shadow-sm relative group">
                <img :src="articleImagePreview || storageUrl(articleForm.featured_image)" class="w-full h-full object-cover" />
              </div>
              <label class="flex-1 w-full flex items-center justify-center gap-3 px-4 py-3 bg-sky-50/60 hover:bg-sky-100/80 border-2 border-dashed border-sky-200 hover:border-sky-400 rounded-xl cursor-pointer transition-all">
                <span class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-sm">📁</span>
                <div class="text-left">
                  <span class="text-xs font-bold text-sky-900 block">Pilih / Drag Gambar Sampul Artikel</span>
                  <span class="text-[11px] text-slate-400">Format: PNG, JPG, WEBP (Maks 5MB)</span>
                </div>
                <input type="file" @change="handleArticleFile" accept="image/*" class="hidden" />
              </label>
            </div>
          </div>

          <div>
            <label class="form-label mb-2">Isi Lengkap Artikel *</label>
            <RichTextEditor v-model="articleForm.content" />
          </div>

          <div>
            <div class="flex justify-between items-center mb-1">
              <label class="form-label mb-0">Ringkasan Artikel (Excerpt / Auto SEO Summary)</label>
              <button type="button" @click="autoGenerateExcerpt" class="text-xs text-sky-600 hover:text-sky-700 font-semibold flex items-center gap-1">
                <span>✨</span> Take / Sync from Article Content
              </button>
            </div>
            <textarea v-model="articleForm.excerpt" class="form-input text-xs" rows="2" placeholder="Otomatis terisi dari ringkasan isi artikel..."></textarea>
            <p class="text-[11px] text-slate-400 mt-1">Otomatis diambil dari 160 karakter pertama isi artikel untuk meta deskripsi SEO Google dan sosial media (WhatsApp / Twitter / FB).</p>
          </div>

          <div class="flex justify-end gap-3 pt-3 border-t border-sky-100">
            <button type="button" @click="showArticleModal = false" class="btn-secondary text-sm">Batal</button>
            <button type="submit" class="btn-primary text-sm px-5" :disabled="savingArticle">
              {{ savingArticle ? 'Menyimpan...' : 'Simpan Artikel' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- GALLERY ALBUM MODAL -->
    <div v-if="showGalleryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-sky-100 pb-3">
          <h3 class="font-bold text-slate-900 text-lg">Tambahkan Album Gallery Baru</h3>
          <button @click="showGalleryModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form @submit.prevent="uploadGallery" class="space-y-4">
          <div>
            <label class="form-label">Nama Album / Judul Galeri *</label>
            <input v-model="galleryForm.title" type="text" class="form-input" required placeholder="Misal: Dokumentasi Umroh Syawal 2025" />
          </div>
          <div>
            <label class="form-label">Kategori / Lokasi</label>
            <input v-model="galleryForm.category" type="text" class="form-input" placeholder="Dokumentasi, Makkah, Madinah, Ziarah" />
          </div>
          <div>
            <label class="form-label mb-2">Pilih Foto Album (Bisa Pilih Banyak Foto Sekaligus) *</label>
            <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-sky-200 hover:border-sky-400 rounded-xl cursor-pointer bg-sky-50/50 hover:bg-sky-100/60 transition-all text-center p-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xl">
                  🖼️
                </div>
                <div class="text-left">
                  <p class="text-xs font-bold text-sky-900">Klik / Drag Foto Album ke sini</p>
                  <p class="text-[11px] text-slate-400">Dapat memilih banyak foto sekaligus (Multi-select)</p>
                </div>
              </div>
              <input @change="handleMultipleGalleryFiles" type="file" accept="image/*" multiple class="hidden" />
            </label>
            <p v-if="galleryFiles.length > 0" class="text-xs text-emerald-600 font-semibold mt-2 flex items-center gap-1">
              <span>✅</span> {{ galleryFiles.length }} foto terpilih untuk diupload ke album ini.
            </p>
          </div>

          <!-- Thumbnail Previews -->
          <div v-if="galleryPreviews.length > 0" class="grid grid-cols-4 gap-2 pt-2 border-t border-slate-100 max-h-40 overflow-y-auto">
            <div v-for="(src, idx) in galleryPreviews" :key="idx" class="aspect-square bg-slate-100 rounded-lg overflow-hidden border border-slate-200">
              <img :src="src" class="w-full h-full object-cover" />
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-3 border-t border-sky-100">
            <button type="button" @click="showGalleryModal = false" class="btn-secondary text-sm">Batal</button>
            <button type="submit" class="btn-primary text-sm px-5" :disabled="uploadingGallery">
              {{ uploadingGallery ? 'Uploading Album...' : 'Simpan Album Gallery' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ALBUM DETAIL MODAL -->
    <div v-if="showAlbumModal && activeAlbum" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl max-w-4xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        
        <!-- Header / Edit Title Mode -->
        <div class="border-b border-sky-100 pb-3 space-y-3">
          <div class="flex justify-between items-start">
            <div v-if="!isEditingAlbumMeta" class="space-y-1">
              <h3 class="font-bold text-slate-900 text-xl">{{ activeAlbum.title }}</h3>
              <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-2.5 py-0.5 bg-sky-100 text-sky-700 rounded-full">
                  {{ activeAlbum.category }}
                </span>
                <span class="text-xs text-slate-400 font-medium">• {{ activeAlbum.photos.length }} Foto dalam album</span>
              </div>
            </div>

            <!-- Inline Edit Album Form -->
            <div v-else class="flex-1 mr-4 space-y-2">
              <div class="grid sm:grid-cols-2 gap-2">
                <div>
                  <label class="text-[11px] font-bold text-slate-500 uppercase">Nama Album</label>
                  <input v-model="editAlbumForm.title" type="text" class="form-input text-xs" required />
                </div>
                <div>
                  <label class="text-[11px] font-bold text-slate-500 uppercase">Kategori / Lokasi</label>
                  <input v-model="editAlbumForm.category" type="text" class="form-input text-xs" />
                </div>
              </div>
              <div class="flex gap-2">
                <button @click="saveAlbumMeta" class="btn-primary text-xs py-1 px-3" :disabled="savingAlbumMeta">
                  {{ savingAlbumMeta ? 'Menyimpan...' : '💾 Simpan Perubahan' }}
                </button>
                <button @click="isEditingAlbumMeta = false" class="btn-secondary text-xs py-1 px-3">Batal</button>
              </div>
            </div>

            <!-- Action Buttons on Top Right -->
            <div class="flex items-center gap-2">
              <button v-if="!isEditingAlbumMeta" @click="startEditAlbumMeta(activeAlbum)" class="text-xs px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded-xl font-bold flex items-center gap-1 shadow-sm">
                ✏️ Edit Album
              </button>

              <!-- Upload More Photos Button -->
              <label class="text-xs px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold flex items-center gap-1 cursor-pointer shadow-md transition-all">
                <span>➕</span> {{ uploadingMorePhotos ? 'Uploading...' : 'Tambahkan Foto' }}
                <input type="file" @change="handleUploadMorePhotos" accept="image/*" multiple class="hidden" :disabled="uploadingMorePhotos" />
              </label>

              <button @click="showAlbumModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold ml-2">&times;</button>
            </div>
          </div>
        </div>

        <!-- Photos Grid inside Album with Shift / Reorder controls -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 py-2">
          <div v-for="(p, idx) in activeAlbum.photos" :key="p.id" class="card overflow-hidden group relative border border-slate-200">
            <div class="aspect-square bg-slate-100 overflow-hidden relative">
              <img :src="storageUrl(p.image_path)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              <div class="absolute top-2 left-2 bg-slate-900/70 text-white font-bold text-[10px] px-2 py-0.5 rounded-md">
                #{{ idx + 1 }}
              </div>
            </div>
            
            <!-- Delete Button -->
            <button @click="deleteSinglePhoto(p)" class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-lg text-xs shadow-md opacity-90 hover:opacity-100 font-bold" title="Hapus foto ini">
              🗑️
            </button>

            <!-- Reorder / Move Left / Right Buttons -->
            <div class="absolute bottom-2 left-2 right-2 flex justify-between items-center gap-1 opacity-90 sm:opacity-0 group-hover:opacity-100 transition-opacity bg-slate-900/80 backdrop-blur p-1 rounded-xl">
              <button 
                type="button" 
                @click="movePhotoInAlbum(activeAlbum, idx, -1)" 
                :disabled="idx === 0" 
                class="px-2.5 py-1 bg-white/20 hover:bg-white/40 disabled:opacity-20 text-white text-xs rounded-lg font-bold transition-colors"
                title="Geser Kiri / Ke Depan"
              >
                ⬅️
              </button>
              <span class="text-[10px] text-sky-200 font-bold">Urutan {{ idx + 1 }}</span>
              <button 
                type="button" 
                @click="movePhotoInAlbum(activeAlbum, idx, 1)" 
                :disabled="idx === activeAlbum.photos.length - 1" 
                class="px-2.5 py-1 bg-white/20 hover:bg-white/40 disabled:opacity-20 text-white text-xs rounded-lg font-bold transition-colors"
                title="Geser Kanan / Ke Belakang"
              >
                ➡️
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-sky-100">
          <button type="button" @click="deleteAlbumGroup(activeAlbum)" class="text-xs text-red-600 hover:underline font-semibold">
            🗑️ Hapus Seluruh Album Ini
          </button>
          <button type="button" @click="showAlbumModal = false" class="btn-secondary text-sm px-5">Tutup</button>
        </div>
      </div>
    </div>

    <!-- FAQ MODAL -->
    <div v-if="showFaqModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-sky-100 pb-3">
          <h3 class="font-bold text-slate-900 text-lg">{{ isEditFaq ? 'Edit FAQ' : 'Tambah FAQ Baru' }}</h3>
          <button @click="showFaqModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form @submit.prevent="saveFaq" class="space-y-4">
          <div>
            <label class="form-label">Pertanyaan *</label>
            <input v-model="faqForm.question" type="text" class="form-input" required placeholder="Apa saja syarat..." />
          </div>
          <div>
            <label class="form-label">Kategori</label>
            <input v-model="faqForm.category" type="text" class="form-input" placeholder="Pendaftaran, Dokumen, Fasilitas" />
          </div>
          <div>
            <label class="form-label">Jawaban *</label>
            <textarea v-model="faqForm.answer" class="form-input" rows="4" required placeholder="Tulis jawaban lengkap..."></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-3 border-t border-sky-100">
            <button type="button" @click="showFaqModal = false" class="btn-secondary text-sm">Batal</button>
            <button type="submit" class="btn-primary text-sm px-5" :disabled="savingFaq">
              {{ savingFaq ? 'Menyimpan...' : 'Simpan FAQ' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- TESTIMONIAL MODAL -->
    <div v-if="showTestimonialModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-sky-100 pb-3">
          <h3 class="font-bold text-slate-900 text-lg">
            {{ isEditTestimonial ? 'Edit Testimoni Jamaah' : 'Tambah Testimoni Jamaah Baru' }}
          </h3>
          <button @click="showTestimonialModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form @submit.prevent="saveTestimonial" class="space-y-4">
          <!-- Photo Profile Field -->
          <div>
            <label class="form-label mb-1.5">Foto Profil Jamaah (Optional)</label>
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-full bg-slate-100 border-2 border-sky-200 overflow-hidden flex-shrink-0 flex items-center justify-center shadow-sm relative">
                <img v-if="testimonialImagePreview || testimonialForm.photo" :src="testimonialImagePreview || storageUrl(testimonialForm.photo)" class="w-full h-full object-cover" />
                <span v-else class="text-2xl text-slate-400">👤</span>
              </div>
              <label class="flex-1 px-4 py-2.5 bg-sky-50 hover:bg-sky-100/80 border border-sky-200 rounded-xl cursor-pointer transition-colors text-xs font-semibold text-sky-700 flex items-center gap-2">
                <span>📷</span> Upload Foto Jamaah
                <input type="file" @change="handleTestimonialImage" accept="image/*" class="hidden" />
              </label>
            </div>
          </div>

          <div>
            <label class="form-label">Nama Jamaah *</label>
            <input v-model="testimonialForm.name" type="text" class="form-input" required placeholder="Hj. Fatimah Az-Zahra" />
          </div>
          <div>
            <label class="form-label">Nama Paket / Keterangan</label>
            <input v-model="testimonialForm.package_name" type="text" class="form-input" placeholder="Paket Umroh Reguler 9 Hari" />
          </div>
          <div>
            <label class="form-label">Rating (1 - 5 Bintang)</label>
            <select v-model.number="testimonialForm.rating" class="form-select">
              <option :value="5">⭐⭐⭐⭐⭐ (5 Bintang)</option>
              <option :value="4">⭐⭐⭐⭐ (4 Bintang)</option>
              <option :value="3">⭐⭐⭐ (3 Bintang)</option>
            </select>
          </div>
          <div>
            <label class="form-label">Ulasan / Testimoni *</label>
            <textarea v-model="testimonialForm.content" class="form-input" rows="3" required placeholder="Pengalaman ibadah umroh..."></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-3 border-t border-sky-100">
            <button type="button" @click="showTestimonialModal = false" class="btn-secondary text-sm">Batal</button>
            <button type="submit" class="btn-primary text-sm px-5" :disabled="savingTestimonial">
              {{ savingTestimonial ? 'Menyimpan...' : (isEditTestimonial ? '💾 Update Testimoni' : '➕ Simpan Testimoni') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import RichTextEditor from '@/components/RichTextEditor.vue'
import api from '@/plugins/axios'

const activeTab = ref('pages')
const tabs = [
  { id: 'pages', label: 'Halaman Landing', icon: '📝' },
  { id: 'articles', label: 'Artikel & Blog', icon: '📰' },
  { id: 'galleries', label: 'Galeri Foto', icon: '🖼️' },
  { id: 'faqs', label: 'FAQ', icon: '❓' },
  { id: 'testimonials', label: 'Testimoni', icon: '⭐' },
]

const storageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `/storage/${path.replace(/^\//, '')}`
}

const stripTags = (text) => {
  if (!text) return ''
  return text.replace(/<[^>]*>?/gm, '').trim()
}

// TAB 1: PAGES & HERO & EXCELLENCE
const selectedPageType = ref('hero')
const savingPage = ref(false)

const pageForm = ref({ title: '', content: '', meta_title: '', meta_description: '', status: 'active' })

const uploadingHeroImg = ref(false)

const heroForm = ref({
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

const excellenceForm = ref({
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

const contactForm = ref({
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
const savingContact = ref(false)

async function loadPageData() {
  try {
    const { data } = await api.get('/admin/cms/pages')
    const pages = data.data || []
    const existing = pages.find(p => p.type === selectedPageType.value)
    
    if (selectedPageType.value === 'hero') {
      if (existing && existing.content) {
        const parsed = typeof existing.content === 'string' ? JSON.parse(existing.content) : existing.content
        heroForm.value = { ...heroForm.value, ...parsed }
      }
    } else if (selectedPageType.value === 'excellence') {
      if (existing && existing.content) {
        const parsed = typeof existing.content === 'string' ? JSON.parse(existing.content) : existing.content
        excellenceForm.value = { ...excellenceForm.value, ...parsed }
      }
    } else if (selectedPageType.value === 'contact') {
      if (existing && existing.content) {
        const parsed = typeof existing.content === 'string' ? JSON.parse(existing.content) : existing.content
        contactForm.value = { ...contactForm.value, ...parsed, title: existing.title || parsed.title || contactForm.value.title }
      }
    } else {
      if (existing) {
        pageForm.value = { ...existing }
      } else {
        pageForm.value = {
          title: selectedPageType.value.toUpperCase() + ' Page',
          content: '',
          meta_title: '',
          meta_description: '',
          status: 'active'
        }
      }
    }
  } catch (e) {
    console.error(e)
  }
}

async function uploadHeroImageFile(e) {
  const file = e.target.files[0]
  if (!file) return
  uploadingHeroImg.value = true
  try {
    const formData = new FormData()
    formData.append('image', file)
    const { data } = await api.post('/admin/cms/hero-image', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    heroForm.value.hero_image = data.data.path
    alert('Gambar banner hero berhasil diupload! Klik "Simpan Banner Hero" untuk menerapkan ke halaman depan.')
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal upload gambar banner hero')
  } finally {
    uploadingHeroImg.value = false
  }
}

async function saveHero() {
  savingPage.value = true
  try {
    await api.put('/admin/cms/pages/hero', {
      title: 'Hero Banner Landing Page',
      content: heroForm.value,
      status: 'active'
    })
    alert('Banner Hero berhasil diperbarui!')
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menyimpan banner hero')
  } finally {
    savingPage.value = false
  }
}

function addExcellenceItem() {
  excellenceForm.value.items.push({
    icon: '✨',
    title: 'Keunggulan Baru',
    desc: 'Deskripsi keunggulan baru...'
  })
}

function removeExcellenceItem(index) {
  excellenceForm.value.items.splice(index, 1)
}

async function saveExcellence() {
  savingPage.value = true
  try {
    await api.put('/admin/cms/pages/excellence', {
      title: 'Mengapa Memilih Kami',
      content: excellenceForm.value,
      status: 'active'
    })
    alert('Pengaturan Seksi Keunggulan berhasil diperbarui!')
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menyimpan keunggulan')
  } finally {
    savingPage.value = false
  }
}

async function saveContactPage() {
  savingContact.value = true
  try {
    await api.put('/admin/cms/pages/contact', {
      title: contactForm.value.title,
      content: contactForm.value,
      status: 'active'
    })
    alert('Pengaturan Halaman Kontak berhasil diperbarui!')
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menyimpan pengaturan halaman kontak')
  } finally {
    savingContact.value = false
  }
}

async function savePage() {
  savingPage.value = true
  try {
    await api.put(`/admin/cms/pages/${selectedPageType.value}`, pageForm.value)
    alert('Halaman CMS berhasil diperbarui!')
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menyimpan halaman')
  } finally {
    savingPage.value = false
  }
}

// TAB 2: ARTICLES
const articles = ref([])
const loadingArticles = ref(false)
const showArticleModal = ref(false)
const isEditArticle = ref(false)
const articleEditId = ref(null)
const savingArticle = ref(false)
const articleFile = ref(null)
const articleImagePreview = ref(null)
const articleForm = ref({ title: '', category: '', excerpt: '', content: '', status: 'published', featured_image: '' })

function handleArticleFile(e) {
  const file = e.target.files[0]
  if (file) {
    articleFile.value = file
    articleImagePreview.value = URL.createObjectURL(file)
  }
}

function autoGenerateExcerpt() {
  if (!articleForm.value.content) return
  const plainText = articleForm.value.content.replace(/<[^>]*>?/gm, '').trim()
  if (plainText) {
    articleForm.value.excerpt = plainText.substring(0, 160) + (plainText.length > 160 ? '...' : '')
  }
}

watch(() => articleForm.value.content, (newContent) => {
  if (!articleForm.value.excerpt && newContent) {
    const plainText = newContent.replace(/<[^>]*>?/gm, '').trim()
    if (plainText) {
      articleForm.value.excerpt = plainText.substring(0, 160) + (plainText.length > 160 ? '...' : '')
    }
  }
})

async function fetchArticles() {
  loadingArticles.value = true
  try {
    const { data } = await api.get('/admin/cms/articles')
    articles.value = data.data.data || data.data
  } catch (e) {
    console.error(e)
  } finally {
    loadingArticles.value = false
  }
}

function openArticleModal(a) {
  articleFile.value = null
  articleImagePreview.value = null
  if (a) {
    isEditArticle.value = true
    articleEditId.value = a.id
    articleForm.value = {
      title: a.title,
      category: a.category || '',
      excerpt: a.excerpt || '',
      content: a.content || '',
      status: a.status || 'published',
      featured_image: a.featured_image || a.image_path || ''
    }
  } else {
    isEditArticle.value = false
    articleEditId.value = null
    articleForm.value = { title: '', category: '', excerpt: '', content: '', status: 'published', featured_image: '' }
  }
  showArticleModal.value = true
}

async function saveArticle() {
  savingArticle.value = true
  try {
    const formData = new FormData()
    formData.append('title', articleForm.value.title)
    formData.append('category', articleForm.value.category || '')
    formData.append('excerpt', articleForm.value.excerpt || '')
    formData.append('content', articleForm.value.content || '')
    formData.append('status', articleForm.value.status || 'published')
    
    if (articleFile.value) {
      formData.append('image', articleFile.value)
    }

    if (isEditArticle.value) {
      formData.append('_method', 'PUT')
      await api.post(`/admin/cms/articles/${articleEditId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await api.post('/admin/cms/articles', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }
    showArticleModal.value = false
    await fetchArticles()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menyimpan artikel')
  } finally {
    savingArticle.value = false
  }
}

async function deleteArticle(a) {
  if (!confirm(`Yakin menghapus artikel "${a.title}"?`)) return
  try {
    await api.delete(`/admin/cms/articles/${a.id}`)
    await fetchArticles()
  } catch (e) {
    alert('Gagal menghapus artikel')
  }
}

// TAB 3: GALLERIES & ALBUMS
const galleries = ref([])
const loadingGalleries = ref(false)
const showGalleryModal = ref(false)
const showAlbumModal = ref(false)
const activeAlbum = ref(null)
const uploadingGallery = ref(false)
const galleryFiles = ref([])
const galleryPreviews = ref([])
const galleryForm = ref({ title: '', category: '' })

const isEditingAlbumMeta = ref(false)
const editAlbumForm = ref({ old_title: '', title: '', category: '' })
const savingAlbumMeta = ref(false)
const uploadingMorePhotos = ref(false)

function startEditAlbumMeta(album) {
  editAlbumForm.value = {
    old_title: album.title,
    title: album.title,
    category: album.category || ''
  }
  isEditingAlbumMeta.value = true
}

async function saveAlbumMeta() {
  if (!editAlbumForm.value.title) return alert('Nama Album tidak boleh kosong')
  savingAlbumMeta.value = true
  try {
    await api.put('/admin/cms/galleries/update-album', editAlbumForm.value)
    if (activeAlbum.value) {
      activeAlbum.value.title = editAlbumForm.value.title
      activeAlbum.value.category = editAlbumForm.value.category
    }
    isEditingAlbumMeta.value = false
    await fetchGalleries()
    alert('Detail album berhasil diperbarui!')
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal mengubah detail album')
  } finally {
    savingAlbumMeta.value = false
  }
}

async function handleUploadMorePhotos(e) {
  const files = Array.from(e.target.files)
  if (!files || files.length === 0) return
  if (!activeAlbum.value) return

  uploadingMorePhotos.value = true
  try {
    const formData = new FormData()
    formData.append('title', activeAlbum.value.title)
    formData.append('category', activeAlbum.value.category || '')

    files.forEach(f => {
      formData.append('images[]', f)
    })

    const { data } = await api.post('/admin/cms/galleries/add-photos', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    await fetchGalleries()

    if (data.data && activeAlbum.value) {
      activeAlbum.value.photos.push(...data.data)
    }

    alert(`${files.length} foto baru berhasil ditambahkan ke album!`)
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menambahkan foto ke album')
  } finally {
    uploadingMorePhotos.value = false
  }
}

async function movePhotoInAlbum(album, currentIndex, direction) {
  const newIndex = currentIndex + direction
  if (newIndex < 0 || newIndex >= album.photos.length) return

  // Swap photos in local array
  const itemToMove = album.photos[currentIndex]
  album.photos.splice(currentIndex, 1)
  album.photos.splice(newIndex, 0, itemToMove)

  // Send updated orders to API
  const orders = album.photos.map((photo, idx) => ({
    id: photo.id,
    sort_order: idx + 1
  }))

  try {
    await api.post('/admin/cms/galleries/reorder', { orders })
    await fetchGalleries()
  } catch (e) {
    console.error('Gagal menggeser urutan foto:', e)
  }
}

const albums = computed(() => {
  const map = {}
  galleries.value.forEach(g => {
    const titleKey = (g.title || 'Galeri Foto').replace(/\s*\(\d+\)$/, '').trim()
    if (!map[titleKey]) {
      map[titleKey] = {
        title: titleKey,
        category: g.category || 'Dokumentasi',
        cover: g.image_path,
        photos: []
      }
    }
    map[titleKey].photos.push(g)
  })
  return Object.values(map)
})

function viewAlbumDetails(album) {
  activeAlbum.value = album
  showAlbumModal.value = true
}

async function deleteSinglePhoto(photo) {
  if (!confirm('Yakin menghapus foto ini dari album?')) return
  try {
    await api.delete(`/admin/cms/galleries/${photo.id}`)
    if (activeAlbum.value) {
      activeAlbum.value.photos = activeAlbum.value.photos.filter(p => p.id !== photo.id)
      if (activeAlbum.value.photos.length === 0) {
        showAlbumModal.value = false
      }
    }
    await fetchGalleries()
  } catch (e) {
    alert('Gagal menghapus foto')
  }
}

async function deleteAlbumGroup(album) {
  if (!confirm(`Yakin menghapus SELURUH Album "${album.title}" (${album.photos.length} foto)?`)) return
  try {
    await api.post('/admin/cms/galleries/destroy-album', { title: album.title })
    showAlbumModal.value = false
    await fetchGalleries()
    alert('Album galeri berhasil dihapus!')
  } catch (e) {
    alert('Gagal menghapus album')
  }
}

async function fetchGalleries() {
  loadingGalleries.value = true
  try {
    const { data } = await api.get('/admin/cms/galleries')
    galleries.value = data.data.data || data.data
  } catch (e) {
    console.error(e)
  } finally {
    loadingGalleries.value = false
  }
}

function handleMultipleGalleryFiles(e) {
  const files = Array.from(e.target.files)
  galleryFiles.value = files
  galleryPreviews.value = files.map(file => URL.createObjectURL(file))
}

async function uploadGallery() {
  if (!galleryFiles.value || galleryFiles.value.length === 0) {
    return alert('Pilih minimal 1 file gambar untuk diupload ke album')
  }
  uploadingGallery.value = true
  try {
    const formData = new FormData()
    formData.append('title', galleryForm.value.title)
    formData.append('category', galleryForm.value.category || '')

    galleryFiles.value.forEach(file => {
      formData.append('images[]', file)
    })

    await api.post('/admin/cms/galleries', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    showGalleryModal.value = false
    galleryForm.value = { title: '', category: '' }
    galleryFiles.value = []
    galleryPreviews.value = []
    await fetchGalleries()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal upload album galeri')
  } finally {
    uploadingGallery.value = false
  }
}

// TAB 4: FAQS
const faqs = ref([])
const loadingFaqs = ref(false)
const showFaqModal = ref(false)
const isEditFaq = ref(false)
const faqEditId = ref(null)
const savingFaq = ref(false)
const faqForm = ref({ question: '', category: '', answer: '' })

async function fetchFaqs() {
  loadingFaqs.value = true
  try {
    const { data } = await api.get('/admin/cms/faqs')
    faqs.value = data.data.data || data.data
  } catch (e) {
    console.error(e)
  } finally {
    loadingFaqs.value = false
  }
}

function openFaqModal(f) {
  if (f) {
    isEditFaq.value = true
    faqEditId.value = f.id
    faqForm.value = { question: f.question, category: f.category || '', answer: f.answer || '' }
  } else {
    isEditFaq.value = false
    faqEditId.value = null
    faqForm.value = { question: '', category: '', answer: '' }
  }
  showFaqModal.value = true
}

async function saveFaq() {
  savingFaq.value = true
  try {
    if (isEditFaq.value) {
      await api.put(`/admin/cms/faqs/${faqEditId.value}`, faqForm.value)
    } else {
      await api.post('/admin/cms/faqs', faqForm.value)
    }
    showFaqModal.value = false
    await fetchFaqs()
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menyimpan FAQ')
  } finally {
    savingFaq.value = false
  }
}

async function deleteFaq(f) {
  if (!confirm('Yakin menghapus FAQ ini?')) return
  try {
    await api.delete(`/admin/cms/faqs/${f.id}`)
    await fetchFaqs()
  } catch (e) {
    alert('Gagal menghapus FAQ')
  }
}

// TAB 5: TESTIMONIALS
const testimonials = ref([])
const loadingTestimonials = ref(false)
const showTestimonialModal = ref(false)
const isEditTestimonial = ref(false)
const testimonialEditId = ref(null)
const savingTestimonial = ref(false)
const testimonialForm = ref({ name: '', package_name: '', rating: 5, content: '', photo: '' })
const testimonialImageFile = ref(null)
const testimonialImagePreview = ref('')

function openAddTestimonialModal() {
  isEditTestimonial.value = false
  testimonialEditId.value = null
  testimonialForm.value = { name: '', package_name: '', rating: 5, content: '', photo: '' }
  testimonialImageFile.value = null
  testimonialImagePreview.value = ''
  showTestimonialModal.value = true
}

function openEditTestimonialModal(t) {
  isEditTestimonial.value = true
  testimonialEditId.value = t.id
  testimonialForm.value = {
    name: t.name || '',
    package_name: t.package_name || '',
    rating: t.rating || 5,
    content: t.content || '',
    photo: t.photo || ''
  }
  testimonialImageFile.value = null
  testimonialImagePreview.value = ''
  showTestimonialModal.value = true
}

function handleTestimonialImage(e) {
  const file = e.target.files[0]
  if (file) {
    testimonialImageFile.value = file
    testimonialImagePreview.value = URL.createObjectURL(file)
  }
}

async function fetchTestimonials() {
  loadingTestimonials.value = true
  try {
    const { data } = await api.get('/admin/cms/testimonials')
    testimonials.value = data.data.data || data.data
  } catch (e) {
    console.error(e)
  } finally {
    loadingTestimonials.value = false
  }
}

async function saveTestimonial() {
  savingTestimonial.value = true
  try {
    const formData = new FormData()
    formData.append('name', testimonialForm.value.name)
    formData.append('package_name', testimonialForm.value.package_name || '')
    formData.append('rating', testimonialForm.value.rating)
    formData.append('content', testimonialForm.value.content)
    
    if (testimonialForm.value.photo) {
      formData.append('photo', testimonialForm.value.photo)
    }
    if (testimonialImageFile.value) {
      formData.append('image', testimonialImageFile.value)
    }

    if (isEditTestimonial.value) {
      await api.post(`/admin/cms/testimonials/${testimonialEditId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await api.post('/admin/cms/testimonials', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    showTestimonialModal.value = false
    await fetchTestimonials()
    alert(isEditTestimonial.value ? 'Testimoni berhasil diperbarui!' : 'Testimoni baru berhasil ditambahkan!')
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menyimpan testimoni')
  } finally {
    savingTestimonial.value = false
  }
}

async function deleteTestimonial(t) {
  if (!confirm(`Yakin menghapus testimoni dari "${t.name}"?`)) return
  try {
    await api.delete(`/admin/cms/testimonials/${t.id}`)
    await fetchTestimonials()
  } catch (e) {
    alert('Gagal menghapus testimoni')
  }
}

watch(activeTab, (newTab) => {
  if (newTab === 'pages') loadPageData()
  else if (newTab === 'articles') fetchArticles()
  else if (newTab === 'galleries') fetchGalleries()
  else if (newTab === 'faqs') fetchFaqs()
  else if (newTab === 'testimonials') fetchTestimonials()
})

onMounted(() => {
  loadPageData()
})
</script>

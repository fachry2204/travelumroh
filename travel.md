# Prompt Antigravity - Sistem Informasi Travel Umroh

## 1. Tujuan Project
Buat aplikasi **Sistem Informasi Travel Umroh** lengkap dengan **Landing Page** dan **CMS Multi Role** menggunakan:

- Backend: **Laravel 11 REST API**
- Frontend: **Vue 3 + Vite**
- Database: **MySQL 8 InnoDB**
- Styling: **Tailwind CSS** dengan warna utama **biru muda**
- Auth: Laravel Sanctum
- Role & Permission: Spatie Laravel Permission
- File Storage: Laravel Storage local/S3 compatible
- Queue/Cache: Redis
- Tidak menggunakan SQLite
- Tidak menggunakan Prisma

Website landing page mengambil inspirasi dari website travel umroh modern seperti Tazkia Travel: hero section kuat, CTA konsultasi, paket umroh, informasi fasilitas, testimoni, galeri, artikel, dan FAQ.

---

## 2. Role Pengguna

### Admin
- Mengelola seluruh data sistem.
- Mengelola landing page/CMS.
- Mengelola paket umroh.
- Mengelola data jamaah.
- Validasi dokumen.
- Validasi pembayaran.
- Mengelola keberangkatan, manifest, rooming list.
- Mengatur komisi agen.
- Melihat laporan penjualan dan komisi.

### Member/Jamaah
- Login ke member area.
- Melengkapi profil dan data jamaah.
- Upload dokumen.
- Melihat status pembayaran.
- Melihat status dokumen.
- Melihat jadwal manasik dan itinerary.
- Mendapat notifikasi.

### Agen
- Mendapat link affiliate/referral unik.
- Melihat lead dan jamaah yang mendaftar melalui link affiliate.
- Melihat status pembayaran jamaah referral.
- Melihat komisi pending, approved, dan paid.
- Download materi promosi.

### Perwakilan
- Mengelola agen di wilayah/cabang.
- Melihat penjualan wilayah.
- Monitoring lead dan booking agen.
- Melihat laporan komisi wilayah.

---

## 3. Landing Page

Buat landing page dengan struktur:

1. Header
   - Logo
   - Menu: Beranda, Paket Umroh, Tentang Kami, Galeri, Artikel, FAQ, Kontak
   - Tombol: Konsultasi WhatsApp

2. Hero Section
   - Headline: “Perjalanan Umroh Nyaman, Aman, dan Terpercaya”
   - Subheadline: “Pilih paket umroh terbaik dengan layanan profesional dari pendaftaran sampai kepulangan.”
   - CTA: Lihat Paket, Konsultasi Sekarang
   - Background visual: jamaah/Ka'bah/ilustrasi travel umroh

3. Paket Umroh
   - Card paket berisi:
     - Nama paket
     - Tanggal berangkat
     - Durasi
     - Maskapai
     - Hotel Makkah
     - Hotel Madinah
     - Harga
     - Sisa seat
     - Tombol detail/daftar

4. Keunggulan
   - Pembimbing berpengalaman
   - Hotel nyaman
   - Jadwal jelas
   - Bantuan dokumen
   - Manasik
   - Pendampingan penuh

5. Legalitas & Kepercayaan
   - Izin resmi
   - Partner bank
   - Partner maskapai
   - Testimoni jamaah

6. Galeri
   - Foto keberangkatan
   - Dokumentasi manasik
   - Dokumentasi jamaah

7. Artikel & Edukasi
   - Panduan dokumen umroh
   - Tips persiapan umroh
   - Tata cara umroh

8. FAQ
   - Syarat daftar umroh
   - Cara pembayaran
   - Dokumen wajib
   - Jadwal keberangkatan

9. Footer
   - Alamat kantor
   - Kontak WhatsApp
   - Email
   - Sosial media
   - Link penting

---

## 4. Modul CMS Admin

### Dashboard
Card KPI:
- Total lead
- Total booking
- Total jamaah
- Sudah DP
- Sudah lunas
- Dokumen kurang
- Jamaah berangkat bulan ini
- Total komisi agen

### CMS Landing Page
- Slider/banner
- Paket unggulan
- Keunggulan
- Testimoni
- Galeri
- Artikel
- FAQ
- Kontak
- SEO meta title/description

### Paket Umroh
CRUD paket dengan field:
- Nama paket
- Kode paket
- Tanggal berangkat
- Tanggal pulang
- Durasi
- Maskapai
- Bandara keberangkatan
- Hotel Makkah
- Hotel Madinah
- Harga quad/triple/double
- DP minimum
- Kuota seat
- Sisa seat
- Fasilitas
- Tidak termasuk
- Itinerary harian
- Status aktif/nonaktif

### Booking & Order
- Nomor booking otomatis
- Sumber pendaftaran: direct, agen, perwakilan, manual admin
- Paket dipilih
- Jumlah jamaah
- Total tagihan
- Status booking: pending, DP, lunas, batal
- Status dokumen: belum lengkap, proses validasi, lengkap
- Status visa: belum diajukan, proses, terbit

### Data Jamaah SISKOPATUH-ready
Siapkan form data jamaah yang lengkap dan mudah disesuaikan dengan format resmi:

Identitas:
- Nama lengkap sesuai KTP
- NIK
- Nomor KK
- Tempat lahir
- Tanggal lahir
- Jenis kelamin
- Status pernikahan
- Pekerjaan
- Pendidikan
- Alamat lengkap
- Provinsi
- Kota/kabupaten
- Kecamatan
- Kelurahan
- Kode pos
- No HP/WhatsApp
- Email

Paspor:
- Nomor paspor
- Tempat terbit paspor
- Tanggal terbit paspor
- Tanggal expired paspor
- Nama sesuai paspor

Keluarga & Kontak Darurat:
- Nama kontak darurat
- Hubungan
- No HP kontak darurat
- Alamat kontak darurat

Kesehatan:
- Golongan darah
- Riwayat penyakit
- Alergi
- Kebutuhan khusus
- Vaksin/sertifikat kesehatan jika diperlukan

Keberangkatan:
- Paket
- Tanggal berangkat
- Tanggal pulang
- Pembimbing
- Hotel Makkah
- Hotel Madinah
- Tipe kamar
- Rooming list
- Bus/group

### Dokumen Jamaah
Upload file:
- KTP
- KK
- Paspor
- Foto 4x6
- Buku nikah/akta bila diperlukan
- Sertifikat vaksin/kesehatan bila diperlukan
- Dokumen mahram bila diperlukan
- Dokumen tambahan

Status dokumen:
- Belum upload
- Menunggu validasi
- Ditolak
- Valid

Jika ditolak, admin wajib mengisi catatan revisi.

### Pembayaran
- Invoice/Tagihan otomatis
- DP
- Pelunasan
- Upload bukti transfer
- Validasi pembayaran admin
- Kwitansi PDF
- Status: menunggu pembayaran, menunggu validasi, diterima, ditolak, refund

### Keberangkatan
- Grup keberangkatan
- Manifest
- Rooming list
- Jadwal manasik
- Itinerary
- Pembimbing
- Data penerbangan
- Export Excel/PDF

### Affiliate & Komisi
- Setiap agen memiliki kode referral dan link unik.
- Contoh: `/daftar?ref=AG001`
- Saat calon jamaah daftar melalui link tersebut, booking menyimpan `agent_id` dan `referral_code`.
- Komisi dapat berupa persen atau nominal tetap per paket.
- Komisi masuk status pending saat booking dibuat.
- Komisi menjadi approved setelah pembayaran valid.
- Komisi menjadi paid setelah admin menandai sudah dibayarkan.

---

## 5. Database Schema Awal

Gunakan migration Laravel untuk membuat tabel berikut.

### users
- id
- name
- email
- password
- phone
- role_type: admin/member/agent/representative
- status: active/inactive/suspended
- created_at
- updated_at

### profiles
- id
- user_id
- avatar
- address
- province
- city
- district
- village
- postal_code
- identity_number
- created_at
- updated_at

### agents
- id
- user_id
- representative_id nullable
- agent_code unique
- referral_code unique
- commission_type: percentage/fixed
- commission_value decimal
- status: active/inactive
- created_at
- updated_at

### representatives
- id
- user_id
- region_name
- office_address
- status
- created_at
- updated_at

### packages
- id
- code unique
- name
- slug
- departure_date
- return_date
- duration_days
- airline
- departure_airport
- makkah_hotel
- madinah_hotel
- price_quad
- price_triple
- price_double
- minimum_dp
- quota
- remaining_seat
- facilities text
- excluded text
- description text
- status active/inactive
- created_at
- updated_at

### package_itineraries
- id
- package_id
- day_number
- title
- description
- created_at
- updated_at

### bookings
- id
- booking_number unique
- user_id nullable
- package_id
- agent_id nullable
- representative_id nullable
- referral_code nullable
- source: direct/agent/representative/admin
- total_pilgrims
- room_type: quad/triple/double
- total_amount
- paid_amount
- outstanding_amount
- booking_status: pending/dp/paid/cancelled
- document_status: incomplete/review/complete
- visa_status: not_submitted/process/issued
- created_at
- updated_at

### pilgrims
- id
- booking_id
- user_id nullable
- full_name
- nik
- family_card_number
- birth_place
- birth_date
- gender
- marital_status
- job
- education
- address
- province
- city
- district
- village
- postal_code
- phone
- email
- passport_number
- passport_issued_place
- passport_issued_date
- passport_expired_date
- passport_name
- blood_type
- medical_history text nullable
- allergy text nullable
- special_needs text nullable
- emergency_contact_name
- emergency_contact_relation
- emergency_contact_phone
- emergency_contact_address
- room_type
- room_number nullable
- bus_number nullable
- created_at
- updated_at

### pilgrim_documents
- id
- pilgrim_id
- document_type: ktp/kk/passport/photo/marriage_book/birth_certificate/vaccine/mahram/other
- file_path
- status: pending/valid/rejected
- note nullable
- validated_by nullable
- validated_at nullable
- created_at
- updated_at

### payments
- id
- booking_id
- payment_number unique
- payment_type: dp/installment/final/refund
- amount
- method: bank_transfer/cash/payment_gateway
- proof_file nullable
- status: pending/review/approved/rejected/refunded
- validated_by nullable
- validated_at nullable
- note nullable
- created_at
- updated_at

### commissions
- id
- booking_id
- agent_id
- commission_type: percentage/fixed
- commission_value
- commission_amount
- status: pending/approved/paid/cancelled
- paid_at nullable
- created_at
- updated_at

### departures
- id
- package_id
- group_code
- departure_date
- return_date
- guide_name
- airline
- flight_number_departure
- flight_number_return
- status: preparation/ready/departed/returned/cancelled
- created_at
- updated_at

### departure_pilgrims
- id
- departure_id
- pilgrim_id
- room_number
- bus_number
- manifest_status
- created_at
- updated_at

### cms_pages
- id
- type: hero/about/faq/contact/seo
- title
- content longtext
- image_path nullable
- sort_order
- status active/inactive
- created_at
- updated_at

### articles
- id
- title
- slug
- excerpt
- content longtext
- featured_image
- status draft/published
- published_at nullable
- created_at
- updated_at

### galleries
- id
- title
- image_path
- caption nullable
- sort_order
- status active/inactive
- created_at
- updated_at

### notifications
- id
- user_id nullable
- title
- message
- channel: app/email/whatsapp
- status: pending/sent/failed/read
- created_at
- updated_at

### audit_logs
- id
- user_id nullable
- action
- table_name
- record_id
- old_values json nullable
- new_values json nullable
- ip_address nullable
- user_agent nullable
- created_at

---

## 6. API Endpoint Awal

### Public
- GET `/api/public/packages`
- GET `/api/public/packages/{slug}`
- POST `/api/public/register-booking`
- POST `/api/public/contact-lead`
- GET `/api/public/articles`
- GET `/api/public/articles/{slug}`
- GET `/api/public/galleries`
- GET `/api/public/faqs`

### Auth
- POST `/api/auth/login`
- POST `/api/auth/register`
- POST `/api/auth/logout`
- GET `/api/auth/me`

### Member
- GET `/api/member/dashboard`
- GET `/api/member/bookings`
- GET `/api/member/bookings/{id}`
- PUT `/api/member/pilgrims/{id}`
- POST `/api/member/pilgrims/{id}/documents`
- POST `/api/member/payments/{bookingId}/upload-proof`

### Agen
- GET `/api/agent/dashboard`
- GET `/api/agent/referral-link`
- GET `/api/agent/leads`
- GET `/api/agent/bookings`
- GET `/api/agent/commissions`

### Perwakilan
- GET `/api/representative/dashboard`
- GET `/api/representative/agents`
- GET `/api/representative/bookings`
- GET `/api/representative/commissions`

### Admin
- GET `/api/admin/dashboard`
- CRUD `/api/admin/packages`
- CRUD `/api/admin/bookings`
- CRUD `/api/admin/pilgrims`
- CRUD `/api/admin/documents`
- CRUD `/api/admin/payments`
- CRUD `/api/admin/agents`
- CRUD `/api/admin/representatives`
- CRUD `/api/admin/commissions`
- CRUD `/api/admin/departures`
- CRUD `/api/admin/cms-pages`
- CRUD `/api/admin/articles`
- CRUD `/api/admin/galleries`
- GET `/api/admin/reports/sales`
- GET `/api/admin/reports/commissions`
- GET `/api/admin/reports/manifest`

---

## 7. Frontend Pages Vue

### Public
- `/`
- `/paket-umroh`
- `/paket-umroh/:slug`
- `/daftar/:packageSlug?`
- `/artikel`
- `/artikel/:slug`
- `/galeri`
- `/faq`
- `/kontak`

### Auth
- `/login`
- `/register`
- `/forgot-password`

### Member
- `/member/dashboard`
- `/member/booking`
- `/member/booking/:id`
- `/member/data-jamaah`
- `/member/dokumen`
- `/member/pembayaran`
- `/member/jadwal-manasik`
- `/member/itinerary`

### Agen
- `/agen/dashboard`
- `/agen/referral`
- `/agen/leads`
- `/agen/booking`
- `/agen/komisi`
- `/agen/materi-promosi`

### Perwakilan
- `/perwakilan/dashboard`
- `/perwakilan/agen`
- `/perwakilan/booking`
- `/perwakilan/komisi`
- `/perwakilan/laporan`

### Admin
- `/admin/dashboard`
- `/admin/cms`
- `/admin/paket`
- `/admin/booking`
- `/admin/jamaah`
- `/admin/dokumen`
- `/admin/pembayaran`
- `/admin/agen`
- `/admin/perwakilan`
- `/admin/komisi`
- `/admin/keberangkatan`
- `/admin/laporan`
- `/admin/setting`

---

## 8. UI/UX Style

Gunakan tema:
- Primary: `#38BDF8` biru muda
- Primary dark: `#075985`
- Background: `#F0F9FF`
- Accent gold: `#FBBF24`
- Success: `#10B981`
- Text dark: `#0F172A`

Style:
- Clean, modern, islami, profesional.
- Gunakan card besar dengan border lembut.
- Button rounded.
- Form multi-step untuk pendaftaran jamaah.
- Data table dengan filter status, search, pagination.
- Dashboard memakai KPI card dan chart sederhana.

---

## 9. Fitur Penting MVP

Bangun dulu:
1. Auth login/register multi-role.
2. Landing page dinamis dari CMS.
3. CRUD paket umroh.
4. Booking paket dari landing page.
5. Member area untuk data jamaah.
6. Upload dokumen jamaah.
7. Pembayaran manual dan upload bukti transfer.
8. Validasi admin.
9. Affiliate link agen.
10. Perhitungan komisi otomatis.
11. Dashboard admin, member, agen, perwakilan.
12. Export invoice/kwitansi/manifest PDF.

---

## 10. Roadmap Pengembangan

### Fase 1 - MVP
- Landing page
- Auth multi-role
- Paket umroh
- Booking
- Data jamaah
- Upload dokumen
- Pembayaran manual
- Dashboard admin/member

### Fase 2 - Affiliate
- Link referral agen
- Komisi otomatis
- Dashboard agen
- Dashboard perwakilan
- Laporan komisi

### Fase 3 - Otomasi
- Notifikasi WhatsApp/email
- Reminder pembayaran
- Export PDF/Excel
- Audit log
- Queue Redis

### Fase 4 - Scale
- Payment gateway
- PWA
- Integrasi eksternal
- Backup otomatis
- Analytics
- Security hardening

---

## 11. Instruksi Khusus untuk Antigravity

Buat project dengan struktur terpisah:

```bash
/backend   = Laravel 11 API
/frontend  = Vue 3 + Vite
```

Backend wajib memakai:
- Laravel migration
- Laravel Sanctum
- MySQL connection
- Spatie Permission
- Form Request validation
- API Resource response
- Policy authorization
- Seeder role awal: admin, member, agent, representative

Frontend wajib memakai:
- Vue 3 Composition API
- Pinia store
- Vue Router
- Axios API client
- Tailwind CSS
- Layout public, auth, admin, member, agen, perwakilan
- Reusable components: Button, Input, Select, Modal, DataTable, StatusBadge, FileUpload, PackageCard

Jangan generate Prisma.
Jangan pakai SQLite.
Gunakan MySQL sebagai database utama.

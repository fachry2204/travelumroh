<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Agent;
use App\Models\Representative;
use App\Models\Package;
use App\Models\PackageItinerary;
use App\Models\Article;
use App\Models\Gallery;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\CmsPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = ['admin', 'member', 'agent', 'representative'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Create Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@travelumroh.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'role_type' => 'admin',
            'status' => 'active',
        ]);
        $admin->assignRole('admin');
        Profile::create(['user_id' => $admin->id]);

        // Create Member
        $member = User::create([
            'name' => 'Budi Santoso',
            'email' => 'member@travelumroh.com',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'role_type' => 'member',
            'status' => 'active',
        ]);
        $member->assignRole('member');
        Profile::create(['user_id' => $member->id]);

        // Create Representative
        $repUser = User::create([
            'name' => 'Ahmad Perwakilan',
            'email' => 'perwakilan@travelumroh.com',
            'password' => Hash::make('password'),
            'phone' => '081234567892',
            'role_type' => 'representative',
            'status' => 'active',
        ]);
        $repUser->assignRole('representative');
        $representative = Representative::create([
            'user_id' => $repUser->id,
            'region_name' => 'Jawa Barat',
            'office_address' => 'Jl. Sudirman No. 100, Bandung',
            'status' => 'active',
        ]);
        Profile::create(['user_id' => $repUser->id]);

        // Create Agent
        $agentUser = User::create([
            'name' => 'Siti Agen',
            'email' => 'agen@travelumroh.com',
            'password' => Hash::make('password'),
            'phone' => '081234567893',
            'role_type' => 'agent',
            'status' => 'active',
        ]);
        $agentUser->assignRole('agent');
        Agent::create([
            'user_id' => $agentUser->id,
            'representative_id' => $representative->id,
            'agent_code' => 'AG001',
            'referral_code' => 'REF001',
            'commission_type' => 'percentage',
            'commission_value' => 5.00,
            'status' => 'active',
        ]);
        Profile::create(['user_id' => $agentUser->id]);

        // Create Packages
        $package1 = Package::create([
            'code' => 'UMR-2025-01',
            'name' => 'Paket Umroh Reguler 9 Hari',
            'slug' => 'paket-umroh-reguler-9-hari-umr-2025-01',
            'departure_date' => '2025-03-15',
            'return_date' => '2025-03-24',
            'duration_days' => 9,
            'airline' => 'Garuda Indonesia',
            'departure_airport' => 'Soekarno-Hatta International Airport (CGK)',
            'makkah_hotel' => 'Pullman ZamZam Makkah',
            'madinah_hotel' => 'Movenpick Hotel Madinah',
            'price_quad' => 28000000,
            'price_triple' => 32000000,
            'price_double' => 38000000,
            'minimum_dp' => 5000000,
            'quota' => 40,
            'remaining_seat' => 28,
            'facilities' => 'Tiket pesawat PP, Visa Umroh, Hotel bintang 5 Makkah & Madinah, Konsumsi 3x sehari, Bus AC, Manasik pra keberangkatan, Pembimbing ibadah berpengalaman, Perlengkapan umroh (koper, baju ihram, tas), Asuransi perjalanan',
            'excluded' => 'Biaya paspor, Pengeluaran pribadi, Oleh-oleh, Ziarah tambahan di luar paket',
            'description' => 'Paket Umroh Reguler 9 Hari adalah pilihan terbaik untuk Anda yang ingin menjalankan ibadah umroh dengan nyaman dan khusyuk. Didampingi pembimbing berpengalaman, hotel bintang 5, dan layanan penuh dari keberangkatan hingga kepulangan.',
            'status' => 'active',
        ]);

        // Itineraries for package 1
        $itineraries = [
            ['day_number' => 1, 'title' => 'Keberangkatan ke Madinah', 'description' => 'Berkumpul di bandara, check-in, penerbangan menuju Madinah. Setibanya, langsung menuju hotel dan istirahat.'],
            ['day_number' => 2, 'title' => 'Ziarah Madinah', 'description' => 'Shalat Arbain di Masjid Nabawi, ziarah ke Raudhah, Makam Rasulullah, Masjid Quba, Masjid Qiblatain, Jabal Uhud.'],
            ['day_number' => 3, 'title' => 'Madinah - Ibadah Bebas', 'description' => 'Ibadah bebas di Masjid Nabawi. Waktu untuk shalat, tadarus, dan berdoa.'],
            ['day_number' => 4, 'title' => 'Madinah ke Makkah', 'description' => 'Perjalanan dari Madinah ke Makkah, mengambil miqat di Bir Ali, niat ihram umroh.'],
            ['day_number' => 5, 'title' => 'Umroh - Tawaf & Sa\'i', 'description' => 'Tiba di Makkah, langsung menuju Masjidil Haram. Tawaf, Sa\'i, dan Tahallul (bercukur). Umroh selesai!'],
            ['day_number' => 6, 'title' => 'Ibadah di Makkah', 'description' => 'Ibadah bebas di Masjidil Haram. Shalat 5 waktu berjamaah, tawaf sunnah, tadarus Al-Quran.'],
            ['day_number' => 7, 'title' => 'Ziarah Makkah', 'description' => 'Ziarah ke Jabal Nur (Gua Hira), Jabal Tsur, Padang Arafah, Mina, dan Muzdalifah.'],
            ['day_number' => 8, 'title' => 'Tawaf Wada & Persiapan Pulang', 'description' => 'Tawaf Wada (tawaf perpisahan), belanja oleh-oleh, persiapan kembali ke tanah air.'],
            ['day_number' => 9, 'title' => 'Kembali ke Indonesia', 'description' => 'Penerbangan pulang menuju Jakarta. Tiba di tanah air, penjemputan oleh keluarga.'],
        ];
        foreach ($itineraries as $itinerary) {
            PackageItinerary::create(array_merge(['package_id' => $package1->id], $itinerary));
        }

        $package2 = Package::create([
            'code' => 'UMR-2025-02',
            'name' => 'Paket Umroh Plus Turki 14 Hari',
            'slug' => 'paket-umroh-plus-turki-14-hari-umr-2025-02',
            'departure_date' => '2025-04-10',
            'return_date' => '2025-04-24',
            'duration_days' => 14,
            'airline' => 'Turkish Airlines',
            'departure_airport' => 'Soekarno-Hatta International Airport (CGK)',
            'makkah_hotel' => 'Hilton Makkah Convention Hotel',
            'madinah_hotel' => 'Shaza Madinah Hotel',
            'price_quad' => 45000000,
            'price_triple' => 52000000,
            'price_double' => 62000000,
            'minimum_dp' => 10000000,
            'quota' => 30,
            'remaining_seat' => 18,
            'facilities' => 'Tiket pesawat PP via Istanbul, Visa Umroh & Turki, Hotel bintang 5, Konsumsi 3x sehari, Tour Istanbul 3 hari, Bus AC, Manasik, Pembimbing berpengalaman, Perlengkapan umroh, Asuransi perjalanan',
            'excluded' => 'Biaya paspor, Pengeluaran pribadi, Oleh-oleh',
            'description' => 'Paket Umroh Plus Turki memberikan pengalaman spiritual sekaligus wisata ke Istanbul yang indah. Nikmati keindahan Hagia Sophia, Grand Bazaar, dan kota bersejarah Istanbul sebelum menjalankan ibadah umroh.',
            'status' => 'active',
        ]);

        $package3 = Package::create([
            'code' => 'UMR-2025-03',
            'name' => 'Paket Umroh Ramadhan 12 Hari',
            'slug' => 'paket-umroh-ramadhan-12-hari-umr-2025-03',
            'departure_date' => '2025-03-25',
            'return_date' => '2025-04-06',
            'duration_days' => 12,
            'airline' => 'Saudia Airlines',
            'departure_airport' => 'Soekarno-Hatta International Airport (CGK)',
            'makkah_hotel' => 'Le Meridien Towers Makkah',
            'madinah_hotel' => 'Al Madinah Harmony Hotel',
            'price_quad' => 38000000,
            'price_triple' => 44000000,
            'price_double' => 52000000,
            'minimum_dp' => 8000000,
            'quota' => 35,
            'remaining_seat' => 12,
            'facilities' => 'Tiket pesawat PP, Visa Umroh, Hotel bintang 4-5, Konsumsi 3x + sahur & buka puasa, Bus AC, Manasik Ramadhan, Pembimbing berpengalaman, Perlengkapan umroh, Asuransi',
            'excluded' => 'Biaya paspor, Pengeluaran pribadi, Oleh-oleh',
            'description' => 'Rasakan keistimewaan ibadah umroh di bulan Ramadhan yang penuh berkah. Shalat tarawih di Masjidil Haram, berbuka bersama jutaan jamaah, dan mengejar malam Lailatul Qadar di tanah suci.',
            'status' => 'active',
        ]);

        // Articles
        Article::create([
            'title' => 'Panduan Lengkap Dokumen Umroh 2025',
            'slug' => 'panduan-lengkap-dokumen-umroh-2025',
            'excerpt' => 'Persiapan dokumen adalah langkah awal yang krusial sebelum berangkat umroh. Simak panduan lengkapnya di sini.',
            'content' => '<h2>Dokumen Wajib Umroh 2025</h2><p>Sebelum mendaftar umroh, pastikan Anda telah mempersiapkan dokumen-dokumen berikut dengan baik:</p><h3>1. Paspor</h3><p>Paspor harus berlaku minimal 6 bulan dari tanggal keberangkatan. Jika paspor Anda hampir habis masa berlakunya, segera perpanjang.</p><h3>2. Kartu Tanda Penduduk (KTP)</h3><p>KTP diperlukan untuk proses verifikasi identitas dan pembuatan visa.</p><h3>3. Kartu Keluarga (KK)</h3><p>KK dibutuhkan untuk menunjukkan hubungan mahram bagi jamaah wanita.</p><h3>4. Foto Terbaru</h3><p>Foto ukuran 4x6 dengan latar belakang putih, berpakaian berwarna (tidak putih), wajah terlihat 80% dari frame.</p><h3>5. Buku Nikah</h3><p>Untuk jamaah wanita yang berangkat bersama suami atau anak.</p>',
            'category' => 'Panduan',
            'status' => 'published',
            'published_at' => now()->subDays(5),
        ]);

        Article::create([
            'title' => 'Tips Persiapan Fisik dan Mental Sebelum Umroh',
            'slug' => 'tips-persiapan-fisik-dan-mental-sebelum-umroh',
            'excerpt' => 'Kondisi fisik dan mental yang prima sangat penting untuk menjalani ibadah umroh dengan khusyuk. Berikut tips persiapannya.',
            'content' => '<h2>Persiapan Fisik</h2><p>Ibadah umroh membutuhkan stamina yang baik karena melibatkan banyak aktivitas fisik seperti tawaf dan sa\'i. Berikut persiapan fisik yang perlu dilakukan:</p><h3>Olahraga Rutin</h3><p>Mulailah berolahraga minimal 3 bulan sebelum keberangkatan. Fokus pada latihan jalan kaki minimal 30 menit per hari untuk melatih stamina.</p><h3>Pemeriksaan Kesehatan</h3><p>Lakukan medical check-up lengkap dan konsultasikan kondisi kesehatan dengan dokter. Pastikan vaksinasi meningitis sudah dilakukan.</p>',
            'category' => 'Tips',
            'status' => 'published',
            'published_at' => now()->subDays(10),
        ]);

        Article::create([
            'title' => 'Tata Cara Umroh Lengkap untuk Pemula',
            'slug' => 'tata-cara-umroh-lengkap-untuk-pemula',
            'excerpt' => 'Bagi yang baru pertama kali umroh, pelajari tata cara pelaksanaan ibadah umroh secara lengkap dan benar.',
            'content' => '<h2>Tata Cara Pelaksanaan Umroh</h2><p>Umroh terdiri dari 4 rukun yang harus dilaksanakan secara berurutan:</p><h3>1. Ihram</h3><p>Ihram adalah niat untuk memulai ibadah umroh. Sebelum ihram, lakukan mandi sunnah, memakai pakaian ihram (untuk laki-laki: 2 helai kain putih tidak berjahit), dan shalat sunnah ihram 2 rakaat.</p><h3>2. Tawaf</h3><p>Tawaf adalah mengelilingi Ka\'bah sebanyak 7 kali putaran dengan arah berlawanan jarum jam. Dimulai dan diakhiri di garis lurus dari Hajar Aswad.</p><h3>3. Sa\'i</h3><p>Sa\'i adalah berlari-lari kecil antara bukit Shafa dan Marwah sebanyak 7 kali perjalanan.</p><h3>4. Tahallul</h3><p>Tahallul adalah memotong rambut. Bagi laki-laki minimal 3 helai rambut atau dicukur habis. Bagi wanita minimal 3 helai rambut.</p>',
            'category' => 'Panduan',
            'status' => 'published',
            'published_at' => now()->subDays(15),
        ]);

        // Galleries
        $galleryItems = [
            ['title' => 'Masjidil Haram', 'image_path' => 'galleries/masjidil-haram.jpg', 'caption' => 'Keindahan Masjidil Haram, kiblat seluruh umat Islam dunia', 'category' => 'Makkah', 'sort_order' => 1],
            ['title' => 'Masjid Nabawi', 'image_path' => 'galleries/masjid-nabawi.jpg', 'caption' => 'Masjid Nabawi di Madinah, masjid Rasulullah SAW', 'category' => 'Madinah', 'sort_order' => 2],
            ['title' => 'Ka\'bah', 'image_path' => 'galleries/kabah.jpg', 'caption' => 'Ka\'bah, pusat kiblat umat Islam seluruh dunia', 'category' => 'Makkah', 'sort_order' => 3],
            ['title' => 'Manasik Umroh', 'image_path' => 'galleries/manasik.jpg', 'caption' => 'Kegiatan manasik umroh sebelum keberangkatan', 'category' => 'Kegiatan', 'sort_order' => 4],
            ['title' => 'Jamaah Berangkat', 'image_path' => 'galleries/jamaah-berangkat.jpg', 'caption' => 'Momen keberangkatan jamaah umroh dengan penuh haru', 'category' => 'Kegiatan', 'sort_order' => 5],
            ['title' => 'Hotel Makkah', 'image_path' => 'galleries/hotel-makkah.jpg', 'caption' => 'Hotel bintang 5 di Makkah dekat Masjidil Haram', 'category' => 'Fasilitas', 'sort_order' => 6],
        ];
        foreach ($galleryItems as $item) {
            Gallery::create(array_merge($item, ['status' => 'active']));
        }

        // FAQs
        $faqs = [
            ['question' => 'Apa saja syarat untuk mendaftar umroh?', 'answer' => 'Syarat mendaftar umroh meliputi: (1) Beragama Islam, (2) Memiliki paspor yang masih berlaku minimal 6 bulan, (3) Memiliki KTP dan KK, (4) Sehat jasmani dan rohani, (5) Untuk wanita di bawah 45 tahun harus ada mahram atau bergabung dengan rombongan resmi.', 'category' => 'Pendaftaran', 'sort_order' => 1],
            ['question' => 'Bagaimana cara melakukan pembayaran?', 'answer' => 'Pembayaran dapat dilakukan melalui transfer bank ke rekening resmi Travel Umroh kami. Minimal pembayaran DP adalah sebagaimana tertera pada paket yang dipilih. Setelah transfer, upload bukti pembayaran melalui member area dan tunggu konfirmasi dari admin kami.', 'category' => 'Pembayaran', 'sort_order' => 2],
            ['question' => 'Dokumen apa saja yang harus diupload?', 'answer' => 'Dokumen yang harus diupload: (1) Scan KTP, (2) Scan Kartu Keluarga, (3) Scan Paspor (semua halaman terisi), (4) Foto 4x6 berlatar putih, (5) Buku nikah/akta lahir (jika diperlukan), (6) Sertifikat vaksin meningitis.', 'category' => 'Dokumen', 'sort_order' => 3],
            ['question' => 'Kapan jadwal keberangkatan tersedia?', 'answer' => 'Jadwal keberangkatan tersedia sepanjang tahun. Kami memiliki paket umroh reguler, paket Ramadhan, dan paket plus wisata. Lihat halaman Paket Umroh untuk melihat jadwal terbaru dan ketersediaan seat.', 'category' => 'Keberangkatan', 'sort_order' => 4],
            ['question' => 'Apakah bisa cicil pembayaran umroh?', 'answer' => 'Ya, kami menyediakan fasilitas cicilan pembayaran. Anda dapat membayar DP minimal terlebih dahulu, kemudian melunasi sisanya secara bertahap sebelum tanggal keberangkatan. Hubungi kami untuk informasi lebih lanjut.', 'category' => 'Pembayaran', 'sort_order' => 5],
            ['question' => 'Apakah ada pembimbing selama umroh?', 'answer' => 'Ya, setiap rombongan akan didampingi oleh pembimbing umroh yang berpengalaman dan bersertifikat. Pembimbing kami hafal Al-Quran, memahami fikih ibadah, dan berpengalaman membimbing di Makkah dan Madinah.', 'category' => 'Fasilitas', 'sort_order' => 6],
        ];
        foreach ($faqs as $faq) {
            Faq::create(array_merge($faq, ['status' => 'active']));
        }

        // Testimonials
        $testimonials = [
            ['name' => 'Hj. Fatimah Zahra', 'package_name' => 'Paket Umroh Reguler 9 Hari', 'content' => 'Alhamdulillah, ibadah umroh kami berjalan dengan sangat lancar. Pelayanan Travel Umroh sangat memuaskan, pembimbing sangat sabar dan berpengalaman. Hotel sangat dekat dengan Masjidil Haram. Rekomendasikan kepada semua yang ingin umroh!', 'rating' => 5, 'sort_order' => 1],
            ['name' => 'H. Ahmad Budiman', 'package_name' => 'Paket Umroh Ramadhan 12 Hari', 'content' => 'Umroh Ramadhan bersama Travel Umroh adalah pengalaman spiritual terbaik dalam hidup saya. Bisa tarawih di Masjidil Haram dan shalat malam di Ka\'bah. Terima kasih atas pelayanan yang luar biasa!', 'rating' => 5, 'sort_order' => 2],
            ['name' => 'Ibu Dewi Rahayu', 'package_name' => 'Paket Umroh Plus Turki 14 Hari', 'content' => 'Paket umroh plus Turki sangat berkesan. Bisa ibadah umroh sekaligus wisata ke Istanbul yang indah. Organisasi rombongan sangat baik, makanan enak, hotel nyaman. Pasti akan umroh lagi bersama Travel Umroh!', 'rating' => 5, 'sort_order' => 3],
        ];
        foreach ($testimonials as $testimonial) {
            Testimonial::create(array_merge($testimonial, ['status' => 'active']));
        }

        // CMS Pages
        CmsPage::create([
            'type' => 'hero',
            'title' => 'Perjalanan Umroh Nyaman, Aman, dan Terpercaya',
            'content' => json_encode([
                'headline' => 'Perjalanan Umroh Nyaman, Aman, dan Terpercaya',
                'subheadline' => 'Pilih paket umroh terbaik dengan layanan profesional dari pendaftaran sampai kepulangan.',
                'cta_primary' => 'Lihat Paket',
                'cta_secondary' => 'Konsultasi Sekarang',
                'whatsapp' => '6281234567890',
            ]),
            'sort_order' => 1,
            'status' => 'active',
        ]);

        CmsPage::create([
            'type' => 'contact',
            'title' => 'Hubungi Kami',
            'content' => json_encode([
                'address' => 'Jl. Raya Umroh No. 123, Jakarta Selatan 12345',
                'phone' => '021-12345678',
                'whatsapp' => '6281234567890',
                'email' => 'info@travelumroh.com',
                'instagram' => '@travelumroh',
                'facebook' => 'Travel Umroh Indonesia',
                'youtube' => 'Travel Umroh TV',
                'maps_embed' => 'https://www.google.com/maps/embed?pb=...',
            ]),
            'sort_order' => 9,
            'status' => 'active',
        ]);

        CmsPage::create([
            'type' => 'seo',
            'title' => 'SEO Configuration',
            'content' => json_encode([]),
            'meta_title' => 'Travel Umroh - Paket Umroh Terpercaya & Profesional',
            'meta_description' => 'Travel Umroh Indonesia menyediakan paket umroh terbaik dengan harga terjangkau. Layanan profesional, pembimbing berpengalaman, hotel bintang 5. Daftar sekarang!',
            'sort_order' => 0,
            'status' => 'active',
        ]);
    }
}

# Product Requirements Document (PRD)

## Portal RPJMD Kabupaten Pasuruan

| Atribut | Nilai |
|---|---|
| Status dokumen | Draf v1.0 — hasil rekonstruksi dari aplikasi berjalan |
| Tanggal penyusunan | 14 September 2026 |
| Pemilik produk | Bapperida Kabupaten Pasuruan |
| Platform | Aplikasi Portal RPJMD Kabupaten Pasuruan |
| Bahasa utama | Bahasa Indonesia |
| Sumber analisis | Kode aplikasi, skema basis data, route, controller, view, dan README repositori |

> Dokumen ini mendeskripsikan kondisi produk yang terlihat dari kode saat ini sekaligus target minimum agar produk layak dioperasikan. Butir berlabel **Gap** belum sepenuhnya tersambung atau memerlukan konfirmasi.

> **Pembaruan implementasi 19 September 2026:** Modul Berita, Galeri, dan Aspirasi/Kontak telah dihentikan secara permanen. Route, controller, model, view, seed data, aset unggahan, dan tabel databasenya tidak lagi menjadi bagian dari aplikasi aktif. Bagian PRD historis yang masih menyebut modul tersebut perlu dibaca sebagai catatan lama, bukan kontrak fitur saat ini.

## 1. Ringkasan produk

Portal RPJMD Kabupaten Pasuruan adalah pusat informasi internal dan terbatas bagi pengguna/warga terdaftar untuk menemukan informasi perencanaan pembangunan daerah, membaca berita, mengakses dokumen resmi, melihat galeri kegiatan, menyampaikan aspirasi, dan bertanya kepada asisten virtual berbasis dokumen RPJMD. **Seluruh fitur dan halaman portal memerlukan login pengguna sebelum dapat diakses.**

Di sisi internal, portal menyediakan dashboard bagi Admin dan Super Admin untuk mengelola konten, dokumen, kategori, galeri, aspirasi, basis pengetahuan chatbot, konfigurasi AI, dan akun pengelola.

## 2. Latar belakang dan masalah

Informasi RPJMD cenderung tersebar dalam dokumen panjang dan sulit ditelusuri oleh warga. Publik juga membutuhkan kanal resmi yang mudah digunakan untuk mengikuti perkembangan pembangunan dan menyampaikan pertanyaan atau aspirasi. Pada saat yang sama, pengelola memerlukan satu tempat untuk memperbarui konten tanpa mengubah kode aplikasi.

Masalah utama yang diselesaikan:

1. Dokumen pembangunan sulit ditemukan dan dipahami oleh masyarakat umum.
2. Berita, profil instansi, galeri, serta dokumen publik memerlukan kanal resmi yang terpusat.
3. Pertanyaan berulang tentang RPJMD membebani layanan informasi manual.
4. Pengelolaan konten dan akun memerlukan pembagian kewenangan yang jelas.
5. Aspirasi warga perlu diterima dan ditandai status tindak lanjutnya.

## 3. Visi produk

Menjadi portal resmi RPJMD Kabupaten Pasuruan yang transparan, mudah ditelusuri, inklusif, dan membantu warga memahami rencana pembangunan melalui informasi terstruktur serta asisten virtual yang bersumber dari dokumen resmi.

## 4. Tujuan

### 4.1 Tujuan pengguna

- Warga/Pengguna harus melakukan login terdaftar terlebih dahulu untuk mengakses portal.
- Pengguna yang telah login dapat menemukan dokumen RPJMD berdasarkan kategori dan tahun.
- Pengguna yang telah login dapat memahami isi dokumen melalui chatbot dalam Bahasa Indonesia atau Inggris.
- Pengguna yang telah login dapat memperoleh kabar pembangunan terbaru dari sumber resmi.
- Pengguna yang telah login dapat mengirim aspirasi atau pertanyaan kepada pengelola.
- Pengelola dapat memperbarui konten tanpa bantuan pengembang.

### 4.2 Tujuan organisasi

- Meningkatkan keterbukaan informasi perencanaan pembangunan.
- Mengurangi waktu pencarian dan permintaan informasi berulang.
- Menjaga konten portal tetap mutakhir dan dapat diaudit.
- Menyediakan dasar pengukuran penggunaan portal dan kualitas chatbot.

## 5. Sasaran keberhasilan

Target angka berikut merupakan baseline yang perlu disepakati setelah analitik produksi tersedia.

| Indikator | Target awal |
|---|---:|
| Ketersediaan portal bulanan | >= 99,5% |
| Keberhasilan halaman publik dimuat | >= 99% |
| Waktu muat LCP halaman utama pada jaringan seluler wajar | <= 2,5 detik (p75) |
| Pencarian dokumen yang menghasilkan klik buka/unduh | >= 60% |
| Pertanyaan chatbot yang mendapat respons tanpa error sistem | >= 95% |
| Feedback positif terhadap jawaban chatbot | >= 75% |
| Aspirasi yang ditandai selesai sesuai SLA internal | >= 90% |
| Konten berita yang dapat dibuat tanpa bantuan teknis | 100% |

## 6. Persona dan hak akses

| Persona | Kebutuhan utama | Hak akses |
|---|---|---|
| Pengunjung/Warga | Membaca informasi, mencari dokumen, bertanya, mengirim aspirasi | Halaman publik dan fitur chatbot tanpa login |
| Admin | Mengelola operasi konten sehari-hari | Dashboard, berita, galeri, dokumen, kategori, profil, statistik/capaian, aspirasi, ingest chatbot, dan setelan yang diizinkan |
| Super Admin | Mengatur portal dan pengelola | Seluruh hak Admin ditambah membuat, mengubah, dan menghapus akun pengelola |

Aturan akses:

- Route admin wajib memerlukan autentikasi dan peran `Admin` atau `Super Admin`.
- Manajemen pengguna hanya dapat dilakukan `Super Admin`.
- Pengguna tanpa hak akses dialihkan ke halaman publik dengan pesan penolakan.
- Super Admin tidak boleh menghapus akunnya sendiri atau Super Admin terakhir.
- Registrasi publik menghasilkan akun `User` dan mengarah ke halaman utama (`/`). Login akun `User` melalui `/login` atau `/client/login` selalu menuju halaman utama, termasuk jika sesi menyimpan tujuan dashboard. Admin dan Super Admin tetap menjalani verifikasi OTP melalui `/login` dan diarahkan ke dashboard admin. Route lama `/dashboard` mengalihkan berdasarkan peran; pengguna biasa tidak memiliki dashboard.

## 7. Ruang lingkup

### 7.1 Termasuk dalam rilis inti

- Beranda portal dan navigasi publik.
- Profil instansi.
- Daftar, pencarian, dan detail berita.
- Galeri kegiatan.
- Bank data/dokumen dengan kategori bertingkat dan filter tahun.
- Formulir aspirasi/kontak.
- Chatbot RPJMD berbasis retrieval-augmented generation (RAG).
- Riwayat, sesi baru, hapus riwayat, feedback, dan ekspor percakapan.
- Dashboard Admin/Super Admin.
- Manajemen berita, galeri, dokumen, kategori, profil, indikator/capaian, aspirasi, basis pengetahuan chatbot, setelan model AI, dan pengguna.
- Pencatatan aktivitas administratif.
- Sitemap dan metadata dasar SEO.

### 7.2 Di luar ruang lingkup rilis inti

- Aplikasi seluler native.
- Forum publik atau komentar berita.
- Workflow persetujuan konten berlapis.
- Integrasi pengaduan dengan SP4N-LAPOR atau sistem eksternal lain.
- Tanda tangan elektronik dokumen.
- Pembayaran atau transaksi keuangan.
- Chat langsung dengan petugas.

## 8. Alur pengguna utama

### 8.1 Mencari dokumen

1. Warga membuka halaman Dokumen.
2. Sistem menampilkan kategori tingkat akar.
3. Warga memilih kategori/subkategori dan, bila diperlukan, tahun.
4. Sistem menampilkan dokumen yang sesuai, urut dari tahun dan pembaruan terbaru.
5. Warga membuka atau mengunduh PDF.

### 8.2 Bertanya kepada chatbot

1. Warga membuka widget chatbot dan memilih bahasa.
2. Warga mengirim pertanyaan.
3. Sistem membentuk atau membaca ID sesi dari cookie.
4. Sistem membuat embedding pertanyaan, mengambil potongan dokumen paling relevan, menambahkan data berita terbaru, lalu meminta jawaban ke Gemini.
5. Sistem menampilkan jawaban serta menyimpan percakapan.
6. Warga dapat memberi feedback, memulai sesi baru, menghapus riwayat, atau mengekspor chat ke PDF/TXT.

### 8.3 Menerbitkan berita

1. Admin masuk ke dashboard.
2. Admin mengisi judul, kategori, konten, gambar opsional, dan status publik/draf.
3. Sistem memvalidasi data, membuat slug unik, menyimpan penulis dan waktu terbit.
4. Berita publik tampil di daftar dan detail; berita draf tidak dapat diakses publik.
5. Aktivitas dicatat dalam log.

### 8.4 Memproses aspirasi

1. Warga mengirim nama, email, subjek, dan pesan.
2. Sistem menyimpan aspirasi dengan status awal `unread`.
3. Admin melihat dan memfilter aspirasi pada dashboard.
4. Admin menandai aspirasi selesai atau menghapusnya.
5. Perubahan dicatat dalam log aktivitas.

## 9. Kebutuhan fungsional

Prioritas menggunakan MoSCoW: Must (wajib), Should (penting), Could (opsional).

### 9.1 Portal publik

| ID | Prioritas | Kebutuhan |
|---|---|---|
| PUB-01 | Must | Sistem menyediakan beranda dengan identitas portal, ajakan membuka dokumen/profil, dan statistik dinamis. |
| PUB-02 | Must | Navigasi publik menyediakan akses ke Beranda, Profil, dan Dokumen; `/launching` dipertahankan sebagai halaman seremoni di luar navbar. |
| PUB-03 | Must | Halaman harus responsif pada desktop, tablet, dan ponsel. |
| PUB-04 | Must | Sistem hanya menampilkan halaman publik Beranda, Profil, dan Dokumen pada navigasi utama; data berita/galeri tetap dikelola melalui Admin. |
| PUB-05 | Must | Sistem menyediakan favicon, judul halaman, dan meta description untuk halaman publik yang masih tersedia. |
| PUB-06 | Should | Sistem menampilkan keadaan kosong dan pesan kesalahan yang mudah dipahami ketika data tidak tersedia. |
| PUB-07 | Should | Seluruh kontrol interaktif dapat digunakan dengan keyboard dan memiliki label aksesibel. |

### 9.2 Profil instansi

| ID | Prioritas | Kebutuhan |
|---|---|---|
| PRO-01 | Must | Sistem menampilkan konten profil yang disimpan sebagai pasangan key, title, dan content. |
| PRO-02 | Must | Admin dapat memperbarui seluruh bagian profil dari dashboard. |
| PRO-03 | Should | Konten profil mendukung struktur seperti gambaran umum, visi, misi, tugas/fungsi, dan informasi organisasi sesuai kebutuhan instansi. |

### 9.3 Berita

| ID | Prioritas | Kebutuhan |
|---|---|---|
| NWS-01 | Must | Admin dapat melihat berita yang dikelola, diurutkan berdasarkan tanggal terbit terbaru, dengan paginasi. |
| NWS-02 | Must | Admin dapat mencari berita berdasarkan judul tanpa membedakan kapitalisasi. |
| NWS-03 | Must | Detail berita tidak dipublikasikan sebagai halaman publik; data dan status berita tetap tersedia untuk pengelolaan Admin. |
| NWS-04 | Must | Admin dapat membuat, mengubah, menghapus, serta mengalihkan status publik/draf berita. |
| NWS-05 | Must | Berita menyimpan penulis, judul, slug, kategori, isi, gambar opsional, waktu publikasi, dan status publikasi. |
| NWS-06 | Must | Gambar berita dibatasi pada format gambar yang disetujui dan ukuran maksimum 2 MB. |
| NWS-07 | Should | Penghapusan berita juga menghapus aset gambarnya bila aset dikelola lokal. |

### 9.4 Galeri

| ID | Prioritas | Kebutuhan |
|---|---|---|
| GAL-01 | Must | Admin dapat melihat galeri terbaru dengan paginasi. |
| GAL-02 | Must | Admin dapat membuat, mengubah, dan menghapus item galeri. |
| GAL-03 | Must | Item galeri memuat judul, gambar, lokasi opsional, deskripsi opsional, dan waktu pembuatan. |
| GAL-04 | Must | Unggahan menerima JPEG, PNG, JPG, atau WebP dengan ukuran maksimum 5 MB. |
| GAL-05 | Should | Saat gambar diganti atau item dihapus, file lama dibersihkan dari penyimpanan. |

### 9.5 Bank data dan dokumen publik

| ID | Prioritas | Kebutuhan |
|---|---|---|
| DOC-01 | Must | Pengunjung dapat menjelajah kategori dokumen bertingkat melalui breadcrumb. |
| DOC-02 | Must | Pengunjung dapat memfilter dokumen berdasarkan kategori aktif dan tahun. |
| DOC-03 | Must | Dokumen diurutkan berdasarkan tahun menurun lalu waktu pembuatan menurun dan ditampilkan dengan paginasi. |
| DOC-04 | Must | Sistem menampilkan waktu pembaruan dokumen terakhir. |
| DOC-05 | Must | Admin dapat membuat, mengubah, dan menghapus kategori beserta relasi induknya. |
| DOC-06 | Must | Nama dan slug kategori harus unik. |
| DOC-07 | Must | Kategori yang masih digunakan dokumen tidak boleh dihapus. |
| DOC-08 | Must | Admin dapat mengunggah satu atau banyak PDF ke sebuah kategori. |
| DOC-09 | Must | Ukuran maksimum setiap PDF publik adalah 250 MB. |
| DOC-10 | Must | Jika judul kosong atau unggahan terdiri dari banyak file, judul berasal dari nama file asli. |
| DOC-11 | Must | Admin dapat mengganti metadata dan file PDF suatu dokumen. |
| DOC-12 | Should | Sistem menjaga kompatibilitas kolom kategori lama selama migrasi data berlangsung. |
| DOC-13 | **Gap** | Tahun dokumen saat unggah masih otomatis memakai tahun berjalan; targetnya Admin dapat memilih tahun dokumen dan sistem memvalidasi rentangnya. |
| DOC-14 | **Gap** | Penghapusan/penggantian record dokumen harus memastikan file lama ikut dibersihkan dan URL penyimpanan bersifat portabel antar-environment. |

### 9.6 Aspirasi dan kontak

| ID | Prioritas | Kebutuhan |
|---|---|---|
| CON-01 | Must | Data aspirasi tetap dapat dikelola Admin; formulir Kontak tidak lagi menjadi halaman publik utama. |
| CON-02 | Must | Aspirasi baru mendapat status `unread`. |
| CON-03 | Must | Admin dapat melihat, mencari/memfilter berdasarkan status, menandai selesai, dan menghapus aspirasi. |
| CON-04 | Should | Sistem menampilkan notifikasi jumlah aspirasi belum selesai pada dashboard. |
| CON-05 | **Gap** | Tetapkan SLA, pemilik tindak lanjut, balasan email, dan kebijakan retensi data aspirasi. |
| CON-06 | **Gap** | Tambahkan perlindungan spam seperti rate limit, honeypot, atau CAPTCHA yang aksesibel. |

### 9.7 Chatbot RPJMD

| ID | Prioritas | Kebutuhan |
|---|---|---|
| BOT-01 | Must | Chatbot menerima pesan teks dan mendukung Bahasa Indonesia serta Inggris. |
| BOT-02 | Must | Endpoint chat dibatasi maksimum 20 permintaan per menit per identitas rate limiter. |
| BOT-03 | Must | Sistem membuat sesi anonim berbasis UUID, menyimpannya dalam cookie hingga 30 hari, serta menyimpan pesan pengguna/model. |
| BOT-04 | Must | Sistem menggunakan embedding pertanyaan untuk memilih maksimal 10 chunk dokumen terdekat berdasarkan cosine similarity. |
| BOT-05 | Must | Konteks dokumen digunakan bila skor relevansi tertinggi melewati ambang 0,3. |
| BOT-06 | Must | Jawaban menyertakan konteks sumber file dan halaman ketika konteks dokumen tersedia. |
| BOT-07 | Should | Chatbot dapat memasukkan ringkasan tiga berita publik terbaru sebagai konteks tambahan. |
| BOT-08 | Must | Sistem menyimpan maksimum empat pasang percakapan terbaru sebagai memori prompt. |
| BOT-09 | Must | Pengguna dapat memuat hingga 50 pesan riwayat, memulai sesi baru, dan menghapus memori percakapan aktif. |
| BOT-10 | Should | Pengguna dapat memberi feedback suka/tidak suka pada jawaban. |
| BOT-11 | Should | Pengguna dapat mengekspor percakapan ke PDF atau TXT; ekspor dibatasi 10 permintaan per menit. |
| BOT-12 | Must | Sistem menangani API key hilang, timeout, rate limit, overload, model tidak ditemukan, serta kegagalan internal dengan pesan yang aman dan mudah dipahami. |
| BOT-13 | Must | Admin dan Super Admin memilih provider generation Google Gemini, OpenAI (GPT), atau Anthropic (Claude), lalu model dari whitelist provider melalui Setelan. Test Model terisolasi tidak mengaktifkan kandidat; Simpan atomik beserta audit memperbarui pasangan provider/model dan berlaku pada request berikutnya. Embedding tetap Gemini dan korpus existing tidak di-ingest ulang. Gemini sudah diimplementasikan; multi-provider masih rancangan, termasuk transisi `ai_model` netral, credential server per provider, readiness, error dan rollback. Lihat [PRD AI Model Management bagian 22–32](PRD-AI-MODEL-MANAGEMENT.md#22-tujuan-dan-cakupan-fase-dua) dan [hasil/rencana UAT](UAT-AI-MODEL-MANAGEMENT.md). |
| BOT-14 | Must | Respons chatbot dan Test Model tidak mengirim detail exception internal atau body error provider. Jalur ini menggunakan pesan aman; audit log/error ingest lama tetap pekerjaan hardening terpisah. |
| BOT-15 | **Gap** | Feedback harus disimpan terstruktur dan terhubung ke pesan; saat ini baru berupa log aplikasi. |
| BOT-16 | **Gap** | Sistem perlu kebijakan privasi, persetujuan cookie, anonimisasi IP, retensi chat, dan mekanisme penghapusan data. |
| BOT-17 | **Gap** | Validasi ekspor harus membatasi ukuran/jumlah pesan dan tidak mempercayai HTML dari klien. |

### 9.8 Ingest basis pengetahuan chatbot

| ID | Prioritas | Kebutuhan |
|---|---|---|
| ING-01 | Must | Admin dapat mengunggah PDF maksimal 50 MB untuk basis pengetahuan chatbot. |
| ING-02 | Must | Pemrosesan berjalan melalui antrean dengan status `pending`, `processing`, `completed`, `failed`, atau `cancelled`. |
| ING-03 | Must | Dashboard menampilkan halaman selesai/total, persentase progres, estimasi waktu, dan pesan error. |
| ING-04 | Must | Teks diekstrak per halaman, dibersihkan, dipecah sekitar 1.500 karakter, lalu dibuat embedding. |
| ING-05 | Must | Admin dapat membatalkan ingest yang belum selesai dan menghapus hasil ingest beserta chunk terkait. |
| ING-06 | Should | Kegagalan satu halaman tidak menghentikan halaman lain dan tercatat di log. |
| ING-07 | **Gap** | Satu sumber dokumen harus memiliki ID stabil; penghapusan chunk tidak boleh hanya bergantung pada nama file asli yang mungkin sama. |
| ING-08 | **Gap** | Dokumen hasil scan memerlukan OCR atau pesan eksplisit bahwa teks tidak dapat diekstrak. |
| ING-09 | **Gap** | Harus ada idempotensi/deduplication agar ingest ulang tidak menghasilkan chunk ganda. |

### 9.9 Capaian, statistik, dan layanan

| ID | Prioritas | Kebutuhan |
|---|---|---|
| STA-01 | Must | Admin dapat membuat dan menghapus statistik hero dengan key unik berawalan `hero_`, label, dan nilai. |
| STA-02 | Must | Admin dapat mengubah nilai statistik/capaian yang tersedia. |
| STA-03 | Must | Admin dapat mengelola sektor serta indikator kemajuan bernilai 0–100. |
| STA-04 | Should | Penghapusan sektor menghapus indikator turunannya sesuai aturan relasi. |
| STA-05 | **Gap** | Route publik khusus capaian tidak tersedia meskipun data sektor/indikator dan UI admin ada; tentukan apakah capaian ditampilkan di beranda atau halaman tersendiri. |
| STA-06 | **Gap** | Method CRUD layanan tersedia di controller, tetapi route admin-nya belum terdaftar. Tentukan apakah modul Layanan dipertahankan dan sambungkan route/UI yang diperlukan. |

### 9.10 Dashboard dan audit

Rencana pemisahan dashboard menjadi halaman berbasis route dijelaskan dalam [PRD Admin Multipage](PRD-ADMIN-MULTIPAGE.md). Pada baseline, ADM-02 masih menggunakan hash. Target pemisahan menggantinya dengan URL per halaman sambil mempertahankan kompatibilitas `#section-*`, fungsi, dan desain dashboard existing; target ini belum diimplementasikan.

| ID | Prioritas | Kebutuhan |
|---|---|---|
| ADM-01 | Must | Dashboard menampilkan ringkasan jumlah berita, aspirasi belum selesai, layanan, pengguna, dan sektor. |
| ADM-02 | Must | Navigasi dashboard memisahkan modul dan mempertahankan section aktif melalui hash URL. |
| ADM-03 | Must | Operasi penting membuat rekaman aktivitas berisi pengguna, tipe, aksi, deskripsi, dan waktu. |
| ADM-04 | Must | Semua operasi tulis menggunakan proteksi CSRF, validasi server, autentikasi, dan otorisasi peran. |
| ADM-05 | Should | Tabel besar menggunakan pagination/filter server-side agar dashboard tetap cepat. |
| ADM-06 | **Gap** | Dashboard saat ini memuat banyak koleksi dengan `get()`; pagination yang tampak pada antarmuka perlu dipastikan benar-benar efisien di server. |

### 9.11 Manajemen pengguna dan autentikasi

| ID | Prioritas | Kebutuhan |
|---|---|---|
| USR-01 | Must | Pengguna dapat login, logout, meminta reset password, mengatur ulang password, dan memverifikasi email. |
| USR-02 | Must | Super Admin dapat membuat pengguna dengan nama, email unik, password minimal delapan karakter, dan peran `Admin`, `Super Admin`, atau `User`. Pilihan yang sama tersedia saat mengedit pengguna, dengan allowlist di server. Akun `User` hanya mengakses portal utama. |
| USR-03 | Must | Super Admin dapat mengubah identitas/peran serta mengganti password pengguna secara opsional. |
| USR-04 | Must | Super Admin dapat menghapus pengguna selain dirinya, dengan syarat minimal satu Super Admin tetap ada. |
| USR-05 | **Gap** | Nilai peran harus dibatasi melalui enum/allowlist (`Admin`, `Super Admin`, dan bila dibutuhkan `User`) pada seluruh endpoint. |
| USR-06 | Should | Aksi sensitif meminta konfirmasi dan dicatat dalam audit log. |

## 10. Aturan bisnis

1. Hanya berita `is_published = true` yang boleh tampil pada endpoint publik.
2. Slug berita dan kategori dokumen harus unik.
3. Kategori dokumen dapat memiliki induk; penghapusan induk mengikuti kebijakan relasi, tetapi kategori yang masih berisi dokumen harus ditolak.
4. Progres indikator harus berada pada rentang 0–100.
5. Cookie sesi chatbot bersifat anonim dan berlaku maksimal 30 hari, kecuali kebijakan privasi menetapkan periode lebih pendek.
6. Konteks chatbot berasal dari dokumen yang telah berhasil di-ingest; jawaban bukan keputusan resmi pemerintah.
7. Data input pengguna harus divalidasi di server dan output konten harus di-escape/sanitasi sesuai konteks.
8. Setiap operasi administratif material harus memiliki jejak audit.
9. File publik dan file basis pengetahuan adalah dua domain penyimpanan berbeda dan perlu siklus hidup masing-masing.

## 11. Model data konseptual

| Entitas | Fungsi | Relasi utama |
|---|---|---|
| User | Akun pengelola dan identitas penulis | memiliki banyak News dan Activity |
| News | Berita publik/draf | dimiliki User |
| Profile | Konten profil berbasis key | mandiri |
| Stat | Statistik hero/capaian dan konfigurasi model | mandiri |
| Service | Tautan layanan | mandiri |
| Sector | Kelompok capaian | memiliki banyak Indicator |
| Indicator | Progres capaian 0–100 | dimiliki Sector |
| Contact | Aspirasi/pesan warga | mandiri, status unread/resolved |
| Gallery | Foto kegiatan | mandiri |
| DocumentCategory | Taksonomi dokumen bertingkat | parent/children dan banyak PublicDocument |
| PublicDocument | Metadata dan URL PDF publik | dimiliki DocumentCategory |
| DocumentIngestion | Status pemrosesan PDF chatbot | secara logis memiliki banyak DocumentChunk |
| DocumentChunk | Teks halaman dan embedding | saat ini ditautkan lewat nama dokumen |
| ChatSession | Sesi percakapan anonim | memiliki banyak ChatMessage |
| ChatMessage | Pesan user/model | dimiliki ChatSession melalui session_id |
| ChatAnalytic | Agregat penggunaan chatbot | **Gap:** skema ada, integrasi belum tampak aktif |
| SharedMessage | Pesan yang dibagikan melalui token | **Gap:** skema ada, fitur/route belum tampak aktif |
| Activity | Audit aktivitas admin | dimiliki User |

## 12. Integrasi dan arsitektur

- Backend: PHP 8.2+, Laravel 11.
- Frontend: Blade, Tailwind CSS 4, Alpine.js, Vite.
- Basis data: kompatibel dengan konfigurasi Laravel; README mengarahkan penggunaan MySQL.
- AI: Google Gemini Generative Language API.
- Embedding: `gemini-embedding-001`.
- Model jawaban: `AiSettings` membaca key `gemini_model` dan `ai_provider` di `stats` tanpa cache lintas request. Default dan whitelist berada di `config/ai.php`: Gemini 2.5 Flash (default) dan Gemini 2.5 Pro. `ChatbotController` mempertahankan RAG/prompt/history, lalu `AIManager` memanggil `GeminiProvider` untuk generation. API key tetap server-only melalui `services.gemini.api_key`.
- Pemrosesan PDF: `smalot/pdfparser`.
- Ekspor PDF: `barryvdh/laravel-dompdf`.
- Background processing: Laravel Queue.
- Email: Laravel Mail untuk autentikasi/reset/verifikasi; konfigurasi environment wajib tersedia.

## 13. Kebutuhan nonfungsional

### 13.1 Keamanan dan privasi

- Seluruh trafik produksi wajib menggunakan HTTPS.
- Secret seperti `APP_KEY`, kredensial database, mail, dan `GEMINI_API_KEY` hanya disimpan di environment/secret manager dan tidak masuk repositori.
- Form dan endpoint mutasi wajib menggunakan CSRF, autentikasi, otorisasi, validasi MIME/ukuran, dan pembatasan laju yang sesuai.
- File unggahan harus diberi nama aman, tidak dapat dieksekusi, dan diverifikasi berdasarkan isi/MIME, bukan ekstensi saja.
- Konten berita/profil dan respons chatbot harus dirender dengan sanitasi yang mencegah XSS.
- Log tidak boleh menyimpan secret atau data pribadi berlebihan.
- Tetapkan privacy notice, tujuan pemrosesan, masa retensi, dan prosedur penghapusan untuk IP, email, aspirasi, cookie, serta riwayat chat.
- Tambahkan header keamanan minimum: CSP, HSTS, X-Content-Type-Options, Referrer-Policy, dan frame policy.

### 13.2 Kinerja dan skalabilitas

- Halaman publik menggunakan pagination dan query terindeks untuk slug, status publikasi, kategori, tahun, session_id, serta waktu.
- Aset gambar dioptimalkan dan menggunakan lazy loading bila tidak berada di viewport awal.
- Ingest PDF wajib berjalan di worker queue, tidak menggantung request web.
- Pencarian similarity di PHP dapat dipakai pada volume kecil; ketika chunk bertambah besar, gunakan vector database/extension dan pencarian top-k di basis data.
- Tetapkan batas concurrency, retry dengan exponential backoff/jitter, dan circuit breaker untuk API AI.

### 13.3 Keandalan dan observabilitas

- Error aplikasi dan job gagal dicatat dengan correlation ID tanpa membocorkan detail internal ke pengguna.
- Tersedia health check aplikasi, database, queue, storage, mail, dan konektivitas Gemini.
- Backup basis data dan dokumen dilakukan terjadwal serta diuji pemulihannya.
- Alert dibuat untuk lonjakan error 5xx, queue tertahan, ingest gagal, dan kegagalan API AI.
- Audit log memiliki kebijakan retensi dan hanya dapat diakses pihak berwenang.

### 13.4 Aksesibilitas dan kompatibilitas

- Target minimum WCAG 2.1 AA.
- Kontras warna, fokus keyboard, label form, pesan error, alt text, heading, dan landmark harus semantik.
- Mendukung dua versi terbaru Chrome, Edge, Firefox, Safari, serta browser seluler umum.
- Ukuran target sentuh minimum 44×44 px dan antarmuka tetap dapat digunakan pada lebar 320 px.

### 13.5 SEO

- Setiap halaman memiliki title dan description unik.
- Detail berita menggunakan canonical URL dan Open Graph yang valid.
- Sitemap hanya memuat URL publik yang dapat diindeks.
- Berita draf, admin, autentikasi, dan endpoint API tidak boleh diindeks.

## 14. Acceptance criteria lintas fitur

Rilis inti dianggap diterima apabila:

1. Pengunjung dapat membuka semua halaman publik tanpa autentikasi dan tidak dapat melihat berita draf.
2. Pencarian berita, filter kategori/tahun dokumen, breadcrumb, dan pagination bekerja pada kondisi data kosong maupun banyak data.
3. Form kontak menolak input wajib kosong/email tidak valid dan menyimpan input valid sebagai `unread`.
4. Chatbot membuat sesi, menyimpan riwayat, menggunakan konteks hasil ingest, menangani kegagalan provider, serta dapat mengekspor chat.
5. Admin tidak dapat mengakses fungsi Super Admin baik dari UI maupun dengan memanggil URL langsung.
6. Super Admin dapat mengelola akun tanpa bisa menghapus diri sendiri atau Super Admin terakhir.
7. CRUD berita, galeri, dokumen, kategori, profil, sektor/indikator, dan aspirasi berhasil serta menulis audit log.
8. Validasi tipe dan ukuran file diuji pada file valid, ekstensi palsu, file terlalu besar, serta nama file dengan karakter khusus.
9. Job ingest dapat selesai, gagal dengan aman, dibatalkan, dan dibersihkan tanpa meninggalkan chunk yatim.
10. Tidak ada secret dalam repositori/log, tidak ada detail exception pada respons produksi, dan pemeriksaan otorisasi mencakup seluruh route admin.
11. Test suite otomatis lulus dan smoke test produksi mencakup beranda, login admin, unggah dokumen, serta satu percakapan chatbot.

## 15. Analitik yang dibutuhkan

Event minimum, tanpa merekam isi sensitif secara default:

- `page_view` dengan jenis halaman.
- `news_search`, `news_open`.
- `document_category_open`, `document_filter_year`, `document_open`, `document_download`.
- `contact_submitted`, `contact_resolved`.
- `chat_session_started`, `chat_question_sent`, `chat_answer_succeeded`, `chat_answer_failed`, `chat_feedback`, `chat_exported`.
- `ingest_started`, `ingest_completed`, `ingest_failed`, `ingest_cancelled`.
- `admin_content_created`, `admin_content_updated`, `admin_content_deleted`.

Dashboard analitik minimal menampilkan jumlah sesi, jumlah pesan, rata-rata pesan per sesi, waktu respons, like/dislike, pertanyaan/topik populer yang telah dianonimkan, serta tren error.

## 16. Risiko dan mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Jawaban AI tidak akurat | Informasi publik menyesatkan | Grounding dokumen, kutipan sumber/halaman, disclaimer, feedback, evaluasi berkala |
| API Gemini lambat/tidak tersedia | Chatbot gagal | Timeout, retry terukur, antrean bila relevan, pesan fallback, monitoring |
| Pencarian similarity linear membesar | Respons lambat dan boros memori | Indeks/vector store, batching, cache, batas korpus |
| PDF scan tidak menghasilkan teks | Basis pengetahuan tidak lengkap | OCR dan laporan kualitas ingest |
| Unggahan berbahaya | Kompromi server/pengguna | MIME sniffing, storage aman, antivirus, nama acak, CSP |
| Data pribadi tersimpan terlalu lama | Risiko privasi/kepatuhan | Minimasi data, retensi, anonimisasi, kontrol akses, penghapusan |
| Hak akses salah konfigurasi | Perubahan konten tanpa izin | Policy/gate terpusat, allowlist role, test otorisasi |
| File dan record tidak sinkron | Storage membengkak/link rusak | Transaksi, lifecycle service, scheduled orphan cleanup |

## 17. Gap implementasi prioritas

### P0 — sebelum produksi

1. Hilangkan detail exception dari respons chatbot dan audit seluruh output sensitif.
2. Tegaskan kebijakan registrasi publik serta allowlist role pada backend.
3. Tambahkan privacy notice, kebijakan cookie/chat, retensi data, dan sanitasi konten.
4. Pastikan seluruh route, policy, dan UI konsisten untuk Admin versus Super Admin.
5. Uji penyimpanan/penghapusan file dan cegah file yatim atau executable upload.
6. Pastikan queue worker, retry, failed job, backup, dan monitoring tersedia di environment produksi.

### P1 — segera setelah stabilisasi

1. Tambahkan pilihan tahun pada unggah dokumen publik.
2. Hubungkan `DocumentIngestion` dan `DocumentChunk` dengan foreign key/ID stabil.
3. Simpan feedback chatbot secara terstruktur dan aktifkan agregasi `ChatAnalytic`.
4. Terapkan pagination/query server-side pada dashboard.
5. Putuskan dan selesaikan modul capaian serta layanan publik/admin.
6. Tambahkan OCR, deduplikasi ingest, dan evaluasi kualitas retrieval.

### P2 — pengembangan lanjutan

1. Fitur berbagi pesan bila entitas `SharedMessage` memang dibutuhkan.
2. Workflow persetujuan berita/dokumen.
3. Integrasi kanal pengaduan atau open data pemerintah.
4. Dashboard analitik konten dan kualitas chatbot yang lebih lengkap.

## 18. Strategi pengujian

- Unit test: scope berita publik, perhitungan similarity, progress ingest, rule role, dan helper model.
- Feature test: seluruh route publik/admin, validasi form, otorisasi, toggle berita, kategori bertingkat, upload, aspirasi, serta endpoint chatbot dengan provider yang di-mock.
- Integration test: database, storage, queue, mail, parser PDF, Gemini embedding/generation pada staging.
- Security test: CSRF, IDOR, stored/reflected XSS, MIME spoofing, upload abuse, rate limit, mass assignment, session/cookie flags.
- Accessibility test: keyboard-only, screen reader smoke test, kontras, zoom 200%, form error announcement.
- Performance test: halaman publik, daftar dokumen besar, dashboard besar, dan retrieval pada jumlah chunk target.
- UAT: skenario warga, Admin, dan Super Admin menggunakan data representatif RPJMD.

## 19. Rencana rilis

### Tahap 1 — hardening

- Tutup gap P0, tambah test otomatis, siapkan konfigurasi staging, backup, monitoring, serta dokumen privasi.

### Tahap 2 — UAT dan migrasi konten

- Validasi struktur kategori, tahun, profil, berita, dokumen, akun, dan korpus chatbot bersama Bapperida.
- Jalankan evaluasi pertanyaan chatbot menggunakan daftar tanya-jawab yang disetujui subject-matter expert.

### Tahap 3 — produksi

- Deploy dengan HTTPS, worker queue, scheduler, logging, dan alert aktif.
- Jalankan smoke test dan pantau error/kinerja secara intensif pada minggu pertama.

### Tahap 4 — optimasi

- Prioritaskan P1 berdasarkan data analitik, feedback warga, dan beban operasional.

## 20. Definition of Done

Sebuah kebutuhan dianggap selesai jika:

- Acceptance criteria terkait terpenuhi.
- Test otomatis relevan tersedia dan lulus.
- Otorisasi, validasi, error handling, logging, aksesibilitas, dan responsivitas telah ditinjau.
- Migrasi basis data dapat dijalankan dan di-rollback dengan aman.
- Dokumentasi operasi/configuration diperbarui tanpa memuat secret.
- Pemilik produk menyetujui hasil UAT.
- Tidak ada defect severity kritis/tinggi yang terbuka.

## 21. Pertanyaan terbuka untuk pemilik produk

1. Siapa pemilik produk, approver konten, dan penanggung jawab operasional resmi?
2. Apakah akun publik memang dibutuhkan, atau registrasi harus ditutup dan akun hanya dibuat Super Admin?
3. Apakah modul Layanan dan Capaian harus tampil publik? Jika ya, pada halaman mana dan data apa yang dianggap resmi?
4. Struktur kategori dokumen final maksimal berapa tingkat, dan siapa yang berwenang mengubahnya?
5. Tahun dokumen memakai tahun terbit, periode RPJMD, atau tahun unggah?
6. Apakah dokumen publik boleh langsung dipublikasikan Admin atau perlu approval?
7. Berapa SLA tindak lanjut aspirasi, siapa penerimanya, dan apakah warga mendapat nomor tiket/balasan email?
8. Berapa lama data chat, IP, cookie, aspirasi, audit log, dan file ingest disimpan?
9. Apakah jawaban chatbot wajib selalu menampilkan referensi nama dokumen, halaman, dan tautan sumber?
10. Model Gemini apa yang disetujui, berapa batas biaya bulanan, dan apa mekanisme fallback ketika kuota habis?
11. Apakah fitur berbagi chat dan analitik pertanyaan akan diaktifkan atau skemanya dihapus?
12. Standar infrastruktur produksi, domain resmi, email pengirim, serta kebijakan backup yang digunakan apa?

## 22. Asumsi penyusunan

- Nama dan pemilik portal diturunkan dari nama repositori, aset, dan isi aplikasi.
- PRD ini bersifat *as-built plus hardening*: fitur yang sudah tampak di kode dicatat sebagai baseline, sedangkan ketidaksambungan dicatat sebagai Gap.
- Tidak ada wawancara stakeholder, data trafik produksi, regulasi internal, desain final, atau lampiran brief khusus yang tersedia saat penyusunan.
- Target metrik dan kebijakan operasional belum final sampai dikonfirmasi pemilik produk.

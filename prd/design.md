# Panduan Desain Portal RPJMD Kabupaten Pasuruan

Tanggal: 14 September 2026  
Status: Rancangan panduan redesign  
Acuan visual: halaman login administrator pada working tree saat dokumen disusun.

## 1. Tujuan dan batas penggunaan

Dokumen ini menjadi pedoman perubahan tampilan Portal RPJMD agar halaman publik, autentikasi, dan dashboard memiliki identitas visual yang konsisten dengan halaman login administrator: resmi, tenang, bersih, dan mudah dibaca.

Redesign mencakup warna, tipografi, tata letak, komponen, responsivitas, dan feedback interaksi. Struktur informasi serta fungsi produk mengikuti [PRD utama](prd/PRD.md). Perubahan route, hak akses, autentikasi, kontrak data, atau fitur bisnis harus dibahas dan didokumentasikan tersendiri.

Spesifikasi yang disebut **acuan existing** diambil dari kode login. Spesifikasi bertanda **target redesign** merupakan usulan penerapan ke seluruh website, belum berarti sudah diimplementasikan atau diuji secara visual di browser.

### Sumber acuan

- [Tampilan login](resources/views/auth/login.blade.php).
- [Stylesheet login dan OTP](resources/css/admin-login.css).
- [Layout autentikasi](resources/views/layouts/guest.blade.php).
- [Stylesheet utama](resources/css/app.css).
- [PRD Login Admin](prd/PRD-LOGIN-ADMIN.md), [desain OTP](prd/PRD-OTP-ADMIN-DESIGN.md), dan [kontrak OTP](prd/PRD-OTP-EMAIL-ADMIN.md).

Jika dokumen desain lama mendeskripsikan tampilan sebelum perubahan login terbaru, gunakan kode login saat ini sebagai acuan visual. PRD tetap menjadi rujukan kebutuhan produk dan keamanan; perbedaan implementasi perlu dicatat, bukan dianggap otomatis terselesaikan oleh redesign.

## 2. Arah visual

Identitas utama menggunakan navy untuk area brand, putih untuk area kerja dan membaca, biru untuk tindakan utama, serta abu-abu kebiruan untuk informasi pendukung. Foto daerah menjadi konteks visual dengan overlay yang menjaga keterbacaan.

Prinsip penerapan:

- Tonjolkan identitas resmi Bapperida dan Kabupaten Pasuruan melalui aset yang tersedia.
- Gunakan judul yang jelas, paragraf singkat, dan ruang kosong yang cukup.
- Tetapkan satu tindakan paling menonjol dalam setiap kelompok tugas.
- Gunakan border halus dan bayangan ringan untuk memisahkan permukaan.
- Pakai dekorasi secara terbatas agar dokumen, berita, dan layanan mudah ditemukan.
- Pertahankan Bahasa Indonesia dan istilah navigasi yang sudah dikenal pengguna.

Layout dua kolom login berlaku untuk alur autentikasi. Halaman publik menggunakan struktur navigasi, konten, dan footer; dashboard menggunakan navigasi samping dan area kerja.

## 3. Acuan existing: halaman login

| Elemen | Spesifikasi dari implementasi |
|---|---|
| Layout desktop | Grid 55% panel brand dan 45% panel form; tinggi minimum `100svh` |
| Panel brand | Navy `#102e56`, teks putih, foto `hero.png` dengan opacity `0.16` dan overlay navy |
| Logo | `Logo Bapperida Kab Pasuruan Putih.png`; kotak 260 × 80 px, `object-fit: contain` |
| Font | Outfit dengan fallback sans-serif |
| Judul brand | `clamp(36px, 3.7vw, 56px)`, bobot 600, line-height 1.15 |
| Judul form | `clamp(28px, 2.4vw, 36px)`, bobot 600, line-height 1.2 |
| Lebar form | Maksimum 440 px |
| Input | Tinggi 52 px, radius 12 px, background `#f8fafc`, border `#94a3b8` |
| Tombol utama | Minimum tinggi 52 px, radius 12 px, background `#1d4ed8` |
| Jarak field | 22 px antar kelompok field |
| Fokus input | Outline biru 2 px dengan offset 2 px |
| Tablet dan mobile | Pada lebar sampai 1023 px menjadi satu kolom; foto, deskripsi brand, dan keterangan keamanan disembunyikan |
| Logo layar kecil | Kotak 220 × 64 px |
| Gerak | Transisi 150 ms; dinonaktifkan ketika `prefers-reduced-motion: reduce` |

Login menampilkan email, kata sandi, tautan lupa sandi, opsi ingat sesi, tombol “Lanjutkan”, dan tautan kembali ke portal. Desain turunan harus mempertahankan urutan login menuju verifikasi email dan tidak memberi kesan bahwa memasukkan kata sandi langsung membuka dashboard.

## 4. Target redesign: token desain

Nama token berikut merupakan usulan untuk implementasi bersama, bukan variabel yang sudah tersedia di stylesheet.

### Warna

| Token | Nilai | Penggunaan |
|---|---|---|
| `--color-brand` | `#102e56` | Hero, footer, sidebar, panel identitas |
| `--color-primary` | `#1d4ed8` | Tombol utama, tautan, kontrol aktif |
| `--color-primary-hover` | `#1e40af` | Hover tombol utama |
| `--color-primary-active` | `#1e3a8a` | Tombol ketika ditekan |
| `--color-focus` | `#2563eb` | Outline fokus pada permukaan terang |
| `--color-accent` | `#a5e2ed` | Ikon dan label pendukung pada navy |
| `--color-surface` | `#ffffff` | Kartu, form, area baca |
| `--color-background` | `#f8fafc` | Latar halaman dan input |
| `--color-text` | `#0f172a` | Judul dan teks utama |
| `--color-text-secondary` | `#475569` | Keterangan pendukung |
| `--color-text-muted` | `#64748b` | Metadata dan placeholder |
| `--color-border` | `#e2e8f0` | Pemisah dekoratif dan kartu; usulan perluasan |
| `--color-input-border` | `#94a3b8` | Batas kontrol form |
| `--color-success` / `--color-success-bg` | `#166534` / `#f0fdf4` | Feedback berhasil |
| `--color-danger` / `--color-danger-bg` | `#991b1b` / `#fef2f2` | Error dan konfirmasi destruktif |
| `--color-warning` / `--color-warning-bg` | `#92400e` / `#fffbeb` | Peringatan; usulan perluasan |

Warna cyan muda digunakan di atas navy, bukan sebagai teks kecil di atas putih. Status selalu disertai teks atau ikon bermakna. Pasangan warna harus diperiksa kontrasnya saat implementasi, terutama di atas foto dan pada state disabled.

### Tipografi

Gunakan `"Outfit", ui-sans-serif, system-ui, sans-serif` secara konsisten. Login sudah menggunakan Outfit, tetapi token `--font-sans` di `app.css` masih menunjuk Figtree; penyelarasan ini menjadi pekerjaan implementasi berikutnya.

| Peran | Desktop | Mobile | Bobot / line-height |
|---|---|---|---|
| Judul hero publik | 48–56 px | 32–36 px | 600 / 1.15 |
| Judul halaman | 32–36 px | 28–30 px | 600 / 1.2 |
| Judul bagian | 24–28 px | 22–24 px | 600 / 1.3 |
| Judul kartu | 18–20 px | 18–20 px | 600 / 1.4 |
| Isi utama | 16 px | 16 px | 400 / 1.7 |
| Label form | 14 px | 14 px | 500 / 1.5 |
| Metadata | 12–14 px | 12–14 px | 400–500 / 1.6 |
| Eyebrow | 12 px | 12 px | 600 / 1.5; uppercase, tracking 0.14em |

Batasi paragraf bacaan panjang sekitar 65–75 karakter per baris. Gunakan bobot 700 hanya ketika dibutuhkan untuk penekanan. Pertahankan ukuran input minimal 16 px pada ponsel.

### Spacing, radius, dan elevasi

- Skala jarak: 4, 8, 12, 16, 20, 24, 32, 40, 48, 64, dan 80 px.
- Container publik: maksimum 1200 px, rata tengah; padding horizontal 20 px pada mobile, 32 px pada tablet, dan 40 px pada desktop.
- Jarak antarbagian: 64–80 px desktop dan 40–48 px mobile.
- Padding kartu: 24 px desktop dan 20 px mobile.
- Radius: 12 px untuk tombol/input, 16 px untuk kartu, 20 px untuk modal; radius penuh untuk badge dan avatar.
- Bayangan kartu: `0 4px 16px rgba(15, 23, 42, 0.06)`; overlay dapat menggunakan bayangan lebih tegas.
- Form autentikasi tetap maksimum 440 px. Form panjang dapat memakai lebar lebih besar sesuai kebutuhan field.

## 5. Target redesign: komponen bersama

| Komponen | Aturan tampilan dan interaksi |
|---|---|
| Header publik | Permukaan putih, logo resmi yang sesuai latar terang, menu aktif biru, pemisah bawah halus; navigasi mobile melalui tombol berlabel |
| Heading halaman | Eyebrow opsional, satu H1, deskripsi singkat, breadcrumb bila ada hierarki |
| Tombol utama | Biru, teks putih, radius 12 px, tinggi 48–52 px; 52 px pada autentikasi |
| Tombol sekunder | Putih atau transparan dengan border dan teks biru; kontras disesuaikan dengan latar |
| Tombol destruktif | Merah, label eksplisit seperti “Hapus dokumen”, didahului konfirmasi |
| Input/select/textarea | Label tetap terlihat, border yang jelas, helper text dan error dekat field; textarea mengikuti panjang isi |
| Kartu berita | Foto rasio 16:9, kategori, tanggal, judul, ringkasan, tautan baca; hindari interaksi tautan bersarang |
| Item dokumen | Ikon PDF, nama, kategori/tahun, metadata yang tersedia, tindakan buka/unduh yang jelas |
| Badge | Teks ringkas; warna semantik untuk status, bukan satu-satunya pembeda |
| Pagination | Halaman aktif biru, tombol sebelumnya/berikutnya, filter tetap terbawa dalam URL |
| Alert | Teks jelas, latar semantik lembut, ikon opsional; error penting tidak hilang otomatis |
| Modal/drawer | Judul, isi, tombol tutup berlabel, urutan aksi konsisten; fokus masuk, terjaga, dan kembali ke pemicu saat ditutup |
| Tabel admin | Header jelas, baris mudah dipindai, status berbentuk badge, aksi berlabel; scroll horizontal berada dalam container |
| Empty state | Judul singkat, alasan yang diketahui, dan langkah berikutnya yang relevan |

Setiap kontrol harus memiliki state normal, hover, focus, active, disabled, serta loading jika ada proses asinkron. Loading memakai label seperti “Menyimpan…” dan mencegah submit ganda selama request berjalan. Jangan menampilkan sukses sebelum server mengonfirmasi hasil.

Contoh copy: “Belum ada dokumen pada kategori ini.”, “Tidak ada berita yang cocok dengan pencarian Anda.”, “Aspirasi berhasil dikirim.”, dan “Pesan belum terkirim. Silakan coba lagi.”

## 6. Target redesign: penerapan per halaman

### Beranda

Urutan tampilan yang dituju:

1. Header dengan navigasi Beranda, Profil, dan Dokumen.
2. Hero navy dengan identitas portal, deskripsi layanan, tindakan utama “Lihat Dokumen”, dan tautan pendukung menuju Profil.
3. Statistik dinamis yang memang tersedia pada aplikasi.
4. Ringkasan akses menuju Profil dan Dokumen dengan kartu putih yang konsisten bila diperlukan.
5. Footer navy dengan identitas instansi dan tautan penting.

Gunakan `hero.png` sebagai foto kontekstual jika sesuai komposisi. Overlay mengikuti karakter login, dengan keterbacaan teks diperiksa pada hasil render. Widget chatbot tetap mudah ditemukan. Jangan menambahkan angka capaian, tautan layanan, atau informasi kontak rekaan.

### Profil

Gunakan header halaman yang ringkas, judul bagian yang berurutan, dan area baca putih. Sajikan bagian profil sesuai data yang tersimpan. Navigasi antarbagian dapat digunakan jika konten panjang dan judul bagian tersedia.

### Konten berita dan galeri

Modul berita dan galeri telah dihentikan dan tidak termasuk dalam antarmuka maupun dashboard aplikasi. Jangan menambahkan kartu, navigasi, model, atau alur pengelolaan untuk kedua modul tersebut.

### Dokumen

Prioritaskan breadcrumb kategori, pilihan kategori/subkategori, filter tahun, daftar dokumen, dan pagination. Pada mobile, filter disusun vertikal di atas hasil. Nama dokumen panjang boleh membungkus. Tombol buka dan unduh tidak bergantung pada ikon saja. Parameter filter dan hierarki kategori harus tetap bekerja.

### Informasi institusi

Informasi institusi dan kontak hanya ditampilkan sebagai teks pada footer bila diperlukan. Modul aspirasi/kontak telah dihentikan dan tidak memiliki halaman, form, atau dashboard pengelolaan.

### Chatbot

Gunakan header navy, area percakapan terang, balon pengguna biru dengan teks putih, dan jawaban asisten pada permukaan netral. Sumber dokumen berupa nama file dan halaman ketika tersedia harus mudah dibaca dan diakses.

Pertahankan pilihan bahasa, sesi baru, riwayat, hapus riwayat, feedback, dan ekspor sesuai kontrak existing. Bedakan kondisi menunggu jawaban, jawaban tersedia, pembatasan request, dan layanan bermasalah melalui pesan yang dapat dipahami. Pada ponsel, panel mengikuti viewport yang tersedia dan input tetap terjangkau saat keyboard terbuka. Penutupan panel tidak boleh tanpa sengaja menghapus percakapan.

### Login, OTP, dan pemulihan kata sandi

Pertahankan login sebagai tolok ukur visual. Terapkan shell brand/form yang sama pada halaman autentikasi terkait. OTP mempertahankan input yang mendukung paste, email tersamarkan, informasi masa berlaku, dan state kirim ulang sesuai backend. Nilai durasi, jumlah percobaan, serta validasi tidak ditentukan ulang oleh dokumen desain ini.

### Dashboard Admin dan Super Admin

Gunakan sidebar navy, latar kerja `#f8fafc`, dan kartu/tabel putih. Lebar sidebar usulan 248–264 px; pada layar kecil menjadi drawer. Area utama menampilkan judul section, ringkasan relevan, toolbar, dan data/form.

Pertahankan navigasi `#section-*`, feedback CRUD, status ingest, aksi pembatalan, dan konfirmasi hapus. Manajemen pengguna hanya tampil sesuai kewenangan dan tetap dilindungi server. Desain tabel padat boleh memakai kontrol lebih ringkas, tetapi target sentuh tetap minimum 44 × 44 px.

## 7. Responsivitas dan aksesibilitas

| Lebar | Perilaku target |
|---|---|
| 320–639 px | Satu kolom, navigasi mobile, form selebar ruang tersedia, CTA membungkus atau tersusun vertikal |
| 640–1023 px | Grid konten dapat menjadi dua kolom; autentikasi tetap satu kolom |
| Mulai 1024 px | Navigasi desktop, autentikasi 55/45, sidebar dashboard, grid publik sampai tiga kolom |
| Mulai 1440 px | Konten dibatasi container; ruang luar bertambah tanpa memperlebar paragraf terus-menerus |

Kriteria implementasi:

- Uji minimal pada 320, 375, 768, 1024, dan 1440 px, serta zoom 200%.
- Tidak ada scroll horizontal pada halaman; tabel lebar boleh memiliki scroll lokal berlabel.
- Target kontras teks biasa minimal 4.5:1, teks besar 3:1, serta kontrol/fokus bermakna 3:1 terhadap warna di sekitarnya.
- Semua fungsi dapat dijangkau keyboard dengan indikator fokus terlihat.
- Sediakan skip link, landmark, satu H1 utama, serta hierarki heading yang logis.
- Label terhubung ke input; error memakai `aria-invalid` dan `aria-describedby` sesuai kebutuhan.
- Gunakan `role="status"` untuk feedback proses dan `role="alert"` secara selektif untuk error mendesak.
- Logo memiliki alt bermakna; foto dekoratif memakai alt kosong, foto konten memiliki deskripsi relevan.
- Navigasi aktif, status, dan error tidak ditandai hanya melalui warna.
- Hormati `prefers-reduced-motion`; animasi tidak menghambat pembacaan atau interaksi.

## 8. Aset, gerak, dan implementasi

Gunakan logo putih yang sudah dipakai login hanya pada latar gelap. Untuk latar terang, pilih varian resmi yang terbaca; jika tidak tersedia, letakkan logo putih dalam bidang navy. Pertahankan rasio, jangan memotong, mewarnai ulang, atau mengganti logo dengan simbol buatan.

Tetapkan dimensi gambar untuk mengurangi pergeseran layout. Gunakan lazy loading pada gambar di bawah layar awal. Font Outfit saat ini dimuat lewat Google Fonts pada layout guest; target pengelolaan aset berikutnya adalah pemuatan konsisten melalui aset lokal/Vite bila tersedia, tanpa menambah CDN baru.

Gunakan transisi warna, border, dan shadow sekitar 150–200 ms. Target redesign menyederhanakan efek shimmer berulang, pembesaran foto otomatis, serta perpindahan kartu besar agar selaras dengan login yang tenang.

Implementasikan token bersama pada stylesheet yang dikelola Vite. Komponen Blade bersama dapat dipakai untuk tombol, field, alert, heading, dan kartu. Hindari ketergantungan halaman publik pada selector `.admin-login`; login dan portal menggunakan token bersama dengan layout masing-masing. Jangan mengedit hasil build secara manual.

## 9. Catatan perbedaan dan tahapan penerapan

Perbedaan yang sudah terlihat dari kode:

| Kondisi saat ini | Target |
|---|---|
| Login memakai Outfit, token font global masih Figtree | Satukan token font dan pemuatannya |
| Beranda memakai aksen kuning, tombol pill, dan beberapa bobot sangat tebal | Gunakan palet navy/biru dan hierarki bobot sesuai panduan |
| Stylesheet utama memiliki shimmer serta hover kartu naik 12 px dan membesar | Gunakan perubahan warna/bayangan yang ringan |
| Layout guest masih memiliki cabang tampilan glass untuk halaman tanpa slot brand | Selaraskan halaman autentikasi yang masuk cakupan dengan shell login |
| Warna dan ukuran tersebar dalam class/style halaman | Konsolidasikan bertahap menjadi token dan komponen bersama |

Tahapan yang disarankan:

1. Rekam tampilan login sebagai baseline visual dan inventarisasi komponen yang dipakai halaman lain.
2. Susun token serta komponen bersama; verifikasi login tidak mengalami regresi.
3. Terapkan pada layout publik dan beranda, lalu profil dan dokumen. Modul berita, galeri, dan aspirasi tidak termasuk dalam cakupan aplikasi aktif.
4. Selaraskan chatbot dan halaman autentikasi terkait.
5. Terapkan pada dashboard dengan mempertahankan navigasi dan otorisasi.
6. Jalankan pemeriksaan visual, aksesibilitas, build, dan regresi perilaku yang terdampak.

## 10. Acceptance criteria redesign

- [ ] Halaman publik, autentikasi, dan dashboard menggunakan palet serta tipografi yang konsisten dengan login.
- [ ] Logo resmi terbaca dengan rasio yang benar pada semua ukuran layar.
- [ ] Tombol, form, alert, kartu, dan pagination mengikuti spesifikasi bersama.
- [ ] Layout lolos pemeriksaan pada seluruh lebar uji tanpa konten atau kontrol terpotong.
- [ ] Navigasi keyboard, fokus, label form, kontras, dan reduced motion telah diperiksa.
- [ ] Empty, error, loading, success, dan disabled state tersedia sesuai interaksi masing-masing.
- [ ] Search, kategori bertingkat, filter tahun, breadcrumb, dan pagination dokumen tetap berfungsi.
- [ ] Alur login/OTP, hak akses dashboard, audit log, serta lifecycle ingest tetap sesuai kontrak.
- [ ] Chatbot mempertahankan sumber jawaban dan fungsi sesi tanpa membuka data internal.
- [ ] Test perilaku yang terdampak diperbarui dan lulus; test AI memakai fake/mock.
- [ ] `php artisan test` dan `npm run build` lulus pada implementasi akhir; PHP yang berubah diformat dengan Pint bila tersedia.
- [ ] Perubahan kontrak produk, bila ada, dicatat pada PRD sebelum dianggap selesai.

Penyusunan dokumen ini belum menerapkan redesign pada website. Verifikasi browser dan pengujian di atas merupakan kriteria untuk tahap implementasi, bukan hasil pengujian dokumen.

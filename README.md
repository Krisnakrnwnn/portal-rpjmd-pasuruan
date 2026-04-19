# 🏛️ Layanan Informasi RPJMD Kota Pasuruan

Sistem Layanan Informasi Rencana Pembangunan Jangka Menengah Daerah (RPJMD) Terpadu berbasis kecerdasan buatan (*AI Chatbot*). Proyek ini dirancang sebagai platform transparansi data pemerintah yang dilengkapi dasbor analitik (*Chart.js*) progresif, ekspor laporan formal (PDF), serta antarmuka PWA (Progressive Web Application).

## 🚀 Fitur Utama

- **Integrasi Penuh AI Chatbot**: Dibekali framework pintar RAG yang terhubung ke **Google Gemini**, membantu merespons jutaan data dan skenario layanan untuk memberikan jawaban efisien ke masyarakat. Termasuk pencegahan kebocoran limit API melalui *Quick Options Interceptor*.
- **Admin Dashboard Dinamis (CMS)**: Manipulasi data Hero Section, target capaian prioritas, program berjalan, yang seketika *real-time* tercermin di halaman Beranda.
- **Interaktif Visual Data (Chart.js)**: Penampil distribusi sektor (Doughnut Chart) dan Bar Chart langsung ditarik dari Database capaian riil.
- **PWA (Progressive Web App) Ready**: Mendukung instalasi *"Add To Home Screen"* via browser *mobile* serta pemuatan secepat kilat menggunakan Service Worker Caching.
- **Smart PDF Export Laporan**: Generator laporan murni dari layout HTML to PDF menggunakan `@media print`, membersihkan tampilan tabel & analitik tanpa sisa kode formulir sehingga menghasilkan bentuk berkas kenegaraan super resmi.
- **Simulator Database Cerdas (Fake Seeder)**: Siap mendemonstrasikan sistem kapan saja tanpa repot karena sudah tertanam ratusan garis Data Indikator, Capaian Sektor, dan Berita secara otomatis!

---

## 💻 Tech Stack

- **Framework**: Laravel 11 (PHP 8.2+)
- **Frontend**: Vite.js + TailwindCSS 3 
- **Database**: PostgreSQL (Supabase Connected) / MySQL / SQLite
- **Libraries**: Chart.js, AOS (Animate on Scroll), Phosphor/Heroicons
- **API**: Google Gemini LLM API via Laravel HTTP Client

---

## 🛠️ Instalasi & Persiapan Lokal (How To Run)

Ikuti langkah-langkah di bawah ini untuk menjalankan portal RPJMD secara sempurna di perangkat Lokal/Laptop Anda:

### 1. Requirements Ekosistem
- **PHP** ^8.2 (Pastikan jalan di sistem)
- **Composer** ^2.6
- **Node.js** ^18 & NPM (Untuk *TailwindCSS Vite compiler*)
- Database Lokal (Laragon/XAMPP) atau Database Remote (Supabase).

### 2. Kloning Repositori
```bash
git clone https://github.com/Krisnakrnwnn/PBLS6.git
cd PBLS6
```

### 3. Konfigurasi Dependensi
Jalankan komando instalasi *Library* Backend dan Frontend:
```bash
# Instal kerangka Laravel
composer install

# Instal NPM (Tailwind & Vite)
npm install
```

### 4. Setup Environment (.env)
1. Salin file `.env.example` lalu ubah namanya menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
2. Hasilkan kunci enkripsi aplikasi:
   ```bash
   php artisan key:generate
   ```
3. Buka konfigurasi `.env` Anda melalui Teks Editor dan isi sambungan Database Anda (MySQL / Postgres) sesuai pengaturan lokal komputer.
   - Jangan lupa sisipkan API KEY **Google Gemini** pada konfigurasi environment yang relevan jika ingin layanan AI Chatbot berfungsi global.

### 5. Bangun Database & Simulator
Berikan nyawa pada sistem Anda dengan membuat struktur (migrasi) tabel dan menyuntikkan simulasi datanya:
```bash
# Mengeksekusi tabel dasar
php artisan migrate:fresh

# Mengeksekusi penciptaan puluhan Data Sektor, Capaian Indikator, & Ekosistem Aplikasi
php artisan db:seed --class=DummyDataSeeder
```

### 6. Jalankan Server Berdampingan (Dual-Terminal)
Anda HARUS mengeksekusi dua perintah ini menggunakan dua Terminal *(Command Prompt / PowerShell)* yang berbeda secara serentak.

**Terminal Pertama (Untuk Server Backend):**
```bash
php artisan serve
# Aplikasi Laravel akan terbuka di alamat -> http://localhost:8000
```

**Terminal Kedua (Untuk Asset Compiling UI):**
```bash
npm run dev
# Vite server akan bekerja me-refresh CSS/JS setiap kali ada modifikasi.
```
*(Catatan Produksi: Jika proyek ini akan dipresentasikan tanpa perlu diedit lagi, cukup jalankan `npm run build` sekali di tahap ini, lalu terminal kedua tersebut boleh dimatikan).*

---

## 🔐 Info Login (Admin Control)
Akses *backroom* melalui URL **`http://localhost:8000/login`**.
Karena fungsi pendaftaran terbuka, Anda dapat melakukan registrasi *User* baru melalui alamat `/register`, atau gunakan akun statis jika diinisialisasi melalui migrasi. Seluruh akun akan otomatis memilik hak pengeditan Dashboard.

## 🤝 Kreator & Lisensi
Proyek pengembangan Sistem Layanan Informasi ini dirancang oleh **Krisnakrnwnn** untuk penugasan *Project Based Learning (PBL)* Universitas.
All Rights Reserved.

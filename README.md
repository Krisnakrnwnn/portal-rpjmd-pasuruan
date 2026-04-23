# 🏛️ Bapperida Kabupaten Pasuruan - Portal Layanan Informasi RPJMD

Sistem Layanan Informasi Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah (Bapperida) Kabupaten Pasuruan. Portal ini menyajikan transparansi data perencanaan (RPJMD) dan capaian kinerja daerah, dilengkapi dengan asisten pintar berbasis kecerdasan buatan (*AI Chatbot*).

## 🚀 Fitur Utama

- **Integrasi Penuh AI Chatbot**: Dibekali framework pintar RAG yang terhubung ke **Google Gemini**, membantu merespons jutaan data dan skenario layanan untuk memberikan jawaban efisien ke masyarakat.
- **Admin Dashboard Dinamis (CMS)**: Kelola data Hero Section, target capaian prioritas, program berjalan, dan aspirasi masyarakat secara real-time.
- **Interaktif Visual Data (Chart.js)**: Penampil distribusi sektor dan indikator kinerja utama langsung dari database.
- **PWA (Progressive Web App)**: Mendukung instalasi di perangkat mobile dan desktop dengan dukungan Service Worker.
- **Smart PDF Export**: Menghasilkan laporan resmi dalam format PDF yang bersih dari elemen navigasi/form.

---

## 💻 Tech Stack

- **Framework**: Laravel 11 (PHP 8.2+)
- **Frontend**: Vite.js + TailwindCSS 3 
- **Database**: MySQL 5.7+ / MariaDB (Direkomendasikan) / PostgreSQL
- **AI Engine**: Google Gemini API
- **Visuals**: Chart.js, AOS, Heroicons

---

## 🛠️ Panduan Instalasi Produksi (Hosting)

Gunakan panduan ini jika Anda adalah administrator yang akan melakukan deployment ke server produksi:

### 1. Persiapan Lingkungan
Pastikan server memiliki:
- **PHP** ^8.2
- **Composer** ^2.x
- **Node.js** ^18 & NPM
- **MySQL** 5.7 atau versi lebih baru

### 2. Kloning & Instalasi
```bash
git clone https://github.com/Krisnakrnwnn/portal-rpjmd-pasuruan.git
cd portal-rpjmd-pasuruan
composer install --optimize-autoloader --no-dev
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` ke `.env` dan sesuaikan nilainya:
```bash
cp .env.example .env
php artisan key:generate --force
```
**PENTING**:
- Atur `APP_ENV=production` dan `APP_DEBUG=false`.
- Masukkan kredensial database (MySQL).
- Masukkan `GEMINI_API_KEY` untuk fitur AI Chatbot.

### 4. Build Assets
Kompilasi aset frontend untuk produksi:
```bash
npm run build
```

### 5. Setup Database & Akun Admin
Jalankan migrasi dan buat akun Super Admin resmi:
```bash
# Migrasi struktur tabel
php artisan migrate --force

# Inisialisasi data dasar & Akun Super Admin Resmi
php artisan db:seed --class=DatabaseSeeder
```

---

## 🔐 Informasi Login Admin (Produksi)

Setelah menjalankan seeder di atas, akses halaman admin di `/login` menggunakan akun resmi:
- **Email**: `bapperida@pasuruankab.go.id`
- **Password**: `Pasuruan2026!`
- **Role**: `Super Admin`

---

## ⚙️ Optimasi Produksi
Sangat disarankan menjalankan perintah berikut untuk performa maksimal:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🤝 Kreator
Proyek pengembangan Sistem Layanan Informasi ini dirancang oleh **Krisnakrnwnn dkk**.
All Rights Reserved.

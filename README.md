# Setup Proyek Portal RPJMD Kabupaten Pasuruan

Berikut adalah langkah-langkah untuk melakukan instalasi dan setup proyek secara lokal.

## 1. Clone & Install Dependencies
Pastikan Anda sudah menginstal PHP (8.1+), Composer, dan Node.js.
```bash
git clone https://github.com/Krisnakrnwnn/portal-rpjmd-pasuruan.git
cd portal-rpjmd-pasuruan
composer install
npm install && npm run build
```

## 2. Setup Environment Variables
Gandakan file `.env.example` menjadi `.env`, lalu *generate application key*.
```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan sesuaikan konfigurasi untuk koneksi database MySQL dan API Key Gemini:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=

# Masukkan API Key dari Google Gemini AI
GEMINI_API_KEY=your_gemini_api_key_here
```

## 3. Setup Database
Buat database baru di MySQL (misalnya melalui phpMyAdmin atau command line) dengan nama yang sesuai di `.env`, lalu jalankan perintah migrasi:
```bash
php artisan migrate
```
*(Opsional: Jalankan `php artisan db:seed` jika ada data seeder bawaan).*

## 4. Ingest Dokumen PDF untuk Chatbot AI
Agar Chatbot dapat menjawab pertanyaan seputar RPJMD, Anda perlu melakukan *ingest* (memasukkan data) dokumen PDF ke dalam sistem.
```bash
php artisan rag:ingest "storage/app/path_to_document.pdf"
```

## 5. Menjalankan Server Lokal
Setelah semua langkah di atas selesai, jalankan server pengembangan lokal.
```bash
php artisan serve
```
Buka browser dan akses aplikasi melalui `http://localhost:8000`.

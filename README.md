# Setup Proyek Portal RPJMD Kabupaten Pasuruan

Berikut adalah langkah-langkah untuk melakukan instalasi dan setup proyek secara lokal.

## 1. Clone & Install Dependencies
Pastikan Anda sudah menginstal PHP (8.1+), Composer, dan Node.js.
```bash
git clone <repo-url>
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

Buka file `.env` dan pastikan konfigurasi untuk database SQLite dan API Key Gemini sudah sesuai:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Masukkan API Key dari Google Gemini AI
GEMINI_API_KEY=your_gemini_api_key_here
```
*(Catatan: Sesuaikan path `DB_DATABASE` dengan lokasi absolut komputer Anda atau cukup set ke lokasi default).*

## 3. Setup Database
Buat file database kosong dan jalankan migrasi database.
```bash
touch database/database.sqlite
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

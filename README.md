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

Konfigurasikan pengiriman email administrator melalui variabel `MAIL_*` Laravel. Fitur OTP aktif secara default dan dapat dikendalikan untuk kebutuhan rollback operasional:

```env
ADMIN_OTP_ENABLED=true
ADMIN_OTP_EXPIRES_MINUTES=5
ADMIN_OTP_MAX_ATTEMPTS=5
ADMIN_OTP_RESEND_COOLDOWN_SECONDS=60
ADMIN_OTP_MAX_DELIVERIES=3
ADMIN_OTP_DELIVERY_WINDOW_SECONDS=900
ADMIN_OTP_IP_MAX_DELIVERIES=10
ADMIN_OTP_RETENTION_HOURS=24
ADMIN_SUPPORT_EMAIL=
MAIL_TIMEOUT=10
```

Untuk Gmail pada port 587 gunakan `MAIL_SCHEME=smtp`; STARTTLS dinegosiasikan otomatis oleh mailer. Jangan menggunakan `MAIL_SCHEME=tls` karena `tls` bukan scheme transport Symfony Mailer yang valid.

Jalankan scheduler dan queue worker pada production. Pengiriman OTP saat ini dilakukan langsung agar kegagalan provider dapat dikembalikan dengan aman pada alur login; timeout mail mencegah request menunggu tanpa batas.

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

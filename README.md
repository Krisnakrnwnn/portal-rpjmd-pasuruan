# Setup Proyek Portal RPJMD Kabupaten Pasuruan

Berikut adalah langkah-langkah untuk melakukan instalasi dan setup proyek secara lokal.

## 1. Clone & Install Dependencies
Pastikan Anda sudah menginstal PHP (8.2+), Composer, dan Node.js.
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

## 6. Pengaturan Model AI Chatbot

Admin dan Super Admin dapat membuka **Setelan Sistem** (`/admin/setelan`). Provider tetap Google Gemini. Pilih Gemini 2.5 Flash atau Gemini 2.5 Pro, gunakan **Test Model** untuk menguji kandidat, kemudian **Simpan Model AI** untuk mengaktifkannya. Mengubah dropdown atau menguji kandidat tidak mengubah model aktif. Test memakai kuota API, satu attempt dengan timeout 45 detik, tanpa dokumen RAG atau riwayat warga.

Model aktif tetap disimpan pada tabel `stats`, key `gemini_model`; provider pada key `ai_provider`. Tidak ada tabel/migration baru. Simpan pertama menambahkan key provider bila belum ada, bersama model dan audit dalam satu transaction. Request chatbot berikutnya membaca nilai terbaru tanpa restart, config clear, edit `.env`, atau deployment ulang. Request yang sudah mengambil konfigurasi dapat menyelesaikan jawaban dengan model sebelumnya.

Katalog dan default generation berada di `config/ai.php`. Model embedding `gemini-embedding-001`, retrieval, prompt, dan history tidak berubah. Katalog awal diverifikasi melalui dokumentasi resmi [Flash](https://ai.google.dev/gemini-api/docs/models/gemini-2.5-flash), [Pro](https://ai.google.dev/gemini-api/docs/models/gemini-2.5-pro), dan [jadwal penghentian](https://ai.google.dev/gemini-api/docs/deprecations) pada 15 September 2026. Penambahan anggota katalog memerlukan review kompatibilitas, biaya, dan kualitas jawaban; perpindahan antaranggota katalog cukup melalui Setelan.

Credential tetap `GEMINI_API_KEY` di server. Jalur chatbot membacanya melalui `config('services.gemini.api_key')` dan mengirim lewat header backend. Tidak ada pengelolaan key di UI/database. Jika setting tersimpan tidak valid, admin melihat status fallback ke default valid; nilai DB tidak ditulis ulang otomatis. Kegagalan provider tidak memicu perpindahan model otomatis.

Sebelum rollout, periksa model lama nonrahasia, pastikan masuk katalog, jalankan Test Model di staging untuk kedua model, dan evaluasi grounding/sumber serta bahasa pada pertanyaan RPJMD. Ketersediaan model pada dokumentasi tidak menjamin akses/kuota API key staging. Jangan menjalankan live API dalam test otomatis. Penggunaan `env()` langsung pada ingestor/command PDF lama dan kompatibilitas config cache-nya masih merupakan utang teknis terpisah.

Validasi otomatis:

```bash
php artisan test
npm run build
```

Test dasar melarang HTTP yang tidak di-fake; test AI menggunakan `Http::fake()`. Untuk regresi browser tanpa layanan Gemini, setelah build gunakan PowerShell:

```powershell
$env:AI_REVIEW='1'
php artisan test --filter=test_authorized_roles_see_save_and_reload_model
Remove-Item Env:AI_REVIEW
node tests/Browser/ai-settings.cjs
```

Script browser menggunakan Chrome headless, HTML dari database test, dan respons lokal palsu. `CHROME_PATH` dapat menentukan lokasi executable. Screenshot hanya disimpan di `storage/framework/testing/ai-review/`.

Status implementasi dan batas verifikasi tersedia di [PRD AI Model Management](prd/PRD-AI-MODEL-MANAGEMENT.md). Gunakan [lembar UAT dan rollout](prd/UAT-AI-MODEL-MANAGEMENT.md) untuk evaluasi kedua model dengan dokumen staging sebelum rilis; hasil otomatis tidak membuktikan akses API atau kualitas jawaban live.

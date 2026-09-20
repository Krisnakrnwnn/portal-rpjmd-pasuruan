# Dokumen Serah-Terima Portal Bapperida

Dokumen ini menjadi panduan serah-terima dan deployment Portal Bapperida Kabupaten Pasuruan kepada tim Kominfo.

## 1. Ringkasan aplikasi

Portal terdiri dari:

- Portal publik: Beranda, Profil, dan Dokumen.
- Halaman seremoni `/launching`.
- PRivIA/chatbot berbasis dokumen RPJMD.
- Login pengguna.
- Dashboard Admin dan Super Admin.
- Upload dokumen publik.
- Ingest PDF privat untuk basis pengetahuan chatbot.
- OTP email untuk login administrator.

## 2. Kebutuhan server

Minimum:

- PHP 8.2 atau lebih baru.
- MySQL.
- Composer.
- Node.js/npm untuk proses build frontend.
- PHP extension Laravel umum: PDO MySQL, Mbstring, OpenSSL, Fileinfo, XML, Ctype, JSON, dan Curl.
- Imagick jika menggunakan jalur ingest PDF Vision.
- HTTPS aktif.
- Web server diarahkan ke folder `public`.

Node.js tidak wajib berjalan terus di production setelah frontend selesai di-build.

## 3. Environment production

Nilai environment harus dikirim melalui kanal aman dan tidak dimasukkan ke repository.

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-resmi
APP_KEY=...

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

GEMINI_API_KEY=...

QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_SCHEME=smtp
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME=...
MAIL_TIMEOUT=10
```

Konfigurasi OTP administrator:

```env
ADMIN_OTP_ENABLED=true
ADMIN_OTP_EXPIRES_MINUTES=5
ADMIN_OTP_MAX_ATTEMPTS=5
ADMIN_OTP_RESEND_COOLDOWN_SECONDS=60
ADMIN_OTP_MAX_DELIVERIES=3
ADMIN_OTP_DELIVERY_WINDOW_SECONDS=900
ADMIN_OTP_IP_MAX_DELIVERIES=10
ADMIN_OTP_RETENTION_HOURS=24
ADMIN_SUPPORT_EMAIL=...
```

File `.env.example` belum tersedia di repository. Daftar environment di atas perlu diverifikasi kembali terhadap server production.

## 4. Deployment

Urutan deployment pada instalasi baru:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

Jangan menjalankan `php artisan key:generate` pada instalasi existing karena dapat membuat session dan cookie lama tidak valid.

Jangan menjalankan `php artisan db:seed` di production tanpa review. Seeder saat ini berisi data bootstrap/demo dan akun Super Admin awal.

Web server harus meneruskan request ke `public/index.php`, mengaktifkan rewrite Laravel, dan menggunakan HTTPS.

## 5. Queue dan scheduler

Queue wajib aktif karena proses ingest dokumen berjalan melalui queue. Default queue menggunakan database.

Tambahkan scheduler Laravel setiap menit:

```cron
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduler aplikasi menjalankan:

- Queue worker dengan `queue:work --stop-when-empty`.
- Pembersihan OTP kedaluwarsa setiap hari.

Alternatifnya, worker dapat dijalankan permanen melalui Supervisor atau systemd. Hindari menjalankan worker ganda tanpa memahami dampaknya.

Monitoring queue:

```bash
php artisan queue:failed
php artisan queue:retry all
```

## 6. Storage dan backup

Direktori berikut harus writable oleh user web server:

- `storage/framework`
- `storage/logs`
- `storage/app/private/documents`
- `public/uploads/documents`
- `bootstrap/cache`

Backup rutin harus mencakup:

- Database.
- `storage/app/private/documents`.
- `public/uploads`.
- File `.env` melalui mekanisme penyimpanan rahasia.
- Konfigurasi web server.

Dokumen chatbot berada di storage privat dan tidak boleh dipindahkan ke folder publik.

## 7. Chatbot dan pengelolaan model AI

### Status implementasi saat serah-terima

Aplikasi yang diserahterimakan saat ini berada pada fase satu:

- Provider generation yang berjalan: Google Gemini.
- Credential generation dan embedding: `GEMINI_API_KEY`.
- Model embedding tetap Gemini dan tidak berubah ketika model generation diganti.
- Model generation yang tersedia mengikuti katalog Gemini pada aplikasi dan pengaturan Admin.
- Tidak ada failover otomatis ke provider atau model lain.

Sebelum go-live:

1. Pastikan `GEMINI_API_KEY` aktif dan memiliki kuota.
2. Login sebagai Admin atau Super Admin.
3. Buka `/admin/setelan`.
4. Uji model Gemini melalui tombol Test Model.
5. Pastikan model aktif tersimpan.
6. Upload atau ingest dokumen RPJMD.
7. Tunggu queue sampai status ingest selesai.
8. Ajukan pertanyaan yang jawabannya memang terdapat di dokumen.
9. Pastikan sumber file dan halaman tampil pada jawaban.

### Rencana fase dua multi-provider

PRD mencatat rencana pengembangan tiga provider:

- Google Gemini — `GEMINI_API_KEY`.
- **OpenAI/GPT — `OPENAI_API_KEY` wajib disiapkan oleh Kominfo.**
- **Anthropic/Claude — `ANTHROPIC_API_KEY` wajib disiapkan oleh Kominfo.**

Kominfo perlu menyiapkan dan mengamankan dua credential berikut sebagai bagian dari persiapan fase dua:

```env
OPENAI_API_KEY=...
ANTHROPIC_API_KEY=...
```

Credential tersebut harus berasal dari akun organisasi resmi, memiliki billing/kuota yang sesuai, dan disimpan hanya pada secret manager atau environment server. Jangan memasukkannya ke repository, database, UI admin, log, atau dokumen serah-terima yang dibagikan umum.

Fase dua tersebut belum boleh dianggap sebagai fitur production pada saat serah-terima. Adapter, katalog model, validasi credential, UAT, evaluasi kualitas RAG, biaya, latency, dan prosedur rollback untuk OpenAI/Anthropic masih harus diselesaikan dan diuji sebelum diaktifkan.

Jika fase dua nanti diimplementasikan:

- Credential provider dibaca dari konfigurasi server, bukan dari UI atau database.
- Provider yang tidak memiliki credential hanya ditampilkan sebagai belum siap.
- Pergantian provider tidak mengubah atau mengharuskan re-ingest embedding secara otomatis.
- Kegagalan provider aktif tidak boleh memicu perpindahan provider diam-diam.
- Pertanyaan embedding tetap dikirim ke Gemini sesuai rancangan PRD.
- Setiap provider harus melalui Test Model dan UAT terpisah sebelum rollout.

Catatan teknis: beberapa command ingest saat ini masih membaca `GEMINI_API_KEY` langsung melalui `env()`. Setelah konfigurasi Laravel di-cache, jalur ingest harus diuji ulang secara khusus.

## 8. Akun dan hak akses

Peran aplikasi:

- `User`: akses portal publik setelah login.
- `Admin`: dashboard dan pengelolaan konten.
- `Super Admin`: seluruh akses Admin dan manajemen pengguna.

Akun bootstrap dari seeder harus:

- Mengganti password segera.
- Memverifikasi email.
- Tidak membagikan credential melalui dokumen publik.
- Dinonaktifkan atau diganti dengan akun resmi Kominfo setelah serah-terima.

## 9. Smoke test pasca-deployment

Uji minimal:

- `/launching` tampil dan asset tidak 404.
- Login User.
- Login Admin dengan OTP.
- Login Super Admin.
- Beranda, Profil, dan Dokumen.
- Upload dan download dokumen.
- Filter kategori dan tahun.
- Dashboard Admin.
- Pengaturan model AI.
- Upload dan ingest PDF.
- Chatbot PRivIA.
- Riwayat percakapan.
- Retry dan export percakapan.
- Pembatasan akses Admin dan Super Admin.
- Penghapusan akun sendiri dan Super Admin terakhir ditolak.
- Email OTP masuk dan kode kedaluwarsa sesuai konfigurasi.
- Tampilan mobile dan desktop.
- HTTPS, asset, storage, serta tidak ada error 404/500.

## 10. Prosedur operasional

Tim Kominfo perlu menetapkan:

- Penanggung jawab akun Super Admin.
- Penanggung jawab upload dan ingest dokumen.
- Penanggung jawab Gemini API dan SMTP.
- Jadwal backup database dan file.
- Prosedur pemantauan queue dan log.
- Kontak teknis untuk insiden deployment.

## 11. Catatan risiko dan batasan

- Repository perlu dibekukan dalam commit/tag release sebelum diserahkan.
- Pastikan seluruh perubahan lokal yang telah disetujui sudah di-commit.
- Jangan menyerahkan `.env` melalui repository atau chat biasa.
- `APP_DEBUG` harus `false` di production.
- Seeder demo tidak boleh dijalankan tanpa review terhadap database production.
- Warning deprecation `PDO::MYSQL_ATTR_SSL_CA` pada PHP 8.5 berasal dari dependency Laravel/vendor dan bukan perubahan aplikasi.
- Uji ingest setelah `config:cache` karena sebagian jalur ingest masih memakai `env()` langsung.

## 12. Persetujuan serah-terima

| Item | Status | Catatan |
|---|---|---|
| Source code dan commit release | [ ] | |
| File environment production | [ ] | Diserahkan melalui kanal aman |
| Database production/staging | [ ] | |
| Asset upload dan dokumen privat | [ ] | |
| Akun Super Admin | [ ] | Password wajib diganti |
| Gemini API dan SMTP | [ ] | Credential tidak masuk repository |
| Queue worker dan scheduler | [ ] | |
| Backup dan restore test | [ ] | |
| Smoke test pasca-deployment | [ ] | |
| Kontak dukungan teknis | [ ] | |

# Product Requirements Document

## Autentikasi Administrator dengan OTP Email

| Informasi | Nilai |
|---|---|
| Produk | Portal Manajemen RPJMD Kabupaten Pasuruan |
| Fitur | Verifikasi OTP Email saat Login Administrator |
| Status | Draft untuk persetujuan |
| Versi | 1.0 |
| Tanggal | 14 September 2026 |
| Pemilik produk | Bapperida Kabupaten Pasuruan |

## 1. Ringkasan

Portal manajemen RPJMD saat ini mengautentikasi administrator menggunakan email dan kata sandi. Setelah kredensial benar, pengguna langsung memiliki sesi terautentikasi dan diarahkan ke halaman aplikasi.

Fitur ini menambahkan OTP (One-Time Password) yang dikirim ke email sebagai faktor autentikasi kedua. Administrator baru memperoleh sesi login penuh setelah email, kata sandi, dan OTP berhasil diverifikasi.

OTP berlaku untuk seluruh akun dengan role `Admin` dan `Super Admin`.

## 2. Latar Belakang

Panel administrator dapat mengubah berita, layanan, statistik capaian, profil instansi, aspirasi, serta data pengguna. Login yang hanya bergantung pada email dan kata sandi meningkatkan dampak apabila kata sandi diketahui pihak lain.

Verifikasi OTP email diperlukan untuk:

- Mengurangi risiko pengambilalihan akun akibat kebocoran kata sandi.
- Memastikan pengguna masih memiliki akses ke email yang terdaftar.
- Memberikan kontrol dan jejak audit yang lebih baik pada proses login administrator.

## 3. Tujuan

- Mewajibkan verifikasi OTP email pada setiap login baru administrator.
- Mencegah akses ke halaman yang membutuhkan autentikasi sebelum OTP valid.
- Menyediakan pengalaman verifikasi yang jelas, aman, dan dapat dipulihkan ketika email terlambat diterima.
- Menyediakan pencatatan kejadian keamanan tanpa menyimpan OTP dalam bentuk terbaca.

## 4. Bukan Tujuan

Rilis ini tidak mencakup:

- Login tanpa kata sandi.
- OTP melalui SMS, WhatsApp, atau aplikasi authenticator.
- Penggantian email mandiri dalam alur OTP.
- Trusted device atau melewati OTP pada perangkat tertentu.
- Pemulihan akun otomatis jika pengguna kehilangan akses email.
- Perubahan hak akses role `Admin` dan `Super Admin`.
- OTP untuk pengunjung portal publik atau chatbot.

## 5. Pengguna dan Hak Akses

### 5.1 Admin

Harus menyelesaikan OTP sebelum memperoleh akses ke fungsi administrasi berita, layanan, capaian, profil, dan aspirasi.

### 5.2 Super Admin

Harus menyelesaikan OTP sebelum memperoleh akses administratif, termasuk pengelolaan akun administrator.

Tidak ada role administrator yang boleh dikecualikan dari OTP.

## 6. Alur Pengguna Utama

1. Pengguna membuka halaman login.
2. Pengguna memasukkan email dan kata sandi.
3. Sistem memvalidasi kredensial dan memastikan akun memiliki role `Admin` atau `Super Admin`.
4. Jika tidak valid, sistem menampilkan pesan login umum tanpa mengungkap apakah email terdaftar.
5. Jika valid, sistem membuat tantangan OTP dan mengirim kode ke email akun.
6. Sistem menampilkan halaman verifikasi dengan alamat email yang disamarkan.
7. Pengguna memasukkan OTP enam digit.
8. Sistem memvalidasi OTP, masa berlaku, jumlah percobaan, dan status penggunaannya.
9. Jika valid, sistem menandai OTP telah digunakan, membuat sesi terautentikasi, memperbarui waktu login, dan mengarahkan pengguna ke tujuan semula atau dashboard admin.
10. Jika tidak valid, sistem menampilkan pesan yang aman dan memberi kesempatan mencoba kembali selama batas percobaan belum tercapai.

## 7. Ketentuan Produk

### 7.1 Bentuk dan masa berlaku OTP

- OTP berupa enam digit angka, termasuk kemungkinan angka nol di awal.
- OTP berlaku selama 5 menit sejak berhasil dibuat.
- OTP hanya dapat digunakan satu kali.
- Hanya OTP terbaru yang aktif untuk satu tantangan login.
- Meminta OTP baru langsung membatalkan OTP sebelumnya.
- OTP tidak boleh dicatat dalam log atau disimpan dalam bentuk plaintext.

### 7.2 Pengiriman ulang

- Tombol kirim ulang aktif 60 detik setelah pengiriman terakhir.
- Maksimal 3 pengiriman OTP untuk satu akun dalam 15 menit.
- Respons pengiriman ulang tidak boleh mengungkap status keberadaan akun.
- Kegagalan layanan email harus ditampilkan sebagai kegagalan sementara dan tidak membuat pengguna login.

### 7.3 Percobaan verifikasi

- Maksimal 5 percobaan OTP untuk satu tantangan login.
- Setelah batas tercapai, tantangan dibatalkan dan pengguna harus kembali ke halaman login.
- OTP kedaluwarsa atau telah digunakan harus ditolak.
- Perbandingan OTP harus dilakukan secara aman terhadap nilai yang sudah di-hash.

### 7.4 Sesi dan opsi “Ingat Sesi Saya”

- Kredensial yang valid belum membentuk sesi autentikasi penuh.
- Identitas sementara hanya digunakan untuk menyelesaikan tantangan OTP.
- Session ID harus diregenerasi setelah OTP berhasil.
- Pilihan “Ingat Sesi Saya” baru diterapkan setelah OTP berhasil.
- Logout tetap mengakhiri sesi dan meregenerasi token sesi.
- Sesi lama yang sudah aktif tidak diputus oleh peluncuran fitur ini, kecuali ada keputusan migrasi terpisah.

### 7.5 Navigasi

- Pengguna yang membuka halaman OTP tanpa tantangan aktif diarahkan ke login.
- Pengguna yang belum menyelesaikan OTP tidak boleh mengakses route ber-middleware `auth` maupun route admin.
- Pengguna yang sudah login dan membuka halaman login/OTP diarahkan ke halaman yang sesuai dengan rolenya.
- Setelah berhasil, sistem menghormati intended URL selama URL tersebut memang dapat diakses oleh role pengguna.

## 8. Kebutuhan Antarmuka

### 8.1 Halaman login

- Tetap menyediakan kolom email, kata sandi, opsi “Ingat Sesi Saya”, dan “Lupa Sandi”.
- Tombol utama menggunakan label yang menjelaskan tahap berikutnya, misalnya “Lanjutkan”.
- Tidak menjanjikan OTP dikirim sebelum kredensial berhasil diperiksa.

### 8.2 Halaman verifikasi OTP

Harus memiliki:

- Judul “Verifikasi Email”.
- Penjelasan bahwa kode dikirim ke email yang disamarkan, misalnya `kr***@example.go.id`.
- Input enam digit dengan dukungan paste dan perangkat mobile.
- Tombol “Verifikasi dan Masuk”.
- Countdown masa berlaku kode.
- Tombol “Kirim Ulang Kode” beserta countdown cooldown.
- Tautan “Kembali ke Login” yang membatalkan tantangan aktif.
- Status loading yang mencegah pengiriman formulir berulang.
- Pesan berhasil atau gagal yang mudah dipahami.

### 8.3 Pesan kesalahan minimum

- Kredensial tidak valid: “Email atau kata sandi tidak sesuai.”
- OTP tidak valid: “Kode verifikasi tidak sesuai.”
- OTP kedaluwarsa: “Kode verifikasi telah kedaluwarsa. Silakan kirim kode baru.”
- Batas percobaan: “Terlalu banyak percobaan. Silakan login kembali.”
- Cooldown resend: “Kode baru dapat dikirim dalam {detik} detik.”
- Gangguan email: “Kode verifikasi belum dapat dikirim. Silakan coba beberapa saat lagi.”

## 9. Kebutuhan Fungsional

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-01 | Sistem memvalidasi email dan kata sandi sebelum membuat OTP. | Wajib |
| FR-02 | Sistem hanya melanjutkan akun dengan role Admin atau Super Admin. | Wajib |
| FR-03 | Sistem membuat OTP enam digit menggunakan generator acak kriptografis. | Wajib |
| FR-04 | Sistem menyimpan hash OTP, masa berlaku, status, jumlah percobaan, dan waktu kirim. | Wajib |
| FR-05 | Sistem mengirim OTP ke email yang tersimpan pada akun. | Wajib |
| FR-06 | Sistem tidak membuat sesi login penuh sebelum OTP berhasil. | Wajib |
| FR-07 | Sistem memverifikasi OTP dan menolaknya jika salah, kedaluwarsa, dibatalkan, atau sudah digunakan. | Wajib |
| FR-08 | Sistem membatalkan OTP lama ketika OTP baru diterbitkan. | Wajib |
| FR-09 | Sistem menerapkan cooldown, batas resend, batas percobaan, dan rate limit login. | Wajib |
| FR-10 | Sistem mempertahankan intended URL dan opsi remember login sampai verifikasi berhasil. | Wajib |
| FR-11 | Sistem mencatat kejadian autentikasi dan keamanan yang relevan. | Wajib |
| FR-12 | Sistem menyediakan fallback yang aman ketika layanan email gagal. | Wajib |

## 10. Kebutuhan Data

Implementasi membutuhkan rekaman tantangan OTP dengan atribut logis berikut:

- Identitas tantangan yang tidak mudah ditebak.
- Referensi pengguna.
- Hash OTP.
- Waktu kedaluwarsa.
- Waktu terakhir dikirim.
- Jumlah percobaan verifikasi.
- Jumlah pengiriman ulang atau data pembatas yang ekuivalen.
- Waktu digunakan atau status pembatalan.
- Metadata keamanan seperlunya, seperti IP dan user agent, dengan memperhatikan kebijakan privasi.

Data OTP yang kedaluwarsa perlu dibersihkan secara berkala. Retensi metadata audit mengikuti kebijakan instansi, sedangkan nilai hash OTP tidak perlu dipertahankan setelah masa retensi operasional berakhir.

## 11. Email OTP

Email harus memuat:

- Identitas Portal Manajemen RPJMD/Bapperida Kabupaten Pasuruan.
- Kode OTP enam digit yang mudah dibaca.
- Informasi bahwa kode berlaku 5 menit.
- Peringatan agar kode tidak diberikan kepada siapa pun.
- Informasi untuk mengabaikan email jika penerima tidak mencoba login.
- Waktu permintaan dan informasi bantuan, tanpa memuat kata sandi atau data sensitif lain.

Subjek yang disarankan: `Kode Verifikasi Login Portal RPJMD`.

### 11.1 Konfigurasi pengiriman wajib

Fitur OTP bergantung pada mailer Laravel yang benar-benar mengirim email. Konfigurasi bawaan/development seperti `MAIL_MAILER=log` tidak mengirim pesan ke kotak masuk; driver tersebut hanya menulis isi email ke log aplikasi dan tidak boleh digunakan pada production karena dapat mengekspos OTP.

Environment production atau staging yang digunakan untuk UAT minimal harus memiliki konfigurasi berikut melalui `.env` atau secret manager:

- `MAIL_MAILER` menggunakan driver pengiriman nyata yang didukung Laravel, misalnya `smtp`.
- `MAIL_HOST` dan `MAIL_PORT` sesuai penyedia email.
- `MAIL_SCHEME` menggunakan scheme transport yang didukung Symfony Mailer, biasanya `smtp` untuk port 587/STARTTLS atau `smtps` untuk koneksi TLS implisit sesuai penyedia. Nilai `tls` bukan scheme yang valid.
- `MAIL_USERNAME` dan `MAIL_PASSWORD` berasal dari secret manager/environment, bukan source code.
- `MAIL_FROM_ADDRESS` menggunakan alamat pengirim yang terverifikasi.
- `MAIL_FROM_NAME` mengidentifikasi Portal RPJMD/Bapperida Kabupaten Pasuruan.
- `MAIL_TIMEOUT` memiliki batas waktu yang wajar agar request login tidak menunggu tanpa batas.
- `ADMIN_OTP_ENABLED=true` pada kondisi operasi normal.
- `ADMIN_SUPPORT_EMAIL` berisi alamat bantuan yang valid bila dicantumkan dalam email.

Setelah konfigurasi environment berubah, cache konfigurasi Laravel wajib dibersihkan atau dibangun ulang sesuai prosedur deployment. Nilai credential dan secret tidak boleh ditampilkan pada dokumentasi, log, response, atau antarmuka pengguna.

### 11.2 Kesiapan pengirim email

Sebelum OTP diaktifkan untuk pengguna:

- Domain/alamat pengirim harus diverifikasi pada penyedia email.
- SPF, DKIM, dan DMARC dikonfigurasi sesuai kebijakan domain instansi bila tersedia.
- Koneksi SMTP/provider diuji dari environment aplikasi, bukan hanya dari komputer pengembang.
- Email uji harus terbukti masuk ke kotak masuk atau folder spam akun Admin dan Super Admin staging.
- Konfigurasi tidak boleh dianggap berhasil hanya karena aplikasi tidak melempar exception atau email terlihat pada log lokal.
- Jika menggunakan Gmail, credential harus menggunakan mekanisme yang didukung Google seperti App Password pada akun yang memenuhi syarat, bukan password login utama.

### 11.3 Perilaku berdasarkan driver

| Kondisi | Perilaku yang diharapkan |
|---|---|
| Mailer pengiriman valid dan provider menerima pesan | Challenge tetap aktif dan pengguna diarahkan ke halaman OTP. |
| Mailer `log` pada local development | Email tidak masuk ke kotak masuk; kode hanya ditulis ke log. Kondisi ini tidak valid untuk UAT/production. |
| SMTP/provider menolak atau timeout | Pengguna tetap guest, challenge dibatalkan/tidak dapat dipakai, pesan gangguan email ditampilkan, dan kegagalan diaudit tanpa OTP. |
| Konfigurasi berubah tetapi cache belum diperbarui | Deployment wajib membersihkan/membangun ulang cache sebelum pengujian diulang. |
| Tabel OTP/audit belum dimigrasikan | Deployment dianggap belum lengkap; migration harus dijalankan sebelum fitur digunakan. Logout tetap harus dapat berlangsung meskipun penyimpanan audit sedang terganggu. |

## 12. Keamanan dan Privasi

- Seluruh halaman dan endpoint autentikasi produksi wajib menggunakan HTTPS.
- OTP disimpan dalam bentuk hash dan tidak pernah ditampilkan kembali oleh server.
- Semua endpoint login, verifikasi, dan resend dilindungi CSRF serta rate limit.
- Rate limit harus mempertimbangkan kombinasi akun/email dan IP agar tidak mudah dilewati atau digunakan untuk memblokir satu akun secara permanen.
- Pesan pada tahap login tidak boleh memungkinkan enumerasi akun.
- Email ditampilkan dalam bentuk tersamarkan pada halaman OTP.
- Sistem tidak boleh mengirim OTP jika kata sandi salah.
- Secret SMTP/API harus berasal dari environment dan tidak masuk repository.
- Log tidak boleh memuat OTP, kata sandi, secret email, atau isi session.
- Driver mail `log` dilarang pada production karena isi email OTP dapat tersimpan pada log aplikasi.
- Jika driver `log` pernah dipakai untuk OTP, log terkait harus diperlakukan sebagai data sensitif, dibatasi aksesnya, dan dirotasi/dihapus mengikuti kebijakan retensi setelah SMTP aktif.
- Setelah OTP berhasil, seluruh tantangan aktif lain untuk alur login yang sama dibatalkan.

## 13. Audit dan Observabilitas

Sistem mencatat minimal:

- Kredensial login gagal.
- OTP diminta dan dikirim, tanpa mencatat kodenya.
- Pengiriman OTP gagal.
- OTP salah, kedaluwarsa, atau terkena batas percobaan.
- OTP berhasil dan login selesai.
- Permintaan kirim ulang terkena rate limit.
- Logout.

Log minimal memuat timestamp, user ID jika telah diketahui, IP, user agent, jenis kejadian, dan hasil. Informasi ini hanya dapat diakses pihak yang berwenang.

## 14. Kebutuhan Nonfungsional

- Permintaan login harus memberi respons UI tanpa menunggu tanpa batas; timeout pengiriman email harus ditentukan pada implementasi.
- Pengiriman email idealnya menggunakan queue agar waktu respons stabil, tetapi kegagalan antrean harus dapat dipantau.
- Halaman login dan OTP harus responsif serta dapat digunakan dengan keyboard.
- Input memiliki label, pesan kesalahan terkait, fokus yang jelas, dan status yang dapat dibaca assistive technology.
- Waktu kedaluwarsa ditentukan oleh server; countdown browser hanya bersifat informatif.
- Sistem tetap mendukung konfigurasi SMTP atau penyedia email yang didukung Laravel.

## 15. Kriteria Penerimaan

### AC-01 — Kredensial benar

**Given** pengguna Admin atau Super Admin memasukkan kredensial benar  
**When** formulir login dikirim  
**Then** OTP dikirim dan pengguna diarahkan ke halaman verifikasi tanpa memperoleh sesi login penuh.

### AC-02 — Kredensial salah

**Given** email atau kata sandi salah  
**When** formulir login dikirim  
**Then** OTP tidak dibuat, email tidak dikirim, dan pesan umum ditampilkan.

### AC-03 — OTP benar

**Given** tantangan masih aktif  
**When** pengguna memasukkan OTP yang benar sebelum 5 menit  
**Then** OTP ditandai telah digunakan, session ID diregenerasi, dan pengguna masuk ke intended URL atau dashboard admin.

### AC-04 — OTP salah

**Given** tantangan masih aktif  
**When** pengguna memasukkan OTP salah  
**Then** login ditolak, jumlah percobaan bertambah, dan pesan aman ditampilkan.

### AC-05 — OTP kedaluwarsa

**Given** OTP berusia lebih dari 5 menit  
**When** pengguna mencoba memverifikasi  
**Then** kode ditolak dan pengguna ditawari pengiriman kode baru.

### AC-06 — OTP digunakan ulang

**Given** OTP telah berhasil digunakan  
**When** kode yang sama dikirim kembali  
**Then** kode ditolak dan tidak membuat sesi tambahan.

### AC-07 — Kirim ulang

**Given** cooldown telah selesai dan batas resend belum tercapai  
**When** pengguna meminta kode baru  
**Then** kode baru dikirim dan seluruh kode sebelumnya tidak berlaku.

### AC-08 — Batas percobaan

**Given** pengguna telah salah memasukkan OTP lima kali  
**When** percobaan berikutnya dilakukan  
**Then** tantangan dibatalkan dan pengguna diwajibkan login kembali.

### AC-09 — Proteksi route

**Given** kredensial benar tetapi OTP belum diverifikasi  
**When** pengguna mencoba membuka route terproteksi  
**Then** akses ditolak dan pengguna diarahkan ke verifikasi atau login.

### AC-10 — Remember login

**Given** pengguna memilih “Ingat Sesi Saya”  
**When** OTP berhasil diverifikasi  
**Then** sesi persisten dibuat sesuai kebijakan aplikasi; jika OTP gagal, sesi persisten tidak dibuat.

### AC-11 — Gangguan email

**Given** penyedia email gagal menerima permintaan pengiriman  
**When** sistem mencoba mengirim OTP  
**Then** pengguna tidak dianggap login, mendapat pesan kegagalan sementara, dan kejadian tercatat untuk operasional.

### AC-12 — Konsistensi role

**Given** pengguna memiliki role Admin atau Super Admin  
**When** melakukan login baru  
**Then** keduanya mengikuti alur OTP yang sama tanpa pengecualian.

### AC-13 — Konfigurasi email production

**Given** fitur OTP diaktifkan pada staging atau production  
**When** pemeriksaan konfigurasi deployment dilakukan  
**Then** mailer menggunakan transport pengiriman nyata, alamat pengirim terverifikasi, credential berasal dari environment/secret manager, dan email uji diterima oleh akun administrator.

### AC-14 — Mailer log tidak dianggap terkirim

**Given** aplikasi masih menggunakan `MAIL_MAILER=log`  
**When** pengguna meminta OTP  
**Then** tim operasional memahami bahwa email tidak dikirim ke kotak masuk, environment tersebut tidak dinyatakan siap production, dan log yang mungkin memuat OTP diperlakukan sebagai data sensitif.

### AC-15 — Deployment database belum lengkap

**Given** migration tabel challenge atau audit OTP belum dijalankan  
**When** deployment diverifikasi  
**Then** deployment dinyatakan gagal dan migration harus diselesaikan sebelum login OTP digunakan; kegagalan audit tidak boleh membuat pengguna yang sudah login gagal logout.

## 16. Metrik Keberhasilan

Metrik dievaluasi setelah peluncuran dengan target awal berikut:

- Minimal 95% email OTP diterima dalam 60 detik, berdasarkan data penyedia email.
- Minimal 90% tantangan OTP yang sah selesai pada pengiriman pertama.
- Tingkat kegagalan teknis pengiriman OTP kurang dari 1%.
- Tidak ada akses route admin dari sesi yang belum menyelesaikan OTP.
- Seluruh kejadian keamanan pada Bagian 13 dapat ditelusuri tanpa mengekspos OTP.

Target dapat disesuaikan setelah baseline produksi tersedia.

## 17. Rencana Peluncuran

1. Konfigurasikan dan verifikasi domain/alamat pengirim email pada lingkungan staging.
2. Pastikan bahwa staging/production tidak menggunakan mailer `log` dan seluruh secret mail berasal dari environment/secret manager.
3. Jalankan seluruh migration OTP dan konfirmasi tabel challenge/audit serta kolom waktu login tersedia.
4. Bersihkan atau bangun ulang cache konfigurasi Laravel setelah perubahan environment.
5. Implementasikan penyimpanan tantangan, email OTP, route, controller, UI, middleware, rate limit, dan audit log.
6. Jalankan pengujian unit, feature, integrasi mail, dan pengujian keamanan dasar.
7. Kirim email uji end-to-end dan pastikan diterima akun Admin serta Super Admin staging dalam target waktu.
8. Uji pengguna dengan akun Admin dan Super Admin di staging.
9. Verifikasi monitoring queue/provider, kegagalan email, rate limit, serta fallback audit.
10. Informasikan administrator bahwa akses ke email terdaftar menjadi wajib.
11. Rilis ke production dan pantau metrik pengiriman serta kegagalan login.

Rollback dilakukan dengan feature flag atau konfigurasi terkontrol, bukan dengan menghapus data autentikasi. Jika OTP dinonaktifkan saat insiden, keputusan dan durasinya harus tercatat.

## 18. Ketergantungan

- Alamat email seluruh administrator valid dan dapat diakses.
- Penyedia email produksi dan identitas pengirim telah dikonfigurasi.
- Queue worker tersedia jika pengiriman dibuat asynchronous.
- Cache/database tersedia untuk rate limit dan tantangan OTP.
- Jam server tersinkronisasi agar masa berlaku konsisten.
- HTTPS aktif pada lingkungan produksi.
- Migration OTP telah dijalankan sebelum route login baru digunakan.
- Cache konfigurasi aplikasi telah diperbarui setelah perubahan `MAIL_*` atau `ADMIN_OTP_*`.
- Transport email production bukan driver `log` atau `array`.

## 19. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Email terlambat atau masuk spam | Admin tidak dapat login tepat waktu | Verifikasi domain, monitor delivery, sediakan resend terkontrol |
| Akun tidak lagi memiliki akses email | Admin terkunci | Prosedur verifikasi manual oleh penanggung jawab berwenang |
| Spam OTP atau serangan brute force | Biaya email dan risiko keamanan | Cooldown, batas percobaan, rate limit per akun dan IP |
| Queue/pengirim email bermasalah | Login seluruh admin terganggu | Monitoring, alert, retry terbatas, runbook insiden |
| OTP bocor melalui log | Pengambilalihan akun | Hash OTP dan sanitasi seluruh log |
| Mailer tetap menggunakan driver `log` | OTP tidak masuk ke email dan kode tersimpan di log | Validasi konfigurasi deployment, larang driver `log` di production, rotasi log sensitif |
| Konfigurasi SMTP berubah tetapi cache lama masih aktif | Aplikasi tetap memakai konfigurasi sebelumnya | Bersihkan/bangun ulang cache konfigurasi dalam prosedur deployment |
| Migration OTP belum dijalankan | Login/logout menghasilkan internal server error | Migration gate sebelum deployment dan audit logger fail-safe agar logout tetap tersedia |
| Perbedaan perilaku antar-role | Celah bypass | Satu alur OTP bersama dan feature test untuk kedua role |

## 20. Keputusan Produk yang Perlu Disahkan

Nilai berikut menjadi baseline PRD dan perlu disetujui pemilik produk sebelum implementasi:

- OTP diwajibkan pada setiap sesi login baru, tanpa trusted device.
- OTP enam digit berlaku selama 5 menit.
- Maksimal 5 percobaan per tantangan.
- Cooldown resend 60 detik dan maksimal 3 pengiriman dalam 15 menit.
- Seluruh Admin dan Super Admin wajib OTP.
- Pemulihan kehilangan akses email dilakukan melalui prosedur manual resmi.
- Sesi yang sudah aktif saat fitur dirilis tidak otomatis diputus.

## 21. Definition of Done

Fitur dinyatakan selesai apabila:

- Seluruh kebutuhan wajib dan kriteria penerimaan lulus di lingkungan staging.
- Tidak ada sesi autentikasi penuh sebelum OTP berhasil.
- Konfigurasi pengirim email produksi telah diverifikasi.
- Staging dan production tidak menggunakan mailer `log`/`array`, cache konfigurasi telah diperbarui, dan email OTP end-to-end terbukti diterima.
- Migration OTP terkonfirmasi berstatus telah dijalankan sebelum aktivasi fitur.
- Test otomatis mencakup alur berhasil, gagal, kedaluwarsa, reuse, resend, rate limit, remember login, dan kedua role.
- Audit log dan monitoring dapat digunakan tanpa membocorkan informasi sensitif.
- Runbook kegagalan email dan pemulihan akses administrator tersedia.
- Dokumentasi operasional dan komunikasi pengguna telah disetujui.

## 22. Penawaran dan Serah Terima ke Kominfo

Bagian ini menjadi panduan saat fitur ditawarkan, dipresentasikan, diuji, dan diserahkan kepada Dinas Komunikasi dan Informatika (Kominfo). Tahap ini tidak menetapkan harga, kontrak, SLA final, atau pilihan vendor; seluruh keputusan tersebut mengikuti kebijakan dan proses resmi Pemerintah Kabupaten Pasuruan.

### 22.1 Posisi solusi yang ditawarkan

Fitur OTP Email Admin ditawarkan sebagai lapisan keamanan tambahan untuk panel pengelolaan Portal RPJMD. Nilai utama yang perlu disampaikan:

- Password yang benar belum cukup untuk membuka sesi administrator.
- Admin dan Super Admin wajib membuktikan akses ke email terdaftar.
- OTP hanya berlaku lima menit, hanya sekali pakai, dan tidak disimpan dalam plaintext.
- Percobaan, pengiriman ulang, dan frekuensi pengiriman dibatasi.
- Kejadian autentikasi dapat diaudit tanpa menyimpan kode OTP.
- Gangguan audit tidak menghalangi logout, sedangkan kegagalan email tidak membuat pengguna dianggap login.
- Konfigurasi dapat dipindahkan dari email development ke infrastruktur email resmi tanpa mengubah alur bisnis aplikasi.

Fitur ini bukan pengganti kebijakan password, HTTPS, pengamanan server, pengelolaan akun, monitoring, backup, maupun prosedur respons insiden.

### 22.2 Status environment saat penawaran

Pada tahap development, email pribadi pengembang boleh digunakan sementara untuk demonstrasi dengan ketentuan:

- Hanya digunakan pada local atau staging terbatas.
- Credential berupa App Password/token khusus aplikasi, bukan password utama email pribadi.
- Credential hanya disimpan pada `.env` atau secret store dan tidak masuk repository, screenshot, dokumen penawaran, maupun rekaman demo.
- Akun penerima demo menggunakan alamat yang telah disetujui dan tidak berisi data produksi.
- Penggunaan email pribadi dinyatakan secara terbuka sebagai konfigurasi sementara, bukan rancangan production.
- Seluruh credential pribadi dihapus dari server dan dicabut setelah Kominfo menyediakan pengirim resmi atau proses serah terima selesai.

Status demonstrasi tidak boleh disebut siap production apabila masih menggunakan email pribadi, domain yang belum diverifikasi, HTTP, atau monitoring yang belum aktif.

### 22.3 Materi yang disiapkan untuk Kominfo

Paket penawaran/technical review minimal memuat:

1. Ringkasan masalah, tujuan, ruang lingkup, dan manfaat OTP.
2. Diagram alur login: kredensial → challenge → email → verifikasi → sesi admin.
3. Matriks requirement FR-01–FR-12 dan acceptance criteria AC-01–AC-15.
4. Daftar route, middleware, tabel, konfigurasi, scheduler, dan mekanisme rate limit.
5. Bukti test otomatis dan hasil UAT tanpa menampilkan OTP, password, cookie, atau secret.
6. Contoh tampilan login, verifikasi, error, resend, dan email dengan data dummy.
7. Runbook gangguan email, rollback terkontrol, dan pemulihan akses administrator.
8. Daftar kebutuhan infrastruktur serta keputusan yang harus disediakan Kominfo.
9. Daftar risiko, limitation, pekerjaan tersisa, dan rencana transisi production.
10. Daftar komponen/dependency beserta versi dan kebutuhan pemeliharaannya.

Kode OTP asli, `.env`, database dump produksi, log mentah, credential SMTP, App Password, dan data pribadi tidak boleh dimasukkan ke materi penawaran.

### 22.4 Skenario demonstrasi

Demo menggunakan akun dan data khusus staging dengan urutan berikut:

1. Tampilkan bahwa password salah tidak membuat challenge dan tidak mengirim email.
2. Login dengan akun Admin menggunakan password benar dan tunjukkan bahwa dashboard belum dapat diakses.
3. Tunjukkan email OTP diterima melalui transport email development/staging.
4. Tunjukkan alamat email yang disamarkan dan countdown pada halaman verifikasi.
5. Masukkan kode salah untuk menunjukkan penolakan dan pencatatan percobaan.
6. Masukkan kode benar untuk menunjukkan sesi dibuat dan intended URL dipulihkan.
7. Logout, lalu tunjukkan bahwa route admin kembali terlindungi.
8. Tunjukkan kode yang sudah digunakan tidak dapat dipakai ulang.
9. Tunjukkan resend setelah cooldown membatalkan kode sebelumnya.
10. Jelaskan batas lima percobaan, tiga pengiriman per 15 menit, audit, pruning, dan fallback insiden.
11. Ulangi happy path menggunakan role Super Admin untuk membuktikan tidak ada pengecualian role.

Demo tidak dilakukan pada akun administrator production dan tidak boleh menampilkan panel provider email atau secret pada layar yang dibagikan.

### 22.5 Kebutuhan dan keputusan dari Kominfo

Sebelum implementasi production, Kominfo perlu menentukan atau menyediakan:

- Domain dan subdomain resmi portal.
- HTTPS/TLS certificate dan kebijakan reverse proxy.
- Alamat pengirim resmi serta nama pengirim.
- SMTP relay atau penyedia transactional email yang diizinkan.
- Credential melalui kanal secret management resmi.
- Konfigurasi SPF, DKIM, dan DMARC serta pihak yang bertanggung jawab atas DNS.
- Alamat email resmi seluruh Admin dan Super Admin.
- Kebijakan password, masa sesi, retensi challenge, audit log, IP, dan user agent.
- Batas pengiriman/provider, monitoring delivery, alert, serta penanggung jawab insiden.
- Infrastruktur database, cache, scheduler/cron, queue bila digunakan, backup, dan sinkronisasi waktu server.
- Prosedur permintaan/perubahan/pencabutan akun dan pemulihan ketika administrator kehilangan akses email.
- PIC teknis, PIC keamanan, approver UAT, serta jalur eskalasi.

Nilai konfigurasi teknis dapat memakai baseline PRD, tetapi keputusan final harus dicatat dan disetujui pihak berwenang.

### 22.6 Tahapan adopsi

#### Tahap A — Presentasi dan technical review

- Presentasikan manfaat, arsitektur, alur keamanan, dependency, dan risiko.
- Konfirmasi kesesuaian dengan kebijakan keamanan informasi serta arsitektur Kominfo.
- Catat perubahan requirement dan pihak yang menyetujuinya.

#### Tahap B — Staging Kominfo

- Deploy pada environment staging yang dikelola atau disetujui Kominfo.
- Gunakan pengirim resmi/non-pribadi bila sudah tersedia.
- Jalankan migration, konfigurasi mail, HTTPS, scheduler, cache, dan monitoring.
- Lakukan smoke test pengiriman ke Admin dan Super Admin.

#### Tahap C — UAT dan security review

- Jalankan seluruh acceptance criteria bersama perwakilan Bapperida dan Kominfo.
- Uji rate limit, expiry, reuse, resend, session regeneration, role, audit, kegagalan email, dan logout.
- Catat bukti hasil, defect, keputusan penerimaan, serta risiko yang diterima.

#### Tahap D — Production dan masa pendampingan

- Ganti seluruh credential development dengan credential resmi.
- Pastikan email pribadi pengembang tidak lagi digunakan atau memiliki akses.
- Deploy melalui prosedur perubahan resmi dan jalankan smoke test tanpa mengekspos OTP.
- Pantau delivery, error, rate limit, dan audit selama masa stabilisasi yang disepakati.
- Serahkan runbook, akses operasional, dokumentasi, dan knowledge transfer kepada PIC Kominfo.

### 22.7 Checklist serah terima

| Item | Bukti penerimaan | Penanggung jawab final |
|---|---|---|
| Source code dan riwayat versi | Repository resmi dapat diakses | Kominfo menetapkan |
| PRD dan arsitektur | Dokumen disetujui | Bapperida/Kominfo |
| Migration database | Status migration berhasil | Tim infrastruktur/aplikasi |
| Domain dan HTTPS | URL resmi valid melalui HTTPS | Kominfo |
| Pengirim email resmi | Email OTP diterima dan autentikasi domain lolos | Kominfo |
| Secret SMTP/provider | Tersimpan pada secret environment, bukan repository | Kominfo |
| Scheduler dan pruning | Jadwal `admin-otp:prune` terpantau | Tim operasi |
| Monitoring dan alert | Simulasi kegagalan terdeteksi | Tim operasi/keamanan |
| Backup dan restore | Bukti uji pemulihan tersedia | Tim infrastruktur |
| UAT Admin/Super Admin | Berita acara atau hasil UAT | Bapperida/Kominfo |
| Runbook dan eskalasi | PIC serta jalur eskalasi disahkan | Kominfo |
| Credential development | Dihapus dan App Password pribadi dicabut | Pengembang dan Kominfo |

### 22.8 Kriteria penerimaan Kominfo

Serah terima fitur OTP dapat dinyatakan selesai apabila:

- Kominfo menyetujui arsitektur, dependency, konfigurasi, dan risiko yang terdokumentasi.
- Seluruh AC-01–AC-15 lulus pada staging yang representatif.
- Email OTP diterima melalui pengirim resmi dan tidak bergantung pada email pribadi pengembang.
- Route admin tidak dapat diakses sebelum OTP berhasil.
- Audit, monitoring, cleanup, backup, dan prosedur insiden telah diverifikasi.
- Tidak ada secret atau OTP pada repository, dokumen serah terima, screenshot, dan log operasional yang dibagikan.
- Akun serta akses pengembang telah dikurangi atau dicabut sesuai kesepakatan setelah masa pendampingan.
- Dokumen penerimaan, PIC operasional, serta tanggung jawab pemeliharaan telah disahkan.

### 22.9 Batas tanggung jawab yang harus disepakati

Dokumen penawaran atau berita acara serah terima harus menyatakan dengan jelas:

- Pihak yang mengelola source code, deployment, database, DNS, email provider, dan secret.
- Pihak yang membayar serta memantau kuota/biaya provider email bila ada.
- SLA dukungan, jam layanan, jalur insiden, dan masa garansi/pemeliharaan.
- Tanggung jawab pembaruan dependency serta penanganan kerentanan keamanan.
- Kebijakan kepemilikan data, retensi audit, backup, dan pemulihan bencana.
- Proses perubahan konfigurasi atau penonaktifan sementara OTP.

Butir komersial dan organisasi tersebut tidak boleh diasumsikan oleh implementasi teknis; semuanya memerlukan persetujuan tertulis.

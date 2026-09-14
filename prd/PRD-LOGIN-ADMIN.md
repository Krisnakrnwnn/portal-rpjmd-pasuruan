# Product Requirements Document

## Redesign Halaman Login Administrator Portal RPJMD

| Informasi | Nilai |
|---|---|
| Produk | Portal Manajemen RPJMD Kabupaten Pasuruan |
| Fitur | Antarmuka Login dan Verifikasi OTP Administrator |
| Status | Draft untuk persetujuan desain |
| Versi | 1.0 |
| Tanggal | 14 September 2026 |
| Pemilik produk | Bapperida Kabupaten Pasuruan |
| Referensi utama | `prd/PRD-OTP-EMAIL-ADMIN.md` |

> Dokumen ini hanya mendefinisikan kebutuhan produk dan desain. Penyusunan PRD ini tidak mengubah Blade, CSS, JavaScript, route, controller, atau perilaku autentikasi yang ada.

## 1. Ringkasan

Halaman login administrator perlu dirapikan agar memiliki hierarki visual yang jelas, menggunakan identitas resmi Bapperida, mudah digunakan pada desktop maupun perangkat seluler, dan konsisten dengan alur OTP Email Admin.

Redesign mencakup satu sistem tampilan autentikasi yang digunakan bersama oleh:

- Login email dan kata sandi.
- Verifikasi OTP email.
- Status keberhasilan dan kegagalan yang berhubungan dengan kedua tahap tersebut.

Perubahan visual tidak boleh mengubah kontrak keamanan atau lifecycle OTP yang telah ditetapkan dalam `prd/PRD-OTP-EMAIL-ADMIN.md`.

## 2. Kondisi Saat Ini

Hasil review implementasi saat ini:

- Header autentikasi masih menggunakan kotak berhuruf “R”, bukan logo Bapperida yang tersedia.
- Identitas “RPJMD Kabupaten Pasuruan” sudah ada, tetapi hubungan dengan Bapperida belum terlihat kuat.
- Form berada di atas latar hero dengan efek glass dan animasi besar; tampilan menarik tetapi hierarki, keterbacaan, dan fokus form dapat lebih konsisten.
- Card menggunakan radius dan spacing besar sehingga ruang vertikal cepat habis pada layar pendek.
- Input sudah memiliki label, ikon, autocomplete, dan focus state.
- Opsi “Ingat Sesi Saya” dan “Lupa Sandi” sudah tersedia.
- Tombol login sudah memakai label “Lanjutkan”, sesuai flow dua tahap.
- Halaman OTP sudah memiliki masked email, input enam digit, countdown, resend, loading, dan pembatalan.
- Pesan error/status masih berupa teks sederhana dan belum memiliki pola alert visual yang konsisten.
- Tahun copyright masih ditulis statis.
- Guest layout dipakai bersama halaman autentikasi lain sehingga perubahan shell harus diuji agar tidak merusak forgot password, reset password, registrasi, verifikasi email, dan konfirmasi password.

## 3. Tujuan

- Menampilkan identitas resmi Bapperida Kabupaten Pasuruan pada seluruh halaman autentikasi administrator.
- Membuat form login lebih rapi, tenang, fokus, dan mudah dipindai.
- Memberi pemahaman bahwa login memiliki dua tahap tanpa menjanjikan OTP sebelum kredensial berhasil.
- Menyatukan tampilan Login dan Verifikasi OTP dalam satu design system.
- Memastikan seluruh state autentikasi mudah dipahami dan aksesibel.
- Menjaga antarmuka layak dipresentasikan serta diserahterimakan kepada Kominfo.

## 4. Bukan Tujuan

Redesign ini tidak mencakup:

- Perubahan autentikasi, lifecycle, masa berlaku, atau algoritma OTP.
- Login tanpa password, social login, SMS OTP, atau trusted device.
- Perubahan role dan hak akses Admin/Super Admin.
- Perubahan dashboard admin atau halaman publik.
- Pembuatan logo atau identitas visual baru.
- Penggunaan alamat email pribadi pada UI.
- Penyimpanan atau penampilan OTP pada client selain input yang diketik pengguna.

## 5. Pengguna

### 5.1 Admin

Masuk untuk mengelola konten, dokumen, galeri, capaian, profil, aspirasi, dan basis pengetahuan chatbot.

### 5.2 Super Admin

Mengikuti tampilan dan flow yang sama dengan Admin, termasuk OTP, lalu memperoleh akses tambahan sesuai otorisasi backend.

Antarmuka tidak boleh memberi kesan bahwa Super Admin dapat melewati OTP.

## 6. Prinsip Desain

1. **Resmi dan dapat dipercaya** — identitas instansi terlihat jelas tanpa dekorasi berlebihan.
2. **Fokus pada tugas** — form dan aksi utama menjadi pusat perhatian.
3. **Tenang dan rapi** — gunakan whitespace, alignment, dan skala tipografi yang konsisten.
4. **Progresif** — pengguna memahami bahwa login terdiri dari pemeriksaan kredensial lalu verifikasi email.
5. **Aman tanpa menakutkan** — pesan keamanan singkat, jelas, dan tidak membocorkan informasi akun.
6. **Konsisten** — shell, komponen, warna, button, input, alert, dan spacing sama pada Login dan OTP.
7. **Responsif dan aksesibel** — tetap usable pada layar kecil, zoom, keyboard, dan assistive technology.

## 7. Aset dan Identitas Visual

### 7.1 Aset tersedia

| Aset | Penggunaan yang direncanakan |
|---|---|
| `public/Logo Bapperida Kab Pasuruan.png` | Wordmark utama pada permukaan terang/card login |
| `public/Logo Bapperida Kab Pasuruan Putih.png` | Alternatif pada panel/latar biru gelap |
| `public/logo.png` | Simbol ringkas untuk favicon, layar sangat sempit, atau fallback |
| `public/hero.png` | Visual pendukung panel informasi, bukan latar yang mengganggu form |

### 7.2 Aturan penggunaan logo

- Kotak berhuruf “R” harus diganti dengan logo resmi yang tersedia.
- Gunakan wordmark berwarna pada latar putih/terang dan wordmark putih pada latar biru gelap.
- Logo harus memakai `object-contain` dan mempertahankan rasio asli; tidak boleh ditarik, dipotong, atau diberi filter warna yang mengubah identitas.
- Sediakan ruang kosong di sekeliling logo minimal setara 25% tinggi simbol.
- Logo harus memiliki teks alternatif yang bermakna, misalnya “Bapperida Kabupaten Pasuruan”.
- Logo tidak boleh menjadi satu-satunya cara menyampaikan nama aplikasi; teks “Portal Manajemen RPJMD” tetap tersedia.
- Bila canvas aset memiliki whitespace berlebihan, aset boleh dioptimalkan/crop secara non-destruktif selama bentuk logo tidak berubah dan file sumber tetap dipertahankan.

### 7.3 Palet dan tipografi

- Warna utama mengikuti identitas yang sudah ada: biru tua, biru, cyan/turquoise, putih, dan slate netral.
- Biru tua digunakan untuk kepercayaan dan area brand; biru utama untuk tombol/tautan; merah hanya untuk error; hijau hanya untuk success.
- Hindari gradient berlebihan pada semua elemen. Gradient boleh dipakai secara terbatas pada panel brand atau tombol utama jika kontras tetap memenuhi standar.
- Gunakan satu keluarga font antarmuka secara konsisten. Implementasi saat ini memuat Outfit tetapi theme utama menyebut Figtree; tahap implementasi harus memilih salah satu dan menghindari dua identitas tipografi yang tidak disengaja.

## 8. Arsitektur Informasi

### 8.1 Layout desktop

Gunakan layout dua panel pada viewport besar:

| Panel | Proporsi | Isi |
|---|---:|---|
| Brand/informasi | 52–58% | Logo resmi, nama portal, deskripsi singkat, indikator keamanan dua tahap, visual hero yang terkontrol |
| Form autentikasi | 42–48% | Judul tahap, deskripsi, status/alert, field, aksi utama, dan bantuan |

Ketentuan:

- Form berada pada permukaan putih solid atau nyaris solid agar keterbacaan stabil.
- Lebar efektif form maksimum sekitar 420–460 px.
- Panel brand boleh memakai `hero.png` dengan overlay biru yang menjamin keterbacaan teks.
- Tidak perlu card glass bertumpuk di tengah layar jika panel form sudah menjadi permukaan utama.
- Konten utama tetap terlihat pada tinggi layar 600 px dengan scroll vertikal bila diperlukan.

### 8.2 Layout tablet

- Layout boleh tetap dua panel dengan proporsi seimbang atau beralih ke satu kolom sesuai ruang.
- Panel brand disederhanakan; elemen dekoratif yang tidak penting dapat disembunyikan.
- Form tidak boleh lebih sempit dari 360 px bila ruang tersedia.

### 8.3 Layout mobile

- Gunakan satu kolom dengan logo dan nama portal di bagian atas.
- Visual hero besar dihilangkan atau dijadikan aksen tipis agar tidak mendorong form ke bawah.
- Padding horizontal minimum 20 px dan tidak menyebabkan overflow pada lebar 320 px.
- Tombol utama memenuhi lebar container dan memiliki tinggi target sentuh minimal 44 px.
- Footer tidak boleh menutupi form dan tahun harus dinamis.

## 9. Spesifikasi Halaman Login

### 9.1 Panel brand

Harus menampilkan:

- Logo resmi Bapperida.
- Label “Portal Manajemen RPJMD”.
- Teks “Kabupaten Pasuruan”.
- Deskripsi singkat: “Kelola informasi perencanaan pembangunan daerah secara aman dan terpusat.”
- Penanda keamanan singkat seperti “Login administrator dilindungi verifikasi email dua tahap.”

Panel tidak boleh menampilkan alamat email pengirim, credential, versi framework, atau detail infrastruktur.

### 9.2 Header form

- Eyebrow/label konteks: “Akses Administrator”.
- Judul utama: “Masuk ke Portal Manajemen”.
- Deskripsi: “Masukkan email dan kata sandi untuk melanjutkan ke verifikasi email.”
- Copy tidak boleh menyatakan OTP telah dikirim sebelum kredensial berhasil diperiksa.

### 9.3 Field email

- Label terlihat: “Alamat Email”.
- Tipe `email`, autocomplete `username`, dan keyboard email pada mobile.
- Placeholder hanya sebagai contoh, bukan pengganti label.
- Mempertahankan input email saat validasi gagal, tetapi tidak mempertahankan password.
- Error berada tepat di bawah field dan terhubung secara aksesibel.

### 9.4 Field kata sandi

- Label terlihat: “Kata Sandi”.
- Tipe awal `password` dan autocomplete `current-password`.
- Menyediakan tombol tampilkan/sembunyikan password yang dapat digunakan keyboard, memiliki accessible name, dan tidak mengubah nilai input.
- Tautan “Lupa Sandi?” berada dekat konteks field tanpa bersaing dengan tombol utama.
- Error berada tepat di bawah field.

### 9.5 Opsi remember

- Checkbox dan label “Ingat Sesi Saya” tetap tersedia.
- Teks bantuan opsional menjelaskan bahwa opsi baru berlaku setelah OTP berhasil.
- Area klik mencakup checkbox dan label.
- State checked memiliki indikator selain perubahan warna.

### 9.6 Tombol utama

- Label: “Lanjutkan”.
- Tidak menggunakan label “Masuk” atau “Kirim OTP” pada tahap kredensial.
- Saat submit, tombol disabled, label berubah menjadi “Memeriksa...”, dan indikator loading tampil tanpa menggeser layout.
- Submit berulang dicegah di client, tetapi backend tetap menjadi sumber validasi utama.

### 9.7 Bantuan dan navigasi

- Sediakan tautan kembali ke portal publik dengan label jelas, misalnya “Kembali ke Portal RPJMD”.
- Pertahankan tautan “Lupa Sandi?” bila route tersedia.
- Jangan tampilkan tautan registrasi pada halaman login administrator kecuali registrasi publik telah disetujui secara eksplisit.
- Tampilkan kontak bantuan resmi hanya melalui konfigurasi, bukan hardcode email pribadi.

## 10. Spesifikasi Halaman Verifikasi OTP

Halaman OTP menggunakan shell visual yang sama dengan login dan seluruh kebutuhan `prd/PRD-OTP-EMAIL-ADMIN.md` tetap berlaku.

### 10.1 Header tahap

- Eyebrow/indikator tahap: “Langkah 2 dari 2”.
- Judul: “Verifikasi Email”.
- Penjelasan menyebut kode enam digit dan email yang telah disamarkan.
- Jangan menampilkan alamat email lengkap.

### 10.2 Input OTP

- Mendukung enam digit, angka nol di awal, paste, `inputmode="numeric"`, dan `autocomplete="one-time-code"`.
- Rekomendasi tampilan adalah satu input yang secara visual terbagi menjadi enam posisi; hindari enam field terpisah yang menyulitkan paste dan screen reader.
- Fokus, invalid, disabled, dan loading state terlihat jelas.
- Error server muncul dekat input dan diumumkan melalui `aria-live`.

### 10.3 Waktu berlaku dan resend

- Countdown expiry diberi label, bukan hanya angka tanpa konteks.
- Countdown browser hanya informatif; keputusan expiry tetap dari server.
- Saat kedaluwarsa, tampilkan pesan “Kode telah kedaluwarsa. Silakan kirim kode baru.”
- “Kirim Ulang Kode” disabled selama cooldown dan menampilkan detik tersisa.
- Loading resend berbeda dari loading verifikasi.
- Kode lama langsung tidak valid setelah resend berhasil.

### 10.4 Aksi sekunder

- Tombol utama: “Verifikasi dan Masuk”.
- Aksi “Kembali ke Login” membatalkan challenge melalui request aman, bukan tautan GET yang hanya mengubah tampilan.
- Urutan fokus: input OTP → verifikasi → resend → kembali ke login.

## 11. Komponen UI Bersama

### 11.1 Authentication shell

Shell menyediakan panel brand, container form, logo, footer dinamis, title halaman, dan slot konten tanpa menduplikasi markup antara Login dan OTP.

### 11.2 Input

- Tinggi konsisten 48–52 px.
- Radius 10–14 px, tidak terlalu bulat.
- Border netral terlihat pada state idle.
- Focus ring minimal 2 px dengan kontras memadai.
- Ikon dekoratif diberi `aria-hidden="true"`.

### 11.3 Alert

| Jenis | Tampilan | Perilaku aksesibel |
|---|---|---|
| Error | Latar merah sangat muda, ikon, judul/pesan merah gelap | `role="alert"`; fokus diarahkan bila submit gagal |
| Success | Latar hijau sangat muda, ikon, teks hijau gelap | `role="status"` atau `aria-live="polite"` |
| Informasi | Latar biru muda dan teks biru tua | Tidak menginterupsi screen reader |
| Warning/cooldown | Latar amber muda dan teks gelap | Menyebut tindakan berikutnya dan waktu tunggu |

Pesan tidak boleh hanya dibedakan berdasarkan warna.

### 11.4 Button

- Primary untuk satu aksi utama per tahap.
- Secondary/tertiary untuk resend, batal, dan kembali ke portal.
- State hover, focus-visible, active, loading, dan disabled harus tersedia.
- Animasi tidak boleh menggeser tombol atau memakai transform berlebihan.

## 12. State dan Pesan

### 12.1 Login

| State | Respons UI |
|---|---|
| Idle | Form siap, email autofocus pada desktop |
| Field invalid | Error spesifik format/wajib dekat field |
| Kredensial salah/non-admin | “Email atau kata sandi tidak sesuai.” tanpa enumerasi akun |
| Rate limited | Pesan waktu tunggu yang aman |
| Loading | Tombol disabled dan “Memeriksa...” |
| Email gagal dikirim | Alert “Kode verifikasi belum dapat dikirim. Silakan coba beberapa saat lagi.”; pengguna tetap guest |
| Berhasil | Redirect ke OTP; tidak menampilkan dashboard sesaat pun |

### 12.2 OTP

| State | Respons UI |
|---|---|
| Kode aktif | Countdown expiry dan cooldown resend tampil |
| Format salah | Instruksi enam digit angka |
| OTP salah | “Kode verifikasi tidak sesuai.” |
| OTP kedaluwarsa | Pesan expiry dan resend tersedia |
| Batas percobaan | Redirect login dengan pesan “Terlalu banyak percobaan. Silakan login kembali.” |
| Resend cooldown | Tombol disabled dan detik tersisa |
| Resend berhasil | Success alert; countdown di-reset |
| Resend gagal | Error sementara; pengguna tidak login |
| Verifikasi loading | Tombol verifikasi disabled dan “Memverifikasi...” |
| Berhasil | Redirect intended URL atau dashboard sesuai role |
| Challenge tidak aktif | Redirect login tanpa membocorkan identitas akun |

## 13. Kebutuhan Fungsional UI

| ID | Kebutuhan | Prioritas |
|---|---|---|
| UI-01 | Shell autentikasi menggunakan logo resmi Bapperida dan menghapus simbol “R” buatan. | Wajib |
| UI-02 | Login dan OTP menggunakan sistem layout, komponen, warna, serta spacing yang konsisten. | Wajib |
| UI-03 | Login mempertahankan email, password, remember, lupa sandi, dan tombol “Lanjutkan”. | Wajib |
| UI-04 | Password memiliki kontrol tampilkan/sembunyikan yang aksesibel. | Wajib |
| UI-05 | Form mencegah submit berulang dan menampilkan loading state. | Wajib |
| UI-06 | Error, success, info, dan warning memakai komponen alert konsisten. | Wajib |
| UI-07 | Halaman OTP memenuhi seluruh state lifecycle dalam PRD OTP. | Wajib |
| UI-08 | Halaman dapat digunakan pada lebar 320 px sampai desktop besar tanpa overflow horizontal. | Wajib |
| UI-09 | Halaman mendukung keyboard, screen reader, zoom 200%, dan reduced motion. | Wajib |
| UI-10 | Tautan kembali ke portal publik tersedia tanpa membatalkan intended URL secara tidak sengaja. | Penting |
| UI-11 | Copyright menggunakan tahun dinamis dan identitas organisasi yang disetujui. | Penting |
| UI-12 | Title browser dan metadata halaman menunjukkan konteks Login/Verifikasi Portal RPJMD. | Penting |
| UI-13 | Registrasi publik tidak dipromosikan dari halaman login administrator. | Wajib |
| UI-14 | Logo dan visual tidak menyebabkan layout shift yang berarti. | Penting |

## 14. Aksesibilitas

- Target WCAG 2.1 AA.
- Semua field memiliki label programatis dan visible label.
- Error terhubung melalui `aria-describedby` dan field invalid menggunakan `aria-invalid="true"`.
- Alert penting menggunakan `role="alert"`; status loading/success menggunakan live region yang sesuai.
- Focus order mengikuti urutan visual dan focus indicator tidak boleh dihilangkan.
- Kontras teks normal minimal 4,5:1 dan teks besar minimal 3:1.
- Logo memiliki alt text; ikon dekoratif disembunyikan dari accessibility tree.
- Countdown tidak diumumkan setiap detik kepada screen reader. Pembaruan diumumkan hanya pada perubahan penting, misalnya cooldown selesai atau OTP kedaluwarsa.
- Animasi background dihentikan jika pengguna memilih `prefers-reduced-motion: reduce`.
- Form tetap dapat digunakan tanpa JavaScript; JavaScript hanya meningkatkan loading/countdown/paste experience.

## 15. Responsivitas dan Kompatibilitas

- Minimum viewport: 320 × 568 px.
- Breakpoint desain diuji minimal pada 320, 375, 768, 1024, dan 1440 px.
- Mendukung dua versi terbaru Chrome, Edge, Firefox, Safari, dan browser mobile umum.
- Keyboard virtual tidak boleh menyembunyikan input aktif atau tombol submit secara permanen.
- Pada landscape dengan tinggi terbatas, halaman harus dapat di-scroll.
- Gambar hero dan logo memiliki ukuran/resolusi yang sesuai serta tidak mengunduh aset yang jauh lebih besar dari kebutuhan tanpa optimasi.

## 16. Keamanan dan Privasi UI

- UI tidak boleh membedakan email tidak terdaftar, password salah, atau role tidak diizinkan.
- Password tidak pernah dimasukkan kembali ke HTML setelah submit gagal.
- Email pada OTP selalu disamarkan.
- OTP tidak disimpan di local storage, session storage, query string, analytics, atau markup tersembunyi.
- Halaman tidak memuat script pihak ketiga baru tanpa review keamanan.
- Autocomplete mengikuti standar: `username`, `current-password`, dan `one-time-code`.
- Kontak bantuan berasal dari konfigurasi resmi dan bukan email pribadi pengembang pada production.
- Tombol kembali/batal tidak boleh meninggalkan challenge aktif bila kontrak backend mensyaratkan pembatalan.

## 17. Kinerja

- Target LCP halaman login <= 2,5 detik pada p75 jaringan seluler yang wajar.
- Target CLS <= 0,1.
- Logo dan hero dioptimalkan ke format/ukuran yang tepat tanpa mengubah identitas visual.
- Konten form utama tetap dapat dirender bila gambar hero gagal dimuat.
- Hindari library UI/animasi baru untuk redesign ini; gunakan Blade, Tailwind, dan JavaScript ringan yang sudah tersedia.
- Font memiliki fallback sistem dan tidak menghalangi render form.

## 18. Acceptance Criteria

### AC-LOGIN-01 — Logo resmi

**Given** halaman login atau OTP dibuka  
**When** header/panel brand dirender  
**Then** logo resmi Bapperida terlihat dengan rasio benar, alt text sesuai, dan simbol “R” buatan tidak digunakan.

### AC-LOGIN-02 — Hierarki desktop

**Given** viewport minimal 1024 px  
**When** halaman login dibuka  
**Then** panel brand dan form tersusun jelas, form berada pada permukaan terbaca, dan aksi utama langsung terlihat.

### AC-LOGIN-03 — Responsif mobile

**Given** viewport 320 px  
**When** halaman login dan OTP digunakan  
**Then** tidak ada overflow horizontal, seluruh kontrol dapat dicapai, dan halaman dapat di-scroll bila tinggi tidak cukup.

### AC-LOGIN-04 — Flow dua tahap

**Given** pengguna belum mengirim kredensial valid  
**When** halaman login ditampilkan  
**Then** copy hanya menjelaskan tahap berikutnya dan tidak menyatakan OTP sudah dikirim.

### AC-LOGIN-05 — Loading login

**Given** pengguna mengirim form login  
**When** request sedang diproses  
**Then** tombol menjadi disabled, label berubah menjadi “Memeriksa...”, dan submit ganda dicegah.

### AC-LOGIN-06 — Password visibility

**Given** pengguna mengisi password  
**When** kontrol tampil/sembunyikan digunakan dengan pointer atau keyboard  
**Then** visibility berubah tanpa mengubah nilai, fokus, atau keamanan submit.

### AC-LOGIN-07 — Error aman

**Given** kredensial salah atau role tidak diizinkan  
**When** server mengembalikan error  
**Then** alert umum tampil, password kosong kembali, dan keberadaan akun tidak terungkap.

### AC-LOGIN-08 — Konsistensi OTP

**Given** kredensial valid  
**When** pengguna diarahkan ke OTP  
**Then** shell tetap konsisten, tahap “2 dari 2” terlihat, email disamarkan, dan input enam digit menjadi fokus utama.

### AC-LOGIN-09 — State OTP

**Given** OTP salah, kedaluwarsa, terkunci, sedang dikirim ulang, atau berhasil  
**When** state tersebut terjadi  
**Then** feedback sesuai PRD OTP ditampilkan tanpa mengekspos data sensitif.

### AC-LOGIN-10 — Aksesibilitas

**Given** pengguna memakai keyboard, screen reader, zoom 200%, atau reduced motion  
**When** menyelesaikan login dan OTP  
**Then** seluruh alur tetap dapat diselesaikan dengan label, fokus, alert, dan kontras yang memenuhi kebutuhan.

### AC-LOGIN-11 — Halaman autentikasi existing

**Given** guest layout digunakan halaman autentikasi lain  
**When** redesign diterapkan  
**Then** lupa/reset password, registrasi bila masih aktif, verifikasi email, dan konfirmasi password tetap dapat digunakan tanpa layout rusak.

### AC-LOGIN-12 — Perilaku backend tidak berubah

**Given** redesign telah diterapkan  
**When** test autentikasi dijalankan  
**Then** lifecycle OTP, rate limit, session regeneration, remember login, intended URL, audit, dan otorisasi tetap lulus.

## 19. Pengujian

### 19.1 Visual dan responsif

- Screenshot comparison pada Login dan OTP untuk desktop, tablet, dan mobile.
- Uji logo berwarna/putih pada background terkait.
- Uji konten panjang, error beberapa baris, zoom 200%, tinggi layar pendek, dan gambar hero gagal.
- Pastikan tidak ada layout shift besar saat font/logo selesai dimuat.

### 19.2 Interaksi

- Tab order seluruh kontrol.
- Enter untuk submit form aktif.
- Toggle password melalui keyboard.
- Pencegahan double submit.
- Paste OTP enam digit, termasuk kode berawalan nol.
- Loading login, loading verifikasi, dan loading resend tidak saling tertukar.
- Cooldown dan expiry tetap diputuskan server.

### 19.3 Regresi

- Jalankan seluruh feature test autentikasi dan OTP.
- Uji route guest dan redirect pengguna yang sudah login.
- Uji forgot/reset password serta halaman auth lain yang memakai guest layout.
- Jalankan build Vite dan pemeriksaan accessibility otomatis bila tersedia.

## 20. Urutan Implementasi yang Direkomendasikan

1. Tetapkan pilihan logo utama dan perlakuan canvas/whitespace bersama pemilik produk.
2. Buat/refactor authentication shell tanpa mengubah route atau backend.
3. Implementasikan layout desktop dan mobile.
4. Buat komponen alert, input, password toggle, dan button state yang dapat digunakan ulang.
5. Terapkan shell ke Login dan OTP.
6. Verifikasi halaman auth lain yang berbagi guest layout.
7. Optimalkan logo/hero dan implementasikan reduced motion.
8. Jalankan visual, accessibility, interaction, dan regression test.
9. Lakukan review Bapperida/Kominfo sebelum dianggap final.

## 21. Definition of Done

Redesign dinyatakan selesai apabila:

- Seluruh UI-01–UI-14 dan AC-LOGIN-01–AC-LOGIN-12 terpenuhi.
- Logo resmi Bapperida digunakan dengan benar pada Login dan OTP.
- Login serta OTP memiliki hierarchy, spacing, alert, dan interaction state yang konsisten.
- Tidak ada perubahan atau regresi pada lifecycle dan keamanan OTP.
- Halaman lulus review responsif pada viewport minimum yang ditetapkan.
- Keyboard-only flow, focus state, screen reader smoke test, contrast, zoom 200%, dan reduced motion telah diuji.
- Seluruh test autentikasi lulus dan build frontend berhasil.
- Tidak ada credential, OTP, email pribadi, atau informasi sensitif dalam source client dan screenshot dokumentasi.
- Pemilik produk menyetujui desain sebelum dipresentasikan atau diserahkan kepada Kominfo.

## 22. Keputusan yang Perlu Disahkan

1. Logo utama menggunakan wordmark berwarna pada panel terang atau versi putih pada panel biru?
2. Apakah `hero.png` tetap digunakan pada panel brand atau diganti foto/ilustrasi resmi yang telah disetujui?
3. Nama final yang tampil: “Portal Manajemen RPJMD”, “Portal RPJMD”, atau nomenklatur resmi lain?
4. Apakah halaman registrasi publik tetap tersedia? Halaman login administrator tidak akan mempromosikannya sampai ada keputusan.
5. Kontak bantuan resmi apa yang akan digunakan saat diserahkan kepada Kominfo?
6. Apakah copyright memakai Pemerintah Kabupaten Pasuruan, Bapperida, atau keduanya?
7. Apakah Kominfo memiliki design system, standar header, atau pedoman identitas visual yang wajib diadopsi?

## 23. Asumsi

- Logo yang tersedia dalam `public/` dianggap kandidat aset resmi, tetapi penggunaan final tetap memerlukan persetujuan pemilik produk.
- Backend OTP yang telah diimplementasikan tetap menjadi source of truth untuk semua state keamanan.
- Redesign menggunakan stack yang ada tanpa dependency frontend baru.
- Copy menggunakan Bahasa Indonesia; lokalisasi bahasa lain berada di luar scope.
- PRD ini dibuat berdasarkan review source Blade dan aset yang tersedia, bukan persetujuan desain final dari Bapperida atau Kominfo.

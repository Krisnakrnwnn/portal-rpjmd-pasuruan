# PRD Teknis Pengelolaan Model AI Chatbot — Multi-Provider

Tanggal: 15 September 2026  
Revisi: 16 September 2026 — fase dua multi-provider

Status: Fase satu Gemini tersedia; fase dua Gemini/OpenAI/Anthropic masih rancangan, belum diimplementasikan

Produk: Portal RPJMD Kabupaten Pasuruan — Bapperida

> **Acuan implementasi berikutnya:** bagian 22–32 menetapkan kebutuhan fase dua dan menggantikan batas Gemini-only pada bagian 5, 6, 9–18, serta acceptance criteria bagian 21 untuk pengembangan multi-provider. Bagian 1–21 dipertahankan sebagai riwayat dan kontrak regresi fase satu. Status test fase satu bukan bukti bahwa GPT/Claude sudah diimplementasikan atau lulus UAT.

> **Informasi hosting dari pengguna:** terdapat 75 dokumen publik dan 9.732 chunk ingest AI; tidak ada staging terpisah. Pengguna melaporkan Test Model `gemini-2.5-flash` berhasil dan `gemini-2.5-pro` gagal dengan pesan model tidak tersedia/akses ditolak. Ini hasil yang dilaporkan pengguna, bukan inspeksi langsung database/API oleh penyusun. Tidak diketahui apakah semua dokumen publik telah di-ingest, apakah semua chunk valid, atau apakah kualitas jawaban sudah memenuhi UAT. Kode HTTP kegagalan Pro belum tersedia; penyebab spesifik belum disimpulkan.

> Catatan implementasi 15 September 2026: fitur telah diterapkan; uraian existing di bawah dipertahankan sebagai baseline sebelum refactor. Interface netral menyediakan `chat()` dan `test()` terpisah agar preview selalu satu attempt. Adapter history lama berada di AIManager. Generation dan query embedding chatbot kini mengirim key melalui header `x-goog-api-key` dengan redirect HTTP dinonaktifkan; endpoint/version, payload embedding, model embedding dan algoritme RAG tetap sama. Penyimpanan memakai transaction dengan lock key provider untuk menyerialkan save bersamaan. Tidak ada migration baru. Hasil API sukses tanpa teks memakai fallback publik existing; preview menganggapnya gagal, termasuk safety-blocked. Test RAG awal dijalankan sebelum refactor, dan hash prompt baseline dipertahankan. UAT kualitas dan akses kedua model menggunakan key staging masih merupakan verifikasi manual terpisah.

Dokumen ini menyempurnakan BOT-13 dalam [PRD utama](PRD.md). Lokasi `prd/` dan nama `PRD-AI-MODEL-MANAGEMENT.md` mengikuti dokumen fitur seperti `PRD-ADMIN-MULTIPAGE.md`. Seluruh path pada bagian existing adalah file yang ditemukan; path pada daftar Create secara eksplisit merupakan usulan file baru.

Analisis awal dilakukan melalui source, migration, konfigurasi nonrahasia, dan test. Bagian 1–3 dan diagram Current merekam baseline sebelum implementasi; pernyataan “belum ada” pada bagian tersebut bukan status kode terbaru. Implementasi lokal telah tersedia dan dilengkapi pada 16 September 2026. Tidak membaca `.env`, memeriksa data produksi, atau menjalankan Gemini sungguhan dalam validasi otomatis. Model aktif deployment serta akses dan kualitas jawaban staging belum dapat dipastikan. Default source bukan bukti model aktif produksi.

### Status implementasi 16 September 2026

- Katalog, resolver database, manager/interface/provider, form Setelan, save atomik beserta audit, guard statistik, dan preview terisolasi sudah terhubung.
- Pesan kegagalan konfigurasi/koneksi chatbot kini menyebut tindakan yang dapat dilakukan pengguna, tanpa detail provider. Status HTTP publik tetap dipertahankan.
- Jeda retry embedding tetap dua detik dan maksimal tiga attempt untuk 429, memakai Laravel Sleep agar test tidak menunggu waktu nyata.
- Test tambahan mencakup batas similarity 0,29/0,30/0,31, chunk berperingkat lebih rendah, kegagalan write, matriks error preview, serta history/clear/new-session setelah save.
- Dokumentasi model Flash/Pro dan jadwal penghentian resmi diperiksa ulang pada 16 September 2026; kedua ID tetap tercantum tanpa tanggal penghentian yang diumumkan. Ini bukan bukti akses API environment.
- UAT dan keputusan rollout dicatat terpisah di [lembar UAT](UAT-AI-MODEL-MANAGEMENT.md). Acceptance criteria 2 (evaluasi model) dan 10 (UAT) tetap terbuka sampai hasil staging tersedia.

## 1. Background

Chatbot sudah menggunakan Google Gemini melalui Laravel HTTP client. **Model generatif sudah configurable melalui database dan admin**, bukan sepenuhnya hard-coded. `ChatbotController::chat()` membaca `stats` dengan `key = gemini_model` pada setiap permintaan, dengan fallback literal `gemini-2.5-flash`. Menu `/admin/setelan` sudah dapat menyimpan nama model.

Requirement baru terutama membutuhkan penguatan mekanisme existing: pilihan model yang disetujui aplikasi, validasi server, pemisahan model aktif dari pilihan belum disimpan, konfigurasi provider, dan batas service yang dapat diperluas. Model embedding adalah konfigurasi berbeda; mengganti model jawaban tidak boleh mengubah embedding atau mengharuskan ingest ulang.

Stack aktual di `composer.json` dan `package.json`: PHP ^8.2, Laravel ^11.0, Breeze, Sanctum, Blade, Tailwind CSS 4, Alpine.js, Vite, Smalot PDF Parser, dan DomPDF. Tidak ada SDK Gemini khusus; request menggunakan `Illuminate\Support\Facades\Http`. README masih menyebut PHP 8.1+, berbeda dari dependency; gunakan dependency aktual sebagai batas instalasi.

## 2. Existing Implementation

### 2.1 Pemetaan source

| File aktual | Class/function atau peran |
|---|---|
| `app/Http/Controllers/ChatbotController.php` | `chat()`: validasi, key, embedding, RAG, berita, prompt, history, request generation, persistence, respons. `cosineSimilarity()` dan `getSystemPrompt()` adalah helper internal. |
| `app/Http/Controllers/ChatbotController.php` | `loadHistory()`, `newSession()`, `clearHistory()`, `feedback()`, `exportChat()`, `exportToPdf()`, `exportToTxt()`. |
| `app/Services/DocumentIngestor.php` | `__construct()` membaca key; `ingest()` mengekstrak PDF per halaman, membagi 1.500 karakter, memanggil embedding dan menyimpan chunk. Bukan service generation chatbot. |
| `app/Jobs/IngestDocumentJob.php` | `handle(DocumentIngestor $ingestor)`; `ShouldQueue`, timeout job 3.600 detik. |
| `app/Console/Commands/IngestPdfCommand.php` | `handle()`, command `rag:ingest`, embedding Gemini melalui HTTP langsung. |
| `app/Console/Commands/IngestPdfVisionCommand.php` | `handle()`, `transcribePageWithVision()`, `getEmbedding()`, command `rag:ingest-vision`; jalur alternatif dengan model berbeda. |
| `app/Http/Controllers/Admin/AdminController.php` | `updateSettings()` menyimpan model dan `Activity::log(...)`; `ingestPdf()`, `checkIngestStatus()`, `cancelIngest()`, `destroyIngest()` mengelola ingest. |
| `app/Http/Controllers/Admin/AdminPageController.php` | `setelan()` membaca model aktif dan profil; `ingest()` menyusun halaman basis pengetahuan. Pola existing memisahkan page composition dari write endpoint. |
| `app/Models/Stat.php` | Eloquent model key/value/label yang juga menyimpan model chatbot. |
| `resources/views/admin/setelan/index.blade.php` | Form model teks bebas, tombol pilihan cepat, label model, Simpan, serta editor profil instansi. |
| `resources/views/admin/scripts/settings.blade.php` | `selectModel()` dan input listener mengubah label model sebelum Simpan. Juga berisi counter editor profil. |
| `resources/views/layouts/app.blade.php` | Widget chatbot dan JavaScript inline: pengiriman chat, Markdown, bahasa, sesi, feedback, ekspor, dan input suara browser. |
| `resources/js/app.js` | Bootstrap dan Alpine; bukan lokasi utama kode chatbot. |
| `resources/views/layouts/admin.blade.php`, `resources/views/admin/partials/sidebar.blade.php`, `resources/js/admin.js` | Layout, navigasi Setelan, kompatibilitas hash, dirty form dan polling admin. |
| `app/Support/AdminNavigation.php` | URL named route dengan konteks query tervalidasi. |
| `config/services.php` | Belum ada entri Gemini. Belum ditemukan file konfigurasi khusus AI/Gemini. |
| `bootstrap/app.php` | Registrasi web/API route, alias middleware role, pengecualian CSRF `/api/chat`, scheduler worker. |

Belum ada `AIManager`, `GeminiProvider`, atau interface provider generation pada source aplikasi.

### 2.2 Model dan environment

| Pemakaian | Nilai/strategi aktual |
|---|---|
| Generation chatbot | `stats.gemini_model` (notasi key/value); fallback `gemini-2.5-flash`; REST `v1beta/models/{model}:generateContent`. |
| Query embedding chatbot | Hard-coded `gemini-embedding-001`, `v1beta/...:embedContent`. |
| Ingest web/queue dan command PDF biasa | Hard-coded `gemini-embedding-001`. |
| Command vision | Hard-coded `gemini-1.5-flash` generation dan `text-embedding-004` embedding, versi endpoint `v1`. |
| Credential | `GEMINI_API_KEY`, dibaca langsung memakai `env()` oleh controller, ingestor, dan kedua command. Nilainya tidak diperiksa. |
| `GEMINI_MODEL` | Hanya disebut dalam pesan error 404; bukan environment variable yang dibaca untuk memilih model chatbot. |

`config/session.php`, `config/queue.php`, `config/database.php`, dan `config/filesystems.php` mengatur infrastruktur terkait. Default disk local aktual berakar di `storage/app/private`; ingestor memakai `Storage::disk('local')->path('documents/...')`, sehingga default lokasinya `storage/app/private/documents`, bukan mengasumsikan path absolut dari panduan lama.

### 2.3 Endpoint aktual

Semua endpoint chatbot berikut didefinisikan dalam `routes/web.php`, memakai stack web/session, walaupun URL diawali `/api`. `routes/api.php` hanya memiliki endpoint user Sanctum.

| Method dan URL | Named route / method | Pembatasan khusus |
|---|---|---|
| POST `/api/chat` | `api.chat` / `chat` | `throttle:20,1`; dikecualikan CSRF oleh bootstrap |
| GET `/api/chat/history` | `api.chat.history` / `loadHistory` | Cookie sesi |
| POST `/api/chat/new-session` | `api.chat.new_session` / `newSession` | CSRF web |
| POST `/api/chat/clear` | `api.chat.clear` / `clearHistory` | CSRF web |
| POST `/api/chat/feedback` | `api.chat.feedback` / `feedback` | CSRF web |
| POST `/api/chat/export` | `api.chat.export` / `exportChat` | CSRF web, `throttle:10,1` |
| GET `/admin/setelan` | `admin.setelan.index` / `AdminPageController::setelan` | `auth`, `admin.role` |
| POST `/admin/settings` | `admin.update_settings` / `AdminController::updateSettings` | `auth`, `admin.role`, CSRF |

Endpoint chatbot berada di luar group `auth`; halaman portal sendiri berada di dalam group `auth`. Dokumen ini tidak mengubah kontrak akses tersebut.

### 2.4 Database terkait

| Tabel / model | Migration aktual dan isi penting |
|---|---|
| `stats` / `Stat` | `database/migrations/2026_04_18_095346_create_stats_table.php`: id, key unik, value string, label nullable, timestamps. |
| Record `gemini_model` | `database/migrations/2026_07_03_170225_add_gemini_model_to_stats_table.php`: data migration `updateOrCreate`, nilai awal `gemini-2.5-flash`; bukan penambahan kolom walau nama migration demikian. |
| `document_chunks` / `DocumentChunk` | `database/migrations/2026_04_18_135648_create_document_chunks_table.php`: nama dokumen, halaman nullable, teks, embedding JSON nullable. |
| `document_ingestions` / `DocumentIngestion` | `database/migrations/2026_05_13_115800_create_document_ingestions_table.php`: file, progres, status, error, timestamp. Belum ada FK chunk ke ingestion. |
| `chat_sessions` / `ChatSession` | `database/migrations/2026_05_05_082857_create_chat_sessions_table.php`: session_id unik, IP nullable, timestamps. |
| `chat_messages` / `ChatMessage` | `database/migrations/2026_05_05_082905_create_chat_messages_table.php`: role, message, session_id string FK ke chat_sessions dengan cascade delete. |
| `chat_analytics` / `ChatAnalytic` | `database/migrations/2026_05_05_082912_create_chat_analytics_table.php`: agregat harian; belum ditulis oleh alur chat yang diperiksa. |
| `shared_messages` | `database/migrations/2026_05_05_082919_create_shared_messages_table.php`: pesan, token unik, expiry; model/route sharing backend belum ditemukan. |
| `activities` / `Activity` | `database/migrations/2026_04_18_112438_create_activities_table.php`; audit perubahan melalui user_id, type, action, description. |

Model terkait berada di `app/Models/` dengan nama class pada tabel. `News::published()` memasok judul tiga berita terbit terbaru; bank PDF publik tidak otomatis menjadi korpus RAG.

### 2.5 Alur RAG dan percakapan yang wajib dipertahankan

1. Validasi `message` required string; bahasa default `id`. Cookie `chat_session_id` atau UUID menentukan sesi database; pesan user disimpan sebelum request embedding.
2. Buat embedding pertanyaan, termasuk ketika korpus kosong. Timeout 30 detik per attempt; maksimal tiga attempt, retry HTTP 429 dengan jeda dua detik.
3. Baca `DocumentChunk` melalui `chunkById(200)`, hitung cosine similarity di PHP, pertahankan sepuluh skor tertinggi. Jika skor tertinggi **> 0,3**, gabungkan seluruh top ten sebagai konteks; bukan memfilter setiap chunk dengan threshold itu.
4. Sertakan penanda `[File: ..., Hal: ...]`. Tambahkan judul/tanggal tiga berita `published()->latest()->take(3)` jika tersedia.
5. `getSystemPrompt()` menghasilkan aturan bahasa, identitas, grounding, sapaan waktu, dan format jawaban. Prompt digabung sebagai teks pada pesan user terbaru, bukan field API `systemInstruction` terpisah.
6. Ambil session Laravel `chatbot_history`, tambah pesan terbaru yang berisi prompt dan konteks. Baca model dari `stats` dan panggil Gemini.
7. Timeout generation 45 detik per attempt, maksimal tiga attempt; retry HTTP 429/503 dengan jeda tiga detik. Tidak ada streaming/SSE; frontend menerima JSON `{reply}` setelah request selesai.
8. Baca `candidates.0.content.parts.0.text`; simpan jawaban database dan empat pasang percakapan terakhir (delapan elemen) di session. History berikutnya memakai pertanyaan asli, bukan prompt konteks panjang. Cookie chat berlaku 43.200 menit (30 hari).
9. `loadHistory()` membaca maksimal 50 pesan terawal menurut created_at ascending. `clearHistory()` hanya menghapus memori session; `newSession()` membuat UUID baru. Frontend memanggil new-session saat halaman dimuat ulang. Feedback saat ini berupa log.

### 2.6 Keterbatasan baseline dan perbedaan PRD

- PRD utama BOT-13 sudah menyatakan model configurable. Masalah bukan ketiadaan fitur Simpan.
- PRD utama masih menyebut admin multipage belum diimplementasikan; source sudah memiliki `/admin/setelan`, page controller, dan view terpisah. Perencanaan mengikuti source aktual.
- Link Setelan pada sidebar berada di dalam kondisi `Super Admin`, walaupun GET/POST backend mengizinkan Admin juga. Implementasi perlu menampilkan link Setelan untuk kedua role sesuai otorisasi existing; link Kelola Pengguna tetap Super Admin-only.
- Pernyataan akses publik dalam PRD utama tidak konsisten dengan kewajiban login di bagian lain; source halaman memakai auth, endpoint chat tidak. Tidak diselesaikan dalam scope ini.
- `updateStats()` dan `updateHeroStats()` menerima key dari request tanpa pembatasan key AI; request langsung dapat menimpa `gemini_model`, melewati validasi pengaturan. Ini wajib ditutup saat implementasi whitelist.
- Catch chatbot membocorkan `$e->getMessage()`; URL request mengandung key sehingga exception/URL tidak boleh dikirim atau dicatat mentah. Ingest juga memiliki log body provider/exception mentah; audit ingest lebih luas merupakan pekerjaan terpisah.
- Markdown dari provider dirender dengan `marked.parse()` ke HTML di layout; sanitasi belum terlihat pada jalur itu. Preview baru harus menggunakan textContent. Hardening widget existing dicatat terpisah dan tidak diam-diam dirombak.
- `exportToPdf()` merujuk view `exports.chat-pdf`, tetapi file view tersebut tidak ditemukan. Jangan mengklaim baseline ekspor PDF sudah lulus.
- Relasi chunk berdasarkan nama, pencarian linear, potensi embedding campuran dari command vision, serta ingest yang dapat selesai walau sejumlah embedding gagal adalah utang teknis terpisah.
- Belum ditemukan test khusus chatbot/provider/RAG. `tests/Feature/AdminMultipageTest.php` mengirim `model-test` ke endpoint settings; fixture ini perlu disesuaikan ketika whitelist diberlakukan.

## 3. Problem

Nama model teks bebas hanya divalidasi `required|string|max:100`. Typo atau model tanpa dukungan generation dapat disimpan, lalu request chatbot gagal. Tombol cepat berisi pilihan statis termasuk keluarga 1.5 yang tidak boleh dianggap tersedia hanya karena masih tampil. Label model berubah saat mengetik, sehingga pilihan belum disimpan tampak seperti konfigurasi aktif.

Fallback literal terduplikasi antara chatbot dan page controller. Provider tidak disimpan; transport Gemini berada di controller yang juga mengatur RAG. Tidak ada preview terisolasi, katalog tervalidasi, maupun jaminan seluruh jalur penulisan konfigurasi mengikuti aturan yang sama.

## 4. Objective

Membuat model AI chatbot dapat dikonfigurasi administrator tanpa perubahan source code, `.env`, restart manual, atau deployment ulang ketika memilih model yang sudah diizinkan aplikasi. Pertahankan perilaku chatbot dan konteksnya; perluasan daftar model baru tetap melalui review kompatibilitas aplikasi.

## 5. Scope

### In Scope

- Menyempurnakan form existing, provider Google Gemini, dropdown whitelist, model aktif, Simpan, audit, validasi dan fallback konfigurasi.
- Membaca konfigurasi dari database setiap request dan memisahkan generation ke service/provider kecil.
- Test Model sederhana sebagai bagian fase pertama: uji konektivitas/generation tanpa menyimpan model atau riwayat chat.
- Menutup bypass key AI melalui endpoint statistik dan mengamankan error pada jalur chatbot/provider yang disentuh.
- Test regresi dan rencana kompatibilitas konfigurasi lama.

### Out of Scope

- Implementasi OpenAI/Claude, kelola API key di UI, katalog model bebas dari admin, serta failover otomatis antarprovider/model.
- Perubahan embedding, chunking, retrieval threshold, system prompt, panjang history, schema percakapan, streaming atau UI widget utama.
- Reingest/migrasi korpus, OCR, vector database, perbaikan ekspor/analitik dan seluruh utang teknis portal.
- Preview RAG penuh dengan dokumen/history produksi; menjadi enhancement terpisah. UAT kualitas model tetap wajib sebelum rilis.

## 6. Functional Requirements

| ID | Kebutuhan |
|---|---|
| FR-01 | Admin dan Super Admin melihat provider, model tersimpan, dan model efektif apabila fallback dipakai. |
| FR-02 | Model dipilih dari katalog config server; provider fase pertama tetap Google Gemini. |
| FR-03 | Simpan memvalidasi pasangan provider/model dan menulis konfigurasi beserta audit secara atomik. |
| FR-04 | Request chat berikutnya setelah commit menggunakan konfigurasi baru tanpa restart/cache clear. |
| FR-05 | Pilihan belum disimpan tidak mengubah label aktif, DB, cookie, atau history. |
| FR-06 | Test Model menguji pilihan kandidat melalui backend dan tidak melakukan aktivasi. |
| FR-07 | Invalid input atau kegagalan penyimpanan mempertahankan konfigurasi valid terakhir. |
| FR-08 | UI menampilkan keberhasilan/kegagalan yang jelas dalam Bahasa Indonesia; nilai pilihan tetap tersedia setelah validation error. |
| FR-09 | API key tidak menjadi field form, response, data HTML/JS atau record database. |
| FR-10 | Endpoint lain tidak boleh mengubah reserved key AI dengan melewati validasi pusat. |

## 7. Non-Functional Requirements

- **Security:** auth/role/CSRF di server; host dan API method tetap ditentukan provider; input bukan URL/class/path. Logging hanya metadata aman seperti provider, model tervalidasi, kategori error, status, correlation ID.
- **Backward compatibility:** default `gemini-2.5-flash`, model lama valid dipertahankan, payload generation dan kontrak `{reply}` tidak berubah. Tidak mengubah isi prompt, embedding, RAG, session maupun persistence sukses.
- **Maintainability:** satu katalog, satu resolver settings, dan satu implementasi generation Gemini; frontend tidak menduplikasi daftar ID model.
- **Error handling:** bounded retry/timeout, exception aman, tidak menulis konfigurasi baru saat validasi gagal, tidak mengubah model aktif saat provider bermasalah.
- **Freshness:** konfigurasi dibaca sekali per request dengan satu query untuk key terkait; tidak ada singleton/static cache settings lintas request. Admin save memakai transaction sehingga pasangan konfigurasi tidak terbaca separuh.
- **Aksesibilitas:** label select, fokus terlihat, error terasosiasi, status pengujian `aria-live`, loading/disabled, keyboard, mobile 320 px dan zoom 200%.

## 8. Proposed Architecture

### Current

```text
Widget Blade -> ChatbotController
                  |-> embedding HTTP -> document_chunks -> cosine/RAG
                  |-> News + system prompt + session history
                  |-> Stat(gemini_model) -> HTTP Gemini generation -> reply

Setelan -> AdminController::updateSettings -> Stat(gemini_model)
```

### Proposed

```text
Widget -> ChatbotController [RAG/prompt/session tetap]
                  |-> AIManager -> AiSettings (Stat + config whitelist)
                  |       |-> GeminiProvider -> Gemini generateContent
                  |<- hasil teks/error terstruktur

Setelan GET -> AiSettings -> model aktif + katalog aman
Simpan -> validasi -> transaction Stat + Activity
Test Model -> validasi kandidat -> AIManager::test -> GeminiProvider
             [tanpa settings write, history, RAG atau session chat]
```

Usulan interface generation: `chat(string $model, array $messages): string`. Pesan internal berupa role `user|assistant` dan teks; GeminiProvider memetakan `assistant` ke `model` serta `parts[].text`. AIManager menyediakan adapter untuk history existing tanpa mengubah format yang tersimpan. Snapshot test wajib membuktikan array `contents` Gemini identik dengan baseline. Hasil gagal memakai exception bertipe kategori/status aman, bukan raw HTTP response di controller/UI.

Embedding tetap mekanisme Gemini terpisah. Menambah provider generation di masa depan tidak otomatis mengganti ruang vektor korpus.

## 9. Database Changes

### Keputusan: gunakan tabel `stats` existing

`stats` sudah berfungsi sebagai key/value settings dan memiliki unique key; tidak ditemukan tabel settings umum lain. Tabel `ai_settings` baru dapat memberi kolom provider/model/FK lebih eksplisit, tetapi menambah sumber kebenaran dan migrasi dari konfigurasi existing untuk kebutuhan hanya satu pasangan aktif. Config file saja tidak memenuhi perubahan oleh admin saat runtime. Gunakan `Stat` di balik `AiSettings` agar detail penyimpanan tidak menyebar.

| Kolom existing | Tipe migration | Default database / aturan |
|---|---|---|
| id | big increment | Otomatis |
| key | string (255), unique | Tidak ada default |
| value | string (255), wajib | Tidak ada default |
| label | string (255), nullable | null |
| created_at, updated_at | timestamps | Dikelola Laravel |

| Key logis | Nilai awal | Makna |
|---|---|---|
| `gemini_model` existing | Pertahankan nilai lama; fallback config `gemini-2.5-flash` jika record tidak ada | Model generatif Gemini aktif |
| `ai_provider` baru | `gemini` | Provider generation aktif |

Service mengekspos struktur netral `{provider, model}`; pada fase ini field provider disimpan sebagai key `ai_provider`, bukan kolom baru. `gemini_model` sengaja tetap digunakan untuk kompatibilitas. Sebelum provider kedua diperkenalkan, lakukan migrasi eksplisit ke key `ai_model` atau tabel dedicated melalui adapter yang sama; jangan menyimpan nama model OpenAI di key Gemini.

Tidak memerlukan schema migration atau data migration untuk fase pertama. Resolver memberi default `gemini` ketika key provider belum ada; save valid pertama melakukan upsert kedua key dalam satu transaction. Missing model juga menggunakan default config tanpa write saat GET/chat. Migration lama tidak diedit/dijalankan ulang untuk mereset model pengguna.

Tidak menambah `is_active`: hanya satu pasangan konfigurasi dan tidak ada kebutuhan mematikan chatbot. Tidak menambah `updated_by` karena `Activity::log()` sudah merekam user_id dan waktu; deskripsi audit menyertakan pasangan sebelum/sesudah yang tervalidasi. Tidak ada FK baru. Tidak menyimpan hasil Test Model atau prompt ke database.

## 10. Backend Changes

### Create — semua path berikut usulan baru

| Path usulan | Tujuan |
|---|---|
| `config/ai.php` | Default provider/model, whitelist ID + label model, pemisahan generation dari embedding. Tidak berisi key. |
| `app/Services/AI/AiSettings.php` | Katalog, validasi konfigurasi tersimpan, resolusi default, transaction save/audit, reserved keys. Memakai model Stat existing. |
| `app/Services/AI/AIManager.php` | Resolusi provider dari registry internal; generation aktif dan pengujian kandidat tanpa mutasi settings. |
| `app/Services/AI/Contracts/AIProviderInterface.php` | Kontrak generation teks netral. |
| `app/Services/AI/Providers/GeminiProvider.php` | Mapping payload, endpoint v1beta existing, timeout/retry generation existing, parsing teks dan kategori error aman. |
| `app/Services/AI/Exceptions/AIProviderException.php` | Error bertipe tanpa body provider, key atau URL rahasia. |
| `app/Http/Requests/Admin/UpdateAiSettingsRequest.php` | Validasi provider/model terpusat untuk write. |
| `app/Http/Requests/Admin/TestAiModelRequest.php` | Validasi kandidat yang memakai katalog yang sama. Prompt pengujian ditetapkan server. |
| `tests/Feature/AiModelSettingsTest.php` | Baca/simpan/preview/auth/CSRF/invalid/bypass/audit. |
| `tests/Feature/ChatbotModelSelectionTest.php` | Request berikutnya, defaults, error, kesetaraan RAG/history dengan HTTP fake. |
| `tests/Unit/AiSettingsTest.php` | Resolver dan katalog; test berbasis DB ditempatkan pada Feature bila membutuhkan application/database. |
| `tests/Unit/GeminiProviderTest.php` | Mapping pesan dan parsing/error dengan HTTP fake/application bootstrap sesuai kebutuhan. |

### Modify — file existing

| Path aktual | Tujuan perubahan implementasi nanti |
|---|---|
| `app/Http/Controllers/ChatbotController.php` | Delegasikan hanya generation ke manager; pakai config key server; amankan catch. RAG/prompt/sesi tetap. |
| `app/Http/Controllers/Admin/AdminController.php` | `updateSettings()` memakai Form Request/service; tambah `testAiModel()`; batasi `updateStats()`/`updateHeroStats()` agar reserved key AI tidak dapat ditulis. |
| `app/Http/Controllers/Admin/AdminPageController.php` | `setelan()` memakai resolver/katalog bersama. |
| `config/services.php` | Tambah `gemini.api_key` dari `env('GEMINI_API_KEY')` untuk jalur generation dan embedding chatbot, agar kompatibel config cache. |
| `routes/web.php` | Pertahankan route Simpan; tambah route preview dengan middleware yang sama dan throttle. |
| `resources/views/admin/setelan/index.blade.php` | Dropdown, provider tetap, label aktif terpisah, preview dan status. Editor profil dipertahankan. |
| `resources/views/admin/scripts/settings.blade.php` | Ganti logika input bebas dengan kandidat/preview, render hasil sebagai teks, pertahankan counter profil. |
| `resources/views/admin/partials/sidebar.blade.php` | Tampilkan link Setelan bagi Admin dan Super Admin sesuai backend; pertahankan pembatasan link Kelola Pengguna. |
| `tests/Feature/AdminMultipageTest.php` | Ganti fixture `model-test` dengan model whitelist dan tetap uji redirect/hak akses. |
| `README.md`, `prd/PRD.md` | Saat implementasi: dokumentasikan penggunaan Setelan, batas whitelist dan kontrak baru BOT-13. |

Tidak perlu mengubah `Stat.php`, frontend chatbot utama, migration existing, job ingest atau provider OpenAI/Claude untuk scope ini. `app/Providers/AppServiceProvider.php` tersedia bila binding diperlukan, tetapi concrete constructor injection dan registry internal manager cukup; jangan menambah binding/settings singleton tanpa kebutuhan.

## 11. Admin UI Changes

Gunakan kartu **Pengaturan Model AI Chatbot** pada `/admin/setelan`, named route `admin.setelan.index`. Pertahankan `section-setelan`, navigasi lama yang dipetakan admin.js, editor profil, dan pola notifikasi existing.

```text
Pengaturan AI Chatbot
Provider AI: Google Gemini
Model aktif: Google Gemini — [nilai efektif server]
[Jika fallback: nilai tersimpan tidak valid; menggunakan default aplikasi]

Model yang dipilih: [dropdown dari katalog server]
[Test Model] [Simpan Model AI]
Hasil uji: [status + respons teks]
```

Provider belum selectable; backend tetap memvalidasi provider `gemini` karena hidden input bukan kontrol keamanan. Mengubah dropdown hanya mengganti kandidat. Indikator aktif tidak berubah sampai save berhasil dan halaman membaca ulang database.

Test Model memakai prompt server tetap: “Jawab singkat dalam Bahasa Indonesia bahwa koneksi model berhasil.” Ini uji generation, bukan jawaban resmi prioritas pembangunan tanpa sumber. Jelaskan bahwa pengujian memakai kuota API dan tidak mengubah model aktif. Simpan tidak otomatis melakukan pengujian; preview sukses bukan jaminan kuota/ketersediaan berikutnya.

Hasil uji menampilkan provider/model yang benar-benar diuji; ketika pilihan berubah, batalkan/abaikan hasil request lama. Tombol uji disabled selama request, hasil memakai textContent dan aria-live. Integrasikan perubahan select dengan dirty-form tracking; Test tidak menandai form telah disimpan. Polling admin tidak boleh menimpa draft atau status uji.

## 12. API / Service Flow

### Production

1. Widget POST `/api/chat` seperti sekarang; input, throttle, dan kontrak JSON tetap.
2. Controller melakukan embedding, RAG, berita, prompt, dan history seperti baseline.
3. Manager membaca snapshot provider/model efektif sekali dari AiSettings setelah context tersedia, lalu memilih provider internal.
4. GeminiProvider menerima model tervalidasi dan pesan, memanggil endpoint v1beta generateContent yang sama. Tidak mengirim pengaturan model dari user chatbot sebagai override.
5. Controller menyimpan jawaban dan history dengan aturan existing, mengembalikan `{reply}` dan cookie existing.

Permintaan yang telah mengambil snapshot boleh menyelesaikan jawaban dengan model lama. Permintaan yang mengambil snapshot setelah Simpan commit harus menggunakan model baru, termasuk pada instance aplikasi lain yang memakai database sama.

### Simpan

Pertahankan POST `/admin/settings` (`admin.update_settings`). Field `gemini_model` tetap digunakan untuk kompatibilitas form existing; provider baru `provider` bernilai `gemini`. Request lama yang tidak menyertakan provider dinormalisasi ke `gemini` selama fase satu saja; provider eksplisit invalid tetap ditolak. Kontrak service menggunakan nama netral `model`.

Auth/role/CSRF -> validasi katalog -> transaction upsert provider/model + Activity -> commit -> redirect `AdminNavigation::url('admin.setelan.index')` dengan flash sukses. Validation error memakai pola Laravel redirect/errors; JSON client menerima 422. Tidak ada panggilan provider pada Simpan.

### Test Model — route baru yang diusulkan

POST `/admin/settings/test-model`, named route `admin.test_ai_model`, `auth` + `admin.role` + CSRF + `throttle:5,1`. Payload provider dan gemini_model; tidak menerima API key, URL, prompt arbitrer atau context pengguna.

Validasi -> manager test dengan kandidat eksplisit -> provider generation satu attempt dengan timeout 45 detik -> JSON aman `{success, provider, model, reply}`. Jalur production tetap maksimal tiga attempt; satu attempt pada preview membatasi biaya dan waktu tunggu. Tidak memanggil `ChatbotController::chat()`, tidak membuat ChatSession/ChatMessage, tidak mengubah chatbot_history/cookie atau settings. Error 422 untuk input, 429 untuk throttle/kuota, 503 untuk provider unavailable, 504 timeout, 502 respons malformed; gunakan pesan aman.

## 13. Model Configuration Strategy

| Pendekatan | Penilaian |
|---|---|
| Dropdown hard-coded di Blade | Mudah tetapi menduplikasi validasi, cepat usang; ditinggalkan. |
| Whitelist config server | Satu sumber pilihan UI/validasi/fallback; paling sesuai fase pertama. |
| Katalog database yang dikelola admin | Membutuhkan CRUD katalog dan pengujian kompatibilitas tersendiri; berlebihan saat ini. |
| Dynamic Models API | Membantu cek kemampuan/availability, tetapi bukan persetujuan biaya/kualitas aplikasi; tidak dipakai pada setiap request. |

Pilih `config/ai.php` untuk whitelist dan default, `stats` untuk pilihan aktif. ID hanya di config, bukan literal pada controller, service request URL, atau Blade. Default initial generation adalah `gemini-2.5-flash`; kandidat awal alternatif `gemini-2.5-pro`, keduanya sudah muncul di UI existing dan dokumentasi resmi.

Verifikasi dokumentasi pada 15 September 2026: Google mencantumkan ID stabil `gemini-2.5-flash` dan output teks dalam [dokumentasi Flash](https://ai.google.dev/gemini-api/docs/models/gemini-2.5-flash), serta ID stabil `gemini-2.5-pro` dan output teks dalam [dokumentasi Pro](https://ai.google.dev/gemini-api/docs/models/gemini-2.5-pro). [Jadwal penghentian Google](https://ai.google.dev/gemini-api/docs/deprecations) saat diperiksa tidak mengumumkan tanggal penghentian kedua ID tersebut. Verifikasi ulang saat implementasi; dokumentasi bukan bukti akses API key environment.

Sebelum rilis, pastikan kandidat mendukung `generateContent` pada v1beta melalui pemeriksaan metadata backend dan smoke test staging yang terkendali. [Models API resmi](https://ai.google.dev/api/models) menyediakan `supportedGenerationMethods`. Aplikasi tetap membatasi pilihan dengan whitelist walaupun provider menawarkan lebih banyak model. Jangan mengaktifkan semua hasil listing otomatis.

Jangan masukkan 1.5 dari tombol cepat lama ke katalog awal tanpa verifikasi baru. Model vision/embedding tidak menjadi pilihan generation admin. Model baru perlu evaluasi Bahasa Indonesia/Inggris, grounding, latensi dan biaya sebelum ditambahkan. Perpindahan antaranggota whitelist tidak membutuhkan deployment; penambahan anggota whitelist baru memang memerlukan pembaruan konfigurasi aplikasi yang direview.

## 14. Validation

- Provider: string, required setelah normalisasi kompatibilitas, `in:gemini`/allowlist registry aktif.
- Model: `required|string|max:100`, `Rule::in(...)` berdasarkan provider; trim input standar Laravel lalu cocokkan ID exact, tanpa koreksi typo/alias otomatis.
- Tolak URL lengkap, `models/` prefix, slash traversal, query string, array, object, blank dan model embedding; exact allowlist menjadi batas utama.
- Save dan Test menggunakan sumber rule katalog sama. Test tidak menerima override system prompt, context atau credential.
- Hanya ambil field yang tervalidasi; tidak menyimpan seluruh request. Provider-model dicek bersama sebelum transaction dimulai.
- Reserved keys minimum `gemini_model` dan `ai_provider` dilarang pada update statistik umum. Validasi seluruh batch dahulu agar input campuran tidak melakukan partial write. `updateHeroStats()` hanya key statistik hero yang sesuai kontrak; jangan memutus statistik sah lainnya tanpa test.
- Baca runtime juga memvalidasi nilai DB; perubahan manual database tidak boleh menghasilkan arbitrary URL/model request.

## 15. Authorization

Gunakan middleware existing `auth` dan `admin.role` di group admin. `AdminRoleMiddleware` mengizinkan `Admin` serta `Super Admin`. Fase satu mempertahankan hak kedua role untuk melihat, menguji dan menyimpan settings. `super.admin` tetap untuk manajemen pengguna; tidak menambah pembatasan Super Admin-only diam-diam.

Guest ditolak oleh auth dan User oleh admin.role mengikuti redirect middleware existing. Endpoint JSON harus tetap terproteksi walaupun respons penolakan mengikuti redirect existing; frontend tidak boleh menganggap HTML redirect sebagai sukses uji. Tes wajib memastikan tidak ada DB write atau HTTP provider saat akses ditolak.

CSRF wajib untuk save/test; tidak memperluas pengecualian `/api/chat`. Tidak perlu policy resource baru karena pengaturan global dan middleware role existing memadai. Audit save mencatat actor, perubahan sebelum/sesudah, serta waktu tanpa secret.

## 16. Error Handling & Fallback

| Kasus | Perilaku target |
|---|---|
| Kandidat invalid saat Simpan/Test | Error validation; DB/history tetap; tidak memanggil provider. |
| Model belum diset | Resolver memakai default config `gemini-2.5-flash`, provider `gemini`; label UI menunjukkan default. |
| Provider/model tersimpan kosong/invalid | Jangan gunakan nilai mentah. Pakai pasangan default yang juga lolos whitelist; tampilkan status fallback di admin dan log aman. Jangan diam-diam menulis ulang DB. |
| Default config sendiri invalid | Gagal aman tanpa request provider; tampilkan pesan konfigurasi perlu diperiksa. |
| Simpan DB/audit gagal | Rollback seluruh perubahan; flash gagal; nilai valid terakhir tetap. |
| Database tidak dapat dibaca | Error layanan aman; jangan menyamakan outage DB dengan record kosong atau mengklaim tersedia last-known-good cache. |
| API key hilang | Pesan konfigurasi aman, tanpa nilai secret; tidak memanggil provider. |
| Gemini 404/400 atau akses ditolak | Pesan model tidak tersedia/konfigurasi perlu diperiksa; settings tidak diubah; tidak retry kesalahan permanen. |
| 429/503 production | Pertahankan maksimal tiga attempt dan jeda existing, kemudian reply aman seperti kontrak sekarang; tidak melakukan switch otomatis. |
| Timeout/network exception | Reply aman tanpa exception URL/key; tidak menambah retry jaringan tanpa keputusan/evaluasi tersendiri. |
| Respons kosong/malformed/safety blocked | Bukan preview sukses. Production mempertahankan fallback teks ketika tidak ada teks jawaban; jangan mengekspos body provider. Test membedakan kegagalan dari jawaban teks. |

Existing 429/503/404 generation mengembalikan JSON reply dengan HTTP 200, sementara kegagalan lain umumnya 500. Refactor harus mempertahankan kontrak status public tersebut dahulu, dengan penggantian teks error yang menyingkap exception/rujukan GEMINI_MODEL yang keliru. Endpoint test baru dapat memakai status eksplisit karena belum memiliki konsumen lama.

“Konfigurasi valid terakhir” dijamin untuk input ditolak/transaction gagal karena nilai sebelumnya tidak ditimpa. Tidak ada histori konfigurasi persisten atau failover lintas model pada fase ini. Jika DB dirusak manual atau model yang tadinya valid dihentikan provider, default konfigurasi bukan jaminan layanan selalu berhasil; operator memilih kembali model yang tersedia melalui Setelan.

## 17. Migration / Backward Compatibility

1. Inventarisasi **hanya nilai setting nonrahasia** pada environment target ketika implementasi diizinkan. Catat model existing; jangan menganggap fallback source adalah nilai deployment.
2. Bila model lama berada dalam whitelist yang terverifikasi, pertahankan nilainya. Bila di luar whitelist, review dukungan dan evaluasi dahulu: tambahkan secara sah atau rencanakan pergantian dengan operator sebelum rollout. Jangan mengganti model produksi diam-diam saat deploy.
3. Missing provider -> Gemini; missing model -> default existing. Save pertama membuat key provider; tidak perlu seeder/migration baru.
4. Buat characterization test sebelum pemindahan HTTP generation: snapshot payload, nilai retrieval, berita terbit, history, response status dan persistence.
5. Deploy code/config feature satu kali; config cache dibuat saat deployment biasa. Pemilihan model sesudahnya membaca DB sehingga tidak membutuhkan config clear, worker restart atau deployment lagi.
6. Rollback kode tetap dapat membaca `gemini_model`; key `ai_provider=gemini` tambahan dapat dibiarkan tanpa mengubah data pengguna. Jangan menghapus record konfigurasi/riwayat saat rollback. Guard whitelist akan hilang bila kembali ke kode lama, sehingga rollback merupakan tindakan operasional sadar.

Generation provider memakai key via `config('services.gemini.api_key')`; query embedding dalam chatbot juga harus memakai konfigurasi yang sama. `DocumentIngestor` dan command existing masih memakai env langsung: audit kompatibilitas config cache pada jalur tersebut merupakan pekerjaan lanjutan terpisah, tanpa perubahan model/algoritme ingest dalam fitur ini.

## 18. Future Multi-Provider Support

Tambahkan provider baru yang mengimplementasikan interface generation, daftarkan registry internal, dan katalog modelnya. Manager mengembalikan teks/error netral, sehingga controller tetap mengurus konteks dan frontend tetap `{reply}`. Credential masing-masing tetap konfigurasi server. Tidak membuat class placeholder atau dependency OpenAI/Anthropic sekarang.

Sebelum provider kedua, pindahkan penyimpanan model ke key netral atau tabel dedicated melalui AiSettings dengan masa transisi dan rollback yang terdokumentasi. Mapping history user/assistant dan penempatan instruksi perlu contract test per provider; jangan membiarkan format Gemini bocor menjadi persyaratan interface permanen. Embedding tetap Gemini sampai proyek migrasi vektor terpisah disetujui.

## 19. Implementation Steps

1. Periksa ulang git status, AGENTS, code dan perubahan requirement. Rekam baseline test, termasuk known failure PDF export tanpa mencampurnya dengan regresi baru.
2. Verifikasi nilai model existing nonrahasia, model kandidat, dukungan API, biaya dan akses staging. Siapkan evaluasi RPJMD bersama pemilik produk.
3. Tambahkan characterization test chatbot dengan Http::fake dan larangan stray HTTP; gunakan SQLite/storage test sesuai konfigurasi test.
4. Buat config katalog/default dan AiSettings menggunakan stats, tanpa migration reset data.
5. Implementasikan validasi save dan tutup bypass endpoint statistik; simpan settings/audit dalam transaction.
6. Ekstrak generation ke GeminiProvider/AIManager, pertahankan payload, timeout/retry public, serta amankan error. Jalankan test kesetaraan sebelum mengubah form.
7. Hubungkan page controller dan form Setelan ke katalog; pisahkan kandidat dari model aktif.
8. Tambah endpoint/button Test Model terisolasi dan throttle; uji tidak ada mutasi history/settings.
9. Jalankan test unit/feature relevan dan suite, Pint pada PHP berubah, build frontend, pemeriksaan browser/mobile/keyboard dan diff.
10. Lakukan UAT model dengan dataset RPJMD representatif di staging. Catat kualitas grounding, bahasa, sumber, latensi, error dan biaya; jangan mengubah chunk/threshold untuk meloloskan model.
11. Perbarui README/PRD utama saat fitur diterapkan. Siapkan deployment dan rollback; tidak ada deployment atau implementasi dalam task penyusunan dokumen ini.

## 20. Testing Checklist

Validasi lokal 16 September 2026: `php artisan test --compact` selesai tanpa kegagalan (146 test, 1.090 assertions). PHP 8.5 melaporkan deprecation `PDO::MYSQL_ATTR_SSL_CA` dari konfigurasi vendor Laravel; vendor tidak diubah. Build Vite, Pint pada PHP yang disentuh, dan `git diff --check` lulus. Browser Chrome dengan mock lokal lulus pada lebar 320/768/1440, keyboard, status draft, loading, pembatalan hasil lama, rendering teks, dan viewport ekuivalen zoom 200%. Zoom browser asli, concurrency lintas instance/database produksi, dan UAT live belum diverifikasi.

- [x] DB tanpa setting memakai `gemini-2.5-flash`; fallback berasal dari config, bukan literal controller/view.
- [x] DB model lama valid dipertahankan; provider absent dinormalisasi Gemini; invalid default gagal aman.
- [x] Admin dan Super Admin dapat melihat/simpan; User/guest ditolak tanpa DB write/provider call.
- [x] Save valid menulis pasangan provider/model + Activity, actor dan before/after; reload menampilkan nilai tersimpan.
- [ ] Dua request chat berurutan dipisahkan save mengirim generation ke model berbeda; instance baru membaca nilai yang sama; request berjalan mempertahankan snapshot konsisten.
- [x] Invalid/blank/URL/model embedding/provider palsu ditolak; nilai lama tetap, tidak ada provider call.
- [x] Payload batch statistik yang mencoba reserved key ditolak tanpa partial write; update hero sah tetap bekerja.
- [x] Simulasi audit/DB write gagal merollback settings; outage baca DB bukan fallback diam-diam.
- [x] Test Model menggunakan kandidat, tidak aktifkan kandidat, tidak membuat pesan/sesi, tidak mengubah history/cookie.
- [x] Preview tidak membutuhkan embedding/RAG; stale response tidak ditampilkan untuk kandidat baru; rendering teks mencegah XSS.
- [ ] Key hilang, 400/401/403/404, 429, 503, timeout, network error, malformed JSON, candidates kosong dan blocked response ditangani aman.
- [ ] Jumlah attempt/jeda/timeout generation public dan embedding tetap; preview maksimal satu attempt. Test memakai fake tanpa menunggu tidur nyata bila seam diperlukan.
- [x] HTTP payload baseline sama: prompt, sapaan waktu dibekukan, bahasa id/en, roles, parts dan history.
- [x] Retrieval korpus kosong, di bawah/sama/di atas 0,3, top ten, penanda file/halaman dan tiga berita published tetap sama; berita draf tidak masuk konteks.
- [x] History delapan elemen, message persistence, cookie dan endpoint history/clear/new-session tidak berubah karena pergantian model.
- [ ] Pemilihan model tidak menulis document_chunks/ingestions atau mengganti embedding endpoint/model.
- [ ] CSRF save/test diuji dengan middleware benar-benar aktif (test Laravel biasa dapat menonaktifkannya); throttle test dan public tetap berfungsi.
- [ ] Response/HTML/JS/log tidak memuat sentinel fake API key, raw provider body, stack trace, URL ber-key atau system prompt internal.
- [ ] Dirty form/polling, redirect, flash, profil editor, navigasi hash, keyboard dan mobile tidak regresi.
- [x] `php artisan test`, `npm run build`, Pint file PHP berubah dan `git diff --check` selesai tanpa kegagalan; ada deprecation vendor pada PHP 8.5. Test AI memakai `Http::fake()`/`Http::preventStrayRequests()` dan database test.
- [ ] Smoke test live staging hanya aktivitas terpisah yang terotorisasi; bukan bagian routine test atau task dokumentasi ini.

## 21. Acceptance Criteria

1. `/admin/setelan` menampilkan Google Gemini dan model aktif dari resolver yang sama dengan chatbot. Kandidat belum disimpan tidak pernah ditandai aktif.
2. Admin dan Super Admin dapat memilih minimal dua model generation yang telah lolos verifikasi/evaluasi, menyimpan, reload, dan melihat nilai baru tanpa perubahan source/.env/deployment.
3. Request chatbot berikutnya setelah commit menggunakan model baru; RAG, embedding, prompt, riwayat, database percakapan, cookie dan kontrak response tetap sesuai characterization test.
4. Model/provider di luar whitelist ditolak di server, termasuk percobaan melalui endpoint statistik; konfigurasi valid terakhir tidak rusak.
5. Test Model memberi hasil atau error aman untuk kandidat tanpa aktivasi atau mutasi percakapan dan memakai auth/role/CSRF/throttle.
6. Default initial mempertahankan `gemini-2.5-flash`; nilai lama valid tidak ditimpa rollout, dan fallback ditampilkan secara jujur kepada admin.
7. API key tetap server-only; frontend/DB/audit tidak menerima secret, exception atau respons mentah provider. Panggilan API dilakukan hanya dari backend.
8. Save atomic dengan audit actor; kegagalan tidak meninggalkan pasangan settings sebagian. Tidak ada cache lintas request yang membuat model lama terus dipakai.
9. Transport generation diisolasi melalui manager/interface/provider tanpa mengimplementasikan provider lain dan tanpa memindahkan atau merancang ulang RAG.
10. Seluruh pengujian scope lulus, UAT model tercatat, dokumentasi operasional diperbarui, dan known technical debt dibedakan dari regresi baru. Pekerjaan fitur dimulai hanya setelah instruksi implementasi berikutnya.

## 22. Tujuan dan cakupan fase dua

Admin dan Super Admin dapat berganti provider **Google Gemini**, **OpenAI (GPT)**, dan **Anthropic (Claude)** beserta model yang disetujui, melalui Setelan tanpa mengedit kode, credential, atau melakukan deploy pada setiap pergantian. Penambahan adapter, credential, dan katalog awal tetap membutuhkan konfigurasi/deployment satu kali.

Termasuk: adapter OpenAI/Anthropic, dropdown provider/model, status credential tanpa nilainya, preview terisolasi, penyimpanan model netral, transisi data Gemini lama, pemetaan error, audit, contract test, UAT per provider, serta prosedur rollback.

Tidak termasuk: router pihak ketiga, endpoint custom, API key di UI/database, dynamic model discovery otomatis, failover antarprovider, pilihan provider oleh warga, streaming, tools/web search, upload dokumen ke provider, migrasi embedding, reingest, perubahan retrieval/prompt/history, atau perbaikan seluruh utang teknis chatbot.

## 23. Pemisahan generation dan embedding

```text
Pertanyaan warga
  -> embedding pertanyaan: Gemini (GEMINI_API_KEY)
  -> retrieval document_chunks existing + berita + prompt + history
  -> AIManager -> snapshot provider/model aktif
       -> GeminiProvider / OpenAIProvider / AnthropicProvider
  -> teks jawaban -> persistence dan JSON {reply} existing

Setelan -> pilih kandidat provider/model -> Test Model (generation saja)
                                       -> Simpan (settings + audit saja)
```

- Default deployment tetap Gemini/`gemini-2.5-flash`; jangan mengaktifkan provider baru otomatis.
- Generation GPT/Claude menerima konteks teks hasil retrieval, bukan seluruh 75 PDF atau seluruh 9.732 chunk. Pemilihan top ten dan threshold > 0,3 tetap berlaku.
- Embedding pertanyaan serta ingest tetap `gemini-embedding-001`. Pergantian generation tidak menulis `document_chunks`/`document_ingestions` dan tidak membutuhkan ingest ulang.
- **GEMINI_API_KEY tetap diperlukan untuk chatbot RAG walaupun generation memakai GPT/Claude.** Preview GPT/Claude dapat berhasil tanpa embedding, sehingga UI harus menampilkan readiness generation dan embedding secara terpisah.
- Riwayat lokal tetap delapan elemen dengan role existing `user|model`. Adapter manager mengubahnya menjadi `user|assistant` untuk provider; jangan memigrasikan record percakapan.
- Save berlaku saat request berikutnya mengambil snapshot. Request yang sudah mengambil snapshot menyelesaikan generation dengan pasangan lama, tanpa mencampur provider/model.

## 24. Functional requirements multi-provider

| ID | Kebutuhan |
|---|---|
| MP-01 | Admin/Super Admin melihat pasangan aktif, status default/fallback, dropdown tiga provider dan model sesuai katalog provider. |
| MP-02 | Perubahan provider menghapus pilihan model yang tidak sesuai, membatalkan preview lama, dan mempertahankan label aktif sampai save berhasil. |
| MP-03 | Model yang ditampilkan hanya exact ID dari katalog server; pasangan silang, provider tak terdaftar, URL, array, atau model embedding ditolak. |
| MP-04 | UI menyatakan credential generation tersedia/belum tersedia; tidak menampilkan key, potongan key, atau mengklaim akses model sudah terverifikasi hanya karena key tersedia. |
| MP-05 | Save ditolak server jika credential kandidat generation atau credential embedding Gemini kosong. Tidak ada HTTP provider pada save; credential terisi bukan jaminan kuota/akses valid. |
| MP-06 | Test Model menguji kandidat dengan credential provider tersebut saja, tanpa embedding, RAG, perubahan settings, riwayat, cookie chat, atau log isi percakapan. |
| MP-07 | Pasangan provider/model disimpan atomik dengan audit aktor dan before/after; kegagalan salah satu write/audit merollback seluruh transaksi. |
| MP-08 | Request generation setelah commit menggunakan provider/model baru tanpa cache clear atau restart. |
| MP-09 | UI memberi pesan Indonesia untuk key belum tersedia, akses ditolak, model tidak tersedia, kuota, overload, timeout, dan respons tidak valid. |
| MP-10 | Konfigurasi Gemini lama dibaca tanpa reset; migrasi ke key netral tidak menyimpan ID GPT/Claude di `gemini_model`. |
| MP-11 | Endpoint statistik tidak boleh membuat/mengubah/menghapus `ai_provider`, `ai_model`, maupun `gemini_model` melalui jalur lain. |
| MP-12 | Kegagalan provider aktif tidak memicu pergantian otomatis ke provider/model lain. |

## 25. Katalog, credential, dan readiness

| Provider ID | Label UI | Konfigurasi server yang diusulkan | Credential environment |
|---|---|---|---|
| `gemini` | Google Gemini | `services.gemini.api_key` | `GEMINI_API_KEY` |
| `openai` | OpenAI (GPT) | `services.openai.api_key` | `OPENAI_API_KEY` |
| `anthropic` | Anthropic (Claude) | `services.anthropic.api_key` | `ANTHROPIC_API_KEY` |

Semua credential dibaca melalui `config()` di adapter, bukan `env()` saat request. Perubahan credential mengikuti prosedur config cache hosting. Tidak memasukkan credential ke source, database, form, hasil Test Model, audit, atau exception. Credential provider tidak boleh dipakai pada host provider lain; host HTTPS dan path API ditentukan server, redirect dinonaktifkan, TLS diverifikasi.

`config/ai.php` menjadi katalog tunggal: provider ID, label, exact model ID, label model, status enabled, serta parameter generation khusus adapter. Registry hanya menerima tiga adapter yang diimplementasikan; request tidak menentukan class atau URL.

Katalog Gemini existing dipertahankan sebagai baseline. Pro tidak boleh ditandai lolos akses hanya karena terdaftar. Untuk GPT dan Claude, **ID model belum ditetapkan dalam revisi ini**: pada implementasi pilih minimal satu model teks dari tiap provider, verifikasi dokumentasi, akses akun hosting, biaya, context window, dan kualitas RPJMD; catat exact ID dan hasilnya sebelum enabled. Jangan menggunakan placeholder `gpt` atau `claude` sebagai ID API atau mengasumsikan semua model akun tersedia. Tidak mewajibkan model termahal/terbaru.

Provider tanpa credential tetap terlihat dengan status “API key belum dikonfigurasi”; Test/Simpan dinonaktifkan dan server menolak request langsung. Readiness `configured` hanya berarti key tidak kosong. Hasil preview bersifat sementara untuk kandidat yang diuji, bukan sertifikat akses permanen. Jika credential provider aktif dihapus, tampilkan status tidak siap dan balas error aman; jangan fallback lintas provider secara diam-diam.

## 26. Kontrak adapter generation

Pertahankan `AIProviderInterface::chat(string $model, array $messages): string` dan `test(...)` serta pesan netral `{role: user|assistant, text}`. Manager memilih adapter dari registry internal. Isi prompt, urutan history, dan konteks tetap sama; pemindahan prompt ke system/developer role adalah perubahan perilaku terpisah yang membutuhkan evaluasi, bukan bagian refactor transport ini.

| Adapter | Transport dan pemetaan target |
|---|---|
| GeminiProvider | Pertahankan endpoint/payload, parsing, timeout, dan retry fase satu. |
| OpenAIProvider | REST `POST https://api.openai.com/v1/responses`, Bearer credential; pesan ke `input`, `store: false`; parse teks pada item output message dan content bertipe `output_text`, jangan mengasumsikan `output[0]` adalah jawaban. Tidak menggunakan conversation/previous_response_id. |
| AnthropicProvider | REST `POST https://api.anthropic.com/v1/messages`, header `x-api-key` dan `anthropic-version` yang ditetapkan serta diverifikasi saat implementasi; pesan ke `messages`, `max_tokens` dari config; gabungkan content bertipe `text` saja. |

Keputusan memakai Responses API mengikuti [panduan text generation OpenAI](https://developers.openai.com/api/docs/guides/text); bentuk input/output dan `store` mengikuti [referensi Responses](https://developers.openai.com/api/reference/cli/resources/responses/methods/create). Anthropic mengikuti [Messages API](https://platform.claude.com/docs/en/api/messages/create) dan [panduan percakapan stateless](https://platform.claude.com/docs/en/build-with-claude/working-with-messages). Referensi diperiksa pada 16 September 2026; verifikasi ulang saat implementasi.

Parameter budget output GPT/Claude ditetapkan per model di config dan dievaluasi untuk jawaban RPJMD; nilai konkret menjadi keputusan implementasi sebelum enabled, bukan input admin. Jangan mengirim temperature/reasoning option generik ke semua model. Pertahankan parameter Gemini existing.

Respons OpenAI incomplete/failed, refusal, Claude terpotong karena `max_tokens`, tool-only, thinking-only, malformed, teks kosong, atau safety block bukan preview sukses. GPT/Claude gagal aman bila tidak ada jawaban lengkap yang dapat digunakan; jangan menampilkan reasoning/internal blocks. Gemini mempertahankan fallback teks produksi existing. Gunakan HTTP fake untuk membuktikan parser dan pemetaan pesan masing-masing provider, termasuk multi-block output.

## 27. Penyimpanan netral dan transisi data

Gunakan tabel `stats` existing dengan unique key; tidak memerlukan perubahan skema atau edit migration lama.

| Key | Makna fase dua |
|---|---|
| `ai_provider` | Provider generation aktif (`gemini`, `openai`, `anthropic`). |
| `ai_model` | Model generation aktif, netral terhadap provider. |
| `gemini_model` | Kompatibilitas fase satu; terakhir disimpan ketika provider Gemini dipilih, tidak pernah diisi model GPT/Claude. |

Urutan resolver dalam satu query snapshot:

1. Jika `ai_model` ada, pasangan `ai_provider` + `ai_model` adalah sumber utama. Missing/blank provider tidak boleh diterka sebagai Gemini untuk record netral.
2. Jika `ai_model` belum ada dan provider kosong karena record tidak ada atau bernilai `gemini`, baca `gemini_model`; hanya record model yang tidak ada boleh menggunakan default. Nilai blank/invalid harus ditandai fallback.
3. Jika kedua setting lama belum ada, gunakan default Gemini/Flash tanpa menulis DB. Jika provider lama eksplisit non-Gemini tetapi `ai_model` belum ada, konfigurasi dianggap invalid; jangan memasangkan GPT/Claude dengan `gemini_model`.
4. Pasangan invalid menggunakan default Gemini/Flash yang tervalidasi katalog dengan status fallback terlihat, sesuai fase satu. Missing key/akses ditolak/outage bukan invalid pair dan tidak memicu fallback. DB outage gagal aman, bukan dianggap setting kosong.

Save pertama menambahkan `ai_model` secara lazy. Semua save mengunci record provider dalam transaction, mengambil nilai sebelum, menulis pasangan netral, memperbarui `gemini_model` hanya untuk Gemini, dan menulis Activity. Jangan mengganti konfigurasi saat GET/preview atau menghapus data percakapan/chunk.

Kompatibilitas request selama **satu siklus rilis transisi**:

- Payload baru `{provider, model}` pada route save/test existing.
- Save lama `{gemini_model}` atau `{provider: gemini, gemini_model}` dinormalisasi hanya ketika `model` tidak disertakan dan provider Gemini. Payload lama tidak dapat mengaktifkan GPT/Claude.
- Preview lama wajib provider eksplisit dan hanya menerima alias `gemini_model` untuk Gemini.
- Jika `model` dan `gemini_model` dikirim bersama, tolak 422 untuk mencegah prioritas ambigu.
- Sebelum menghapus alias, pastikan UI/client lama tidak digunakan dan dokumentasikan rilis penghapusan. Reserved key lama tetap diblokir di endpoint statistik selama masih disimpan.

Hindari menjalankan writer fase satu dan dua bersamaan: deploy seragam atau hentikan sementara write settings selama rollout; writer lama hanya memperbarui `gemini_model` dan bisa tertinggal dari `ai_model`.

Rollback ke kode Gemini-only: sebelum rollback, gunakan versi baru untuk menyimpan Gemini dengan model yang sudah lolos koneksi; verifikasi kedua key model sinkron, lalu rollback kode. Jangan rollback ketika provider aktif GPT/Claude dan mengasumsikan kode lama memahami `ai_model`. `ai_model` boleh tetap ada; sebelum re-upgrade, rekonsiliasi dengan setting Gemini jika operator mengubahnya selama kode lama berjalan. Jangan menyalin nilai stale secara otomatis.

## 28. UI, endpoint, otorisasi, dan error

Tetap gunakan GET `/admin/setelan`, POST `/admin/settings`, POST `/admin/settings/test-model`, named routes existing, auth + `admin.role`, serta CSRF. Admin dan Super Admin tetap sama-sama berhak; tidak menambah Super Admin-only. Preview tetap `throttle:5,1`; chat/ekspor mempertahankan throttle existing.

UI menampilkan pasangan aktif, provider kandidat, model kandidat, status credential generation dan embedding, serta tombol Test/Simpan. Tidak menampilkan input credential. Ketika provider berubah, model kandidat dikosongkan sampai dipilih; daftar model berasal dari katalog server. Hasil preview wajib menampilkan provider/model yang benar-benar diuji; respons lama dibuang jika salah satu pilihan berubah. Test tidak menghapus dirty state. Teks hasil memakai `textContent`, label/error terasosiasi, loading disabled, `aria-live`, keyboard, 320px, dan zoom 200%.

Preview JSON tetap `{success, provider, model, reply}`. Tambahkan `error_code` aman pada kegagalan (`configuration`, `access_denied`, `model_unavailable`, `rate_limited`, `unavailable`, `timeout`, `invalid_response`) dan `request_id` internal untuk penelusuran. Jangan menyimpulkan penyebab lebih spesifik daripada status/type provider yang tervalidasi. Log hanya provider/model allowlist, kategori, upstream status, durasi, request ID; tanpa raw body, header, key, prompt, chunk, atau pertanyaan warga.

| Kondisi | Preview | Chatbot publik |
|---|---|---|
| Input invalid/credential belum tersedia saat Save | 422, tanpa mutation/provider call | Tidak menerima override provider/model dari warga. |
| Credential preview hilang | 503 + configuration | 500 dengan pesan konfigurasi aman; tidak beralih provider. |
| Provider 401/403 atau model 404 | 502 + kategori aman | Kontrak Gemini existing dipertahankan; GPT/Claude 500. |
| 429 | 429 | Reply aman HTTP 200 seperti kontrak publik existing. |
| Overload 503; Anthropic 529 | 503 | Reply sibuk HTTP 200. |
| Timeout/network | 504 | 500, tanpa detail internal. |
| Respons tidak valid/tidak lengkap | 502 | GPT/Claude 500; fallback Gemini existing tetap. |

Preview satu attempt, timeout 45 detik. Production Gemini tetap maksimal tiga attempt/45 detik, jeda existing. GPT/Claude awalnya **satu attempt/45 detik tanpa retry otomatis** untuk membatasi duplikasi biaya; perubahan retry harus dievaluasi tersendiri. Embedding tetap maksimal tiga attempt/30 detik pada 429 dengan jeda dua detik. Cocokkan timeout PHP/proxy hosting dengan batas request; jangan meningkatkan deadline diam-diam.

## 29. Data dan batas keamanan

Pergantian provider mengirim pertanyaan, konteks hasil retrieval, dan history yang diperlukan kepada provider terpilih. Pertanyaan embedding tetap dikirim ke Gemini. Jangan mengirim IP, email, user ID, cookie, seluruh PDF, atau history melebihi kontrak. History sebelum pergantian provider dapat menjadi bagian konteks ke provider baru; jelaskan alur ini dalam dokumentasi privasi sebelum mengaktifkan provider baru untuk warga.

`store: false` adalah pengaturan request OpenAI, bukan jaminan zero retention seluruh layanan. Pengelola meninjau ketentuan data provider dan kesesuaian dokumen/percakapan sebelum rollout. Tidak mengubah retensi database lokal pada fitur ini. Jangan menyimpan hasil preview atau menambah log prompt untuk keperluan UAT. Isi dokumen dan pesan tetap data tidak tepercaya; perbedaan kepatuhan grounding/prompt injection harus dievaluasi per model.

## 30. Rencana file dan implementasi

| File | Perubahan yang direncanakan, belum diterapkan |
|---|---|
| `app/Services/AI/Providers/OpenAIProvider.php` | Adapter Responses API, parsing teks/status dan error aman. |
| `app/Services/AI/Providers/AnthropicProvider.php` | Adapter Messages API, header/version, parsing teks/stop reason. |
| `app/Services/AI/AIManager.php` | Registry tiga provider dan snapshot pasangan aktif. |
| `app/Services/AI/AiSettings.php` | Katalog/readiness, resolver `ai_model`, alias transisi, transaction dan audit. |
| `app/Services/AI/Exceptions/AIProviderException.php` | Kategori aman dan pemetaan provider error. |
| `config/ai.php`, `config/services.php` | Katalog/parameter per provider, tiga credential server. |
| `app/Http/Requests/Admin/UpdateAiSettingsRequest.php`, `TestAiModelRequest.php` | Validasi `{provider, model}`, readiness, alias Gemini dan penolakan field ambigu. |
| `app/Http/Controllers/Admin/AdminController.php` | Save/test netral, guard tiga reserved keys, logging metadata aman. |
| `app/Http/Controllers/Admin/AdminPageController.php` | Katalog dan status generation/embedding untuk view. |
| `app/Http/Controllers/ChatbotController.php` | Error multi-provider; embedding/RAG/persistence tetap. |
| `resources/views/admin/setelan/index.blade.php`, `resources/views/admin/scripts/settings.blade.php` | Dropdown terkait, label aktif, status credential, pembatalan preview lintas provider. |
| `tests/Unit/*ProviderTest.php`, `tests/Feature/AiModelSettingsTest.php`, `ChatbotModelSelectionTest.php`, `tests/Browser/ai-settings.cjs` | Contract test, transisi data, UI dan regresi seluruh provider. |
| `README.md`, dokumen PRD/UAT | Cara konfigurasi hosting, status implementasi, hasil evaluasi dan rollback. |

Urutan kerja: baseline tests → tetapkan kandidat/parameter → resolver transisi + test → adapter + fake HTTP → endpoint/UI → regression suite/Pint/build/browser → credential hosting → preview tiap provider → evaluasi RAG → rollout terkontrol. Penambahan key di hosting dilakukan operator melalui konfigurasi server. Tidak membaca atau menyalin secret untuk menyusun PRD ini.

## 31. Pengujian dan acceptance criteria fase dua

Checklist berikut masih **belum dilaksanakan untuk multi-provider**:

- [ ] Admin/Super Admin dapat test/save/reload tiga provider dengan minimal satu model tervalidasi masing-masing; guest/User/CSRF invalid ditolak tanpa HTTP/write.
- [ ] Pasangan silang, unknown provider/model, key override, URL/prompt override tidak dapat memengaruhi request provider; payload alias ambigu ditolak.
- [ ] Credential generation/embedding kosong memblokir save; preview memakai key provider yang benar dan tidak melakukan embedding. Status key tersedia tidak mengklaim akses model lulus.
- [ ] Missing `ai_model`, konfigurasi Gemini lama, konfigurasi netral valid/invalid, partial pair, default invalid, dan DB outage mengikuti resolver; GET tidak mengubah DB.
- [ ] Save atomik/audit rollback diuji pada kegagalan setiap write. Guard statistik mencakup ketiga keys. Lock diuji pada engine DB target di environment uji, bukan hanya SQLite.
- [ ] Chat sebelum/sesudah save memakai provider berbeda; snapshot konsisten, instance baru membaca pasangan yang sama, tidak ada cache lintas request.
- [ ] Gemini payload baseline tetap; OpenAI parsing multi-item/text, Anthropic multi-block/text, refusal/thinking-only/incomplete/empty/malformed diuji dengan fake.
- [ ] 400/401/403/404/429/500/503/529, timeout/network, key hilang, bounded attempts dan timeout tidak membocorkan credential/provider body.
- [ ] RAG/batas 0,3/top ten/sumber/berita terbit/history/cookie/persistence tetap; tidak ada write chunk/ingestion atau reingest saat provider diganti.
- [ ] Riwayat dari provider lama dapat dipakai provider baru tanpa role invalid; tidak mengirim metadata pribadi tambahan.
- [ ] Browser menguji pergantian provider/model saat preview berjalan, dirty state, error, keyboard, mobile dan zoom; tidak ada XSS atau label aktif palsu.
- [ ] Semua routine tests memakai DB/storage test dan HTTP fake; suite, Pint, build dan diff checks lulus.
- [ ] Model, batas output, biaya/latensi dan akses akun diverifikasi; hasil UAT grounding/bahasa/sumber tercatat per provider. Model yang gagal tidak dinyatakan siap produksi.
- [ ] Runbook konfigurasi dan rollback diuji; proses tidak mereset settings atau 9.732 chunk yang dilaporkan tersedia.

Selesai implementasi berarti semua alur provider terhubung dan test otomatis lulus. Selesai rilis berarti tambahan akses API dan UAT per model sudah lulus. Jika hanya Gemini memiliki credential, deployment boleh mempertahankan Gemini aktif dan menampilkan provider lain belum siap; kondisi tersebut **belum memenuhi penerimaan live tiga provider**.

## 32. UAT pada kondisi hosting saat ini

Tidak ada staging terpisah menurut pengguna. Gunakan database/storage test dan fake untuk pengembangan. Uji koneksi live dapat memakai Test Model hosting secara terbatas tanpa mengaktifkan kandidat; akses OpenAI/Anthropic memerlukan credential masing-masing yang belum dikonfirmasi tersedia.

UAT RAG penuh pada hosting memakai pertanyaan uji dan sesi uji baru serta dokumen yang benar-benar ada dalam korpus. Karena pemilihan provider global memengaruhi warga, jadwalkan pergantian aktif pada jendela uji yang disepakati, catat pasangan sebelumnya, lalu pulihkan setelah uji. Jangan membuat endpoint publik bypass untuk preview RAG penuh dalam scope ini. Alternatif bila tidak ada jendela uji adalah menunggu environment uji terpisah; jangan menjalankan suite/migration destruktif terhadap hosting.

Hasil awal dan matriks Gemini/GPT/Claude dicatat di [UAT AI Model Management](UAT-AI-MODEL-MANAGEMENT.md). Koneksi Flash yang dilaporkan berhasil bukan bukti kualitas RAG; kegagalan Pro bukan bukti semua model Gemini atau provider lain gagal. Penetapan exact ID GPT/Claude, budget output, batas biaya/latensi, credential, dan jendela UAT merupakan keputusan terbuka sebelum enabled/rollout.

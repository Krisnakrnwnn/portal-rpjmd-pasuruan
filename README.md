# Portal RPJMD Kabupaten Pasuruan

Portal informasi RPJMD (Rencana Pembangunan Jangka Menengah Daerah) Kabupaten Pasuruan dengan AI Chatbot.

## 🚀 Fitur Utama

### Chatbot AI (Enhanced)
- ✅ RAG-based responses menggunakan Google Gemini
- ✅ Export chat (PDF/TXT)
- ✅ Voice input & output
- ✅ Multi-language (Indonesian/English)
- ✅ Persistent chat history
- ✅ Time-based greetings

### Portal Features
- Profil Instansi
- Berita & Informasi
- Layanan Publik
- Capaian Pembangunan
- Kontak & Aspirasi

## 📦 Tech Stack

- **Backend:** Laravel 11, PHP 8.1+
- **Database:** SQLite
- **AI:** Google Gemini API
- **Frontend:** Blade, Tailwind CSS, Vanilla JS
- **PDF:** DomPDF

## 🔧 Setup

### 1. Clone & Install
```bash
git clone <repo-url>
cd PBLS6
composer install
npm install && npm run build
```

### 2. Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
GEMINI_API_KEY=your_gemini_api_key_here
DB_CONNECTION=sqlite
DB_DATABASE=/full/path/to/database.sqlite
```

### 3. Database
```bash
touch database/database.sqlite
php artisan migrate
```

### 4. Ingest PDF Documents
```bash
php artisan rag:ingest "path/to/document.pdf"
```

## 🚀 Deploy ke Production

### Via Git Pull (Recommended)
```bash
# Di server
cd /path/to/project
git pull origin main
php artisan migrate
php artisan optimize
```

### Via SCP (Manual)
```bash
# Di lokal
cd PBLS6
deploy-to-server.bat  # Windows
# atau
bash deploy-to-server.sh  # Linux/Mac
```

## 📝 Maintenance

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Optimize
```bash
php artisan optimize
```

## 🔐 Security

- ✅ CSRF protection
- ✅ Rate limiting (20 req/min chat, 10 req/min export)
- ✅ Input validation & sanitization
- ✅ Secure cookies (HttpOnly)
- ⚠️ HTTPS required for voice features

## 📞 Support

Website: https://portal-rpjmd-kabpasuruan.artdevata.net/

---

**Version:** 2.0 (Chatbot Enhanced)  
**Last Updated:** May 2026

@echo off
REM Deploy Script untuk Chatbot Enhancement (Windows)
REM Usage: deploy-to-server.bat

SET SERVER=u689664472@145.79.14.54
SET PORT=65002
SET REMOTE_PATH=/home/u689664472/domains/portal-rpjmd-kabpasuruan.artdevata.net/public_html/laravel

echo ========================================
echo 🚀 Starting deployment to production...
echo ========================================
echo.

echo 📦 Step 1: Uploading Controllers...
scp -P %PORT% app/Http/Controllers/ChatbotController.php %SERVER%:%REMOTE_PATH%/app/Http/Controllers/

echo.
echo 📦 Step 2: Uploading Models...
scp -P %PORT% app/Models/ChatSession.php %SERVER%:%REMOTE_PATH%/app/Models/
scp -P %PORT% app/Models/ChatMessage.php %SERVER%:%REMOTE_PATH%/app/Models/
scp -P %PORT% app/Models/ChatAnalytic.php %SERVER%:%REMOTE_PATH%/app/Models/

echo.
echo 📦 Step 3: Uploading Migrations...
scp -P %PORT% database/migrations/2026_05_05_082857_create_chat_sessions_table.php %SERVER%:%REMOTE_PATH%/database/migrations/
scp -P %PORT% database/migrations/2026_05_05_082905_create_chat_messages_table.php %SERVER%:%REMOTE_PATH%/database/migrations/
scp -P %PORT% database/migrations/2026_05_05_082912_create_chat_analytics_table.php %SERVER%:%REMOTE_PATH%/database/migrations/
scp -P %PORT% database/migrations/2026_05_05_082919_create_shared_messages_table.php %SERVER%:%REMOTE_PATH%/database/migrations/

echo.
echo 📦 Step 4: Uploading Views...
scp -P %PORT% resources/views/layouts/app.blade.php %SERVER%:%REMOTE_PATH%/resources/views/layouts/
scp -P %PORT% resources/views/exports/chat-pdf.blade.php %SERVER%:%REMOTE_PATH%/resources/views/exports/

echo.
echo 📦 Step 5: Uploading Routes...
scp -P %PORT% routes/web.php %SERVER%:%REMOTE_PATH%/routes/

echo.
echo ✅ Upload selesai!
echo.
echo 🔧 Langkah selanjutnya (jalankan di SSH):
echo    cd %REMOTE_PATH%
echo    php artisan migrate
echo    php artisan cache:clear
echo    php artisan config:clear
echo    php artisan view:clear
echo    php artisan route:clear
echo    php artisan optimize
echo.
echo ========================================
echo ✨ Deployment script selesai!
echo ========================================
pause

#!/bin/bash

# Script untuk Upload Gambar ke Server (FIXED untuk struktur hosting)
# Usage: bash deploy-images-to-server-fixed.sh

SERVER="u689664472@145.79.14.54"
PORT="65002"
# PERBAIKAN: public_html adalah document root, bukan laravel/public
REMOTE_PATH="/home/u689664472/domains/portal-rpjmd-kabpasuruan.artdevata.net/public_html"

echo "🖼️  Deploying images to production server..."
echo "================================================"
echo "Target: $REMOTE_PATH/uploads/news/"
echo ""

# Tanya konfirmasi
read -p "Upload semua gambar di public/uploads/news/ ke server? [y/n]: " confirm
if [ "$confirm" != "y" ]; then
    echo "❌ Upload dibatalkan."
    exit 1
fi

echo ""
echo "📦 Step 1: Membuat folder uploads/news di server..."
ssh -p $PORT $SERVER "mkdir -p $REMOTE_PATH/uploads/news && chmod 755 $REMOTE_PATH/uploads && chmod 755 $REMOTE_PATH/uploads/news"

echo ""
echo "📦 Step 2: Upload gambar .jpg..."
scp -P $PORT public/uploads/news/*.jpg $SERVER:$REMOTE_PATH/uploads/news/ 2>/dev/null || echo "   ⚠️  Tidak ada file .jpg"

echo ""
echo "📦 Step 3: Upload gambar .png..."
scp -P $PORT public/uploads/news/*.png $SERVER:$REMOTE_PATH/uploads/news/ 2>/dev/null || echo "   ⚠️  Tidak ada file .png"

echo ""
echo "📦 Step 4: Upload gambar .gif..."
scp -P $PORT public/uploads/news/*.gif $SERVER:$REMOTE_PATH/uploads/news/ 2>/dev/null || echo "   ⚠️  Tidak ada file .gif"

echo ""
echo "📦 Step 5: Upload gambar .webp..."
scp -P $PORT public/uploads/news/*.webp $SERVER:$REMOTE_PATH/uploads/news/ 2>/dev/null || echo "   ⚠️  Tidak ada file .webp"

echo ""
echo "📦 Step 6: Upload .htaccess untuk keamanan..."
scp -P $PORT public/uploads/.htaccess $SERVER:$REMOTE_PATH/uploads/

echo ""
echo "📦 Step 7: Set permission file gambar..."
ssh -p $PORT $SERVER "chmod 644 $REMOTE_PATH/uploads/news/* 2>/dev/null"

echo ""
echo "✅ Upload gambar selesai!"
echo ""
echo "🧪 Testing:"
echo "   Buka browser dan cek:"
echo "   https://portal-rpjmd-kabpasuruan.artdevata.net/uploads/news/jGBTrI791ICUGpuGBgRf3qcqCEFIbyeFiEAjPnS0.jpg"
echo ""
echo "📋 Struktur di server:"
echo "   $REMOTE_PATH/uploads/news/*.jpg"
echo ""
echo "================================================"
echo "✨ Deployment selesai!"

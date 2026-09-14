<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Protected Portal Routes (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::get('/', [PortalController::class, 'home'])->name('home');
    Route::get('/profil', [PortalController::class, 'profil'])->name('profil');
    Route::get('/berita', [PortalController::class, 'berita'])->name('berita');
    Route::get('/berita/{slug}', [PortalController::class, 'beritaDetail'])->name('berita.detail');
    Route::get('/galeri', [PortalController::class, 'galeri'])->name('galeri');
    Route::get('/dokumen', [PortalController::class, 'dokumen'])->name('dokumen');
    Route::get('/kontak', [PortalController::class, 'kontak'])->name('kontak');
    Route::post('/kontak', [PortalController::class, 'storeContact'])->name('kontak.store');
});

// API Chatbot (with rate limiting)
Route::post('/api/chat', [ChatbotController::class, 'chat'])
    ->middleware('throttle:20,1') // Max 20 requests per minute
    ->name('api.chat');

// Load chat history
Route::get('/api/chat/history', [ChatbotController::class, 'loadHistory'])
    ->name('api.chat.history');

// Start new session
Route::post('/api/chat/new-session', [ChatbotController::class, 'newSession'])
    ->name('api.chat.new_session');

// Clear chat history
Route::post('/api/chat/clear', [ChatbotController::class, 'clearHistory'])
    ->name('api.chat.clear');

// Feedback endpoint (optional analytics)
Route::post('/api/chat/feedback', [ChatbotController::class, 'feedback'])
    ->name('api.chat.feedback');

// Export chat history
Route::post('/api/chat/export', [ChatbotController::class, 'exportChat'])
    ->middleware('throttle:10,1') // Max 10 exports per minute
    ->name('api.chat.export');

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Admin Routes (Protected by Auth + Admin Role)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.role'])->group(function () {
    Route::get('/', [AdminPageController::class, 'dashboard'])->name('dashboard');
    Route::get('/berita', [AdminPageController::class, 'berita'])->name('berita.index');
    Route::get('/berita/tambah', [AdminPageController::class, 'createBerita'])->name('berita.create');
    Route::get('/berita/{news}/edit', [AdminPageController::class, 'editBerita'])->name('berita.edit');
    Route::get('/dokumen', [AdminPageController::class, 'dokumen'])->name('dokumen.index');
    Route::get('/dokumen/tambah', [AdminPageController::class, 'createDokumen'])->name('dokumen.create');
    Route::get('/dokumen/{document}/edit', [AdminPageController::class, 'editDokumen'])->name('dokumen.edit');
    Route::get('/galeri', [AdminPageController::class, 'galeri'])->name('galeri.index');
    Route::get('/aspirasi', [AdminPageController::class, 'aspirasi'])->name('aspirasi.index');
    Route::get('/ingest', [AdminPageController::class, 'ingest'])->name('ingest.index');
    Route::get('/setelan', [AdminPageController::class, 'setelan'])->name('setelan.index');
    Route::post('/stats', [AdminController::class, 'updateStats'])->name('update_stats');
    Route::post('/hero-stats', [AdminController::class, 'updateHeroStats'])->name('update_hero_stats');
    Route::post('/hero-stats/add', [AdminController::class, 'storeHeroStat'])->name('store_hero_stat');
    Route::delete('/hero-stats/{id}', [AdminController::class, 'deleteHeroStat'])->name('delete_hero_stat');

    // Berita (Admin & Super Admin)
    Route::post('/berita', [AdminController::class, 'storeNews'])->name('store_news');
    Route::put('/berita/{id}', [AdminController::class, 'updateNews'])->name('update_news');
    Route::delete('/berita/{id}', [AdminController::class, 'deleteNews'])->name('delete_news');

    // Galeri (Admin & Super Admin)
    Route::post('/galeri', [AdminController::class, 'storeGallery'])->name('store_gallery');
    Route::put('/galeri/{id}', [AdminController::class, 'updateGallery'])->name('update_gallery');
    Route::delete('/galeri/{id}', [AdminController::class, 'deleteGallery'])->name('delete_gallery');

    // Aspirasi/Kontak (Admin & Super Admin)
    Route::post('/kontak/{id}/resolve', [AdminController::class, 'resolveContact'])->name('resolve_contact');
    Route::delete('/kontak/{id}', [AdminController::class, 'deleteContact'])->name('delete_contact');

    // Toggle Publik/Draft (Admin & Super Admin)
    Route::post('/berita/{id}/toggle', [AdminController::class, 'togglePublish'])->name('toggle_publish');

    // Profil Instansi (Admin & Super Admin)
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('update_profile');

    // Document Categories
    Route::post('/document-categories', [AdminController::class, 'storeDocumentCategory'])->name('document-categories.store');
    Route::put('/document-categories/{id}', [AdminController::class, 'updateDocumentCategory'])->name('document-categories.update');
    Route::delete('/document-categories/{id}', [AdminController::class, 'destroyDocumentCategory'])->name('document-categories.destroy');

    // Public Documents
    Route::post('/documents', [AdminController::class, 'storeDocument'])->name('store_document');
    Route::put('/documents/{id}', [AdminController::class, 'updateDocument'])->name('update_document');
    Route::delete('/documents/{id}', [AdminController::class, 'deleteDocument'])->name('delete_document');

    // Setelan (Admin & Super Admin)
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('update_settings');

    // Chatbot Ingestion (Admin & Super Admin)
    Route::post('/chatbot/ingest', [AdminController::class, 'ingestPdf'])->name('chatbot.ingest');
    Route::get('/chatbot/ingest-status/{id}', [AdminController::class, 'checkIngestStatus'])->name('chatbot.ingest_status');
    Route::delete('/chatbot/ingest/{id}', [AdminController::class, 'destroyIngest'])->name('chatbot.ingest.destroy');
    Route::post('/chatbot/ingest/{id}/cancel', [AdminController::class, 'cancelIngest'])->name('chatbot.ingest.cancel');

    // Super Admin ONLY
    Route::middleware('super.admin')->group(function () {
        Route::get('/pengguna', [AdminPageController::class, 'pengguna'])->name('pengguna.index');
        Route::get('/pengguna/tambah', [AdminPageController::class, 'createPengguna'])->name('pengguna.create');
        Route::get('/pengguna/{user}/edit', [AdminPageController::class, 'editPengguna'])->name('pengguna.edit');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('store_user');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('update_user');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('delete_user');
    });
});

// Breeze Default Routes
Route::get('/dashboard', function () {
    return redirect()->route(in_array(request()->user()->role, ['Admin', 'Super Admin'], true)
        ? 'admin.dashboard'
        : 'home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

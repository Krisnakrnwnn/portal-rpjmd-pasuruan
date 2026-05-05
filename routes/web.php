<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Public Portal Routes
Route::get('/', [PortalController::class, 'home'])->name('home');
Route::get('/profil', [PortalController::class, 'profil'])->name('profil');
Route::get('/berita', [PortalController::class, 'berita'])->name('berita');
Route::get('/berita/{slug}', [PortalController::class, 'beritaDetail'])->name('berita.detail');
Route::get('/layanan', [PortalController::class, 'layanan'])->name('layanan');
Route::get('/capaian', [PortalController::class, 'capaian'])->name('capaian');
Route::get('/kontak', [PortalController::class, 'kontak'])->name('kontak');
Route::post('/kontak', [PortalController::class, 'storeContact'])->name('kontak.store');

// API Chatbot (with rate limiting)
Route::post('/api/chat', [\App\Http\Controllers\ChatbotController::class, 'chat'])
    ->middleware('throttle:20,1') // Max 20 requests per minute
    ->name('api.chat');

// Load chat history
Route::get('/api/chat/history', [\App\Http\Controllers\ChatbotController::class, 'loadHistory'])
    ->name('api.chat.history');

// Start new session
Route::post('/api/chat/new-session', [\App\Http\Controllers\ChatbotController::class, 'newSession'])
    ->name('api.chat.new_session');

// Clear chat history
Route::post('/api/chat/clear', [\App\Http\Controllers\ChatbotController::class, 'clearHistory'])
    ->name('api.chat.clear');

// Feedback endpoint (optional analytics)
Route::post('/api/chat/feedback', [\App\Http\Controllers\ChatbotController::class, 'feedback'])
    ->name('api.chat.feedback');

// Export chat history
Route::post('/api/chat/export', [\App\Http\Controllers\ChatbotController::class, 'exportChat'])
    ->middleware('throttle:10,1') // Max 10 exports per minute
    ->name('api.chat.export');

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Admin Routes (Protected by Auth + Admin Role)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.role'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/stats', [AdminController::class, 'updateStats'])->name('update_stats');
    Route::post('/hero-stats', [AdminController::class, 'updateHeroStats'])->name('update_hero_stats');
    Route::post('/hero-stats/add', [AdminController::class, 'storeHeroStat'])->name('store_hero_stat');
    Route::delete('/hero-stats/{id}', [AdminController::class, 'deleteHeroStat'])->name('delete_hero_stat');

    // Berita (Admin & Super Admin)
    Route::post('/berita', [AdminController::class, 'storeNews'])->name('store_news');
    Route::put('/berita/{id}', [AdminController::class, 'updateNews'])->name('update_news');
    Route::delete('/berita/{id}', [AdminController::class, 'deleteNews'])->name('delete_news');

    // Layanan (Admin & Super Admin)
    Route::post('/layanan', [AdminController::class, 'storeService'])->name('store_service');
    Route::put('/layanan/{id}', [AdminController::class, 'updateService'])->name('update_service');
    Route::delete('/layanan/{id}', [AdminController::class, 'deleteService'])->name('delete_service');

    // Capaian Sektor & Indikator (Admin & Super Admin)
    Route::post('/sectormake', [AdminController::class, 'storeSector'])->name('store_sector');
    Route::put('/sector/{id}', [AdminController::class, 'updateSector'])->name('update_sector');
    Route::delete('/sector/{id}', [AdminController::class, 'deleteSector'])->name('delete_sector');
    Route::post('/indicator', [AdminController::class, 'storeIndicator'])->name('store_indicator');
    Route::put('/indicator/{id}', [AdminController::class, 'updateIndicator'])->name('update_indicator');
    Route::delete('/indicator/{id}', [AdminController::class, 'deleteIndicator'])->name('delete_indicator');

    // Aspirasi/Kontak (Admin & Super Admin)
    Route::post('/kontak/{id}/resolve', [AdminController::class, 'resolveContact'])->name('resolve_contact');

    // Toggle Publik/Draft (Admin & Super Admin)
    Route::post('/berita/{id}/toggle', [AdminController::class, 'togglePublish'])->name('toggle_publish');

    // Profil Instansi (Admin & Super Admin)
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('update_profile');

    // Super Admin ONLY
    Route::middleware('super.admin')->group(function () {
        Route::post('/users', [AdminController::class, 'storeUser'])->name('store_user');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('update_user');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('delete_user');
    });
});

// Breeze Default Routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

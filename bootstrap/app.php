<?php

use App\Http\Middleware\AdminRoleMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->redirectUsersTo(function ($request) {
            return in_array($request->user()?->role, ['Admin', 'Super Admin'], true)
                ? route('admin.dashboard')
                : route('dashboard');
        });

        $middleware->alias([
            'admin.role' => AdminRoleMiddleware::class,
            'super.admin' => SuperAdminMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/api/chat',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Menjalankan queue secara otomatis setiap menit (Sangat penting untuk Shared Hosting)
        $schedule->command('queue:work --stop-when-empty')->everyMinute();
        $schedule->command('admin-otp:prune')->daily();
    })->create();

// Custom Public Path untuk Shared Hosting
// Jika index.php ada satu level di atas (public_html/), berarti kita di hosting
// Di mana struktur: public_html/ = webroot, public_html/laravel/ = app Laravel
if (file_exists(dirname(__DIR__).'/../index.php')) {
    $app->usePublicPath(realpath(dirname(__DIR__).'/../'));
}

return $app;

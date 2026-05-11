<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin.role' => \App\Http\Middleware\AdminRoleMiddleware::class,
            'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);
        
        $middleware->validateCsrfTokens(except: [
            '/api/chat'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Custom Public Path untuk Shared Hosting
// Jika index.php ada satu level di atas (public_html/), berarti kita di hosting
// Di mana struktur: public_html/ = webroot, public_html/laravel/ = app Laravel
if (file_exists(dirname(__DIR__) . '/../index.php')) {
    $app->usePublicPath(realpath(dirname(__DIR__) . '/../'));
}

return $app;

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!app()->runningInConsole()) {
            $socials = \App\Models\Profile::whereIn('key', ['ig_link', 'fb_link', 'wa_number'])
                         ->get()
                         ->pluck('content', 'key');
            view()->share('socials', $socials);
        }
    }
}

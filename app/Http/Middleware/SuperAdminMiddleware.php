<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'Super Admin') {
            return $next($request);
        }

        return redirect(route('admin.dashboard'))->with('error', 'Akses ditolak. Fitur ini hanya untuk Super Admin.');
    }
}

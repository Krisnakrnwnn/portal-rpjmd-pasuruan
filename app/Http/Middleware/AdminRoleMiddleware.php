<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && in_array(auth()->user()->role, ['Super Admin', 'Admin'])) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientLoginController extends Controller
{
    /**
     * Display the Client login view.
     * Redirect authenticated clients away from login page.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // If already authenticated as a client (role = User), redirect to home
        if (Auth::check() && Auth::user()->role === 'User') {
            $request->session()->forget('url.intended');

            return redirect()->route('home');
        }

        return view('auth.client-login');
    }

    /**
     * Handle an incoming Client authentication request.
     * Only authenticates users with role 'User'. Admins are rejected.
     */
    public function store(ClientLoginRequest $request): RedirectResponse
    {
        $user = $request->authenticate();

        Auth::guard('web')->login($user, false);
        $request->session()->regenerate();

        $request->session()->forget('url.intended');

        return redirect()->route('home');
    }

    /**
     * Destroy the Client authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }
}

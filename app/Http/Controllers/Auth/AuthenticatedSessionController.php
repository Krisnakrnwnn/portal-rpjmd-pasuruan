<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\OtpDeliveryException;
use App\Exceptions\OtpRateLimitedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AdminOtpService;
use App\Services\AuthSecurityEventRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, AdminOtpService $otp, AuthSecurityEventRecorder $events): RedirectResponse
    {
        $user = $request->authenticate($events);

        if (! config('admin-auth.otp.enabled')) {
            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            $user->forceFill(['last_login_at' => now()])->save();
            $events->record($request, 'otp_feature_bypassed', 'success', $user);

            return redirect()->intended(route('admin.dashboard'));
        }

        try {
            $challenge = $otp->createChallenge(
                $user,
                $request,
                $request->boolean('remember'),
                $request->session()->get('url.intended'),
            );
        } catch (OtpRateLimitedException $exception) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => "Terlalu banyak permintaan kode. Silakan coba lagi dalam {$exception->retryAfter} detik."]);
        } catch (OtpDeliveryException) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Kode verifikasi belum dapat dikirim. Silakan coba beberapa saat lagi.']);
        }

        $request->session()->put(AdminOtpService::SESSION_KEY, $challenge->challenge_id);

        return redirect()->route('otp.show')->with('status', 'Kode verifikasi telah dikirim.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request, AuthSecurityEventRecorder $events): RedirectResponse
    {
        $user = $request->user();
        $events->record($request, 'logout', 'success', $user);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

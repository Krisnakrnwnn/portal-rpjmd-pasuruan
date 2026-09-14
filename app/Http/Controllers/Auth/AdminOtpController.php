<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\OtpCooldownException;
use App\Exceptions\OtpDeliveryException;
use App\Exceptions\OtpRateLimitedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyAdminOtpRequest;
use App\Models\AdminLoginOtp;
use App\Services\AdminOtpService;
use App\Services\AuthSecurityEventRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminOtpController extends Controller
{
    public function show(Request $request, AdminOtpService $otp): View|RedirectResponse
    {
        $challenge = $this->challenge($request);

        if ($challenge === null || $challenge->used_at !== null || $challenge->cancelled_at !== null) {
            $request->session()->forget(AdminOtpService::SESSION_KEY);

            return redirect()->route('login');
        }

        return view('auth.verify-admin-otp', [
            'maskedEmail' => $otp->maskEmail($challenge->user->email),
            'expiresIn' => max(0, $challenge->expires_at->getTimestamp() - now()->getTimestamp()),
            'resendIn' => $otp->cooldownRemaining($challenge),
        ]);
    }

    public function verify(
        VerifyAdminOtpRequest $request,
        AdminOtpService $otp,
        AuthSecurityEventRecorder $events,
    ): RedirectResponse {
        $challenge = $this->challenge($request);

        if ($challenge === null || $challenge->used_at !== null || $challenge->cancelled_at !== null) {
            $request->session()->forget(AdminOtpService::SESSION_KEY);

            return redirect()->route('login')->withErrors([
                'email' => 'Sesi verifikasi tidak aktif. Silakan login kembali.',
            ]);
        }

        $result = $otp->verify($challenge, $request->string('otp')->toString(), $request);

        if ($result === 'invalid') {
            throw ValidationException::withMessages([
                'otp' => 'Kode verifikasi tidak sesuai.',
            ]);
        }

        if ($result === 'expired') {
            throw ValidationException::withMessages([
                'otp' => 'Kode verifikasi telah kedaluwarsa. Silakan kirim kode baru.',
            ]);
        }

        if (in_array($result, ['inactive', 'attempt_limit'], true)) {
            $request->session()->forget(AdminOtpService::SESSION_KEY);

            return redirect()->route('login')->withErrors([
                'email' => $result === 'attempt_limit'
                    ? 'Terlalu banyak percobaan. Silakan login kembali.'
                    : 'Sesi verifikasi tidak aktif. Silakan login kembali.',
            ]);
        }

        $user = $challenge->user;
        Auth::guard('web')->login($user, $challenge->remember);
        $request->session()->regenerate();
        $request->session()->forget(AdminOtpService::SESSION_KEY);
        $user->forceFill(['last_login_at' => now()])->save();
        $events->record($request, 'otp_verified', 'success', $user);

        return $challenge->intended_url
            ? redirect()->to($challenge->intended_url)
            : redirect()->route('admin.dashboard');
    }

    public function resend(Request $request, AdminOtpService $otp): RedirectResponse
    {
        $challenge = $this->challenge($request);

        if ($challenge === null || $challenge->used_at !== null || $challenge->cancelled_at !== null) {
            $request->session()->forget(AdminOtpService::SESSION_KEY);

            return redirect()->route('login');
        }

        try {
            $otp->resend($challenge, $request);
        } catch (OtpCooldownException $exception) {
            return back()->withErrors([
                'resend' => "Kode baru dapat dikirim dalam {$exception->retryAfter} detik.",
            ]);
        } catch (OtpRateLimitedException $exception) {
            return back()->withErrors([
                'resend' => "Batas pengiriman kode tercapai. Silakan coba lagi dalam {$exception->retryAfter} detik.",
            ]);
        } catch (OtpDeliveryException) {
            return back()->withErrors([
                'resend' => 'Kode verifikasi belum dapat dikirim. Silakan coba beberapa saat lagi.',
            ]);
        }

        return back()->with('status', 'Kode verifikasi baru telah dikirim.');
    }

    public function cancel(Request $request, AdminOtpService $otp): RedirectResponse
    {
        $challenge = $this->challenge($request);

        if ($challenge !== null) {
            $otp->cancel($challenge);
        }

        $request->session()->forget(AdminOtpService::SESSION_KEY);

        return redirect()->route('login')->with('status', 'Verifikasi dibatalkan. Silakan login kembali.');
    }

    private function challenge(Request $request): ?AdminLoginOtp
    {
        $challengeId = $request->session()->get(AdminOtpService::SESSION_KEY);

        if (! is_string($challengeId) || $challengeId === '') {
            return null;
        }

        return AdminLoginOtp::query()
            ->with('user')
            ->whereHas('user', fn ($query) => $query->whereIn('role', ['Admin', 'Super Admin']))
            ->where('challenge_id', $challengeId)
            ->first();
    }
}

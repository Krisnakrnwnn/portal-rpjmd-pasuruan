<?php

namespace App\Services;

use App\Exceptions\OtpCooldownException;
use App\Exceptions\OtpDeliveryException;
use App\Exceptions\OtpRateLimitedException;
use App\Mail\AdminLoginOtpMail;
use App\Models\AdminLoginOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

class AdminOtpService
{
    public const SESSION_KEY = 'admin_otp.challenge_id';

    public function __construct(private readonly AuthSecurityEventRecorder $events) {}

    public function createChallenge(User $user, Request $request, bool $remember, ?string $intendedUrl): AdminLoginOtp
    {
        $this->ensureDeliveryIsAllowed($user, $request);

        $code = $this->generateCode();

        $challenge = DB::transaction(function () use ($user, $request, $remember, $intendedUrl, $code) {
            AdminLoginOtp::query()
                ->where('user_id', $user->id)
                ->active()
                ->update(['cancelled_at' => now()]);

            return AdminLoginOtp::create([
                'challenge_id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(config('admin-auth.otp.expires_minutes')),
                'remember' => $remember,
                'intended_url' => $this->safeIntendedUrl($intendedUrl),
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 512),
            ]);
        });

        $this->events->record($request, 'otp_requested', 'pending', $user);

        try {
            $this->deliver($challenge, $code, $request);
        } catch (Throwable $exception) {
            $challenge->update(['cancelled_at' => now()]);
            $this->events->record($request, 'otp_delivery_failed', 'error', $user);

            Log::error('Pengiriman OTP administrator gagal.', [
                'exception_type' => $exception::class,
            ]);

            throw new OtpDeliveryException(previous: $exception);
        }

        return $challenge->refresh();
    }

    public function resend(AdminLoginOtp $challenge, Request $request): AdminLoginOtp
    {
        if ($challenge->used_at !== null || $challenge->cancelled_at !== null) {
            throw new OtpRateLimitedException(0);
        }

        $cooldown = $this->cooldownRemaining($challenge);

        if ($cooldown > 0) {
            $this->events->record($request, 'otp_resend_rate_limited', 'denied', $challenge->user, [
                'reason' => 'cooldown',
                'retry_after' => $cooldown,
            ]);

            throw new OtpCooldownException($cooldown);
        }

        $this->ensureDeliveryIsAllowed($challenge->user, $request);

        if ($challenge->send_count >= config('admin-auth.otp.max_deliveries')) {
            $retryAfter = max(1, RateLimiter::availableIn($this->accountDeliveryKey($challenge->user)));
            $this->events->record($request, 'otp_resend_rate_limited', 'denied', $challenge->user, [
                'reason' => 'challenge_limit',
                'retry_after' => $retryAfter,
            ]);

            throw new OtpRateLimitedException($retryAfter);
        }

        $code = $this->generateCode();

        $challenge = DB::transaction(function () use ($challenge, $code) {
            $locked = AdminLoginOtp::query()->lockForUpdate()->findOrFail($challenge->id);

            if ($locked->used_at !== null || $locked->cancelled_at !== null) {
                throw new OtpRateLimitedException(0);
            }

            $locked->update([
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(config('admin-auth.otp.expires_minutes')),
            ]);

            return $locked;
        });

        $this->events->record($request, 'otp_requested', 'pending', $challenge->user, ['resend' => true]);

        try {
            $this->deliver($challenge, $code, $request);
        } catch (Throwable $exception) {
            $this->events->record($request, 'otp_delivery_failed', 'error', $challenge->user, ['resend' => true]);

            Log::error('Pengiriman ulang OTP administrator gagal.', [
                'exception_type' => $exception::class,
            ]);

            throw new OtpDeliveryException(previous: $exception);
        }

        return $challenge->refresh();
    }

    public function verify(AdminLoginOtp $challenge, string $code, Request $request): string
    {
        return DB::transaction(function () use ($challenge, $code, $request) {
            $locked = AdminLoginOtp::query()->with('user')->lockForUpdate()->findOrFail($challenge->id);

            if ($locked->used_at !== null || $locked->cancelled_at !== null) {
                return 'inactive';
            }

            if (! in_array($locked->user->role, ['Admin', 'Super Admin'], true)) {
                $locked->update(['cancelled_at' => now()]);

                return 'inactive';
            }

            if ($locked->isExpired()) {
                $this->events->record($request, 'otp_expired', 'denied', $locked->user);

                return 'expired';
            }

            if ($locked->attempts >= config('admin-auth.otp.max_attempts')) {
                $locked->update(['cancelled_at' => now()]);
                $this->events->record($request, 'otp_attempt_limit', 'denied', $locked->user);

                return 'attempt_limit';
            }

            if (! Hash::check($code, $locked->code_hash)) {
                $locked->increment('attempts');
                $locked->refresh();

                if ($locked->attempts >= config('admin-auth.otp.max_attempts')) {
                    $locked->update(['cancelled_at' => now()]);
                    $this->events->record($request, 'otp_attempt_limit', 'denied', $locked->user);

                    return 'attempt_limit';
                }

                $this->events->record($request, 'otp_invalid', 'denied', $locked->user, [
                    'remaining_attempts' => config('admin-auth.otp.max_attempts') - $locked->attempts,
                ]);

                return 'invalid';
            }

            $locked->update(['used_at' => now()]);

            AdminLoginOtp::query()
                ->where('user_id', $locked->user_id)
                ->whereKeyNot($locked->id)
                ->active()
                ->update(['cancelled_at' => now()]);

            return 'verified';
        });
    }

    public function cancel(AdminLoginOtp $challenge): void
    {
        if ($challenge->used_at === null && $challenge->cancelled_at === null) {
            $challenge->update(['cancelled_at' => now()]);
        }
    }

    public function cooldownRemaining(AdminLoginOtp $challenge): int
    {
        if ($challenge->last_sent_at === null) {
            return 0;
        }

        $availableAt = $challenge->last_sent_at->getTimestamp()
            + (int) config('admin-auth.otp.resend_cooldown_seconds');

        return max(0, $availableAt - now()->getTimestamp());
    }

    public function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visibleLength = min(2, max(1, mb_strlen($local)));

        return mb_substr($local, 0, $visibleLength).'***@'.$domain;
    }

    private function deliver(AdminLoginOtp $challenge, string $code, Request $request): void
    {
        Mail::to($challenge->user)->send(new AdminLoginOtpMail(
            $code,
            now()->timezone(config('app.timezone'))->format('d M Y H:i T'),
        ));

        $challenge->forceFill([
            'last_sent_at' => now(),
            'send_count' => $challenge->send_count + 1,
        ])->save();

        $window = (int) config('admin-auth.otp.delivery_window_seconds');
        RateLimiter::hit($this->accountDeliveryKey($challenge->user), $window);
        RateLimiter::hit($this->ipDeliveryKey($request), $window);

        $this->events->record($request, 'otp_sent', 'success', $challenge->user, [
            'delivery_number' => $challenge->send_count,
        ]);
    }

    private function ensureDeliveryIsAllowed(User $user, Request $request): void
    {
        $accountKey = $this->accountDeliveryKey($user);
        $ipKey = $this->ipDeliveryKey($request);
        $accountLimited = RateLimiter::tooManyAttempts($accountKey, config('admin-auth.otp.max_deliveries'));
        $ipLimited = RateLimiter::tooManyAttempts($ipKey, config('admin-auth.otp.ip_max_deliveries'));

        if (! $accountLimited && ! $ipLimited) {
            return;
        }

        $retryAfter = max(
            $accountLimited ? RateLimiter::availableIn($accountKey) : 0,
            $ipLimited ? RateLimiter::availableIn($ipKey) : 0,
        );

        $this->events->record($request, 'otp_resend_rate_limited', 'denied', $user, [
            'reason' => $accountLimited ? 'account_limit' : 'ip_limit',
            'retry_after' => $retryAfter,
        ]);

        throw new OtpRateLimitedException(max(1, $retryAfter));
    }

    private function accountDeliveryKey(User $user): string
    {
        return 'admin-otp-delivery:user:'.$user->id;
    }

    private function ipDeliveryKey(Request $request): string
    {
        return 'admin-otp-delivery:ip:'.hash('sha256', (string) $request->ip());
    }

    private function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function safeIntendedUrl(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        if (Str::startsWith($url, '/') && ! Str::startsWith($url, '//')) {
            return $url;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return $host !== null && hash_equals((string) parse_url(config('app.url'), PHP_URL_HOST), (string) $host)
            ? $url
            : null;
    }
}

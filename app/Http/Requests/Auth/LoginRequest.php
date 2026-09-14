<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\AuthSecurityEventRecorder;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(AuthSecurityEventRecorder $events): User
    {
        $this->ensureIsNotRateLimited($events);

        $credentials = $this->only('email', 'password');
        $provider = Auth::guard('web')->getProvider();
        $user = $provider->retrieveByCredentials($credentials);
        $validRole = $user instanceof User && in_array($user->role, ['User', 'Admin', 'Super Admin'], true);

        if (! $validRole || ! $provider->validateCredentials($user, $credentials)) {
            RateLimiter::hit($this->throttleKey());
            $events->record($this, 'credentials_failed', 'denied', $validRole ? $user : null);

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi tidak sesuai.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        return $user;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(AuthSecurityEventRecorder $events): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $events->record($this, 'credentials_rate_limited', 'denied', metadata: [
            'retry_after' => $seconds,
        ]);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}

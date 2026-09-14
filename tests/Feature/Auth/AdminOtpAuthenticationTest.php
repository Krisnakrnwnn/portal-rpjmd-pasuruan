<?php

namespace Tests\Feature\Auth;

use App\Mail\AdminLoginOtpMail;
use App\Models\AdminAuthEvent;
use App\Models\AdminLoginOtp;
use App\Models\User;
use App\Services\AdminOtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;
use Tests\TestCase;

class AdminOtpAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'admin-auth.otp.enabled' => true,
            'admin-auth.otp.expires_minutes' => 5,
            'admin-auth.otp.max_attempts' => 5,
            'admin-auth.otp.resend_cooldown_seconds' => 60,
            'admin-auth.otp.max_deliveries' => 3,
            'admin-auth.otp.delivery_window_seconds' => 900,
            'admin-auth.otp.ip_max_deliveries' => 100,
        ]);

        RateLimiter::clear('admin-otp-delivery:ip:'.hash('sha256', '127.0.0.1'));
    }

    public function test_valid_admin_credentials_create_hashed_otp_without_authenticating(): void
    {
        [$user, $challenge, $code, $response] = $this->beginOtp('Admin');

        $this->assertGuest();
        $response->assertRedirect(route('otp.show', absolute: false));
        $this->assertTrue(Hash::check($code, $challenge->code_hash));
        $this->assertNotSame($code, $challenge->code_hash);
        $this->assertSame(0, $challenge->attempts);
        $this->assertSame(1, $challenge->send_count);
        $this->assertDatabaseHas('admin_auth_events', [
            'user_id' => $user->id,
            'event' => 'otp_sent',
            'outcome' => 'success',
        ]);
    }

    public function test_invalid_credentials_and_unknown_roles_do_not_send_otp(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'Admin']);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors(['email' => 'Email atau kata sandi tidak sesuai.']);

        $regularUser = User::factory()->create(['role' => 'Unknown']);

        $this->post('/login', [
            'email' => $regularUser->email,
            'password' => 'password',
        ])->assertSessionHasErrors(['email' => 'Email atau kata sandi tidak sesuai.']);

        Mail::assertNothingSent();
        $this->assertDatabaseCount('admin_login_otps', 0);
        $this->assertGuest();
    }

    public function test_correct_otp_authenticates_admin_updates_login_time_and_consumes_challenge(): void
    {
        [$user, $challenge, $code] = $this->beginOtp('Admin', remember: true);
        $sessionIdBeforeVerification = session()->getId();

        $response = $this->post(route('otp.verify'), ['otp' => $code]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
        $this->assertNotNull($challenge->fresh()->used_at);
        $this->assertFalse(session()->has(AdminOtpService::SESSION_KEY));
        $this->assertNotSame($sessionIdBeforeVerification, session()->getId());
        $response->assertCookie(auth()->guard('web')->getRecallerName());
    }

    public function test_both_administrator_roles_use_the_same_otp_flow(): void
    {
        foreach (['Admin', 'Super Admin'] as $role) {
            [$user, $challenge, $code] = $this->beginOtp($role);

            $this->post(route('otp.verify'), ['otp' => $code])
                ->assertRedirect(route('admin.dashboard', absolute: false));

            $this->assertAuthenticatedAs($user);
            $this->post(route('logout'));
            $this->assertNotNull($challenge->fresh()->used_at);
        }
    }

    public function test_wrong_otp_increments_attempts_and_fifth_failure_cancels_the_challenge(): void
    {
        [, $challenge] = $this->beginOtp();

        foreach (range(1, 4) as $attempt) {
            $this->post(route('otp.verify'), ['otp' => '999999'])
                ->assertSessionHasErrors(['otp' => 'Kode verifikasi tidak sesuai.']);
            $this->assertSame($attempt, $challenge->fresh()->attempts);
            $this->assertGuest();
        }

        $this->post(route('otp.verify'), ['otp' => '999999'])
            ->assertRedirect(route('login', absolute: false))
            ->assertSessionHasErrors(['email' => 'Terlalu banyak percobaan. Silakan login kembali.']);

        $this->assertSame(5, $challenge->fresh()->attempts);
        $this->assertNotNull($challenge->fresh()->cancelled_at);
        $this->assertGuest();
    }

    public function test_expired_otp_is_rejected_and_can_be_replaced_after_cooldown(): void
    {
        [, $challenge, $oldCode] = $this->beginOtp();
        $challenge->update([
            'expires_at' => now()->subSecond(),
            'last_sent_at' => now()->subSeconds(61),
        ]);

        $this->post(route('otp.verify'), ['otp' => $oldCode])
            ->assertSessionHasErrors(['otp' => 'Kode verifikasi telah kedaluwarsa. Silakan kirim kode baru.']);

        $this->post(route('otp.resend'))->assertSessionHas('status');

        $newChallenge = $challenge->fresh();
        $newCode = Mail::sent(AdminLoginOtpMail::class)->last()->code;
        $this->assertFalse(Hash::check($oldCode, $newChallenge->code_hash));
        $this->assertTrue(Hash::check($newCode, $newChallenge->code_hash));
        $this->assertTrue($newChallenge->expires_at->isFuture());
    }

    public function test_resend_enforces_cooldown_delivery_limit_and_invalidates_previous_code(): void
    {
        [, $challenge, $firstCode] = $this->beginOtp();

        $this->post(route('otp.resend'))
            ->assertSessionHasErrors('resend');

        $this->travel(61)->seconds();
        $this->post(route('otp.resend'))->assertSessionHas('status');
        $secondCode = Mail::sent(AdminLoginOtpMail::class)->last()->code;

        $this->post(route('otp.verify'), ['otp' => $firstCode])
            ->assertSessionHasErrors(['otp' => 'Kode verifikasi tidak sesuai.']);

        $this->travel(61)->seconds();
        $this->post(route('otp.resend'))->assertSessionHas('status');
        $this->assertSame(3, $challenge->fresh()->send_count);

        $this->travel(61)->seconds();
        $this->post(route('otp.resend'))->assertSessionHasErrors('resend');
        $this->assertNotSame($firstCode, $secondCode);
    }

    public function test_delivery_limit_applies_across_new_login_challenges_for_the_same_account(): void
    {
        Mail::fake();
        $user = User::factory()->create(['role' => 'Admin']);

        foreach (range(1, 3) as $delivery) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ])->assertRedirect(route('otp.show', absolute: false));
        }

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        Mail::assertSent(AdminLoginOtpMail::class, 3);
        $this->assertDatabaseCount('admin_login_otps', 3);
        $this->assertGuest();
    }

    public function test_used_otp_cannot_create_another_session(): void
    {
        [$user, $challenge, $code] = $this->beginOtp();
        $this->post(route('otp.verify'), ['otp' => $code]);
        $this->post(route('logout'));

        $this->withSession([AdminOtpService::SESSION_KEY => $challenge->challenge_id])
            ->post(route('otp.verify'), ['otp' => $code])
            ->assertRedirect(route('login', absolute: false));

        $this->assertGuest();
        $this->assertDatabaseCount('admin_auth_events', 4);
        $this->assertSame($user->id, $challenge->user_id);
    }

    public function test_six_digit_code_with_a_leading_zero_is_accepted(): void
    {
        [$user, $challenge] = $this->beginOtp();
        $challenge->update(['code_hash' => Hash::make('012345')]);

        $this->post(route('otp.verify'), ['otp' => '012345'])
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_role_change_during_pending_challenge_prevents_login(): void
    {
        [$user, , $code] = $this->beginOtp();
        $user->update(['role' => 'User']);

        $this->post(route('otp.verify'), ['otp' => $code])
            ->assertRedirect(route('login', absolute: false));

        $this->assertGuest();
    }

    public function test_pending_challenge_does_not_grant_access_to_protected_routes(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login', absolute: false));

        $this->beginOtp();

        $this->get(route('admin.dashboard'))->assertRedirect(route('login', absolute: false));
        $this->assertGuest();
    }

    public function test_intended_admin_url_is_restored_only_after_otp_and_remember_is_deferred(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login', absolute: false));
        [$user, , $code] = $this->beginOtp(remember: true);

        $this->assertGuest();
        $this->assertNull($user->fresh()->remember_token);

        $response = $this->post(route('otp.verify'), ['otp' => $code]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_otp_page_requires_an_active_challenge_and_masks_email(): void
    {
        $this->get(route('otp.show'))->assertRedirect(route('login', absolute: false));

        [$user] = $this->beginOtp(email: 'krisna@example.go.id');

        $this->get(route('otp.show'))
            ->assertOk()
            ->assertSee('kr***@example.go.id')
            ->assertDontSee($user->email);
    }

    public function test_cancel_marks_challenge_inactive_and_returns_to_login(): void
    {
        [, $challenge] = $this->beginOtp();

        $this->post(route('otp.cancel'))
            ->assertRedirect(route('login', absolute: false));

        $this->assertNotNull($challenge->fresh()->cancelled_at);
        $this->assertFalse(session()->has(AdminOtpService::SESSION_KEY));
    }

    public function test_mail_failure_never_authenticates_and_is_audited_without_otp_data(): void
    {
        Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('Provider unavailable'));
        $user = User::factory()->create(['role' => 'Admin']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors(['email' => 'Kode verifikasi belum dapat dikirim. Silakan coba beberapa saat lagi.']);

        $this->assertGuest();
        $this->assertNotNull(AdminLoginOtp::firstOrFail()->cancelled_at);
        $event = AdminAuthEvent::where('event', 'otp_delivery_failed')->firstOrFail();
        $this->assertNull($event->metadata);
    }

    public function test_otp_email_contains_required_security_information(): void
    {
        $this->beginOtp();
        $mail = Mail::sent(AdminLoginOtpMail::class)->first();
        $html = $mail->render();

        $this->assertStringContainsString('Bapperida Kabupaten Pasuruan', $html);
        $this->assertStringContainsString('5 menit', $html);
        $this->assertStringContainsString('Jangan berikan kode ini kepada siapa pun', $html);
        $this->assertStringContainsString('Jika Anda tidak mencoba login', $html);
        $this->assertSame('Kode Verifikasi Login Portal RPJMD', $mail->envelope()->subject);
    }

    public function test_controlled_feature_flag_can_bypass_otp_for_incident_rollback(): void
    {
        Mail::fake();
        config(['admin-auth.otp.enabled' => false]);
        $user = User::factory()->create(['role' => 'Super Admin']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        Mail::assertNothingSent();
        $this->assertDatabaseHas('admin_auth_events', [
            'user_id' => $user->id,
            'event' => 'otp_feature_bypassed',
        ]);
    }

    public function test_prune_command_removes_only_challenges_past_retention(): void
    {
        [, $oldChallenge] = $this->beginOtp();
        $oldChallenge->update(['expires_at' => now()->subHours(25)]);

        [, $recentChallenge] = $this->beginOtp();
        $recentChallenge->update(['expires_at' => now()->subHour()]);

        $this->artisan('admin-otp:prune')->assertSuccessful();

        $this->assertModelMissing($oldChallenge);
        $this->assertModelExists($recentChallenge);
    }

    private function beginOtp(
        string $role = 'Admin',
        bool $remember = false,
        ?string $email = null,
    ): array {
        Mail::fake();
        $user = User::factory()->create([
            'email' => $email ?? fake()->unique()->safeEmail(),
            'role' => $role,
            'remember_token' => null,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => $remember ? 'on' : null,
        ]);

        $challenge = AdminLoginOtp::whereBelongsTo($user)->latest('id')->firstOrFail();
        $code = Mail::sent(AdminLoginOtpMail::class)->last()->code;

        return [$user, $challenge, $code, $response];
    }
}

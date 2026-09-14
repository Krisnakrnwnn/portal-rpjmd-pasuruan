<x-guest-layout>
    <x-slot:pageTitle>Verifikasi OTP | Portal RPJMD Kabupaten Pasuruan</x-slot:pageTitle>
    <x-slot:panelLabelId>otp-title</x-slot:panelLabelId>
    <x-slot:loginBrand>
        <img class="admin-login__hero" src="{{ asset('hero.png') }}" alt="" aria-hidden="true">
        <div class="admin-login__brand-content">
            <a href="/" class="admin-login__logo-link">
                <img class="admin-login__logo" src="{{ asset('Logo Bapperida Kab Pasuruan Putih.png') }}" alt="Bapperida Kabupaten Pasuruan">
            </a>
            <div class="admin-login__intro">
                <p class="admin-login__eyebrow">Kabupaten Pasuruan</p>
                <h2>Portal Manajemen<br> RPJMD</h2>
                <p class="admin-login__description">Kelola informasi perencanaan pembangunan daerah secara aman dan terpusat.</p>
            </div>
            <div class="admin-login__security">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3 4 6v6c0 4 8 9 8 9s8-5 8-9V6l-8-3Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8 12 3 3 5-6"/></svg>
                <p>Langkah terakhir verifikasi identitas Anda<br><strong>dengan kode yang dikirim ke email.</strong></p>
            </div>
        </div>
    </x-slot:loginBrand>

    <header class="admin-login__heading" aria-labelledby="otp-title">
        <p class="admin-login__eyebrow admin-otp__step-label">
            <span class="admin-otp__step-badge" aria-label="Langkah 2 dari 2">
                <svg class="admin-otp__step-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Langkah 2 dari 2
            </span>
        </p>
        <h1 id="otp-title">Verifikasi Email</h1>
        <p>
            Masukkan kode enam digit yang kami kirim ke
            <strong class="admin-otp__masked-email">{{ $maskedEmail }}</strong>
            untuk melanjutkan. Kode berlaku selama 5 menit.
        </p>
    </header>

    {{-- Live region untuk status / resend --}}
    <div aria-live="polite" aria-atomic="true" class="admin-otp__alerts">
        <x-auth-session-status class="admin-otp__alert admin-otp__alert--status" role="status" :status="session('status')" />
        <x-input-error :messages="$errors->get('resend')" class="admin-otp__alert admin-otp__alert--error" role="alert" />
    </div>

    <form id="otp-form" method="POST" action="{{ route('otp.verify') }}" class="admin-otp__form" novalidate>
        @csrf

        {{-- OTP Input --}}
        <div class="admin-otp__field">
            <label for="otp" class="admin-otp__label">
                <svg class="admin-otp__label-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Kode Verifikasi
            </label>
            <input
                id="otp"
                type="text"
                name="otp"
                value="{{ old('otp') }}"
                required
                autofocus
                inputmode="numeric"
                autocomplete="one-time-code"
                pattern="[0-9]{6}"
                maxlength="6"
                aria-describedby="otp-help otp-expiry"
                aria-invalid="{{ $errors->has('otp') ? 'true' : 'false' }}"
                @if($errors->has('otp')) aria-errormessage="otp-error" @endif
                class="admin-otp__input{{ $errors->has('otp') ? ' admin-otp__input--invalid' : '' }}"
                placeholder="000000"
            >
            <p id="otp-help" class="admin-otp__hint">Kode hanya dapat digunakan satu kali.</p>
            <x-input-error :messages="$errors->get('otp')" class="admin-otp__field-error" id="otp-error" role="alert" />
        </div>

        {{-- Countdown expiry --}}
        <div class="admin-otp__expiry-wrap" id="otp-expiry" aria-live="off">
            <svg class="admin-otp__expiry-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
            <span id="expiry-countdown" class="admin-otp__expiry-text">Kode berlaku selama --:--</span>
        </div>

        {{-- Submit Button --}}
        <button
            id="verify-button"
            type="submit"
            class="admin-login__submit admin-otp__submit"
        >
            <span data-label>Verifikasi dan Masuk</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </button>
    </form>

    {{-- Secondary actions --}}
    <div class="admin-otp__secondary">
        {{-- Resend OTP --}}
        <form id="resend-form" method="POST" action="{{ route('otp.resend') }}">
            @csrf
            <button
                id="resend-button"
                type="submit"
                class="admin-otp__resend-btn"
            >
                <svg class="admin-otp__resend-icon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span data-label>Kirim Ulang Kode</span>
                <span id="resend-countdown" class="admin-otp__resend-countdown"></span>
            </button>
        </form>

        <span class="admin-otp__divider" aria-hidden="true">·</span>

        {{-- Cancel / Back to Login --}}
        <form method="POST" action="{{ route('otp.cancel') }}">
            @csrf
            <button type="submit" class="admin-otp__cancel-btn">
                <svg class="admin-otp__cancel-icon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Login
            </button>
        </form>
    </div>

    <script>
        (() => {
            const otpInput = document.getElementById('otp');
            const otpForm = document.getElementById('otp-form');
            const verifyButton = document.getElementById('verify-button');
            const resendForm = document.getElementById('resend-form');
            const resendButton = document.getElementById('resend-button');
            const expiryOutput = document.getElementById('expiry-countdown');
            const resendOutput = document.getElementById('resend-countdown');
            let expiresIn = @json($expiresIn);
            let resendIn = @json($resendIn);

            const formatTime = (seconds) => {
                const minutes = Math.floor(seconds / 60).toString().padStart(2, '0');
                const remainder = (seconds % 60).toString().padStart(2, '0');
                return `${minutes}:${remainder}`;
            };

            const render = () => {
                expiryOutput.textContent = expiresIn > 0
                    ? `Kode berlaku selama ${formatTime(expiresIn)}`
                    : 'Kode telah kedaluwarsa. Silakan kirim kode baru.';

                if (expiresIn <= 0) {
                    expiryOutput.closest('.admin-otp__expiry-wrap').classList.add('admin-otp__expiry-wrap--expired');
                } else {
                    expiryOutput.closest('.admin-otp__expiry-wrap').classList.remove('admin-otp__expiry-wrap--expired');
                }

                resendButton.disabled = resendIn > 0;
                resendOutput.textContent = resendIn > 0 ? `(${resendIn} detik)` : '';
            };

            otpInput.addEventListener('input', () => {
                otpInput.value = otpInput.value.replace(/\D/g, '').slice(0, 6);
            });

            otpForm.addEventListener('submit', () => {
                verifyButton.disabled = true;
                verifyButton.querySelector('[data-label]').textContent = 'Memverifikasi...';
            });

            resendForm.addEventListener('submit', () => {
                resendButton.disabled = true;
                resendButton.querySelector('[data-label]').textContent = 'Mengirim...';
            });

            render();
            window.setInterval(() => {
                expiresIn = Math.max(0, expiresIn - 1);
                resendIn = Math.max(0, resendIn - 1);
                render();
            }, 1000);
        })();
    </script>
</x-guest-layout>

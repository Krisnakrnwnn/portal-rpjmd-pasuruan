<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-3xl font-black text-blue-900 tracking-tight mb-2">Verifikasi Email</h2>
        <p class="text-slate-500 text-sm font-medium leading-relaxed">
            Masukkan kode enam digit yang dikirim ke
            <strong class="text-slate-700">{{ $maskedEmail }}</strong>.
        </p>
    </div>

    <div aria-live="polite" aria-atomic="true">
        <x-auth-session-status class="mb-5" :status="session('status')" />
        <x-input-error :messages="$errors->get('resend')" class="mb-5" />
    </div>

    <form id="otp-form" method="POST" action="{{ route('otp.verify') }}" class="space-y-6">
        @csrf

        <div class="space-y-2">
            <label for="otp" class="text-xs font-black text-blue-900 uppercase tracking-widest pl-1">
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
                class="block w-full px-4 py-4 bg-white/60 border border-white/30 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white outline-none transition-all text-center text-3xl tracking-[0.45em] font-black text-blue-900"
            >
            <p id="otp-help" class="text-xs text-slate-500">Kode hanya dapat digunakan satu kali.</p>
            <x-input-error :messages="$errors->get('otp')" class="mt-1" />
        </div>

        <p id="otp-expiry" class="text-sm font-semibold text-slate-600 text-center" aria-live="polite">
            <span id="expiry-countdown">Kode berlaku selama --:--</span>
        </p>

        <button id="verify-button" type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 disabled:opacity-60 disabled:cursor-not-allowed text-white font-black text-lg rounded-2xl shadow-xl shadow-blue-900/20 transition-all flex items-center justify-center gap-3">
            <span data-label>Verifikasi dan Masuk</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-white/30 space-y-4 text-center">
        <form id="resend-form" method="POST" action="{{ route('otp.resend') }}">
            @csrf
            <button id="resend-button" type="submit" class="text-sm font-bold text-blue-700 hover:text-blue-900 disabled:text-slate-400 disabled:cursor-not-allowed transition-colors">
                <span data-label>Kirim Ulang Kode</span>
                <span id="resend-countdown"></span>
            </button>
        </form>

        <form method="POST" action="{{ route('otp.cancel') }}">
            @csrf
            <button type="submit" class="text-xs font-bold text-slate-500 hover:text-slate-800 uppercase tracking-wider transition-colors">
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

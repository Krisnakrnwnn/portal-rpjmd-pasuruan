<x-guest-layout>
    <x-slot:pageTitle>Login | Portal Informasi Perencanaan Daerah, Riset Dan Inovasi | Kabupaten Pasuruan</x-slot:pageTitle>
    <x-slot:loginBrand>
        <img class="admin-login__hero" src="{{ asset('hero.png') }}" alt="" aria-hidden="true">
        <div class="admin-login__brand-content">
            <a href="/" class="admin-login__logo-link admin-login__logo-link--pair">
                <img class="admin-login__pemda-logo" src="{{ asset('logo_pasuruan.png') }}" alt="Logo Pemda Kabupaten Pasuruan">
                <img class="admin-login__logo" src="{{ asset('Logo Bapperida Kab Pasuruan Putih.png') }}" alt="Bapperida Kabupaten Pasuruan">
            </a>
            <div class="admin-login__intro">
                <p class="admin-login__eyebrow">Kabupaten Pasuruan</p>
                <h2>Portal Informasi Perencanaan Daerah, Riset Dan Inovasi</h2>
                <p class="admin-login__description">Kelola informasi perencanaan pembangunan daerah secara aman dan terpusat.</p>
            </div>
            <div class="admin-login__security">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3 4 6v6c0 4 8 9 8 9s8-5 8-9V6l-8-3Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8 12 3 3 5-6"/></svg>
                <p>Login administrator dilindungi<br><strong>verifikasi email dua tahap.</strong></p>
            </div>
        </div>
    </x-slot:loginBrand>
    <header class="admin-login__heading">
        <p class="admin-login__eyebrow">Akses Portal</p>
        <h1 id="login-title">Masuk ke Portal</h1>
        <p>Masukkan email dan kata sandi Anda. Akun administrator akan melanjutkan ke verifikasi email.</p>
    </header>

    <!-- Session Status -->
    <x-auth-session-status class="admin-login__status" role="status" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="admin-login__fields">
        @csrf

        <!-- Email Address -->
        <div class="admin-login__field">
            <label for="email">Alamat Email</label>
            <div class="admin-login__input-wrap">
                <div class="admin-login__input-icon">
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                </div>
                <input id="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" @if($errors->has('email')) aria-describedby="email-error" @endif type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="admin-login__input"
                    placeholder="nama@email.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="admin-login__error" id="email-error" role="alert" />
        </div>

        <!-- Password -->
        <div class="admin-login__field">
            <div class="admin-login__label-row">
                <label for="password">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Lupa Sandi?</a>
                @endif
            </div>
            <div class="admin-login__input-wrap">
                <div class="admin-login__input-icon">
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" @if($errors->has('password')) aria-describedby="password-error" @endif type="password" name="password" required autocomplete="current-password"
                    class="admin-login__input"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="admin-login__error" id="password-error" role="alert" />
        </div>

        <!-- Remember Me -->
        <div class="admin-login__remember">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">Ingat Sesi Saya</label>
        </div>

        <div>
            <button type="submit" class="admin-login__submit">
                Lanjutkan
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </button>
        </div>
    </form>
    <p class="admin-login__account">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
</x-guest-layout>

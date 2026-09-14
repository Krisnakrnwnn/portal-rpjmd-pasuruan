<x-guest-layout>
    <x-slot:pageTitle>Lupa Sandi | Portal RPJMD Kabupaten Pasuruan</x-slot:pageTitle>
    <x-slot:panelLabelId>forgot-title</x-slot:panelLabelId>
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
                <p>Login administrator dilindungi<br><strong>verifikasi email dua tahap.</strong></p>
            </div>
        </div>
    </x-slot:loginBrand>

    <header class="admin-login__heading">
        <p class="admin-login__eyebrow">Pemulihan Akses</p>
        <h1 id="forgot-title">Lupa Kata Sandi?</h1>
        <p>Masukkan alamat email akun administrator Anda. Kami akan mengirimkan tautan untuk membuat kata sandi baru.</p>
    </header>

    {{-- Session Status --}}
    <x-auth-session-status class="admin-login__status" role="status" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="admin-login__fields">
        @csrf

        {{-- Email Address --}}
        <div class="admin-login__field">
            <label for="email">Alamat Email</label>
            <div class="admin-login__input-wrap">
                <div class="admin-login__input-icon">
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                </div>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                    @if($errors->has('email')) aria-describedby="email-error" @endif
                    class="admin-login__input"
                    placeholder="nama@email.com"
                >
            </div>
            <x-input-error :messages="$errors->get('email')" class="admin-login__error" id="email-error" role="alert" />
        </div>

        <div>
            <button type="submit" class="admin-login__submit">
                Kirim Tautan Reset
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </button>
        </div>
    </form>

    <a href="{{ route('login') }}" class="admin-login__back">
        <span aria-hidden="true">←</span> Kembali ke Login
    </a>
</x-guest-layout>

<x-guest-layout>
    <x-slot:pageTitle>Daftar Akun | Portal Informasi Perencanaan Daerah, Riset Dan Inovasi | Kabupaten Pasuruan</x-slot:pageTitle>
    <x-slot:panelLabelId>register-title</x-slot:panelLabelId>
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
                <p>Akses pengelolaan portal diberikan sesuai<br><strong>hak akses akun yang ditetapkan.</strong></p>
            </div>
        </div>
    </x-slot:loginBrand>
    <header class="admin-login__heading">
        <p class="admin-login__eyebrow">Registrasi Akun</p>
        <h1 id="register-title">Buat Akun Portal</h1>
        <p>Lengkapi data berikut untuk membuat akun Anda.</p>
    </header>
    <form method="POST" action="{{ route('register') }}" class="admin-login__fields">
        @csrf
        @foreach ([
            'name' => ['label' => 'Nama Lengkap', 'type' => 'text', 'autocomplete' => 'name', 'placeholder' => 'Nama lengkap Anda'],
            'email' => ['label' => 'Alamat Email', 'type' => 'email', 'autocomplete' => 'username', 'placeholder' => 'nama@email.com'],
            'password' => ['label' => 'Kata Sandi', 'type' => 'password', 'autocomplete' => 'new-password', 'placeholder' => 'Buat kata sandi'],
            'password_confirmation' => ['label' => 'Konfirmasi Kata Sandi', 'type' => 'password', 'autocomplete' => 'new-password', 'placeholder' => 'Ulangi kata sandi'],
        ] as $fieldName => $field)
            <div class="admin-login__field">
                <label for="{{ $fieldName }}">{{ $field['label'] }}</label>
                <input id="{{ $fieldName }}" class="admin-login__input admin-login__input--plain"
                    type="{{ $field['type'] }}" name="{{ $fieldName }}"
                    @if ($field['type'] !== 'password') value="{{ old($fieldName) }}" maxlength="255" @endif
                    autocomplete="{{ $field['autocomplete'] }}" placeholder="{{ $field['placeholder'] }}"
                    aria-invalid="{{ $errors->has($fieldName) ? 'true' : 'false' }}"
                    @if ($errors->has($fieldName)) aria-describedby="{{ $fieldName }}-error" @endif
                    @if ($fieldName === 'name') autofocus @endif required>
                <x-input-error :messages="$errors->get($fieldName)" class="admin-login__error" :id="$fieldName.'-error'" role="alert" />
            </div>
        @endforeach
        <button type="submit" class="admin-login__submit">Daftar Akun</button>
    </form>
    <p class="admin-login__account">Sudah punya akun? <a href="{{ route('login') }}">Masuk sekarang</a></p>
</x-guest-layout>

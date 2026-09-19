<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PRivIA | Portal Bapperida Kabupaten Pasuruan</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="privia-page" data-active-conversation="{{ $activeConversationId ?? '' }}" data-user-name="{{ trim((string) auth()->user()->name) }}">
    <div id="privia-app" class="privia-app">
        <aside id="privia-sidebar" class="privia-sidebar" aria-label="Riwayat percakapan">
            <div class="privia-sidebar__brand">
                <a href="{{ route('home') }}" class="privia-brand-link">
                    <span class="privia-brand-mark"><img src="{{ asset('logo.png') }}" alt=""></span>
                    <span><strong>PRivIA</strong><small>Asisten Bapperida</small></span>
                </a>
                <button type="button" id="privia-sidebar-close" class="privia-icon-button privia-mobile-only" aria-label="Tutup menu riwayat">×</button>
            </div>

            <button type="button" id="privia-new-chat" class="privia-new-chat">
                <span aria-hidden="true">+</span> Chat Baru
            </button>

            <div class="privia-sidebar__section-title">Riwayat Percakapan</div>
            <div id="privia-conversation-list" class="privia-conversation-list" aria-live="polite"></div>

            <div class="privia-sidebar__footer">
                <a href="{{ route('home') }}" class="privia-back-link">← Kembali ke Portal</a>
                <span>{{ auth()->user()->name }}</span>
            </div>
        </aside>

        <div id="privia-backdrop" class="privia-backdrop" hidden></div>

        <main class="privia-main">
            <header class="privia-header">
                <button type="button" id="privia-sidebar-open" class="privia-icon-button privia-mobile-only" aria-label="Buka riwayat percakapan">☰</button>
                <div>
                    <p class="privia-eyebrow">Perencanaan, Riset dan Inovasi</p>
                    <h1 id="privia-title">PRivIA</h1>
                </div>
                <span id="privia-status" class="privia-status">Online</span>
            </header>

            <section id="privia-messages" class="privia-messages" aria-live="polite" aria-label="Percakapan PRivIA"></section>

            <form id="privia-composer" class="privia-composer">
                <textarea id="privia-input" rows="1" maxlength="10000" placeholder="Tanyakan sesuatu tentang RPJMD atau Bapperida..." aria-label="Pesan untuk PRivIA"></textarea>
                <button id="privia-send" type="submit" class="privia-send" aria-label="Kirim pesan">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14"></path>
                        <path d="m13 6 6 6-6 6"></path>
                    </svg>
                </button>
                <p class="privia-composer__hint">Enter untuk mengirim · Shift + Enter untuk baris baru</p>
            </form>
        </main>
    </div>

    <div id="privia-dialog" class="privia-dialog" hidden>
        <div class="privia-dialog__overlay" data-dialog-close></div>
        <section class="privia-dialog__panel" role="dialog" aria-modal="true" aria-labelledby="privia-dialog-title" aria-describedby="privia-dialog-description">
            <button type="button" id="privia-dialog-close" class="privia-dialog__close" aria-label="Tutup dialog">×</button>
            <p class="privia-dialog__eyebrow">Riwayat Percakapan</p>
            <h2 id="privia-dialog-title">Ganti nama percakapan</h2>
            <p id="privia-dialog-description" class="privia-dialog__description">Ubah nama percakapan agar lebih mudah ditemukan di riwayat.</p>
            <p id="privia-dialog-context" class="privia-dialog__context" hidden></p>
            <label id="privia-dialog-input-label" class="privia-dialog__label" for="privia-dialog-input">Nama percakapan</label>
            <input id="privia-dialog-input" class="privia-dialog__input" type="text" maxlength="120" autocomplete="off">
            <p id="privia-dialog-error" class="privia-dialog__error" role="alert" hidden></p>
            <div class="privia-dialog__actions">
                <button type="button" id="privia-dialog-cancel" class="privia-dialog__button privia-dialog__button--secondary">Batal</button>
                <button type="button" id="privia-dialog-confirm" class="privia-dialog__button privia-dialog__button--primary">Simpan Perubahan</button>
            </div>
        </section>
    </div>
</body>
</html>

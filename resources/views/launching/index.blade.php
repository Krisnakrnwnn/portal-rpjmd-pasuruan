<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#102e56">
        <title>Launching Portal Bapperida Kabupaten Pasuruan</title>
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="launching-page" style="--launching-background: url('{{ asset('launching-bg.png') }}');">
        <main class="launching-shell" aria-live="polite">
            <section class="launching-screen launching-screen--intro" data-screen="intro" aria-labelledby="launching-title">
                <header class="launching-identity" aria-label="Pemerintah Kabupaten Pasuruan dan Bapperida Kabupaten Pasuruan">
                    <div class="launching-pasuruan-logos">
                        <img src="{{ asset('logo_pasuruan.png') }}" width="160" height="160" alt="Lambang Pemerintah Kabupaten Pasuruan">
                        <img class="launching-bapperida-logo" src="{{ asset('Logo Bapperida Kab Pasuruan Putih.png') }}" width="260" height="160" alt="Logo Bapperida Kabupaten Pasuruan">
                    </div>
                    <strong class="launching-pasuruan-caption">Pemerintah Kabupaten Pasuruan</strong>
                    <span class="launching-institution-name">Bapperida Kabupaten Pasuruan</span>
                </header>

                <div class="launching-intro-content">
                    <p class="launching-eyebrow">Portal Informasi Pemerintah Daerah, Riset dan Inovasi</p>
                    <h1 id="launching-title" aria-label="Launching">Launching<span id="launching-dots" aria-hidden="true">...</span></h1>
                    <p class="launching-title">Portal Informasi Perencanaan Daerah,<br>Riset dan Inovasi</p>
                    <p class="launching-supporting-copy">Mendukung perencanaan pembangunan daerah yang transparan, berbasis data, riset, dan inovasi.</p>

                    <button type="button" class="launching-button launching-button--primary" data-start>
                        Mulai Launching
                        <span aria-hidden="true">→</span>
                    </button>
                </div>

                <p class="launching-footer">Portal Informasi Perencanaan Daerah, Riset dan Inovasi</p>
            </section>

            <section class="launching-screen launching-screen--robot-reveal" data-screen="robot" aria-label="Kemunculan PRivIA" hidden>
                <div class="launching-loading" aria-live="polite">
                    <div class="launching-robot-reveal" aria-hidden="true">
                        <span class="launching-robot-reveal-halo"></span>
                        <img src="{{ asset('prisia-robot.png') }}" width="640" height="640" alt="">
                    </div>
                    <div class="launching-loading-copy">
                        <p>Launching Portal<span class="launching-loading-dots" aria-hidden="true">...</span></p>
                        <div class="launching-progress" role="progressbar" aria-label="Progress launching portal" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                            <span class="launching-progress-bar"></span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="launching-screen launching-screen--privia" data-screen="privia" aria-labelledby="privia-title" hidden>
                <div class="launching-privia-layout">
                    <div class="launching-privia-stage">
                        <a class="launching-robot-link" href="{{ route('home') }}" aria-label="Klik PRivIA untuk masuk ke portal">
                            <span class="launching-robot-halo" aria-hidden="true"></span>
                            <img src="{{ asset('prisia-robot.png') }}" width="640" height="640" alt="PRivIA, bot asisten Portal Bapperida">
                        </a>
                        <div class="launching-chat-bubble" data-privia-bubble aria-labelledby="privia-title">
                            <strong id="privia-title">Halo, saya PRivIA</strong>
                            <span>Bot asisten Anda.</span>
                            <span>Jika membutuhkan bantuan, klik saya.</span>
                        </div>
                    </div>
                    <a class="launching-button launching-button--primary" href="{{ route('home') }}">Masuk ke Portal <span aria-hidden="true">→</span></a>
                </div>
                <p class="launching-footer">Portal Informasi Perencanaan Daerah, Riset dan Inovasi</p>
            </section>
        </main>

        <script>
            (() => {
                const intro = document.querySelector('[data-screen="intro"]');
                const robotReveal = document.querySelector('[data-screen="robot"]');
                const privia = document.querySelector('[data-screen="privia"]');
                const startButton = document.querySelector('[data-start]');
                const progress = document.querySelector('.launching-progress');
                const progressBar = document.querySelector('.launching-progress-bar');
                const priviaBubble = document.querySelector('[data-privia-bubble]');
                const launchingDots = document.querySelector('#launching-dots');
                const LAUNCH_DURATION = 10000;
                let launchStarted = false;
                let progressFrame = null;

                if (launchingDots && !launchingDots.dataset.animationInitialized) {
                    launchingDots.dataset.animationInitialized = 'true';

                    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                        launchingDots.textContent = '...';
                    } else {
                        const dots = ['.', '..', '...'];
                        let dotIndex = 0;

                        launchingDots.textContent = dots[dotIndex];
                        window.setInterval(() => {
                            dotIndex = (dotIndex + 1) % dots.length;
                            launchingDots.textContent = dots[dotIndex];
                        }, 1000);
                    }
                }

                startButton.addEventListener('click', () => {
                    if (launchStarted) return;

                    launchStarted = true;
                    startButton.disabled = true;
                    intro.classList.add('is-leaving');
                    window.setTimeout(() => {
                        intro.hidden = true;
                        robotReveal.hidden = false;
                        window.requestAnimationFrame(() => {
                            robotReveal.classList.add('is-visible');

                            const startedAt = window.performance.now();
                            const updateProgress = () => {
                                const percentage = Math.min(100, ((window.performance.now() - startedAt) / LAUNCH_DURATION) * 100);
                                progressBar.style.width = `${percentage}%`;
                                progress.setAttribute('aria-valuenow', String(Math.round(percentage)));

                                if (percentage < 100) {
                                    progressFrame = window.requestAnimationFrame(updateProgress);
                                }
                            };

                            updateProgress();
                        });

                        window.setTimeout(() => {
                            if (progressFrame) window.cancelAnimationFrame(progressFrame);
                            progressBar.style.width = '100%';
                            progress.setAttribute('aria-valuenow', '100');
                            robotReveal.classList.remove('is-visible');

                            window.setTimeout(() => {
                                robotReveal.hidden = true;
                                privia.hidden = false;
                                window.requestAnimationFrame(() => {
                                    privia.classList.add('is-visible');
                                    window.setTimeout(() => priviaBubble.classList.add('is-visible'), 400);
                                });
                            }, 450);
                        }, LAUNCH_DURATION);
                    }, 500);
                });
            })();
        </script>
    </body>
</html>

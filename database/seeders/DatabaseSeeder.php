<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed News
        \App\Models\News::create([
            'title' => 'Musrenbang Kabupaten Pasuruan 2026 Hasilkan 68 Usulan Program Prioritas',
            'slug' => 'musrenbang-kota-pasuruan-2026',
            'category' => 'Musrenbang 2026',
            'content' => 'Pemerintah Kabupaten Pasuruan sukses menyelenggarakan Musyawarah Rencana Pembangunan tingkat kota dengan menampung 68 usulan program prioritas dari seluruh OPD dan masyarakat...',
            'image_url' => 'news1.png',
            'published_at' => now(),
        ]);

        \App\Models\News::create([
            'title' => 'Dokumen RPJMD Kabupaten Pasuruan 2025-2029 Resmi Ditetapkan Perda',
            'slug' => 'dokumen-rpjmd-ditetapkan',
            'category' => 'Dokumen Resmi',
            'content' => 'DPRD Kabupaten Pasuruan mengesahkan Peraturan Daerah tentang RPJMD Kabupaten Pasuruan 2025-2029 dalam sidang paripurna, menjadi acuan pembangunan lima tahun ke depan.',
            'image_url' => 'news2.png',
            'published_at' => now(),
        ]);

        \App\Models\News::create([
            'title' => 'Progres Infrastruktur Kabupaten Pasuruan Capai 78% Sesuai Target RPJMD',
            'slug' => 'progres-infrastruktur-capai-target',
            'category' => 'Realisasi Program',
            'content' => 'Wali Kabupaten Pasuruan mengumumkan capaian realisasi program infrastruktur telah mencapai 78% dari target RPJMD, dipimpin oleh proyek jalan dan drainase perkotaan.',
            'image_url' => 'news3.png',
            'published_at' => now(),
        ]);

        // Seed Services
        \App\Models\Service::create([
            'name' => 'Dokumen RPJMD',
            'description' => 'Unduh dan akses dokumen resmi Rencana Pembangunan Jangka Menengah Daerah Kabupaten Pasuruan 2025–2029 beserta lampiran program prioritas dan indikator kinerja.',
            'icon' => 'document-report',
            'url' => 'https://drive.google.com/file/d/1jzDOgd_ihEcBCMvGR25VkiIMbSMsV6Aq/view?usp=drive_link',
        ]);

        \App\Models\Service::create([
            'name' => 'Monitoring Capaian',
            'description' => 'Pantau realisasi dan capaian program prioritas RPJMD Kabupaten Pasuruan secara berkala, lengkap dengan indikator kinerja utama (IKU) setiap OPD terkait.',
            'icon' => 'chart-bar',
            'url' => route('capaian'),
        ]);

        \App\Models\Service::create([
            'name' => 'Aspirasi & Pengaduan',
            'description' => 'Sampaikan aspirasi, masukan, dan laporan terkait pelaksanaan program RPJMD Kabupaten Pasuruan langsung kepada tim pengelola melalui portal digital resmi.',
            'icon' => 'office-building',
            'url' => route('kontak'),
        ]);

        // Seed Sectors & Indicators
        $infrastruktur = \App\Models\Sector::create([
            'name' => 'Infrastruktur',
            'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>',
            'theme_color' => 'blue',
        ]);
        \App\Models\Indicator::create(['sector_id' => $infrastruktur->id, 'name' => 'Akses Air Minum Layak', 'progress' => 90]);
        \App\Models\Indicator::create(['sector_id' => $infrastruktur->id, 'name' => 'Sanitasi & Air Bersih', 'progress' => 74]);

        $pendidikan = \App\Models\Sector::create([
            'name' => 'Pendidikan',
            'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>',
            'theme_color' => 'yellow',
        ]);
        \App\Models\Indicator::create(['sector_id' => $pendidikan->id, 'name' => 'Program Beasiswa', 'progress' => 98]);
        \App\Models\Indicator::create(['sector_id' => $pendidikan->id, 'name' => 'Kualitas Ruang Kelas', 'progress' => 84]);
        // Panggil Seed Lanjutan (Fake Data & Admin)
        $this->call([
            SuperAdminSeeder::class,
            DummyDataSeeder::class,
        ]);
    }
}

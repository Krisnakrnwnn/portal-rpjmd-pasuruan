<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Sector;
use App\Models\Indicator;
use App\Models\News;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Membersihkan tabel (Agnostik untuk Postgres/Supabase)
        Indicator::query()->delete();
        Sector::query()->delete();
        // News::query()->delete(); // Biarkan berita asli tetap ada

        // 2. Data Sektor & Indikator Fiktif Interaktif
        $sectorsData = [
            'Infrastruktur & Tata Ruang' => [
                ['name' => 'Kondisi Jalan Baik', 'target' => 90, 'progress' => 85, 'unit' => '%', 'year' => 2026],
                ['name' => 'Cakupan Air Bersih', 'target' => 95, 'progress' => 92, 'unit' => '%', 'year' => 2026],
                ['name' => 'Kawasan Bebas Banjir', 'target' => 100, 'progress' => 78, 'unit' => '%', 'year' => 2026],
            ],
            'Kesehatan Masyarakat' => [
                ['name' => 'Angka Harapan Hidup', 'target' => 75, 'progress' => 73, 'unit' => 'Tahun', 'year' => 2026],
                ['name' => 'Prevalensi Stunting', 'target' => 10, 'progress' => 14, 'unit' => '%', 'year' => 2026],
                ['name' => 'Cakupan Imunisasi Dasar', 'target' => 100, 'progress' => 98, 'unit' => '%', 'year' => 2026],
            ],
            'Pendidikan & SDM' => [
                ['name' => 'Rata-rata Lama Sekolah', 'target' => 12, 'progress' => 10, 'unit' => 'Tahun', 'year' => 2026],
                ['name' => 'Angka Partisipasi Kasar PT', 'target' => 45, 'progress' => 38, 'unit' => '%', 'year' => 2026],
            ],
            'Ekonomi & Kesejahteraan' => [
                ['name' => 'Pertumbuhan Ekonomi', 'target' => 5, 'progress' => 8, 'unit' => '%', 'year' => 2026],
                ['name' => 'Tingkat Kemiskinan', 'target' => 6, 'progress' => 6, 'unit' => '%', 'year' => 2026],
                ['name' => 'Pengangguran Terbuka', 'target' => 4, 'progress' => 5, 'unit' => '%', 'year' => 2026],
            ],
            'Lingkungan Hidup' => [
                ['name' => 'Indeks Kualitas Udara', 'target' => 80, 'progress' => 76, 'unit' => 'Poin', 'year' => 2026],
                ['name' => 'Pengelolaan Sampah Terpadu', 'target' => 100, 'progress' => 65, 'unit' => '%', 'year' => 2026],
            ]
        ];

        foreach ($sectorsData as $sectorName => $indicators) {
            $sector = Sector::create([
                'name' => $sectorName
            ]);

            foreach ($indicators as $ind) {
                Indicator::create([
                    'sector_id' => $sector->id,
                    'name' => $ind['name'],
                    'progress' => $ind['progress']
                ]);
            }
        }

        // 3. Tambahan Berita Fiktif untuk memeriahkan Landing Page
        $firstUserId = \App\Models\User::first()->id ?? 1;
        for ($i = 4; $i <= 8; $i++) {
            News::create([
                'title' => 'Pencapaian Baru Kota Pasuruan Q' . rand(1,4) . ' ' . rand(2025, 2027) . ': Transformasi ' . Str::random(5),
                'slug' => Str::slug('berita-dummy-pencapaian-' . Str::random(8)),
                'category' => 'Press Release',
                'content' => 'Ini adalah konten berita simulasi yang dihasilkan secara otomatis oleh sistem Seeder. Pemerintah terus menggenjot percepatan pembangunan sesuai peta jalan RPJMD yang telah disusun. Partisipasi masyarakat dinilai sangat memuaskan.',
                'image_url' => null,
                'is_published' => true,
                'published_at' => now()->subDays(rand(1, 30)),
                'user_id' => $firstUserId,
            ]);
        }
    }
}

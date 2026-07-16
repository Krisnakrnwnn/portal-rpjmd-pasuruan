<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'RPJMD', 'slug' => 'rpjmd', 'description' => 'Rencana Pembangunan Jangka Menengah Daerah'],
            ['name' => 'RKPD', 'slug' => 'rkpd', 'description' => 'Rencana Kerja Pemerintah Daerah'],
            ['name' => 'Hasil Riset', 'slug' => 'hasil-riset', 'description' => 'Dokumen riset dan kajian publik'],
            ['name' => 'Inovasi', 'slug' => 'inovasi', 'description' => 'Kumpulan inovasi daerah Kabupaten Pasuruan'],
            ['name' => 'RINOVA', 'slug' => 'rinova', 'description' => 'Dokumen RINOVA'],
            ['name' => 'RENDALEV', 'slug' => 'rendalev', 'description' => 'Dokumen RENDALEV'],
            ['name' => 'PPM', 'slug' => 'ppm', 'description' => 'Dokumen PPM'],
            ['name' => 'PERSIK', 'slug' => 'persik', 'description' => 'Dokumen PERSIK'],
            ['name' => 'BPS', 'slug' => 'bps', 'description' => 'Dokumen BPS'],
        ];

        foreach ($categories as $cat) {
            \App\Models\DocumentCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}

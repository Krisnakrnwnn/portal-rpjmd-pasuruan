<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            [
                'key'     => 'sejarah',
                'title'   => 'Sejarah Singkat',
                'content' => 'Kabupaten Pasuruan merupakan salah satu kabupaten strategis di Provinsi Jawa Timur yang memiliki jejak sejarah panjang. Hari Jadi Kabupaten Pasuruan secara resmi ditetapkan pada tanggal 18 September 929 Masehi, merujuk pada Prasasti Cunggrang yang dikeluarkan oleh Raja Mpu Sindok dari Kerajaan Medang.

Sejak masa kejayaan kerajaan Nusantara, era kolonial, hingga masa kemerdekaan, Pasuruan selalu memegang peranan penting sebagai jalur perdagangan utama, kawasan agraris yang subur, serta basis perkembangan peradaban Islam di wilayah timur Pulau Jawa. Saat ini, Kabupaten Pasuruan terus bertransformasi menjadi daerah maju yang memadukan potensi agrobisnis, pariwisata bertaraf internasional (kawasan Gunung Bromo), serta kawasan industri mandiri yang terintegrasi.',
            ],
            [
                'key'     => 'visi',
                'title'   => 'Visi 2025–2029',
                'content' => 'Terwujudnya Kabupaten Pasuruan yang Maju, Sejahtera, dan Berkeadilan',
            ],
            [
                'key'     => 'misi',
                'title'   => 'Misi 2025–2029',
                'content' => 'Mendukung dan Mendorong Kualitas Keimanan dan Kesalehan Masyarakat|Memperkecil Ketimpangan dan Kesenjangan Sosial Ekonomi melalui Program Tepat Guna dan Tepat Sasaran|Membangun Sumberdaya Manusia yang Unggul dan Produk Asli Pasuruan yang Kompetitif|Memperkuat Sinergitas antara Pemerintah, Pemerintah Daerah, Pemerintah Desa, Dunia Usaha dan Masyarakat sebagai Pilar Membangun Daerah|Meningkatkan Kualitas Pelayanan Publik berbasis Teknologi Informasi serta Aparatur Pemerintah yang Lebih Profesional dan Humanis',
            ],
        ];

        foreach ($profiles as $profile) {
            DB::table('profiles')->updateOrInsert(
                ['key' => $profile['key']],
                array_merge($profile, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}

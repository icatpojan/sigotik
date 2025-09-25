<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PortNews;

class PortNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $news = [
            [
                'id' => 1,
                'img' => 'default-news.jpg',
                'news_title' => 'Selamat Datang di Sistem Sigotik',
                'news' => 'Sistem Manajemen BBM Kapal (Sigotik) adalah aplikasi untuk mengelola anggaran dan transaksi BBM kapal.',
                'kategori_id' => '1',
                'author' => 'Administrator',
                'date_create' => now(),
                'post' => '1'
            ],
            [
                'id' => 2,
                'img' => 'default-news.jpg',
                'news_title' => 'Panduan Penggunaan Sistem',
                'news' => 'Panduan lengkap untuk menggunakan sistem Sigotik dapat dilihat di menu bantuan.',
                'kategori_id' => '4',
                'author' => 'Administrator',
                'date_create' => now(),
                'post' => '1'
            ],
        ];

        foreach ($news as $item) {
            PortNews::updateOrCreate(
                ['id' => $item['id']],
                $item
            );
        }
    }
}

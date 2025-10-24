<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipeDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipeDokumen = [
            [
                'id' => 1,
                'kode_dokumen' => 'FOTO_SEGEL',
                'nama_dokumen' => 'Foto Segel',
                'deskripsi' => 'Foto segel tangki BBM',
                'allowed_extensions' => json_encode(['jpg', 'jpeg', 'png', 'gif']),
                'max_size_kb' => 5120, // 5MB
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'kode_dokumen' => 'FOTO_FLOWMETER',
                'nama_dokumen' => 'Foto Flowmeter',
                'deskripsi' => 'Foto flowmeter saat pengisian',
                'allowed_extensions' => json_encode(['jpg', 'jpeg', 'png', 'gif']),
                'max_size_kb' => 5120, // 5MB
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'kode_dokumen' => 'FOTO_KAPAL',
                'nama_dokumen' => 'Foto Kapal',
                'deskripsi' => 'Foto kapal saat operasional',
                'allowed_extensions' => json_encode(['jpg', 'jpeg', 'png', 'gif']),
                'max_size_kb' => 5120, // 5MB
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'kode_dokumen' => 'FOTO_TANGKI',
                'nama_dokumen' => 'Foto Tangki',
                'deskripsi' => 'Foto kondisi tangki BBM',
                'allowed_extensions' => json_encode(['jpg', 'jpeg', 'png', 'gif']),
                'max_size_kb' => 5120, // 5MB
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'kode_dokumen' => 'FOTO_DOKUMEN',
                'nama_dokumen' => 'Foto Dokumen',
                'deskripsi' => 'Foto dokumen pendukung',
                'allowed_extensions' => json_encode(['jpg', 'jpeg', 'png', 'gif', 'pdf']),
                'max_size_kb' => 10240, // 10MB
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'kode_dokumen' => 'FOTO_SARANA',
                'nama_dokumen' => 'Foto Sarana Pengisian',
                'deskripsi' => 'Foto sarana pengisian BBM',
                'allowed_extensions' => json_encode(['jpg', 'jpeg', 'png', 'gif']),
                'max_size_kb' => 5120, // 5MB
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'kode_dokumen' => 'FOTO_OPERASIONAL',
                'nama_dokumen' => 'Foto Operasional',
                'deskripsi' => 'Foto kegiatan operasional',
                'allowed_extensions' => json_encode(['jpg', 'jpeg', 'png', 'gif']),
                'max_size_kb' => 5120, // 5MB
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'kode_dokumen' => 'FOTO_LAINNYA',
                'nama_dokumen' => 'Foto Lainnya',
                'deskripsi' => 'Foto dokumentasi lainnya',
                'allowed_extensions' => json_encode(['jpg', 'jpeg', 'png', 'gif']),
                'max_size_kb' => 5120, // 5MB
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tipe_dokumen')->insert($tipeDokumen);
    }
}

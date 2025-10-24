<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipeBaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipeBa = [
            [
                'id' => 1,
                'kode_ba' => 'BA-01',
                'nama_ba' => 'BA Akhir Bulan',
                'deskripsi' => 'Berita Acara Akhir Bulan untuk laporan bulanan BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'kode_ba' => 'BA-02',
                'nama_ba' => 'BA Sisa Sebelum Pengisian',
                'deskripsi' => 'Berita Acara Sisa Sebelum Pengisian BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'kode_ba' => 'BA-03',
                'nama_ba' => 'BA Penggunaan BBM',
                'deskripsi' => 'Berita Acara Penggunaan BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'kode_ba' => 'BA-04',
                'nama_ba' => 'BA Pemeriksaan Sarana Pengisian',
                'deskripsi' => 'Berita Acara Pemeriksaan Sarana Pengisian BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'kode_ba' => 'BA-05',
                'nama_ba' => 'BA Penerimaan BBM',
                'deskripsi' => 'Berita Acara Penerimaan BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'kode_ba' => 'BA-06',
                'nama_ba' => 'BA Sebelum Pelayaran',
                'deskripsi' => 'Berita Acara Sebelum Pelayaran',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'kode_ba' => 'BA-07',
                'nama_ba' => 'BA Sesudah Pelayaran',
                'deskripsi' => 'Berita Acara Sesudah Pelayaran',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'kode_ba' => 'BA-08',
                'nama_ba' => 'BA Penitipan BBM',
                'deskripsi' => 'Berita Acara Penitipan BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'kode_ba' => 'BA-09',
                'nama_ba' => 'BA Pengembalian BBM',
                'deskripsi' => 'Berita Acara Pengembalian BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'kode_ba' => 'BA-10',
                'nama_ba' => 'BA Peminjaman BBM',
                'deskripsi' => 'Berita Acara Peminjaman BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'kode_ba' => 'BA-11',
                'nama_ba' => 'BA Penerimaan Pinjaman BBM',
                'deskripsi' => 'Berita Acara Penerimaan Pinjaman BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'kode_ba' => 'BA-12',
                'nama_ba' => 'BA Pengembalian Pinjaman BBM',
                'deskripsi' => 'Berita Acara Pengembalian Pinjaman BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'kode_ba' => 'BA-13',
                'nama_ba' => 'BA Penerimaan Pengembalian BBM',
                'deskripsi' => 'Berita Acara Penerimaan Pengembalian BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'kode_ba' => 'BA-14',
                'nama_ba' => 'BA Pemberian Hibah BBM Antar Kapal Pengawas',
                'deskripsi' => 'Berita Acara Pemberian Hibah BBM Antar Kapal Pengawas',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 15,
                'kode_ba' => 'BA-15',
                'nama_ba' => 'BA Penerimaan Hibah BBM Antar Kapal Pengawas',
                'deskripsi' => 'Berita Acara Penerimaan Hibah BBM Antar Kapal Pengawas',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 16,
                'kode_ba' => 'BA-16',
                'nama_ba' => 'BA Pemberian Hibah BBM Instansi Lain',
                'deskripsi' => 'Berita Acara Pemberian Hibah BBM Instansi Lain',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 17,
                'kode_ba' => 'BA-17',
                'nama_ba' => 'BA Penerimaan Hibah BBM',
                'deskripsi' => 'Berita Acara Penerimaan Hibah BBM',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 18,
                'kode_ba' => 'BA-18',
                'nama_ba' => 'BA Penerimaan Hibah BBM Instansi Lain',
                'deskripsi' => 'Berita Acara Penerimaan Hibah BBM Instansi Lain',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tipe_ba')->insert($tipeBa);
    }
}

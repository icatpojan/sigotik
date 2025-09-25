<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MKapal;

class MKapalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kapals = [
            [
                'm_kapal_id' => 1,
                'nama_kapal' => 'Kapal Patroli 001',
                'code_kapal' => 'KAPAL-001',
                'm_upt_code' => '000',
                'bobot' => 50.00,
                'panjang' => 20.00,
                'tinggi' => 5.00,
                'lebar' => 8.00,
                'main_engine' => 'YANMAR 6GH-UTE',
                'jml_main_engine' => 2,
                'pk_main_engine' => '500 HP',
                'aux_engine_utama' => 'MITSUBISHI 4D 30',
                'jml_aux_engine_utama' => 1,
                'pk_aux_engine_utama' => '100 HP',
                'gerak_engine' => 'Diesel',
                'aux_engine_emergency' => 'Emergency Engine',
                'galangan_pembuat' => 'PT. Galangan Indonesia',
                'kapasitas_tangki' => '5000',
                'jml_tangki' => 2,
                'tahun_buat' => 2020,
                'jml_abk' => 10,
                'nama_nakoda' => 'John Doe',
                'nip_nakoda' => '123456789012345678',
                'jabatan_nakoda' => 'Nakhoda',
                'pangkat_nakoda' => 'III/c',
                'golongan_nakoda' => 'III/c',
                'gambar_kapal' => 'kapal-001.jpg',
                'lampiran_kapal' => 'lampiran-001.pdf',
                'nama_kkm' => 'Jane Doe',
                'nip_kkm' => '987654321098765432',
                'jabatan_kkm' => 'KKM',
                'pangkat_kkm' => 'III/b',
                'golongan_kkm' => 'III/b',
                'date_insert' => now(),
                'user_insert' => 'admin',
                'date_update' => now(),
                'user_update' => 'admin'
            ],
        ];

        foreach ($kapals as $kapal) {
            MKapal::updateOrCreate(
                ['m_kapal_id' => $kapal['m_kapal_id']],
                $kapal
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BbmKapaltrans;

class BbmKapaltransSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kapaltrans = [
            [
                'trans_id' => 1,
                'kapal_code' => 'KAPAL-001',
                'nomor_surat' => 'SUR-001-2024',
                'tanggal_surat' => now(),
                'jam_surat' => '08:00:00',
                'zona_waktu_surat' => 'WIB',
                'lokasi_surat' => 'Jakarta',
                'volume_sisa' => 500.00,
                'volume_sebelum' => 1000,
                'tanggal_sebelum' => now()->subDays(7),
                'volume_pengisian' => 2000.00,
                'tanggal_pengisian' => now(),
                'volume_pemakaian' => 500.00,
                'nomor_nota' => 'NOTA-001-2024',
                'keterangan_jenis_bbm' => 'Solar',
                'status_ba' => 0,
                'jenis_tranport' => '2',
                'status_segel' => 1,
                'gambar_segel' => null,
                'status_flowmeter' => 1,
                'gambar_flowmeter' => null,
                'nama_nahkoda' => 'John Doe',
                'nip_nahkoda' => '123456789012345678',
                'jabatan_nahkoda' => 'Nakhoda',
                'pangkat_nahkoda' => 'III/c',
                'golongan_nahkoda' => 'III/c',
                'nama_kkm' => 'Jane Doe',
                'nip_kkm' => '987654321098765432',
                'jabatan_kkm' => 'KKM',
                'pangkat_kkm' => 'III/b',
                'golongan_kkm' => 'III/b',
                'nama_an' => 'Bob Smith',
                'nip_an' => '111111111111111111',
                'jabatan_an' => 'AN',
                'pangkat_an' => 'III/a',
                'golongan_an' => 'III/a',
                'an_nakhoda' => 0,
                'an_kkm' => 0,
                'kapal_code_temp' => null,
                'pangkat_nahkoda' => 'III/c',
                'nama_nahkoda_temp' => null,
                'nip_nahkoda_temp' => null,
                'jabatan_nahkoda_temp' => null,
                'pangkat_nahkoda_temp' => null,
                'golongan_nahkoda_temp' => null,
                'nama_kkm_temp' => null,
                'nip_kkm_temp' => null,
                'jabatan_kkm_temp' => null,
                'pangkat_kkm_temp' => null,
                'golongan_kkm_temp' => null,
                'nama_an_temp' => null,
                'nip_an_temp' => null,
                'jabatan_an_temp' => null,
                'pangkat_an_temp' => null,
                'golongan_an_temp' => null,
                'an_nakhoda_temp' => 0,
                'an_kkm_temp' => 0,
                'user_input' => 'admin',
                'tanggal_input' => now(),
                'user_app' => 'admin',
                'tanggal_app' => now(),
                'status_trans' => 1
            ],
        ];

        foreach ($kapaltrans as $item) {
            BbmKapaltrans::updateOrCreate(
                ['trans_id' => $item['trans_id']],
                $item
            );
        }
    }
}

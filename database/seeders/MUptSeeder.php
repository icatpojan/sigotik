<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MUpt;

class MUptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $upts = [
            [
                'm_upt_id' => 1,
                'nama' => 'Pusat',
                'code' => '000',
                'alamat1' => 'Jakarta',
                'alamat2' => 'Indonesia',
                'alamat3' => '',
                'kota' => 'Jakarta',
                'zona_waktu_upt' => 'WIB',
                'nama_petugas' => 'Administrator',
                'nip_petugas' => '000000000000000000',
                'jabatan_petugas' => 'Administrator',
                'pangkat_petugas' => 'IV/a',
                'date_insert' => now(),
                'user_insert' => 'system',
                'date_update' => now(),
                'user_update' => 'system'
            ],
        ];

        foreach ($upts as $upt) {
            MUpt::updateOrCreate(
                ['m_upt_id' => $upt['m_upt_id']],
                $upt
            );
        }
    }
}

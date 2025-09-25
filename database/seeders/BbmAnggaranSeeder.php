<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\BbmAnggaran;

class BbmAnggaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $anggarans = [
            [
                'anggaran_id' => 1,
                'periode' => 2024,
                'm_upt_code' => '000',
                'anggaran' => 0.00,
                'perubahan_ke' => 0,
                'keterangan' => 'PAGU ANGGARAN BBM AWAL',
                'statusanggaran' => 1,
                'user_input' => 'admin',
                'tanggal_input' => now(),
                'user_app' => 'admin',
                'tanggal_app' => now()
            ],
        ];

        foreach ($anggarans as $anggaran) {
            BbmAnggaran::updateOrCreate(
                ['anggaran_id' => $anggaran['anggaran_id']],
                $anggaran
            );
        }
    }
}

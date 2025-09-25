<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BbmAnggaranUpt;

class BbmAnggaranUptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $anggaranUpts = [
            [
                'anggaran_upt_id' => 1,
                'tanggal_trans' => now(),
                'm_upt_code' => '000',
                'nominal' => 100000000.00,
                'nomor_surat' => 'SUR-001-2024',
                'keterangan' => 'Perubahan Anggaran BBM',
                'statusperubahan' => 1,
                'user_input' => 'admin',
                'tanggal_input' => now(),
                'user_app' => 'admin',
                'tanggal_app' => now()
            ],
        ];

        foreach ($anggaranUpts as $anggaranUpt) {
            BbmAnggaranUpt::updateOrCreate(
                ['anggaran_upt_id' => $anggaranUpt['anggaran_upt_id']],
                $anggaranUpt
            );
        }
    }
}

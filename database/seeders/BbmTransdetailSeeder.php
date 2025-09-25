<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BbmTransdetail;

class BbmTransdetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transdetails = [
            [
                'bbm_transdetail_id' => 1,
                'nomor_surat' => 'SUR-001-2024',
                'transportasi' => 'Kapal',
                'no_so' => 'SO-001-2024',
                'no_do' => 'DO-001-2024',
                'volume_isi' => 1000.00,
                'keterangan' => 'Pengisian BBM Kapal',
                'no_invoice' => 'INV-001-2024',
                'tgl_invoice' => now(),
                'harga_total' => 10000000.00,
                'status_bayar' => 0,
                'no_tagihan' => 'TAG-001-2024',
                'tanggalinput' => now(),
                'userid' => 'admin'
            ],
        ];

        foreach ($transdetails as $transdetail) {
            BbmTransdetail::updateOrCreate(
                ['bbm_transdetail_id' => $transdetail['bbm_transdetail_id']],
                $transdetail
            );
        }
    }
}

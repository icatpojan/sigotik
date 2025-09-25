<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BbmTagihan;

class BbmTagihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tagihans = [
            [
                'tagihan_id' => 1,
                'm_upt_code' => '000',
                'no_tagihan' => 'TAG-001-2024',
                'tanggal_invoice' => now(),
                'no_invoice' => 'INV-001-2024',
                'penyedia' => 'PT. Contoh Penyedia',
                'quantity' => 1000,
                'harga' => 10000000.00,
                'hargaperliter' => 10000.00,
                'ppn' => 1100000.00,
                'total' => 11100000.00,
                'statustagihan' => 0,
                'tanggal_sppd' => now(),
                'file' => null,
                'user_input' => 'admin',
                'tanggal_input' => now(),
                'user_app' => '',
                'tanggal_app' => null,
                'user_batal' => '',
                'tanggal_batal' => null
            ],
        ];

        foreach ($tagihans as $tagihan) {
            BbmTagihan::updateOrCreate(
                ['tagihan_id' => $tagihan['tagihan_id']],
                $tagihan
            );
        }
    }
}

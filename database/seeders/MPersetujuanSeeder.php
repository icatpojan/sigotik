<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MPersetujuan;

class MPersetujuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $persetujuans = [
            ['id' => 1, 'deskripsi_persetujuan' => 'Direktur POA'],
            ['id' => 2, 'deskripsi_persetujuan' => 'Kepala Pangkalan PSDKP'],
            ['id' => 3, 'deskripsi_persetujuan' => 'Kepala Stasiun PSDKP'],
        ];

        foreach ($persetujuans as $persetujuan) {
            MPersetujuan::updateOrCreate(
                ['id' => $persetujuan['id']],
                $persetujuan
            );
        }
    }
}

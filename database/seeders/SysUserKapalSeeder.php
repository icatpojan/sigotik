<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SysUserKapal;

class SysUserKapalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userKapals = [
            [
                'sys_user_kapal_id' => 1,
                'conf_user_id' => 1,
                'm_kapal_id' => 1
            ],
        ];

        foreach ($userKapals as $userKapal) {
            SysUserKapal::updateOrCreate(
                ['sys_user_kapal_id' => $userKapal['sys_user_kapal_id']],
                $userKapal
            );
        }
    }
}

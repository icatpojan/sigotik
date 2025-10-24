<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysUserKapalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userKapalRelations = [
            [
                'sys_user_kapal_id' => 141,
                'conf_user_id' => 16, // orca01
                'm_kapal_id' => 1 // Akar Bahar 01 (sesuai dengan data yang ada)
            ],
            [
                'sys_user_kapal_id' => 142,
                'conf_user_id' => 17, // orca02
                'm_kapal_id' => 2 // Baracuda 1 (sesuai dengan data yang ada)
            ],
            [
                'sys_user_kapal_id' => 143,
                'conf_user_id' => 19, // orca04
                'm_kapal_id' => 3 // Baracuda 2 (sesuai dengan data yang ada)
            ],
            [
                'sys_user_kapal_id' => 144,
                'conf_user_id' => 20, // macantutul01
                'm_kapal_id' => 4 // Hiu 01 (sesuai dengan data yang ada)
            ],
            [
                'sys_user_kapal_id' => 146,
                'conf_user_id' => 23, // akarbahar01
                'm_kapal_id' => 1 // Akar Bahar 01
            ],
            [
                'sys_user_kapal_id' => 147,
                'conf_user_id' => 22, // paus01
                'm_kapal_id' => 5 // Hiu 02 (sesuai dengan data yang ada)
            ],
            [
                'sys_user_kapal_id' => 148,
                'conf_user_id' => 18, // orca03
                'm_kapal_id' => 6 // Hiu 03 (sesuai dengan data yang ada)
            ],
            [
                'sys_user_kapal_id' => 159,
                'conf_user_id' => 27, // pontianak
                'm_kapal_id' => 14 // Hiu 11 (sesuai dengan data yang ada)
            ],
            [
                'sys_user_kapal_id' => 160,
                'conf_user_id' => 27, // pontianak
                'm_kapal_id' => 19 // Hiu Macan 01 (sesuai dengan data yang ada)
            ],
            [
                'sys_user_kapal_id' => 169,
                'conf_user_id' => 28, // hiu01
                'm_kapal_id' => 4 // Hiu 01
            ],
            [
                'sys_user_kapal_id' => 250,
                'conf_user_id' => 26, // macan01
                'm_kapal_id' => 19 // Hiu Macan 01
            ]
        ];

        foreach ($userKapalRelations as $relation) {
            DB::table('sys_user_kapal')->insert($relation);
        }
    }
}

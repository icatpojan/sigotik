<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MUptSeeder::class,
            MKapalSeeder::class,
            ConfGroupSeeder::class,
            CategorySeeder::class,
            MPersetujuanSeeder::class,
            StmMenuv2Seeder::class,
            ConfRoleMenuSeeder::class,
            MenuSeeder::class,
            PortNewsSeeder::class,
            UjicobaSeeder::class,
            BbmAnggaranSeeder::class,
            BbmAnggaranUptSeeder::class,
            BbmKapaltransSeeder::class,
            BbmTagihanSeeder::class,
            BbmTransdetailSeeder::class,
            SysUserKapalSeeder::class,
            ConfUserSeeder::class,
        ]);
    }
}

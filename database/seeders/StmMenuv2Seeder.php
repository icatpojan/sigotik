<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StmMenuv2;

class StmMenuv2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            ['id' => 1, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Master Data', 'linka' => null, 'icon' => 'fa fa-archive', 'urutan' => 2],
            ['id' => 2, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Permohonan', 'linka' => 'master.display_permohonan', 'icon' => 'fa fa-laptop', 'urutan' => 6],
            ['id' => 3, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Transaksi', 'linka' => null, 'icon' => 'fa fa-exchange-alt', 'urutan' => 3],
            ['id' => 4, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 5, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Pengaturan', 'linka' => null, 'icon' => 'fa fa-cog', 'urutan' => 5],
        ];

        foreach ($menus as $menu) {
            StmMenuv2::updateOrCreate(
                ['id' => $menu['id']],
                $menu
            );
        }
    }
}

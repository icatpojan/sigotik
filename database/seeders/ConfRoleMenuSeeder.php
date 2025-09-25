<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfRoleMenu;

class ConfRoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleMenus = [
            ['id' => 1, 'conf_group_id' => 1, 'stm_menu_id' => 1], // Admin - Master Data
            ['id' => 2, 'conf_group_id' => 1, 'stm_menu_id' => 2], // Admin - Permohonan
            ['id' => 3, 'conf_group_id' => 1, 'stm_menu_id' => 3], // Admin - Transaksi
            ['id' => 4, 'conf_group_id' => 1, 'stm_menu_id' => 4], // Admin - Laporan
            ['id' => 5, 'conf_group_id' => 1, 'stm_menu_id' => 5], // Admin - Pengaturan
        ];

        foreach ($roleMenus as $roleMenu) {
            ConfRoleMenu::updateOrCreate(
                ['id' => $roleMenu['id']],
                $roleMenu
            );
        }
    }
}

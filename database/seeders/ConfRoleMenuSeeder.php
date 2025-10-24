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
            // Admins (Group 1) - Full Access - Berdasarkan data asli project_ci
            ['id' => 1, 'conf_group_id' => 1, 'stm_menu_id' => 6], // User
            ['id' => 2, 'conf_group_id' => 1, 'stm_menu_id' => 7], // Role
            ['id' => 3, 'conf_group_id' => 1, 'stm_menu_id' => 8], // Menu
            ['id' => 4, 'conf_group_id' => 1, 'stm_menu_id' => 9], // Release
            ['id' => 5, 'conf_group_id' => 1, 'stm_menu_id' => 10], // Kapal
            ['id' => 6, 'conf_group_id' => 1, 'stm_menu_id' => 11], // UPT
            ['id' => 7, 'conf_group_id' => 1, 'stm_menu_id' => 30], // Anggaran dan Realisasi
            ['id' => 8, 'conf_group_id' => 1, 'stm_menu_id' => 31], // Entri Anggaran
            ['id' => 9, 'conf_group_id' => 1, 'stm_menu_id' => 32], // Perubahan Anggaran
            ['id' => 10, 'conf_group_id' => 1, 'stm_menu_id' => 33], // Approval Anggaran
            ['id' => 11, 'conf_group_id' => 1, 'stm_menu_id' => 34], // Entry Realisasi
            ['id' => 12, 'conf_group_id' => 1, 'stm_menu_id' => 35], // Approval Realisasi
            ['id' => 13, 'conf_group_id' => 1, 'stm_menu_id' => 36], // Tanggal SPPD
            ['id' => 14, 'conf_group_id' => 1, 'stm_menu_id' => 37], // Entry Anggaran Internal
            ['id' => 15, 'conf_group_id' => 1, 'stm_menu_id' => 38], // Approval Anggaran Internal
            ['id' => 16, 'conf_group_id' => 1, 'stm_menu_id' => 39], // Pembatalan Anggaran Internal
            ['id' => 17, 'conf_group_id' => 1, 'stm_menu_id' => 40], // Laporan BBM
            ['id' => 18, 'conf_group_id' => 1, 'stm_menu_id' => 41], // Laporan Anggaran
            ['id' => 19, 'conf_group_id' => 1, 'stm_menu_id' => 42], // Monitoring BBM
            ['id' => 20, 'conf_group_id' => 1, 'stm_menu_id' => 43], // Monitoring PINJAMAN

            // UPT (Group 2) - Limited Access - Berdasarkan data asli project_ci
            ['id' => 21, 'conf_group_id' => 2, 'stm_menu_id' => 30], // Anggaran dan Realisasi
            ['id' => 22, 'conf_group_id' => 2, 'stm_menu_id' => 31], // Entri Anggaran
            ['id' => 23, 'conf_group_id' => 2, 'stm_menu_id' => 34], // Entry Realisasi
            ['id' => 24, 'conf_group_id' => 2, 'stm_menu_id' => 35], // Approval Realisasi
            ['id' => 25, 'conf_group_id' => 2, 'stm_menu_id' => 36], // Tanggal SPPD
            ['id' => 26, 'conf_group_id' => 2, 'stm_menu_id' => 37], // Entry Anggaran Internal
            ['id' => 27, 'conf_group_id' => 2, 'stm_menu_id' => 38], // Approval Anggaran Internal
            ['id' => 28, 'conf_group_id' => 2, 'stm_menu_id' => 39], // Pembatalan Anggaran Internal
            ['id' => 29, 'conf_group_id' => 2, 'stm_menu_id' => 40], // Laporan BBM
            ['id' => 30, 'conf_group_id' => 2, 'stm_menu_id' => 41], // Laporan Anggaran
            ['id' => 31, 'conf_group_id' => 2, 'stm_menu_id' => 42], // Monitoring BBM
            ['id' => 32, 'conf_group_id' => 2, 'stm_menu_id' => 43], // Monitoring PINJAMAN

            // KAPAL (Group 3) - Very Limited Access - Hanya BA dan Laporan
            ['id' => 33, 'conf_group_id' => 3, 'stm_menu_id' => 20], // BA Sebelum Pengisian
            ['id' => 34, 'conf_group_id' => 3, 'stm_menu_id' => 21], // BA Penggunaan BBM
            ['id' => 35, 'conf_group_id' => 3, 'stm_menu_id' => 22], // BA Penerimaan BBM
            ['id' => 36, 'conf_group_id' => 3, 'stm_menu_id' => 23], // BA Pemeriksaan Sarana Pengisian
            ['id' => 37, 'conf_group_id' => 3, 'stm_menu_id' => 26], // BA Sebelum Pelayaran
            ['id' => 38, 'conf_group_id' => 3, 'stm_menu_id' => 27], // BA Sesudah Pelayaran
            ['id' => 39, 'conf_group_id' => 3, 'stm_menu_id' => 28], // BA Akhir Bulan
            ['id' => 40, 'conf_group_id' => 3, 'stm_menu_id' => 40], // Laporan BBM

            // PUSAT (Group 5) - Limited Access - Hanya Laporan
            ['id' => 41, 'conf_group_id' => 5, 'stm_menu_id' => 40], // Laporan BBM
            ['id' => 42, 'conf_group_id' => 5, 'stm_menu_id' => 41], // Laporan Anggaran
            ['id' => 43, 'conf_group_id' => 5, 'stm_menu_id' => 42], // Monitoring BBM
        ];

        foreach ($roleMenus as $roleMenu) {
            ConfRoleMenu::updateOrCreate(
                ['id' => $roleMenu['id']],
                $roleMenu
            );
        }
    }
}

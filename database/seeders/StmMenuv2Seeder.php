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
            // Master Data
            ['id' => 1, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Master Data', 'linka' => null, 'icon' => 'fa fa-archive', 'urutan' => 2],
            ['id' => 2, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Permohonan', 'linka' => 'master.display_permohonan', 'icon' => 'fa fa-laptop', 'urutan' => 6],
            ['id' => 3, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Transaksi', 'linka' => null, 'icon' => 'fa fa-exchange-alt', 'urutan' => 3],
            ['id' => 4, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 5, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Config', 'linka' => null, 'icon' => 'fa fa-gears', 'urutan' => 1],

            // User Management (Config submenu)
            ['id' => 6, 'id_parentmenu' => 5, 'level' => 2, 'menu' => 'User', 'linka' => 'users.index', 'icon' => null, 'urutan' => 1],
            ['id' => 7, 'id_parentmenu' => 5, 'level' => 2, 'menu' => 'Role', 'linka' => 'groups.index', 'icon' => null, 'urutan' => 2],
            ['id' => 8, 'id_parentmenu' => 5, 'level' => 2, 'menu' => 'Menu', 'linka' => 'menus.index', 'icon' => null, 'urutan' => 3],
            ['id' => 9, 'id_parentmenu' => 5, 'level' => 2, 'menu' => 'Release', 'linka' => 'release.index', 'icon' => null, 'urutan' => 4],

            // Master Data Items
            ['id' => 10, 'id_parentmenu' => 1, 'level' => 2, 'menu' => 'Kapal', 'linka' => 'kapals.index', 'icon' => null, 'urutan' => 1],
            ['id' => 11, 'id_parentmenu' => 1, 'level' => 2, 'menu' => 'UPT', 'linka' => 'upts.index', 'icon' => null, 'urutan' => 2],

            // Monitoring BBM
            ['id' => 19, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Monitoring BBM', 'linka' => null, 'icon' => 'fa fa-desktop', 'urutan' => 3],
            ['id' => 20, 'id_parentmenu' => 19, 'level' => 2, 'menu' => 'BA Sebelum Pengisian', 'linka' => 'ba-sebelum-pengisian.index', 'icon' => null, 'urutan' => 1],
            ['id' => 21, 'id_parentmenu' => 19, 'level' => 2, 'menu' => 'BA Penggunaan BBM', 'linka' => 'ba-penggunaan-bbm.index', 'icon' => null, 'urutan' => 4],
            ['id' => 22, 'id_parentmenu' => 19, 'level' => 2, 'menu' => 'BA Penerimaan BBM', 'linka' => 'ba-penerimaan-bbm.index', 'icon' => null, 'urutan' => 6],
            ['id' => 23, 'id_parentmenu' => 19, 'level' => 2, 'menu' => 'BA Pemeriksaan Sarana Pengisian', 'linka' => 'ba-pemeriksaan-sarana-pengisian.index', 'icon' => null, 'urutan' => 5],
            ['id' => 26, 'id_parentmenu' => 19, 'level' => 2, 'menu' => 'BA Sebelum Pelayaran', 'linka' => 'ba-sebelum-pelayaran.index', 'icon' => null, 'urutan' => 2],
            ['id' => 27, 'id_parentmenu' => 19, 'level' => 2, 'menu' => 'BA Sesudah Pelayaran', 'linka' => 'ba-sesudah-pelayaran.index', 'icon' => null, 'urutan' => 3],
            ['id' => 28, 'id_parentmenu' => 19, 'level' => 2, 'menu' => 'BA Akhir Bulan', 'linka' => 'ba-akhir-bulan.index', 'icon' => null, 'urutan' => 7],

            // Anggaran dan Realisasi
            ['id' => 30, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Anggaran dan Realisasi', 'linka' => null, 'icon' => 'fa fa-book', 'urutan' => 7],
            ['id' => 31, 'id_parentmenu' => 30, 'level' => 2, 'menu' => 'Entri Anggaran', 'linka' => 'anggaran.entri-anggaran', 'icon' => null, 'urutan' => 1],
            ['id' => 32, 'id_parentmenu' => 30, 'level' => 2, 'menu' => 'Perubahan Anggaran', 'linka' => 'anggaran.perubahan-anggaran', 'icon' => null, 'urutan' => 2],
            ['id' => 33, 'id_parentmenu' => 30, 'level' => 2, 'menu' => 'Approval Anggaran', 'linka' => 'anggaran.approval-anggaran', 'icon' => null, 'urutan' => 3],
            ['id' => 34, 'id_parentmenu' => 30, 'level' => 2, 'menu' => 'Entry Realisasi', 'linka' => 'anggaran.entry-realisasi', 'icon' => null, 'urutan' => 4],
            ['id' => 35, 'id_parentmenu' => 30, 'level' => 2, 'menu' => 'Approval Realisasi', 'linka' => 'anggaran.approval-realisasi', 'icon' => null, 'urutan' => 5],
            ['id' => 36, 'id_parentmenu' => 30, 'level' => 2, 'menu' => 'Tanggal SPPD', 'linka' => 'anggaran.tanggal-sppd', 'icon' => null, 'urutan' => 7],
            ['id' => 37, 'id_parentmenu' => 30, 'level' => 2, 'menu' => 'Entry Anggaran Internal', 'linka' => 'anggaran.entry-anggaran-internal', 'icon' => null, 'urutan' => 8],
            ['id' => 38, 'id_parentmenu' => 30, 'level' => 2, 'menu' => 'Approval Anggaran Internal', 'linka' => 'anggaran.approval-anggaran-internal', 'icon' => null, 'urutan' => 9],
            ['id' => 39, 'id_parentmenu' => 30, 'level' => 2, 'menu' => 'Pembatalan Anggaran Internal', 'linka' => 'anggaran.pembatalan-anggaran-internal', 'icon' => null, 'urutan' => 10],

            // Laporan BBM
            ['id' => 40, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan BBM', 'linka' => null, 'icon' => 'fa fa-ship', 'urutan' => 8],
            ['id' => 41, 'id_parentmenu' => 40, 'level' => 2, 'menu' => 'LAP Total Penerimaan & Penggunan BBM', 'linka' => 'lap.display_penerimaanbbm', 'icon' => null, 'urutan' => 1],
            ['id' => 42, 'id_parentmenu' => 40, 'level' => 2, 'menu' => 'LAP Detail Penggunaan & Penerimaan BBM', 'linka' => 'lap.display_detail_penerimaanbbm', 'icon' => null, 'urutan' => 2],
            ['id' => 43, 'id_parentmenu' => 40, 'level' => 2, 'menu' => 'History Penerimaan & Penggunaan BBM', 'linka' => 'lap.history_detail_penerimaanbbm', 'icon' => null, 'urutan' => 3],

            // Laporan Anggaran
            ['id' => 44, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Anggaran & Realisasi', 'linka' => null, 'icon' => 'fa fa-print', 'urutan' => 9],
            ['id' => 45, 'id_parentmenu' => 44, 'level' => 2, 'menu' => 'Laporan Anggaran', 'linka' => 'lap.display_anggaran', 'icon' => null, 'urutan' => 1],
            ['id' => 46, 'id_parentmenu' => 44, 'level' => 2, 'menu' => 'Riwayat Anggaran & Realisasi ALL', 'linka' => 'lap.display_riwayatanggaran', 'icon' => null, 'urutan' => 2],
            ['id' => 47, 'id_parentmenu' => 44, 'level' => 2, 'menu' => 'Laporan Realisasi per Periode', 'linka' => 'lap.display_realisasi', 'icon' => null, 'urutan' => 3],
            ['id' => 48, 'id_parentmenu' => 44, 'level' => 2, 'menu' => 'Laporan Transaksi Realisasi UPT', 'linka' => 'lap.display_anggaranupt', 'icon' => null, 'urutan' => 4],
        ];

        foreach ($menus as $menu) {
            StmMenuv2::updateOrCreate(
                ['id' => $menu['id']],
                $menu
            );
        }
    }
}

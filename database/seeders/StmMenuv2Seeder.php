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
            ['id' => 5, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Pengaturan', 'linka' => null, 'icon' => 'fa fa-cog', 'urutan' => 5],

            // User Management
            ['id' => 6, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'User', 'linka' => 'users.index', 'icon' => 'fa fa-users', 'urutan' => 1],
            ['id' => 7, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Role', 'linka' => 'groups.index', 'icon' => 'fa fa-user-shield', 'urutan' => 1],
            ['id' => 8, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Menu', 'linka' => null, 'icon' => 'fa fa-list', 'urutan' => 1],
            ['id' => 9, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Release', 'linka' => null, 'icon' => 'fa fa-rocket', 'urutan' => 1],

            // Master Data Items
            ['id' => 10, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Kapal', 'linka' => null, 'icon' => 'fa fa-ship', 'urutan' => 2],
            ['id' => 11, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'UPT', 'linka' => null, 'icon' => 'fa fa-building', 'urutan' => 2],

            // Monitoring BBM
            ['id' => 12, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Sebelum Pengisian', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 13, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Sebelum Pelayaran', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 14, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Sesudah Pelayaran', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 15, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Penggunaan BBM', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 16, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Pemeriksaan Sarana Pengisian', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 17, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Penerimaan BBM', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 18, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Akhir Bulan', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 19, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Penitipan BBM', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 20, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Pengembalian BBM', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],

            // Monitoring Pinjaman
            ['id' => 21, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Peminjaman BBM', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 22, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Penerimaan Pinjaman BBM', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 23, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Pengembalian Pinjaman BBM', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 24, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Penerimaan Pengembalian BBM', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],

            // Monitoring Hibah
            ['id' => 25, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Pemberi Hibah BBM Kapal Pengawas', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 26, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Penerima Hibah BBM Kapal Pengawas', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 27, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Pemberi Hibah BBM Dengan Instansi Lain', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 28, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Penerima Hibah BBM Dengan Instansi Lain', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],
            ['id' => 29, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'BA Penerimaan Hibah BBM', 'linka' => null, 'icon' => 'fa fa-file-alt', 'urutan' => 3],

            // Anggaran dan Realisasi
            ['id' => 30, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Entri Anggaran', 'linka' => null, 'icon' => 'fa fa-money-bill', 'urutan' => 3],
            ['id' => 31, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Perubahan Anggaran', 'linka' => null, 'icon' => 'fa fa-money-bill', 'urutan' => 3],
            ['id' => 32, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Approval Anggaran', 'linka' => null, 'icon' => 'fa fa-check', 'urutan' => 3],
            ['id' => 33, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Entry Realisasi', 'linka' => null, 'icon' => 'fa fa-money-bill', 'urutan' => 3],
            ['id' => 34, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Approval Realisasi', 'linka' => null, 'icon' => 'fa fa-check', 'urutan' => 3],
            ['id' => 35, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Pembatalan Realisasi', 'linka' => null, 'icon' => 'fa fa-times', 'urutan' => 3],
            ['id' => 36, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Tanggal SPPD', 'linka' => null, 'icon' => 'fa fa-calendar', 'urutan' => 3],
            ['id' => 37, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Entry Anggaran Internal', 'linka' => null, 'icon' => 'fa fa-money-bill', 'urutan' => 3],
            ['id' => 38, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Approval Anggaran Internal', 'linka' => null, 'icon' => 'fa fa-check', 'urutan' => 3],
            ['id' => 39, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Pembatalan Anggaran Internal', 'linka' => null, 'icon' => 'fa fa-times', 'urutan' => 3],

            // Laporan BBM
            ['id' => 40, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'LAP Total Penerimaan & Penggunaan BBM', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 41, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'LAP Detail Penggunaan & Penerimaan BBM', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 42, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'History Penerimaan & Penggunaan BBM', 'linka' => null, 'icon' => 'fa fa-history', 'urutan' => 4],
            ['id' => 43, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan BBM Akhir Bulan', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 44, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Penerimaan BBM', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 45, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Penitipan BBM', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 46, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Pengembalian BBM', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 47, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Peminjaman', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 48, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Pengembalian Pinjaman', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 49, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Pinjaman Belum di Kembalikan', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 50, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Hibah Antar Kapal Pengawas', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 51, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Pemberi Hibah BBM Instansi Lain', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 52, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Penerima Hibah BBM Instansi Lain', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 53, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Penerimaan Hibah BBM', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],

            // Laporan Anggaran
            ['id' => 54, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Anggaran', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 55, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Riwayat Anggaran & Realisasi ALL', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 56, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Realisasi per Periode', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 57, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Transaksi Realisasi UPT', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 58, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Transaksi Perubahan Anggaran Internal UPT', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 59, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Berita Acara Pembayaran Tagihan', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],
            ['id' => 60, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Laporan Verifikasi Tagihan', 'linka' => null, 'icon' => 'fa fa-chart-bar', 'urutan' => 4],

            // Portal Berita
            ['id' => 61, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Portal Berita', 'linka' => null, 'icon' => 'fa fa-newspaper', 'urutan' => 5],

            // Additional Features
            ['id' => 62, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Buat Notifikasi', 'linka' => null, 'icon' => 'fa fa-bell', 'urutan' => 5],
            ['id' => 63, 'id_parentmenu' => null, 'level' => 1, 'menu' => 'Input Rencana', 'linka' => null, 'icon' => 'fa fa-calendar-plus', 'urutan' => 5],
        ];

        foreach ($menus as $menu) {
            StmMenuv2::updateOrCreate(
                ['id' => $menu['id']],
                $menu
            );
        }
    }
}

<?php

namespace App\Helpers;

class SidebarHelper
{
    /**
     * Determine if a menu item is active based on current route
     */
    public static function isActive($routeName, $currentRoute = null)
    {
        if ($currentRoute === null) {
            $currentRoute = request()->route()->getName();
        }

        return $currentRoute === $routeName;
    }

    /**
     * Determine if a menu group is active based on current route
     */
    public static function isGroupActive($routes, $currentRoute = null)
    {
        if ($currentRoute === null) {
            $currentRoute = request()->route()->getName();
        }

        return in_array($currentRoute, $routes);
    }

    /**
     * Get active menu class
     */
    public static function getActiveClass($routeName, $currentRoute = null)
    {
        return self::isActive($routeName, $currentRoute) ? 'menu-item-active' : 'menu-item-inactive';
    }

    /**
     * Get menu group class (for dropdown headers)
     */
    public static function getMenuGroupClass($groupName, $currentRoute = null)
    {
        if ($currentRoute === null) {
            $currentRoute = request()->route()->getName();
        }

        $groups = self::getMenuGroups();
        $isGroupActive = isset($groups[$groupName]) && in_array($currentRoute, $groups[$groupName]);

        // Always return inactive for menu groups (dropdown headers)
        return 'menu-item-inactive';
    }

    /**
     * Get active dropdown class
     */
    public static function getDropdownActiveClass($routeName, $currentRoute = null)
    {
        return self::isActive($routeName, $currentRoute) ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive';
    }

    /**
     * Get active icon class
     */
    public static function getIconActiveClass($routeName, $currentRoute = null)
    {
        return self::isActive($routeName, $currentRoute) ? 'menu-item-icon-active' : 'menu-item-icon-inactive';
    }

    /**
     * Get active dropdown icon class
     */
    public static function getDropdownIconActiveClass($routeName, $currentRoute = null)
    {
        return self::isActive($routeName, $currentRoute) ? 'menu-dropdown-item-icon-active' : 'menu-dropdown-item-icon-inactive';
    }

    /**
     * Get menu groups with their routes
     */
    public static function getMenuGroups()
    {
        return [
            'Dashboard' => ['dashboard'],
            'Config' => ['users.index', 'groups.index', 'menus.index', 'release.index'],
            'Master Data' => ['kapals.index', 'upts.index'],
            'Monitoring BBM' => [
                'ba-sebelum-pengisian.index',
                'ba-sebelum-pelayaran.index',
                'ba-sesudah-pelayaran.index',
                'ba-pemeriksaan-sarana-pengisian.index',
                'ba-penggunaan-bbm.index',
                'ba-akhir-bulan.index',
                'ba-penerimaan-bbm.index',
                'ba-penitipan-bbm.index',
                'ba-pengembalian-bbm.index'
            ],
            'Monitoring PINJAMAN' => [
                'ba-peminjaman-bbm.index',
                'ba-penerimaan-pinjaman-bbm.index',
                'ba-pengembalian-pinjaman-bbm.index',
                'ba-penerimaan-pengembalian-pinjaman-bbm.index'
            ],
            'Monitoring HIBAH' => [
                'ba-pemberi-hibah-bbm-kapal-pengawas.index',
                'ba-penerima-hibah-bbm-kapal-pengawas.index',
                'ba-pemberi-hibah-bbm-dengan-instansi-lain.index',
                'ba-penerima-hibah-bbm-dengan-instansi-lain.index',
                'ba-penerimaan-hibah-bbm.index'
            ],
            'Anggaran dan Realisasi' => [
                'anggaran.entri-anggaran',
                'anggaran.perubahan-anggaran',
                'anggaran.approval-anggaran',
                'anggaran.entry-realisasi',
                'anggaran.approval-realisasi',
                'anggaran.pembatalan-realisasi',
                'anggaran.tanggal-sppd',
                'anggaran.entry-anggaran-internal',
                'anggaran.approval-anggaran-internal',
                'anggaran.pembatalan-anggaran-internal'
            ],
            'Laporan BBM' => [
                'laporan-bbm.total-penerimaan-penggunaan',
                'laporan-bbm.detail-penggunaan-penerimaan',
                'laporan-bbm.history-penerimaan-penggunaan',
                'laporan-bbm.akhir-bulan',
                'laporan-bbm.penerimaan',
                'laporan-bbm.penitipan',
                'laporan-bbm.pengembalian',
                'laporan-bbm.peminjaman',
                'laporan-bbm.pengembalian-pinjaman',
                'laporan-bbm.pinjaman-belum-dikembalikan',
                'laporan-bbm.hibah-antar-kapal-pengawas',
                'laporan-bbm.pemberi-hibah-instansi-lain',
                'laporan-bbm.penerima-hibah-instansi-lain',
                'laporan-bbm.penerimaan-hibah'
            ],
            'Laporan Anggaran' => [
                'laporan-anggaran.anggaran',
                'laporan-anggaran.riwayat-all',
                'laporan-anggaran.realisasi-periode',
                'laporan-anggaran.transaksi-realisasi-upt',
                'laporan-anggaran.perubahan-anggaran-internal',
                'laporan-anggaran.berita-acara-pembayaran',
                'laporan-anggaran.verifikasi-tagihan'
            ],
            'Portal Berita' => ['portnews.index']
        ];
    }

    /**
     * Get active menu group
     */
    public static function getActiveGroup($currentRoute = null)
    {
        if ($currentRoute === null) {
            $currentRoute = request()->route()->getName();
        }

        $groups = self::getMenuGroups();

        foreach ($groups as $groupName => $routes) {
            if (in_array($currentRoute, $routes)) {
                return $groupName;
            }
        }

        return null;
    }
}

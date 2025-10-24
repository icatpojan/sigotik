<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Web\master\UserController;
use App\Http\Controllers\Web\master\GroupController;
use App\Http\Controllers\Web\master\MenuController;
use App\Http\Controllers\Web\master\UptController;
use App\Http\Controllers\Web\master\KapalController;
use App\Http\Controllers\Web\master\ReleaseController;
use App\Http\Controllers\Web\master\PortNewsController;

use App\Http\Controllers\Web\monitoring\BbmController;
use App\Http\Controllers\Web\monitoring\BaSebelumPelayaranController;
use App\Http\Controllers\Web\monitoring\BaSesudahPelayaranController;
use App\Http\Controllers\Web\monitoring\BaSebelumPengisianController;
use App\Http\Controllers\Web\monitoring\BaPenggunaanBbmController;
use App\Http\Controllers\Web\monitoring\BaPemeriksaanSaranaPengisianController;
use App\Http\Controllers\Web\monitoring\BaAkhirBulanController;
use App\Http\Controllers\Web\monitoring\BaPenerimaanBbmController;
use App\Http\Controllers\Web\monitoring\BaPenitipanBbmController;
use App\Http\Controllers\Web\monitoring\BaPengembalianBbmController;

use App\Http\Controllers\Web\anggaran\AnggaranController;
use App\Http\Controllers\Web\anggaran\TanggalSppdController;
use App\Http\Controllers\Web\anggaran\EntryAnggaranInternalController;
use App\Http\Controllers\Web\anggaran\ApprovalAnggaranInternalController;
use App\Http\Controllers\Web\anggaran\PembatalanAnggaranInternalController;
use App\Http\Controllers\Web\anggaran\AnggaranEntryRealisasiController;

use App\Http\Controllers\Web\pinjaman\BaPeminjamanBbmController;
use App\Http\Controllers\Web\pinjaman\BaPenerimaanPinjamanBbmController;
use App\Http\Controllers\Web\pinjaman\BaPengembalianPinjamanBbmController;
use App\Http\Controllers\Web\pinjaman\BaPenerimaanPengembalianPinjamanBbmController;

use App\Http\Controllers\Web\hibah\BaPemberiHibahBbmKapalPengawasController;
use App\Http\Controllers\Web\hibah\BaPenerimaHibahBbmKapalPengawasController;
use App\Http\Controllers\Web\hibah\BaPemberiHibahBbmDenganInstansiLainController;
use App\Http\Controllers\Web\hibah\BaPenerimaHibahBbmDenganInstansiLainController;
use App\Http\Controllers\Web\hibah\BaPenerimaanHibahBbmController;

use App\Http\Controllers\Web\laporan\BbmReportController;
use App\Http\Controllers\Web\laporan\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', function () {
    return redirect('/login');
});


// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Web\master\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [App\Http\Controllers\Web\master\DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/chart', [App\Http\Controllers\Web\master\DashboardController::class, 'getChartData'])->name('dashboard.chart');
    Route::get('/dashboard/table', [App\Http\Controllers\Web\master\DashboardController::class, 'getTableData'])->name('dashboard.table');

    Route::get('/home', function () {
        return redirect('/dashboard');
    });

    // User Management Routes
    // Route::resource('users', UserController::class);
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/data', [UserController::class, 'getUsers'])->name('users.data');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Group Management Routes
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/data', [GroupController::class, 'getGroups'])->name('groups.data');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

    // Permission Management Routes
    Route::get('/groups/menus/all', [GroupController::class, 'getMenus'])->name('groups.menus');
    Route::get('/groups/{group}/permissions', [GroupController::class, 'getGroupPermissions'])->name('groups.permissions');
    Route::post('/groups/{group}/permissions', [GroupController::class, 'updatePermissions'])->name('groups.permissions.update');

    // Menu Management Routes
    Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
    Route::get('/menus/data', [MenuController::class, 'getMenus'])->name('menus.data');
    Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
    Route::get('/menus/{menu}', [MenuController::class, 'show'])->name('menus.show');
    Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
    Route::get('/menus/parent/all', [MenuController::class, 'getParentMenus'])->name('menus.parent');

    // UPT Management Routes
    Route::get('/upts', [UptController::class, 'index'])->name('upts.index');
    Route::get('/upts/data', [UptController::class, 'getUpts'])->name('upts.data');
    Route::post('/upts', [UptController::class, 'store'])->name('upts.store');
    Route::get('/upts/{upt}', [UptController::class, 'show'])->name('upts.show');
    Route::put('/upts/{upt}', [UptController::class, 'update'])->name('upts.update');
    Route::delete('/upts/{upt}', [UptController::class, 'destroy'])->name('upts.destroy');

    // Kapal Management Routes
    Route::get('/kapals', [KapalController::class, 'index'])->name('kapals.index');
    Route::get('/kapals/data', [KapalController::class, 'getKapals'])->name('kapals.data');
    Route::post('/kapals', [KapalController::class, 'store'])->name('kapals.store');
    Route::get('/kapals/{kapal}', [KapalController::class, 'show'])->name('kapals.show');
    Route::put('/kapals/{kapal}', [KapalController::class, 'update'])->name('kapals.update');
    Route::delete('/kapals/{kapal}', [KapalController::class, 'destroy'])->name('kapals.destroy');

    // Release Management Routes (Read Only)
    Route::get('/release', [ReleaseController::class, 'index'])->name('release.index');
    Route::get('/release/data', [ReleaseController::class, 'getBbmKapaltrans'])->name('release.data');
    Route::get('/release/{bbmKapaltrans}', [ReleaseController::class, 'show'])->name('release.show');
    Route::post('/release/release', [ReleaseController::class, 'release'])->name('release.release');

    // Port News Management Routes
    Route::get('/portnews', [PortNewsController::class, 'index'])->name('portnews.index');
    Route::get('/portnews/data', [PortNewsController::class, 'getPortNews'])->name('portnews.data');
    Route::post('/portnews', [PortNewsController::class, 'store'])->name('portnews.store');
    Route::get('/portnews/{portNews}', [PortNewsController::class, 'show'])->name('portnews.show');
    Route::put('/portnews/{portNews}', [PortNewsController::class, 'update'])->name('portnews.update');
    Route::delete('/portnews/{portNews}', [PortNewsController::class, 'destroy'])->name('portnews.destroy');

    // BBM Management Routes
    Route::get('/bbm', [BbmController::class, 'index'])->name('bbm.index');
    Route::get('/bbm/data', [BbmController::class, 'getBbmData'])->name('bbm.data');
    Route::post('/bbm', [BbmController::class, 'store'])->name('bbm.store');
    Route::get('/bbm/{bbm}', [BbmController::class, 'show'])->name('bbm.show');
    Route::put('/bbm/{bbm}', [BbmController::class, 'update'])->name('bbm.update');
    Route::delete('/bbm/{bbm}', [BbmController::class, 'destroy'])->name('bbm.destroy');
    Route::get('/bbm/{bbm}/pdf', [BbmController::class, 'generatePdf'])->name('bbm.pdf');
    Route::get('/bbm/status-ba/options', [BbmController::class, 'getStatusBaOptions'])->name('bbm.status-ba');
    Route::get('/bbm/status-trans/options', [BbmController::class, 'getStatusTransOptions'])->name('bbm.status-trans');

    // BA Sebelum Pelayaran Routes
    Route::get('/ba-sebelum-pelayaran', [BaSebelumPelayaranController::class, 'index'])->name('ba-sebelum-pelayaran.index');
    Route::get('/ba-sebelum-pelayaran/data', [BaSebelumPelayaranController::class, 'getData'])->name('ba-sebelum-pelayaran.data');
    Route::get('/ba-sebelum-pelayaran/kapal-data', [BaSebelumPelayaranController::class, 'getKapalData'])->name('ba-sebelum-pelayaran.kapal-data');
    Route::post('/ba-sebelum-pelayaran', [BaSebelumPelayaranController::class, 'store'])->name('ba-sebelum-pelayaran.store');
    Route::get('/ba-sebelum-pelayaran/{baSebelumPelayaran}', [BaSebelumPelayaranController::class, 'show'])->name('ba-sebelum-pelayaran.show');
    Route::put('/ba-sebelum-pelayaran/{baSebelumPelayaran}', [BaSebelumPelayaranController::class, 'update'])->name('ba-sebelum-pelayaran.update');
    Route::delete('/ba-sebelum-pelayaran/{baSebelumPelayaran}', [BaSebelumPelayaranController::class, 'destroy'])->name('ba-sebelum-pelayaran.destroy');
    Route::get('/ba-sebelum-pelayaran/{baSebelumPelayaran}/pdf', [BaSebelumPelayaranController::class, 'generatePdf'])->name('ba-sebelum-pelayaran.pdf');
    Route::post('/ba-sebelum-pelayaran/{baSebelumPelayaran}/upload', [BaSebelumPelayaranController::class, 'uploadDocument'])->name('ba-sebelum-pelayaran.upload');
    Route::get('/ba-sebelum-pelayaran/{baSebelumPelayaran}/view-document', [BaSebelumPelayaranController::class, 'viewDocument'])->name('ba-sebelum-pelayaran.view-document');
    Route::delete('/ba-sebelum-pelayaran/{baSebelumPelayaran}/delete-document', [BaSebelumPelayaranController::class, 'deleteDocument'])->name('ba-sebelum-pelayaran.delete-document');

    Route::get('/ba-sebelum-pengisian', [BaSebelumPengisianController::class, 'index'])->name('ba-sebelum-pengisian.index');
    Route::get('/ba-sebelum-pengisian/data', [BaSebelumPengisianController::class, 'getData'])->name('ba-sebelum-pengisian.data');
    Route::get('/ba-sebelum-pengisian/kapal-data', [BaSebelumPengisianController::class, 'getKapalData'])->name('ba-sebelum-pengisian.kapal-data');
    Route::post('/ba-sebelum-pengisian', [BaSebelumPengisianController::class, 'store'])->name('ba-sebelum-pengisian.store');
    Route::get('/ba-sebelum-pengisian/{baSebelumPengisian}', [BaSebelumPengisianController::class, 'show'])->name('ba-sebelum-pengisian.show');
    Route::put('/ba-sebelum-pengisian/{baSebelumPengisian}', [BaSebelumPengisianController::class, 'update'])->name('ba-sebelum-pengisian.update');
    Route::delete('/ba-sebelum-pengisian/{baSebelumPengisian}', [BaSebelumPengisianController::class, 'destroy'])->name('ba-sebelum-pengisian.destroy');
    Route::get('/ba-sebelum-pengisian/{id}/pdf', [BaSebelumPengisianController::class, 'generatePdf'])->name('ba-sebelum-pengisian.pdf');
    Route::post('/ba-sebelum-pengisian/{baSebelumPengisian}/upload', [BaSebelumPengisianController::class, 'uploadDocument'])->name('ba-sebelum-pengisian.upload');
    Route::get('/ba-sebelum-pengisian/{baSebelumPengisian}/view-document', [BaSebelumPengisianController::class, 'viewDocument'])->name('ba-sebelum-pengisian.view-document');
    Route::delete('/ba-sebelum-pengisian/{baSebelumPengisian}/delete-document', [BaSebelumPengisianController::class, 'deleteDocument'])->name('ba-sebelum-pengisian.delete-document');

    Route::get('/ba-sesudah-pelayaran', [BaSesudahPelayaranController::class, 'index'])->name('ba-sesudah-pelayaran.index');
    Route::get('/ba-sesudah-pelayaran/data', [BaSesudahPelayaranController::class, 'getData'])->name('ba-sesudah-pelayaran.data');
    Route::get('/ba-sesudah-pelayaran/kapal-data', [BaSesudahPelayaranController::class, 'getKapalData'])->name('ba-sesudah-pelayaran.kapal-data');
    Route::post('/ba-sesudah-pelayaran', [BaSesudahPelayaranController::class, 'store'])->name('ba-sesudah-pelayaran.store');
    Route::get('/ba-sesudah-pelayaran/{baSesudahPelayaran}', [BaSesudahPelayaranController::class, 'show'])->name('ba-sesudah-pelayaran.show');
    Route::put('/ba-sesudah-pelayaran/{baSesudahPelayaran}', [BaSesudahPelayaranController::class, 'update'])->name('ba-sesudah-pelayaran.update');
    Route::delete('/ba-sesudah-pelayaran/{baSesudahPelayaran}', [BaSesudahPelayaranController::class, 'destroy'])->name('ba-sesudah-pelayaran.destroy');
    Route::get('/ba-sesudah-pelayaran/{baSesudahPelayaran}/pdf', [BaSesudahPelayaranController::class, 'generatePdf'])->name('ba-sesudah-pelayaran.pdf');
    Route::post('/ba-sesudah-pelayaran/{baSesudahPelayaran}/upload', [BaSesudahPelayaranController::class, 'uploadDocument'])->name('ba-sesudah-pelayaran.upload');
    Route::get('/ba-sesudah-pelayaran/{baSesudahPelayaran}/view-document', [BaSesudahPelayaranController::class, 'viewDocument'])->name('ba-sesudah-pelayaran.view-document');
    Route::delete('/ba-sesudah-pelayaran/{baSesudahPelayaran}/delete-document', [BaSesudahPelayaranController::class, 'deleteDocument'])->name('ba-sesudah-pelayaran.delete-document');

    // BA Penggunaan BBM Routes
    Route::get('/ba-penggunaan-bbm', [BaPenggunaanBbmController::class, 'index'])->name('ba-penggunaan-bbm.index');
    Route::get('/ba-penggunaan-bbm/data', [BaPenggunaanBbmController::class, 'getData'])->name('ba-penggunaan-bbm.data');
    Route::get('/ba-penggunaan-bbm/kapal-data', [BaPenggunaanBbmController::class, 'getKapalData'])->name('ba-penggunaan-bbm.kapal-data');
    Route::get('/ba-penggunaan-bbm/ba-data', [BaPenggunaanBbmController::class, 'getBaData'])->name('ba-penggunaan-bbm.ba-data');
    Route::post('/ba-penggunaan-bbm/get-data-ba', [BaPenggunaanBbmController::class, 'getDataBa'])->name('ba-penggunaan-bbm.get-data-ba');
    Route::post('/ba-penggunaan-bbm', [BaPenggunaanBbmController::class, 'store'])->name('ba-penggunaan-bbm.store');
    Route::get('/ba-penggunaan-bbm/{baPenggunaanBbm}', [BaPenggunaanBbmController::class, 'show'])->name('ba-penggunaan-bbm.show');
    Route::put('/ba-penggunaan-bbm/{baPenggunaanBbm}', [BaPenggunaanBbmController::class, 'update'])->name('ba-penggunaan-bbm.update');
    Route::delete('/ba-penggunaan-bbm/{baPenggunaanBbm}', [BaPenggunaanBbmController::class, 'destroy'])->name('ba-penggunaan-bbm.destroy');
    Route::get('/ba-penggunaan-bbm/{baPenggunaanBbm}/pdf', [BaPenggunaanBbmController::class, 'generatePdf'])->name('ba-penggunaan-bbm.pdf');
    Route::post('/ba-penggunaan-bbm/{baPenggunaanBbm}/upload', [BaPenggunaanBbmController::class, 'uploadDocument'])->name('ba-penggunaan-bbm.upload');
    Route::get('/ba-penggunaan-bbm/{baPenggunaanBbm}/view-document', [BaPenggunaanBbmController::class, 'viewDocument'])->name('ba-penggunaan-bbm.view-document');
    Route::delete('/ba-penggunaan-bbm/{baPenggunaanBbm}/delete-document', [BaPenggunaanBbmController::class, 'deleteDocument'])->name('ba-penggunaan-bbm.delete-document');

    Route::get('/ba-pemeriksaan-sarana-pengisian', [BaPemeriksaanSaranaPengisianController::class, 'index'])->name('ba-pemeriksaan-sarana-pengisian.index');
    Route::get('/ba-pemeriksaan-sarana-pengisian/data', [BaPemeriksaanSaranaPengisianController::class, 'getData'])->name('ba-pemeriksaan-sarana-pengisian.data');
    Route::get('/ba-pemeriksaan-sarana-pengisian/kapal-data', [BaPemeriksaanSaranaPengisianController::class, 'getKapalData'])->name('ba-pemeriksaan-sarana-pengisian.kapal-data');
    Route::get('/ba-pemeriksaan-sarana-pengisian/ba-data', [BaPemeriksaanSaranaPengisianController::class, 'getBaData'])->name('ba-pemeriksaan-sarana-pengisian.ba-data');
    Route::post('/ba-pemeriksaan-sarana-pengisian', [BaPemeriksaanSaranaPengisianController::class, 'store'])->name('ba-pemeriksaan-sarana-pengisian.store');
    Route::get('/ba-pemeriksaan-sarana-pengisian/{baPemeriksaaSaranaPengisian}', [BaPemeriksaanSaranaPengisianController::class, 'show'])->name('ba-pemeriksaan-sarana-pengisian.show');
    Route::put('/ba-pemeriksaan-sarana-pengisian/{baPemeriksaaSaranaPengisian}', [BaPemeriksaanSaranaPengisianController::class, 'update'])->name('ba-pemeriksaan-sarana-pengisian.update');
    Route::delete('/ba-pemeriksaan-sarana-pengisian/{baPemeriksaaSaranaPengisian}', [BaPemeriksaanSaranaPengisianController::class, 'destroy'])->name('ba-pemeriksaan-sarana-pengisian.destroy');
    Route::get('/ba-pemeriksaan-sarana-pengisian/{baPemeriksaaSaranaPengisian}/pdf', [BaPemeriksaanSaranaPengisianController::class, 'generatePdf'])->name('ba-pemeriksaan-sarana-pengisian.pdf');
    Route::post('/ba-pemeriksaan-sarana-pengisian/{baPemeriksaaSaranaPengisian}/upload', [BaPemeriksaanSaranaPengisianController::class, 'uploadDocument'])->name('ba-pemeriksaan-sarana-pengisian.upload');
    Route::get('/ba-pemeriksaan-sarana-pengisian/{baPemeriksaaSaranaPengisian}/view-document', [BaPemeriksaanSaranaPengisianController::class, 'viewDocument'])->name('ba-pemeriksaan-sarana-pengisian.view-document');
    Route::delete('/ba-pemeriksaan-sarana-pengisian/{baPemeriksaaSaranaPengisian}/delete-document', [BaPemeriksaanSaranaPengisianController::class, 'deleteDocument'])->name('ba-pemeriksaan-sarana-pengisian.delete-document');
    Route::post('/ba-pemeriksaan-sarana-pengisian/{baPemeriksaaSaranaPengisian}/delete-image', [BaPemeriksaanSaranaPengisianController::class, 'deleteImage'])->name('ba-pemeriksaan-sarana-pengisian.delete-image');

    Route::get('/ba-akhir-bulan', [BaAkhirBulanController::class, 'index'])->name('ba-akhir-bulan.index');
    Route::get('/ba-akhir-bulan/data', [BaAkhirBulanController::class, 'getData'])->name('ba-akhir-bulan.data');
    Route::get('/ba-akhir-bulan/kapal-data', [BaAkhirBulanController::class, 'getKapalData'])->name('ba-akhir-bulan.kapal-data');
    Route::get('/ba-akhir-bulan/ba-data', [BaAkhirBulanController::class, 'getBaData'])->name('ba-akhir-bulan.ba-data');
    Route::post('/ba-akhir-bulan', [BaAkhirBulanController::class, 'store'])->name('ba-akhir-bulan.store');
    Route::get('/ba-akhir-bulan/{baAkhirBulan}', [BaAkhirBulanController::class, 'show'])->name('ba-akhir-bulan.show');
    Route::put('/ba-akhir-bulan/{baAkhirBulan}', [BaAkhirBulanController::class, 'update'])->name('ba-akhir-bulan.update');
    Route::delete('/ba-akhir-bulan/{baAkhirBulan}', [BaAkhirBulanController::class, 'destroy'])->name('ba-akhir-bulan.destroy');
    Route::get('/ba-akhir-bulan/{id}/pdf', [BaAkhirBulanController::class, 'generatePdf'])->name('ba-akhir-bulan.pdf');
    Route::post('/ba-akhir-bulan/{id}/upload', [BaAkhirBulanController::class, 'uploadDocument'])->name('ba-akhir-bulan.upload');
    Route::get('/ba-akhir-bulan/{id}/view-document', [BaAkhirBulanController::class, 'viewDocument'])->name('ba-akhir-bulan.view-document');
    Route::delete('/ba-akhir-bulan/{id}/delete-document', [BaAkhirBulanController::class, 'deleteDocument'])->name('ba-akhir-bulan.delete-document');

    Route::get('/ba-penerimaan-bbm', [BaPenerimaanBbmController::class, 'index'])->name('ba-penerimaan-bbm.index');
    Route::get('/ba-penerimaan-bbm/data', [BaPenerimaanBbmController::class, 'getData'])->name('ba-penerimaan-bbm.data');
    Route::get('/ba-penerimaan-bbm/kapal-data', [BaPenerimaanBbmController::class, 'getKapalData'])->name('ba-penerimaan-bbm.kapal-data');
    Route::get('/ba-penerimaan-bbm/ba-data', [BaPenerimaanBbmController::class, 'getBaData'])->name('ba-penerimaan-bbm.ba-data');
    Route::post('/ba-penerimaan-bbm/fix-volume', [BaPenerimaanBbmController::class, 'fixVolumePengisian'])->name('ba-penerimaan-bbm.fix-volume');
    Route::post('/ba-penerimaan-bbm', [BaPenerimaanBbmController::class, 'store'])->name('ba-penerimaan-bbm.store');
    Route::get('/ba-penerimaan-bbm/{id}', [BaPenerimaanBbmController::class, 'show'])->name('ba-penerimaan-bbm.show');
    Route::put('/ba-penerimaan-bbm/{id}', [BaPenerimaanBbmController::class, 'update'])->name('ba-penerimaan-bbm.update');
    Route::delete('/ba-penerimaan-bbm/{id}', [BaPenerimaanBbmController::class, 'destroy'])->name('ba-penerimaan-bbm.destroy');
    Route::get('/ba-penerimaan-bbm/{id}/pdf', [BaPenerimaanBbmController::class, 'generatePdf'])->name('ba-penerimaan-bbm.pdf');
    Route::post('/ba-penerimaan-bbm/{id}/upload', [BaPenerimaanBbmController::class, 'uploadDocument'])->name('ba-penerimaan-bbm.upload');
    Route::get('/ba-penerimaan-bbm/{id}/view-document', [BaPenerimaanBbmController::class, 'viewDocument'])->name('ba-penerimaan-bbm.view-document');
    Route::delete('/ba-penerimaan-bbm/{id}/delete-document', [BaPenerimaanBbmController::class, 'deleteDocument'])->name('ba-penerimaan-bbm.delete-document');

    Route::get('/ba-penitipan-bbm', [BaPenitipanBbmController::class, 'index'])->name('ba-penitipan-bbm.index');
    Route::get('/ba-penitipan-bbm/data', [BaPenitipanBbmController::class, 'getData'])->name('ba-penitipan-bbm.data');
    Route::get('/ba-penitipan-bbm/kapal-data', [BaPenitipanBbmController::class, 'getKapalData'])->name('ba-penitipan-bbm.kapal-data');
    Route::get('/ba-penitipan-bbm/ba-data', [BaPenitipanBbmController::class, 'getBaData'])->name('ba-penitipan-bbm.ba-data');
    Route::post('/ba-penitipan-bbm', [BaPenitipanBbmController::class, 'store'])->name('ba-penitipan-bbm.store');
    Route::get('/ba-penitipan-bbm/{id}', [BaPenitipanBbmController::class, 'show'])->name('ba-penitipan-bbm.show');
    Route::put('/ba-penitipan-bbm/{id}', [BaPenitipanBbmController::class, 'update'])->name('ba-penitipan-bbm.update');
    Route::delete('/ba-penitipan-bbm/{id}', [BaPenitipanBbmController::class, 'destroy'])->name('ba-penitipan-bbm.destroy');
    Route::get('/ba-penitipan-bbm/{id}/pdf', [BaPenitipanBbmController::class, 'generatePdf'])->name('ba-penitipan-bbm.pdf');
    Route::post('/ba-penitipan-bbm/{id}/upload', [BaPenitipanBbmController::class, 'uploadDocument'])->name('ba-penitipan-bbm.upload');
    Route::get('/ba-penitipan-bbm/{id}/view-document', [BaPenitipanBbmController::class, 'viewDocument'])->name('ba-penitipan-bbm.view-document');
    Route::delete('/ba-penitipan-bbm/{id}/delete-document', [BaPenitipanBbmController::class, 'deleteDocument'])->name('ba-penitipan-bbm.delete-document');

    Route::get('/ba-pengembalian-bbm', [BaPengembalianBbmController::class, 'index'])->name('ba-pengembalian-bbm.index');
    Route::get('/ba-pengembalian-bbm/data', [BaPengembalianBbmController::class, 'getData'])->name('ba-pengembalian-bbm.data');
    Route::get('/ba-pengembalian-bbm/kapal-data', [BaPengembalianBbmController::class, 'getKapalData'])->name('ba-pengembalian-bbm.kapal-data');
    Route::get('/ba-pengembalian-bbm/ba-data', [BaPengembalianBbmController::class, 'getBaData'])->name('ba-pengembalian-bbm.ba-data');
    Route::post('/ba-pengembalian-bbm', [BaPengembalianBbmController::class, 'store'])->name('ba-pengembalian-bbm.store');
    Route::get('/ba-pengembalian-bbm/{id}', [BaPengembalianBbmController::class, 'show'])->name('ba-pengembalian-bbm.show');
    Route::put('/ba-pengembalian-bbm/{id}', [BaPengembalianBbmController::class, 'update'])->name('ba-pengembalian-bbm.update');
    Route::delete('/ba-pengembalian-bbm/{id}', [BaPengembalianBbmController::class, 'destroy'])->name('ba-pengembalian-bbm.destroy');
    Route::get('/ba-pengembalian-bbm/{id}/pdf', [BaPengembalianBbmController::class, 'generatePdf'])->name('ba-pengembalian-bbm.pdf');
    Route::post('/ba-pengembalian-bbm/{id}/upload', [BaPengembalianBbmController::class, 'uploadDocument'])->name('ba-pengembalian-bbm.upload');
    Route::get('/ba-pengembalian-bbm/{id}/view-document', [BaPengembalianBbmController::class, 'viewDocument'])->name('ba-pengembalian-bbm.view-document');
    Route::delete('/ba-pengembalian-bbm/{id}/delete-document', [BaPengembalianBbmController::class, 'deleteDocument'])->name('ba-pengembalian-bbm.delete-document');

    Route::get('/ba-peminjaman-bbm', [BaPeminjamanBbmController::class, 'index'])->name('ba-peminjaman-bbm.index');
    Route::get('/ba-peminjaman-bbm/data', [BaPeminjamanBbmController::class, 'getData'])->name('ba-peminjaman-bbm.data');
    Route::get('/ba-peminjaman-bbm/kapal-data', [BaPeminjamanBbmController::class, 'getKapalData'])->name('ba-peminjaman-bbm.kapal-data');
    Route::get('/ba-peminjaman-bbm/ba-data', [BaPeminjamanBbmController::class, 'getBaData'])->name('ba-peminjaman-bbm.ba-data');
    Route::post('/ba-peminjaman-bbm', [BaPeminjamanBbmController::class, 'store'])->name('ba-peminjaman-bbm.store');
    Route::get('/ba-peminjaman-bbm/{baPeminjamanBbm}', [BaPeminjamanBbmController::class, 'show'])->name('ba-peminjaman-bbm.show');
    Route::put('/ba-peminjaman-bbm/{baPeminjamanBbm}', [BaPeminjamanBbmController::class, 'update'])->name('ba-peminjaman-bbm.update');
    Route::delete('/ba-peminjaman-bbm/{baPeminjamanBbm}', [BaPeminjamanBbmController::class, 'destroy'])->name('ba-peminjaman-bbm.destroy');
    Route::get('/ba-peminjaman-bbm/{baPeminjamanBbm}/pdf', [BaPeminjamanBbmController::class, 'generatePdf'])->name('ba-peminjaman-bbm.pdf');
    Route::post('/ba-peminjaman-bbm/{baPeminjamanBbm}/upload', [BaPeminjamanBbmController::class, 'uploadDocument'])->name('ba-peminjaman-bbm.upload');
    Route::get('/ba-peminjaman-bbm/{baPeminjamanBbm}/view-document', [BaPeminjamanBbmController::class, 'viewDocument'])->name('ba-peminjaman-bbm.view-document');
    Route::delete('/ba-peminjaman-bbm/{baPeminjamanBbm}/delete-document', [BaPeminjamanBbmController::class, 'deleteDocument'])->name('ba-peminjaman-bbm.delete-document');

    Route::get('/ba-penerimaan-pinjaman-bbm', [BaPenerimaanPinjamanBbmController::class, 'index'])->name('ba-penerimaan-pinjaman-bbm.index');
    Route::get('/ba-penerimaan-pinjaman-bbm/api', [BaPenerimaanPinjamanBbmController::class, 'apiIndex'])->name('ba-penerimaan-pinjaman-bbm.api');
    Route::get('/ba-penerimaan-pinjaman-bbm/data', [BaPenerimaanPinjamanBbmController::class, 'getData'])->name('ba-penerimaan-pinjaman-bbm.data');
    Route::get('/ba-penerimaan-pinjaman-bbm/kapal-data', [BaPenerimaanPinjamanBbmController::class, 'getKapalData'])->name('ba-penerimaan-pinjaman-bbm.kapal-data');
    Route::get('/ba-penerimaan-pinjaman-bbm/ba-data', [BaPenerimaanPinjamanBbmController::class, 'getBaData'])->name('ba-penerimaan-pinjaman-bbm.ba-data');
    Route::post('/ba-penerimaan-pinjaman-bbm', [BaPenerimaanPinjamanBbmController::class, 'store'])->name('ba-penerimaan-pinjaman-bbm.store');
    Route::get('/ba-penerimaan-pinjaman-bbm/{baPenerimaanPinjamanBbm}', [BaPenerimaanPinjamanBbmController::class, 'show'])->name('ba-penerimaan-pinjaman-bbm.show');
    Route::put('/ba-penerimaan-pinjaman-bbm/{baPenerimaanPinjamanBbm}', [BaPenerimaanPinjamanBbmController::class, 'update'])->name('ba-penerimaan-pinjaman-bbm.update');
    Route::delete('/ba-penerimaan-pinjaman-bbm/{baPenerimaanPinjamanBbm}', [BaPenerimaanPinjamanBbmController::class, 'destroy'])->name('ba-penerimaan-pinjaman-bbm.destroy');
    Route::get('/ba-penerimaan-pinjaman-bbm/{baPenerimaanPinjamanBbm}/pdf', [BaPenerimaanPinjamanBbmController::class, 'generatePdf'])->name('ba-penerimaan-pinjaman-bbm.pdf');
    Route::post('/ba-penerimaan-pinjaman-bbm/{baPenerimaanPinjamanBbm}/upload', [BaPenerimaanPinjamanBbmController::class, 'uploadDocument'])->name('ba-penerimaan-pinjaman-bbm.upload');
    Route::get('/ba-penerimaan-pinjaman-bbm/{baPenerimaanPinjamanBbm}/view-document', [BaPenerimaanPinjamanBbmController::class, 'viewDocument'])->name('ba-penerimaan-pinjaman-bbm.view-document');
    Route::delete('/ba-penerimaan-pinjaman-bbm/{baPenerimaanPinjamanBbm}/delete-document', [BaPenerimaanPinjamanBbmController::class, 'deleteDocument'])->name('ba-penerimaan-pinjaman-bbm.delete-document');

    Route::get('/ba-pengembalian-pinjaman-bbm', [BaPengembalianPinjamanBbmController::class, 'index'])->name('ba-pengembalian-pinjaman-bbm.index');
    Route::get('/ba-pengembalian-pinjaman-bbm/data', [BaPengembalianPinjamanBbmController::class, 'getData'])->name('ba-pengembalian-pinjaman-bbm.data');
    Route::get('/ba-pengembalian-pinjaman-bbm/kapal-data', [BaPengembalianPinjamanBbmController::class, 'getKapalData'])->name('ba-pengembalian-pinjaman-bbm.kapal-data');
    Route::get('/ba-pengembalian-pinjaman-bbm/ba-data', [BaPengembalianPinjamanBbmController::class, 'getBaData'])->name('ba-pengembalian-pinjaman-bbm.ba-data');
    Route::post('/ba-pengembalian-pinjaman-bbm', [BaPengembalianPinjamanBbmController::class, 'store'])->name('ba-pengembalian-pinjaman-bbm.store');
    Route::get('/ba-pengembalian-pinjaman-bbm/{baPengembalianPinjamanBbm}', [BaPengembalianPinjamanBbmController::class, 'show'])->name('ba-pengembalian-pinjaman-bbm.show');
    Route::put('/ba-pengembalian-pinjaman-bbm/{baPengembalianPinjamanBbm}', [BaPengembalianPinjamanBbmController::class, 'update'])->name('ba-pengembalian-pinjaman-bbm.update');
    Route::delete('/ba-pengembalian-pinjaman-bbm/{baPengembalianPinjamanBbm}', [BaPengembalianPinjamanBbmController::class, 'destroy'])->name('ba-pengembalian-pinjaman-bbm.destroy');
    Route::get('/ba-pengembalian-pinjaman-bbm/{baPengembalianPinjamanBbm}/pdf', [BaPengembalianPinjamanBbmController::class, 'generatePdf'])->name('ba-pengembalian-pinjaman-bbm.pdf');
    Route::post('/ba-pengembalian-pinjaman-bbm/{baPengembalianPinjamanBbm}/upload', [BaPengembalianPinjamanBbmController::class, 'uploadDocument'])->name('ba-pengembalian-pinjaman-bbm.upload');
    Route::get('/ba-pengembalian-pinjaman-bbm/{baPengembalianPinjamanBbm}/view-document', [BaPengembalianPinjamanBbmController::class, 'viewDocument'])->name('ba-pengembalian-pinjaman-bbm.view-document');
    Route::delete('/ba-pengembalian-pinjaman-bbm/{baPengembalianPinjamanBbm}/delete-document', [BaPengembalianPinjamanBbmController::class, 'deleteDocument'])->name('ba-pengembalian-pinjaman-bbm.delete-document');

    Route::get('/ba-penerimaan-pengembalian-pinjaman-bbm', [BaPenerimaanPengembalianPinjamanBbmController::class, 'index'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.index');
    Route::get('/ba-penerimaan-pengembalian-pinjaman-bbm/data', [BaPenerimaanPengembalianPinjamanBbmController::class, 'getData'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.data');
    Route::get('/ba-penerimaan-pengembalian-pinjaman-bbm/kapal-data', [BaPenerimaanPengembalianPinjamanBbmController::class, 'getKapalData'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.kapal-data');
    Route::get('/ba-penerimaan-pengembalian-pinjaman-bbm/ba-data', [BaPenerimaanPengembalianPinjamanBbmController::class, 'getBaData'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.ba-data');
    Route::post('/ba-penerimaan-pengembalian-pinjaman-bbm', [BaPenerimaanPengembalianPinjamanBbmController::class, 'store'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.store');
    Route::get('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalian}', [BaPenerimaanPengembalianPinjamanBbmController::class, 'show'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.show');
    Route::put('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalian}', [BaPenerimaanPengembalianPinjamanBbmController::class, 'update'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.update');
    Route::delete('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalian}', [BaPenerimaanPengembalianPinjamanBbmController::class, 'destroy'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.destroy');
    Route::get('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalian}/pdf', [BaPenerimaanPengembalianPinjamanBbmController::class, 'generatePdf'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.pdf');
    Route::post('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalian}/upload', [BaPenerimaanPengembalianPinjamanBbmController::class, 'uploadDocument'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.upload');
    Route::get('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalian}/view-document', [BaPenerimaanPengembalianPinjamanBbmController::class, 'viewDocument'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.view-document');
    Route::delete('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalian}/delete-document', [BaPenerimaanPengembalianPinjamanBbmController::class, 'deleteDocument'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.delete-document');

    // BA Pemberi Hibah BBM Kapal Pengawas Routes
    Route::get('/ba-pemberi-hibah-bbm-kapal-pengawas', [BaPemberiHibahBbmKapalPengawasController::class, 'index'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.index');
    Route::get('/ba-pemberi-hibah-bbm-kapal-pengawas/data', [BaPemberiHibahBbmKapalPengawasController::class, 'getData'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.data');
    Route::get('/ba-pemberi-hibah-bbm-kapal-pengawas/kapal-data', [BaPemberiHibahBbmKapalPengawasController::class, 'getKapalData'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.kapal-data');
    Route::get('/ba-pemberi-hibah-bbm-kapal-pengawas/kapal-penerima-data', [BaPemberiHibahBbmKapalPengawasController::class, 'getKapalPenerimaData'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.kapal-penerima-data');
    Route::get('/ba-pemberi-hibah-bbm-kapal-pengawas/volume-sounding', [BaPemberiHibahBbmKapalPengawasController::class, 'getVolumeSounding'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.volume-sounding');
    Route::get('/ba-pemberi-hibah-bbm-kapal-pengawas/persetujuan-data', [BaPemberiHibahBbmKapalPengawasController::class, 'getPersetujuanData'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.persetujuan-data');
    Route::post('/ba-pemberi-hibah-bbm-kapal-pengawas', [BaPemberiHibahBbmKapalPengawasController::class, 'store'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.store');
    Route::get('/ba-pemberi-hibah-bbm-kapal-pengawas/{baPemberiHibah}', [BaPemberiHibahBbmKapalPengawasController::class, 'show'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.show');
    Route::put('/ba-pemberi-hibah-bbm-kapal-pengawas/{baPemberiHibah}', [BaPemberiHibahBbmKapalPengawasController::class, 'update'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.update');
    Route::delete('/ba-pemberi-hibah-bbm-kapal-pengawas/{baPemberiHibah}', [BaPemberiHibahBbmKapalPengawasController::class, 'destroy'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.destroy');
    Route::get('/ba-pemberi-hibah-bbm-kapal-pengawas/{baPemberiHibah}/pdf', [BaPemberiHibahBbmKapalPengawasController::class, 'generatePdf'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.pdf');
    Route::post('/ba-pemberi-hibah-bbm-kapal-pengawas/{baPemberiHibah}/upload', [BaPemberiHibahBbmKapalPengawasController::class, 'uploadDocument'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.upload');
    Route::get('/ba-pemberi-hibah-bbm-kapal-pengawas/{baPemberiHibah}/view-document', [BaPemberiHibahBbmKapalPengawasController::class, 'viewDocument'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.view-document');
    Route::delete('/ba-pemberi-hibah-bbm-kapal-pengawas/{baPemberiHibah}/delete-document', [BaPemberiHibahBbmKapalPengawasController::class, 'deleteDocument'])->name('ba-pemberi-hibah-bbm-kapal-pengawas.delete-document');

    // BA Penerima Hibah BBM Kapal Pengawas Routes
    Route::get('/ba-penerima-hibah-bbm-kapal-pengawas', [BaPenerimaHibahBbmKapalPengawasController::class, 'index'])->name('ba-penerima-hibah-bbm-kapal-pengawas.index');
    Route::get('/ba-penerima-hibah-bbm-kapal-pengawas/data', [BaPenerimaHibahBbmKapalPengawasController::class, 'getData'])->name('ba-penerima-hibah-bbm-kapal-pengawas.data');
    Route::get('/ba-penerima-hibah-bbm-kapal-pengawas/kapal-data', [BaPenerimaHibahBbmKapalPengawasController::class, 'getKapalData'])->name('ba-penerima-hibah-bbm-kapal-pengawas.kapal-data');
    Route::get('/ba-penerima-hibah-bbm-kapal-pengawas/ba-data', [BaPenerimaHibahBbmKapalPengawasController::class, 'getBaData'])->name('ba-penerima-hibah-bbm-kapal-pengawas.ba-data');
    Route::get('/ba-penerima-hibah-bbm-kapal-pengawas/kapal-pemberi-data', [BaPenerimaHibahBbmKapalPengawasController::class, 'getKapalPemberiData'])->name('ba-penerima-hibah-bbm-kapal-pengawas.kapal-pemberi-data');
    Route::get('/ba-penerima-hibah-bbm-kapal-pengawas/persetujuan-data', [BaPenerimaHibahBbmKapalPengawasController::class, 'getPersetujuanData'])->name('ba-penerima-hibah-bbm-kapal-pengawas.persetujuan-data');
    Route::post('/ba-penerima-hibah-bbm-kapal-pengawas', [BaPenerimaHibahBbmKapalPengawasController::class, 'store'])->name('ba-penerima-hibah-bbm-kapal-pengawas.store');
    Route::get('/ba-penerima-hibah-bbm-kapal-pengawas/{baPenerimaHibah}', [BaPenerimaHibahBbmKapalPengawasController::class, 'show'])->name('ba-penerima-hibah-bbm-kapal-pengawas.show');
    Route::put('/ba-penerima-hibah-bbm-kapal-pengawas/{baPenerimaHibah}', [BaPenerimaHibahBbmKapalPengawasController::class, 'update'])->name('ba-penerima-hibah-bbm-kapal-pengawas.update');
    Route::delete('/ba-penerima-hibah-bbm-kapal-pengawas/{baPenerimaHibah}', [BaPenerimaHibahBbmKapalPengawasController::class, 'destroy'])->name('ba-penerima-hibah-bbm-kapal-pengawas.destroy');
    Route::get('/ba-penerima-hibah-bbm-kapal-pengawas/{baPenerimaHibah}/pdf', [BaPenerimaHibahBbmKapalPengawasController::class, 'generatePdf'])->name('ba-penerima-hibah-bbm-kapal-pengawas.pdf');
    Route::post('/ba-penerima-hibah-bbm-kapal-pengawas/{baPenerimaHibah}/upload', [BaPenerimaHibahBbmKapalPengawasController::class, 'uploadDocument'])->name('ba-penerima-hibah-bbm-kapal-pengawas.upload');
    Route::get('/ba-penerima-hibah-bbm-kapal-pengawas/{baPenerimaHibah}/view-document', [BaPenerimaHibahBbmKapalPengawasController::class, 'viewDocument'])->name('ba-penerima-hibah-bbm-kapal-pengawas.view-document');
    Route::delete('/ba-penerima-hibah-bbm-kapal-pengawas/{baPenerimaHibah}/delete-document', [BaPenerimaHibahBbmKapalPengawasController::class, 'deleteDocument'])->name('ba-penerima-hibah-bbm-kapal-pengawas.delete-document');
    Route::get('/ba-penerima-hibah/pemberi-options', [BaPenerimaHibahBbmKapalPengawasController::class, 'getBaPemberiOptions'])->name('ba-penerima-hibah-bbm-kapal-pengawas.ba-pemberi-options');
    Route::get('/ba-penerima-hibah/pemberi-data', [BaPenerimaHibahBbmKapalPengawasController::class, 'getBaPemberiData'])->name('ba-penerima-hibah-bbm-kapal-pengawas.ba-pemberi-data');

    // BA Pemberi Hibah BBM Dengan Instansi Lain Routes
    Route::get('/ba-pemberi-hibah-bbm-dengan-instansi-lain', [BaPemberiHibahBbmDenganInstansiLainController::class, 'index'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.index');
    Route::get('/ba-pemberi-hibah-bbm-dengan-instansi-lain/data', [BaPemberiHibahBbmDenganInstansiLainController::class, 'getData'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.data');
    Route::get('/ba-pemberi-hibah-bbm-dengan-instansi-lain/kapal-data', [BaPemberiHibahBbmDenganInstansiLainController::class, 'getKapalData'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.kapal-data');
    Route::get('/ba-pemberi-hibah-bbm-dengan-instansi-lain/persetujuan-data', [BaPemberiHibahBbmDenganInstansiLainController::class, 'getPersetujuanData'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.persetujuan-data');
    Route::get('/ba-pemberi-hibah-bbm-dengan-instansi-lain/ba-data', [BaPemberiHibahBbmDenganInstansiLainController::class, 'getBaData'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.ba-data');
    Route::post('/ba-pemberi-hibah-bbm-dengan-instansi-lain', [BaPemberiHibahBbmDenganInstansiLainController::class, 'store'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.store');
    Route::get('/ba-pemberi-hibah-bbm-dengan-instansi-lain/{baPemberiHibah}', [BaPemberiHibahBbmDenganInstansiLainController::class, 'show'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.show');
    Route::put('/ba-pemberi-hibah-bbm-dengan-instansi-lain/{baPemberiHibah}', [BaPemberiHibahBbmDenganInstansiLainController::class, 'update'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.update');
    Route::delete('/ba-pemberi-hibah-bbm-dengan-instansi-lain/{baPemberiHibah}', [BaPemberiHibahBbmDenganInstansiLainController::class, 'destroy'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.destroy');
    Route::get('/ba-pemberi-hibah-bbm-dengan-instansi-lain/{baPemberiHibah}/pdf', [BaPemberiHibahBbmDenganInstansiLainController::class, 'generatePdf'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.pdf');
    Route::post('/ba-pemberi-hibah-bbm-dengan-instansi-lain/{baPemberiHibah}/upload', [BaPemberiHibahBbmDenganInstansiLainController::class, 'uploadDocument'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.upload');
    Route::get('/ba-pemberi-hibah-bbm-dengan-instansi-lain/{baPemberiHibah}/view-document', [BaPemberiHibahBbmDenganInstansiLainController::class, 'viewDocument'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.view-document');
    Route::delete('/ba-pemberi-hibah-bbm-dengan-instansi-lain/{baPemberiHibah}/delete-document', [BaPemberiHibahBbmDenganInstansiLainController::class, 'deleteDocument'])->name('ba-pemberi-hibah-bbm-dengan-instansi-lain.delete-document');

    // BA Penerima Hibah BBM Dengan Instansi Lain Routes
    Route::get('/ba-penerima-hibah-bbm-dengan-instansi-lain', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'index'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.index');
    Route::get('/ba-penerima-hibah-bbm-dengan-instansi-lain/data', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'getData'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.data');
    Route::get('/ba-penerima-hibah-bbm-dengan-instansi-lain/kapal-data', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'getKapalData'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.kapal-data');
    Route::get('/ba-penerima-hibah-bbm-dengan-instansi-lain/ba-sebelum-pengisian-data', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'getBaSebelumPengisianData'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.ba-sebelum-pengisian-data');
    Route::get('/ba-penerima-hibah-bbm-dengan-instansi-lain/persetujuan-data', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'getPersetujuanData'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.persetujuan-data');
    Route::post('/ba-penerima-hibah-bbm-dengan-instansi-lain', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'store'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.store');
    Route::get('/ba-penerima-hibah-bbm-dengan-instansi-lain/{baPenerimaHibah}', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'show'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.show');
    Route::put('/ba-penerima-hibah-bbm-dengan-instansi-lain/{baPenerimaHibah}', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'update'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.update');
    Route::delete('/ba-penerima-hibah-bbm-dengan-instansi-lain/{baPenerimaHibah}', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'destroy'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.destroy');
    Route::get('/ba-penerima-hibah-bbm-dengan-instansi-lain/{baPenerimaHibah}/pdf', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'generatePdf'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.pdf');
    Route::post('/ba-penerima-hibah-bbm-dengan-instansi-lain/{baPenerimaHibah}/upload', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'uploadDocument'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.upload');
    Route::get('/ba-penerima-hibah-bbm-dengan-instansi-lain/{baPenerimaHibah}/view-document', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'viewDocument'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.view-document');
    Route::delete('/ba-penerima-hibah-bbm-dengan-instansi-lain/{baPenerimaHibah}/delete-document', [BaPenerimaHibahBbmDenganInstansiLainController::class, 'deleteDocument'])->name('ba-penerima-hibah-bbm-dengan-instansi-lain.delete-document');

    // BA Penerimaan Hibah BBM Routes
    Route::get('/ba-penerimaan-hibah-bbm', [BaPenerimaanHibahBbmController::class, 'index'])->name('ba-penerimaan-hibah-bbm.index');
    Route::get('/ba-penerimaan-hibah-bbm/data', [BaPenerimaanHibahBbmController::class, 'getData'])->name('ba-penerimaan-hibah-bbm.data');
    Route::get('/ba-penerimaan-hibah-bbm/kapal-data', [BaPenerimaanHibahBbmController::class, 'getKapalData'])->name('ba-penerimaan-hibah-bbm.kapal-data');
    Route::get('/ba-penerimaan-hibah-bbm/upt-data', [BaPenerimaanHibahBbmController::class, 'getUptData'])->name('ba-penerimaan-hibah-bbm.upt-data');
    Route::get('/ba-penerimaan-hibah-bbm/ba-data', [BaPenerimaanHibahBbmController::class, 'getBaData'])->name('ba-penerimaan-hibah-bbm.ba-data');
    Route::post('/ba-penerimaan-hibah-bbm', [BaPenerimaanHibahBbmController::class, 'store'])->name('ba-penerimaan-hibah-bbm.store');
    Route::get('/ba-penerimaan-hibah-bbm/{baPenerimaanHibah}', [BaPenerimaanHibahBbmController::class, 'show'])->name('ba-penerimaan-hibah-bbm.show');
    Route::put('/ba-penerimaan-hibah-bbm/{baPenerimaanHibah}', [BaPenerimaanHibahBbmController::class, 'update'])->name('ba-penerimaan-hibah-bbm.update');
    Route::delete('/ba-penerimaan-hibah-bbm/{baPenerimaanHibah}', [BaPenerimaanHibahBbmController::class, 'destroy'])->name('ba-penerimaan-hibah-bbm.destroy');
    Route::get('/ba-penerimaan-hibah-bbm/{baPenerimaanHibah}/pdf', [BaPenerimaanHibahBbmController::class, 'generatePdf'])->name('ba-penerimaan-hibah-bbm.pdf');
    Route::post('/ba-penerimaan-hibah-bbm/{baPenerimaanHibah}/upload', [BaPenerimaanHibahBbmController::class, 'uploadDocument'])->name('ba-penerimaan-hibah-bbm.upload');
    Route::get('/ba-penerimaan-hibah-bbm/{baPenerimaanHibah}/view-document', [BaPenerimaanHibahBbmController::class, 'viewDocument'])->name('ba-penerimaan-hibah-bbm.view-document');
    Route::delete('/ba-penerimaan-hibah-bbm/{baPenerimaanHibah}/delete-document', [BaPenerimaanHibahBbmController::class, 'deleteDocument'])->name('ba-penerimaan-hibah-bbm.delete-document');

    // BBM Reports Routes
    Route::prefix('laporan-bbm')->name('laporan-bbm.')->group(function () {
        // LAP Total Penerimaan & Penggunaan BBM
        Route::get('/total-penerimaan-penggunaan', [BbmReportController::class, 'totalPenerimaanPenggunaan'])->name('total-penerimaan-penggunaan');
        Route::get('/total-penerimaan-penggunaan/data', [BbmReportController::class, 'getTotalPenerimaanPenggunaanData'])->name('total-penerimaan-penggunaan.data');
        Route::get('/total-penerimaan-penggunaan/export', [BbmReportController::class, 'exportTotalPenerimaanPenggunaan'])->name('total-penerimaan-penggunaan.export');

        // LAP Detail Penggunaan & Penerimaan BBM
        Route::get('/detail-penggunaan-penerimaan', [BbmReportController::class, 'detailPenggunaanPenerimaan'])->name('detail-penggunaan-penerimaan');
        Route::get('/detail-penggunaan-penerimaan/data', [BbmReportController::class, 'getDetailPenggunaanPenerimaanData'])->name('detail-penggunaan-penerimaan.data');
        Route::get('/detail-penggunaan-penerimaan/export', [BbmReportController::class, 'exportDetailPenggunaanPenerimaan'])->name('detail-penggunaan-penerimaan.export');

        // History Penerimaan & Penggunaan BBM
        Route::get('/history-penerimaan-penggunaan', [BbmReportController::class, 'historyPenerimaanPenggunaan'])->name('history-penerimaan-penggunaan');
        Route::get('/history-penerimaan-penggunaan/data', [BbmReportController::class, 'getHistoryPenerimaanPenggunaanData'])->name('history-penerimaan-penggunaan.data');
        Route::get('/history-penerimaan-penggunaan/export', [BbmReportController::class, 'exportHistoryPenerimaanPenggunaan'])->name('history-penerimaan-penggunaan.export');

        // Laporan BBM Akhir Bulan
        Route::get('/akhir-bulan', [BbmReportController::class, 'akhirBulan'])->name('akhir-bulan');
        Route::get('/akhir-bulan/data', [BbmReportController::class, 'getAkhirBulanData'])->name('akhir-bulan.data');
        Route::get('/akhir-bulan/export', [BbmReportController::class, 'exportAkhirBulan'])->name('akhir-bulan.export');

        // Laporan Penerimaan BBM
        Route::get('/penerimaan', [BbmReportController::class, 'penerimaan'])->name('penerimaan');
        Route::get('/penerimaan/data', [BbmReportController::class, 'getPenerimaanData'])->name('penerimaan.data');
        Route::get('/penerimaan/export', [BbmReportController::class, 'exportPenerimaan'])->name('penerimaan.export');

        // Laporan Penitipan BBM
        Route::get('/penitipan', [BbmReportController::class, 'penitipan'])->name('penitipan');
        Route::get('/penitipan/data', [BbmReportController::class, 'getPenitipanData'])->name('penitipan.data');
        Route::get('/penitipan/export', [BbmReportController::class, 'exportPenitipan'])->name('penitipan.export');

        // Laporan Pengembalian BBM
        Route::get('/pengembalian', [BbmReportController::class, 'pengembalian'])->name('pengembalian');
        Route::get('/pengembalian/data', [BbmReportController::class, 'getPengembalianData'])->name('pengembalian.data');
        Route::get('/pengembalian/export', [BbmReportController::class, 'exportPengembalian'])->name('pengembalian.export');

        // Laporan Peminjaman
        Route::get('/peminjaman', [BbmReportController::class, 'peminjaman'])->name('peminjaman');
        Route::get('/peminjaman/data', [BbmReportController::class, 'getPeminjamanData'])->name('peminjaman.data');
        Route::get('/peminjaman/export', [BbmReportController::class, 'exportPeminjaman'])->name('peminjaman.export');

        // Laporan Pengembalian Pinjaman
        Route::get('/pengembalian-pinjaman', [BbmReportController::class, 'pengembalianPinjaman'])->name('pengembalian-pinjaman');
        Route::get('/pengembalian-pinjaman/data', [BbmReportController::class, 'getPengembalianPinjamanData'])->name('pengembalian-pinjaman.data');
        Route::get('/pengembalian-pinjaman/export', [BbmReportController::class, 'exportPengembalianPinjaman'])->name('pengembalian-pinjaman.export');

        // Laporan Pinjaman Belum dikembalikan
        Route::get('/pinjaman-belum-dikembalikan', [BbmReportController::class, 'pinjamanBelumDikembalikan'])->name('pinjaman-belum-dikembalikan');
        Route::get('/pinjaman-belum-dikembalikan/data', [BbmReportController::class, 'getPinjamanBelumDikembalikanData'])->name('pinjaman-belum-dikembalikan.data');
        Route::get('/pinjaman-belum-dikembalikan/export', [BbmReportController::class, 'exportPinjamanBelumDikembalikan'])->name('pinjaman-belum-dikembalikan.export');

        // Laporan Hibah Antar Kapal Pengawas
        Route::get('/hibah-antar-kapal-pengawas', [BbmReportController::class, 'hibahAntarKapalPengawas'])->name('hibah-antar-kapal-pengawas');
        Route::get('/hibah-antar-kapal-pengawas/data', [BbmReportController::class, 'getHibahAntarKapalPengawasData'])->name('hibah-antar-kapal-pengawas.data');
        Route::get('/hibah-antar-kapal-pengawas/export', [BbmReportController::class, 'exportHibahAntarKapalPengawas'])->name('hibah-antar-kapal-pengawas.export');

        // Laporan Pemberi Hibah BBM Instansi Lain
        Route::get('/pemberi-hibah-instansi-lain', [BbmReportController::class, 'pemberiHibahInstansiLain'])->name('pemberi-hibah-instansi-lain');
        Route::get('/pemberi-hibah-instansi-lain/data', [BbmReportController::class, 'getPemberiHibahInstansiLainData'])->name('pemberi-hibah-instansi-lain.data');
        Route::get('/pemberi-hibah-instansi-lain/export', [BbmReportController::class, 'exportPemberiHibahInstansiLain'])->name('pemberi-hibah-instansi-lain.export');

        // Laporan Penerima Hibah BBM Instansi Lain
        Route::get('/penerima-hibah-instansi-lain', [BbmReportController::class, 'penerimaHibahInstansiLain'])->name('penerima-hibah-instansi-lain');
        Route::get('/penerima-hibah-instansi-lain/data', [BbmReportController::class, 'getPenerimaHibahInstansiLainData'])->name('penerima-hibah-instansi-lain.data');
        Route::get('/penerima-hibah-instansi-lain/export', [BbmReportController::class, 'exportPenerimaHibahInstansiLain'])->name('penerima-hibah-instansi-lain.export');

        // Laporan Penerimaan Hibah BBM
        Route::get('/penerimaan-hibah', [BbmReportController::class, 'penerimaanHibah'])->name('penerimaan-hibah');
        Route::get('/penerimaan-hibah/data', [BbmReportController::class, 'getPenerimaanHibahData'])->name('penerimaan-hibah.data');
        Route::get('/penerimaan-hibah/export', [BbmReportController::class, 'exportPenerimaanHibah'])->name('penerimaan-hibah.export');

        // Helper routes for dropdown data
        Route::get('/upt-options', [BbmReportController::class, 'getUptOptions'])->name('upt-options');
        Route::get('/kapal-options', [BbmReportController::class, 'getKapalOptions'])->name('kapal-options');
    });

    // ==================== ANGGARAN ROUTES ====================
    Route::prefix('anggaran')->name('anggaran.')->middleware('role')->group(function () {
        // Entri Anggaran
        Route::get('/entri-anggaran', [AnggaranController::class, 'entriAnggaran'])->name('entri-anggaran');
        Route::get('/entri-anggaran/data', [AnggaranController::class, 'getEntriAnggaranData'])->name('entri-anggaran.data');
        Route::post('/entri-anggaran/create', [AnggaranController::class, 'createEntriAnggaran'])->name('entri-anggaran.create');
        Route::get('/entri-anggaran/view/{periode}/{perubahanKe}', [AnggaranController::class, 'viewEntriAnggaran'])->name('entri-anggaran.view');
        Route::get('/entri-anggaran/edit/{periode}/{perubahanKe}', [AnggaranController::class, 'editEntriAnggaran'])->name('entri-anggaran.edit');
        Route::post('/entri-anggaran/update', [AnggaranController::class, 'updateEntriAnggaran'])->name('entri-anggaran.update');
        Route::delete('/entri-anggaran/delete/{periode}/{perubahanKe}', [AnggaranController::class, 'deleteEntriAnggaran'])->name('entri-anggaran.delete');

        // Perubahan Anggaran
        Route::get('/perubahan-anggaran', [AnggaranController::class, 'perubahanAnggaran'])->name('perubahan-anggaran');
        Route::get('/perubahan-anggaran/data', [AnggaranController::class, 'getPerubahanAnggaranData'])->name('perubahan-anggaran.data');
        Route::get('/perubahan-anggaran/view/{periode}/{perubahanKe}', [AnggaranController::class, 'viewPerubahanAnggaran'])->name('perubahan-anggaran.view');
        Route::get('/perubahan-anggaran/edit/{periode}/{perubahanKe}', [AnggaranController::class, 'editPerubahanAnggaran'])->name('perubahan-anggaran.edit');
        Route::post('/perubahan-anggaran/create', [AnggaranController::class, 'createPerubahanAnggaran'])->name('perubahan-anggaran.create');
        Route::post('/perubahan-anggaran/update', [AnggaranController::class, 'updatePerubahanAnggaran'])->name('perubahan-anggaran.update');
        Route::post('/perubahan-anggaran/upload/{periode}/{perubahanKe}', [AnggaranController::class, 'uploadPerubahanAnggaran'])->name('perubahan-anggaran.upload');
        Route::delete('/perubahan-anggaran/delete/{periode}/{perubahanKe}', [AnggaranController::class, 'deletePerubahanAnggaran'])->name('perubahan-anggaran.delete');

        // Approval Anggaran
        Route::get('/approval-anggaran', [AnggaranController::class, 'approvalAnggaran'])->name('approval-anggaran');
        Route::get('/approval-anggaran/data', [AnggaranController::class, 'getApprovalAnggaranData'])->name('approval-anggaran.data');
        Route::get('/approval-anggaran/view/{periode}', [AnggaranController::class, 'viewApprovalAnggaran'])->name('approval-anggaran.view');
        Route::post('/approval-anggaran/approve', [AnggaranController::class, 'approveAnggaran'])->name('approval-anggaran.approve');

        // Entry Realisasi
        Route::get('/entry-realisasi', [AnggaranController::class, 'entryRealisasi'])->name('entry-realisasi');
        Route::get('/entry-realisasi/data', [AnggaranController::class, 'getEntryRealisasiData'])->name('entry-realisasi.data');
        Route::get('/entry-realisasi/form/{id?}', [AnggaranController::class, 'getFormEntryRealisasi'])->name('entry-realisasi.form');
        Route::get('/entry-realisasi/view/{id}', [AnggaranController::class, 'getViewApprovalRealisasi'])->name('entry-realisasi.view');
        Route::get('/entry-realisasi/edit/{id}', [AnggaranController::class, 'getFormEntryRealisasi'])->name('entry-realisasi.edit');
        Route::post('/entry-realisasi/create', [AnggaranController::class, 'createEntryRealisasi'])->name('entry-realisasi.create');
        Route::post('/entry-realisasi/update', [AnggaranController::class, 'updateEntryRealisasi'])->name('entry-realisasi.update');
        Route::delete('/entry-realisasi/delete/{id}', [AnggaranController::class, 'deleteEntryRealisasi'])->name('entry-realisasi.delete');

        // Additional routes for tagihan BBM functionality
        Route::get('/upt-info', [AnggaranController::class, 'getUptInfo'])->name('upt-info');
        Route::get('/get-so-data/{multino}', [AnggaranController::class, 'getSoData'])->name('get-so-data');
        Route::get('/generate-nomor-tagihan', [AnggaranController::class, 'generateNomorTagihan'])->name('generate-nomor-tagihan');

        // Approval Realisasi
        Route::get('/approval-realisasi', [AnggaranController::class, 'approvalRealisasi'])->name('approval-realisasi');
        Route::get('/approval-realisasi/data', [AnggaranController::class, 'getApprovalRealisasiData'])->name('approval-realisasi.data');
        Route::get('/approval-realisasi/view/{id}', [AnggaranController::class, 'getViewApprovalRealisasi'])->name('approval-realisasi.view');

        // Tanggal SPPD
        Route::get('/tanggal-sppd', [TanggalSppdController::class, 'index'])->name('tanggal-sppd');
        Route::get('/tanggal-sppd/data', [TanggalSppdController::class, 'getData'])->name('tanggal-sppd.data');
        Route::get('/tanggal-sppd/view/{id}', [TanggalSppdController::class, 'viewTagihan'])->name('tanggal-sppd.view');
        Route::get('/tanggal-sppd/form-input/{id}', [TanggalSppdController::class, 'getFormInputTanggal'])->name('tanggal-sppd.form-input');
        Route::get('/tanggal-sppd/form-upload/{id}', [TanggalSppdController::class, 'getUploadForm'])->name('tanggal-sppd.form-upload');
        Route::post('/tanggal-sppd/update-tanggal', [TanggalSppdController::class, 'updateTanggalSppd'])->name('tanggal-sppd.update-tanggal');
        Route::post('/tanggal-sppd/upload-file', [TanggalSppdController::class, 'uploadFile'])->name('tanggal-sppd.upload-file');
        Route::get('/tanggal-sppd/download/{filename}', [TanggalSppdController::class, 'downloadFile'])->name('tanggal-sppd.download-file');
        Route::post('/tanggal-sppd/cancel', [TanggalSppdController::class, 'cancelTagihan'])->name('tanggal-sppd.cancel');
        Route::post('/approval-realisasi/approve', [AnggaranController::class, 'approveRealisasi'])->name('approval-realisasi.approve');

        // Pembatalan Realisasi
        Route::get('/pembatalan-realisasi', [AnggaranController::class, 'pembatalanRealisasi'])->name('pembatalan-realisasi');
        Route::get('/pembatalan-realisasi/data', [AnggaranController::class, 'getPembatalanRealisasiData'])->name('pembatalan-realisasi.data');
        Route::get('/pembatalan-realisasi/view/{id}', [AnggaranController::class, 'getViewPembatalanRealisasi'])->name('pembatalan-realisasi.view');
        Route::post('/pembatalan-realisasi/cancel', [AnggaranController::class, 'cancelRealisasi'])->name('pembatalan-realisasi.cancel');


        // Entry Anggaran Internal
        Route::get('/entry-anggaran-internal', [EntryAnggaranInternalController::class, 'index'])->name('entry-anggaran-internal');
        Route::get('/entry-anggaran-internal/data', [EntryAnggaranInternalController::class, 'getData'])->name('entry-anggaran-internal.data');
        Route::get('/entry-anggaran-internal/form', [EntryAnggaranInternalController::class, 'getFormData'])->name('entry-anggaran-internal.form');
        Route::get('/entry-anggaran-internal/view/{id}', [EntryAnggaranInternalController::class, 'getViewData'])->name('entry-anggaran-internal.view');
        Route::get('/entry-anggaran-internal/edit/{id}', [EntryAnggaranInternalController::class, 'getEditForm'])->name('entry-anggaran-internal.edit');
        Route::post('/entry-anggaran-internal/create', [EntryAnggaranInternalController::class, 'create'])->name('entry-anggaran-internal.create');
        Route::post('/entry-anggaran-internal/update', [EntryAnggaranInternalController::class, 'update'])->name('entry-anggaran-internal.update');
        Route::delete('/entry-anggaran-internal/delete/{id}', [EntryAnggaranInternalController::class, 'delete'])->name('entry-anggaran-internal.delete');

        // Approval Anggaran Internal
        Route::get('/approval-anggaran-internal', [ApprovalAnggaranInternalController::class, 'index'])->name('approval-anggaran-internal');
        Route::get('/approval-anggaran-internal/data', [ApprovalAnggaranInternalController::class, 'getData'])->name('approval-anggaran-internal.data');
        Route::get('/approval-anggaran-internal/view/{id}', [ApprovalAnggaranInternalController::class, 'getViewData'])->name('approval-anggaran-internal.view');
        Route::post('/approval-anggaran-internal/approve', [ApprovalAnggaranInternalController::class, 'approve'])->name('approval-anggaran-internal.approve');

        // Pembatalan Anggaran Internal
        Route::get('/pembatalan-anggaran-internal', [PembatalanAnggaranInternalController::class, 'index'])->name('pembatalan-anggaran-internal');
        Route::get('/pembatalan-anggaran-internal/data', [PembatalanAnggaranInternalController::class, 'getData'])->name('pembatalan-anggaran-internal.data');
        Route::get('/pembatalan-anggaran-internal/view/{id}', [PembatalanAnggaranInternalController::class, 'getViewData'])->name('pembatalan-anggaran-internal.view');
        Route::post('/pembatalan-anggaran-internal/cancel', [PembatalanAnggaranInternalController::class, 'cancel'])->name('pembatalan-anggaran-internal.cancel');

        // Helper routes for anggaran
        Route::get('/upt-options', [AnggaranController::class, 'getUptOptions'])->name('upt-options');
        Route::get('/anggaran-data', [AnggaranController::class, 'getAnggaranData'])->name('anggaran-data');
        Route::get('/data-anggaran', [AnggaranController::class, 'getDataAnggaran'])->name('data-anggaran');
        Route::get('/data-anggaran2/{id}/{tahun}', [AnggaranController::class, 'getDataAnggaran2'])->name('data-anggaran2');
        Route::get('/nominal-awal', [AnggaranController::class, 'getNominalAwal'])->name('nominal-awal');
        Route::post('/sync-approved-realisasi', [AnggaranController::class, 'syncAllApprovedRealisasi'])->name('sync-approved-realisasi');
    });

    // ==================== LAPORAN ANGGARAN ROUTES ====================
    Route::prefix('laporan-anggaran')->name('laporan-anggaran.')->group(function () {
        // Laporan Anggaran
        Route::get('/anggaran', [LaporanController::class, 'anggaran'])->name('anggaran');
        Route::get('/anggaran/data', [LaporanController::class, 'getAnggaranData'])->name('anggaran.data');
        Route::get('/anggaran/periodes', [LaporanController::class, 'getPeriodeOptions'])->name('anggaran.periodes');

        // Riwayat Anggaran & Realisasi ALL
        Route::get('/riwayat-all', [LaporanController::class, 'riwayatAll'])->name('riwayat-all');
        Route::get('/riwayat-all/data', [LaporanController::class, 'getRiwayatAllData'])->name('riwayat-all.data');
        Route::match(['GET', 'POST'], '/riwayat-all/export', [LaporanController::class, 'export'])->name('riwayat-all.export');

        // Laporan Realisasi per Periode
        Route::get('/realisasi-periode', [LaporanController::class, 'realisasiPeriode'])->name('realisasi-periode');
        Route::get('/realisasi-periode/data', [LaporanController::class, 'getRealisasiPeriodeData'])->name('realisasi-periode.data');
        Route::get('/realisasi-periode/export', [LaporanController::class, 'exportRealisasiPeriode'])->name('realisasi-periode.export');
        Route::get('/realisasi-periode/upts', [LaporanController::class, 'getUptOptions'])->name('realisasi-periode.upts');

        // Laporan Transaksi Realisasi UPT
        Route::get('/transaksi-realisasi-upt', [LaporanController::class, 'transaksiRealisasiUpt'])->name('transaksi-realisasi-upt');
        Route::get('/transaksi-realisasi-upt/data', [LaporanController::class, 'getTransaksiRealisasiUptData'])->name('transaksi-realisasi-upt.data');
        Route::get('/transaksi-realisasi-upt/export', [LaporanController::class, 'exportTransaksiRealisasiUpt'])->name('transaksi-realisasi-upt.export');
        Route::get('/transaksi-realisasi-upt/upts', [LaporanController::class, 'getUptOptions'])->name('transaksi-realisasi-upt.upts');
        Route::get('/transaksi-realisasi-upt/no-tagihan', [LaporanController::class, 'getNoTagihanOptions'])->name('transaksi-realisasi-upt.no-tagihan');

        // Laporan Transaksi Perubahan Anggaran Internal UPT
        Route::get('/perubahan-anggaran-internal', [LaporanController::class, 'perubahanAnggaranInternal'])->name('perubahan-anggaran-internal');
        Route::get('/perubahan-anggaran-internal/data', [LaporanController::class, 'getPerubahanAnggaranInternalData'])->name('perubahan-anggaran-internal.data');
        Route::get('/perubahan-anggaran-internal/export', [LaporanController::class, 'exportPerubahanAnggaranInternal'])->name('perubahan-anggaran-internal.export');
        Route::get('/perubahan-anggaran-internal/upts', [LaporanController::class, 'getUptOptions'])->name('perubahan-anggaran-internal.upts');

        // Laporan Berita Acara Pembayaran Tagihan
        Route::get('/berita-acara-pembayaran', [LaporanController::class, 'beritaAcaraPembayaran'])->name('berita-acara-pembayaran');
        Route::get('/berita-acara-pembayaran/data', [LaporanController::class, 'getBeritaAcaraPembayaranData'])->name('berita-acara-pembayaran.data');
        Route::get('/berita-acara-pembayaran/export', [LaporanController::class, 'exportBeritaAcaraPembayaran'])->name('berita-acara-pembayaran.export');
        Route::get('/berita-acara-pembayaran/upts', [LaporanController::class, 'getUptOptions'])->name('berita-acara-pembayaran.upts');

        // Laporan Verifikasi Tagihan routes
        Route::get('/verifikasi-tagihan', [LaporanController::class, 'verifikasiTagihan'])->name('verifikasi-tagihan');
        Route::get('/verifikasi-tagihan/data', [LaporanController::class, 'getVerifikasiTagihanData'])->name('verifikasi-tagihan.data');
        Route::get('/verifikasi-tagihan/export', [LaporanController::class, 'exportVerifikasiTagihan'])->name('verifikasi-tagihan.export');
        Route::get('/verifikasi-tagihan/upts', [LaporanController::class, 'getUptOptions'])->name('verifikasi-tagihan.upts');
        Route::get('/verifikasi-tagihan/no-tagihan', [LaporanController::class, 'getNoTagihanOptions'])->name('verifikasi-tagihan.no-tagihan');

        // Export routes
        Route::post('/export/excel/{type}', [LaporanController::class, 'exportExcel'])->name('export.excel');
        Route::post('/export/pdf/{type}', [LaporanController::class, 'exportPdf'])->name('export.pdf');
    });
});

// Swagger Documentation Routes
Route::get('/api/documentation', function () {
    return view('swagger.index');
})->name('swagger.docs');

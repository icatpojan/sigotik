<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UptController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\PortNewsController;
use App\Http\Controllers\BbmController;
use App\Http\Controllers\BaSebelumPelayaranController;
use App\Http\Controllers\BaSesudahPelayaranController;
use App\Http\Controllers\BaSebelumPengisianController;
use App\Http\Controllers\BaPenggunaanBbmController;
use App\Http\Controllers\BaPemeriksaanSaranaPengisianController;
use App\Http\Controllers\BaAkhirBulanController;
use App\Http\Controllers\BaPenerimaanBbmController;
use App\Http\Controllers\BaPenitipanBbmController;
use App\Http\Controllers\BaPengembalianBbmController;

use App\Http\Controllers\BaPeminjamanBbmController;
use App\Http\Controllers\BaPenerimaanPinjamanBbmController;
use App\Http\Controllers\BaPengembalianPinjamanBbmController;
use App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController;

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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
    Route::get('/ba-sebelum-pengisian/{baSebelumPengisian}/pdf', [BaSebelumPengisianController::class, 'generatePdf'])->name('ba-sebelum-pengisian.pdf');
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
    Route::get('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalianPinjamanBbm}', [BaPenerimaanPengembalianPinjamanBbmController::class, 'show'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.show');
    Route::put('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalianPinjamanBbm}', [BaPenerimaanPengembalianPinjamanBbmController::class, 'update'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.update');
    Route::delete('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalianPinjamanBbm}', [BaPenerimaanPengembalianPinjamanBbmController::class, 'destroy'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.destroy');
    Route::get('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalianPinjamanBbm}/pdf', [BaPenerimaanPengembalianPinjamanBbmController::class, 'generatePdf'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.pdf');
    Route::post('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalianPinjamanBbm}/upload', [BaPenerimaanPengembalianPinjamanBbmController::class, 'uploadDocument'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.upload');
    Route::get('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalianPinjamanBbm}/view-document', [BaPenerimaanPengembalianPinjamanBbmController::class, 'viewDocument'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.view-document');
    Route::delete('/ba-penerimaan-pengembalian-pinjaman-bbm/{baPenerimaanPengembalianPinjamanBbm}/delete-document', [BaPenerimaanPengembalianPinjamanBbmController::class, 'deleteDocument'])->name('ba-penerimaan-pengembalian-pinjaman-bbm.delete-document');
});

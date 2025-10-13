<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BbmController as ApiBbmController;
use App\Http\Controllers\Api\KapalController as ApiKapalController;
use App\Http\Controllers\Api\UptController as ApiUptController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PortNewsController;
use App\Http\Controllers\SwaggerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {

    // Authentication endpoints
    Route::post('/auth/login', [AuthController::class, 'apiLogin']);
    Route::post('/auth/refresh', [AuthController::class, 'apiRefresh']);

    // Public data endpoints
    Route::get('/public/status-ba-options', [ApiBbmController::class, 'getStatusBaOptions']);
    Route::get('/public/status-trans-options', [ApiBbmController::class, 'getStatusTransOptions']);
    Route::get('/public/upts', [ApiUptController::class, 'index']);
    Route::get('/public/kapals', [ApiKapalController::class, 'index']);
});

// Protected API routes (authentication required)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'apiLogout']);

    // User profile
    Route::get('/user/profile', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    });

    // User Management
    Route::apiResource('users', UserController::class);
    Route::get('/users/{user}/permissions', [UserController::class, 'getPermissions']);
    Route::post('/users/{user}/permissions', [UserController::class, 'updatePermissions']);

    // Group Management
    Route::apiResource('groups', GroupController::class);
    Route::get('/groups/{group}/permissions', [GroupController::class, 'getGroupPermissions']);
    Route::post('/groups/{group}/permissions', [GroupController::class, 'updatePermissions']);

    // Menu Management
    Route::apiResource('menus', MenuController::class);
    Route::get('/menus/parent/all', [MenuController::class, 'getParentMenus']);

    // UPT Management
    Route::apiResource('upts', ApiUptController::class);

    // Kapal Management
    Route::apiResource('kapals', ApiKapalController::class);

    // BBM Management
    Route::apiResource('bbm', ApiBbmController::class);
    Route::get('/bbm/{bbm}/pdf', [ApiBbmController::class, 'generatePdf']);
    Route::post('/bbm/{bbm}/upload', [ApiBbmController::class, 'uploadDocument']);

    // BA Sebelum Pelayaran
    Route::prefix('ba-sebelum-pelayaran')->group(function () {
        Route::get('/', [App\Http\Controllers\BaSebelumPelayaranController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaSebelumPelayaranController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaSebelumPelayaranController::class, 'getKapalData']);
        Route::post('/', [App\Http\Controllers\BaSebelumPelayaranController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaSebelumPelayaranController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaSebelumPelayaranController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaSebelumPelayaranController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaSebelumPelayaranController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaSebelumPelayaranController::class, 'uploadDocument']);
    });

    // BA Sesudah Pelayaran
    Route::prefix('ba-sesudah-pelayaran')->group(function () {
        Route::get('/', [App\Http\Controllers\BaSesudahPelayaranController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaSesudahPelayaranController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaSesudahPelayaranController::class, 'getKapalData']);
        Route::post('/', [App\Http\Controllers\BaSesudahPelayaranController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaSesudahPelayaranController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaSesudahPelayaranController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaSesudahPelayaranController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaSesudahPelayaranController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaSesudahPelayaranController::class, 'uploadDocument']);
    });

    // BA Sebelum Pengisian
    Route::prefix('ba-sebelum-pengisian')->group(function () {
        Route::get('/', [App\Http\Controllers\BaSebelumPengisianController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaSebelumPengisianController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaSebelumPengisianController::class, 'getKapalData']);
        Route::post('/', [App\Http\Controllers\BaSebelumPengisianController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaSebelumPengisianController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaSebelumPengisianController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaSebelumPengisianController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaSebelumPengisianController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaSebelumPengisianController::class, 'uploadDocument']);
    });

    // BA Penggunaan BBM
    Route::prefix('ba-penggunaan-bbm')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPenggunaanBbmController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPenggunaanBbmController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPenggunaanBbmController::class, 'getKapalData']);
        Route::get('/ba-data', [App\Http\Controllers\BaPenggunaanBbmController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaPenggunaanBbmController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPenggunaanBbmController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPenggunaanBbmController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPenggunaanBbmController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPenggunaanBbmController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPenggunaanBbmController::class, 'uploadDocument']);
    });

    // BA Pemeriksaan Sarana Pengisian
    Route::prefix('ba-pemeriksaan-sarana-pengisian')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'getKapalData']);
        Route::get('/ba-data', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'uploadDocument']);
        Route::post('/{id}/delete-image', [App\Http\Controllers\BaPemeriksaanSaranaPengisianController::class, 'deleteImage']);
    });

    // BA Akhir Bulan
    Route::prefix('ba-akhir-bulan')->group(function () {
        Route::get('/', [App\Http\Controllers\BaAkhirBulanController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaAkhirBulanController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaAkhirBulanController::class, 'getKapalData']);
        Route::get('/ba-data', [App\Http\Controllers\BaAkhirBulanController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaAkhirBulanController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaAkhirBulanController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaAkhirBulanController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaAkhirBulanController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaAkhirBulanController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaAkhirBulanController::class, 'uploadDocument']);
    });

    // BA Penerimaan BBM
    Route::prefix('ba-penerimaan-bbm')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPenerimaanBbmController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPenerimaanBbmController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPenerimaanBbmController::class, 'getKapalData']);
        Route::get('/ba-data', [App\Http\Controllers\BaPenerimaanBbmController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaPenerimaanBbmController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPenerimaanBbmController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPenerimaanBbmController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPenerimaanBbmController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPenerimaanBbmController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPenerimaanBbmController::class, 'uploadDocument']);
    });

    // BA Penitipan BBM
    Route::prefix('ba-penitipan-bbm')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPenitipanBbmController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPenitipanBbmController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPenitipanBbmController::class, 'getKapalData']);
        Route::get('/ba-data', [App\Http\Controllers\BaPenitipanBbmController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaPenitipanBbmController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPenitipanBbmController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPenitipanBbmController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPenitipanBbmController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPenitipanBbmController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPenitipanBbmController::class, 'uploadDocument']);
    });

    // BA Pengembalian BBM
    Route::prefix('ba-pengembalian-bbm')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPengembalianBbmController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPengembalianBbmController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPengembalianBbmController::class, 'getKapalData']);
        Route::get('/ba-data', [App\Http\Controllers\BaPengembalianBbmController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaPengembalianBbmController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPengembalianBbmController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPengembalianBbmController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPengembalianBbmController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPengembalianBbmController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPengembalianBbmController::class, 'uploadDocument']);
    });

    // BA Peminjaman BBM
    Route::prefix('ba-peminjaman-bbm')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPeminjamanBbmController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPeminjamanBbmController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPeminjamanBbmController::class, 'getKapalData']);
        Route::get('/ba-data', [App\Http\Controllers\BaPeminjamanBbmController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaPeminjamanBbmController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPeminjamanBbmController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPeminjamanBbmController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPeminjamanBbmController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPeminjamanBbmController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPeminjamanBbmController::class, 'uploadDocument']);
    });

    // BA Penerimaan Pinjaman BBM
    Route::prefix('ba-penerimaan-pinjaman-bbm')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPenerimaanPinjamanBbmController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPenerimaanPinjamanBbmController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPenerimaanPinjamanBbmController::class, 'getKapalData']);
        Route::get('/ba-data', [App\Http\Controllers\BaPenerimaanPinjamanBbmController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaPenerimaanPinjamanBbmController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPenerimaanPinjamanBbmController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPenerimaanPinjamanBbmController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPenerimaanPinjamanBbmController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPenerimaanPinjamanBbmController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPenerimaanPinjamanBbmController::class, 'uploadDocument']);
    });

    // BA Pengembalian Pinjaman BBM
    Route::prefix('ba-pengembalian-pinjaman-bbm')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPengembalianPinjamanBbmController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPengembalianPinjamanBbmController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPengembalianPinjamanBbmController::class, 'getKapalData']);
        Route::get('/ba-data', [App\Http\Controllers\BaPengembalianPinjamanBbmController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaPengembalianPinjamanBbmController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPengembalianPinjamanBbmController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPengembalianPinjamanBbmController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPengembalianPinjamanBbmController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPengembalianPinjamanBbmController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPengembalianPinjamanBbmController::class, 'uploadDocument']);
    });

    // BA Penerimaan Pengembalian Pinjaman BBM
    Route::prefix('ba-penerimaan-pengembalian-pinjaman-bbm')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController::class, 'getKapalData']);
        Route::get('/ba-data', [App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPenerimaanPengembalianPinjamanBbmController::class, 'uploadDocument']);
    });

    // BA Pemberi Hibah BBM Kapal Pengawas
    Route::prefix('ba-pemberi-hibah-bbm-kapal-pengawas')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'getKapalData']);
        Route::get('/kapal-penerima-data', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'getKapalPenerimaData']);
        Route::get('/persetujuan-data', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'getPersetujuanData']);
        Route::post('/', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPemberiHibahBbmKapalPengawasController::class, 'uploadDocument']);
    });

    // BA Penerima Hibah BBM Kapal Pengawas
    Route::prefix('ba-penerima-hibah-bbm-kapal-pengawas')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'getKapalData']);
        Route::get('/kapal-pemberi-data', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'getKapalPemberiData']);
        Route::get('/persetujuan-data', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'getPersetujuanData']);
        Route::get('/pemberi-options', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'getBaPemberiOptions']);
        Route::get('/pemberi-data', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'getBaPemberiData']);
        Route::post('/', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPenerimaHibahBbmKapalPengawasController::class, 'uploadDocument']);
    });

    // BA Pemberi Hibah BBM Dengan Instansi Lain
    Route::prefix('ba-pemberi-hibah-bbm-dengan-instansi-lain')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPemberiHibahBbmDenganInstansiLainController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPemberiHibahBbmDenganInstansiLainController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPemberiHibahBbmDenganInstansiLainController::class, 'getKapalData']);
        Route::get('/persetujuan-data', [App\Http\Controllers\BaPemberiHibahBbmDenganInstansiLainController::class, 'getPersetujuanData']);
        Route::post('/', [App\Http\Controllers\BaPemberiHibahBbmDenganInstansiLainController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPemberiHibahBbmDenganInstansiLainController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPemberiHibahBbmDenganInstansiLainController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPemberiHibahBbmDenganInstansiLainController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPemberiHibahBbmDenganInstansiLainController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPemberiHibahBbmDenganInstansiLainController::class, 'uploadDocument']);
    });

    // BA Penerima Hibah BBM Dengan Instansi Lain
    Route::prefix('ba-penerima-hibah-bbm-dengan-instansi-lain')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPenerimaHibahBbmDenganInstansiLainController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPenerimaHibahBbmDenganInstansiLainController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPenerimaHibahBbmDenganInstansiLainController::class, 'getKapalData']);
        Route::get('/persetujuan-data', [App\Http\Controllers\BaPenerimaHibahBbmDenganInstansiLainController::class, 'getPersetujuanData']);
        Route::post('/', [App\Http\Controllers\BaPenerimaHibahBbmDenganInstansiLainController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPenerimaHibahBbmDenganInstansiLainController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPenerimaHibahBbmDenganInstansiLainController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPenerimaHibahBbmDenganInstansiLainController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPenerimaHibahBbmDenganInstansiLainController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPenerimaHibahBbmDenganInstansiLainController::class, 'uploadDocument']);
    });

    // BA Penerimaan Hibah BBM
    Route::prefix('ba-penerimaan-hibah-bbm')->group(function () {
        Route::get('/', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'apiIndex']);
        Route::get('/data', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'getData']);
        Route::get('/kapal-data', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'getKapalData']);
        Route::get('/upt-data', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'getUptData']);
        Route::get('/ba-data', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'getBaData']);
        Route::post('/', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'destroy']);
        Route::get('/{id}/pdf', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'generatePdf']);
        Route::post('/{id}/upload', [App\Http\Controllers\BaPenerimaanHibahBbmController::class, 'uploadDocument']);
    });

    // Port News Management
    Route::apiResource('portnews', PortNewsController::class);

    // Dashboard & Statistics
    Route::get('/dashboard/stats', function (Request $request) {
        $user = $request->user();

        // Get statistics based on user role and UPT
        $stats = [
            'total_bbm_transactions' => \App\Models\BbmKapaltrans::count(),
            'total_kapals' => \App\Models\MKapal::count(),
            'total_upts' => \App\Models\MUpt::count(),
            'pending_approvals' => \App\Models\BbmKapaltrans::where('status_trans', 0)->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    });

    // Search across all modules
    Route::get('/search', function (Request $request) {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');

        $results = [];

        if ($type === 'all' || $type === 'bbm') {
            $bbmResults = \App\Models\BbmKapaltrans::with('kapal')
                ->where('nomor_surat', 'like', "%{$query}%")
                ->orWhere('lokasi_surat', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            $results['bbm'] = $bbmResults;
        }

        if ($type === 'all' || $type === 'kapal') {
            $kapalResults = \App\Models\MKapal::with('upt')
                ->where('nama_kapal', 'like', "%{$query}%")
                ->orWhere('code_kapal', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            $results['kapal'] = $kapalResults;
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    });
});

// API Information and Swagger
Route::get('/', [SwaggerController::class, 'apiInfo']);

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BbmController as ApiBbmController;
use App\Http\Controllers\Api\KapalController as ApiKapalController;
use App\Http\Controllers\Api\UptController as ApiUptController;
use App\Http\Controllers\Api\MobileDashboardController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BaSebelumPengisianController;
use App\Http\Controllers\Api\BaPemeriksaanSaranaPengisianController;
use App\Http\Controllers\Api\BaPenerimaanBbmController;
use App\Http\Controllers\Api\FotoUploadController;
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

// API Information and Swagger
Route::get('/', [SwaggerController::class, 'apiInfo']);

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Authentication endpoints
    Route::post('/auth/login', [AuthController::class, 'apiLogin']);
    Route::post('/auth/refresh', [AuthController::class, 'apiRefresh']);

    // Public data endpoints
    Route::get('/public/status-ba-options', [ApiBbmController::class, 'getStatusBaOptions']);
    Route::get('/public/status-trans-options', [ApiBbmController::class, 'getStatusTransOptions']);
    Route::get('/public/upts', [ApiUptController::class, 'index']);
});

// Protected API routes (authentication required)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'apiLogout']);

    // Dashboard & Statistics
    Route::get('/dashboard', [MobileDashboardController::class, 'getDashboardData']);
    Route::get('/dashboard/quick-stats', [MobileDashboardController::class, 'getQuickStats']);
    Route::get('/dashboard/stats', [StatsController::class, 'getStats']);

    // User Management
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);

    // Search
    Route::get('/search', [SearchController::class, 'search']);

    // UPT Management
    Route::apiResource('upts', ApiUptController::class);

    // Kapal Management
    Route::apiResource('kapals', ApiKapalController::class);

    // BBM Management
    Route::apiResource('bbm', ApiBbmController::class);
    Route::get('/bbm/{bbm}/pdf', [ApiBbmController::class, 'generatePdf']);
    Route::post('/bbm/{bbm}/upload', [ApiBbmController::class, 'uploadDocument']);

    // BA Sebelum Pengisian
    Route::prefix('ba-sebelum-pengisian')->group(function () {
        Route::get('/', [BaSebelumPengisianController::class, 'apiIndex']);
        Route::get('/data', [BaSebelumPengisianController::class, 'getData']);
        Route::get('/kapal-data', [BaSebelumPengisianController::class, 'getKapalData']);
        Route::post('/', [BaSebelumPengisianController::class, 'store']);
        Route::get('/{id}', [BaSebelumPengisianController::class, 'show']);
        Route::put('/{id}', [BaSebelumPengisianController::class, 'update']);
        Route::delete('/{id}', [BaSebelumPengisianController::class, 'destroy']);
        Route::get('/{id}/pdf', [BaSebelumPengisianController::class, 'generatePdf']);
        Route::post('/{id}/upload', [BaSebelumPengisianController::class, 'uploadDocument']);
    });

    // BA Pemeriksaan Sarana Pengisian
    Route::prefix('ba-pemeriksaan-sarana-pengisian')->group(function () {
        Route::get('/', [BaPemeriksaanSaranaPengisianController::class, 'apiIndex']);
        Route::get('/data', [BaPemeriksaanSaranaPengisianController::class, 'getData']);
        Route::get('/kapal-data', [BaPemeriksaanSaranaPengisianController::class, 'getKapalData']);
        Route::get('/ba-data', [BaPemeriksaanSaranaPengisianController::class, 'getBaData']);
        Route::post('/', [BaPemeriksaanSaranaPengisianController::class, 'store']);
        Route::get('/{id}', [BaPemeriksaanSaranaPengisianController::class, 'show']);
        Route::put('/{id}', [BaPemeriksaanSaranaPengisianController::class, 'update']);
        Route::delete('/{id}', [BaPemeriksaanSaranaPengisianController::class, 'destroy']);
        Route::get('/{id}/pdf', [BaPemeriksaanSaranaPengisianController::class, 'generatePdf']);
        Route::post('/{id}/upload', [BaPemeriksaanSaranaPengisianController::class, 'uploadDocument']);
        Route::post('/{id}/delete-image', [BaPemeriksaanSaranaPengisianController::class, 'deleteImage']);
    });

    // BA Penerimaan BBM
    Route::prefix('ba-penerimaan-bbm')->group(function () {
        Route::get('/', [BaPenerimaanBbmController::class, 'apiIndex']);
        Route::get('/data', [BaPenerimaanBbmController::class, 'getData']);
        Route::get('/kapal-data', [BaPenerimaanBbmController::class, 'getKapalData']);
        Route::get('/ba-data', [BaPenerimaanBbmController::class, 'getBaData']);
        Route::post('/', [BaPenerimaanBbmController::class, 'store']);
        Route::get('/{id}', [BaPenerimaanBbmController::class, 'show']);
        Route::put('/{id}', [BaPenerimaanBbmController::class, 'update']);
        Route::delete('/{id}', [BaPenerimaanBbmController::class, 'destroy']);
        Route::get('/{id}/pdf', [BaPenerimaanBbmController::class, 'generatePdf']);
        Route::post('/{id}/upload', [BaPenerimaanBbmController::class, 'uploadDocument']);
    });

    // Foto Upload API
    Route::prefix('foto')->group(function () {
        Route::post('/upload', [FotoUploadController::class, 'upload']);
        Route::post('/upload-multiple', [FotoUploadController::class, 'uploadMultiple']);
        Route::get('/', [FotoUploadController::class, 'index']);
        Route::get('/trans/{transId}', [FotoUploadController::class, 'getByTransId']);
        Route::get('/statistics', [FotoUploadController::class, 'getStatistics']);
        Route::post('/cleanup', [FotoUploadController::class, 'cleanup']);
        Route::get('/tipe-ba/list', [FotoUploadController::class, 'getTipeBa']);
        Route::get('/tipe-dokumen/list', [FotoUploadController::class, 'getTipeDokumen']);
        Route::get('/{id}', [FotoUploadController::class, 'show']);
        Route::delete('/{id}', [FotoUploadController::class, 'destroy']);
    });
});

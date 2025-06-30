<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RumahController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RumahPenghuniController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Image serving route (public - no authentication required)
Route::get('/images/ktp/{filename}', function ($filename) {
    $path = storage_path('app/public/penghuni/ktp/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->where('filename', '.*');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Laporan routes
    Route::controller(LaporanController::class)->prefix('laporan')->group(function () {
        Route::get('summary', 'summary');
        Route::get('grafik', 'grafik');
        Route::get('pemasukan', 'pemasukan');
        Route::get('pengeluaran', 'pengeluaran');
        Route::get('saldo', 'saldo');
    });

    // CRUD routes
    Route::get('penghuni/all', [PenghuniController::class, 'all']);
    Route::apiResource('rumah', RumahController::class);
    Route::apiResource('penghuni', PenghuniController::class);
    Route::apiResource('pembayaran', PembayaranController::class);
    Route::apiResource('pengeluaran', PengeluaranController::class);

    // RumahPenghuni routes
    Route::get('rumah-penghuni/all', [RumahPenghuniController::class, 'all']);
    Route::apiResource('rumah-penghuni', RumahPenghuniController::class)->except(['create', 'edit']);

    // Rumah history endpoint
    Route::get('/rumah/{id}/history', [RumahController::class, 'history']);
});


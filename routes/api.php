<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RumahController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\RumahPenghuniController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('rumah', RumahController::class);
Route::apiResource('penghuni', PenghuniController::class);
Route::apiResource('rumah-penghuni', RumahPenghuniController::class);
Route::apiResource('pembayaran', PembayaranController::class);
Route::apiResource('pengeluaran', PengeluaranController::class);

Route::controller(LaporanController::class)->prefix('laporan')->group(function () {
    Route::get('summary', 'summary');
    Route::get('grafik', 'grafik');
    Route::get('pemasukan', 'pemasukan');
    Route::get('pengeluaran', 'pengeluaran');
    Route::get('saldo', 'saldo');
});

Route::post('/login', [AuthController::class, 'login']);

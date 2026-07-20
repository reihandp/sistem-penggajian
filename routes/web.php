<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeSalaryController;

// ======================================================================
// 1. REDIRECT HALAMAN UTAMA
// ======================================================================

// Otomatis arahkan halaman paling depan (domain.com/) ke halaman penggajian
Route::redirect('/', '/penggajian');

// ======================================================================
// 2. ROUTE GUEST (HANYA BISA DIAKSES JIKA BELUM LOGIN)
// ======================================================================

Route::middleware('guest')->group(function () {
    // Menampilkan form login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

    // Memproses data login
    Route::post('/login', [AuthController::class, 'login']);
});

// ======================================================================
// 3. ROUTE ADMIN (HANYA BISA DIAKSES JIKA SUDAH LOGIN)
// ======================================================================

Route::middleware('auth')->group(function () {
    // ------------------------------------------------------------------
    // 3.1. Resource Route Penggajian
    // ------------------------------------------------------------------

    // Resource controller untuk pengelolaan data gaji karyawan
    // Tetap mempertahankan custom parameter 'employeeSalary'
    Route::resource('penggajian', EmployeeSalaryController::class)->parameters([
        'penggajian' => 'employeeSalary',
    ]);

    // ------------------------------------------------------------------
    // 3.2. Route Laporan
    // ------------------------------------------------------------------

    // Menampilkan laporan ringkasan data gaji karyawan
    Route::get('laporan', [EmployeeSalaryController::class, 'laporan'])->name('laporan.index');

    // ------------------------------------------------------------------
    // 3.3. Route Logout
    // ------------------------------------------------------------------

    // Memproses logout pengguna (ditangani oleh AuthController)
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeSalaryController;

// Otomatis arahkan halaman paling depan (domain.com/) ke halaman penggajian
Route::redirect('/', '/penggajian');


// =========================================================
// ROUTE GUEST (HANYA BISA DIAKSES JIKA BELUM LOGIN)
// =========================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


// =========================================================
// ROUTE ADMIN (HANYA BISA DIAKSES JIKA SUDAH LOGIN)
// =========================================================
Route::middleware('auth')->group(function () {
    
    // Route Penggajian (Tetap mempertahankan custom parameter Anda!)
    Route::resource('penggajian', EmployeeSalaryController::class)->parameters([
        'penggajian' => 'employeeSalary',
    ]);
    
    // Route Laporan
    Route::get('laporan', [EmployeeSalaryController::class, 'laporan'])->name('laporan.index');

    // Route Logout (Sekarang ditangani dengan rapi oleh AuthController)
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
});
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\EmployeeSalaryController;
// use Illuminate\Http\Request;

// // Halaman Utama bawaan Laravel
// // Route::get('/', function () {
// //     return view('welcome');
// // });

// Route::redirect('/', '/penggajian');
// Route::resource('penggajian', EmployeeSalaryController::class)->parameters([
// 	'penggajian' => 'employeeSalary',
// ]);
// Route::get('laporan', [EmployeeSalaryController::class, 'laporan'])->name('laporan.index');

// Route::post('logout', function (Request $request) {
// 	$request->session()->invalidate();
// 	$request->session()->regenerateToken();

// 	return redirect('/penggajian')->with('success', 'Berhasil logout.');
// })->name('logout');
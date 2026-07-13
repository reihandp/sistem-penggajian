<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeSalaryController;
use Illuminate\Http\Request;

// Halaman Utama bawaan Laravel
// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', '/penggajian');
Route::resource('penggajian', EmployeeSalaryController::class)->parameters([
	'penggajian' => 'employeeSalary',
]);
Route::get('laporan', [EmployeeSalaryController::class, 'laporan'])->name('laporan.index');

Route::post('logout', function (Request $request) {
	$request->session()->invalidate();
	$request->session()->regenerateToken();

	return redirect('/penggajian')->with('success', 'Berhasil logout.');
})->name('logout');
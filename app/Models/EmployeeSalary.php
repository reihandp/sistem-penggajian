<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    protected $table = 'gaji_karyawan_indonesia';

    // Mengizinkan kolom-kolom diisi secara massal
    protected $fillable = ['nama', 'pengalaman_kerja_tahun', 'usia', 'jenis_kelamin', 'gaji_per_bulan_rp'];
}

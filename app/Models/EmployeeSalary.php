<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// EmployeeSalary sebagai model untuk tabel gaji_karyawan_indonesia_updated
class EmployeeSalary extends Model
{
    protected $table = 'gaji_karyawan_indonesia_updated';
    // Mengizinkan kolom-kolom diisi secara massal
    protected $fillable = ['id', 'nama', 'pengalaman_kerja_tahun', 'usia', 'jenis_kelamin', 'gaji_per_bulan_rp', 'id_jabatan'];

    public $incrementing = true;
    protected $keyType = 'int';

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }
}

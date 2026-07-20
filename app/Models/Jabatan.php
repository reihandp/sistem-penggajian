<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel data_jabatan
 * 
 * @property int $id_jabatan
 * @property string $nama_jabatan
 * @property-read \Illuminate\Database\Eloquent\Collection|EmployeeSalary[] $employees
 */
class Jabatan extends Model
{
    // ======================================================================
    // 1. KONFIGURASI TABEL
    // ======================================================================

    /**
     * Nama tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'data_jabatan';

    /**
     * Nama primary key dari tabel.
     *
     * @var string
     */
    protected $primaryKey = 'id_jabatan';

    /**
     * Mengaktifkan auto-increment untuk primary key.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Tipe data primary key.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (mass assignment).
     *
     * @var array
     */
    protected $fillable = [
        'id_jabatan',
        'nama_jabatan'
    ];

    // ======================================================================
    // 2. RELASI ANTAR MODEL
    // ======================================================================

    /**
     * Mendefinisikan relasi hasMany ke model EmployeeSalary.
     * Satu jabatan dapat dimiliki oleh banyak karyawan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany(EmployeeSalary::class, 'id_jabatan', 'id_jabatan');
    }
}
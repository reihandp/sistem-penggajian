<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel gaji_karyawan_indonesia_updated
 * 
 * @property int $id
 * @property string $nama
 * @property int $pengalaman_kerja_tahun
 * @property int $usia
 * @property string $jenis_kelamin
 * @property int $gaji_per_bulan_rp
 * @property int|null $id_jabatan
 * @property-read Jabatan|null $jabatan
 */
class EmployeeSalary extends Model
{
    // ======================================================================
    // 1. KONFIGURASI TABEL
    // ======================================================================

    /**
     * Nama tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'gaji_karyawan_indonesia_updated';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (mass assignment).
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'nama',
        'pengalaman_kerja_tahun',
        'usia',
        'jenis_kelamin',
        'gaji_per_bulan_rp',
        'id_jabatan'
    ];

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

    // ======================================================================
    // 2. RELASI ANTAR MODEL
    // ======================================================================

    /**
     * Mendefinisikan relasi belongsTo ke model Jabatan.
     * Menghubungkan employee salary dengan data jabatan melalui foreign key id_jabatan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }
}
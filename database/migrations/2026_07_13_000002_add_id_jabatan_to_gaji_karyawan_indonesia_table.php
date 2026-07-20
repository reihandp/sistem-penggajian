<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // ======================================================================
    // 1. PROSES MIGRASI (UP)
    // ======================================================================

    /**
     * Menambahkan kolom id_jabatan dan foreign key ke tabel gaji_karyawan_indonesia.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('gaji_karyawan_indonesia', function (Blueprint $table) {
            // Tambahkan kolom id_jabatan setelah kolom gaji_per_bulan_rp
            $table->unsignedBigInteger('id_jabatan')->nullable()->after('gaji_per_bulan_rp');

            // Tambahkan foreign key constraint ke tabel data_jabatan
            $table->foreign('id_jabatan')
                  ->references('id_jabatan')
                  ->on('data_jabatan')
                  ->onDelete('set null');
        });
    }

    // ======================================================================
    // 2. PROSES ROLLBACK MIGRASI (DOWN)
    // ======================================================================

    /**
     * Membatalkan migrasi dengan menghapus foreign key dan kolom id_jabatan.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('gaji_karyawan_indonesia', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['id_jabatan']);

            // Hapus kolom id_jabatan
            $table->dropColumn('id_jabatan');
        });
    }
};
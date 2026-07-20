<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // ======================================================================
    // 1. PROSES MIGRASI (UP)
    // ======================================================================

    /**
     * Menjalankan migrasi untuk membuat tabel gaji_karyawan_indonesia_updated
     * beserta view, stored procedure, dan trigger.
     *
     * @return void
     */
    public function up(): void
    {
        // ------------------------------------------------------------------
        // 1.1. Membuat Tabel
        // ------------------------------------------------------------------

        Schema::create('gaji_karyawan_indonesia_updated', function (Blueprint $table) {
            // Kolom primary key auto-increment
            $table->id();

            // Kolom data karyawan
            $table->string('nama')->index(); // MEMENUHI KUK: Membangkitkan Indeks

            // Kolom pengalaman dan usia
            $table->integer('pengalaman_kerja_tahun');
            $table->integer('usia');

            // Kolom jenis kelamin dengan panjang maksimal 20 karakter
            $table->string('jenis_kelamin', 20);

            // Kolom gaji menggunakan big integer untuk menampung nominal besar
            $table->bigInteger('gaji_per_bulan_rp');

            // Foreign key ke tabel data_jabatan (nullable)
            $table->unsignedBigInteger('id_jabatan')->nullable();

            // Timestamp created_at dan updated_at
            $table->timestamps();

            // Definisi foreign key constraint
            $table->foreign('id_jabatan')
                  ->references('id_jabatan')
                  ->on('data_jabatan')
                  ->onDelete('set null');
        });

        // ------------------------------------------------------------------
        // 1.2. Membuat View, Stored Procedure, dan Trigger
        // ------------------------------------------------------------------
        // MEMENUHI KUK: SQL DML, Stored Procedure, Trigger, dan View

        DB::unprepared("
            -- ================================================================
            -- VIEW: Rekap Data Gaji Karyawan
            -- ================================================================
            CREATE VIEW view_rekap_gaji_karyawan AS
            SELECT
                a.nama,
                a.gaji_per_bulan_rp,
                b.nama_jabatan
            FROM gaji_karyawan_indonesia_updated a
            LEFT JOIN data_jabatan b ON a.id_jabatan = b.id_jabatan;

            -- ================================================================
            -- STORED PROCEDURE: Total Beban Gaji
            -- ================================================================
            CREATE PROCEDURE GetTotalGaji()
            BEGIN
                SELECT SUM(gaji_per_bulan_rp) as total_beban_gaji
                FROM gaji_karyawan_indonesia_updated;
            END;

            -- ================================================================
            -- TRIGGER: Mencegah Nilai Gaji Negatif (sebelum INSERT)
            -- ================================================================
            CREATE TRIGGER trg_cek_gaji_minus BEFORE INSERT ON gaji_karyawan_indonesia_updated
            FOR EACH ROW
            BEGIN
                IF NEW.gaji_per_bulan_rp < 0 THEN
                    SET NEW.gaji_per_bulan_rp = 0;
                END IF;
            END;
        ");
    }

    // ======================================================================
    // 2. PROSES ROLLBACK MIGRASI (DOWN)
    // ======================================================================

    /**
     * Membatalkan migrasi dengan menghapus view, stored procedure, trigger,
     * dan tabel gaji_karyawan_indonesia_updated.
     *
     * @return void
     */
    public function down(): void
    {
        // Hapus objek-objek database yang dibuat di method up()
        DB::unprepared("
            DROP VIEW IF EXISTS view_rekap_gaji_karyawan;
            DROP PROCEDURE IF EXISTS GetTotalGaji;
            DROP TRIGGER IF EXISTS trg_cek_gaji_minus;
        ");

        // Hapus tabel
        Schema::dropIfExists('gaji_karyawan_indonesia_updated');
    }
};
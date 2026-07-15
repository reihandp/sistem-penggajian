<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gaji_karyawan_indonesia_updated', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->index(); // MEMENUHI KUK: Membangkitkan Indeks
            $table->integer('pengalaman_kerja_tahun');
            $table->integer('usia');
            $table->string('jenis_kelamin', 20);
            $table->bigInteger('gaji_per_bulan_rp');
            $table->unsignedBigInteger('id_jabatan')->nullable();
            $table->timestamps();

            $table->foreign('id_jabatan')->references('id_jabatan')->on('data_jabatan')->onDelete('set null');
        });

        // MEMENUHI KUK: SQL DML, Stored Procedure, Trigger, dan View
        DB::unprepared("
            -- Membuat View
            CREATE VIEW view_rekap_gaji_karyawan AS
            SELECT a.nama, a.gaji_per_bulan_rp, b.nama_jabatan
            FROM gaji_karyawan_indonesia_updated a
            LEFT JOIN data_jabatan b ON a.id_jabatan = b.id_jabatan;

            -- Membuat Stored Procedure
            CREATE PROCEDURE GetTotalGaji()
            BEGIN
                SELECT SUM(gaji_per_bulan_rp) as total_beban_gaji FROM gaji_karyawan_indonesia_updated;
            END;

            -- Membuat Trigger (Mencegah gaji negatif)
            CREATE TRIGGER trg_cek_gaji_minus BEFORE INSERT ON gaji_karyawan_indonesia_updated
            FOR EACH ROW
            BEGIN
                IF NEW.gaji_per_bulan_rp < 0 THEN
                    SET NEW.gaji_per_bulan_rp = 0;
                END IF;
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("
            DROP VIEW IF EXISTS view_rekap_gaji_karyawan;
            DROP PROCEDURE IF EXISTS GetTotalGaji;
            DROP TRIGGER IF EXISTS trg_cek_gaji_minus;
        ");
        Schema::dropIfExists('gaji_karyawan_indonesia_updated');
    }
};

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;
// // sss
// use Illuminate\Support\Facades\DB;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('gaji_karyawan_indonesia_updated', function (Blueprint $table) {
//             $table->id();
//             $table->string('nama');
//             $table->integer('pengalaman_kerja_tahun');
//             $table->integer('usia');
//             $table->string('jenis_kelamin', 20);
//             $table->bigInteger('gaji_per_bulan_rp');
//             $table->unsignedBigInteger('id_jabatan')->nullable();
//             $table->timestamps();

//             $table->foreign('id_jabatan')->references('id_jabatan')->on('data_jabatan')->onDelete('set null');
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     // Fungsi down untuk menghapus tabel gaji_karyawan_indonesia_updated jika migrasi dibatalkan.
//     public function down(): void
//     {
//         Schema::dropIfExists('gaji_karyawan_indonesia_updated');
//     }
// };

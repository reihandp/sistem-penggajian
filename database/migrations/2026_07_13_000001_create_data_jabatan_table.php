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
     * Membuat tabel data_jabatan untuk menyimpan master data jabatan.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('data_jabatan', function (Blueprint $table) {
            // Primary key dengan nama custom 'id_jabatan' (auto-increment)
            $table->id('id_jabatan');

            // Kolom nama jabatan
            $table->string('nama_jabatan');

            // Timestamp created_at dan updated_at
            $table->timestamps();
        });
    }

    // ======================================================================
    // 2. PROSES ROLLBACK MIGRASI (DOWN)
    // ======================================================================

    /**
     * Membatalkan migrasi dengan menghapus tabel data_jabatan.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('data_jabatan');
    }
};
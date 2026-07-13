<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gaji_karyawan_indonesia', function (Blueprint $table) {
            $table->unsignedBigInteger('id_jabatan')->nullable()->after('gaji_per_bulan_rp');
            $table->foreign('id_jabatan')->references('id_jabatan')->on('data_jabatan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_karyawan_indonesia', function (Blueprint $table) {
            $table->dropForeign(['id_jabatan']);
            $table->dropColumn('id_jabatan');
        });
    }
};

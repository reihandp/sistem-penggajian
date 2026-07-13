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
        Schema::create('gaji_karyawan_indonesia', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('pengalaman_kerja_tahun');
            $table->integer('usia');
            $table->string('jenis_kelamin', 20);
            $table->bigInteger('gaji_per_bulan_rp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_karyawan_indonesia');
    }
};
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
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id(); // ID otomatis otomatis bertambah (Auto-increment)
            $table->integer('experience_years');
            $table->integer('age');
            $table->string('gender', 10);
            $table->bigInteger('salary'); // Menggunakan bigInteger agar bisa menampung nominal besar
            $table->timestamps();
        });
    }

    // public function up(): void
    // {
    //     Schema::create('employee_salaries', function (Blueprint $table) {
    //         $table->id(); 
    //         $table->integer('experience_years');
    //         $table->integer('age');
    //         $table->string('gender', 10);
    //         $table->bigInteger('salary'); // Menggunakan bigInteger untuk nominal uang
    //         $table->timestamps();
    //     });
    // }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};

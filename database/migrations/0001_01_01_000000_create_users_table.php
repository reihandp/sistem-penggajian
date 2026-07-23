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
     * Membuat tabel-tabel default Laravel untuk autentikasi:
     * - users
     * - password_reset_tokens
     * - sessions
     *
     * @return void
     */
    public function up(): void
    {
        // ------------------------------------------------------------------
        // 1.1. Tabel Users
        // ------------------------------------------------------------------

        Schema::create('users', function (Blueprint $table) {
            $table->id();                                   // Primary key auto-increment
            $table->string('name');                         // Nama pengguna
            $table->string('email')->unique();              // Email (unik)
            $table->timestamp('email_verified_at')->nullable(); // Waktu verifikasi email
            $table->string('password');                     // Password (hash)
            $table->rememberToken();                        // Token untuk "remember me"
            $table->timestamps();                           // created_at & updated_at
        });

        // ------------------------------------------------------------------
        // 1.2. Tabel Password Reset Tokens
        // ------------------------------------------------------------------

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();             // Email sebagai primary key
            $table->string('token');                        // Token reset password
            $table->timestamp('created_at')->nullable();    // Waktu pembuatan token
        });

        // ------------------------------------------------------------------
        // 1.3. Tabel Sessions
        // ------------------------------------------------------------------

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();                // Session ID sebagai primary key
            $table->foreignId('user_id')->nullable()->index(); // Foreign key ke users
            $table->string('ip_address', 45)->nullable();   // IP Address pengguna
            $table->text('user_agent')->nullable();         // User Agent browser
            $table->longText('payload');                    // Data session (terenkripsi)
            $table->integer('last_activity')->index();      // Waktu aktivitas terakhir
        });
    }

    // ======================================================================
    // 2. PROSES ROLLBACK MIGRASI (DOWN)
    // ======================================================================

    /**
     * Membatalkan migrasi dengan menghapus semua tabel yang dibuat.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
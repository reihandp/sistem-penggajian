<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // ======================================================================
    // 1. TRAITS
    // ======================================================================

    use WithoutModelEvents;

    // ======================================================================
    // 2. PROSES SEEDING
    // ======================================================================

    /**
     * Menjalankan semua seeder untuk mengisi data awal ke database.
     *
     * @return void
     */
    public function run(): void
    {
        // ------------------------------------------------------------------
        // 2.1. Seed Data User
        // ------------------------------------------------------------------

        // Membuat 10 user menggunakan factory (dikomentari)
        // User::factory(10)->create();

        // Membuat 1 user spesifik untuk testing
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // ------------------------------------------------------------------
        // 2.2. Seed Data Master dan Transaksi
        // ------------------------------------------------------------------

        // Jalankan seeder jabatan terlebih dahulu (karena menjadi foreign key)
        $this->call(JabatanSeeder::class);

        // Jalankan seeder gaji karyawan setelah jabatan tersedia
        $this->call(EmployeeSalarySeeder::class);
    }
}
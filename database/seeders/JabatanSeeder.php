<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    // ======================================================================
    // 1. PROSES SEEDING DATA JABATAN
    // ======================================================================

    /**
     * Mengisi tabel data_jabatan dari file CSV.
     *
     * @return void
     */
    public function run(): void
    {
        // ------------------------------------------------------------------
        // 1.1. Membaca File CSV
        // ------------------------------------------------------------------

        // Format CSV: (id_jabatan, nama_jabatan)
        $csvFile = fopen(database_path('seeders/data_jabatan.csv'), 'r');

        // ------------------------------------------------------------------
        // 1.2. Memproses Setiap Baris CSV
        // ------------------------------------------------------------------

        $firstRow = true;

        while (($data = fgetcsv($csvFile, 2000, ',')) !== false) {
            // Lewati baris pertama (header)
            if (!$firstRow) {
                // Insert data ke tabel data_jabatan
                DB::table('data_jabatan')->insert([
                    'id_jabatan' => (int) $data[0],   // Kolom 0: ID Jabatan
                    'nama_jabatan' => $data[1],       // Kolom 1: Nama Jabatan
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $firstRow = false;
        }

        // ------------------------------------------------------------------
        // 1.3. Menutup File
        // ------------------------------------------------------------------

        fclose($csvFile);
    }
}

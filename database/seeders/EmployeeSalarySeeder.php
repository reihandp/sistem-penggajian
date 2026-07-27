<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSalarySeeder extends Seeder
{
    // ======================================================================
    // 1. PROSES SEEDING DATA GAJI KARYAWAN
    // ======================================================================

    /**
     * Mengisi tabel gaji_karyawan_indonesia_updated dari file CSV.
     *
     * @return void
     */
    public function run(): void
    {
        // ------------------------------------------------------------------
        // 1.1. Menentukan Path File CSV
        // ------------------------------------------------------------------

        $csvPath = database_path('seeders/gaji_karyawan_indonesia_updated.csv');

        // Fallback ke nama file lama jika file dengan nama baru tidak ditemukan
        if (!file_exists($csvPath)) {
            $csvPath = database_path('seeders/data_gaji_karyawan.csv');
        }

        // ------------------------------------------------------------------
        // 1.2. Membaca dan Memproses File CSV
        // ------------------------------------------------------------------

        $csvFile = fopen($csvPath, 'r');
        $firstRow = true;

        while (($data = fgetcsv($csvFile, 2000, ',')) !== false) {
            // Lewati baris pertama (header)
            if (!$firstRow) {
                
                // Insert data ke tabel menggunakan DB facade
                DB::table('gaji_karyawan_indonesia_updated')->insert([
                    // Kolom 0: ID (nullable)
                    'id' => isset($data[0]) && is_numeric($data[0]) ? (int) $data[0] : null,

                    // Kolom 1: Nama Karyawan
                    'nama' => $data[1] ?? null,

                    // Kolom 2: Pengalaman Kerja (tahun)
                    'pengalaman_kerja_tahun' => isset($data[2]) ? (int) $data[2] : null,

                    // Kolom 3: Usia
                    'usia' => isset($data[3]) ? (int) $data[3] : null,

                    // Kolom 4: Jenis Kelamin
                    'jenis_kelamin' => $data[4] ?? null,

                    // Kolom 5: Gaji Per Bulan (Rp)
                    'gaji_per_bulan_rp' => isset($data[5]) ? (int) $data[5] : null,

                    // Kolom 7: ID Jabatan (nullable, kosongkan jika tidak ada)
                    // Jika di CSV tidak ada kolom jabatan, akan fallback ke random 1-5 agar data jabatan terisi
                    'id_jabatan' => isset($data[7]) && $data[7] !== '' ? (int) $data[7] : rand(1, 5),

                    // Timestamp
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $firstRow = false;
        }

        // Tutup file setelah selesai diproses
        fclose($csvFile);
    }
}


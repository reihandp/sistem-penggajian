<?php

namespace Database\Seeders;

use App\Models\EmployeeSalary;
use Illuminate\Database\Seeder;

class EmployeeSalarySeeder extends Seeder
{
    public function run(): void
    {
        // New CSV format: (ID,Nama,Pengalaman_Kerja_Tahun,Usia,Jenis_Kelamin,Gaji_Per_Bulan_Rp,id_jabatan)
        $csvPath = database_path('seeders/gaji_karyawan_indonesia_updated.csv');
        if (!file_exists($csvPath)) {
            // fallback to old filename if updated not present
            $csvPath = database_path('seeders/Gaji_Karyawan_Indonesia.csv');
        }

        $csvFile = fopen($csvPath, 'r');
        $firstRow = true;

        while (($data = fgetcsv($csvFile, 2000, ',')) !== FALSE) {
            if (!$firstRow) {
                // Use DB insert to allow explicit ID if present in CSV
                \Illuminate\Support\Facades\DB::table('gaji_karyawan_indonesia_updated')->insert([
                    // CSV columns: 0:id,1:nama,2:pengalaman,3:usia,4:jenis_kelamin,5:gaji,6:id_jabatan
                    'id' => isset($data[0]) && is_numeric($data[0]) ? (int)$data[0] : null,
                    'nama' => $data[1] ?? null,
                    'pengalaman_kerja_tahun' => isset($data[2]) ? (int)$data[2] : null,
                    'usia' => isset($data[3]) ? (int)$data[3] : null,
                    'jenis_kelamin' => $data[4] ?? null,
                    'gaji_per_bulan_rp' => isset($data[5]) ? (int)$data[5] : null,
                    'id_jabatan' => isset($data[6]) && $data[6] !== '' ? (int)$data[6] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $firstRow = false;
        }

        fclose($csvFile);
    }
}

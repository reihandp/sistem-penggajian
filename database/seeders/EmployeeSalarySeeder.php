<?php

namespace Database\Seeders;

use App\Models\EmployeeSalary;
use Illuminate\Database\Seeder;

class EmployeeSalarySeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = fopen(database_path('seeders/Gaji_Karyawan_Indonesia.csv'), 'r');
        $firstRow = true;
        
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstRow) {
                EmployeeSalary::create([
                    'nama' => $data[1],
                    'pengalaman_kerja_tahun' => (int) $data[2],
                    'usia' => (int) $data[3],
                    'jenis_kelamin' => $data[4],
                    'gaji_per_bulan_rp' => (int) $data[5],
                ]);
            }
            $firstRow = false;
        }
        fclose($csvFile);
    }
}

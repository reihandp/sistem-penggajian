<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = fopen(database_path('seeders/data_jabatan.csv'), 'r');
        $firstRow = true;

        while (($data = fgetcsv($csvFile, 2000, ',')) !== FALSE) {
            if (!$firstRow) {
                DB::table('data_jabatan')->insert([
                    'id_jabatan' => (int) $data[0],
                    'nama_jabatan' => $data[1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $firstRow = false;
        }

        fclose($csvFile);
    }
}

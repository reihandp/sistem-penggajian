<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SalaryCalculationTest extends TestCase
{
    /**
     * Skenario 1: Menguji karyawan dengan gaji normal dan pengalaman > 2 tahun.
     * MEMENUHI KUK: Melaksanakan pengujian unit program
     */
    public function test_karyawan_pengalaman_tiga_tahun_dapat_bonus_200rb()
    {
        // 1. Persiapan Data (Arrange)
        $gajiPokok = 5000000;
        $pengalaman = 3;
        
        // 2. Eksekusi Logika Algoritma (Act)
        $totalGaji = $this->simulasiHitungGaji($gajiPokok, $pengalaman);

        // 3. Pembuktian (Assert)
        // Kita berekspektasi hasilnya harus 5.200.000
        $this->assertEquals(5200000, $totalGaji);
    }

    /**
     * Skenario 2: Menguji anomali gaji 0 (Trik Debugging kita sebelumnya).
     */
    public function test_karyawan_gaji_nol_tidak_dapat_bonus()
    {
        $gajiPokok = 0;
        $pengalaman = 10; // Walau pengalaman 10 tahun, karena gajinya 0 harusnya tidak dapat bonus
        
        $totalGaji = $this->simulasiHitungGaji($gajiPokok, $pengalaman);

        // Kita berekspektasi hasilnya tetap 0
        $this->assertEquals(0, $totalGaji);
    }

    /**
     * Fungsi duplikat dari Controller untuk simulasi tes (Isolasi Unit).
     */
    private function simulasiHitungGaji($gajiPokok, $pengalaman)
    {
        if ($gajiPokok <= 0) {
            return $gajiPokok;
        }

        $bonus = 0;
        if ($pengalaman >= 5) {
            $bonus = 500000;
        } elseif ($pengalaman >= 2) {
            $bonus = 200000;
        }

        return $gajiPokok + $bonus;
    }
}
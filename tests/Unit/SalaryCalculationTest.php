<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SalaryCalculationTest extends TestCase
{
    // ======================================================================
    // 1. SKENARIO UJI: BONUS BERDASARKAN PENGALAMAN
    // ======================================================================

    /**
     * Skenario 1: Karyawan dengan pengalaman 3 tahun mendapatkan bonus Rp 200.000.
     * Melaksanakan pengujian unit program.
     *
     * @return void
     */
    public function test_karyawan_pengalaman_tiga_tahun_dapat_bonus_200rb()
    {
        // ------------------------------------------------------------------
        // 1.1. Persiapan Data (Arrange)
        // ------------------------------------------------------------------

        $gajiPokok = 5000000;
        $pengalaman = 3;

        // ------------------------------------------------------------------
        // 1.2. Eksekusi Logika (Act)
        // ------------------------------------------------------------------

        $totalGaji = $this->simulasiHitungGaji($gajiPokok, $pengalaman);

        // ------------------------------------------------------------------
        // 1.3. Verifikasi Hasil (Assert)
        // ------------------------------------------------------------------

        // Ekspektasi: gaji pokok + bonus Rp 200.000 = Rp 5.200.000
        $this->assertEquals(5200000, $totalGaji);
    }

    // ======================================================================
    // 2. SKENARIO UJI: ANOMALI GAJI NOL
    // ======================================================================

    /**
     * Skenario 2: Karyawan dengan gaji pokok 0 tidak mendapatkan bonus,
     * meskipun pengalaman kerja tinggi.
     *
     * @return void
     */
    public function test_karyawan_gaji_nol_tidak_dapat_bonus()
    {
        // ------------------------------------------------------------------
        // 2.1. Persiapan Data (Arrange)
        // ------------------------------------------------------------------

        $gajiPokok = 0;
        $pengalaman = 10; // Pengalaman tinggi, tetapi gaji pokok 0

        // ------------------------------------------------------------------
        // 2.2. Eksekusi Logika (Act)
        // ------------------------------------------------------------------

        $totalGaji = $this->simulasiHitungGaji($gajiPokok, $pengalaman);

        // ------------------------------------------------------------------
        // 2.3. Verifikasi Hasil (Assert)
        // ------------------------------------------------------------------

        // Ekspektasi: tetap 0 (bonus tidak diberikan karena gaji pokok <= 0)
        $this->assertEquals(0, $totalGaji);
    }

    // ======================================================================
    // 3. FUNGSI PEMBANTU UNTUK SIMULASI
    // ======================================================================

    /**
     * Fungsi simulasi perhitungan gaji (duplikat dari Controller)
     * untuk keperluan pengujian unit secara terisolasi.
     *
     * @param int $gajiPokok
     * @param int $pengalaman
     * @return int Total gaji setelah bonus
     */
    private function simulasiHitungGaji($gajiPokok, $pengalaman)
    {
        // Jika gaji pokok <= 0, tolak semua bonus
        if ($gajiPokok <= 0) {
            return $gajiPokok;
        }

        // Hitung bonus berdasarkan pengalaman kerja
        $bonus = 0;
        if ($pengalaman >= 5) {
            $bonus = 500000;
        } elseif ($pengalaman >= 2) {
            $bonus = 200000;
        }

        return $gajiPokok + $bonus;
    }
}
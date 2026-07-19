<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalary;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Exception;

class EmployeeSalaryController extends Controller
{       
    // Fungsi untuk menampilkan daftar data gaji karyawan dengan fitur pencarian
    public function index(): View
    {
        $search = request('search'); 

        $salaries = EmployeeSalary::when($search, function ($query, $search) {
            $query->where('nama', 'like', $search . '%');
        })
            ->with('jabatan')
            ->latest('id')
            ->paginate(10) // Pagination untuk menampilkan 10 data per halaman
            ->withQueryString();

        return view('penggajian.index', compact('salaries'));
    }

    // Fungsi untuk menampilkan form input data gaji karyawan baru
    public function create(): View
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        return view('penggajian.create', compact('jabatan'));
    }

    /*--------------------------------------------------------------------
      - Bagian Penyimpanan Data Gaji Karyawan Baru 
      dengan Transaksi Database dan Validasi Input     
    --------------------------------------------------------------------*/
    
    // Fungsi untuk menyimpan data gaji karyawan baru dengan penanganan transaksi database dan validasi input.
    // * Melakukan perubahan data dengan perintah commit/rollback
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'pengalaman_kerja_tahun' => ['required', 'integer', 'min:0'],
            'usia' => ['required', 'integer', 'min:0'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'gaji_per_bulan_rp' => ['required', 'integer', 'min:0'],
            'id_jabatan' => ['nullable', 'integer', 'exists:data_jabatan,id_jabatan'],
        ]);

        // * Penghitungan total gaji dengan bonus berdasarkan pengalaman kerja
        $validated['gaji_per_bulan_rp'] = $this->hitungTotalGajiDenganBonus(
            $validated['gaji_per_bulan_rp'], 
            $validated['pengalaman_kerja_tahun'],
            $validated['id_jabatan'] ?? null
        );

        // Transaksi database untuk menyimpan data gaji karyawan baru
        DB::beginTransaction();
        try {
            EmployeeSalary::create($validated);
            DB::commit();
            return redirect()->route('penggajian.index')->with('success', 'Data gaji berhasil ditambahkan.');
        } catch (Exception $e) {
            DB::rollBack();
            // * Debugging - Mencatat kode kesalahan
            // yang akan disimpan di log bagian storage/logs/laravel.log
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // Fungsi untuk menampilkan detail data gaji karyawan
    public function show(EmployeeSalary $employeeSalary)
    {
        return redirect()->route('penggajian.index');
    }

    /*--------------------------------------------------------------------
      - Bagian Edit dan Update Data Gaji Karyawan     
    --------------------------------------------------------------------*/

    // // Fungsi untuk menampilkan form edit data gaji karyawan dengan penanganan bonus lama
    // public function edit(EmployeeSalary $employeeSalary): View
    // {
    //     $jabatan = Jabatan::orderBy('nama_jabatan')->get();

    //     // 1. REVERSE LOGIC: Kita hitung dulu bonus apa yang menempel pada data lama
    //     $pengalaman = $employeeSalary->pengalaman_kerja_tahun;
    //     $bonusLama = 0;
        
    //     // CEK: Hanya hitung bonusLama jika gaji di database memang lebih dari 0
    //     if ($employeeSalary->gaji_per_bulan_rp > 0) {
    //         if ($pengalaman >= 5) {
    //             $bonusLama = 500000;
    //         } elseif ($pengalaman >= 2) {
    //             $bonusLama = 200000;
    //         }
    //     }

    //     // Kurangi bonus lama dari gaji per bulan agar form edit menampilkan gaji pokok yang sebenarnya
    //     $employeeSalary->gaji_per_bulan_rp = $employeeSalary->gaji_per_bulan_rp - $bonusLama;

    //     // Validasi agar gaji pokok tidak menjadi negatif setelah dikurangi bonus lama
    //     if ($employeeSalary->gaji_per_bulan_rp < 0) {
    //         $employeeSalary->gaji_per_bulan_rp = 0;
    //     }
        
    //     // Mengembalikan view edit dengan data gaji karyawan dan daftar jabatan
    //     return view('penggajian.edit', compact('employeeSalary', 'jabatan'));
    // }

    // Fungsi untuk menampilkan form edit data gaji karyawan dengan penanganan bonus lama
    public function edit(EmployeeSalary $employeeSalary): View
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();

        $bonusLama = 0;
        
        // CEK: Hanya hitung bonusLama jika gaji di database memang lebih dari 0
        if ($employeeSalary->gaji_per_bulan_rp > 0) {
            
            // PANGGIL MESIN BONUS (Jangan tulis if-else manual lagi di sini!)
            $bonusLama = $this->hitungBonus(
                $employeeSalary->pengalaman_kerja_tahun, 
                $employeeSalary->id_jabatan
            );
        }

        // Kurangi bonus lama dari gaji per bulan agar form edit menampilkan gaji pokok yang sebenarnya
        $employeeSalary->gaji_per_bulan_rp = $employeeSalary->gaji_per_bulan_rp - $bonusLama;

        // Validasi agar gaji pokok tidak menjadi negatif setelah dikurangi bonus lama
        if ($employeeSalary->gaji_per_bulan_rp < 0) {
            $employeeSalary->gaji_per_bulan_rp = 0;
        }
        
        // Mengembalikan view edit dengan data gaji karyawan dan daftar jabatan
        return view('penggajian.edit', compact('employeeSalary', 'jabatan'));
    }
    
    // Fungsi untuk memperbarui data gaji karyawan dengan penanganan transaksi database dan validasi input.
    public function update(Request $request, EmployeeSalary $employeeSalary): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'pengalaman_kerja_tahun' => ['required', 'integer', 'min:0'],
            'usia' => ['required', 'integer', 'min:0'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'gaji_per_bulan_rp' => ['required', 'integer', 'min:0'],
            'id_jabatan' => ['nullable', 'integer', 'exists:data_jabatan,id_jabatan'],
        ]);

        // * Penghitungan total gaji dengan bonus berdasarkan pengalaman kerja
        $validated['gaji_per_bulan_rp'] = $this->hitungTotalGajiDenganBonus(
            $validated['gaji_per_bulan_rp'], 
            $validated['pengalaman_kerja_tahun'],
            $validated['id_jabatan'] ?? null
        );

        // Transaksi database untuk memperbarui data gaji karyawan
        DB::beginTransaction();
        try {
            $employeeSalary->update($validated);
            DB::commit();
            return redirect()->route('penggajian.index')->with('success', 'Data gaji berhasil diperbarui.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }


    /*--------------------------------------------------------------------
      - Bagian Penghapusan Data Gaji Karyawan     
    --------------------------------------------------------------------*/

    // Fungsi untuk menghapus data gaji karyawan dengan penanganan transaksi database.
    public function destroy(EmployeeSalary $employeeSalary): RedirectResponse
    {
        DB::beginTransaction();
        try {
            EmployeeSalary::whereKey($employeeSalary->getKey())->delete();
            DB::commit();
            return redirect()->route('penggajian.index')->with('success', 'Data gaji berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('penggajian.index')->with('error', 'Gagal menghapus data.');
        }
    }

    /*--------------------------------------------------------------------
      - Bagian Laporan gaji karyawan   
    --------------------------------------------------------------------*/

    // Fungsi untuk menampilkan laporan ringkasan data gaji karyawan dengan statistik total, rata-rata, tertinggi, dan terendah.
    public function laporan(): View
    {
        // Mengeksekusi Raw SQL query dengan sintaks DML
        $stats = DB::select("
            SELECT 
                COUNT(id) as total_data, 
                AVG(gaji_per_bulan_rp) as rata_rata_gaji, 
                MAX(gaji_per_bulan_rp) as gaji_tertinggi, 
                MIN(gaji_per_bulan_rp) as gaji_terendah 
            FROM gaji_karyawan_indonesia_updated
        ");

        $summary = [
            'total_data' => $stats[0]->total_data,
            'rata_rata_gaji' => (int) $stats[0]->rata_rata_gaji,
            'gaji_tertinggi' => (int) $stats[0]->gaji_tertinggi,
            'gaji_terendah' => (int) $stats[0]->gaji_terendah,
        ]; 

        // Mengembalikan view laporan dengan data ringkasan statistik gaji karyawan
        return view('penggajian.laporan', compact('summary'));
    }

    // /**
    //  * Algoritma penambahan bonus berdasarkan pengalaman kerja.
    //  * * Berdasarkan Varian dan Invarian
    //  *
    //  * @param int $gajiPokok (Invarian)
    //  * @param int $pengalaman (Varian)
    //  * @return int Total Gaji
    //  */

    // private function hitungTotalGajiDenganBonus(int $gajiPokok, int $pengalaman): int
    // {
    //     // Jika gaji pokok kurang dari atau sama dengan 0, maka tidak ada bonus yang diberikan
    //     if ($gajiPokok <= 0) {
    //         return $gajiPokok;
    //     }

    //     $bonus = 0;
        
    //     // Logika menentukan bonus berdasarkan pengalaman kerja
    //     if ($pengalaman >= 5) {
    //         $bonus = 500000;
    //     } elseif ($pengalaman >= 2) {
    //         $bonus = 200000;
    //     }

    //     // Mengembalikan total gaji yang merupakan penjumlahan dari gaji pokok dan bonus
    //     return $gajiPokok + $bonus;
    // }

    //---------------------------------------------------------------------
    /**
     * Algoritma penambahan bonus berdasarkan jabatan dan pengalaman.
     * MEMENUHI KUK: Menerapkan algoritma percabangan kompleks (Switch & If-Else)
     *
     * @param int $gajiPokok (Invarian)
     * @param int $pengalaman (Varian 1)
     * @param int|null $idJabatan (Varian 2)
     * @return int Total Gaji Akhir
     */
    private function hitungTotalGajiDenganBonus(int $gajiPokok, int $pengalaman, ?int $idJabatan): int
    {
        // Early return: Jika gaji pokok 0, tolak proses bonus
        if ($gajiPokok <= 0) {
            return $gajiPokok;
        }

        // Panggil mesin pemroses bonus
        $bonus = $this->hitungBonus($pengalaman, $idJabatan);

        return $gajiPokok + $bonus;
    }

    /**
     * Mesin pemroses nominal bonus murni (Fungsi terpisah agar kode rapi / DRY)
     */
    private function hitungBonus(int $pengalaman, ?int $idJabatan): int
    {
        $bonus = 0;
        
        // Jika belum ada jabatan, tidak dapat bonus jabatan
        if (!$idJabatan) {
            return 0;
        }

        // Logika Switch-Case berdasarkan ID Jabatan
        switch ($idJabatan) {
            case 1: // Manager
                if ($pengalaman >= 10) $bonus = 2000000;
                elseif ($pengalaman >= 5) $bonus = 1000000;
                elseif ($pengalaman >= 2) $bonus = 500000;
                break;
            case 2: // Staff
                if ($pengalaman >= 10) $bonus = 1000000;
                elseif ($pengalaman >= 5) $bonus = 500000;
                elseif ($pengalaman >= 2) $bonus = 200000;
                break;
            case 3: // Admin
                if ($pengalaman >= 10) $bonus = 800000;
                elseif ($pengalaman >= 5) $bonus = 400000;
                elseif ($pengalaman >= 2) $bonus = 150000;
                break;
            case 4: // Supervisor
                if ($pengalaman >= 10) $bonus = 1500000;
                elseif ($pengalaman >= 5) $bonus = 750000;
                elseif ($pengalaman >= 2) $bonus = 300000;
                break;
            case 5: // Direktur
                if ($pengalaman >= 10) $bonus = 5000000;
                elseif ($pengalaman >= 5) $bonus = 2500000;
                elseif ($pengalaman >= 2) $bonus = 1000000;
                break;
        }

        return $bonus;
    }
}

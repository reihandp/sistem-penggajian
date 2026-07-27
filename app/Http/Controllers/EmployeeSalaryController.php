<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalary;
use App\Models\Jabatan;
use Illuminate\Http\Request; //  Fungsi untuk menangani data HTTP (GET, POST, PUT, DELETE)
use Illuminate\Http\RedirectResponse; // Fungsi ini akan mengembalikan respons pengalihan (redirect) ke halaman lain.
use Illuminate\View\View; // Fungsi ini akan mengembalikan respons tampilan (view) file Blade 
use Illuminate\Support\Facades\DB; // Fungsi ini digunakan untuk melakukan operasi database secara langsung (raw SQL) dan transaksi database.
use Exception;

class EmployeeSalaryController extends Controller
{
    // ======================================================================
    // 1. HALAMAN UTAMA & PENCARIAN DATA GAJI
    // ======================================================================

    /**
     * Menampilkan daftar data gaji karyawan dengan fitur pencarian.
     *
     * @return View
     */
    public function index(): View
    {
        $search = request('search');

        $salaries = EmployeeSalary::when($search, function ($query, $search) {
            $query->where('nama', 'like', $search . '%');
        })
            ->with('jabatan')
            ->latest('id')
            ->paginate(10) // Menampilkan 10 data per halaman
            ->withQueryString();

        return view('penggajian.index', compact('salaries'));
    }

    // ======================================================================
    // 2. FORM TAMBAH DATA GAJI
    // ======================================================================

    /**
     * Menampilkan form input data gaji karyawan baru.
     *
     * @return View
     */
    public function create(): View
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        return view('penggajian.create', compact('jabatan'));
    }

    // ======================================================================
    // 3. PROSES SIMPAN DATA GAJI
    // ======================================================================

    /**
     * Menyimpan data gaji karyawan baru dengan transaksi database dan validasi input.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input dari form
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'pengalaman_kerja_tahun' => ['required', 'integer', 'min:0'],
            'usia' => ['required', 'integer', 'min:0'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'gaji_per_bulan_rp' => ['required', 'integer', 'min:0'],
            'id_jabatan' => ['nullable', 'integer', 'exists:data_jabatan,id_jabatan'],
        ]);

        // Hitung total gaji dengan bonus berdasarkan pengalaman kerja
        $validated['gaji_per_bulan_rp'] = $this->hitungTotalGajiDenganBonus(
            $validated['gaji_per_bulan_rp'],
            $validated['pengalaman_kerja_tahun'],
            $validated['id_jabatan'] ?? null
        );

        // Transaksi database untuk menyimpan data
        DB::beginTransaction();
        try {
            EmployeeSalary::create($validated);
            DB::commit();
            return redirect()->route('penggajian.index')->with('success', 'Data gaji berhasil ditambahkan.');
        } catch (Exception $e) {
            DB::rollBack();
            // Log error untuk debugging (tersimpan di storage/logs/laravel.log)
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // ======================================================================
    // 4. DETAIL DATA GAJI (REDIRECT KE INDEX)
    // ======================================================================

    /**
     * Menampilkan detail data gaji karyawan (dialihkan ke halaman index).
     *
     * @param EmployeeSalary $employeeSalary
     * @return RedirectResponse
     */
    public function show(EmployeeSalary $employeeSalary)
    {
        return redirect()->route('penggajian.index');
    }

    // ======================================================================
    // 5. FORM EDIT DATA GAJI
    // ======================================================================

    /**
     * Menampilkan form edit data gaji karyawan dengan penanganan bonus lama.
     *
     * @param EmployeeSalary $employeeSalary
     * @return View
     */
    public function edit(EmployeeSalary $employeeSalary): View
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();

        // Hitung bonus lama untuk dikurangi dari gaji per bulan
        $bonusLama = 0;
        if ($employeeSalary->gaji_per_bulan_rp > 0) {
            $bonusLama = $this->hitungBonus(
                $employeeSalary->pengalaman_kerja_tahun,
                $employeeSalary->id_jabatan
            );
        }

        // Kurangi bonus lama agar menampilkan gaji pokok yang sebenarnya
        $employeeSalary->gaji_per_bulan_rp = $employeeSalary->gaji_per_bulan_rp - $bonusLama;

        // Pastikan gaji pokok tidak negatif
        if ($employeeSalary->gaji_per_bulan_rp < 0) {
            $employeeSalary->gaji_per_bulan_rp = 0;
        }

        return view('penggajian.edit', compact('employeeSalary', 'jabatan'));
    }

    // ======================================================================
    // 6. PROSES UPDATE DATA GAJI
    // ======================================================================

    /**
     * Memperbarui data gaji karyawan dengan transaksi database dan validasi input.
     *
     * @param Request $request
     * @param EmployeeSalary $employeeSalary
     * @return RedirectResponse
     */
    public function update(Request $request, EmployeeSalary $employeeSalary): RedirectResponse
    {
        // Validasi input dari form
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'pengalaman_kerja_tahun' => ['required', 'integer', 'min:0'],
            'usia' => ['required', 'integer', 'min:0'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'gaji_per_bulan_rp' => ['required', 'integer', 'min:0'],
            'id_jabatan' => ['nullable', 'integer', 'exists:data_jabatan,id_jabatan'],
        ]);

        // Hitung total gaji dengan bonus berdasarkan pengalaman kerja
        $validated['gaji_per_bulan_rp'] = $this->hitungTotalGajiDenganBonus(
            $validated['gaji_per_bulan_rp'],
            $validated['pengalaman_kerja_tahun'],
            $validated['id_jabatan'] ?? null
        );

        // Transaksi database untuk memperbarui data
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

    // ======================================================================
    // 7. PROSES HAPUS DATA GAJI
    // ======================================================================

    /**
     * Menghapus data gaji karyawan dengan transaksi database.
     *
     * @param EmployeeSalary $employeeSalary
     * @return RedirectResponse
     */
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

    // ======================================================================
    // 8. LAPORAN STATISTIK GAJI
    // ======================================================================

    /**
     * Menampilkan laporan ringkasan data gaji karyawan
     * (total, rata-rata, tertinggi, terendah).
     *
     * @return View
     */
    public function laporan(): View
    {
        // Eksekusi raw SQL query untuk statistik
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

        return view('penggajian.laporan', compact('summary'));
    }

    // ======================================================================
    // 9. FUNGSI PEMBANTU PERHITUNGAN BONUS
    // ======================================================================

    /**
     * Menghitung total gaji akhir dengan penambahan bonus berdasarkan jabatan dan pengalaman.
     * Menerapkan algoritma percabangan kompleks (Switch & If-Else).
     *
     * @param int $gajiPokok
     * @param int $pengalaman
     * @param int|null $idJabatan
     * @return int Total gaji akhir
     */
    private function hitungTotalGajiDenganBonus(int $gajiPokok, int $pengalaman, ?int $idJabatan): int
    {
        // Jika gaji pokok 0, tolak proses bonus
        if ($gajiPokok <= 0) {
            return $gajiPokok;
        }

        $bonus = $this->hitungBonus($pengalaman, $idJabatan);
        return $gajiPokok + $bonus;
    }

    /**
     * Mesin pemroses nominal bonus berdasarkan jabatan dan pengalaman kerja.
     * Fungsi terpisah agar kode lebih rapi dan DRY.
     *
     * @param int $pengalaman
     * @param int|null $idJabatan
     * @return int Nominal bonus
     */
    private function hitungBonus(int $pengalaman, ?int $idJabatan): int
    {
        // Jika belum ada jabatan, tidak mendapat bonus
        if (!$idJabatan) {
            return 0;
        }

        $bonus = 0;

        // Logika bonus berdasarkan ID Jabatan
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

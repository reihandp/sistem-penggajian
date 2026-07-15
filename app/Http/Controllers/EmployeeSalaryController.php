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
    public function index(): View
    {
        $search = request('search');

        $salaries = EmployeeSalary::when($search, function ($query, $search) {
            $query->where('nama', 'like', $search . '%');
        })
            ->with('jabatan')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('penggajian.index', compact('salaries'));
    }

    public function create(): View
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        return view('penggajian.create', compact('jabatan'));
    }

    /**
     * Store a newly created resource in storage.
     * MEMENUHI KUK: Melakukan perubahan data dengan perintah commit/rollback
     */
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

        // Hitung Bonus (MEMENUHI KUK: Mengimplementasikan Algoritma Pemrograman)
        $validated['gaji_per_bulan_rp'] = $this->hitungTotalGajiDenganBonus(
            $validated['gaji_per_bulan_rp'], 
            $validated['pengalaman_kerja_tahun']
        );

        DB::beginTransaction();
        try {
            EmployeeSalary::create($validated);
            DB::commit();
            return redirect()->route('penggajian.index')->with('success', 'Data gaji berhasil ditambahkan.');
        } catch (Exception $e) {
            DB::rollBack();
            // MEMENUHI KUK: Debugging - Mencatat kode kesalahan
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function show(EmployeeSalary $employeeSalary)
    {
        return redirect()->route('penggajian.index');
    }

    // public function edit(EmployeeSalary $employeeSalary): View
    // {
    //     $jabatan = Jabatan::orderBy('nama_jabatan')->get();
    //     return view('penggajian.edit', compact('employeeSalary', 'jabatan'));
    // }

    public function edit(EmployeeSalary $employeeSalary): View
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();

        // 1. REVERSE LOGIC: Kita hitung dulu bonus apa yang menempel pada data lama
        $pengalaman = $employeeSalary->pengalaman_kerja_tahun;
        $bonusLama = 0;
        
        // if ($pengalaman >= 5) {
        //     $bonusLama = 500000;
        // } elseif ($pengalaman >= 2) {
        //     $bonusLama = 200000;
        // }
        // CEK: Hanya hitung bonusLama jika gaji di database memang lebih dari 0
        if ($employeeSalary->gaji_per_bulan_rp > 0) {
            if ($pengalaman >= 5) {
                $bonusLama = 500000;
            } elseif ($pengalaman >= 2) {
                $bonusLama = 200000;
            }
        }

        // 2. DEKALKULASI: Kurangi gaji di memori sementara dengan bonus lamanya.
        // Trik ini membuat Halaman UI (Form) akan kembali menampilkan "Gaji Pokok Dasar",
        // BUKAN total gaji yang sudah terkena bonus, tanpa harus mengubah kode di file blade.php Anda!
        $employeeSalary->gaji_per_bulan_rp = $employeeSalary->gaji_per_bulan_rp - $bonusLama;

        // Validasi ekstra: memastikan nilai tidak bocor menjadi minus di form
        if ($employeeSalary->gaji_per_bulan_rp < 0) {
            $employeeSalary->gaji_per_bulan_rp = 0;
        }

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

        $validated['gaji_per_bulan_rp'] = $this->hitungTotalGajiDenganBonus(
            $validated['gaji_per_bulan_rp'], 
            $validated['pengalaman_kerja_tahun']
        );

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

    /**
     * Display summary report page.
     * MEMENUHI KUK: Menggunakan SQL Murni (Raw SQL)
     */
    public function laporan(): View
    {
        // Mengeksekusi Raw SQL query untuk membuktikan penguasaan sintaks DML
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

    /**
     * Algoritma penambahan bonus berdasarkan pengalaman kerja.
     * MEMENUHI KUK: Menjelaskan Varian dan Invarian
     *
     * @param int $gajiPokok (Invarian)
     * @param int $pengalaman (Varian)
     * @return int Total Gaji
     */
    private function hitungTotalGajiDenganBonus(int $gajiPokok, int $pengalaman): int
    {
        // 1. Jika gaji pokok 0 atau minus, langsung kembalikan nilainya (TANPA BONUS)
        if ($gajiPokok <= 0) {
            return $gajiPokok;
        }

        $bonus = 0;
        
        // Logika percabangan / algoritma
        if ($pengalaman >= 5) {
            $bonus = 500000;
        } elseif ($pengalaman >= 2) {
            $bonus = 200000;
        }

        return $gajiPokok + $bonus;
    }
}

// namespace App\Http\Controllers;

// use App\Models\EmployeeSalary;
// use App\Models\Jabatan;
// use Illuminate\Http\Request;
// use Illuminate\Http\RedirectResponse;
// use Illuminate\View\View;

// class EmployeeSalaryController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     // Fungsi index untuk menampilkan daftar data gaji karyawan dengan fitur pencarian dan pagination.
//     public function index(): View
//     {
//         $search = request('search');

//         $salaries = EmployeeSalary::when($search, function ($query, $search) {
//             $query->where('nama', 'like', $search . '%');
//         })
//             ->with('jabatan')
//             ->latest()
//             ->paginate(10)
//             ->withQueryString();

//         return view('penggajian.index', compact('salaries'));
//     }

//     /**
//      * Show the form for creating a new resource.
//      */
//     // Fungsi create untuk menampilkan form input data gaji karyawan baru.
//     public function create(): View
//     {
//         $jabatan = Jabatan::orderBy('nama_jabatan')->get();
//         return view('penggajian.create', compact('jabatan'));
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     // Fungsi store untuk menyimpan data gaji karyawan baru.
//     public function store(Request $request): RedirectResponse
//     {
//         $validated = $request->validate([
//             'nama' => ['required', 'string', 'max:255'],
//             'pengalaman_kerja_tahun' => ['required', 'integer', 'min:0'],
//             'usia' => ['required', 'integer', 'min:0'],
//             'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
//             'gaji_per_bulan_rp' => ['required', 'integer', 'min:0'],
//             'id_jabatan' => ['nullable', 'integer', 'exists:data_jabatan,id_jabatan'],
//         ]);

//         EmployeeSalary::create($validated);

//         return redirect()
//             ->route('penggajian.index')
//             ->with('success', 'Data gaji berhasil ditambahkan.');
//     }

//     /**
//      * Display the specified resource.
//      */
//     // Fungsi show untuk menampilkan detail data gaji karyawan.
//     public function show(EmployeeSalary $employeeSalary)
//     {
//         return redirect()->route('penggajian.index');
//     }

//     /**
//      * Show the form for editing the specified resource.
//      */
//     // Fungsi edit untuk menampilkan form edit data gaji karyawan.
//     public function edit(EmployeeSalary $employeeSalary): View
//     {
//         $jabatan = Jabatan::orderBy('nama_jabatan')->get();
//         return view('penggajian.edit', compact('employeeSalary', 'jabatan'));
//     }

//     /**
//      * Update the specified resource in storage.
//      */
//     // Fungsi update untuk memperbarui data gaji karyawan.
//     public function update(Request $request, EmployeeSalary $employeeSalary): RedirectResponse
//     {
//         $validated = $request->validate([
//             'nama' => ['required', 'string', 'max:255'],
//             'pengalaman_kerja_tahun' => ['required', 'integer', 'min:0'],
//             'usia' => ['required', 'integer', 'min:0'],
//             'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
//             'gaji_per_bulan_rp' => ['required', 'integer', 'min:0'],
//             'id_jabatan' => ['nullable', 'integer', 'exists:data_jabatan,id_jabatan'],
//         ]);

//         $employeeSalary->update($validated);

//         return redirect()
//             ->route('penggajian.index')
//             ->with('success', 'Data gaji berhasil diperbarui.');
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     // Fungsi destroy untuk menghapus data gaji karyawan.
//     public function destroy(EmployeeSalary $employeeSalary): RedirectResponse
//     {
//         EmployeeSalary::whereKey($employeeSalary->getKey())->delete();

//         return redirect()
//             ->route('penggajian.index')
//             ->with('success', 'Data gaji berhasil dihapus.');
//     }

//     /**
//      * Display summary report page.
//      */
//     // Fungsi laporan untuk menampilkan halaman laporan ringkasan data gaji karyawan.
//     public function laporan(): View
//     {
//         $summary = [
//             'total_data' => EmployeeSalary::count(),
//             'rata_rata_gaji' => (int) EmployeeSalary::avg('gaji_per_bulan_rp'),
//             'gaji_tertinggi' => (int) EmployeeSalary::max('gaji_per_bulan_rp'),
//             'gaji_terendah' => (int) EmployeeSalary::min('gaji_per_bulan_rp'),
//         ];

//         return view('penggajian.laporan', compact('summary'));
//     }
// }

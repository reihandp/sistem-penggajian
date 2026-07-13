<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalary;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        return view('penggajian.create', compact('jabatan'));
    }

    /**
     * Store a newly created resource in storage.
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

        EmployeeSalary::create($validated);

        return redirect()
            ->route('penggajian.index')
            ->with('success', 'Data gaji berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeSalary $employeeSalary)
    {
        return redirect()->route('penggajian.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeSalary $employeeSalary): View
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        return view('penggajian.edit', compact('employeeSalary', 'jabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
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

        $employeeSalary->update($validated);

        return redirect()
            ->route('penggajian.index')
            ->with('success', 'Data gaji berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeSalary $employeeSalary): RedirectResponse
    {
        EmployeeSalary::whereKey($employeeSalary->getKey())->delete();

        return redirect()
            ->route('penggajian.index')
            ->with('success', 'Data gaji berhasil dihapus.');
    }

    /**
     * Display summary report page.
     */
    public function laporan(): View
    {
        $summary = [
            'total_data' => EmployeeSalary::count(),
            'rata_rata_gaji' => (int) EmployeeSalary::avg('gaji_per_bulan_rp'),
            'gaji_tertinggi' => (int) EmployeeSalary::max('gaji_per_bulan_rp'),
            'gaji_terendah' => (int) EmployeeSalary::min('gaji_per_bulan_rp'),
        ];

        return view('penggajian.laporan', compact('summary'));
    }
}

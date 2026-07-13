@extends('layouts.penggajian')

@section('title', 'Daftar Gaji Karyawan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Daftar Gaji Karyawan</h4>
            <p class="text-muted mb-0">Data dari dataset Kaggle yang sudah diolah menjadi tabel gaji sederhana.</p>
        </div>
        <a href="{{ route('penggajian.create') }}" class="btn btn-dark btn-sm">+ Tambah Data</a>
    </div>

    <div class="card shadow-sm border mb-3">
        <div class="card-body">
            <form action="{{ route('penggajian.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-8 col-lg-6">
                    <label for="search" class="form-label">Cari Nama Karyawan</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        placeholder="Contoh: Budi"
                        value="{{ request('search') }}"
                    >
                </div>
                <div class="col-12 col-md-auto">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <a href="{{ route('penggajian.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0 payroll-table">
                    <thead>
                        <tr>
                            <th class="ps-4">No</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Pengalaman</th>
                            <th>Usia</th>
                            <th>Jenis Kelamin</th>
                            <th>Gaji Per Bulan</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $key => $item)
                            <tr>
                                <td class="ps-4 text-muted">{{ $salaries->firstItem() + $key }}</td>
                                <td>#{{ $item->id }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->pengalaman_kerja_tahun }} thn</td>
                                <td>{{ $item->usia }} thn</td>
                                <td>
                                    <span class="badge badge-gender {{ $item->jenis_kelamin == 'Perempuan' ? 'bg-danger-subtle text-danger' : 'bg-info-subtle text-info-emphasis' }}">
                                        {{ $item->jenis_kelamin }}
                                    </span>
                                </td>
                                <td class="fw-semibold">Rp {{ number_format($item->gaji_per_bulan_rp, 0, ',', '.') }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ url('/penggajian/' . $item->id . '/edit') }}" class="btn btn-outline-secondary btn-sm py-1 px-2">Edit</a>
                                    <form action="{{ url('/penggajian/' . $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">Belum ada data gaji.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">
            Menampilkan {{ $salaries->firstItem() }}–{{ $salaries->lastItem() }} dari {{ $salaries->total() }} data
        </small>
        {{ $salaries->links('pagination::bootstrap-5') }}
    </div>
@endsection
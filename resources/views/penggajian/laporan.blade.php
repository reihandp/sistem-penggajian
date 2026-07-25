@extends('layouts.penggajian')

@section('title', 'Laporan Penggajian')

@section('content')
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">Laporan Penggajian</h4>
        <p class="text-muted mb-0">Ringkasan laporan gaji karyawan berdasarkan data yang tersimpan.</p>
    </div>

    <div class="row g-3 g-md-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border">
                <div class="card-body">
                    <small class="text-muted">Total Data</small>
                    <h3 class="fw-bold mt-2 mb-0">{{ number_format($summary['total_data']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border">
                <div class="card-body">
                    <small class="text-muted">Rata-rata Gaji Per Bulan</small>
                    <h3 class="fw-bold mt-2 mb-0">Rp {{ number_format($summary['rata_rata_gaji'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border">
                <div class="card-body">
                    <small class="text-muted">Gaji Tertinggi</small>
                    <h3 class="fw-bold mt-2 mb-0">Rp {{ number_format($summary['gaji_tertinggi'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border">
                <div class="card-body">
                    <small class="text-muted">Gaji Terendah</small>
                    <h3 class="fw-bold mt-2 mb-0">Rp {{ number_format($summary['gaji_terendah'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.penggajian')

@section('title', 'Tambah Data Gaji')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="mb-4">
                <h4 class="fw-bold mb-1">Tambah Data Gaji</h4>
                <p class="text-muted mb-0">Isi data berikut untuk menambahkan record gaji baru.</p>
            </div>

            <form action="{{ route('penggajian.store') }}" method="POST">
                @csrf
                @include('penggajian._form')
            </form>
        </div>
    </div>
@endsection
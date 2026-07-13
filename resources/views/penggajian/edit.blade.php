@extends('layouts.penggajian')

@section('title', 'Edit Data Gaji')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="mb-4">
                <h4 class="fw-bold mb-1">Edit Data Gaji</h4>
                <p class="text-muted mb-0">Perbarui data karyawan dengan form di bawah ini.</p>
            </div>

            <form action="{{ url('/penggajian/' . $employeeSalary->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('penggajian._form', ['employeeSalary' => $employeeSalary])
            </form>
        </div>
    </div>
@endsection
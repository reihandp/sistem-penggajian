@php
    $isEdit = isset($employeeSalary);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $employeeSalary->nama ?? '') }}" maxlength="255">
        @error('nama')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Pengalaman Kerja (Tahun)</label>
        <input type="number" name="pengalaman_kerja_tahun" class="form-control @error('pengalaman_kerja_tahun') is-invalid @enderror" value="{{ old('pengalaman_kerja_tahun', $employeeSalary->pengalaman_kerja_tahun ?? '') }}" min="0">
        @error('pengalaman_kerja_tahun')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Usia</label>
        <input type="number" name="usia" class="form-control @error('usia') is-invalid @enderror" value="{{ old('usia', $employeeSalary->usia ?? '') }}" min="0">
        @error('usia')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
            <option value="">Pilih jenis kelamin</option>
            <option value="Laki-laki" @selected(old('jenis_kelamin', $employeeSalary->jenis_kelamin ?? '') === 'Laki-laki')>Laki-laki</option>
            <option value="Perempuan" @selected(old('jenis_kelamin', $employeeSalary->jenis_kelamin ?? '') === 'Perempuan')>Perempuan</option>
        </select>
        @error('jenis_kelamin')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Gaji Per Bulan (Rp)</label>
        <input type="number" name="gaji_per_bulan_rp" class="form-control @error('gaji_per_bulan_rp') is-invalid @enderror" value="{{ old('gaji_per_bulan_rp', $employeeSalary->gaji_per_bulan_rp ?? '') }}" min="0" placeholder="Contoh: 5000000">
        @error('gaji_per_bulan_rp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Masukkan angka dalam Rupiah tanpa tanda titik atau simbol Rp.</small>
    </div>

    <div class="col-md-6">
        <label class="form-label">Jabatan</label>
        @php $jabatanList = $jabatan ?? collect(); @endphp
        <select name="id_jabatan" class="form-select @error('id_jabatan') is-invalid @enderror">
            <option value="">-- Pilih Jabatan --</option>
            @foreach($jabatanList as $j)
                <option value="{{ $j->id_jabatan }}" @selected(old('id_jabatan', $employeeSalary->id_jabatan ?? '') == $j->id_jabatan)>{{ $j->nama_jabatan }}</option>
            @endforeach
        </select>
        @error('id_jabatan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <!-- tombol batal dan simpan data gaji karyawan -->
    <a href="{{ route('penggajian.index') }}" class="btn btn-outline-secondary">Batal</a>
    <button class="btn btn-dark">{{ $isEdit ? 'Update Data' : 'Simpan Data' }}</button>
</div>
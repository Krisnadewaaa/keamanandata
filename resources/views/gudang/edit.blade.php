@extends('layouts.gudang')

@section('title', 'Edit Profil Pegawai Gudang')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-gray-800"><i class="fas fa-user-edit mr-2 text-primary"></i> Edit Profil Pegawai Gudang</h1>
        <a href="{{ route('gudang.profile') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Card Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-user mr-2"></i> Informasi Profil</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('gudang.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Pegawai</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                               id="nama" name="nama" value="{{ old('nama', $pegawai->NAMA_PEGAWAI) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', $pegawai->EMAIL_PEGAWAI) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="telepon" class="form-label">No. Telepon</label>
                    <input type="text" class="form-control @error('telepon') is-invalid @enderror"
                        id="telepon" name="telepon" value="{{ old('telepon', $pegawai->NO_TELEPON_PEGAWAI) }}" required>
                    @error('telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control @error('alamat') is-invalid @enderror"
                              id="alamat" name="alamat" rows="3" required>{{ old('alamat', $pegawai->ALAMAT_PEGAWAI) }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                           id="tanggal_lahir" name="tanggal_lahir" 
                           value="{{ old('tanggal_lahir', 
                               $pegawai->TANGGAL_LAHIR instanceof \DateTime 
                                   ? $pegawai->TANGGAL_LAHIR->format('Y-m-d') 
                                   : (is_string($pegawai->TANGGAL_LAHIR) && !empty($pegawai->TANGGAL_LAHIR) 
                                       ? date('Y-m-d', strtotime($pegawai->TANGGAL_LAHIR)) 
                                       : '')
                           ) }}">
                    <small class="form-text text-muted">Tanggal lahir akan digunakan untuk reset password (format: ddmmyyyy)</small>
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('gudang.profile') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
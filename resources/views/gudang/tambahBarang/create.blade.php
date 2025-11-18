@extends('layouts.gudang')

@section('title', 'Tambah Barang Baru')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('gudang.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('gudang.stok') }}" class="text-decoration-none">Stok Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Barang</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="card-title mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Barang Baru
                </h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('gudang.barang.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategori as $kat)
                                        <option value="{{ $kat->ID_KATEGORI }}" {{ old('kategori') == $kat->ID_KATEGORI ? 'selected' : '' }}>
                                            {{ $kat->JENIS_KATEGORI }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="harga" name="harga" value="{{ old('harga') }}" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="stok" name="stok" value="{{ old('stok') }}" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Tersedia" {{ old('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                    <option value="Donasi" {{ old('status') == 'Donasi' ? 'selected' : '' }}>Donasi</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="garansi" class="form-label">Garansi <span class="text-danger">*</span></label>
                                <select class="form-select" id="garansi" name="garansi" required>
                                    <option value="">Pilih Garansi</option>
                                    <option value="Ya" {{ old('garansi') == 'Ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="Tidak" {{ old('garansi') == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="foto1" class="form-label">Foto Produk 1</label>
                                <input type="file" class="form-control" id="foto1" name="foto1" accept="image/*">
                                <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>
                            </div>

                            <div class="mb-3">
                                <label for="foto2" class="form-label">Foto Produk 2</label>
                                <input type="file" class="form-control" id="foto2" name="foto2" accept="image/*">
                                <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>
                            </div>

                            <!-- Info Penitipan (akan muncul jika status = Penitipan) -->
                            <div id="penitipan-info" class="alert alert-info" style="display: none;">
                                <h6><i class="fas fa-info-circle me-1"></i>Informasi Penitipan</h6>
                                <div class="mb-2">
                                    <label for="penitip" class="form-label">Penitip <span class="text-danger">*</span></label>
                                    <select class="form-select" id="penitip" name="penitip">
                                        <option value="">Pilih Penitip</option>
                                        @foreach($penitip as $p)
                                            <option value="{{ $p->ID_PENITIP }}" {{ old('penitip') == $p->ID_PENITIP ? 'selected' : '' }}>
                                                {{ $p->NAMA_PENITIP }} - {{ $p->NO_TELEPON_PENITIP }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Informasi otomatis masa penitipan -->
                                <div class="alert alert-light mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Masa Penitipan (Otomatis):</strong><br>
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Tanggal masuk gudang: <strong><span id="tanggal-masuk">{{ date('d/m/Y') }}</span></strong><br>
                                        <i class="fas fa-calendar-check me-1"></i>
                                        Tanggal berakhir: <strong><span id="tanggal-berakhir">{{ date('d/m/Y', strtotime('+30 days')) }}</span></strong><br>
                                        <i class="fas fa-clock me-1"></i>
                                        Durasi: <strong>30 hari</strong><br>
                                        <i class="fas fa-user-tie me-1"></i>
                                        Petugas: <strong>{{ auth()->guard('pegawai')->user()->NAMA_PEGAWAI }}</strong>
                                    </small>
                                </div>
                                
                                <div class="alert alert-warning mt-2">
                                    <small>
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <strong>Catatan Penting:</strong><br>
                                        • Masa penitipan otomatis dihitung 30 hari dari tanggal masuk gudang<br>
                                        • Tanggal berakhir adalah tanggal keluar barang dari penitipan<br>
                                        • Sistem akan mengingatkan 7 hari sebelum masa berakhir<br>
                                        • Anda dapat memperpanjang atau mengubah status penitipan setelah barang tersimpan
                                    </small>
                                </div>
                            </div>

                            <!-- Info Petugas untuk Barang Non-Penitipan -->
                            <div id="barang-info" class="alert alert-light">
                                <h6><i class="fas fa-info-circle me-1"></i>Informasi Barang</h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    Tanggal input: <strong>{{ date('d/m/Y H:i') }}</strong><br>
                                    <i class="fas fa-user-tie me-1"></i>
                                    Petugas input: <strong>{{ auth()->guard('pegawai')->user()->NAMA_PEGAWAI }}</strong><br>
                                    <small class="text-info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Informasi petugas dan tanggal akan tercatat dalam sistem
                                    </small>
                                </small>
                            </div>
                        </div>
                    </div>

                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fas fa-save me-1"></i>Simpan Barang
                            </button>
                            <a href="{{ route('gudang.stok') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
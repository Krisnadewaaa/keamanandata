@extends('layouts.organisasi')

@section('title', 'Buat Request Donasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Request Donasi</h1>
        <a href="{{ route('organisasi.donasi.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Request Donasi</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <h5 class="alert-heading">Petunjuk Pengajuan Request Donasi</h5>
                <p>Silakan isi form di bawah ini dengan deskripsi kebutuhan barang Anda secara detail. Jelaskan untuk apa barang tersebut dibutuhkan oleh organisasi Anda.</p>
                <hr>
                <p class="mb-0">Tim ReUseMart akan meninjau request Anda dan menyediakan barang sesuai ketersediaan.</p>
            </div>
            
            <form action="{{ route('organisasi.donasi.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="isi_request" class="form-label">Isi Request Donasi</label>
                    <textarea class="form-control @error('isi_request') is-invalid @enderror" id="isi_request" name="isi_request" rows="5" required placeholder="Contoh: Kami membutuhkan laptop bekas untuk kegiatan pelatihan komputer bagi anak-anak kurang mampu di komunitas kami.">{{ old('isi_request') }}</textarea>
                    @error('isi_request')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="form-text">
                        Jelaskan secara detail kebutuhan barang dan penggunaannya di organisasi Anda.
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i> Kirim Request Donasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
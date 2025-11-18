@extends('layouts.organisasi')

@section('title', 'Edit Request Donasi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Request Donasi</h1>
        <a href="{{ route('organisasi.donasi.show', $donasi->ID_DONASI) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Request Donasi</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-warning mb-4">
                <h5 class="alert-heading">Perhatian!</h5>
                <p>Anda hanya dapat mengedit request donasi yang belum diproses oleh tim ReUseMart.</p>
                <hr>
                <p class="mb-0">Setelah request diproses dan barang donasi disiapkan, request tidak dapat diedit lagi.</p>
            </div>
            
            <form action="{{ route('organisasi.donasi.update', $donasi->ID_DONASI) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="isi_request" class="form-label">Isi Request Donasi</label>
                    <textarea class="form-control @error('isi_request') is-invalid @enderror" id="isi_request" name="isi_request" rows="5" required>{{ old('isi_request', $donasi->ISI_REQUEST) }}</textarea>
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
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('layouts.cs')

@section('title', 'Verifikasi Pembayaran - ' . $transaction->NOMOR_TRANSAKSI)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Verifikasi Pembayaran</h1>
        <a href="{{ route('cs.payment.verification.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
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

    <div class="row">
        <div class="col-lg-8">
            <!-- Transaction Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Transaksi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>No. Transaksi</strong></td>
                                    <td>:</td>
                                    <td>{{ $transaction->NOMOR_TRANSAKSI }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Transaksi</strong></td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->TANGGAL_TRANSAKSI)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pembeli</strong></td>
                                    <td>:</td>
                                    <td>
                                        {{ $transaction->pembeli->NAMA_PEMBELI }}<br>
                                        <small>{{ $transaction->pembeli->EMAIL_PEMBELI }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Produk</strong></td>
                                    <td>:</td>
                                    <td>{{ $transaction->barang->NAMA_BARANG }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Metode Pembayaran</strong></td>
                                    <td>:</td>
                                    <td>{{ $transaction->METODE_PEMBAYARAN }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Pembayaran</strong></td>
                                    <td>:</td>
                                    <td class="text-success"><strong>Rp {{ number_format($transaction->TOTAL_TRANSAKSI, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Poin Ditukar</strong></td>
                                    <td>:</td>
                                    <td>{{ $transaction->POIN_DITUKAR }} poin</td>
                                </tr>
                                <tr>
                                    <td><strong>Upload Bukti</strong></td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->TANGGAL_UPLOAD_BUKTI)->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Proof -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bukti Pembayaran</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset($transaction->BUKTI_PEMBAYARAN) }}" 
                         alt="Bukti Pembayaran" 
                         class="img-fluid rounded shadow"
                         style="max-width: 500px;">
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Verification Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Verifikasi Pembayaran</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('cs.payment.verification.verify', $transaction->ID_TRANSAKSI) }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="status">Status Verifikasi</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="Valid">Valid - Setujui Pembayaran</option>
                                <option value="Invalid">Invalid - Tolak Pembayaran</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="catatan">Catatan Verifikasi</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" name="catatan" rows="4" 
                                      placeholder="Berikan catatan verifikasi (opsional)">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Perhatian:</h6>
                            <ul class="mb-0">
                                <li><strong>Valid:</strong> Transaksi akan disetujui dan status berubah menjadi "Disiapkan"</li>
                                <li><strong>Invalid:</strong> Transaksi akan dibatalkan, poin dikembalikan, dan stok dipulihkan</li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-check"></i> Proses Verifikasi
                        </button>
                    </form>
                </div>
            </div>

            <!-- Payment Instructions Reference -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Informasi Rekening</h6>
                </div>
                <div class="card-body">
                    <p><strong>Bank BCA</strong></p>
                    <p>No. Rekening: 1234567890</p>
                    <p>Atas Nama: ReUseMart</p>
                    
                    <div class="alert alert-info mt-3">
                        <h6>Checklist Verifikasi:</h6>
                        <ul class="mb-0">
                            <li>Jumlah transfer sesuai</li>
                            <li>Rekening tujuan benar</li>
                            <li>Tanggal transfer valid</li>
                            <li>Nama pengirim jelas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
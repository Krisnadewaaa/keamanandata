@extends('layouts.cs')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Verifikasi Pembayaran</h1>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('cs.payment.verification.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">No. Transaksi</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Cari nomor transaksi...">
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi Menunggu Verifikasi</h6>
        </div>
        <div class="card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Pembeli</th>
                                <th>Produk</th>
                                <th>Total</th>
                                <th>Tanggal Upload</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->NOMOR_TRANSAKSI }}</td>
                                <td>
                                    {{ $transaction->pembeli->NAMA_PEMBELI }}<br>
                                    <small class="text-muted">{{ $transaction->pembeli->EMAIL_PEMBELI }}</small>
                                </td>
                                <td>{{ $transaction->barang->NAMA_BARANG }}</td>
                                <td>Rp {{ number_format($transaction->TOTAL_TRANSAKSI, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->TANGGAL_UPLOAD_BUKTI)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('cs.payment.verification.show', $transaction->ID_TRANSAKSI) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Verifikasi
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{ $transactions->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada transaksi yang menunggu verifikasi.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
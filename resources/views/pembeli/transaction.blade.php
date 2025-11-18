@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    @if(isset($pembeli))
                        <img src="{{ asset($pembeli->FOTO_PROFIL ? $pembeli->FOTO_PROFIL : 'https://via.placeholder.com/150') }}" alt="Foto Profil" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover; border-radius: 0;">
                        <h5>{{ $pembeli->NAMA_PEMBELI }}</h5>
                        <p class="text-muted">{{ $pembeli->EMAIL_PEMBELI }}</p>
                    @endif
                </div>
            </div>
            
            <div class="list-group mt-4">
                <a href="{{ route('pembeli.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('pembeli.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user me-2"></i> Profil
                </a>
                <a href="{{ route('pembeli.transactions') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-shopping-cart me-2"></i> Riwayat Transaksi
                </a>
                <a href="{{ route('pembeli.cart.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-basket me-2"></i> Keranjang
                </a>
                <a href="{{ route('pembeli.alamat.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-map-marker-alt me-2"></i> Alamat
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Riwayat Transaksi</h5>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <form action="{{ route('pembeli.transactions') }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="sort" class="form-label">Urutkan</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Tanggal (Terbaru)</option>
                                    <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Tanggal (Terlama)</option>
                                    <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Nominal (Terbesar)</option>
                                    <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Nominal (Terkecil)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-filter me-2"></i> Filter
                            </button>
                            <a href="{{ route('pembeli.transactions') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Reset
                            </a>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <!-- Transactions Table -->
                    @if(count($transactions) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Produk</th>
                                        <th>Metode Pengiriman</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->NOMOR_TRANSAKSI ?? sprintf("%02d.%02d.%03d", date('y', strtotime($transaction->TANGGAL_TRANSAKSI)), date('m', strtotime($transaction->TANGGAL_TRANSAKSI)), $transaction->ID_TRANSAKSI) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($transaction->TANGGAL_TRANSAKSI)->format('d/m/Y') }}</td>
                                            <td>{{ $transaction->barang->NAMA_BARANG }}</td>
                                            <td>{{ $transaction->METODE_PENGIRIMAN }}</td>
                                            <td>Rp {{ number_format($transaction->TOTAL_TRANSAKSI, 0, ',', '.') }}</td>
                                            <td>
                                                @switch($transaction->STATUS_TRANSAKSI)
                                                    @case('Menunggu Pembayaran')
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="fas fa-clock me-1"></i>Menunggu Pembayaran
                                                        </span>
                                                        @break
                                                    @case('Menunggu Konfirmasi')
                                                        <span class="badge bg-info text-dark">
                                                            <i class="fas fa-hourglass-half me-1"></i>Menunggu Konfirmasi
                                                        </span>
                                                        @break
                                                    @case('Disiapkan')
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-box me-1"></i>Disiapkan
                                                        </span>
                                                        @break
                                                    @case('Dikirim')
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-truck me-1"></i>Dikirim
                                                        </span>
                                                        @break
                                                    @case('Selesai')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Selesai
                                                        </span>
                                                        @break
                                                    @case('Batal')
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times me-1"></i>Dibatalkan
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $transaction->STATUS_TRANSAKSI }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pembeli.transaction.detail', $transaction->ID_TRANSAKSI) }}" 
                                                       class="btn btn-sm btn-outline-success" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($transaction->STATUS_TRANSAKSI == 'Menunggu Pembayaran')
                                                        <a href="{{ route('pembeli.payment.form', $transaction->ID_TRANSAKSI) }}" 
                                                           class="btn btn-sm btn-warning" title="Bayar Sekarang">
                                                            <i class="fas fa-credit-card"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center my-4">
                            <nav>
                                {{ $transactions->appends(request()->query())->links() }}
                            </nav>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Tidak ada transaksi yang ditemukan. Silakan coba filter yang berbeda atau mulai berbelanja.
                        </div>
                        <div class="text-center">
                            <a href="{{ route('barang.index') }}" class="btn btn-success">Jelajahi Produk</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transaction Status Legend -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Keterangan Status Transaksi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <span class="badge bg-warning text-dark me-2">Menunggu Pembayaran</span>
                                    Transaksi menunggu pembayaran dalam 1 menit
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-info text-dark me-2">Menunggu Konfirmasi</span>
                                    Bukti pembayaran sedang diverifikasi CS
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-primary me-2">Disiapkan</span>
                                    Barang sedang disiapkan untuk pengiriman
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <span class="badge bg-primary me-2">Dikirim</span>
                                    Barang sedang dalam pengiriman
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-success me-2">Selesai</span>
                                    Transaksi telah selesai
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-danger me-2">Dibatalkan</span>
                                    Transaksi dibatalkan karena tidak dibayar atau bukti tidak valid
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh for pending transactions every 30 seconds
    setInterval(() => {
        // Check for expired transactions
        fetch('/pembeli/transactions/check-expired', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Refresh page if any transaction status might have changed
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }, 30000); // Check every 30 seconds
});
</script>
@endpush
@endsection
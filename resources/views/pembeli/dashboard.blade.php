@extends('layouts.app')

@section('title', 'Dashboard Pembeli')

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
                <div class="d-grid gap-2">
                    <a href="{{ route('pembeli.edit') }}" class="btn btn-outline-success">
                        <i class="fas fa-edit me-2"></i> Edit Profil
                    </a>
                    <a href="{{ route('pembeli.password') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-key me-2"></i> Ubah Password
                    </a>
                </div>
            </div>
        </div>

        <div class="list-group mt-4">
            <a href="{{ route('pembeli.dashboard') }}" class="list-group-item list-group-item-action active">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a href="{{ route('pembeli.profile') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user me-2"></i> Profil
            </a>
            <a href="{{ route('pembeli.transactions') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-shopping-cart me-2"></i> Riwayat Transaksi
            </a>
            <a href="#" class="list-group-item list-group-item-action">
                <i class="fas fa-heart me-2"></i> Wishlist
            </a>
            <a href="{{ route('pembeli.alamat.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-map-marker-alt me-2"></i> Alamat
                </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">
        <!-- Welcome Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h4>Selamat Datang, {{ Auth::guard('pembeli')->user()->NAMA_PEMBELI }}!</h4>
                        <p class="text-muted mb-0">Temukan berbagai barang bekas berkualitas dengan harga terjangkau di ReUseMart.</p>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('barang.index') }}" class="btn btn-success">
                            <i class="fas fa-search me-2"></i> Jelajahi Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card h-100 border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-coins fa-3x text-success mb-3"></i>
                        <h6>Poin Reward</h6>
                        <h4>{{ Auth::guard('pembeli')->user()->POINT_PEMBELI }}</h4>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="/pembeli/profile" class="btn btn-sm btn-outline-success w-100">Lihat Poin</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                        <h6>Transaksi</h6>
                        <h4>{{ count(Auth::guard('pembeli')->user()->transaksis) }}</h4>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="{{ route('pembeli.transactions') }}" class="btn btn-sm btn-outline-primary w-100">Lihat Transaksi</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-heart fa-3x text-info mb-3"></i>
                        <h6>Wishlist</h6>
                        <h4>0</h4>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="#" class="btn btn-sm btn-outline-info w-100">Lihat Wishlist</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Transaksi Terbaru</h5>
            </div>
            <div class="card-body">
                @if(count(Auth::guard('pembeli')->user()->transaksis) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(Auth::guard('pembeli')->user()->transaksis->take(5) as $transaksi)
                                    <tr>
                                        <td>{{ sprintf("%02d.%02d.%03d", date('y', strtotime($transaksi->TANGGAL_TRANSAKSI)), date('m', strtotime($transaksi->TANGGAL_TRANSAKSI)), $transaksi->ID_TRANSAKSI) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaksi->TANGGAL_TRANSAKSI)->format('d/m/Y') }}</td>
                                        <td>{{ $transaksi->barang->NAMA_BARANG }}</td>
                                        <td>Rp {{ number_format($transaksi->TOTAL_TRANSAKSI, 0, ',', '.') }}</td>
                                        <td>
                                            @switch($transaksi->STATUS_TRANSAKSI)
                                                @case('Menunggu Pembayaran')
                                                    <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                                                    @break
                                                @case('Menunggu Konfirmasi')
                                                    <span class="badge bg-info text-dark">Menunggu Konfirmasi</span>
                                                    @break
                                                @case('Disiapkan')
                                                    <span class="badge bg-primary">Disiapkan</span>
                                                    @break
                                                @case('Selesai')
                                                    <span class="badge bg-success">Selesai</span>
                                                    @break
                                                @case('Hangus')
                                                    <span class="badge bg-danger">Hangus</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $transaksi->STATUS_TRANSAKSI }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <a href="{{ route('pembeli.transaction.detail', $transaksi->ID_TRANSAKSI) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('pembeli.transactions') }}" class="btn btn-outline-success">Lihat Semua Transaksi</a>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Anda belum memiliki transaksi. Jelajahi produk kami dan temukan barang berkualitas dengan harga terjangkau.
                    </div>
                    <div class="text-center">
                        <a href="{{ route('barang.index') }}" class="btn btn-success">Jelajahi Produk</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recommended Products -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Produk Rekomendasi untuk Anda</h5>
            </div>
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach(\App\Models\Barang::where('STATUS', 'Tersedia')->take(3)->get() as $barang)
                        <div class="col">
                            <div class="card h-100">
                                <img src="{{ asset('images/fotoProduk/' . ($barang->foto_produk ?? 'default.jpg')) }}" class="img-fluid card-img-top" alt="{{ $barang->NAMA_BARANG }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $barang->NAMA_BARANG }}</h5>
                                    <p class="card-text text-muted">{{ Str::limit($barang->DESKRIPSI, 50) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-success fw-bold">Rp {{ number_format($barang->HARGA, 0, ',', '.') }}</span>
                                        @if($barang->GARANSI == 'Ya')
                                            <span class="badge bg-info text-dark"><i class="fas fa-shield-alt me-1"></i> Garansi</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="{{ route('barang.show', $barang->ID_BARANG) }}" class="btn btn-outline-success w-100">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
    <!-- Pastikan sudah load toastr.js di layout/app atau di sini -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @if(session('success'))
        <script>
            toastr.success("{{ session('success') }}");
        </script>
    @endif

    @if(session('info'))
        <script>
            toastr.info("{{ session('info') }}");
        </script>
    @endif
@endsection

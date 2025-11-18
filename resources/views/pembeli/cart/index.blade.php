@extends('layouts.app')

@section('title', 'Keranjang Belanja')

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
            <a href="{{ route('pembeli.dashboard') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a href="{{ route('pembeli.profile') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user me-2"></i> Profil
            </a>
            <a href="{{ route('pembeli.transactions') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-shopping-cart me-2"></i> Riwayat Transaksi
            </a>
            <a href="{{ route('pembeli.cart.index') }}" class="list-group-item list-group-item-action active">
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
                <h5 class="mb-0">Keranjang Belanja</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if($cartItems->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h4>Keranjang Anda Kosong</h4>
                        <p class="text-muted">Jelajahi produk kami dan temukan barang bekas berkualitas dengan harga terjangkau.</p>
                        <a href="{{ route('barang.index') }}" class="btn btn-success mt-3">
                            <i class="fas fa-search me-2"></i> Jelajahi Produk
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('images/fotoProduk/' . ($item->barang->foto_produk ?? 'default.jpg')) }}" 
                                                    class="me-3" 
                                                    style="width: 80px; height: 80px; object-fit: cover;" 
                                                    alt="{{ $item->barang->NAMA_BARANG }}">
                                                <div>
                                                    <h6 class="mb-1">{{ $item->barang->NAMA_BARANG }}</h6>
                                                    <p class="text-muted mb-0">{{ Str::limit($item->barang->DESKRIPSI, 50) }}</p>
                                                    @if($item->barang->GARANSI == 'Ya')
                                                        <span class="badge bg-info text-dark"><i class="fas fa-shield-alt me-1"></i> Garansi</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="fw-bold">Rp {{ number_format($item->barang->HARGA, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <form action="{{ route('pembeli.cart.remove', $item->ID_KERANJANG) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')">
                                                    <i class="fas fa-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="mb-3">Ringkasan Belanja</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Biaya Pengiriman 
                                    @if($subtotal >= 1500000)
                                        <span class="badge bg-success">Gratis</span>
                                    @endif
                                </span>
                                <span>{{ $shippingCost > 0 ? 'Rp ' . number_format($shippingCost, 0, ',', '.') : 'Gratis' }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2 fw-bold">
                                <span>Total</span>
                                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="mt-3">
                                <a href="{{ route('pembeli.cart.checkout') }}" class="btn btn-success w-100">
                                    <i class="fas fa-shopping-cart me-2"></i> Lanjut ke Checkout
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        @if(!$cartItems->isEmpty())
            <div class="card">
                <div class="card-body">
                    <h5>Informasi Pengiriman</h5>
                    <ul>
                        <li>Pengiriman kurir hanya tersedia untuk area Yogyakarta.</li>
                        <li>Pengiriman gratis untuk pembelian di atas Rp 1.500.000.</li>
                        <li>Biaya pengiriman untuk pembelian di bawah Rp 1.500.000 adalah Rp 100.000.</li>
                        <li>Opsi "Ambil Sendiri" tersedia, barang dapat diambil di gudang ReUseMart.</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
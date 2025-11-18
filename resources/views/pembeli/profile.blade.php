@extends('layouts.app')

@section('title', 'Profil Pembeli')

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
                <a href="{{ route('pembeli.dashboard') }}" class="list-group-item list-group-item-action action">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('pembeli.profile') }}" class="list-group-item list-group-item-action active">
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
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Nama Lengkap</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ $pembeli->NAMA_PEMBELI }}
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Email</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ $pembeli->EMAIL_PEMBELI }}
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Tanggal Bergabung</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ now()->format('d F Y') }} <!-- In a real app, use the created_at timestamp -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Poin Reward</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card bg-success bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-success mb-2">Total Poin Anda</h6>
                                    <h1 class="mb-0">{{ $pembeli->POINT_PEMBELI }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="mb-2">Poin yang bisa ditukar</h6>
                                    <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tukarPoinModal">Tukar Poin</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h6>Hadiah yang Bisa Didapatkan</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Hadiah</th>
                                    <th>Poin yang Dibutuhkan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($merchandises as $merch)
                                    <tr>
                                        <td>{{ $merch->JENIS_MERCHANDISE }}</td>
                                        <td>{{ $merch->POINT }} poin</td>
                                        <td>
                                            @if($pembeli->POINT_PEMBELI >= $merch->POINT)
                                                <span class="badge bg-success">Tersedia</span>
                                            @else
                                                <span class="badge bg-secondary">Butuh {{ $merch->POINT - $pembeli->POINT_PEMBELI }} poin lagi</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Transaksi Terbaru</h5>
                </div>
                <div class="card-body">
                    @if(count($pembeli->transaksis) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No. Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Produk</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pembeli->transaksis->take(5) as $transaksi)
                                        <tr>
                                            <td>{{ sprintf("%02d.%02d.%03d", date('y'), date('m'), $transaksi->ID_TRANSAKSI) }}</td>
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
                                                    @case('Dikirim')
                                                        <span class="badge bg-primary">Dikirim</span>
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
        </div>
    </div>

    <div class="modal fade" id="tukarPoinModal" tabindex="-1" aria-labelledby="tukarPoinModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('pembeli.tukarPoin') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tukarPoinModalLabel">Tukar Poin Reward</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Total poin Anda: <strong>{{ $pembeli->POINT_PEMBELI }}</strong></p>

                        <div class="mb-3">
                            <label for="hadiah" class="form-label">Pilih Hadiah yang ingin ditukar:</label>
                            <select class="form-select" id="hadiah" name="hadiah" required>
                                <option value="" disabled selected>-- Pilih Hadiah --</option>
                                @foreach($merchandises as $m)
                                    <option value="{{ $m->ID_MERCHANDISE }}|{{ $m->POINT }}">
                                        {{ $m->JENIS_MERCHANDISE }} - {{ $m->POINT }} poin
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-text">Poin Anda akan dipotong sesuai dengan hadiah yang dipilih.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Tukar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
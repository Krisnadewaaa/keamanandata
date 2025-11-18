@extends('layouts.app')

@section('title', 'Dashboard Penitip')

@section('content')
<div class="container mt-4">
    <h2>Dashboard Penitip</h2>

<!-- Form Pencarian -->
<form action="{{ route('penitip.dashboard') }}" method="GET" class="mb-3">
    <input type="text" name="search" placeholder="Cari barang..." class="form-control" value="{{ request('search') }}">
</form>

@if(request('search'))
    @if($penitipanList->isEmpty())
        <p>Barang tidak ditemukan.</p>
    @elseif($penitipanList->count() === 1)
        @php 
            $penitipan = $penitipanList->first(); 
            $barang = $penitipan->barang; 

            $tanggalBerakhir = \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR);
            $tanggalSekarang = \Carbon\Carbon::now();
            $bolehAmbilKembali = $tanggalSekarang->greaterThanOrEqualTo($tanggalBerakhir);
        @endphp

        @if($barang)
            <div class="card mb-4">
                <div class="card-header">
                    <strong>{{ $barang->NAMA_BARANG }}</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $foto1 = $barang->foto_produk;
                            $foto2 = $barang->foto_produk2;
                        @endphp

                        @if($foto1 || $foto2)
                            @if($foto1)
                                <div class="col-md-3">
                                    <img src="{{ asset('images/fotoProduk/' . $foto1) }}"
                                         class="img-fluid rounded"
                                         alt="Foto Produk 1"
                                         style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                            @endif
                            @if($foto2)
                                <div class="col-md-3">
                                    <img src="{{ asset('images/fotoProduk2/' . $foto2) }}"
                                         class="img-fluid rounded"
                                         alt="Foto Produk 2"
                                         style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                            @endif
                        @else
                            <div class="col-md-3">
                                <img src="{{ asset('images/default-product.jpg') }}"
                                     class="img-fluid rounded"
                                     alt="Tidak ada foto"
                                     style="width: 100%; height: 200px; object-fit: cover;">
                            </div>
                        @endif

                        <div class="col-md-6">
                            <p><strong>Deskripsi:</strong> {{ $barang->DESKRIPSI }}</p>

                            <p><strong>Tanggal Berakhir:</strong> {{ $tanggalBerakhir->format('d-m-Y') }}</p>

                            @if ($barang->STATUS === 'Diambil Kembali')
                                <div class="alert alert-success">
                                    Barang sudah diambil kembali, perpanjangan tidak diperlukan.
                                </div>

                            @elseif($barang->STATUS === 'Sold Out')
                                <div class="alert alert-success">
                                    Barang sudah Sold Out.
                                </div>

                            @elseif(\Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR)->greaterThanOrEqualTo(\Carbon\Carbon::today()))
                                <button class="btn btn-warning" disabled>Masih Aktif</button>
                                <button class="btn btn-danger" disabled>Masih Aktif</button>

                            @elseif ($barang->transaksi->first() && $barang->transaksi->first()->STATUS_TRANSAKSI === 'Sedang Dikirim')
                                <div class="alert alert-info">
                                    Barang sedang dalam pengiriman, perpanjangan dan pengambilan sementara tidak bisa dilakukan.
                                </div>

                            @else
                                <div class="d-flex gap-2">
                                    <form action="{{ route('penitip.perpanjang', $penitipan->ID_PENITIPAN) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-warning" {{ $penitipan->STATUS_PERPANJANGAN ? 'disabled' : '' }}>
                                            {{ $penitipan->STATUS_PERPANJANGAN ? 'Sudah Diperpanjang' : 'Perpanjang 30 Hari' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('penitip.ambil_kembali', $penitipan->ID_PENITIPAN) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" {{ $bolehAmbilKembali ? '' : 'disabled' }}>
                                            Ambil Kembali
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <p>Data barang tidak ditemukan untuk penitipan ini.</p>
        @endif

    @else
        <p>Ditemukan {{ $penitipanList->count() }} barang:</p>
        @foreach($penitipanList as $penitipan)
            @php 
                $barang = $penitipan->barang; 
                $tanggalBerakhir = \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR);
                $tanggalSekarang = \Carbon\Carbon::now();
                $bolehAmbilKembali = $tanggalSekarang->greaterThanOrEqualTo($tanggalBerakhir);
            @endphp
            @if($barang)
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>{{ $barang->NAMA_BARANG }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $foto1 = $barang->foto_produk;
                                $foto2 = $barang->foto_produk2;
                            @endphp

                            @if($foto1 || $foto2)
                                @if($foto1)
                                    <div class="col-md-3">
                                        <img src="{{ asset('images/fotoProduk/' . $foto1) }}"
                                             class="img-fluid rounded"
                                             alt="Foto Produk 1"
                                             style="width: 100%; height: 200px; object-fit: cover;">
                                    </div>
                                @endif
                                @if($foto2)
                                    <div class="col-md-3">
                                        <img src="{{ asset('images/fotoProduk2/' . $foto2) }}"
                                             class="img-fluid rounded"
                                             alt="Foto Produk 2"
                                             style="width: 100%; height: 200px; object-fit: cover;">
                                    </div>
                                @endif
                            @else
                                <div class="col-md-3">
                                    <img src="{{ asset('images/default-product.jpg') }}"
                                         class="img-fluid rounded"
                                         alt="Tidak ada foto"
                                         style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                            @endif

                            <div class="col-md-6">
                                <p><strong>Deskripsi:</strong> {{ $barang->DESKRIPSI }}</p>
                                <p><strong>Tanggal Berakhir:</strong> {{ $tanggalBerakhir->format('d-m-Y') }}</p>

                                @if ($barang->STATUS === 'Diambil Kembali')
                                    <div class="alert alert-success">
                                        Barang sudah diambil kembali, perpanjangan tidak diperlukan.
                                    </div>

                                @elseif($barang->STATUS === 'Sold Out')
                                    <div class="alert alert-success">
                                        Barang sudah Sold Out.
                                    </div>

                                @elseif(\Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR)->greaterThanOrEqualTo(\Carbon\Carbon::today()))
                                    <button class="btn btn-warning" disabled>Masih Aktif</button>
                                    <button class="btn btn-danger" disabled>Masih Aktif</button>

                                @elseif ($barang->transaksi->first() && $barang->transaksi->first()->STATUS_TRANSAKSI === 'Sedang Dikirim')
                                    <div class="alert alert-info">
                                        Barang sedang dalam pengiriman, perpanjangan dan pengambilan sementara tidak bisa dilakukan.
                                    </div>

                                @else
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('penitip.perpanjang', $penitipan->ID_PENITIPAN) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning" {{ $penitipan->STATUS_PERPANJANGAN ? 'disabled' : '' }}>
                                                {{ $penitipan->STATUS_PERPANJANGAN ? 'Sudah Diperpanjang' : 'Perpanjang 30 Hari' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('penitip.ambil_kembali', $penitipan->ID_PENITIPAN) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" {{ $bolehAmbilKembali ? '' : 'disabled' }}>
                                                Ambil Kembali
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <p>WOW</p>
            @endif
        @endforeach
    @endif
@else
    <!-- Jika tidak ada pencarian, tampilkan semua -->
    @foreach($penitipanList as $penitipan)
        @php 
            $barang = $penitipan->barang; 
            $tanggalBerakhir = \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR);
            $tanggalSekarang = \Carbon\Carbon::now();
            $bolehAmbilKembali = $tanggalSekarang->greaterThanOrEqualTo($tanggalBerakhir);
        @endphp
        @if($barang)
            <div class="card mb-4">
                <div class="card-header">
                    <strong>{{ $barang->NAMA_BARANG }}</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $foto1 = $barang->foto_produk;
                            $foto2 = $barang->foto_produk2;
                        @endphp

                        @if($foto1 || $foto2)
                            @if($foto1)
                                <div class="col-md-3">
                                    <img src="{{ asset('images/fotoProduk/' . $foto1) }}"
                                         class="img-fluid rounded"
                                         alt="Foto Produk 1"
                                         style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                            @endif
                            @if($foto2)
                                <div class="col-md-3">
                                    <img src="{{ asset('images/fotoProduk2/' . $foto2) }}"
                                         class="img-fluid rounded"
                                         alt="Foto Produk 2"
                                         style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                            @endif
                        @else
                            <div class="col-md-3">
                                <img src="{{ asset('images/default-product.jpg') }}"
                                     class="img-fluid rounded"
                                     alt="Tidak ada foto"
                                     style="width: 100%; height: 200px; object-fit: cover;">
                            </div>
                        @endif

                        <div class="col-md-6">
                            <p><strong>Deskripsi:</strong> {{ $barang->DESKRIPSI }}</p>
                            <p><strong>Tanggal Berakhir:</strong> {{ $tanggalBerakhir->format('d-m-Y') }}</p>

                            @if ($barang->STATUS === 'Diambil Kembali')
                                <div class="alert alert-info mt-2">
                                    Barang telah diambil oleh penitip.
                                </div>
                                
                            @elseif($barang->STATUS === 'Sold Out')
                                <div class="alert alert-success">
                                    Barang sudah Sold Out.
                                </div>

                            @elseif(\Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR)->greaterThanOrEqualTo(\Carbon\Carbon::today()))
                                <button class="btn btn-warning" disabled>Masih Aktif</button>
                                <button class="btn btn-danger" disabled>Masih Aktif</button>

                            @elseif ($barang->transaksi->first() && $barang->transaksi->first()->STATUS_TRANSAKSI === 'Sedang Dikirim')
                                <div class="alert alert-info mt-2">
                                    Barang sedang dalam pengiriman.
                                </div>

                            @else
                                <div class="d-flex gap-2">
                                    <form action="{{ route('penitip.perpanjang', $penitipan->ID_PENITIPAN) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-warning" {{ $penitipan->STATUS_PERPANJANGAN ? 'disabled' : '' }}>
                                            {{ $penitipan->STATUS_PERPANJANGAN ? 'Sudah Diperpanjang' : 'Perpanjang 30 Hari' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('penitip.ambil_kembali', $penitipan->ID_PENITIPAN) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" {{ $bolehAmbilKembali ? '' : 'disabled' }}>
                                            Ambil Kembali
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- <p>Data barang tidak ditemukan untuk penitipan dengan ID {{ $penitipan->ID_PENITIPAN }}.</p> --}}
            <p></p>
        @endif
    @endforeach
@endif

    
</div>
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    @if(Auth::guard('penitip')->user()->FOTO_KTP)
                        <img src="{{ asset('storage/' . Auth::guard('penitip')->user()->FOTO_KTP) }}" class="img-thumbnail" alt="Foto KTP" width="150">
                    @else
                        <img src="{{ asset('images/default-ktp.jpg') }}" class="img-thumbnail" alt="Foto KTP Default" width="150">
                    @endif
                    <h5>{{ Auth::guard('penitip')->user()->NAMA_PENITIP }}</h5>
                    <p class="text-muted">{{ Auth::guard('penitip')->user()->EMAIL_PENITIP }}</p>
                    <div class="d-flex justify-content-center mb-2">
                        <div class="badge bg-success rounded-pill px-3 py-2">
                            <i class="fas fa-star me-1"></i> Rating: {{ number_format(Auth::guard('penitip')->user()->RATING_PENITIP, 1) }}
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('penitip.edit') }}" class="btn btn-outline-success">
                            <i class="fas fa-edit me-2"></i> Edit Profil
                        </a>
                        <a href="{{ route('penitip.password') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-key me-2"></i> Ubah Password
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="list-group mt-4">
                <a href="{{ route('penitip.dashboard') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('penitip.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user me-2"></i> Profil
                </a>
                <a href="{{ route('penitip.consignments') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-box me-2"></i> Riwayat Penitipan
                </a>
                <a href="{{ route('penitip.sales') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-chart-line me-2"></i> Riwayat Penjualan
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
                            <h4>Selamat Datang, {{ Auth::guard('penitip')->user()->NAMA_PENITIP }}!</h4>
                            <p class="text-muted mb-0">Titipkan barang bekas Anda di ReUseMart dan dapatkan keuntungan dari barang yang tidak terpakai.</p>
                        </div>
                        <div class="ms-auto">
                            <a href="#" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i> Titipkan Barang
                            </a>
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card h-100 border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-wallet fa-3x text-success mb-3"></i>
                            <h6>Saldo</h6>
                            <h4>Rp {{ number_format(Auth::guard('penitip')->user()->UANG_PENITIP, 0, ',', '.') }}</h4>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="#" class="btn btn-sm btn-outline-success w-100">Tarik Saldo</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card h-100 border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-box fa-3x text-primary mb-3"></i>
                            <h6>Barang Dititipkan</h6>
                            <h4>{{ count(Auth::guard('penitip')->user()->penitipan ?? []) }}</h4>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="{{ route('penitip.consignments') }}" class="btn btn-sm btn-outline-primary w-100">Lihat Penitipan</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card h-100 border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-3x text-info mb-3"></i>
                            <h6>Barang Terjual</h6>
                            <h4>{{ Auth::guard('penitip')->user()->penitipan->where('STATUS_PENITIPAN', 'Selesai')->count() }}</h4>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="{{ route('penitip.sales') }}" class="btn btn-sm btn-outline-info w-100">Lihat Penjualan</a>
                        </div>
                    </div>
                </div>
                 <!-- Kartu Poin di sebelah kiri -->
            <div class="col-md-3">
                <div class="card bg-secondary bg-opacity-10">
                    <div class="card-body text-center">
                        <h6 class="text-secondary mb-2">Total Poin Donasi</h6>
                        <h1 class="mb-0">{{ $penitip->POIN_PENITIP ?? 0 }} poin</h1>
                        <p class="text-muted small mb-2">1 Poin = Rp10.000</p>

                        @php
                            $konversiSaldo = ($penitip->POIN_PENITIP ?? 0) * 10000;
                        @endphp

                        @if(($penitip->POIN_PENITIP ?? 0) > 0)
                            <form action="{{ route('penitip.tukar.poin') }}" method="POST">
                                @csrf
                                <input type="hidden" name="jumlah_poin" value="{{ $penitip->POIN_PENITIP }}">
                                <button type="submit" class="btn btn-secondary">
                                    Tukar Poin Menjadi Rp {{ number_format($konversiSaldo, 0, ',', '.') }}
                                </button>
                            </form>
                        @else
                            <button class="btn btn-outline-secondary" disabled>Tidak ada poin untuk ditukar</button>
                        @endif
                    </div>
                </div>
            </div>
                <div class="col-md-3">
                    <div class="card h-100 border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-star fa-3x text-warning mb-3"></i>
                            <h6>Rating</h6>
                            <h4>{{ number_format(Auth::guard('penitip')->user()->RATING_PENITIP, 1) }}</h4>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= Auth::guard('penitip')->user()->RATING_PENITIP)
                                        <i class="fas fa-star"></i>
                                    @elseif($i <= Auth::guard('penitip')->user()->RATING_PENITIP + 0.5)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <form action="{{ route('penitip.update.rating') }}" method="POST" onsubmit="return confirm('Yakin ingin memperbarui rating penitip?')">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-sync-alt"></i> Update Rating Penitip
                            </button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Active Consignments -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Penitipan Aktif</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('penitip.dashboard') }}" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama Barang..." value="{{ request('search') }}">
                            <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                    
                    @php
                        $activeConsignments = Auth::guard('penitip')->user()->penitipan->where('STATUS_PENITIPAN', 'Aktif');
                    @endphp
                    
                    @if(($activeConsignments ?? collect())->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Berakhir</th>
                                        <th>Sisa Waktu</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeConsignments as $consignment)
                                        <tr>
                                            {{-- <td>{{ sprintf("%s%03d", substr($consignment->barang->NAMA_BARANG, 0, 1), $consignment->ID_PENITIPAN) }}</td>
                                            <td>{{ $consignment->barang->NAMA_BARANG }}</td> --}}
                                            <td>
                                                {{ $consignment->barang && $consignment->barang->NAMA_BARANG
                                                    ? sprintf("%s%03d", substr($consignment->barang->NAMA_BARANG, 0, 1), $consignment->ID_PENITIPAN)
                                                    : '-' }}
                                            </td>
                                            <td>
                                                {{ $consignment->barang->NAMA_BARANG ?? '-' }}
                                            </td>

                                            <td>{{ \Carbon\Carbon::parse($consignment->TANGGAL_MULAI)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($consignment->TANGGAL_BERAKHIR)->format('d/m/Y') }}</td>
                                            <td>
                                                @php
                                                    $daysLeft = \Carbon\Carbon::now()->diffInDays($consignment->TANGGAL_BERAKHIR, false);
                                                @endphp
                                                @if($daysLeft > 7)
                                                    <span class="text-success">{{ $daysLeft }} hari</span>
                                                @elseif($daysLeft > 0)
                                                    <span class="text-warning">{{ $daysLeft }} hari</span>
                                                @elseif($daysLeft == 0)
                                                    <span class="text-danger">Berakhir hari ini</span>
                                                @else
                                                    <span class="text-danger">Berakhir {{ abs($daysLeft) }} hari lalu</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('penitip.consignments.detail', $consignment->ID_PENITIPAN) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('penitip.consignments') }}" class="btn btn-outline-success">Lihat Semua Penitipan</a>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Anda belum memiliki penitipan aktif. Mulai titipkan barang Anda dan dapatkan keuntungan dari barang bekas yang tidak terpakai.
                        </div>
                        <div class="text-center">
                            <a href="#" class="btn btn-success">Titipkan Barang</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Recent Sales -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Penjualan Terbaru</h5>
                </div>
                <div class="card-body">
                    @php
                        $penitipanIds = Auth::guard('penitip')->user()->penitipan->pluck('ID_PENITIPAN');
                        $barangIds = \App\Models\Barang::whereIn('ID_PENITIPAN', $penitipanIds)->pluck('ID_BARANG');
                        $recentSales = \App\Models\Transaksi::whereIn('ID_BARANG', $barangIds)
                                            ->where('STATUS_TRANSAKSI', 'Selesai')
                                            ->orderBy('TANGGAL_TRANSAKSI', 'desc')
                                            ->take(5)
                                            ->get();
                    @endphp
                    
                    @if(($recentSales ?? collect())->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No. Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Produk</th>
                                        <th>Harga Jual</th>
                                        <th>Pendapatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSales as $sale)
                                        <tr>
                                            <td>{{ sprintf("%02d.%02d.%03d", date('y', strtotime($sale->TANGGAL_TRANSAKSI)), date('m', strtotime($sale->TANGGAL_TRANSAKSI)), $sale->ID_TRANSAKSI) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sale->TANGGAL_TRANSAKSI)->format('d/m/Y') }}</td>
                                            <td>{{ $sale->barang->NAMA_BARANG }}</td>
                                            <td>Rp {{ number_format($sale->TOTAL_TRANSAKSI, 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $commission = $sale->komisi->KOMISI_REUSEMART + $sale->komisi->KOMISI_PEGAWAI;
                                                    $income = $sale->TOTAL_TRANSAKSI - $commission;
                                                    if($sale->komisi->KOMISI_PENITIP) {
                                                        $income += $sale->komisi->KOMISI_PENITIP;
                                                    }
                                                @endphp
                                                Rp {{ number_format($income, 0, ',', '.') }}
                                                @if($sale->komisi->KOMISI_PENITIP)
                                                    <span class="badge bg-warning text-dark">+Bonus</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('penitip.sale.detail', $sale->ID_TRANSAKSI) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('penitip.sales') }}" class="btn btn-outline-success">Lihat Semua Penjualan</a>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Belum ada penjualan. Mulai titipkan barang Anda dan tunggu hingga terjual.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Tips for Consignors -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Tips untuk Penitip</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 bg-success bg-opacity-10">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-camera me-2 text-success"></i> Foto yang Bagus</h5>
                                    <p class="card-text">Pastikan barang Anda memiliki foto yang jelas dan menarik. Foto dengan pencahayaan yang baik dan menampilkan barang dari berbagai sudut akan meningkatkan peluang penjualan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 bg-primary bg-opacity-10">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-tag me-2 text-primary"></i> Harga yang Tepat</h5>
                                    <p class="card-text">Tentukan harga yang kompetitif untuk barang Anda. Harga yang terlalu tinggi akan mengurangi minat pembeli, sedangkan harga yang terlalu rendah akan mengurangi keuntungan Anda.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 bg-info bg-opacity-10">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-broom me-2 text-info"></i> Bersihkan Barang</h5>
                                    <p class="card-text">Pastikan barang dalam kondisi bersih dan terawat sebelum dititipkan. Barang yang bersih dan terawat lebih menarik minat pembeli dan dapat dijual dengan harga lebih tinggi.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 bg-warning bg-opacity-10">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-file-alt me-2 text-warning"></i> Deskripsi Lengkap</h5>
                                    <p class="card-text">Berikan deskripsi yang detail tentang barang, termasuk kondisi, usia, dan keunggulan barang. Deskripsi yang jujur dan lengkap akan membangun kepercayaan pembeli.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
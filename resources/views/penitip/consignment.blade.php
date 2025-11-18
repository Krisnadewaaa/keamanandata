@extends('layouts.app')

@section('title', 'Riwayat Penitipan')

@section('content')
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Profile Picture">
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
                <a href="{{ route('penitip.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user me-2"></i> Profil
                </a>
                <a href="{{ route('penitip.consignments') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-box me-2"></i> Riwayat Penitipan
                </a>
                {{-- <a href="{{ route('penitip.sales') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-chart-line me-2"></i> Riwayat Penjualan
                </a> --}}
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Riwayat Penitipan</h5>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <form action="{{ route('penitip.consignments') }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="Donasi" {{ request('status') == 'Donasi' ? 'selected' : '' }}>Donasi</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="sort" class="form-label">Urutkan</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="date_desc" {{ request('sort') == 'date_desc' || !request('sort') ? 'selected' : '' }}>Tanggal (Terbaru)</option>
                                    <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Tanggal (Terlama)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-filter me-2"></i> Filter
                            </button>
                            <a href="{{ route('penitip.consignments') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Reset
                            </a>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <!-- Consignments Table -->
                    @if(count($consignments) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Berakhir</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($consignments as $consignment)
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
                                                @switch($consignment->STATUS_PENITIPAN)
                                                    @case('Aktif')
                                                        <span class="badge bg-success">Aktif</span>
                                                        @break
                                                    @case('Selesai')
                                                        <span class="badge bg-primary">Selesai</span>
                                                        @break
                                                    @case('Donasi')
                                                        <span class="badge bg-info">Donasi</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $consignment->STATUS_PENITIPAN }}</span>
                                                @endswitch
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
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $consignments->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Tidak ada penitipan yang ditemukan. Silakan coba filter yang berbeda atau mulai titipkan barang Anda sekarang.
                        </div>
                        <div class="text-center">
                            <a href="#" class="btn btn-success">Titipkan Barang</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informasi Penitipan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-box fa-3x text-success mb-3"></i>
                                    <h6>Total Barang Dititipkan</h6>
                                    <h4>{{ count($consignments) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-3x text-primary mb-3"></i>
                                    <h6>Barang Terjual</h6>
                                    <h4>{{ $consignments->where('STATUS_PENITIPAN', 'Selesai')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-hand-holding-heart fa-3x text-info mb-3"></i>
                                    <h6>Barang Didonasikan</h6>
                                    <h4>{{ $consignments->where('STATUS_PENITIPAN', 'Donasi')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Masa penitipan barang di ReUseMart adalah 30 hari. Jika barang tidak terjual dalam 30 hari, Anda dapat memperpanjang masa penitipan 1 kali (30 hari) atau mengambil barang Anda kembali. Jika barang tidak diambil dalam 7 hari setelah masa penitipan berakhir, barang akan didonasikan ke organisasi sosial.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
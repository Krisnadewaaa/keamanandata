@extends('layouts.app')

@section('title', 'Riwayat Penjualan')

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
                <a href="{{ route('penitip.consignments') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-box me-2"></i> Riwayat Penitipan
                </a>
                <a href="{{ route('penitip.sales') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-chart-line me-2"></i> Riwayat Penjualan
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Riwayat Penjualan</h5>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <form action="{{ route('penitip.sales') }}" method="GET" class="mb-4">
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
                                    <option value="date_desc" {{ request('sort') == 'date_desc' || !request('sort') ? 'selected' : '' }}>Tanggal (Terbaru)</option>
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
                            <a href="{{ route('penitip.sales') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Reset
                            </a>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <!-- Sales Table -->
                    @if(count($sales) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Produk</th>
                                        <th>Harga Jual</th>
                                        <th>Komisi ReUseMart</th>
                                        <th>Pendapatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sales as $sale)
                                        <tr>
                                            <td>{{ sprintf("%02d.%02d.%03d", date('y', strtotime($sale->TANGGAL_TRANSAKSI)), date('m', strtotime($sale->TANGGAL_TRANSAKSI)), $sale->ID_TRANSAKSI) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sale->TANGGAL_TRANSAKSI)->format('d/m/Y') }}</td>
                                            <td>{{ $sale->barang->NAMA_BARANG }}</td>
                                            <td>Rp {{ number_format($sale->TOTAL_TRANSAKSI, 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $commission = $sale->komisi->KOMISI_REUSEMART + $sale->komisi->KOMISI_PEGAWAI;
                                                @endphp
                                                Rp {{ number_format($commission, 0, ',', '.') }}
                                                <small class="text-muted">
                                                    @php
                                                        $percentage = ($commission / $sale->TOTAL_TRANSAKSI) * 100;
                                                    @endphp
                                                    ({{ number_format($percentage, 0) }}%)
                                                </small>
                                            </td>
                                            <td>
                                                @php
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
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                                        <td><strong>Rp {{ number_format($sales->sum('TOTAL_TRANSAKSI'), 0, ',', '.') }}</strong></td>
                                        <td>
                                            @php
                                                $totalCommission = $sales->sum(function($sale) {
                                                    return $sale->komisi->KOMISI_REUSEMART + $sale->komisi->KOMISI_PEGAWAI;
                                                });
                                            @endphp
                                            <strong>Rp {{ number_format($totalCommission, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $totalIncome = $sales->sum(function($sale) {
                                                    $commission = $sale->komisi->KOMISI_REUSEMART + $sale->komisi->KOMISI_PEGAWAI;
                                                    $income = $sale->TOTAL_TRANSAKSI - $commission;
                                                    if($sale->komisi->KOMISI_PENITIP) {
                                                        $income += $sale->komisi->KOMISI_PENITIP;
                                                    }
                                                    return $income;
                                                });
                                            @endphp
                                            <strong>Rp {{ number_format($totalIncome, 0, ',', '.') }}</strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $sales->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Tidak ada riwayat penjualan yang ditemukan. Silakan coba filter yang berbeda atau tunggu hingga barang Anda terjual.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Statistik Penjualan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h6>Total Barang Terjual</h6>
                                    <h4>{{ count($sales) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-money-bill-wave fa-3x text-primary mb-3"></i>
                                    <h6>Total Pendapatan</h6>
                                    @php
                                        $totalIncome = $sales->sum(function($sale) {
                                            $commission = $sale->komisi->KOMISI_REUSEMART + $sale->komisi->KOMISI_PEGAWAI;
                                            $income = $sale->TOTAL_TRANSAKSI - $commission;
                                            if($sale->komisi->KOMISI_PENITIP) {
                                                $income += $sale->komisi->KOMISI_PENITIP;
                                            }
                                            return $income;
                                        });
                                    @endphp
                                    <h4>Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-gift fa-3x text-warning mb-3"></i>
                                    <h6>Total Bonus</h6>
                                    @php
                                        $totalBonus = $sales->sum(function($sale) {
                                            return $sale->komisi->KOMISI_PENITIP ?? 0;
                                        });
                                    @endphp
                                    <h4>Rp {{ number_format($totalBonus, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Ketentuan Komisi:
                        <ul class="mb-0 mt-2">
                            <li>Komisi ReUseMart sebesar 20% dari harga jual untuk penitipan pertama.</li>
                            <li>Komisi ReUseMart sebesar 30% dari harga jual untuk penitipan yang diperpanjang.</li>
                            <li>Bonus 10% dari komisi jika barang terjual dalam waktu kurang dari 7 hari.</li>
                            <li>Hunter akan mendapatkan komisi 5% dari harga jual jika barang adalah hasil hunting.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
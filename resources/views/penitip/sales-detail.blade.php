@extends('layouts.app')

@section('title', 'Detail Penjualan')

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
            <div class="mb-4">
                <a href="{{ route('penitip.sales') }}" class="btn btn-outline-secondary mb-3">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Riwayat Penjualan
                </a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Penjualan</h5>
                    <div>
                        <span class="badge bg-success">Selesai</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informasi Transaksi</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%">No. Transaksi</td>
                                    <td width="5%">:</td>
                                    <td width="55%">{{ sprintf("%02d.%02d.%03d", date('y', strtotime($sale->TANGGAL_TRANSAKSI)), date('m', strtotime($sale->TANGGAL_TRANSAKSI)), $sale->ID_TRANSAKSI) }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Transaksi</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($sale->TANGGAL_TRANSAKSI)->format('d F Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td>Metode Pembayaran</td>
                                    <td>:</td>
                                    <td>{{ $sale->METODE_PEMBAYARAN }}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge bg-success">{{ $sale->STATUS_TRANSAKSI }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informasi Pengiriman</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%">Metode Pengiriman</td>
                                    <td width="5%">:</td>
                                    <td width="55%">{{ $sale->METODE_PENGIRIMAN }}</td>
                                </tr>
                                <tr>
                                    <td>Status Pengiriman</td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge bg-success">{{ $sale->STATUS_PENGIRIMAN }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Biaya Pengiriman</td>
                                    <td>:</td>
                                    <td>{{ $sale->BIAYA_PENGIRIMAN ? 'Rp ' . number_format($sale->BIAYA_PENGIRIMAN, 0, ',', '.') : 'Gratis' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="text-muted mb-3">Detail Produk</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/80x80/28a745/FFFFFF?text={{ urlencode($sale->barang->NAMA_BARANG) }}" class="me-3" alt="{{ $sale->barang->NAMA_BARANG }}">
                                            <div>
                                                <h6 class="mb-1">{{ $sale->barang->NAMA_BARANG }}</h6>
                                                <small class="text-muted">{{ Str::limit($sale->barang->DESKRIPSI, 50) }}</small>
                                                @if($sale->barang->GARANSI == 'Ya')
                                                    <div>
                                                        <span class="badge bg-info text-dark"><i class="fas fa-shield-alt me-1"></i> Garansi</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format($sale->TOTAL_TRANSAKSI, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <h6 class="text-muted mb-3">Rincian Komisi</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td width="30%">Harga Jual</td>
                                    <td width="70%">Rp {{ number_format($sale->TOTAL_TRANSAKSI, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Komisi ReUseMart</td>
                                    <td class="text-danger">- Rp {{ number_format($sale->komisi->KOMISI_REUSEMART, 0, ',', '.') }}</td>
                                </tr>
                                @if($sale->komisi->KOMISI_PEGAWAI > 0)
                                    <tr>
                                        <td>Komisi Hunter</td>
                                        <td class="text-danger">- Rp {{ number_format($sale->komisi->KOMISI_PEGAWAI, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                @if($sale->komisi->KOMISI_PENITIP > 0)
                                    <tr>
                                        <td>Bonus Penjualan Cepat</td>
                                        <td class="text-success">+ Rp {{ number_format($sale->komisi->KOMISI_PENITIP, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                <tr class="table-light">
                                    <td><strong>Total Diterima</strong></td>
                                    <td>
                                        <strong>
                                            @php
                                                $totalReceived = $sale->TOTAL_TRANSAKSI - $sale->komisi->KOMISI_REUSEMART - $sale->komisi->KOMISI_PEGAWAI;
                                                if($sale->komisi->KOMISI_PENITIP > 0) {
                                                    $totalReceived += $sale->komisi->KOMISI_PENITIP;
                                                }
                                            @endphp
                                            Rp {{ number_format($totalReceived, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-success mt-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">Penjualan Berhasil</h5>
                                @if($sale->komisi->KOMISI_PENITIP > 0)
                                    <p class="mb-0">Selamat! Barang Anda terjual dengan cepat (kurang dari 7 hari) dan Anda mendapatkan bonus sebesar Rp {{ number_format($sale->komisi->KOMISI_PENITIP, 0, ',', '.') }}.</p>
                                @else
                                    <p class="mb-0">Barang Anda telah berhasil terjual. Dana telah ditambahkan ke saldo Anda.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="text-end">
                        <button type="button" class="btn btn-success" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> Cetak Rincian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
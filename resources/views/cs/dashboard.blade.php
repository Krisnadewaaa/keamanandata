{{-- File: resources/views/cs/dashboard.blade.php --}}

@extends('layouts.cs')

@section('title', 'Dashboard Customer Service')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard Customer Service</h1>
            <p class="mb-0 text-muted">Selamat datang, {{ $pegawai->NAMA_PEGAWAI }}! Kelola operasional CS dari sini.</p>
        </div>
        <div class="d-none d-sm-inline-block">
            <span class="badge bg-success">Online</span>
            <small class="text-muted ms-2">{{ now()->format('d M Y, H:i') }}</small>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card stats-primary h-100">
                <div class="card-body">
                    <div class="stats-icon">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                    <div class="text-center">
                        <h5 class="card-title">Total Penitip</h5>
                        <h2 class="display-6 fw-bold">{{ $totalPenitip }}</h2>
                        <p class="card-text text-muted">Penitip terdaftar</p>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    <a href="{{ route('cs.penitip.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card stats-success h-100">
                <div class="card-body">
                    <div class="stats-icon">
                        <i class="fas fa-comments fa-2x text-white"></i>
                    </div>
                    <div class="text-center">
                        <h5 class="card-title">Diskusi Baru</h5>
                        <h2 class="display-6 fw-bold">{{ $totalDiskusiBelumDijawab ?? 0 }}</h2>
                        <p class="card-text text-muted">Menunggu jawaban</p>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    <a href="{{ route('cs.diskusi.index') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-arrow-right me-1"></i> Jawab Diskusi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card stats-warning h-100">
                <div class="card-body">
                    <div class="stats-icon">
                        <i class="fas fa-credit-card fa-2x text-white"></i>
                    </div>
                    <div class="text-center">
                        <h5 class="card-title">Verifikasi Pembayaran</h5>
                        <h2 class="display-6 fw-bold">{{ \App\Models\Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')->whereNotNull('BUKTI_PEMBAYARAN')->count() }}</h2>
                        <p class="card-text text-muted">Menunggu verifikasi</p>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    <a href="{{ route('cs.payment.verification.index') }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-arrow-right me-1"></i> Verifikasi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card stats-info h-100">
                <div class="card-body">
                    <div class="stats-icon">
                        <i class="fas fa-shopping-cart fa-2x text-white"></i>
                    </div>
                    <div class="text-center">
                        <h5 class="card-title">Transaksi Hari Ini</h5>
                        <h2 class="display-6 fw-bold">{{ \App\Models\Transaksi::whereDate('TANGGAL_TRANSAKSI', \Carbon\Carbon::today())->count() }}</h2>
                        <p class="card-text text-muted">Transaksi baru</p>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    <a href="#" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-arrow-right me-1"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Aksi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('cs.payment.verification.index') }}" class="btn btn-warning w-100 h-100 d-flex flex-column justify-content-center quick-action-btn">
                                <i class="fas fa-credit-card fa-2x mb-2"></i>
                                <span>Verifikasi Pembayaran</span>
                                @php
                                    $pendingCount = \App\Models\Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')->whereNotNull('BUKTI_PEMBAYARAN')->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="badge bg-danger mt-1">{{ $pendingCount }} pending</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('cs.penitip.create') }}" class="btn btn-success w-100 h-100 d-flex flex-column justify-content-center quick-action-btn">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <span>Tambah Penitip Baru</span>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('cs.diskusi.index') }}" class="btn btn-info w-100 h-100 d-flex flex-column justify-content-center quick-action-btn">
                                <i class="fas fa-comments fa-2x mb-2"></i>
                                <span>Jawab Diskusi</span>
                                @if($totalDiskusiBelumDijawab > 0)
                                    <span class="badge bg-danger mt-1">{{ $totalDiskusiBelumDijawab }} baru</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('cs.penitip.index') }}" class="btn btn-primary w-100 h-100 d-flex flex-column justify-content-center quick-action-btn">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <span>Kelola Penitip</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payment Verifications -->
    @php
        $pendingPayments = \App\Models\Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')
                                              ->whereNotNull('BUKTI_PEMBAYARAN')
                                              ->with(['pembeli', 'barang'])
                                              ->orderBy('TANGGAL_UPLOAD_BUKTI', 'asc')
                                              ->take(5)
                                              ->get();
    @endphp
    
    @if($pendingPayments->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pembayaran Menunggu Verifikasi ({{ $pendingPayments->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Pembeli</th>
                                    <th>Produk</th>
                                    <th>Total</th>
                                    <th>Upload</th>
                                    <th>Waktu Tunggu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingPayments as $payment)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $payment->NOMOR_TRANSAKSI }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-bold">{{ $payment->pembeli->NAMA_PEMBELI }}</span><br>
                                            <small class="text-muted">{{ $payment->pembeli->EMAIL_PEMBELI }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $payment->barang->NAMA_BARANG }}">
                                            {{ $payment->barang->NAMA_BARANG }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            Rp {{ number_format($payment->TOTAL_TRANSAKSI, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ \Carbon\Carbon::parse($payment->TANGGAL_UPLOAD_BUKTI)->format('d/m H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $waitingTime = \Carbon\Carbon::parse($payment->TANGGAL_UPLOAD_BUKTI)->diffForHumans();
                                            $waitingMinutes = \Carbon\Carbon::parse($payment->TANGGAL_UPLOAD_BUKTI)->diffInMinutes();
                                        @endphp
                                        <span class="badge {{ $waitingMinutes > 30 ? 'bg-danger' : ($waitingMinutes > 15 ? 'bg-warning text-dark' : 'bg-success') }}">
                                            {{ $waitingTime }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('cs.payment.verification.show', $payment->ID_TRANSAKSI) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-eye me-1"></i> Verifikasi
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <a href="{{ route('cs.payment.verification.index') }}" class="btn btn-warning">
                            <i class="fas fa-credit-card me-2"></i>Lihat Semua Verifikasi
                            <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Content Grid -->
    <div class="row">
        <!-- Penitip Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Penitip Terbaru
                    </h5>
                    <span class="badge bg-primary">{{ $penitipTerbaru->count() }}</span>
                </div>
                <div class="card-body">
                    @if($penitipTerbaru->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Rating</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($penitipTerbaru as $penitip)
                                    <tr>
                                        <td>{{ $penitip->NAMA_PENITIP }}</td>
                                        <td>
                                            <small class="text-muted">{{ $penitip->EMAIL_PENITIP }}</small>
                                        </td>
                                        <td>
                                            <div class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $penitip->RATING_PENITIP)
                                                        <i class="fas fa-star"></i>
                                                    @elseif($i - 0.5 <= $penitip->RATING_PENITIP)
                                                        <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                                <small class="text-muted ms-1">({{ number_format($penitip->RATING_PENITIP, 1) }})</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('cs.penitip.show', $penitip->ID_PENITIP) }}" 
                                                   class="btn btn-outline-primary" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('cs.penitip.edit', $penitip->ID_PENITIP) }}" 
                                                   class="btn btn-outline-success" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada penitip terdaftar</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent text-end">
                    <a href="{{ route('cs.penitip.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-users me-1"></i> Lihat Semua Penitip
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Diskusi Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>Diskusi Terbaru
                    </h5>
                    @if($totalDiskusiBelumDijawab > 0)
                        <span class="badge bg-danger">{{ $totalDiskusiBelumDijawab }} belum dijawab</span>
                    @else
                        <span class="badge bg-success">Semua terjawab</span>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($diskusiBaru) && $diskusiBaru->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($diskusiBaru as $diskusi)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-primary d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <h6 class="mb-0">{{ $diskusi->NAMA_PENGIRIM }}</h6>
                                                @if($diskusi->created_at)
                                                    <small class="text-muted ms-2">{{ \Carbon\Carbon::parse($diskusi->created_at)->diffForHumans() }}</small>
                                                @else
                                                    <small class="text-muted ms-2">Baru saja</small>
                                                @endif
                                            </div>
                                            <p class="mb-2 text-muted">{{ Str::limit($diskusi->KOMENTAR, 80) }}</p>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    @if($diskusi->barang)
                                                        <span class="badge bg-light text-dark">
                                                            <i class="fas fa-tag me-1"></i> 
                                                            {{ Str::limit($diskusi->barang->NAMA_BARANG, 20) }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-exclamation-triangle me-1"></i> Barang tidak tersedia
                                                        </span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('cs.diskusi.reply', $diskusi->ID_DISKUSI) }}" 
                                                   class="btn btn-success btn-sm">
                                                    <i class="fas fa-reply me-1"></i> Balas
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr class="my-2">
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada diskusi baru yang perlu ditanggapi</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent text-end">
                    <a href="{{ route('cs.diskusi.index') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-comments me-1"></i> Lihat Semua Diskusi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Ringkasan Aktivitas Hari Ini
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="fw-bold text-primary">{{ \App\Models\Transaksi::whereDate('TANGGAL_TRANSAKSI', today())->count() }}</h4>
                                <p class="text-muted mb-0">Transaksi Baru</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="fw-bold text-success">{{ \App\Models\Transaksi::whereDate('TANGGAL_VERIFIKASI', today())->where('STATUS_VERIFIKASI', 'Valid')->count() }}</h4>
                                <p class="text-muted mb-0">Pembayaran Diverifikasi</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border-end">
                                <h4 class="fw-bold text-info">{{ \App\Models\Diskusi::whereDate('created_at', today())->count() }}</h4>
                                <p class="text-muted mb-0">Diskusi Baru</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            {{-- GANTI: Menggunakan ID_PENITIP terbesar sebagai proxy untuk "penitip baru" --}}
                            @php
                                $maxPenitipId = \App\Models\Penitip::max('ID_PENITIP') ?? 0;
                                $newPenitipCount = \App\Models\Penitip::where('ID_PENITIP', '>', $maxPenitipId - 5)->count(); // 5 penitip terbaru sebagai "hari ini"
                            @endphp
                            <h4 class="fw-bold text-warning">{{ $newPenitipCount }}</h4>
                            <p class="text-muted mb-0">Penitip Terbaru</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
    }
    
    .quick-action-btn {
        min-height: 120px;
        padding: 1.5rem;
    }
    
    .border-end {
        border-right: 1px solid #dee2e6;
    }
    
    @media (max-width: 768px) {
        .border-end {
            border-right: none;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .border-end:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh pending payments count every 30 seconds
    setInterval(() => {
        fetch('/check-expired-transactions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.cancelled > 0) {
                // Show toast notification
                if (window.showToast) {
                    window.showToast(`${data.cancelled} transaksi dibatalkan karena expired`, 'warning');
                }
                console.log(`${data.cancelled} transaksi dibatalkan karena expired`);
                
                // Optionally refresh page after 3 seconds to update counts
                setTimeout(() => {
                    location.reload();
                }, 3000);
            }
        })
        .catch(error => console.error('Error:', error));
    }, 30000); // Check every 30 seconds
    
    // Add hover effects to stats cards
    document.querySelectorAll('.stats-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Auto-refresh notification counts every 2 minutes
    setInterval(() => {
        // You can implement an AJAX call here to update notification counts
        // without refreshing the entire page
        console.log('Checking for new notifications...');
    }, 120000); // Check every 2 minutes
});
</script>
@endpush
@endsection
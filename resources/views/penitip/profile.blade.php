@extends('layouts.app')

@section('title', 'Profil Penitip')

@section('content')
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Notification Bell -->
                    <div class="position-relative mb-2">
                        <div class="dropdown">
                            <button class="btn btn-link p-0 position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fa-lg text-primary"></i>
                                @php
                                    $unreadCount = collect($notifications ?? [])->where('is_read', false)->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge">
                                        {{ $unreadCount }}
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                @endif
                            </button>
                            
                            <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span>Notifikasi</span>
                                    @if($unreadCount > 0)
                                        <button class="btn btn-sm btn-link p-0 text-primary" onclick="markAllAsRead()">
                                            <small>Tandai semua dibaca</small>
                                        </button>
                                    @endif
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                
                                @if(empty($notifications))
                                    <li class="px-3 py-2 text-center text-muted">
                                        <i class="fas fa-bell-slash mb-2"></i><br>
                                        Tidak ada notifikasi
                                    </li>
                                @else
                                    @foreach($notifications as $index => $notification)
                                        <li>
                                            <a class="dropdown-item py-2 {{ !$notification['is_read'] ? 'bg-light' : '' }}" 
                                               href="#" 
                                               onclick="markAsRead({{ $index }})">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0 me-2">
                                                        @if($notification['type'] == 'barang_terjual')
                                                            <i class="fas fa-money-bill-wave text-success"></i>
                                                        @else
                                                            <i class="fas fa-info-circle text-primary"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 fs-6">{{ $notification['title'] }}</h6>
                                                        <p class="mb-1 small text-muted">{{ $notification['message'] }}</p>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}</small>
                                                        @if(!$notification['is_read'])
                                                            <span class="badge bg-primary ms-2">Baru</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        @if(!$loop->last)
                                            <li><hr class="dropdown-divider"></li>
                                        @endif
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>

                    <img src="{{ asset($penitip->FOTO_PROFIL ?? 'images/default-profile.png') }}" class="rounded-circle mb-3" alt="Profile Picture" style="width: 150px; height: 150px;">
                    <h5>{{ $penitip->NAMA_PENITIP }}</h5>
                    <p class="text-muted">{{ $penitip->EMAIL_PENITIP }}</p>
                    <div class="d-flex justify-content-center mb-2">
                        <div class="badge bg-success rounded-pill px-3 py-2">
                            <i class="fas fa-star me-1"></i> Rating: {{ number_format($penitip->RATING_PENITIP, 1) }}
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
                <a href="{{ route('penitip.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
                <a href="{{ route('penitip.profile') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-user me-2"></i> Profil
                </a>
                <a href="{{ route('penitip.consignments') }}" class="list-group-item list-group-item-action">
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
                    <h5 class="mb-0">Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Nama Lengkap</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ $penitip->NAMA_PENITIP }}
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Email</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ $penitip->EMAIL_PENITIP }}
                        </div>
                    </div>
                    <hr>
                    {{-- <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Rating</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            <div class="d-flex align-items-center">
                                <div class="text-warning me-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $penitip->RATING_PENITIP)
                                            <i class="fas fa-star"></i>
                                        @elseif($i <= $penitip->RATING_PENITIP + 0.5)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span>{{ number_format($penitip->RATING_PENITIP, 1) }}/5.0</span>
                            </div>
                        </div>
                    </div> --}}
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
                    <h5 class="mb-0">Saldo Rekening</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card bg-success bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-success mb-2">Total Saldo</h6>
                                    <h1 class="mb-0">Rp {{ number_format($penitip->UANG_PENITIP, 0, ',', '.') }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="mb-2">Tarik Saldo</h6>
                                    <a href="#" class="btn btn-success">Tarik Saldo</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Saldo ini merupakan hasil penjualan barang titipan Anda yang telah berhasil terjual. Anda dapat menarik saldo ini ke rekening bank Anda.
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Penitipan Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($penitip->penitipans && $penitip->penitipans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Berakhir</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($penitip->penitipans->take(5) as $penitipan)
                                        <tr>
                                            {{-- <td>{{ sprintf("%s%03d", substr($penitipan->barang->NAMA_BARANG, 0, 1), $penitipan->ID_PENITIPAN) }}</td>
                                            <td>{{ $penitipan->barang->NAMA_BARANG }}</td> --}}
                                            <td>
                                                {{ $penitipan->barang && $penitipan->barang->NAMA_BARANG
                                                    ? sprintf("%s%03d", substr($penitipan->barang->NAMA_BARANG, 0, 1), $penitipan->ID_PENITIPAN)
                                                    : '-' }}
                                            </td>
                                            <td>
                                                {{ $consignment->barang->NAMA_BARANG ?? '-' }}
                                            </td>

                                            <td>{{ \Carbon\Carbon::parse($penitipan->TANGGAL_MULAI)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR)->format('d/m/Y') }}</td>
                                            <td>
                                                @switch($penitipan->STATUS_PENITIPAN)
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
                                                        <span class="badge bg-secondary">{{ $penitipan->STATUS_PENITIPAN }}</span>
                                                @endswitch
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
                            <i class="fas fa-info-circle me-2"></i> Anda belum memiliki barang yang dititipkan. Mulai titipkan barang Anda dan dapatkan keuntungan dari barang bekas yang tidak terpakai.
                        </div>
                        <div class="text-center">
                            <a href="#" class="btn btn-success">Titipkan Barang</a>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($penitip->topSeller)
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-crown me-2"></i> Top Seller</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="display-4 text-warning mb-3">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h4>Selamat! Anda adalah Top Seller bulan ini!</h4>
                            <p>Anda telah berhasil menjual {{ $penitip->topSeller->JUMLAH_PENJUALAN }} barang dengan nilai penjualan tertinggi untuk bulan ini. Sebagai apresiasi, Anda mendapatkan bonus diskon komisi sebesar 1%.</p>
                        </div>
                    </div>
                </div>
            @endif 
        </div>
    </div>

<!-- Custom CSS untuk Notification -->
    <style>
    .notification-dropdown {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 0.5rem;
    }

    .notification-dropdown .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .notification-dropdown .dropdown-item.bg-light {
        background-color: #e3f2fd !important;
        border-left: 3px solid #2196f3;
    }

    .btn-link:hover {
        text-decoration: none;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }

    .badge.bg-danger {
        animation: pulse 2s infinite;
    }
    </style>

    <!-- JavaScript untuk Notification -->
    <script>
    function markAsRead(index) {
        fetch(`/penitip/notifications/${index}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI - remove "Baru" badge and background
                const notificationItem = event.target.closest('.dropdown-item');
                notificationItem.classList.remove('bg-light');
                const newBadge = notificationItem.querySelector('.badge.bg-primary');
                if (newBadge) {
                    newBadge.remove();
                }
                
                // Update notification count
                updateNotificationCount();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function markAllAsRead() {
        fetch('/penitip/notifications/read-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh page to update UI
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function updateNotificationCount() {
        fetch('/penitip/notifications/count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count;
                } else {
                    // Create new badge if doesn't exist
                    const bellIcon = document.querySelector('.fa-bell').parentElement;
                    const newBadge = document.createElement('span');
                    newBadge.id = 'notificationBadge';
                    newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                    newBadge.innerHTML = data.count + '<span class="visually-hidden">unread messages</span>';
                    bellIcon.appendChild(newBadge);
                }
            } else {
                if (badge) {
                    badge.remove();
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Auto-update notification count every 30 seconds
    setInterval(updateNotificationCount, 30000);
    </script>
@endsection
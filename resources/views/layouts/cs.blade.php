<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CS Dashboard') - ReUseMart</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Custom CSS -->
     <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .content {
            flex: 1;
        }
        
        .footer {
            margin-top: auto;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: #28a745 !important;
        }
        
        .sidebar {
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            min-height: calc(100vh - 56px);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: black;
            padding: 1rem 1.5rem;
            margin: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .sidebar .nav-link:hover::before {
            left: 100%;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        .badge-notification {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .card-dashboard {
            border-left: 4px solid #28a745;
            transition: transform 0.2s ease;
        }
        
        .card-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* Stats Cards */
        .stats-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .stats-card .card-body {
            padding: 2rem;
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .stats-primary .stats-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        .stats-success .stats-icon {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .stats-warning .stats-icon {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        
        .stats-info .stats-icon {
            background: linear-gradient(135deg, #17a2b8, #007bff);
        }

        /* Pagination styling */
        .pagination {
            justify-content: center;
        }
        .page-item.active .page-link {
            background-color: #28a745;
            border-color: #28a745;
        }
        .page-link {
            color: #28a745;
        }
        .page-link:hover {
            color: #1e7e34;
        }

        /* Table enhancements */
        .table-hover tbody tr:hover {
            background-color: rgba(40, 167, 69, 0.05);
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }

        /* Alert styling */
        .alert {
            border: none;
            border-radius: 10px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        }
        
        .alert-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
        }

        /* Button enhancements */
        .btn {
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* Quick actions */
        .quick-action-btn {
            position: relative;
            overflow: hidden;
        }
        
        .quick-action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .quick-action-btn:hover::before {
            left: 100%;
        }

        /* Dropdown menu styling */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-radius: 10px;
        }
        
        .dropdown-item {
            padding: 0.75rem 1.5rem;
            transition: background-color 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        /* Loading animation */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .spinner-border-sm {
            margin-right: 0.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 56px;
                left: -100%;
                width: 250px;
                height: calc(100vh - 56px);
                z-index: 1000;
                transition: left 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .content {
                margin-left: 0;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container-fluid">
            <button class="btn btn-outline-secondary d-md-none" type="button" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <a class="navbar-brand ms-2" href="{{ route('cs.dashboard') }}">
                <i class="fas fa-recycle me-2"></i>ReUseMart CS Panel
            </a>
            
            <!-- Notification Bell -->
            <div class="navbar-nav me-3">
                @php
                    $totalNotifications = \App\Models\Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')
                                                            ->whereNotNull('BUKTI_PEMBAYARAN')
                                                            ->count() + 
                                        \App\Models\Diskusi::whereNull('ID_PARENT')
                                                           ->whereDoesntHave('replies')
                                                           ->count();
                @endphp
                
                <div class="dropdown">
                    <button class="btn btn-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        @if($totalNotifications > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $totalNotifications }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                        <li><h6 class="dropdown-header">Notifikasi</h6></li>
                        
                        @php
                            $pendingPayments = \App\Models\Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')
                                                                  ->whereNotNull('BUKTI_PEMBAYARAN')
                                                                  ->count();
                            $pendingDiscussions = \App\Models\Diskusi::whereNull('ID_PARENT')
                                                                    ->whereDoesntHave('replies')
                                                                    ->count();
                        @endphp
                        
                        @if($pendingPayments > 0)
                            <li>
                                <a class="dropdown-item" href="{{ route('cs.payment.verification.index') }}">
                                    <i class="fas fa-credit-card text-warning me-2"></i>
                                    {{ $pendingPayments }} pembayaran menunggu verifikasi
                                </a>
                            </li>
                        @endif
                        
                        @if($pendingDiscussions > 0)
                            <li>
                                <a class="dropdown-item" href="{{ route('cs.diskusi.index') }}">
                                    <i class="fas fa-comments text-info me-2"></i>
                                    {{ $pendingDiscussions }} diskusi belum dijawab
                                </a>
                            </li>
                        @endif
                        
                        @if($totalNotifications == 0)
                            <li><span class="dropdown-item text-muted">Tidak ada notifikasi baru</span></li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-user-tie me-1"></i> {{ Auth::guard('pegawai')->user()->NAMA_PEGAWAI }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('home') }}"><i class="fas fa-home me-2"></i>Kembali ke Website</a></li>
                    <li><a class="dropdown-item" href="{{ route('cs.profile') }}"><i class="fas fa-user me-2"></i>Profil Saya</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebar">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cs.dashboard') ? 'active' : '' }}" href="{{ route('cs.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cs.penitip.*') ? 'active' : '' }}" href="{{ route('cs.penitip.index') }}">
                                    <i class="fas fa-users"></i> Kelola Penitip
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cs.diskusi.*') ? 'active' : '' }}" href="{{ route('cs.diskusi.index') }}">
                                    <i class="fas fa-comments"></i> Kelola Diskusi
                                    @php
                                        $pendingDiscussions = \App\Models\Diskusi::whereNull('ID_PARENT')
                                                                                ->whereDoesntHave('replies')
                                                                                ->count();
                                    @endphp
                                    @if($pendingDiscussions > 0)
                                        <span class="badge-notification">{{ $pendingDiscussions }}</span>
                                    @endif
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cs.payment.verification.*') ? 'active' : '' }}" href="{{ route('cs.payment.verification.index') }}">
                                    <i class="fas fa-credit-card"></i> Verifikasi Pembayaran
                                    @php
                                        $pendingPayments = \App\Models\Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')
                                                                              ->whereNotNull('BUKTI_PEMBAYARAN')
                                                                              ->count();
                                    @endphp
                                    @if($pendingPayments > 0)
                                        <span class="badge-notification">{{ $pendingPayments }}</span>
                                    @endif
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cs.merchandise.index') ? 'active' : '' }}" href="{{ route('cs.merchandise.index') }}">
                                    <i class="fas fa-box"></i> Daftar Merchandise
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cs.klaim-merchandise.index') ? 'active' : '' }}" href="{{ route('cs.klaim-merchandise.index') }}">
                                    <i class="fas fa-gift"></i> Daftar Klaim Merchandise
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cs.profile*') ? 'active' : '' }}" href="{{ route('cs.profile') }}">
                                    <i class="fas fa-user-cog"></i> Profil Saya
                                </a>
                            </li>
                            
                            <li class="nav-item mt-3">
                                <hr class="text-white-50">
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">
                                    <i class="fas fa-external-link-alt"></i> Kembali ke Website
                                </a>
                            </li>
                        </ul>
                        
                        <!-- Quick Stats in Sidebar -->
                        <div class="mt-4 p-3">
                            <h6 class="text-black-50 mb-3">Quick Stats</h6>
                            
                            <div class="d-flex justify-content-between text-black mb-2">
                                <small>Pending Payments:</small>
                                <span class="badge bg-warning">{{ \App\Models\Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')->whereNotNull('BUKTI_PEMBAYARAN')->count() }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between text-black mb-2">
                                <small>Unanswered:</small>
                                <span class="badge bg-info">{{ \App\Models\Diskusi::whereNull('ID_PARENT')->whereDoesntHave('replies')->count() }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between text-black">
                                <small>Today's Trans:</small>
                                <span class="badge bg-success">{{ \App\Models\Transaksi::whereDate('TANGGAL_TRANSAKSI', today())->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
                    <!-- Alert Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Page Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer bg-light py-3 text-center border-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-md-start">
                    <span class="text-muted">&copy; {{ date('Y') }} ReUseMart. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="d-none position-fixed top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="text-center text-white">
                <div class="spinner-border text-light mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Memproses permintaan...</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Transaction Manager JS -->
    <script src="{{ asset('js/transaction-manager.js') }}"></script>
    
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(e) {
                    if (window.innerWidth < 768 && 
                        !sidebar.contains(e.target) && 
                        !sidebarToggle.contains(e.target) &&
                        sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                });
            }
            
            // Auto-refresh notifications every 30 seconds
            setInterval(function() {
                // Check for expired transactions
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
                        console.log(`${data.cancelled} transaksi dibatalkan karena expired`);
                        
                        // Update notification counts (simplified - in production you might want to fetch updated counts)
                        updateNotificationCounts();
                    }
                })
                .catch(error => console.error('Error:', error));
            }, 30000);
            
            // Update last update time
            setInterval(function() {
                document.getElementById('last-update').textContent = new Date().toLocaleString('id-ID');
            }, 60000);
            
            // Loading overlay functions
            window.showLoading = function() {
                document.getElementById('loadingOverlay').classList.remove('d-none');
            };
            
            window.hideLoading = function() {
                document.getElementById('loadingOverlay').classList.add('d-none');
            };
            
            // Form loading states
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + submitBtn.textContent;
                        
                        // Re-enable after 5 seconds as fallback
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = submitBtn.textContent.replace(/^\s*Loading...\s*/, '');
                        }, 5000);
                    }
                });
            });
            
            // Smooth transitions for cards
            document.querySelectorAll('.card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transition = 'transform 0.2s ease';
                });
            });
        });
        
        // Function to update notification counts (you can implement this based on your needs)
        function updateNotificationCounts() {
            // This would typically make an AJAX call to get updated counts
            // For now, we'll just log that we should update
            console.log('Should update notification counts');
        }
        
        // Utility function to show toast notifications
        window.showToast = function(message, type = 'success') {
            const toastContainer = document.querySelector('.toast-container') || createToastContainer();
            
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remove toast element after it's hidden
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        };
        
        function createToastContainer() {
            const container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }
    </script>
    
    @yield('scripts')
</body>
</html>
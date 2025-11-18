<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Gudang') - ReUseMart</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

        <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            display: flex;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #198754 0%, #157347 100%);
            color: white;
            min-height: 100vh;
            position: fixed;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: #ffffffcc;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 0 25px 25px 0;
            margin: 2px 0;
            margin-right: 10px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.15);
            color: #fff;
            transform: translateX(5px);
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            width: 16px;
            text-align: center;
        }

        .main-content {
            margin-left: 280px;
            flex-grow: 1;
            padding: 20px;
        }

        .navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .footer {
            text-align: center;
            padding: 15px;
            background: #fff;
            border-top: 1px solid #dee2e6;
        }

        .dropdown-menu {
            min-width: 180px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .brand {
            font-size: 1.25rem;
            font-weight: bold;
            padding: 20px;
            display: block;
            color: white;
            text-decoration: none;
            background-color: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .brand i {
            margin-right: 8px;
        }

        .brand:hover {
            color: white;
            text-decoration: none;
        }

        /* Menu section headers */
        .menu-header {
            color: rgba(255,255,255,0.7);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px 20px 5px 20px;
            margin-top: 10px;
        }

        /* Card improvements */
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2);
        }

        /* Select2 customization */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border-color: #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        /* Badge improvements */
        .badge {
            font-size: 0.75em;
            padding: 0.5em 0.75em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -280px;
                transition: margin-left 0.3s;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <a href="{{ route('gudang.dashboard') }}" class="brand">
            <i class="fas fa-warehouse"></i> ReUseMart Gudang
        </a>
        
        <nav class="nav flex-column mt-3">
            <!-- Dashboard -->
            <a class="nav-link {{ request()->routeIs('gudang.dashboard') ? 'active' : '' }}" href="{{ route('gudang.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <!-- Inventory Management -->
            <div class="menu-header">Manajemen Stok</div>
            <a class="nav-link {{ request()->routeIs('gudang.stok*') ? 'active' : '' }}" href="{{ route('gudang.stok') }}">
                <i class="fas fa-boxes"></i> Stok Barang
            </a>
            <a class="nav-link {{ request()->routeIs('gudang.stok.tambah') ? 'active' : '' }}" href="{{ route('gudang.stok.tambah') }}">
                <i class="fas fa-plus-circle"></i> Tambah Barang
            </a>

            <!-- Penitipan Management -->
            <div class="menu-header">Manajemen Penitipan</div>
            <a class="nav-link {{ request()->routeIs('gudang.penitipan.index') ? 'active' : '' }}" href="{{ route('gudang.penitipan.index') }}">
                <i class="fas fa-handshake"></i> Daftar Penitipan
            </a>
            <a class="nav-link {{ request()->routeIs('gudang.penitipan.create') ? 'active' : '' }}" href="{{ route('gudang.penitipan.create') }}">
                <i class="fas fa-plus"></i> Tambah Penitipan
            </a>
            <a class="nav-link {{ request()->routeIs('gudang.penitipan.laporan*') ? 'active' : '' }}" href="{{ route('gudang.penitipan.laporan') }}">
                <i class="fas fa-chart-line"></i> Laporan Penitipan
            </a>
            <!-- Profile -->
            <div class="menu-header">Akun</div>
            <a class="nav-link {{ request()->routeIs('gudang.profile*') ? 'active' : '' }}" href="{{ route('gudang.profile') }}">
                <i class="fas fa-user"></i> Profil Saya
            </a>

            {{-- <a class="nav-link {{ request()->routeIs('gudang.penitip.uang*') ? 'active' : '' }}" href="{{ route('gudang.penitip.uang') }}">
                <i class="fas fa-user"></i> Tampil Uang Penitip
            </a> --}}
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar Top -->
        <nav class="navbar navbar-expand navbar-light bg-white mb-4">
            <div class="container-fluid">
                <!-- Mobile menu toggle -->
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Search bar (optional) -->
                <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100">
                    <!-- You can add search functionality here -->
                </div>

                <!-- Navbar items -->
                <ul class="navbar-nav ms-auto">
                    <!-- Notifications (optional) -->
                    <li class="nav-item dropdown no-arrow mx-1">
                        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell fa-fw"></i>
                            <!-- Counter for notifications -->
                            <span class="badge badge-danger badge-counter" id="notificationCount" style="display: none;">3+</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="alertsDropdown">
                            <h6 class="dropdown-header">Notifikasi</h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center small text-gray-500" href="#">Tidak ada notifikasi baru</a>
                        </div>
                    </li>

                    <!-- User dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            <span class="d-none d-lg-inline">{{ Auth::guard('pegawai')->user()->NAMA_PEGAWAI ?? 'Pegawai' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li><h6 class="dropdown-header">{{ Auth::guard('pegawai')->user()->NAMA_PEGAWAI ?? 'Pegawai' }}</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('gudang.profile') }}">
                                <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                                Profil Saya
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('gudang.edit') }}">
                                <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                                Pengaturan
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        @yield('content')

        <!-- Footer -->
        <footer class="footer mt-auto">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col">
                        <span class="text-muted">&copy; {{ date('Y') }} ReUseMart. All rights reserved.</span>
                    </div>
                    <div class="col-auto">
                        <span class="text-muted">Dashboard Gudang</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('toast_success'))
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: "{{ session('toast_success') }}",
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('toast_error'))
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: "{{ session('toast_error') }}",
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            $(document).ready(function() {
                // Mobile sidebar toggle
                $('#sidebarToggle').click(function() {
                    $('.sidebar').toggleClass('show');
                });

                // Close sidebar when clicking outside on mobile
                $(document).click(function(e) {
                    if (!$(e.target).closest('.sidebar, #sidebarToggle').length) {
                        $('.sidebar').removeClass('show');
                    }
                });

                // Auto-dismiss alerts after 5 seconds
                $('.alert').each(function() {
                    if ($(this).hasClass('alert-success') || $(this).hasClass('alert-info')) {
                        setTimeout(() => {
                            $(this).fadeOut();
                        }, 5000);
                    }
                });

                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        });
        </script>

    @yield('scripts')
</body>
</html>

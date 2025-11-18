<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ReUseMart') - ReUseMart</title>
    
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
        
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .btn-outline-success {
            color: #28a745;
            border-color: #28a745;
        }
        
        .btn-outline-success:hover {
            background-color: #28a745;
            color: white;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('styles')
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <script>
    $(document).ready(function () {
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif

        @if($errors->any())
            toastr.error("{{ $errors->first() }}");
        @endif

        @if(session('notifications'))
            @foreach(session('notifications') as $notif)
                toastr["{{ $notif['type'] }}"]("{!! $notif['text'] !!}");
            @endforeach
        @endif
    });
</script>



    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('images/ReUseMart.png') }}" alt="ReUseMart" class="me-2" style="width: 30px; height: 30px;">
                ReUseMart
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('barang.index') ? 'active' : '' }}" href="{{ route('barang.index') }}">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Kontak</a>
                    </li>
                </ul>
                
                <div class="d-flex">
                    @auth('pembeli')
                        <div class="dropdown">
                            <button class="btn btn-outline-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> {{ Auth::guard('pembeli')->user()->NAMA_PEMBELI }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="{{ route('pembeli.profile') }}">Profil</a></li>
                                <li><a class="dropdown-item" href="{{ route('pembeli.transactions') }}">Riwayat Transaksi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @elseauth('penitip')
                        <div class="dropdown">
                            <button class="btn btn-outline-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> {{ Auth::guard('penitip')->user()->NAMA_PENITIP }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="{{ route('penitip.profile') }}">Profil</a></li>
                                <li><a class="dropdown-item" href="{{ route('penitip.consignments') }}">Riwayat Penitipan</a></li>
                                {{-- <li><a class="dropdown-item" href="{{ route('penitip.sales') }}">Riwayat Penjualan</a></li> --}}
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @elseauth('pegawai')
                        <div class="dropdown">
                            <button class="btn btn-outline-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-tie me-1"></i> {{ Auth::guard('pegawai')->user()->NAMA_PEGAWAI }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                @if(Auth::guard('pegawai')->user()->isOwner())
                                    <li><a class="dropdown-item" href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                                @elseif(Auth::guard('pegawai')->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                @elseif(Auth::guard('pegawai')->user()->isGudang())
                                    <li><a class="dropdown-item" href="{{ route('gudang.dashboard') }}">Dashboard</a></li>
                                @elseif(Auth::guard('pegawai')->user()->isCS())
                                    <li><a class="dropdown-item" href="{{ route('cs.dashboard') }}">Dashboard</a></li>
                                @elseif(Auth::guard('pegawai')->user()->isKurir())
                                    <li><a class="dropdown-item" href="{{ route('kurir.dashboard') }}">Dashboard</a></li>
                                @elseif(Auth::guard('pegawai')->user()->isHunter())
                                    <li><a class="dropdown-item" href="{{ route('hunter.dashboard') }}">Dashboard</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @elseauth('organisasi')
                        <div class="dropdown">
                            <button class="btn btn-outline-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-building me-1"></i> {{ Auth::guard('organisasi')->user()->NAMA_ORGANISASI }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="{{ route('organisasi.dashboard') }}">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-success me-2">Login</a>
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Daftar
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="{{ route('register.pembeli') }}">Pembeli</a></li>
                                <li><a class="dropdown-item" href="{{ route('register.organisasi') }}">Organisasi</a></li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="content py-4">
        <div class="container">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Main Content -->
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer bg-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-3">ReUseMart</h5>
                    <p class="text-muted">Platform jual beli barang bekas berkualitas. Mendukung gerakan reduce, reuse, recycle untuk lingkungan yang lebih baik.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Kategori</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('barang.index') }}?category=1" class="text-decoration-none text-muted">Elektronik & Gadget</a></li>
                        <li><a href="{{ route('barang.index') }}?category=2" class="text-decoration-none text-muted">Pakaian & Aksesori</a></li>
                        <li><a href="{{ route('barang.index') }}?category=3" class="text-decoration-none text-muted">Perabotan Rumah Tangga</a></li>
                        <li><a href="{{ route('barang.index') }}?category=4" class="text-decoration-none text-muted">Buku & Alat Tulis</a></li>
                        <li><a href="{{ route('barang.index') }}" class="text-decoration-none text-muted">Lihat Semua</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Jl. Green Eco Park No. 456 Yogyakarta</li>
                        <li class="text-muted"><i class="fas fa-phone me-2"></i>(0274) 123456</li>
                        <li class="text-muted"><i class="fas fa-envelope me-2"></i>info@reusemart.com</li>
                    </ul>
                    <div class="mt-3">
                        <a href="#" class="text-muted me-2"><i class="fab fa-facebook-square fa-lg"></i></a>
                        <a href="#" class="text-muted me-2"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-muted me-2"><i class="fab fa-twitter-square fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center text-muted">
                <small>&copy; {{ date('Y') }} ReUseMart. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for Toastr) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Toastr Notifications -->
<script>
    @if(session('notifications'))
        @foreach(session('notifications') as $notif)
            toastr["{{ $notif['type'] }}"]("{!! $notif['text'] !!}");
        @endforeach
    @endif
</script>

    <!-- Custom JS -->
    @yield('scripts')
</body>
</html>
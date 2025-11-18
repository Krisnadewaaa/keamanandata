@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <!-- Hero Section -->
    <div class="bg-success bg-opacity-10 rounded-3 p-5 mb-5">
        <div class="row align-items-center"> <!-- Tambahkan align-items-center di sini -->
            <div class="col-md-6">
                <h1 class="fw-bold mb-3">ReUseMart</h1>
                <h4 class="mb-4">Platform Jual Beli Barang Bekas Berkualitas</h4>
                <p class="lead mb-4">
                    Mendukung gerakan reduce, reuse, recycle untuk lingkungan yang lebih baik.
                    Dapatkan barang berkualitas dengan harga terjangkau atau jual barang bekasmu dengan mudah.
                </p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <a href="{{ route('barang.index') }}" class="btn btn-success btn-lg px-4 me-md-2">Jelajahi Produk</a>
                    <a href="{{ route('register.penitip') }}" class="btn btn-outline-success btn-lg px-4">Jual Barangmu</a>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <img src="{{ asset('images/ReUseMart.png') }}" class="img-fluid rounded w-75" alt="ReUseMart">
            </div>
        </div>
    </div>

    <!-- Kategori Section -->
    <h2 class="mb-4">Kategori Produk</h2>
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4 mb-5">
        @foreach($categories as $category)
            <div class="col">
                <a href="{{ route('barang.index', ['category' => $category->ID_KATEGORI]) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 bg-success bg-opacity-10">
                        <div class="card-body text-center">
                            @switch($category->ID_KATEGORI)
                                @case(1)
                                    <i class="fas fa-mobile-alt fa-3x mb-3 text-success"></i>
                                    @break
                                @case(2)
                                    <i class="fas fa-tshirt fa-3x mb-3 text-success"></i>
                                    @break
                                @case(3)
                                    <i class="fas fa-couch fa-3x mb-3 text-success"></i>
                                    @break
                                @case(4)
                                    <i class="fas fa-book fa-3x mb-3 text-success"></i>
                                    @break
                                @case(5)
                                    <i class="fas fa-gamepad fa-3x mb-3 text-success"></i>
                                    @break
                                @case(6)
                                    <i class="fas fa-baby fa-3x mb-3 text-success"></i>
                                    @break
                                @case(7)
                                    <i class="fas fa-car fa-3x mb-3 text-success"></i>
                                    @break
                                @case(8)
                                    <i class="fas fa-seedling fa-3x mb-3 text-success"></i>
                                    @break
                                @case(9)
                                    <i class="fas fa-briefcase fa-3x mb-3 text-success"></i>
                                    @break
                                @case(10)
                                    <i class="fas fa-spa fa-3x mb-3 text-success"></i>
                                    @break
                                @default
                                    <i class="fas fa-box fa-3x mb-3 text-success"></i>
                            @endswitch
                            <h5 class="card-title">{{ $category->JENIS_KATEGORI }}</h5>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Produk Terbaru Section -->
    <h2 class="mb-4">Produk Terbaru</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-5">
        @foreach($barangs as $barang)
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('images/fotoProduk/' . ($barang->foto_produk ?? 'default.jpg')) }}" class="img-fluid card-img-top" alt="{{ $barang->NAMA_BARANG }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $barang->NAMA_BARANG }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($barang->DESKRIPSI, 50) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold">Rp {{ number_format($barang->HARGA, 0, ',', '.') }}</span>
                            @if($barang->GARANSI == 'Ya')
                                <span class="badge bg-info text-dark"><i class="fas fa-shield-alt me-1"></i> Garansi</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('barang.show', $barang->ID_BARANG) }}" class="btn btn-outline-success w-100">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="text-center">
        <a href="{{ route('barang.index') }}" class="btn btn-success">Lihat Semua Produk</a>
    </div>

    <!-- Cara Kerja Section -->
    <h2 class="mt-5 mb-4">Cara Kerja ReUseMart</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <div class="col">
            <div class="card h-100 border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <div class="display-3 text-success mb-3">01</div>
                    <h4 class="card-title mb-3">Jual Barang Bekas</h4>
                    <p class="card-text">Titipkan barang bekasmu yang masih layak pakai ke ReUseMart. Kami akan melakukan quality control dan membantu menjualnya.</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <div class="display-3 text-success mb-3">02</div>
                    <h4 class="card-title mb-3">Beli Barang Berkualitas</h4>
                    <p class="card-text">Temukan berbagai barang bekas berkualitas dengan harga terjangkau. Semua barang telah melalui proses quality control.</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <div class="display-3 text-success mb-3">03</div>
                    <h4 class="card-title mb-3">Donasi untuk Sosial</h4>
                    <p class="card-text">Barang yang tidak terjual dapat didonasikan ke organisasi sosial. Bersama-sama kita bisa memberikan manfaat lebih bagi masyarakat.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimoni Section -->
    <h2 class="mt-5 mb-4">Testimoni Pengguna</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="mb-3 text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="card-text">"Sangat puas dengan layanan ReUseMart. Barang bekas saya laku dengan harga yang memuaskan. Proses penitipan juga sangat mudah!"</p>
                    <div class="d-flex align-items-center mt-3">
                        <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Testimoni">
                        <div>
                            <h6 class="mb-0">Budi Santoso</h6>
                            <small class="text-muted">Penitip Barang</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="mb-3 text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="card-text">"Saya berhasil mendapatkan laptop bekas yang masih bagus dengan harga terjangkau. Kualitas terjamin dan masih bergaransi. Terima kasih ReUseMart!"</p>
                    <div class="d-flex align-items-center mt-3">
                        <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Testimoni">
                        <div>
                            <h6 class="mb-0">Siti Rahma</h6>
                            <small class="text-muted">Pembeli</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="mb-3 text-warning">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="card-text">"Sebagai organisasi sosial, kami sangat terbantu dengan program donasi dari ReUseMart. Barang-barang yang kami terima sangat bermanfaat bagi penerima manfaat kami."</p>
                    <div class="d-flex align-items-center mt-3">
                        <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Testimoni">
                        <div>
                            <h6 class="mb-0">Yayasan Peduli Kasih</h6>
                            <small class="text-muted">Organisasi Sosial</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<h2 class="mt-5 mb-4">Penitip dengan Rating Tertinggi</h2>
<div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
    @foreach($penitipsTop as $penitip)
        <div class="col">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <img src="{{ asset($penitip->FOTO_PROFIL ?? 'images/default-profile.png') }}" class="rounded-circle mb-3" width="80" height="80" alt="Foto Profil">
                    <h5>{{ $penitip->NAMA_PENITIP }}</h5>
                    <div class="text-warning mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $penitip->avg_rating)
                                <i class="fas fa-star"></i>
                            @elseif($i <= $penitip->avg_rating + 0.5)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="text-muted">Rating: {{ number_format($penitip->avg_rating, 1) }}/5.0</p>
                </div>
            </div>
        </div>
    @endforeach
</div>


    
@endsection
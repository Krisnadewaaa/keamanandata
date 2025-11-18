@extends('layouts.app')

@section('title', $barang->NAMA_BARANG)

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('barang.index') }}" class="text-decoration-none">Produk</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $barang->NAMA_BARANG }}</li>
        </ol>
    </nav>

    <h3 class="mb-4 mt-3">Produk Terkait</h3>

    <div class="row">
        <!-- Product Images -->
        <div class="col-md-5 mb-4">
            <div class="card">
                <div class="card-body">
                    <!-- Carousel -->
                    <div id="produkCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="{{ asset('images/fotoProduk/' . ($barang->foto_produk ?? 'default.jpg')) }}" class="d-block w-100" alt="{{ $barang->NAMA_BARANG }}">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('images/fotoProduk2/' . ($barang->foto_produk2 ?? 'default.jpg')) }}" class="d-block w-100" alt="{{ $barang->NAMA_BARANG }}">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#produkCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#produkCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    
                    <!-- Additional Images Row -->
                    <div class="row mt-3">
                        <div class="col-3">
                            <img src="{{ asset('images/fotoProduk/' . ($barang->foto_produk ?? 'default.jpg')) }}" class="img-thumbnail" alt="{{ $barang->NAMA_BARANG }}">
                        </div>
                        <div class="col-3">
                            <img src="{{ asset('images/fotoProduk2/' . ($barang->foto_produk2 ?? 'default.jpg')) }}" class="img-thumbnail" alt="{{ $barang->NAMA_BARANG }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-3">{{ $barang->NAMA_BARANG }}</h2>
                    
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="text-success mb-0">Rp {{ number_format($barang->HARGA, 0, ',', '.') }}</h3>
                        @if($barang->GARANSI == 'Ya')
                            <span class="badge bg-info text-dark ms-3"><i class="fas fa-shield-alt me-1"></i> Garansi</span>
                        @endif
                    </div>
                    
                    <hr>
                    
                    <h5>Deskripsi Produk</h5>
                    <p>{{ $barang->DESKRIPSI }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Detail Produk</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 d-flex justify-content-between">
                                    <span>Kategori</span>
                                    <span class="text-muted">{{ $barang->kategori->JENIS_KATEGORI }}</span>
                                </li>
                                <li class="list-group-item px-0 d-flex justify-content-between">
                                    <span>Status</span>
                                    <span class="text-{{ $barang->STATUS == 'Tersedia' ? 'success' : 'danger' }}">{{ $barang->STATUS }}</span>
                                </li>
                                <li class="list-group-item px-0 d-flex justify-content-between">
                                    <span>Stok</span>
                                    <span class="text-{{ $barang->stok > 0 ? 'success' : 'danger' }}">{{ $barang->stok }}</span>
                                </li>
                                <li class="list-group-item px-0 d-flex justify-content-between">
                                    <span>Garansi</span>
                                    @if($barang->GARANSI == 'Ya')
                                        <span class="text-success">Ya <a href="{{ route('barang.warranty', $barang->ID_BARANG) }}" class="ms-2 text-decoration-none">(Cek Status)</a></span>
                                    @else
                                        <span class="text-muted">Tidak</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Informasi Penitipan</h5>
                            <p class="text-muted">Barang ini adalah barang titipan yang telah melalui proses quality control oleh tim ReUseMart.</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        @auth('pembeli')
                            @if($barang->STATUS == 'Tersedia' && $barang->stok > 0)
                                <form action="{{ route('pembeli.cart.add', $barang->ID_BARANG) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-shopping-cart me-2"></i> Tambahkan ke Keranjang
                                    </button>
                                </form>
                                <a href="{{ route('pembeli.cart.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-shopping-basket me-2"></i> Lihat Keranjang
                                </a>
                                <button type="button" class="btn btn-outline-success">
                                    <i class="fas fa-heart me-2"></i> Tambah ke Wishlist
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-lg" disabled>
                                    <i class="fas fa-times-circle me-2"></i> Produk Tidak Tersedia
                                </button>
                                <small class="text-danger text-center">Stok produk ini telah habis</small>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i> Login untuk Membeli
                            </a>
                            <a href="{{ route('register.pembeli') }}" class="btn btn-outline-success">
                                <i class="fas fa-user-plus me-2"></i> Belum Punya Akun? Daftar Sekarang
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            
            <!-- Diskusi Produk -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Diskusi Produk</h5>
                </div>
                <div class="card-body">
                    <!-- Form Tambah Diskusi -->
                    @auth('pembeli')
                        <div class="mb-4">
                            <form action="{{ route('diskusi.store', $barang->ID_BARANG) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="komentar" class="form-label">Tanyakan sesuatu tentang produk ini</label>
                                    <textarea class="form-control @error('komentar') is-invalid @enderror" id="komentar" name="komentar" rows="3" placeholder="Tulis pertanyaan Anda di sini...">{{ old('komentar') }}</textarea>
                                    @error('komentar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-success">Kirim Pertanyaan</button>
                            </form>
                        </div>
                    @elseauth('pegawai')
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i> Anda login sebagai CS. Anda dapat membalas pertanyaan pelanggan di bawah.
                        </div>
                    @else
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i> Silakan <a href="{{ route('login') }}" class="alert-link">login</a> untuk bertanya tentang produk ini.
                        </div>
                    @endauth
                    
                    <hr>
                    
                    <!-- Daftar Diskusi -->
                    @if($barang->diskusiParent()->count() > 0)
                        @foreach($barang->diskusiParent as $diskusi)
                            <div class="mb-3">
                                <div class="d-flex mb-2">
                                    <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="User">
                                    <div>
                                        <h6 class="mb-0">{{ $diskusi->NAMA_PENGIRIM }}</h6>
                                        <small class="text-muted">{{ $diskusi->created_at }}</small>
                                    </div>
                                    
                                    @auth('pegawai')
                                        <div class="ms-auto">
                                            <a href="{{ route('diskusi.reply.form', $diskusi->ID_DISKUSI) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-reply"></i> Balas
                                            </a>
                                            <form action="{{ route('diskusi.destroy', $diskusi->ID_DISKUSI) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endauth
                                </div>
                                <p class="mb-2">{{ $diskusi->KOMENTAR }}</p>
                                
                                <!-- Balasan Diskusi -->
                                @foreach($diskusi->replies as $reply)
                                    <div class="d-flex ms-5 mb-2">
                                        <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="Admin">
                                        <div>
                                            <h6 class="mb-0">{{ $reply->NAMA_PENGIRIM }}</h6>
                                            <small class="text-muted">{{ $reply->created_at }}</small>
                                        </div>
                                        
                                        @auth('pegawai')
                                            <div class="ms-auto">
                                                <form action="{{ route('diskusi.destroy', $reply->ID_DISKUSI) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endauth
                                    </div>
                                    <div class="ms-5">
                                        <p class="mb-0">{{ $reply->KOMENTAR }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="mb-0">Belum ada diskusi untuk produk ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-5">
        @foreach($relatedProducts as $relatedProduct)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body pb-0">
                        <h5 class="card-title">{{ $relatedProduct->NAMA_BARANG }}</h5>
                    </div>
                    <img src="{{ asset('images/fotoProduk/' . ($relatedProduct->foto_produk ?? 'default.jpg')) }}" class="card-img-top" alt="{{ $relatedProduct->NAMA_BARANG }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body pt-2">
                        <p class="card-text text-muted">{{ Str::limit($relatedProduct->DESKRIPSI, 50) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold">Rp {{ number_format($relatedProduct->HARGA, 0, ',', '.') }}</span>
                            @if($relatedProduct->GARANSI == 'Ya')
                                <span class="badge bg-info text-dark"><i class="fas fa-shield-alt me-1"></i> Garansi</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-grid">
                            <a href="{{ route('barang.show', $relatedProduct->ID_BARANG) }}" class="btn btn-outline-success">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
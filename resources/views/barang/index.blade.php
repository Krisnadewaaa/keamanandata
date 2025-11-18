@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Filter Produk</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('barang.index') }}" method="GET">
                        <!-- Search -->
                        <div class="mb-4">
                            <label for="search" class="form-label">Cari Produk</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search" placeholder="Cari..." value="{{ request('search') }}">
                                <button class="btn btn-outline-success" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="mb-4">
                            <label class="form-label">Kategori</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="category-all" value="all" {{ request('category') == 'all' || !request('category') ? 'checked' : '' }}>
                                <label class="form-check-label" for="category-all">
                                    Semua Kategori
                                </label>
                            </div>
                            @foreach($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="category" id="category-{{ $category->ID_KATEGORI }}" value="{{ $category->ID_KATEGORI }}" {{ request('category') == $category->ID_KATEGORI ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category-{{ $category->ID_KATEGORI }}">
                                        {{ $category->JENIS_KATEGORI }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Sort -->
                        <div class="mb-4">
                            <label for="sort" class="form-label">Urutkan</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga (Terendah)</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga (Tertinggi)</option>
                            </select>
                        </div>
                        
                        <!-- Warranty -->
                        <div class="mb-4">
                            <label class="form-label">Garansi</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="warranty" id="warranty" value="1" {{ request('warranty') ? 'checked' : '' }}>
                                <label class="form-check-label" for="warranty">
                                    Produk Bergaransi
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-filter me-2"></i> Filter
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Produk</h2>
                <p class="text-muted mb-0">Menampilkan {{ $barangs->count() }} dari {{ $barangs->total() }} produk</p>
            </div>
            
            @if($barangs->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($barangs as $barang)
                        <div class="col">
                            <div class="card h-100">
                                <!-- Gambar produk -->
                                <img src="{{ asset('images/fotoProduk/' . ($barang->foto_produk ?? 'default.jpg')) }}" 
                                    class="card-img-top img-fluid" 
                                    alt="{{ $barang->NAMA_BARANG }}" 
                                    style="object-fit: cover; height: 250px; width: 100%; border-radius: 5px;">
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
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $barangs->appends(request()->query())->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Tidak ada produk yang ditemukan. Silakan coba filter yang berbeda.
                </div>
            @endif
        </div>
    </div>
@endsection
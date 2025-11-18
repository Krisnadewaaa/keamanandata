@extends('layouts.app')

@section('title', 'Diskusi - ' . $barang->NAMA_BARANG)

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('barang.index') }}" class="text-decoration-none">Produk</a></li>
            <li class="breadcrumb-item"><a href="{{ route('barang.show', $barang->ID_BARANG) }}" class="text-decoration-none">{{ $barang->NAMA_BARANG }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Diskusi</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="mb-0">Diskusi Produk</h3>
                        <a href="{{ route('barang.show', $barang->ID_BARANG) }}" class="btn btn-outline-success ms-auto">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Detail Produk
                        </a>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <img src="https://via.placeholder.com/100/28a745/FFFFFF?text={{ substr($barang->NAMA_BARANG, 0, 1) }}" class="rounded me-3" alt="{{ $barang->NAMA_BARANG }}">
                        <div>
                            <h5>{{ $barang->NAMA_BARANG }}</h5>
                            <p class="text-muted">{{ Str::limit($barang->DESKRIPSI, 100) }}</p>
                            <div class="d-flex align-items-center">
                                <span class="text-success fw-bold">Rp {{ number_format($barang->HARGA, 0, ',', '.') }}</span>
                                @if($barang->GARANSI == 'Ya')
                                    <span class="badge bg-info text-dark ms-2"><i class="fas fa-shield-alt me-1"></i> Garansi</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

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
                </div>
            </div>

            <!-- Daftar Diskusi -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Semua Diskusi</h5>
                </div>
                <div class="card-body">
                    @if($diskusi->count() > 0)
                        @foreach($diskusi->where('ID_PARENT', null) as $parent)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex mb-2">
                                    <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="User">
                                    <div>
                                        <h6 class="mb-0">{{ $parent->NAMA_PENGIRIM }}</h6>
                                        <small class="text-muted">{{ $parent->created_at }}</small>
                                    </div>
                                    
                                    @auth('pegawai')
                                        <div class="ms-auto">
                                            <a href="{{ route('diskusi.reply.form', $parent->ID_DISKUSI) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-reply"></i> Balas
                                            </a>
                                            <form action="{{ route('diskusi.destroy', $parent->ID_DISKUSI) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endauth
                                </div>
                                <p class="mb-2">{{ $parent->KOMENTAR }}</p>
                                
                                <!-- Balasan Diskusi -->
                                @foreach($diskusi->where('ID_PARENT', $parent->ID_DISKUSI) as $reply)
                                    <div class="ms-5 mb-2 p-3 bg-light rounded">
                                        <div class="d-flex">
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
                                        <p class="mb-0 mt-2">{{ $reply->KOMENTAR }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $diskusi->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="mb-0">Belum ada diskusi untuk produk ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Produk Terkait</h5>
                </div>
                <div class="card-body">
                    @foreach(\App\Models\Barang::where('ID_KATEGORI', $barang->ID_KATEGORI)
                                            ->where('ID_BARANG', '!=', $barang->ID_BARANG)
                                            ->where('STATUS', 'Tersedia')
                                            ->take(3)
                                            ->get() as $relatedProduct)
                        <div class="d-flex mb-3">
                            <img src="https://via.placeholder.com/80/28a745/FFFFFF?text={{ substr($relatedProduct->NAMA_BARANG, 0, 1) }}" class="rounded" alt="{{ $relatedProduct->NAMA_BARANG }}">
                            <div class="ms-3">
                                <h6 class="mb-1">{{ $relatedProduct->NAMA_BARANG }}</h6>
                                <p class="text-success mb-1">Rp {{ number_format($relatedProduct->HARGA, 0, ',', '.') }}</p>
                                <a href="{{ route('barang.show', $relatedProduct->ID_BARANG) }}" class="btn btn-sm btn-outline-success">Lihat Detail</a>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Bantuan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><i class="fas fa-question-circle me-2 text-success"></i> Tentang Diskusi</h6>
                        <p class="text-muted small">Diskusi memungkinkan Anda untuk bertanya langsung tentang produk kepada CS ReUseMart.</p>
                    </div>
                    <div class="mb-3">
                        <h6><i class="fas fa-shield-alt me-2 text-success"></i> Tentang Garansi</h6>
                        <p class="text-muted small">Produk dengan label garansi masih dalam masa garansi dari pabrik.</p>
                    </div>
                    <div>
                        <h6><i class="fas fa-truck me-2 text-success"></i> Tentang Pengiriman</h6>
                        <p class="text-muted small">Pengiriman dilakukan dalam 1-2 hari kerja untuk area Yogyakarta. Anda juga dapat mengambil barang langsung di gudang kami.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
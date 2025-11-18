{{-- File: resources/views/pembeli/transactionDetail.blade.php (Updated) --}}

@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')

            <style>
        .star-rating {
            font-size: 1.5rem;
            color: #ddd;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .star-rating .star {
            margin-right: 5px;
            transition: color 0.2s ease;
            display: inline-block;
        }
        .star-rating .star:hover {
            color: #ffc107;
        }
        .star-rating .star.active {
            color: #ffc107;
        }
        .star-rating .star.half i {
            background: linear-gradient(90deg, #ffc107 50%, #ddd 50%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .form-select:disabled {
            background-color: #f8f9fa;
            opacity: 0.6;
            cursor: not-allowed;
        }
        .rating-display .star-display {
            margin-bottom: 10px;
        }
        .rating-display {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #28a745;
        }
    </style>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    @if(isset($pembeli))
                        <img src="{{ asset($pembeli->FOTO_PROFIL ? $pembeli->FOTO_PROFIL : 'https://via.placeholder.com/150') }}" alt="Foto Profil" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover; border-radius: 0;">
                        <h5>{{ $pembeli->NAMA_PEMBELI }}</h5>
                        <p class="text-muted">{{ $pembeli->EMAIL_PEMBELI }}</p>
                    @endif
                </div>
            </div>
            
            <div class="list-group mt-4">
                <a href="{{ route('pembeli.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('pembeli.transactions') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-shopping-cart me-2"></i> Riwayat Transaksi
                </a>
                <a href="{{ route('pembeli.cart.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-basket me-2"></i> Keranjang
                </a>

            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="mb-4">
                <a href="{{ route('pembeli.transactions') }}" class="btn btn-outline-secondary mb-3">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Riwayat Transaksi
                </a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Transaksi {{ $transaction->NOMOR_TRANSAKSI }}</h5>
                    <div>
                        <span class="badge {{ $transaction->STATUS_TRANSAKSI == 'Selesai' ? 'bg-success' : 
                        ($transaction->STATUS_TRANSAKSI == 'Batal' ? 'bg-danger' : 
                        ($transaction->STATUS_TRANSAKSI == 'Menunggu Pembayaran' ? 'bg-warning text-dark' : 
                        ($transaction->STATUS_TRANSAKSI == 'Menunggu Konfirmasi' ? 'bg-info text-dark' : 'bg-primary'))) }}">
                            {{ $transaction->STATUS_TRANSAKSI }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informasi Transaksi</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%">No. Transaksi</td>
                                    <td width="5%">:</td>
                                    <td width="55%">{{ $transaction->NOMOR_TRANSAKSI }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Transaksi</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->TANGGAL_TRANSAKSI)->format('d F Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td>Metode Pembayaran</td>
                                    <td>:</td>
                                    <td>{{ $transaction->METODE_PEMBAYARAN }}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge {{ $transaction->STATUS_TRANSAKSI == 'Selesai' ? 'bg-success' : 
                                        ($transaction->STATUS_TRANSAKSI == 'Batal' ? 'bg-danger' : 
                                        ($transaction->STATUS_TRANSAKSI == 'Menunggu Pembayaran' ? 'bg-warning text-dark' : 
                                        ($transaction->STATUS_TRANSAKSI == 'Menunggu Konfirmasi' ? 'bg-info text-dark' : 'bg-primary'))) }}">
                                            {{ $transaction->STATUS_TRANSAKSI }}
                                        </span>
                                    </td>
                                </tr>
                                @if($transaction->POIN_DITUKAR > 0)
                                <tr>
                                    <td>Poin Ditukar</td>
                                    <td>:</td>
                                    <td>{{ $transaction->POIN_DITUKAR }} poin</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informasi Pengiriman</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%">Metode Pengiriman</td>
                                    <td width="5%">:</td>
                                    <td width="55%">{{ $transaction->METODE_PENGIRIMAN }}</td>
                                </tr>
                                <tr>
                                    <td>Status Pengiriman</td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge {{ $transaction->STATUS_PENGIRIMAN == 'Terkirim' ? 'bg-success' : 
                                        ($transaction->STATUS_PENGIRIMAN == 'Tidak Diambil' ? 'bg-danger' : 
                                        ($transaction->STATUS_PENGIRIMAN == 'Menunggu' ? 'bg-warning text-dark' : 
                                        ($transaction->STATUS_PENGIRIMAN == 'Sedang Dikirim' ? 'bg-info text-dark' : 'bg-primary'))) }}">
                                            {{ $transaction->STATUS_PENGIRIMAN }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Biaya Pengiriman</td>
                                    <td>:</td>
                                    <td>{{ $transaction->BIAYA_PENGIRIMAN ? 'Rp ' . number_format($transaction->BIAYA_PENGIRIMAN, 0, ',', '.') : 'Gratis' }}</td>
                                </tr>
                                @if($transaction->BATAS_WAKTU_PEMBAYARAN && $transaction->STATUS_TRANSAKSI == 'Menunggu Pembayaran')
                                <tr>
                                    <td>Batas Pembayaran</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->BATAS_WAKTU_PEMBAYARAN)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>

                            {{-- Tampilkan form rating hanya jika transaksi selesai dan pengiriman sudah diterima --}}
                            @if(
                                $transaction->STATUS_TRANSAKSI == 'Selesai' &&
                                in_array($transaction->STATUS_PENGIRIMAN, ['Terkirim', 'Diambil'])
                            )
                                <hr>
                                <h6 class="text-muted mb-3">
                                    @if($transaction->RATING_BARANG)
                                        Rating Produk Anda
                                    @else
                                        Beri Rating Produk
                                    @endif
                                </h6>
                                
                                <form action="{{ route('pembeli.rating.simpan', $transaction->ID_TRANSAKSI) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="rating" class="form-label">Rating (1 - 5)</label>
                                        
                                        {{-- Display Rating jika sudah ada --}}
                                        @if($transaction->RATING_BARANG)
                                            <div class="rating-display mb-3">
                                                <div class="star-display">
                                                    @php
                                                        $rating = $transaction->RATING_BARANG;
                                                        $fullStars = floor($rating);
                                                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                                    @endphp

                                                    {{-- Bintang penuh --}}
                                                    @for($i = 0; $i < $fullStars; $i++)
                                                        <i class="fas fa-star text-warning fs-4"></i>
                                                    @endfor

                                                    {{-- Setengah bintang --}}
                                                    @if($hasHalfStar)
                                                        <i class="fas fa-star-half-alt text-warning fs-4"></i>
                                                    @endif

                                                    {{-- Bintang kosong --}}
                                                    @for($i = 0; $i < $emptyStars; $i++)
                                                        <i class="far fa-star text-warning fs-4"></i>
                                                    @endfor

                                                    <span class="ms-2 text-muted fw-bold">
                                                        {{ number_format($transaction->RATING_BARANG, 1) }}/5.0
                                                    </span>
                                                </div>
                                                <small class="text-success d-block mt-2">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Rating telah disimpan pada 
                                                    {{ $transaction->updated_at 
                                                        ? $transaction->updated_at->format('d M Y H:i') 
                                                        : '-' }}
                                                </small>
                                            </div>
                                        @endif

                                        {{-- Visual Star Rating (jika belum ada rating) --}}
                                        @if(!$transaction->RATING_BARANG)
                                            <div class="star-rating mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span class="star" data-rating="{{ $i }}">
                                                        <i class="far fa-star"></i>
                                                    </span>
                                                @endfor
                                            </div>
                                        @endif

                                        {{-- Dropdown Pilihan Rating --}}
                                        <select class="form-select" name="rating" id="rating" 
                                                {{ $transaction->RATING_BARANG ? 'disabled' : 'required' }}>
                                            <option value="">-- Pilih Rating --</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                @foreach([0, 0.5] as $decimal)
                                                    @php 
                                                        $value = $i + $decimal;
                                                        if($value > 5) break;
                                                        $label = $value == floor($value) ? intval($value) : number_format($value, 1);
                                                    @endphp
                                                    <option value="{{ $value }}" {{ $transaction->RATING_BARANG == $value ? 'selected' : '' }}>
                                                        {{ $label }} Bintang
                                                    </option>
                                                @endforeach
                                            @endfor
                                        </select>

                                        {{-- Teks penjelas --}}
                                        <small class="text-muted d-block mt-2">
                                            <span id="rating-text">
                                                @if($transaction->RATING_BARANG)
                                                    @php
                                                        $ratingLabels = [
                                                            1 => 'Sangat Buruk',
                                                            1.5 => 'Buruk', 
                                                            2 => 'Kurang',
                                                            2.5 => 'Cukup',
                                                            3 => 'Biasa',
                                                            3.5 => 'Baik',
                                                            4 => 'Sangat Baik',
                                                            4.5 => 'Luar Biasa',
                                                            5 => 'Sempurna'
                                                        ];
                                                    @endphp
                                                    Penilaian Anda: {{ $ratingLabels[$transaction->RATING_BARANG] ?? 'Rating ' . $transaction->RATING_BARANG }}
                                                @else
                                                    Pilih rating untuk produk ini
                                                @endif
                                            </span>
                                        </small>
                                    </div>

                                    {{-- Tombol Submit --}}
                                    @if(!$transaction->RATING_BARANG)
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-star me-2"></i> Simpan Rating
                                        </button>
                                    @else
                                        <div class="alert alert-success mt-2">
                                            <i class="fas fa-thumbs-up me-2"></i>
                                            Terima kasih! Rating Anda telah tersimpan.
                                        </div>
                                    @endif
                                </form>
                            @endif

                            {{-- Jika tidak memenuhi syarat untuk rating --}}
                            @if(
                                $transaction->STATUS_TRANSAKSI == 'Selesai' &&
                                !in_array($transaction->STATUS_PENGIRIMAN, ['Terkirim', 'Diambil'])
                            )
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Rating hanya dapat diberikan setelah barang diterima.
                                </div>
                            @endif

                            @if($transaction->STATUS_TRANSAKSI == 'Selesai' && in_array($transaction->STATUS_PENGIRIMAN, ['Terkirim', 'Diambil']))
    <hr>
    <h6 class="text-muted mb-3">
        @if($transaction->RATING_PENITIP)
            Rating Penitip
        @else
            Beri Rating untuk Penitip
        @endif
    </h6>

    <form action="{{ route('pembeli.rating.penitip', $transaction->ID_TRANSAKSI) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="rating_penitip" class="form-label">Rating Penitip (1 - 5)</label>

            @if($transaction->RATING_PENITIP)
                <div class="star-display mb-3">
                    @php
                        $rating = $transaction->RATING_PENITIP;
                        $full = floor($rating);
                        $half = ($rating - $full) >= 0.5;
                        $empty = 5 - $full - ($half ? 1 : 0);
                    @endphp

                    @for($i = 0; $i < $full; $i++)
                        <i class="fas fa-star text-warning fs-4"></i>
                    @endfor
                    @if($half)
                        <i class="fas fa-star-half-alt text-warning fs-4"></i>
                    @endif
                    @for($i = 0; $i < $empty; $i++)
                        <i class="far fa-star text-warning fs-4"></i>
                    @endfor

                    <span class="ms-2 text-muted fw-bold">
                        {{ number_format($rating, 1) }}/5.0
                    </span>
                </div>
            @else
                <select class="form-select" name="rating_penitip" required>
                    <option value="">-- Pilih Rating --</option>
                    @for($i = 1; $i <= 5; $i++)
                        @foreach([0, 0.5] as $decimal)
                            @php
                                $val = $i + $decimal;
                                if ($val > 5) break;
                            @endphp
                            <option value="{{ $val }}">{{ $val }} Bintang</option>
                        @endforeach
                    @endfor
                </select>

                <button type="submit" class="btn btn-primary mt-2">
                    <i class="fas fa-star me-2"></i> Simpan Rating Penitip
                </button>
            @endif
        </div>
    </form>
@endif

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
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/fotoProduk/' . ($transaction->barang->foto_produk ?? 'default.jpg')) }}" 
                                                 class="me-3" 
                                                 style="width: 80px; height: 80px; object-fit: cover;" 
                                                 alt="{{ $transaction->barang->NAMA_BARANG }}">
                                            <div>
                                                <h6 class="mb-1">{{ $transaction->barang->NAMA_BARANG }}</h6>
                                                <small class="text-muted">{{ Str::limit($transaction->barang->DESKRIPSI, 50) }}</small>
                                                @if($transaction->barang->GARANSI == 'Ya')
                                                    <div>
                                                        <span class="badge bg-info text-dark"><i class="fas fa-shield-alt me-1"></i> Garansi</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format($transaction->barang->HARGA, 0, ',', '.') }}</td>
                                    <td>1</td>
                                    <td>Rp {{ number_format($transaction->barang->HARGA, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end">Subtotal</td>
                                    <td>Rp {{ number_format($transaction->barang->HARGA, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Biaya Pengiriman</td>
                                    <td>{{ $transaction->BIAYA_PENGIRIMAN ? 'Rp ' . number_format($transaction->BIAYA_PENGIRIMAN, 0, ',', '.') : 'Gratis' }}</td>
                                </tr>
                                @if($transaction->POIN_DITUKAR > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Potongan Poin</td>
                                    <td>- Rp {{ number_format($transaction->POIN_DITUKAR * 10000, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total</td>
                                    <td class="fw-bold">Rp {{ number_format($transaction->TOTAL_TRANSAKSI, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Status-specific messages and actions -->
                    @if($transaction->STATUS_TRANSAKSI == 'Menunggu Pembayaran')
                        <div class="alert alert-warning mt-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">Menunggu Pembayaran</h5>
                                    <p class="mb-0">Silakan lakukan pembayaran sesuai dengan total transaksi. Pembayaran harus dilakukan dalam waktu 1 menit.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('pembeli.payment.form', $transaction->ID_TRANSAKSI) }}" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card me-2"></i> Bayar Sekarang
                            </a>
                        </div>
                        
                    @elseif($transaction->STATUS_TRANSAKSI == 'Menunggu Konfirmasi')
                        <div class="alert alert-info mt-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">Menunggu Verifikasi</h5>
                                    <p class="mb-0">Bukti pembayaran Anda sedang diverifikasi oleh tim Customer Service. Proses ini biasanya memakan waktu 5-10 menit.</p>
                                </div>
                            </div>
                        </div>
                        
                        @if($transaction->BUKTI_PEMBAYARAN)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Bukti Pembayaran</h6>
                                </div>
                                <div class="card-body text-center">
                                    <img src="{{ asset($transaction->BUKTI_PEMBAYARAN) }}" 
                                         alt="Bukti Pembayaran" 
                                         class="img-thumbnail" 
                                         style="max-width: 300px;">
                                    <p class="mt-2 text-muted small">
                                        Diupload: {{ \Carbon\Carbon::parse($transaction->TANGGAL_UPLOAD_BUKTI)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                    @elseif($transaction->STATUS_TRANSAKSI == 'Disiapkan')
                        <div class="alert alert-primary mt-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-box fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">Barang Sedang Disiapkan</h5>
                                    <p class="mb-0">Pembayaran Anda telah diverifikasi. Barang sedang disiapkan oleh tim kami untuk pengiriman atau pengambilan.</p>
                                </div>
                            </div>
                        </div>
                        
                    @elseif($transaction->STATUS_TRANSAKSI == 'Selesai')
                        <div class="alert alert-success mt-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">Transaksi Selesai</h5>
                                    <p class="mb-0">Terima kasih telah berbelanja di ReUseMart. Poin reward telah ditambahkan ke akun Anda.</p>
                                </div>
                            </div>
                        </div>
                        
                    @elseif($transaction->STATUS_TRANSAKSI == 'Batal')
                        <div class="alert alert-danger mt-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">Transaksi Dibatalkan</h5>
                                    <p class="mb-0">
                                        Transaksi ini telah dibatalkan 
                                        @if($transaction->STATUS_VERIFIKASI == 'Invalid')
                                            karena bukti pembayaran tidak valid.
                                        @else
                                            karena tidak ada pembayaran dalam batas waktu yang ditentukan.
                                        @endif
                                        @if($transaction->POIN_DITUKAR > 0)
                                            Poin yang ditukar telah dikembalikan.
                                        @endif
                                    </p>
                                    @if($transaction->CATATAN_VERIFIKASI)
                                        <p class="mb-0 mt-2"><strong>Catatan:</strong> {{ $transaction->CATATAN_VERIFIKASI }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        @if($transaction->STATUS_TRANSAKSI == 'Selesai' && $transaction->barang->GARANSI == 'Ya')
                            <a href="{{ route('barang.warranty', $transaction->barang->ID_BARANG) }}" class="btn btn-outline-info">
                                <i class="fas fa-shield-alt me-2"></i> Cek Status Garansi
                            </a>
                        @else
                            <span></span>
                        @endif
                        
                        <a href="#" class="btn btn-success" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> Cetak Nota
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh for pending transactions
    @if(in_array($transaction->STATUS_TRANSAKSI, ['Menunggu Pembayaran', 'Menunggu Konfirmasi']))
        setInterval(() => {
            fetch(`/pembeli/transaction/{{ $transaction->ID_TRANSAKSI }}/status`)
                .then(response => response.json())
                .then(data => {
                    if (data.status !== '{{ $transaction->STATUS_TRANSAKSI }}') {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 10000); // Check every 10 seconds
    @endif
});
@if(!$transaction->RATING_BARANG)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const stars = document.querySelectorAll('.star');
                const ratingSelect = document.getElementById('rating');
                const ratingText = document.getElementById('rating-text');
                
                const ratingLabels = {
                    '': 'Pilih rating untuk produk ini',
                    '1': '1 Bintang - Sangat Buruk',
                    '1.5': '1.5 Bintang - Buruk',
                    '2': '2 Bintang - Kurang',
                    '2.5': '2.5 Bintang - Cukup',
                    '3': '3 Bintang - Biasa',
                    '3.5': '3.5 Bintang - Baik',
                    '4': '4 Bintang - Sangat Baik',
                    '4.5': '4.5 Bintang - Luar Biasa',
                    '5': '5 Bintang - Sempurna'
                };

                // Function untuk update tampilan bintang
                function updateStars(rating) {
                    stars.forEach((star, index) => {
                        const starValue = index + 1;
                        star.classList.remove('active', 'half');
                        
                        if (rating >= starValue) {
                            // Bintang penuh
                            star.classList.add('active');
                            star.querySelector('i').className = 'fas fa-star';
                        } else if (rating >= starValue - 0.5) {
                            // Setengah bintang
                            star.classList.add('half');
                            star.querySelector('i').className = 'fas fa-star-half-alt';
                        } else {
                            // Bintang kosong
                            star.querySelector('i').className = 'far fa-star';
                        }
                    });
                }

                // Function untuk update teks
                function updateText(rating) {
                    ratingText.textContent = ratingLabels[rating] || 'Rating: ' + rating + ' Bintang';
                }

                // Event listener untuk klik bintang
                stars.forEach((star, index) => {
                    star.addEventListener('click', function() {
                        const starNumber = index + 1;
                        const currentRating = parseFloat(ratingSelect.value);
                        let newRating;
                        
                        // Logic untuk toggle antara penuh dan setengah
                        if (currentRating === starNumber) {
                            // Jika klik bintang yang sama, buat setengah
                            newRating = starNumber - 0.5;
                        } else if (currentRating === starNumber - 0.5) {
                            // Jika sudah setengah, hapus rating
                            newRating = '';
                        } else {
                            // Bintang penuh
                            newRating = starNumber;
                        }
                        
                        ratingSelect.value = newRating;
                        updateStars(parseFloat(newRating) || 0);
                        updateText(newRating);
                    });

                    // Hover effect
                    star.addEventListener('mouseenter', function() {
                        const hoverRating = index + 1;
                        updateStars(hoverRating);
                    });
                });

                // Reset hover effect
                document.querySelector('.star-rating').addEventListener('mouseleave', function() {
                    const currentRating = parseFloat(ratingSelect.value) || 0;
                    updateStars(currentRating);
                });

                // Event listener untuk dropdown
                ratingSelect.addEventListener('change', function() {
                    const rating = parseFloat(this.value) || 0;
                    updateStars(rating);
                    updateText(this.value);
                });

                // Set initial state
                const initialRating = parseFloat(ratingSelect.value) || 0;
                if (initialRating > 0) {
                    updateStars(initialRating);
                    updateText(ratingSelect.value);
                }
            });
        </script>
    @endif
@endpush
@endsection


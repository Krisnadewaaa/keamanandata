@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    @if(isset($pembeli))
                        <div class="mb-3">
                            <img src="{{ asset($pembeli->FOTO_PROFIL ? $pembeli->FOTO_PROFIL : 'https://via.placeholder.com/150') }}" 
                                 alt="Foto Profil" 
                                 class="rounded-circle img-thumbnail" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <h5 class="mb-1">{{ $pembeli->NAMA_PEMBELI }}</h5>
                        <p class="text-muted small mb-3">{{ $pembeli->EMAIL_PEMBELI }}</p>
                        <div class="d-flex justify-content-center">
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="fas fa-coins me-1"></i>
                                {{ number_format($pembeli->POINT_PEMBELI) }} Poin
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="list-group mt-4 shadow-sm">
                <a href="{{ route('pembeli.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard
                </a>
                <a href="{{ route('pembeli.transactions') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-cart me-2 text-info"></i> Riwayat Transaksi
                </a>
                <a href="{{ route('pembeli.cart.index') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-shopping-basket me-2"></i> Keranjang
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Back Button & Title -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('pembeli.cart.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Keranjang
                </a>
                <h2 class="mb-0 text-primary">Checkout</h2>
            </div>

            <form id="checkoutForm" action="{{ route('pembeli.cart.process') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Products Section -->
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header bg-light border-0">
                                <h5 class="mb-0 text-dark">
                                    <i class="fas fa-box me-2 text-primary"></i>Produk yang Dibeli
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach($cartItems as $item)
                                    <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="position-relative me-3">
                                            <img src="{{ asset('images/fotoProduk/' . ($item->barang->foto_produk ?? 'default.jpg')) }}" 
                                                 class="rounded" 
                                                 style="width: 80px; height: 80px; object-fit: cover;" 
                                                 alt="{{ $item->barang->NAMA_BARANG }}">
                                            @if($item->barang->GARANSI == 'Ya')
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info">
                                                    <i class="fas fa-shield-alt"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold">{{ $item->barang->NAMA_BARANG }}</h6>
                                            <p class="text-muted mb-2 small">{{ Str::limit($item->barang->DESKRIPSI, 60) }}</p>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @if($item->barang->GARANSI == 'Ya')
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                                        <i class="fas fa-shield-alt me-1"></i> Bergaransi
                                                    </span>
                                                @endif
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                    <i class="fas fa-leaf me-1"></i> Second Hand
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success fs-5 mb-1">Rp {{ number_format($item->barang->HARGA, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header bg-light border-0">
                                <h5 class="mb-0 text-dark">
                                    <i class="fas fa-shipping-fast me-2 text-primary"></i>Metode Pengiriman
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check h-100">
                                            <input class="form-check-input" type="radio" name="shipping_method" id="shippingKurir" value="kurir" checked onchange="calculateTotal()">
                                            <label class="form-check-label w-100" for="shippingKurir">
                                                <div class="card h-100 border-2 border-primary bg-primary bg-opacity-5 shipping-option">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-truck fa-2x text-primary mb-2"></i>
                                                        <h6 class="card-title">Kurir ReUseMart</h6>
                                                        <p class="card-text small text-muted mb-0">
                                                            Gratis untuk pembelian di atas Rp 1.500.000
                                                        </p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check h-100">
                                            <input class="form-check-input" type="radio" name="shipping_method" id="shippingPickup" value="ambil_sendiri" onchange="calculateTotal()">
                                            <label class="form-check-label w-100" for="shippingPickup">
                                                <div class="card h-100 border-2 shipping-option">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-store fa-2x text-success mb-2"></i>
                                                        <h6 class="card-title">Ambil Sendiri</h6>
                                                        <p class="card-text small text-muted mb-0">
                                                            Ambil langsung di toko (Gratis)
                                                        </p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Address Selection -->
                                <div id="alamatSection" class="mt-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-map-marker-alt me-2 text-danger"></i>Pilih Alamat Pengiriman
                                    </h6>
                                    @if($alamat->count() > 0)
                                        <div class="row g-3">
                                            @foreach($alamat as $address)
                                                <div class="col-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="alamat_id" id="alamat{{ $address->ID_ALAMAT }}" value="{{ $address->ID_ALAMAT }}" {{ $address->IS_DEFAULT ? 'checked' : '' }}>
                                                        <label class="form-check-label w-100" for="alamat{{ $address->ID_ALAMAT }}">
                                                            <div class="card border-2 {{ $address->IS_DEFAULT ? 'border-primary bg-primary bg-opacity-5' : '' }} address-option">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <h6 class="mb-1">{{ $address->ALAMAT_LENGKAP }}</h6>
                                                                            <p class="text-muted small mb-0">{{ $address->KOTA }}, {{ $address->PROVINSI }}</p>
                                                                        </div>
                                                                        @if($address->IS_DEFAULT)
                                                                            <span class="badge bg-primary">
                                                                                <i class="fas fa-star me-1"></i>Utama
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-warning border-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Anda belum memiliki alamat. <a href="{{ route('pembeli.alamat.create') }}" class="alert-link fw-bold">Tambah alamat sekarang</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header bg-light border-0">
                                <h5 class="mb-0 text-dark">
                                    <i class="fas fa-credit-card me-2 text-primary"></i>Metode Pembayaran
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="transferBank" value="transfer" checked>
                                    <label class="form-check-label w-100" for="transferBank">
                                        <div class="card border-2 border-success bg-success bg-opacity-5">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="fas fa-university fa-2x text-success me-3"></i>
                                                    <div>
                                                        <h6 class="mb-0">Transfer Bank</h6>
                                                        <small class="text-muted">BCA - 1234567890</small>
                                                    </div>
                                                </div>
                                                <div class="bg-light p-3 rounded">
                                                    <h6 class="mb-2">
                                                        <i class="fas fa-info-circle text-primary me-2"></i>Instruksi Pembayaran:
                                                    </h6>
                                                    <ol class="mb-0 small">
                                                        <li>Transfer ke rekening <strong>BCA 1234567890</strong> a.n. ReUseMart</li>
                                                        <li>Selesaikan pembayaran dalam waktu <strong class="text-danger">1 menit</strong></li>
                                                        <li>Upload bukti pembayaran setelah melakukan transfer</li>
                                                    </ol>
                                                    <div class="alert alert-warning mt-3 mb-0 border-0 bg-warning bg-opacity-10">
                                                        <i class="fas fa-clock text-warning me-2"></i>
                                                        <strong>Pesanan yang tidak dibayar dalam 1 menit akan otomatis dibatalkan.</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Point Redemption -->
                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-header bg-light border-0">
                                <h5 class="mb-0 text-dark">
                                    <i class="fas fa-coins me-2 text-warning"></i>Tukar Poin
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-wallet fa-2x text-warning me-3"></i>
                                            <div>
                                                <h6 class="mb-0">Poin Tersedia</h6>
                                                <span class="h5 text-success mb-0">{{ number_format($pembeli->POINT_PEMBELI) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white border-0">
                                                <i class="fas fa-coins"></i>
                                            </span>
                                            <input type="number" 
                                                   class="form-control form-control-lg border-0 shadow-sm" 
                                                   id="redeemedPoints" 
                                                   name="redeemed_points" 
                                                   min="0" 
                                                   max="{{ $pembeli->POINT_PEMBELI }}" 
                                                   value="0" 
                                                   placeholder="Masukkan poin"
                                                   onchange="calculatePointDiscount()"
                                                   oninput="calculatePointDiscount()">
                                            <span class="input-group-text bg-light border-0 small text-muted">poin</span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>1 poin = Rp 10.000 diskon
                                        </small>
                                    </div>
                                </div>
                                
                                <!-- Point Result -->
                                <div id="pointResult" class="d-none">
                                    <div class="alert alert-success border-0 bg-success bg-opacity-10">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                            <div>
                                                <h6 class="mb-1 text-success">Poin Berhasil Ditukar!</h6>
                                                <span id="pointMessage" class="small"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card sticky-top shadow border-0" style="top: 20px;">
                            <div class="card-header bg-dark text-white border-0">
                                <h5 class="mb-0">
                                    <i class="fas fa-receipt me-2"></i>Ringkasan Pesanan
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <span class="text-muted">Subtotal</span>
                                    <span id="subtotalDisplay" class="fw-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <div>
                                        <span class="text-muted">Biaya Pengiriman</span>
                                        @if($subtotal >= 1500000)
                                            <br><small class="badge bg-success bg-opacity-10 text-success border border-success">Gratis Ongkir</small>
                                        @endif
                                    </div>
                                    <span id="shippingDisplay" class="fw-bold">{{ $shippingCost == 0 ? 'Gratis' : 'Rp ' . number_format($shippingCost, 0, ',', '.') }}</span>
                                </div>
                                
                                <!-- Point Discount Row -->
                                <div id="pointDiscountRow" class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="display: none;">
                                    <span class="text-muted">
                                        <i class="fas fa-coins text-warning me-1"></i>Diskon Poin
                                    </span>
                                    <span id="pointDiscountDisplay" class="fw-bold text-success">- Rp 0</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-4 pt-2">
                                    <span class="h5 mb-0">Total</span>
                                    <span id="totalDisplay" class="h4 mb-0 text-success fw-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-gift text-info fa-lg me-3"></i>
                                        <div>
                                            <h6 class="mb-1 text-info">Reward Points</h6>
                                            <span class="small">Dapatkan <strong>{{ $pointsEarned }}</strong> poin dari pembelian ini!</span>
                                            @if($subtotal > 500000)
                                                <br><small class="text-muted">Termasuk bonus 20% untuk pembelian di atas Rp 500.000</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-success btn-lg w-100 py-3 shadow">
                                    <i class="fas fa-shopping-cart me-2"></i> Buat Pesanan
                                </button>
                                
                                <div class="mt-3 text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1 text-success"></i>
                                        Pembayaran aman dan terjamin
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden fields untuk form submission -->
                <input type="hidden" name="calculated_discount" id="calculatedDiscount" value="0">
                <input type="hidden" name="final_total" id="finalTotal" value="{{ $total }}">
            </form>
        </div>
    </div>
</div>

<style>
/* Basic Card Styling */
.card {
    transition: transform 0.2s ease;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
}

.card:hover {
    transform: translateY(-1px);
}

/* Shipping and Address Options */
.shipping-option {
    cursor: pointer;
    transition: all 0.3s ease;
}

.address-option {
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-check-input:checked + .form-check-label .shipping-option,
.form-check-input:checked + .form-check-label .address-option {
    border-color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.05) !important;
    transform: scale(1.02);
}

/* Button Styling */
.btn-success {
    background: linear-gradient(45deg, #198754, #20c997);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(45deg, #157347, #1aa085);
    transform: translateY(-1px);
}

/* Input Group Styling */
.input-group .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Badge Styling */
.badge {
    font-size: 0.75em;
}

/* Alert Styling */
.alert {
    border-radius: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .sticky-top {
        position: relative !important;
        top: auto !important;
    }
    
    .col-lg-3, .col-lg-9, .col-lg-8, .col-lg-4 {
        margin-bottom: 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between h2 {
        text-align: center;
    }
}

@media (max-width: 576px) {
    .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .row.align-items-center {
        flex-direction: column;
        gap: 1rem;
    }
    
    .input-group {
        width: 100%;
    }
}

/* Hide form-check inputs */
.form-check-input {
    position: absolute;
    opacity: 0;
}

/* Loading Animation */
@keyframes loading {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.loading {
    animation: loading 1.5s infinite;
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
// Data dari server
const SUBTOTAL = {{ $subtotal }};
const MAX_POINTS = {{ $pembeli->POINT_PEMBELI }};
const FREE_SHIPPING_THRESHOLD = 1500000;
const SHIPPING_COST = 100000;

// Fungsi untuk format rupiah
function formatRupiah(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

// Hitung total keseluruhan
function calculateTotal() {
    const isPickup = document.getElementById('shippingPickup').checked;
    const pointsUsed = parseInt(document.getElementById('redeemedPoints').value) || 0;
    
    // Hitung biaya pengiriman
    let shippingCost = 0;
    if (!isPickup) {
        shippingCost = SUBTOTAL >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_COST;
    }
    
    // Hitung diskon poin (1 poin = Rp 1,000)
    const pointDiscount = pointsUsed * 10000;
    
    // Hitung total
    const total = Math.max(0, SUBTOTAL + shippingCost - pointDiscount);
    
    // Update tampilan
    document.getElementById('shippingDisplay').textContent = shippingCost === 0 ? 'Gratis' : formatRupiah(shippingCost);
    document.getElementById('totalDisplay').textContent = formatRupiah(total);
    
    // Update hidden fields
    document.getElementById('calculatedDiscount').value = pointDiscount;
    document.getElementById('finalTotal').value = total;
    
    // Show/hide point discount row
    const pointDiscountRow = document.getElementById('pointDiscountRow');
    const pointDiscountDisplay = document.getElementById('pointDiscountDisplay');
    
    if (pointDiscount > 0) {
        pointDiscountRow.style.display = 'flex';
        pointDiscountDisplay.textContent = '- ' + formatRupiah(pointDiscount);
    } else {
        pointDiscountRow.style.display = 'none';
    }
    
    // Show/hide address section
    const alamatSection = document.getElementById('alamatSection');
    alamatSection.style.display = isPickup ? 'none' : 'block';
}

// Hitung diskon poin (dipanggil otomatis saat input berubah)
function calculatePointDiscount() {
    const pointsInput = document.getElementById('redeemedPoints');
    const points = parseInt(pointsInput.value) || 0;
    const pointResult = document.getElementById('pointResult');
    const pointMessage = document.getElementById('pointMessage');
    
    // Validasi
    if (points < 0) {
        pointsInput.value = 0;
        return;
    }
    
    if (points > MAX_POINTS) {
        pointsInput.value = MAX_POINTS;
        showNotification(`Maksimal poin yang dapat ditukar adalah ${MAX_POINTS.toLocaleString('id-ID')}`, 'warning');
        return;
    }
    
    // Hitung diskon
    const discount = points * 10000;
    const remainingPoints = MAX_POINTS - points;
    
    // Tampilkan hasil dengan animasi
    if (points > 0) {
        pointResult.classList.remove('d-none');
        pointMessage.innerHTML = `
            <strong>${points.toLocaleString('id-ID')} poin</strong> ditukar menjadi 
            <strong class="text-success">${formatRupiah(discount)}</strong> diskon!<br>
            <small class="text-muted">Sisa poin: ${remainingPoints.toLocaleString('id-ID')}</small>
        `;
    } else {
        pointResult.classList.add('d-none');
    }
    
    // Update total
    calculateTotal();
}

// Show notification (optional untuk feedback)
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 500px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    `;
    
    notification.innerHTML = `
        <div>${message}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Form validation
function validateForm() {
    const form = document.getElementById('checkoutForm');
    const shippingKurir = document.getElementById('shippingKurir').checked;
    
    // Validate shipping address if kurir selected
    if (shippingKurir) {
        const addressSelected = document.querySelector('input[name="alamat_id"]:checked');
        if (!addressSelected) {
            showNotification('Silakan pilih alamat pengiriman.', 'warning');
            return false;
        }
    }
    
    return true;
}

// Initialize ketika halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Set initial state
    calculateTotal();
    
    // Add event listeners
    document.getElementById('shippingKurir').addEventListener('change', calculateTotal);
    document.getElementById('shippingPickup').addEventListener('change', calculateTotal);
    
    // Real-time calculation saat mengetik dengan debounce
    let pointCalculationTimeout;
    document.getElementById('redeemedPoints').addEventListener('input', function() {
        clearTimeout(pointCalculationTimeout);
        pointCalculationTimeout = setTimeout(calculatePointDiscount, 300);
    });
    
    // Form submission validation
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses Pesanan...';
        }
        
        // Update final values
        calculateTotal();
    });
    
    // Auto-hide address section on page load if pickup is selected
    const alamatSection = document.getElementById('alamatSection');
    if (document.getElementById('shippingPickup').checked) {
        alamatSection.style.display = 'none';
    }
});

// Error handling
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    showNotification('Terjadi kesalahan pada halaman. Silakan refresh halaman.', 'danger');
});

// Prevent form double submission
let formSubmitted = false;
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    if (formSubmitted) {
        e.preventDefault();
        return false;
    }
    formSubmitted = true;
});
</script>
@endsection
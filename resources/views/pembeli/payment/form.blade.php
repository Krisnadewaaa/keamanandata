@extends('layouts.app')

@section('title', 'Pembayaran - ' . $transaction->NOMOR_TRANSAKSI)

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
                                {{ number_format($pembeli->POINT_PEMBELI ?? 0) }} Poin
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="list-group mt-4 shadow-sm">
                <a href="{{ route('pembeli.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard
                </a>
                <a href="{{ route('pembeli.transactions') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-shopping-cart me-2"></i> Riwayat Transaksi
                </a>
                <a href="{{ route('pembeli.cart.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-basket me-2 text-info"></i> Keranjang
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Back Button & Title -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('pembeli.transactions') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Transaksi
                </a>
                <h2 class="mb-0 text-primary">
                    <i class="fas fa-credit-card me-2"></i>Pembayaran
                </h2>
            </div>
            
            <!-- Payment Timer Alert -->
            @php
                // Set timer fixed 1 menit untuk testing yang stabil
                $remainingSeconds = 60; // Selalu mulai dari 1 menit
                
                // Jika ingin menggunakan waktu real, bisa uncomment ini:
                /*
                try {
                    $createdTime = \Carbon\Carbon::parse($transaction->TANGGAL_TRANSAKSI);
                    $currentTime = \Carbon\Carbon::now();
                    $totalSeconds = $currentTime->timestamp - $createdTime->timestamp;
                    $remainingSeconds = 60 - $totalSeconds; // 60 detik - waktu yang sudah berlalu
                    $remainingSeconds = max(0, min(60, $remainingSeconds)); // Batasi antara 0-60
                } catch (\Exception $e) {
                    $remainingSeconds = 60;
                }
                */
                
                // Jika waktu habis (untuk testing bisa di-comment)
                if ($remainingSeconds <= 0) {
                    \App\Models\Transaksi::cancelTransaction($transaction->ID_TRANSAKSI);
                    session()->flash('error', 'Waktu pembayaran telah habis. Transaksi dibatalkan dan poin/stok dikembalikan.');
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = '" . route('pembeli.transactions') . "';
                        }, 2000);
                    </script>";
                }
            @endphp
            
            <div class="alert alert-warning border-0 shadow-sm mb-4" id="paymentTimer">
                <div class="d-flex align-items-center">
                    <div class="timer-icon me-3">
                        <i class="fas fa-clock fa-3x text-warning"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>Batas Waktu Pembayaran
                        </h5>
                        <div class="d-flex align-items-center">
                            <span class="me-2">Sisa waktu:</span>
                            <span id="countdown" class="badge bg-warning text-dark fs-5 px-3 py-2 countdown-display">
                                {{ sprintf('%02d:%02d', floor($remainingSeconds / 60), $remainingSeconds % 60) }}
                            </span>
                        </div>
                        <small class="text-muted">Transaksi akan dibatalkan otomatis jika tidak dibayar dalam waktu yang ditentukan</small>
                    </div>
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle fa-lg me-3 text-danger"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                </div>
            @endif
            
            <!-- Main Payment Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient bg-primary text-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>Pembayaran Transaksi {{ $transaction->NOMOR_TRANSAKSI }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Transaction Details -->
                    <div class="row mb-4">
                        <!-- Left Column - Transaction Info -->
                        <div class="col-lg-6 mb-4">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Detail Transaksi
                                    </h6>
                                    <div class="transaction-details">
                                        <div class="detail-row">
                                            <span class="label">No. Transaksi</span>
                                            <span class="value fw-bold">{{ $transaction->NOMOR_TRANSAKSI }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Tanggal</span>
                                            <span class="value">{{ \Carbon\Carbon::parse($transaction->TANGGAL_TRANSAKSI)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Produk</span>
                                            <span class="value">{{ $transaction->barang->NAMA_BARANG }}</span>
                                        </div>
                                        <div class="detail-row border-top pt-2">
                                            <span class="label fw-bold">Total Pembayaran</span>
                                            <span class="value h5 text-success mb-0">Rp {{ number_format($transaction->TOTAL_TRANSAKSI, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Payment Instructions -->
                        <div class="col-lg-6 mb-4">
                            <div class="card border-success bg-success bg-opacity-15 h-100 payment-instruction-card">
                                <div class="card-body">
                                    <h6 class="card-title text-success mb-3">
                                        <i class="fas fa-university me-2"></i>Instruksi Pembayaran
                                    </h6>
                                    <div class="payment-instructions">
                                        <div class="instruction-item">
                                            <i class="fas fa-university text-success me-2"></i>
                                            <strong>Bank BCA</strong>
                                        </div>
                                        <div class="instruction-item">
                                            <i class="fas fa-credit-card text-success me-2"></i>
                                            <strong>No. Rekening:</strong> 1234567890
                                        </div>
                                        <div class="instruction-item">
                                            <i class="fas fa-user text-success me-2"></i>
                                            <strong>Atas Nama:</strong> ReUseMart
                                        </div>
                                        <div class="instruction-item highlight">
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            <strong>Jumlah Transfer:</strong>
                                            <span class="h6 text-success mb-0">Rp {{ number_format($transaction->TOTAL_TRANSAKSI, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info border-0 bg-info bg-opacity-10 mt-3 mb-0">
                                        <small>
                                            <i class="fas fa-lightbulb text-info me-2"></i>
                                            <strong>Tips:</strong> Simpan screenshot instruksi ini untuk memudahkan transfer
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Payment Status Content -->
                    @if($transaction->STATUS_TRANSAKSI == 'Menunggu Pembayaran')
                        <!-- Upload Payment Proof -->
                        <div class="card border-warning bg-warning bg-opacity-5">
                            <div class="card-header bg-warning bg-opacity-10 border-warning">
                                <h6 class="mb-0 text-dark">
                                    <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran
                                </h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('pembeli.payment.upload', $transaction->ID_TRANSAKSI) }}" 
                                      method="POST" 
                                      enctype="multipart/form-data" 
                                      class="payment-form">
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="mb-3">
                                                <label for="payment_proof" class="form-label fw-bold">
                                                    <i class="fas fa-camera me-2 text-primary"></i>Pilih File Bukti Pembayaran
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-primary text-white border-0">
                                                        <i class="fas fa-image"></i>
                                                    </span>
                                                    <input type="file" 
                                                           class="form-control form-control-lg border-0 shadow-sm @error('payment_proof') is-invalid @enderror" 
                                                           id="payment_proof" 
                                                           name="payment_proof" 
                                                           accept="image/*" 
                                                           required>
                                                </div>
                                                @error('payment_proof')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Format: JPG, JPEG, PNG. Maksimal 2MB
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="d-grid h-100 align-items-end">
                                                <button type="submit" class="btn btn-success btn-lg shadow">
                                                    <i class="fas fa-cloud-upload-alt me-2"></i> Upload Bukti
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Upload Guidelines -->
                        <div class="card border-info bg-info bg-opacity-5 mt-4">
                            <div class="card-body">
                                <h6 class="text-dark mb-3">
                                    <i class="fas fa-lightbulb me-2"></i>Petunjuk Upload Bukti Pembayaran
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="guideline-list">
                                            <li><i class="fas fa-check text-success me-2"></i>Foto bukti transfer harus jelas dan tidak buram</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Jumlah transfer harus sesuai dengan total pembayaran</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="guideline-list">
                                            <li><i class="fas fa-check text-success me-2"></i>Sertakan tanggal dan waktu transfer</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Transfer dari rekening atas nama Anda</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    @elseif($transaction->STATUS_TRANSAKSI == 'Menunggu Konfirmasi')
                        <!-- Verification Status -->
                        <div class="card border-info bg-info bg-opacity-5">
                            <div class="card-body text-center py-5">
                                <div class="verification-status">
                                    <div class="spinner-border text-info mb-3" role="status" style="width: 3rem; height: 3rem;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <h5 class="text-info mb-3">
                                        <i class="fas fa-clock me-2"></i>Menunggu Verifikasi
                                    </h5>
                                    <p class="text-muted mb-0">
                                        Bukti pembayaran Anda sedang diverifikasi oleh tim Customer Service.<br>
                                        Proses ini biasanya memakan waktu <strong>5-10 menit</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Uploaded Payment Proof -->
                        @if($transaction->BUKTI_PEMBAYARAN)
                            <div class="card border-success bg-success bg-opacity-5 mt-4">
                                <div class="card-header bg-success bg-opacity-10 border-success">
                                    <h6 class="mb-0 text-success">
                                        <i class="fas fa-check-circle me-2"></i>Bukti Pembayaran Terkirim
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="uploaded-proof">
                                        <img src="{{ asset($transaction->BUKTI_PEMBAYARAN) }}" 
                                             alt="Bukti Pembayaran" 
                                             class="img-thumbnail shadow-sm" 
                                             style="max-width: 400px; max-height: 300px; cursor: pointer;"
                                             onclick="showImageModal(this.src)">
                                        <p class="mt-3 text-muted mb-0">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            Diupload pada: {{ \Carbon\Carbon::parse($transaction->TANGGAL_UPLOAD_BUKTI)->format('d/m/Y H:i') }}
                                        </p>
                                        <small class="text-success">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Klik gambar untuk memperbesar
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" alt="Bukti Pembayaran" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Styling */
.timer-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.countdown-display {
    font-family: 'Courier New', monospace;
    font-size: 1.2em !important;
    min-width: 120px;
    text-align: center;
}

.transaction-details .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.transaction-details .detail-row:last-child {
    border-bottom: none;
}

.transaction-details .label {
    flex: 1;
    color: #6c757d;
    font-weight: 500;
}

.transaction-details .value {
    flex: 1;
    text-align: right;
    font-weight: 600;
}

.payment-instructions .instruction-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(25, 135, 84, 0.1);
}

.payment-instructions .instruction-item:last-child {
    border-bottom: none;
}

.payment-instructions .highlight {
    background: rgba(25, 135, 84, 0.25) !important;
    border: 2px solid rgba(25, 135, 84, 0.3);
    border-radius: 0.75rem;
    padding: 1.25rem;
    margin-top: 0.75rem;
    box-shadow: 0 2px 10px rgba(25, 135, 84, 0.1);
}

.guideline-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.guideline-list li {
    padding: 0.5rem 0;
    display: flex;
    align-items: center;
}

.verification-status {
    animation: fadeIn 1s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.uploaded-proof img {
    transition: transform 0.3s ease;
}

.uploaded-proof img:hover {
    transform: scale(1.05);
}

.payment-instruction-card {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.15) 0%, rgba(32, 201, 151, 0.10) 100%) !important;
    border: 2px solid rgba(25, 135, 84, 0.3) !important;
    box-shadow: 0 4px 15px rgba(25, 135, 84, 0.1);
}

.payment-instruction-card:hover {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.20) 0%, rgba(32, 201, 151, 0.15) 100%) !important;
    border-color: rgba(25, 135, 84, 0.5) !important;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(25, 135, 84, 0.15);
}

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

.input-group .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Responsive Design */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between h2 {
        text-align: center;
    }
    
    .countdown-display {
        font-size: 1em !important;
        min-width: 100px;
    }
    
    .transaction-details .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .transaction-details .value {
        text-align: left;
    }
}

/* Critical Timer Styling */
.timer-critical {
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0.3; }
}

.timer-danger .countdown-display {
    background-color: #dc3545 !important;
    color: white !important;
}

.timer-warning .countdown-display {
    background-color: #ffc107 !important;
    color: #000 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hitung sisa waktu dalam detik dari PHP
    let remainingSeconds = {{ $remainingSeconds }};
    
    const countdownElement = document.getElementById('countdown');
    const timerElement = document.getElementById('paymentTimer');
    
    function updateDisplay() {
        if (remainingSeconds <= 0) {
            countdownElement.textContent = 'Waktu Habis';
            countdownElement.className = 'badge bg-danger text-white fs-5 px-3 py-2 countdown-display';
            timerElement.className = 'alert alert-danger border-0 shadow-sm mb-4 timer-danger';
            
            // Auto cancel transaction
            cancelExpiredTransaction();
            
            return false; // Stop the timer
        }
        
        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;
        
        countdownElement.textContent = 
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        
        // Update styling based on remaining time
        if (remainingSeconds <= 10) {
            countdownElement.className = 'badge bg-danger text-white fs-5 px-3 py-2 countdown-display timer-critical';
            timerElement.className = 'alert alert-danger border-0 shadow-sm mb-4 timer-danger';
        } else if (remainingSeconds <= 30) {
            countdownElement.className = 'badge bg-warning text-dark fs-5 px-3 py-2 countdown-display timer-warning';
            timerElement.className = 'alert alert-warning border-0 shadow-sm mb-4';
        }
        
        return true; // Continue the timer
    }
    
    function cancelExpiredTransaction() {
        // Show loading message
        const alertHtml = `
            <div class="alert alert-danger border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm text-danger me-3" role="status"></div>
                    <div>
                        <h6 class="mb-1">Waktu Pembayaran Telah Habis</h6>
                        <p class="mb-0">Membatalkan transaksi dan mengembalikan poin/stok...</p>
                    </div>
                </div>
            </div>
        `;
        
        timerElement.outerHTML = alertHtml;
        
        // Send cancel request using existing route
        fetch(`/pembeli/transaction/{{ $transaction->ID_TRANSAKSI }}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                reason: 'Payment timeout'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const successHtml = `
                    <div class="alert alert-success border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                            <div>
                                <h6 class="mb-1">Transaksi Dibatalkan</h6>
                                <p class="mb-0">${data.message}</p>
                                <small class="text-muted">Mengalihkan ke halaman transaksi...</small>
                            </div>
                        </div>
                    </div>
                `;
                
                document.querySelector('.alert-danger').outerHTML = successHtml;
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '{{ route("pembeli.transactions") }}';
                }, 2000);
            } else {
                // Show error and redirect anyway
                setTimeout(() => {
                    alert('Waktu pembayaran telah habis. Transaksi dibatalkan.');
                    window.location.href = '{{ route("pembeli.transactions") }}';
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback redirect
            setTimeout(() => {
                alert('Waktu pembayaran telah habis. Transaksi dibatalkan.');
                window.location.href = '{{ route("pembeli.transactions") }}';
            }, 1000);
        });
    }
    
    // Check if already expired on load
    if (remainingSeconds <= 0) {
        cancelExpiredTransaction();
        return;
    }
    
    // Initial display
    updateDisplay();
    
    // Start countdown
    const countdownInterval = setInterval(() => {
        remainingSeconds--;
        if (!updateDisplay()) {
            clearInterval(countdownInterval);
        }
    }, 1000);
    
    // File input validation
    const fileInput = document.getElementById('payment_proof');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.');
                    this.value = '';
                    return;
                }
            }
        });
    }
    
    // Form submission handling
    const paymentForm = document.querySelector('.payment-form');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            // Check if time expired before submitting
            if (remainingSeconds <= 0) {
                e.preventDefault();
                alert('Waktu pembayaran telah habis. Tidak dapat upload bukti pembayaran.');
                return false;
            }
            
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengupload...';
            }
        });
    }
});

// Image modal function
function showImageModal(imageSrc) {
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    document.getElementById('modalImage').src = imageSrc;
    modal.show();
}
</script>
@endsection
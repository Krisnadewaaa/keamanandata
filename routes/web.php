<?php
// File: routes/web.php (Complete updated version)

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\Pembeli\AlamatPembeliController;
use App\Http\Controllers\Auth\PasswordResetFormController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PegawaiGudangController;
use App\Http\Middleware\PegawaiMiddleware;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\DiskusiController;
use App\Http\Controllers\CsDiskusiController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentVerificationController;
use App\Http\Controllers\LaporanPDFController;
use App\Http\Controllers\PublikController;
use App\Http\Controllers\cs\MerchandiseController;
use App\Http\Controllers\cs\PointRewardController;
use App\Http\Controllers\ApiMonitoringController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Product routes
Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
Route::get('/barang/{id}', [BarangController::class, 'show'])->name('barang.show');
Route::get('/barang/{id}/warranty', [BarangController::class, 'checkWarranty'])->name('barang.warranty');
Route::get('/barang/{id}/diskusi', [DiskusiController::class, 'index'])->name('diskusi.index');
Route::get('/penitip/{id}/rating', [BarangController::class, 'getRataRataRatingPenitip']);
Route::get('/penitip/rating-rendah', [BarangController::class, 'penitipRatingRendah'])->name('penitip.ratingRendah');

// Diskusi routes
Route::get('/diskusi/{id}/reply', [DiskusiController::class, 'replyForm'])->name('diskusi.reply.form');
Route::post('/diskusi/{id}/reply', [DiskusiController::class, 'reply'])->name('diskusi.reply');
Route::delete('/diskusi/{id}', [DiskusiController::class, 'destroy'])->name('diskusi.destroy');
Route::get('/penitip/{id}/rating', [PublikController::class, 'lihatRatingPenitip'])->name('publik.penitip.rating');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/otp-verify', [OtpAuthController::class, 'showVerifyForm'])->name('otp.verify.form');
    Route::post('/otp-verify', [OtpAuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/otp-resend', [OtpAuthController::class, 'resendOtp'])->name('otp.resend');
    
    Route::get('/register/pembeli', [AuthController::class, 'showRegisterPembeliForm'])->name('register.pembeli');
    Route::post('/register/pembeli', [AuthController::class, 'registerPembeli'])->name('register.pembeli.submit');
    
    Route::get('/register/penitip', [AuthController::class, 'showRegisterPenitipForm'])->name('register.penitip');
    Route::post('/register/penitip', [AuthController::class, 'registerPenitip'])->name('register.penitip.submit');
    
    Route::get('/register/organisasi', [AuthController::class, 'showRegisterOrganisasiForm'])->name('register.organisasi');
    Route::post('/register/organisasi', [AuthController::class, 'registerOrganisasi'])->name('register.organisasi.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// AJAX route for automatic cancellation check
Route::post('/check-expired-transactions', function() {
    $cancelledCount = \App\Models\Transaksi::cancelExpiredTransactions();
    return response()->json([
        'cancelled' => $cancelledCount,
        'status' => 'checked',
        'timestamp' => now()->toISOString()
    ]);
})->name('check.expired.transactions');

// Pembeli (Buyer) routes
Route::middleware(['auth:pembeli'])->group(function () {
    Route::get('/pembeli/profile', [PembeliController::class, 'profile'])->name('pembeli.profile');
    Route::get('/pembeli/transactions', [PembeliController::class, 'transactions'])->name('pembeli.transactions');
    Route::get('/pembeli/transactions/{id}', [PembeliController::class, 'transactionDetail'])->name('pembeli.transaction.detail');
    Route::post('/pembeli/transaction/{id}/rating', [PembeliController::class, 'simpanRating'])->name('pembeli.rating.simpan');
    Route::post('/transaksi/{id}/rating-penitip', [PembeliController::class, 'simpanRatingPenitip'])->name('pembeli.rating.penitip');
    Route::post('/barang/{id}/diskusi', [DiskusiController::class, 'store'])->name('diskusi.store');
    Route::get('/pembeli/password', [PembeliController::class, 'changePasswordForm'])->name('pembeli.password');
    Route::post('/pembeli/password', [PembeliController::class, 'changePassword'])->name('pembeli.password.update');
    Route::get('/pembeli/dashboard', [PembeliController::class, 'dashboard'])->name('pembeli.dashboard');
    Route::get('/pembeli/edit', [PembeliController::class, 'edit'])->name('pembeli.edit');
    Route::put('/pembeli/update', [PembeliController::class, 'update'])->name('pembeli.update');

    Route::get('/tukar-poin', [PointRewardController::class, 'showTukarPoin'])->name('pembeli.tukarPoin.view');
    Route::post('/tukar-poin', [PointRewardController::class, 'tukarPoin'])->name('pembeli.tukarPoin');
    
    Route::get('/pembeli/payment/{id}', [App\Http\Controllers\Pembeli\TransactionPaymentController::class, 'showPaymentForm'])->name('pembeli.payment.form');
    Route::post('/pembeli/payment/{id}/upload', [App\Http\Controllers\Pembeli\TransactionPaymentController::class, 'uploadPaymentProof'])->name('pembeli.payment.upload');
    Route::get('/pembeli/transaction/{id}/remaining-time', [App\Http\Controllers\Pembeli\TransactionPaymentController::class, 'getRemainingTime'])->name('pembeli.transaction.remaining-time');
    Route::get('/pembeli/transaction/{id}/status', [App\Http\Controllers\Pembeli\TransactionStatusController::class, 'getTransactionStatus'])->name('pembeli.transaction.status');
    Route::post('/pembeli/transactions/check-expired', [App\Http\Controllers\Pembeli\TransactionStatusController::class, 'checkExpiredTransactions'])->name('pembeli.transactions.check-expired');

    Route::post('/pembeli/cart/calculate-points', [App\Http\Controllers\Pembeli\CartController::class, 'calculatePointDiscount'])->name('pembeli.cart.calculate-points');
    
    // Cancel specific transaction route
    Route::post('/pembeli/transaction/{id}/cancel', function($id) {
        $success = \App\Models\Transaksi::cancelTransaction($id);
        return response()->json([
            'success' => $success,
            'message' => $success ? 'Transaksi berhasil dibatalkan' : 'Gagal membatalkan transaksi'
        ]);
    })->name('pembeli.transaction.cancel');
});

// Penitip (Consignor) routes
Route::middleware(['auth:penitip'])->group(function () {
    Route::get('/penitip/profile', [PenitipController::class, 'profile'])->name('penitip.profile');
    Route::get('/penitip/dashboard', [PenitipController::class, 'dashboard'])->name('penitip.dashboard');
    Route::get('/penitip/consignments', [PenitipController::class, 'consignments'])->name('penitip.consignments');
    Route::get('/penitip/consignments/{id}', [PenitipController::class, 'consignmentDetail'])->name('penitip.consignment.detail');
    Route::get('/penitip/sales', [PenitipController::class, 'sales'])->name('penitip.sales');
    Route::get('/penitip/sales/{id}', [PenitipController::class, 'saleDetail'])->name('penitip.sale.detail');
    Route::get('/penitip/edit', [PenitipController::class, 'edit'])->name('penitip.edit');
    Route::post('/penitip/update', [PenitipController::class, 'update'])->name('penitip.update');
    Route::get('/penitipan-list', [PenitipController::class, 'penitipanList'])->name('penitipan.list');
    Route::get('/penitip/password', [PenitipController::class, 'changePasswordForm'])->name('penitip.password');
    Route::post('/penitip/password', [PenitipController::class, 'changePassword'])->name('penitip.password.update');
    Route::post('/penitip/tukar-poin', [PenitipController::class, 'tukarPoinKeSaldo'])->name('penitip.tukar.poin');
    Route::post('/penitip/update-rating', [PenitipController::class, 'updateSemuaRatingPenitip'])->name('penitip.update.rating');

    Route::post('/perpanjang/{id}', [PenitipController::class, 'perpanjang'])->name('penitip.perpanjang');
    Route::post('/penitip/ambil-kembali/{id}', [PenitipController::class, 'ambilKembali'])->name('penitip.ambil_kembali');
    Route::patch('penitip/consignments/{id}/take', [PenitipController::class, 'takeItem'])->name('penitip.consignments.take');
    Route::get('/penitip/consignments/{id}', [PenitipController::class, 'show'])->name('penitip.consignments.detail');

    Route::get('/penitip/notifications/count', [PenitipController::class, 'getUnreadNotificationCount'])->name('penitip.notifications.count');
    Route::post('/penitip/notifications/{index}/read', [PenitipController::class, 'markNotificationAsRead'])->name('penitip.notifications.read');
    Route::post('/penitip/notifications/read-all', [PenitipController::class, 'markAllNotificationsAsRead'])->name('penitip.notifications.read-all');
});

// Alamat routes for buyers
Route::middleware(['auth:pembeli'])->prefix('pembeli')->name('pembeli.')->group(function () {
    Route::resource('alamat', AlamatPembeliController::class)->except(['show']);
    Route::post('alamat/{id}/default', [AlamatPembeliController::class, 'setDefault'])->name('alamat.default');
});

// Cart and Checkout routes
Route::middleware(['auth:pembeli'])->prefix('pembeli/cart')->name('pembeli.cart.')->group(function () {
    Route::get('/', [App\Http\Controllers\Pembeli\CartController::class, 'index'])->name('index');
    Route::post('/add/{id}', [App\Http\Controllers\Pembeli\CartController::class, 'addToCart'])->name('add');
    Route::delete('/remove/{id}', [App\Http\Controllers\Pembeli\CartController::class, 'removeFromCart'])->name('remove');
    Route::get('/checkout', [App\Http\Controllers\Pembeli\CartController::class, 'checkout'])->name('checkout');
    Route::post('/process', [App\Http\Controllers\Pembeli\CartController::class, 'processOrder'])->name('process');
    Route::post('/calculate-points', [App\Http\Controllers\Pembeli\CartController::class, 'calculatePointDiscount'])->name('calculate-points');
});

// Password Reset routes
Route::get('/password/reset/form', [PasswordResetFormController::class, 'showResetForm'])->name('password.reset.form');
Route::get('/password/reset/form', [PasswordResetFormController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset/update', [PasswordResetFormController::class, 'resetPassword'])->name('password.update');
Route::post('/password/reset/submit', [PasswordResetFormController::class, 'resetPassword'])->name('password.reset.submit');
Route::get('/password/reset', [PasswordResetLinkController::class, 'showRequestForm'])->name('password.request');
Route::post('/password/reset/send-link', [PasswordResetLinkController::class, 'sendResetLink'])->name('password.reset.submit');
Route::post('/password/email', [PasswordResetLinkController::class, 'sendResetLink'])->name('password.email');

// Admin routes
Route::middleware([\App\Http\Middleware\PegawaiMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::get('/admin/profile/edit', [AdminController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/admin/profile/update-password-dob', [AdminController::class, 'updatePasswordToDob'])->name('admin.profile.update-password-dob');

    //Route buat monitoring
    Route::get('/admin/monitoring', [ApiMonitoringController::class, 'index'])->name('admin.monitoring');
    Route::get('/admin/monitoring/chart/hour', [ApiMonitoringController::class, 'chartPerHour'])->name('admin.monitoring.chart.hour');
    Route::get('/admin/monitoring/chart/day', [ApiMonitoringController::class, 'chartPerDay'])->name('admin.monitoring.chart.day');
    Route::post('/admin/monitoring/block', [ApiMonitoringController::class, 'blockIp'])->name('admin.monitoring.block');
    Route::post('/admin/monitoring/unblock/{ip}', [ApiMonitoringController::class, 'unblockIp'])->name('admin.monitoring.unblock');
    Route::post('/admin/monitoring/alerts/{id}/read', [ApiMonitoringController::class, 'markAlertRead'])->name('admin.monitoring.alerts.read');

    // Organisasi management
    Route::get('/admin/organisasi', [AdminController::class, 'organisasiIndex'])->name('admin.organisasi.index');
    Route::get('/admin/organisasi/create', [AdminController::class, 'organisasiCreate'])->name('admin.organisasi.create');
    Route::post('/admin/organisasi', [AdminController::class, 'organisasiStore'])->name('admin.organisasi.store');
    Route::get('/admin/organisasi/{id}', [AdminController::class, 'organisasiShow'])->name('admin.organisasi.show');
    Route::get('/admin/organisasi/{id}/edit', [AdminController::class, 'organisasiEdit'])->name('admin.organisasi.edit');
    Route::put('/admin/organisasi/{id}', [AdminController::class, 'organisasiUpdate'])->name('admin.organisasi.update');
    Route::delete('/admin/organisasi/{id}', [AdminController::class, 'organisasiDestroy'])->name('admin.organisasi.destroy');

    // Pegawai management
    Route::get('/admin/pegawai', [AdminController::class, 'pegawaiIndex'])->name('admin.pegawai.index');
    Route::get('/admin/pegawai/create', [AdminController::class, 'pegawaiCreate'])->name('admin.pegawai.create');
    Route::post('/admin/pegawai', [AdminController::class, 'pegawaiStore'])->name('admin.pegawai.store');
    Route::get('/admin/pegawai/{id}', [AdminController::class, 'pegawaiShow'])->name('admin.pegawai.show');
    Route::delete('/admin/pegawai/{id}', [AdminController::class, 'pegawaiDestroy'])->name('admin.pegawai.destroy');
    Route::get('/admin/pegawai/{id}/edit', [AdminController::class, 'pegawaiEdit'])->name('admin.pegawai.edit');
    Route::put('/admin/pegawai/{id}', [AdminController::class, 'pegawaiUpdate'])->name('admin.pegawai.update');
    Route::post('/admin/pegawai/{id}/reset-password', [AdminController::class, 'pegawaiResetPassword'])->name('admin.pegawai.resetPassword');

    // Password change
    Route::get('/password/change', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.change.submit');

    // Organisasi password
    Route::get('/admin/organisasi/ubah-password', [OrganisasiController::class, 'editPassword'])->name('organisasi.password');
    Route::post('/admin/organisasi/ubah-password', [OrganisasiController::class, 'updatePassword'])->name('organisasi.password.update');
    
    Route::get('/admin/organisasi/donasi', [OrganisasiController::class, 'donasiIndex'])->name('organisasi.donasi');
    Route::post('/admin/reset-password/{id}', [AuthController::class, 'resetEmployeePassword'])->name('admin.reset.password');
});

// Organisasi routes
Route::middleware([\App\Http\Middleware\OrganisasiMiddleware::class . ':organisasi'])->group(function () {
    Route::get('/organisasi/dashboard', [OrganisasiController::class, 'dashboard'])->name('organisasi.dashboard');
    Route::get('/organisasi/profile', [OrganisasiController::class, 'profile'])->name('organisasi.profile');
    Route::get('/organisasi/profile/edit', [OrganisasiController::class, 'editProfile'])->name('organisasi.profile.edit');
    Route::put('/organisasi/profile', [OrganisasiController::class, 'updateProfile'])->name('organisasi.profile.update');
    Route::get('/organisasi/donasi', [OrganisasiController::class, 'donasiIndex'])->name('organisasi.donasi.index');
    Route::get('/organisasi/donasi/create', [OrganisasiController::class, 'donasiCreate'])->name('organisasi.donasi.create');
    Route::post('/organisasi/donasi', [OrganisasiController::class, 'donasiStore'])->name('organisasi.donasi.store');
    Route::get('/organisasi/donasi/{id}', [OrganisasiController::class, 'donasiShow'])->name('organisasi.donasi.show');
    Route::get('/organisasi/donasi/{id}/edit', [OrganisasiController::class, 'donasiEdit'])->name('organisasi.donasi.edit');
    Route::put('/organisasi/donasi/{id}', [OrganisasiController::class, 'donasiUpdate'])->name('organisasi.donasi.update');
    Route::delete('/organisasi/donasi/{id}', [OrganisasiController::class, 'donasiDestroy'])->name('organisasi.donasi.destroy');
});

// Customer Service routes
Route::middleware([\App\Http\Middleware\PegawaiMiddleware::class . ':cs'])->group(function () {
    Route::get('/cs/dashboard', [CustomerServiceController::class, 'dashboard'])->name('cs.dashboard');
    
    // Profile management
    Route::get('/cs/profile', [CustomerServiceController::class, 'profile'])->name('cs.profile');
    Route::get('/cs/profile/edit', [CustomerServiceController::class, 'edit'])->name('cs.profile.edit');
    Route::put('/cs/profile/update', [CustomerServiceController::class, 'updateProfile'])->name('cs.profile.update');
    Route::post('/cs/profile/update-password-dob', [CustomerServiceController::class, 'updatePasswordToDob'])->name('cs.profile.update-password-dob');
    
    // Penitip management
    Route::get('/cs/penitip', [CustomerServiceController::class, 'penitipIndex'])->name('cs.penitip.index');
    Route::get('/cs/penitip/create', [CustomerServiceController::class, 'penitipCreate'])->name('cs.penitip.create');
    Route::post('/cs/penitip', [CustomerServiceController::class, 'penitipStore'])->name('cs.penitip.store');
    Route::get('/cs/penitip/{id}', [CustomerServiceController::class, 'penitipShow'])->name('cs.penitip.show');
    Route::get('/cs/penitip/{id}/edit', [CustomerServiceController::class, 'penitipEdit'])->name('cs.penitip.edit');
    Route::put('/cs/penitip/{id}', [CustomerServiceController::class, 'penitipUpdate'])->name('cs.penitip.update');
    Route::delete('/cs/penitip/{id}', [CustomerServiceController::class, 'penitipDestroy'])->name('cs.penitip.destroy');

    // Diskusi management
    Route::get('/cs/diskusi', [CustomerServiceController::class, 'diskusiIndex'])->name('cs.diskusi.index');
    Route::get('/cs/diskusi/{id}', [CustomerServiceController::class, 'diskusiShow'])->name('cs.diskusi.show');
    Route::get('/cs/diskusi/{id}/reply', [CustomerServiceController::class, 'diskusiReplyForm'])->name('cs.diskusi.reply');
    Route::post('/cs/diskusi/{id}/reply', [CustomerServiceController::class, 'diskusiReply'])->name('cs.diskusi.reply.submit');
    Route::delete('/cs/diskusi/{id}', [CustomerServiceController::class, 'diskusiDestroy'])->name('cs.diskusi.destroy');

    // Payment verification
    Route::get('/cs/payment/verification', [App\Http\Controllers\CS\PaymentVerificationController::class, 'index'])->name('cs.payment.verification.index');
    Route::get('/cs/payment/verification/{id}', [App\Http\Controllers\CS\PaymentVerificationController::class, 'show'])->name('cs.payment.verification.show');
    Route::post('/cs/payment/verification/{id}/verify', [App\Http\Controllers\CS\PaymentVerificationController::class, 'verify'])->name('cs.payment.verification.verify');

    Route::get('/cs/merchandise', [App\Http\Controllers\CS\MerchandiseController::class, 'index'])->name('cs.merchandise.index');
    Route::get('/cs/klaim-merchandise', [PointRewardController::class, 'index'])->name('cs.klaim-merchandise.index');
    Route::put('/cs/klaim-merchandise/{id}', [PointRewardController::class, 'update'])->name('cs.klaim-merchandise.update');
    
});

// Pegawai Gudang routes
Route::middleware([PegawaiMiddleware::class . ':gudang'])->group(function () {
    Route::get('/gudang/dashboard', [PegawaiGudangController::class, 'dashboard'])->name('gudang.dashboard');
    Route::get('/gudang/profile', [PegawaiGudangController::class, 'profile'])->name('gudang.profile');
    Route::get('/gudang/edit', [PegawaiGudangController::class, 'edit'])->name('gudang.edit');
    Route::put('/profil/update', [PegawaiGudangController::class, 'updateProfile'])->name('gudang.profile.update');
    Route::post('/gudang/profile/update-password-dob', [PegawaiGudangController::class, 'updatePasswordToDob'])
         ->name('gudang.profile.update-password-dob');
    Route::get('/gudang/barang/{id}', [PegawaiGudangController::class, 'detailBarang'])->name('gudang.barang.detail');
    Route::get('/gudang/stok', [PegawaiGudangController::class, 'stok'])->name('gudang.stok');
    Route::get('/gudang/stok/tambah', [PegawaiGudangController::class, 'tambahBarangForm'])->name('gudang.stok.tambah');
    Route::post('/gudang/barang/store', [PegawaiGudangController::class, 'simpanBarang'])->name('gudang.barang.store');
    Route::get('/gudang/laporan/pdf', [PegawaiGudangController::class, 'downloadDistribusiPdf'])->name('gudang.laporan.pdf');
    Route::get('/gudang/pengambilan/riwayat', [PegawaiGudangController::class, 'riwayatPengambilan'])->name('gudang.pengambilan.riwayat');
    Route::get('/gudang/transaksi_pending', [PegawaiGudangController::class, 'daftarTransaksiPending'])->name('gudang.transaksi.pending');

    Route::get('/transaksi/hangus', [PegawaiGudangController::class, 'transaksiHangus'])->name('gudang.transaksi.hangus');
    Route::put('/gudang/transaksi-hangus/{id}', [PegawaiGudangController::class, 'konfirmasiHangus'])->name('gudang.konfirmasi-hangus');
    Route::get('/transaksi/{id}/detail', [PegawaiGudangController::class, 'detailTransaksi'])->name('gudang.transaksi.detail');

    Route::get('/gudang/distribusi/riwayat', [PegawaiGudangController::class, 'riwayatDistribusi'])->name('gudang.distribusi.riwayat');
    Route::get('/gudang/riwayat-distribusi', [PegawaiGudangController::class, 'downloadDistribusiPdf'])->name('gudang.riwayat.distribusi');
    Route::get('/gudang/nota-kurir/{id}', [PegawaiGudangController::class, 'cetakNotaKurir'])->name('gudang.nota.kurir');
    Route::get('/gudang/nota-ambil/{id}', [PegawaiGudangController::class, 'cetakNotaAmbil'])->name('gudang.nota.ambil');
    Route::get('/gudang/transaksi/{id}/detail', [PegawaiGudangController::class, 'detailTransaksi'])
    ->name('gudang.transaksi.detail');
    Route::get('/gudang/transaksi/{id}/show', [PegawaiGudangController::class, 'showTransaksi'])
    ->name('gudang.transaksi.show');

    Route::get('/transaksi/{id}/penjadwalan', [PegawaiGudangController::class, 'formPenjadwalanPengiriman'])->name('gudang.transaksi.penjadwalan.form');
    Route::post('/transaksi/{id}/penjadwalan', [PegawaiGudangController::class, 'simpanPenjadwalanPengiriman'])->name('gudang.transaksi.penjadwalan.simpan');
    Route::post('/transaksi/{id}/konfirmasi-terima', [PegawaiGudangController::class, 'konfirmasiDiterima'])->name('gudang.transaksi.konfirmasi');

    Route::get('penjadwalan', [PegawaiGudangController::class, 'indexPenjadwalan'])->name('gudang.penjadwalan.index');
    Route::get('{id}/jadwalkan', [PegawaiGudangController::class, 'formPenjadwalanPengiriman'])->name('gudang.transaksi.jadwalkan.form');
    Route::post('{id}/jadwalkan', [PegawaiGudangController::class, 'simpanPenjadwalanPengiriman'])->name('gudang.transaksi.jadwalkan.simpan');
    Route::post('{id}/ambil-sendiri', [PegawaiGudangController::class, 'jadwalkanPengambilanSendiri'])->name('gudang.transaksi.ambil.sendiri');
    Route::post('{id}/konfirmasi-diterima', [PegawaiGudangController::class, 'konfirmasiDiterima'])->name('gudang.transaksi.diterima');
    Route::get('gudang/transaksi/{id}/detail', [PegawaiGudangController::class, 'detailKurir'])
    ->name('gudang.transaksi.detail');
    Route::get('/gudang/transaksi/{id}', [PegawaiGudangController::class, 'show'])->name('gudang.transaksi.detail');

    Route::put('/gudang/barang/{id}', [PegawaiGudangController::class, 'updateBarang'])->name('gudang.barang.update');
    Route::put('/gudang/barang/{id}/terjual', [PegawaiGudangController::class, 'markBarangTerjual'])->name('gudang.barang.terjual');

    Route::prefix('gudang/penitipan')->group(function () {
        Route::get('/', [PegawaiGudangController::class, 'daftarPenitipan'])->name('gudang.penitipan.index');
        Route::get('/create', [PegawaiGudangController::class, 'createPenitipan'])->name('gudang.penitipan.create');
        Route::get('/gudang/penitipan/search', [PegawaiGudangController::class, 'searchDaftarPenitipan'])->name('gudang.penitipan.search');
        Route::post('/store', [PegawaiGudangController::class, 'storePenitipan'])->name('gudang.penitipan.store');
        Route::get('/{id}/detail', [PegawaiGudangController::class, 'detailPenitipan'])->name('gudang.penitipan.detail');
        Route::get('/{id}/edit', [PegawaiGudangController::class, 'editPenitipan'])->name('gudang.penitipan.edit');
        Route::put('/{id}/update', [PegawaiGudangController::class, 'updatePenitipan'])->name('gudang.penitipan.update');
        Route::put('/{id}/perpanjang', [PegawaiGudangController::class, 'perpanjangPenitipan'])->name('gudang.penitipan.perpanjang');
        Route::put('/{id}/akhiri', [PegawaiGudangController::class, 'akhiriPenitipan'])->name('gudang.penitipan.akhiri');
        Route::get('/cari', [PegawaiGudangController::class, 'cariTransaksiTitipan'])->name('gudang.penitipan.cari');
        Route::get('/laporan', [PegawaiGudangController::class, 'penitipanLaporan'])->name('gudang.penitipan.laporan');
        Route::get('/laporan/pdf', [PegawaiGudangController::class, 'penitipanLaporanPdf'])->name('gudang.penitipan.laporan.pdf');
        Route::get('/{id}/pdf', [PegawaiGudangController::class, 'exportPDF'])->name('gudang.penitipan.pdf');
        Route::get('/{id}/preview', [PegawaiGudangController::class, 'previewPDF'])->name('gudang.penitipan.preview');
        Route::get('/{id}/print', [PegawaiGudangController::class, 'printNota'])->name('gudang.penitipan.print');

        Route::get('/uang', [PegawaiGudangController::class, 'penitipUang'])->name('gudang.penitip.uang');
    });
});

Route::middleware([\App\Http\Middleware\PegawaiMiddleware::class . ':owner'])->group(function () {
    // Dashboard & Profile
    Route::get('/owner/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
    Route::get('/owner/profile', [OwnerController::class, 'profile'])->name('owner.profile');
    Route::get('/owner/profile/edit', [OwnerController::class, 'edit'])->name('owner.profile.edit');
    Route::put('/owner/profile', [OwnerController::class, 'updateProfile'])->name('owner.profile.update');
    Route::post('/owner/profile/update-password-dob', [OwnerController::class, 'updatePasswordToDob'])->name('owner.profile.update-password-dob');
    
    // Donation management
    Route::get('/owner/donasi/requests', [OwnerController::class, 'donationRequests'])->name('owner.donasi.requests');
    Route::get('/owner/donasi/history', [OwnerController::class, 'donationHistory'])->name('owner.donasi.history');
    Route::post('/owner/donasi/{id}/approve', [OwnerController::class, 'approveDonation'])->name('owner.donasi.approve');
    Route::post('/owner/donasi/{id}/reject', [OwnerController::class, 'rejectDonation'])->name('owner.donasi.reject');
    Route::post('/owner/donasi/update-info/{id}', [OwnerController::class, 'updateDonationInfo'])->name('owner.donasi.update-info');
    Route::get('/owner/donasi/allocate', [OwnerController::class, 'allocateDonation'])->name('owner.donasi.allocate');
    Route::post('/owner/donasi/allocate', [OwnerController::class, 'allocateDonationToOrganization'])->name('owner.donasi.allocate.store');
    Route::post('/owner/donasi/allocate/{id}', [OwnerController::class, 'allocateDonationToOrganization'])->name('owner.donasi.allocate.organization');

    // Laporan Dashboard (ganti semua laporan PDF ke satu halaman dashboard laporan)
    Route::get('/owner/laporan', [LaporanPDFController::class, 'dashboard'])->name('owner.laporan.dashboard');

    Route::get('/owner/laporan/penjualan-bulanan', [LaporanPDFController::class, 'penjualanBulanan'])->name('owner.laporan.penjualan-bulanan');
    Route::get('/owner/laporan/komisi-bulanan', [LaporanPDFController::class, 'komisiBulanan'])->name('owner.laporan.komisi-bulanan');
    Route::get('/owner/laporan/stok-gudang', [LaporanPDFController::class, 'stokGudang'])->name('owner.laporan.stok-gudang');
    Route::get('/owner/laporan/donasi-barang', [LaporanPDFController::class, 'donasiBarang'])->name('owner.laporan.donasi-barang');
    Route::get('/owner/laporan/masa-titip-habis', [LaporanPDFController::class, 'masaTitipHabis'])->name('owner.laporan.masa-titip-habis');
    Route::get('/owner/laporan/penjualan-kategori', [LaporanPDFController::class, 'penjualanKategori'])->name('owner.laporan.penjualan-kategori');
    Route::get('/owner/laporan/request-donasi', [LaporanPDFController::class, 'requestDonasi'])->name('owner.laporan.request-donasi');
    Route::get('/owner/laporan/transaksi-penitip', [LaporanPDFController::class, 'transaksiPenitip'])->name('owner.laporan.transaksi-penitip');

        // Filter form routes
    Route::get('/owner/laporan/donasi-barang/filter', [LaporanPDFController::class, 'showDonasiBarangFilter'])->name('owner.laporan.donasi-barang.filter');
    Route::get('/owner/laporan/request-donasi/filter', [LaporanPDFController::class, 'showRequestDonasiFilter'])->name('owner.laporan.request-donasi.filter');
    Route::get('/owner/laporan/transaksi-penitip/filter', [LaporanPDFController::class, 'showTransaksiPenitipFilter'])->name('owner.laporan.transaksi-penitip.filter');

    // Filtered PDF generation routes
    Route::get('/owner/laporan/donasi-barang', [LaporanPDFController::class, 'donasiBarang'])->name('owner.laporan.donasi-barang');
    Route::get('/owner/laporan/request-donasi', [LaporanPDFController::class, 'requestDonasi'])->name('owner.laporan.request-donasi');
    Route::get('/owner/laporan/transaksi-penitip', [LaporanPDFController::class, 'transaksiPenitip'])->name('owner.laporan.transaksi-penitip');

    Route::get('/owner/laporan/donasi-elektronik/filter', [LaporanPDFController::class, 'showDonasiElektronikFilter'])->name('owner.laporan.donasi-elektronik.filter');
    Route::get('/owner/laporan/donasi-elektronik', [LaporanPDFController::class, 'donasiBarangElektronik'])->name('owner.laporan.donasi-elektronik');
});

// Additional Password Reset routes
Route::get('/password/reset', [PasswordResetLinkController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/password/reset/send', [PasswordResetLinkController::class, 'sendResetLink'])->name('password.send-link');
Route::get('/reset-password', [PasswordResetFormController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetFormController::class, 'resetPassword'])->name('password.update');
Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'submitReset'])->name('password.reset.submit');
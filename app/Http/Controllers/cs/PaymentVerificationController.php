<?php

namespace App\Http\Controllers\cs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pembeli;
use App\Models\Barang;
use App\Models\Penitipan;
use App\Models\Komisi;
use App\Models\Reusemart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PaymentVerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')
                          ->whereNotNull('BUKTI_PEMBAYARAN')
                          ->with(['pembeli', 'barang']);

        if ($request->filled('search')) {
            $query->where('NOMOR_TRANSAKSI', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('TANGGAL_UPLOAD_BUKTI', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('TANGGAL_UPLOAD_BUKTI', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('TANGGAL_UPLOAD_BUKTI', 'asc')->paginate(10);
        return view('cs.payment.verification.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')
            ->whereNotNull('BUKTI_PEMBAYARAN')
            ->with(['pembeli', 'barang', 'penitipan'])
            ->findOrFail($id);

        return view('cs.payment.verification.show', compact('transaction'));
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Valid,Invalid',
            'catatan' => 'nullable|string|max:500'
        ]);
        
        $transaction = Transaksi::where('STATUS_TRANSAKSI', 'Menunggu Konfirmasi')
                               ->whereNotNull('BUKTI_PEMBAYARAN')
                               ->with(['pembeli', 'barang', 'penitipan'])
                               ->findOrFail($id);
        
        $cs = Auth::guard('pegawai')->user();
        
        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            if ($request->status == 'Valid') {
                // Payment is valid - approve transaction
                $transaction->STATUS_VERIFIKASI = 'Valid';
                $transaction->STATUS_TRANSAKSI = 'Disiapkan';
                $transaction->TANGGAL_VERIFIKASI = Carbon::now();
                $transaction->VERIFIED_BY = $cs->ID_PEGAWAI;
                $transaction->CATATAN_VERIFIKASI = $request->catatan;
                
                // Add points to buyer (points earned from purchase)
                $pembeli = $transaction->pembeli;
                $subtotal = $transaction->TOTAL_TRANSAKSI - $transaction->BIAYA_PENGIRIMAN + ($transaction->POIN_DITUKAR * 1000);
                $pointsEarned = floor($subtotal / 10000);
                
                // Add 20% bonus if purchase > 500,000
                if ($subtotal > 500000) {
                    $pointsEarned = ceil($pointsEarned * 1.2);
                }
                
                $pembeli->POINT_PEMBELI += $pointsEarned;
                $pembeli->save();

                if ($transaction->STATUS_TRANSAKSI == 'Disiapkan') {
                    $harga = $transaction->barang->HARGA;
                    $tanggalTerjual = Carbon::parse($transaction->TANGGAL_TRANSAKSI ?? now());
                    $penitipan = $transaction->penitipan;

                    if (!$penitipan) {
                        return back()->with('error', 'Data penitipan tidak ditemukan.');
                    }


                    // Jika terjual lebih dari 30 hari setelah tanggal berakhir
                    $selisihHari = Carbon::parse($tanggalTerjual)->diffInDays($penitipan->TANGGAL_MULAI);

                    // Baru pakai untuk komisi
                    if ($selisihHari > 30) {
                        $komisiDasar = $harga * 0.30;
                    } else {
                        $komisiDasar = $harga * 0.20;
                    }

                    $komisiPenitip = 0;
                    $komisiPegawai = 0;

                    // Jika barang milik penitip
                    if ($penitipan->ID_PENITIP != null && $penitipan->ID_BARANGHUNTER == null) {
                        $selisihHari = Carbon::parse($penitipan->TANGGAL_MULAI)->diffInDays($tanggalTerjual, false);
                        $komisiPenitip = $komisiDasar * ($selisihHari < 7 ? 0.15 : 0.10);
                    }

                    // Jika barang milik pegawai hunter
                    elseif ($penitipan->ID_PENITIP == null && $penitipan->ID_BARANGHUNTER != null) {
                        $komisiPegawai = $komisiDasar * 0.05;
                    }

                    $komisiReusemart = $komisiDasar - $komisiPenitip - $komisiPegawai;

                    // Simpan data komisi
                    $komisi = Komisi::create([
                        'ID_TRANSAKSI'     => $transaction->ID_TRANSAKSI,
                        'TOTAL_KOMISI'     => $komisiDasar,
                        'KOMISI_PENITIP'   => $komisiPenitip,
                        'KOMISI_PEGAWAI'   => $komisiPegawai,
                        'KOMISI_REUSEMART' => $komisiReusemart,
                        'TANGGAL_KOMISI'   => now(),
                        'STATUS_KOMISI'    => 'Disiapkan',
                    ]);

                    // Update transaksi dengan ID_KOMISI
                    $transaction->update(['ID_KOMISI' => $komisi->ID_KOMISI]);

                    // === Distribusi Uang ===
                    $reusemart = Reusemart::first();
                    if ($reusemart && $komisiReusemart > 0) {
                        $reusemart->increment('TOTAL_PENDAPATAN', $komisiReusemart);
                    }

                    if ($komisiPenitip > 0 && $penitipan->penitip) {
                        $penitipan->penitip->increment('UANG_PENITIP', $komisiPenitip);
                    }

                    // if ($komisiPegawai > 0 && $penitipan->barangHunter->pegawai) {
                    //     $penitipan->barangHunter->pegawai->increment('UANG_PEGAWAI', $komisiPegawai);
                    // }

                    $pegawai = $penitipan->barangHunter?->pegawai;

                    if ($komisiPegawai > 0 && $pegawai) {
                        $pegawai->UANG_PEGAWAI += $komisiPegawai;
                        $pegawai->save();
                    }
                }
                
                $transaction->save();
                
                // UPDATE STATUS PENITIPAN DAN TANGGAL BERAKHIR - INI YANG PENTING!
                $this->updatePenitipanStatusAndDate($transaction);
                
                // CREATE NOTIFICATION FOR PENITIP
                $this->createPenitipNotification($transaction);
                
                DB::commit();
                
                return redirect()->route('cs.payment.verification.index')
                               ->with('success', 'Pembayaran berhasil diverifikasi. Transaksi telah disetujui, status penitipan diubah menjadi "Selesai" dengan tanggal berakhir hari ini, dan notifikasi telah dikirim ke penitip.');
                
            } else {
                // Payment is invalid - reject transaction
                $transaction->STATUS_VERIFIKASI = 'Invalid';
                $transaction->STATUS_TRANSAKSI = 'Batal';
                $transaction->TANGGAL_VERIFIKASI = Carbon::now();
                $transaction->VERIFIED_BY = $cs->ID_PEGAWAI;
                $transaction->CATATAN_VERIFIKASI = $request->catatan;
                
                // Return points to buyer if they were redeemed
                if ($transaction->POIN_DITUKAR > 0) {
                    $pembeli = $transaction->pembeli;
                    $pembeli->POINT_PEMBELI += $transaction->POIN_DITUKAR;
                    $pembeli->save();
                }
                
                // Return stock to product
                $barang = $transaction->barang;
                $barang->stok += 1;
                $barang->STATUS = 'Tersedia';
                $barang->save();
                
                $transaction->save();
                
                DB::commit();
                
                return redirect()->route('cs.payment.verification.index')
                               ->with('success', 'Pembayaran ditolak. Transaksi dibatalkan dan stok dikembalikan.');
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->route('cs.payment.verification.index')
                           ->with('error', 'Terjadi kesalahan saat memproses verifikasi: ' . $e->getMessage());
        }
    }


/**
     * Update status penitipan menjadi "Selesai" dan set tanggal berakhir menjadi hari ini
     */
    private function updatePenitipanStatusAndDate($transaction)
    {
        try {
            // Cari penitipan berdasarkan ID_PENITIPAN dari transaksi
            if ($transaction->ID_PENITIPAN) {
                $penitipan = Penitipan::find($transaction->ID_PENITIPAN);
                
                if ($penitipan) {
                    // Update status penitipan menjadi "Selesai"
                    $penitipan->STATUS_PENITIPAN = 'Selesai';
                    
                    // PENTING: Set tanggal berakhir menjadi hari ini
                    $penitipan->TANGGAL_BERAKHIR = Carbon::now()->format('Y-m-d');
                    
                    $penitipan->save();
                    
                    \Log::info("Status penitipan ID {$penitipan->ID_PENITIPAN} berhasil diubah menjadi 'Selesai' dengan tanggal berakhir " . Carbon::now()->format('Y-m-d') . " untuk transaksi ID {$transaction->ID_TRANSAKSI}");
                } else {
                    \Log::warning("Penitipan dengan ID {$transaction->ID_PENITIPAN} tidak ditemukan untuk transaksi ID {$transaction->ID_TRANSAKSI}");
                }
            } else {
                \Log::info("Transaksi ID {$transaction->ID_TRANSAKSI} tidak memiliki ID_PENITIPAN (kemungkinan barang bukan titipan)");
            }
            
        } catch (\Exception $e) {
            \Log::error("Error updating penitipan status and date for transaction {$transaction->ID_TRANSAKSI}: " . $e->getMessage());
            // Tidak throw exception agar tidak mengganggu proses utama
        }
    }
    
    /**
     * Create notification for penitip when their item is sold
     */
    private function createPenitipNotification($transaction)
    {
        try {
            // Get penitip from transaction
            $penitipId = null;
            
            // Check if the item belongs to a penitip through penitipan
            if ($transaction->penitipan && $transaction->penitipan->penitip) {
                $penitipId = $transaction->penitipan->penitip->ID_PENITIP;
            }
            
            if ($penitipId) {
                // Calculate penitip earnings (example: 80% of sale price - commission)
                $salePrice = $transaction->barang->HARGA;
                $commission = $salePrice * 0.2; // 20% commission
                $penitipEarnings = $salePrice - $commission;
                
                // Create notification data
                $notificationData = [
                    'type' => 'barang_terjual',
                    'title' => 'Barang Anda Terjual!',
                    'message' => "Selamat! Barang '{$transaction->barang->NAMA_BARANG}' telah terjual dengan harga Rp " . number_format($salePrice, 0, ',', '.') . ". Pendapatan Anda: Rp " . number_format($penitipEarnings, 0, ',', '.') . ". Status penitipan telah diubah menjadi 'Selesai'.",
                    'transaction_id' => $transaction->ID_TRANSAKSI,
                    'item_name' => $transaction->barang->NAMA_BARANG,
                    'sale_price' => $salePrice,
                    'earnings' => $penitipEarnings,
                    'created_at' => now()->toISOString(),
                    'is_read' => false
                ];
                
                // Store notification in cache for the specific penitip
                $cacheKey = "penitip_notifications_{$penitipId}";
                $existingNotifications = Cache::get($cacheKey, []);
                
                // Add new notification to the beginning of array
                array_unshift($existingNotifications, $notificationData);
                
                // Keep only last 10 notifications
                $existingNotifications = array_slice($existingNotifications, 0, 10);
                
                // Store back to cache (expire in 30 days)
                Cache::put($cacheKey, $existingNotifications, now()->addDays(30));
                
                // Update penitip's money/earnings if needed
                $penitip = $transaction->penitipan->penitip;
                $penitip->UANG_PENITIP = ($penitip->UANG_PENITIP ?? 0) + $penitipEarnings;
                $penitip->save();
                
                \Log::info("Notifikasi berhasil dibuat untuk penitip ID {$penitipId} untuk transaksi ID {$transaction->ID_TRANSAKSI}");
            }
            
        } catch (\Exception $e) {
            \Log::error("Error creating penitip notification for transaction {$transaction->ID_TRANSAKSI}: " . $e->getMessage());
            // Tidak throw exception agar tidak mengganggu proses utama
        }
    }
}
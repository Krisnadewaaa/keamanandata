<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use App\Models\Penitipan;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class PenitipController extends Controller
{
    /**
     * Show consignor dashboard.
     */
    public function dashboard(Request $request)
    {
        $penitip = Auth::guard('penitip')->user();
        $keyword = $request->input('search');

        // Ambil daftar penitipan dengan relasi
        $penitipanList = Penitipan::with(['barang.kategori'])
            ->where('ID_PENITIP', $penitip->ID_PENITIP)
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('TANGGAL_BERAKHIR', 'like', "%{$keyword}%")
                        ->orWhereHas('barang', function ($q) use ($keyword) {
                            $q->where('NAMA_BARANG', 'like', "%{$keyword}%")
                            ->orWhere('DESKRIPSI', 'like', "%{$keyword}%")
                            ->orWhereHas('kategori', function ($k) use ($keyword) {
                                $k->where('JENIS_KATEGORI', 'like', "%{$keyword}%");
                            });
                        });
                });
            })
            ->orderByDesc('TANGGAL_MULAI')
            ->get();

        $notFound = $keyword && $penitipanList->isEmpty();
        $transaksi = $penitip->transaksiTerbaru();

        // Ambil semua ID_BARANG milik penitip
        $idBarangMilikPenitip = Penitipan::where('ID_PENITIP', $penitip->ID_PENITIP)
            ->pluck('ID_BARANG');

        // Cek status transaksi untuk notifikasi
        $notifications = [];

        if ($idBarangMilikPenitip->isNotEmpty()) {
            $sedangDikirim = Transaksi::whereIn('ID_BARANG', $idBarangMilikPenitip)
                ->where('STATUS_TRANSAKSI', 'Sedang Dikirim')
                ->exists();

            $transaksiSelesai = Transaksi::whereIn('ID_BARANG', $idBarangMilikPenitip)
                ->where('STATUS_TRANSAKSI', 'Selesai')
                ->exists();

            if ($sedangDikirim) {
                $notifications[] = [
                    'type' => 'success',
                    'text' => 'Salah satu barang Anda sedang dalam proses pengiriman ke pembeli.'
                ];
            }

            if ($transaksiSelesai) {
                $notifications[] = [
                    'type' => 'info',
                    'text' => 'Salah satu barang Anda sudah diterima pembeli dan transaksi selesai.'
                ];
            }
        }

        if (!empty($notifications)) {
            session()->flash('notifications', $notifications);
        }

        return view('penitip.dashboard', compact('penitip', 'penitipanList', 'transaksi', 'keyword', 'notFound'));
    }
    /**
     * Show consignor profile.
     */
    
    public function profile()
    {
        $user = Auth::guard('penitip')->user();
        $penitip = Penitip::findOrFail($user->ID_PENITIP);
        
        // Get notifications for this penitip
        $notifications = $this->getNotifications($user->ID_PENITIP);
        
        return view('penitip.profile', compact('penitip', 'notifications'));
    }

    /**
     * 
     * Edit consignor profile form.
     */
    public function edit()
    {
        $penitip = Auth::guard('penitip')->user();
        return view('penitip.edit', compact('penitip'));
    }

    /**
     * Update consignor profile.
     */
    public function update(Request $request)
    {
        $penitip = Auth::guard('penitip')->user();

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:penitip,EMAIL_PENITIP,' . $penitip->ID_PENITIP . ',ID_PENITIP',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('foto_profil')) {
            if ($penitip->FOTO_PROFIL && file_exists(public_path($penitip->FOTO_PROFIL))) {
                unlink(public_path($penitip->FOTO_PROFIL));
            }

            $filename = time() . '_' . $request->file('foto_profil')->getClientOriginalName();
            $request->file('foto_profil')->move(public_path('images/profilePenitip'), $filename);
            $penitip->FOTO_PROFIL = 'images/profilePenitip/' . $filename;
        }

        $penitip->NAMA_PENITIP = $request->nama;
        $penitip->EMAIL_PENITIP = $request->email;
        $penitip->save();

        return redirect()->route('penitip.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Show change password form.
     */
    public function changePasswordForm()
    {
        return view('penitip.change-password');
    }

    /**
     * Change consignor password.
     */
    public function changePassword(Request $request)
    {
        $penitip = Auth::guard('penitip')->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        if (!Hash::check($request->current_password, $penitip->PASSWORD_PENITIP)) {
            return back()->with('error', 'Password saat ini salah.');
        }

        $penitip->PASSWORD_PENITIP = Hash::make($request->password);
        $penitip->save();

        return redirect()->route('penitip.profile')->with('success', 'Password berhasil diubah.');
    }

    /**
     * List consignments with filters.
     */
    public function consignments(Request $request)
    {
        $penitip = Auth::guard('penitip')->user();
        $query = Penitipan::where('ID_PENITIP', $penitip->ID_PENITIP);

        if ($request->status && $request->status != 'all') {
            $query->where('STATUS_PENITIPAN', $request->status);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('TANGGAL_MULAI', [$request->start_date, $request->end_date]);
        }

        $query->orderBy('TANGGAL_MULAI', $request->sort === 'date_asc' ? 'asc' : 'desc');

        $consignments = $query->paginate(10);

        return view('penitip.consignment', compact('consignments'));
    }

    /**
     * Consignment detail.
     */
    public function consignmentDetail($id)
    {
        $penitip = Auth::guard('penitip')->user();
        $consignment = Penitipan::where('ID_PENITIP', $penitip->ID_PENITIP)
            ->where('ID_PENITIPAN', $id)
            ->firstOrFail();

        return view('penitip.consignment-detail', compact('consignment'));
    }

    /**
     * Sales history.
     */
    public function sales(Request $request)
    {
        $penitip = Auth::guard('penitip')->user();

        $penitipanIds = Penitipan::where('ID_PENITIP', $penitip->ID_PENITIP)->pluck('ID_PENITIPAN');
        $barangIds = Barang::whereIn('ID_PENITIPAN', $penitipanIds)->pluck('ID_BARANG');

        $query = Transaksi::whereIn('ID_BARANG', $barangIds)->where('STATUS_TRANSAKSI', 'Selesai');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('TANGGAL_TRANSAKSI', [$request->start_date, $request->end_date]);
        }

        if ($request->sort) {
            $sortMap = [
                'date_asc' => ['TANGGAL_TRANSAKSI', 'asc'],
                'date_desc' => ['TANGGAL_TRANSAKSI', 'desc'],
                'amount_asc' => ['TOTAL_TRANSAKSI', 'asc'],
                'amount_desc' => ['TOTAL_TRANSAKSI', 'desc'],
            ];
            $sort = $sortMap[$request->sort] ?? ['TANGGAL_TRANSAKSI', 'desc'];
            $query->orderBy(...$sort);
        } else {
            $query->orderBy('TANGGAL_TRANSAKSI', 'desc');
        }

        $sales = $query->paginate(10);
        return view('penitip.sales', compact('sales'));
    }

    /**
     * Sales detail.
     */
    public function saleDetail($id)
    {
        $penitip = Auth::guard('penitip')->user();

        $penitipanIds = Penitipan::where('ID_PENITIP', $penitip->ID_PENITIP)->pluck('ID_PENITIPAN');
        $barangIds = Barang::whereIn('ID_PENITIPAN', $penitipanIds)->pluck('ID_BARANG');

        $sale = Transaksi::whereIn('ID_BARANG', $barangIds)
            ->where('ID_TRANSAKSI', $id)
            ->firstOrFail();

        return view('penitip.sales-detail', compact('sale'));
    }

    /**
     * Extend a consignment.
     */


     public function perpanjang($idBarang)
    {
        $penitip = Auth::guard('penitip')->user();

        // Cari penitipan milik penitip yang terkait dengan barang ini
        $penitipan = Penitipan::where('ID_BARANG', $idBarang)
            ->where('ID_PENITIP', $penitip->ID_PENITIP)
            ->first();

        if (!$penitipan) {
            return redirect()->back()->with('error', 'Penitipan tidak ditemukan untuk barang ini atau bukan milik Anda.');
        }

        // Cek transaksi dengan status "Sedang Dikirim" atau "Selesai" untuk barang ini
        $adaTransaksi = Transaksi::where('ID_BARANG', $idBarang)
            ->whereIn('STATUS_TRANSAKSI', ['Sedang Dikirim', 'Selesai'])
            ->exists();

        if ($adaTransaksi) {
            return redirect()->back()->with('error', 'Barang sudah pernah dalam proses pengiriman atau transaksi selesai, tidak bisa diperpanjang.');
        }

        // Tambah tanggal berakhir 30 hari
        if (!$penitipan->TANGGAL_BERAKHIR) {
            $penitipan->TANGGAL_BERAKHIR = now()->addDays(30);
        } else {
            $penitipan->TANGGAL_BERAKHIR = \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR)->copy()->addDays(30);
        }
        $penitipan->STATUS_PENITIPAN = 'Aktif';
        $penitipan->save();

        return redirect()->back()->with('success', 'Masa penitipan berhasil diperpanjang 30 hari.');
    }

    public function ambilKembali($id)
    {
        $penitipan = Penitipan::findOrFail($id);
        $barang = $penitipan->barang;
        $tanggalSekarang = Carbon::now();
        $tanggalBerakhir = Carbon::parse($penitipan->TANGGAL_BERAKHIR);

        if ($tanggalSekarang->lt($tanggalBerakhir)) {
            return back()->with('error', 'Tanggal berakhir belum habis, tidak bisa ambil kembali.');
        }

        $barang->STATUS = 'Diambil Kembali';
        $barang->save();

        $penitipan->STATUS_PENITIPAN = 'Diambil Kembali';
        $penitipan->save();

        return back()->with('success', 'Barang berhasil diambil kembali.');
    }

    public function takeItem($id)
    {
        // Ambil data penitipan
        $penitipan = DB::table('penitipan')
            ->where('ID_PENITIPAN', $id)
            ->first();

        if (!$penitipan) {
            abort(404, 'Data penitipan tidak ditemukan');
        }

        // Update status penitipan jadi kosong
        DB::table('penitipan')
            ->where('ID_PENITIPAN', $id)
            ->update(['STATUS_PENITIPAN' => 'Diambil Kembali']);

        // Update status barang dan hapus foto produk
        DB::table('barang')
            ->where('ID_BARANG', $penitipan->ID_BARANG)
            ->update([
                'STATUS' => 'Diambil Kembali',
                'FOTO_PRODUK' => null,
                'FOTO_PRODUK2' => null,
            ]);

        return redirect()->route('penitip.consignments.detail', $id)
            ->with('success', 'Barang berhasil diambil, status diperbarui, dan foto dihapus.');
    }


    public function cekStatusKadaluarsa()
    {
        // Ambil penitipan yang sudah lewat tanggal_berakhir (30 hari) dan barang statusnya Aktif
        $expiredPenitipan = DB::table('penitipan')
            ->join('barang', 'penitipan.ID_BARANG', '=', 'barang.ID_BARANG')
            ->where('barang.STATUS', 'Aktif')
            ->whereRaw('DATEDIFF(CURDATE(), penitipan.TANGGAL_BERAKHIR) > 30')
            ->select('penitipan.ID_PENITIPAN', 'penitipan.ID_BARANG')
            ->get();

        foreach ($expiredPenitipan as $item) {
            // Update status barang jadi 'Tidak Aktif' (atau kosong)
            DB::table('barang')
                ->where('ID_BARANG', $item->ID_BARANG)
                ->update(['STATUS' => 'Tidak Aktif']); 

            // Update status penitipan jadi 'Kadaluarsa'
            DB::table('penitipan')
                ->where('ID_PENITIPAN', $item->ID_PENITIPAN)
                ->update(['STATUS_PENITIPAN' => 'Kadaluarsa']);
        }
    }

    public function show($id)
    {
        // Ambil data penitipan berdasarkan ID
        $consignment = DB::table('penitipan')
            ->where('ID_PENITIPAN', $id)
            ->first();

        if (!$consignment) {
            abort(404, 'Data penitipan tidak ditemukan');
        }

        // Ambil data barang terkait
        $barang = DB::table('barang')
            ->where('ID_BARANG', $consignment->ID_BARANG)
            ->first();

        // Ambil kategori barang jika barang tersedia
        $kategori = null;
        if ($barang) {
            $kategori = DB::table('kategori_barang') // ← ini sudah disesuaikan
                ->where('ID_KATEGORI', $barang->ID_KATEGORI)
                ->first();
        }

        // Kirim data ke view, disatukan dalam satu objek
        return view('penitip.consignment-detail', [
            'consignment' => (object) array_merge((array) $consignment, [
                'barang' => $barang,
                'kategori' => $kategori ?? (object) ['NAMA_KATEGORI' => '-'],
            ]),
        ]);
    }

    public function tukarPoinKeSaldo(Request $request)
    {
        $penitip = Auth::guard('penitip')->user();

        $jumlahPoin = $penitip->POIN_PENITIP ?? 0;

        if ($jumlahPoin <= 0) {
            return back()->with('error', 'Anda tidak memiliki poin untuk ditukar.');
        }

        $saldoDariPoin = $jumlahPoin * 10000;

        // Tambahkan saldo ke penitip
        $penitip->UANG_PENITIP += $saldoDariPoin;

        // Reset poin ke 0
        $penitip->POIN_PENITIP = 0;

        $penitip->save();

        return back()->with('success', 'Poin berhasil ditukar menjadi saldo sebesar Rp ' . number_format($saldoDariPoin, 0, ',', '.'));
    }

    /**
         * Get notifications for penitip
         */
        private function getNotifications($penitipId)
        {
            $cacheKey = "penitip_notifications_{$penitipId}";
            return Cache::get($cacheKey, []);
        }

        /**
         * Get unread notification count
         */
        public function getUnreadNotificationCount()
        {
            $user = Auth::guard('penitip')->user();
            $notifications = $this->getNotifications($user->ID_PENITIP);
            
            $unreadCount = collect($notifications)->where('is_read', false)->count();
            
            return response()->json(['count' => $unreadCount]);
        }

        /**
         * Mark notification as read
         */
        public function markNotificationAsRead($notificationIndex)
        {
            $user = Auth::guard('penitip')->user();
            $cacheKey = "penitip_notifications_{$user->ID_PENITIP}";
            $notifications = Cache::get($cacheKey, []);
            
            if (isset($notifications[$notificationIndex])) {
                $notifications[$notificationIndex]['is_read'] = true;
                Cache::put($cacheKey, $notifications, now()->addDays(30));
            }
            
            return response()->json(['success' => true]);
        }

        /**
         * Mark all notifications as read
         */
        public function markAllNotificationsAsRead()
        {
            $user = Auth::guard('penitip')->user();
            $cacheKey = "penitip_notifications_{$user->ID_PENITIP}";
            $notifications = Cache::get($cacheKey, []);
            
            foreach ($notifications as &$notification) {
                $notification['is_read'] = true;
            }
            
            Cache::put($cacheKey, $notifications, now()->addDays(30));
            
            return response()->json(['success' => true]);
        }



        public function updateSemuaRatingPenitip()
        {
            // Ambil semua penitip dari tabel penitip
            $penitips = Penitip::all();

            foreach ($penitips as $penitip) {
                // Hitung rata-rata rating dari transaksi yang berkaitan dengan penitip ini
                $avgRating = Transaksi::whereHas('penitipan', function ($query) use ($penitip) {
                        $query->where('ID_PENITIP', $penitip->ID_PENITIP);
                    })
                    ->whereNotNull('RATING_PENITIP')
                    ->avg('RATING_PENITIP');

                // Update kolom RATING_PENITIP di tabel penitip
                $penitip->RATING_PENITIP = $avgRating ? round($avgRating, 2) : 0;
                $penitip->save();
            }

            return back()->with('success', 'Rata-rata rating semua penitip berhasil diperbarui.');
        }


    //     public function perpanjang($id)
    // {
    //     // Cari entri penitipan berdasarkan ID_BARANG
    //     $penitipan = \App\Models\Penitipan::where('ID_BARANG', $id)->firstOrFail();

    //     if (!$penitipan->TANGGAL_BERAKHIR) {
    //         $penitipan->TANGGAL_BERAKHIR = now()->addDays(30);
    //     } else {
    //         $penitipan->TANGGAL_BERAKHIR = \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR)->addDays(30);
    //     }

    //     $penitipan->save();

    //     return redirect()->back()->with('success', 'Masa penitipan berhasil diperpanjang 30 hari.');
    // }
}

    

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembeli;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Merchandise;
use App\Models\PointReward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PembeliController extends Controller
{
    public function dashboard()
{
    $pembeli = Auth::guard('pembeli')->user();

    $messages = [];

    $transaksiSedangDikirim = \App\Models\Transaksi::whereHas('barang', function ($query) use ($pembeli) {
        $query->where('ID_PEMBELI', $pembeli->ID_PEMBELI);
    })->where('STATUS_TRANSAKSI', 'Sedang Dikirim')->exists();

    $transaksiSelesai = \App\Models\Transaksi::whereHas('barang', function ($query) use ($pembeli) {
        $query->where('ID_PEMBELI', $pembeli->ID_PEMBELI);
    })->where('STATUS_TRANSAKSI', 'Selesai')->exists();

    if ($transaksiSedangDikirim) {
        $messages[] = ['type' => 'success', 'text' => 'Barang Anda sedang dalam perjalanan. Silakan cek halaman transaksi Anda.'];
    }

    if ($transaksiSelesai) {
        $messages[] = ['type' => 'info', 'text' => 'Barang Anda sudah sampai dan transaksi selesai. Terima kasih telah berbelanja!'];
    }

    if (!empty($messages)) {
        session()->flash('notifications', $messages);
    }

    return view('pembeli.dashboard', compact('pembeli'));
}


    public function profile()
    {
        $pembeli = Auth::guard('pembeli')->user();
        $merchandises = Merchandise::all();

        return view('pembeli.profile', compact('pembeli', 'merchandises'));
    }

    public function edit()
    {
        $pembeli = Auth::guard('pembeli')->user();
        return view('pembeli.edit', compact('pembeli'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pembeli,EMAIL_PEMBELI,' . Auth::guard('pembeli')->user()->ID_PEMBELI . ',ID_PEMBELI',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $pembeli = Auth::guard('pembeli')->user();
        $pembeli->NAMA_PEMBELI = $request->nama;
        $pembeli->EMAIL_PEMBELI = $request->email;

        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada dan filenya ditemukan
            if ($pembeli->FOTO_PROFIL && file_exists(public_path($pembeli->FOTO_PROFIL))) {
                unlink(public_path($pembeli->FOTO_PROFIL));
            }

            // Simpan foto baru
            $file = $request->file('foto_profil');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = 'images/profiles/' . $filename;
            
            // Buat folder jika belum ada
            if (!file_exists(public_path('images/profiles'))) {
                mkdir(public_path('images/profiles'), 0755, true);
            }

            $file->move(public_path('images/profiles'), $filename);

            // Update path di database
            $pembeli->FOTO_PROFIL = $path;
        }

        $pembeli->save();

        return redirect()->route('pembeli.profile')->with('success', 'Profil berhasil diperbarui!');
    }

    public function changePasswordForm()
    {
        $pembeli = Auth::guard('pembeli')->user();
        return view('pembeli.changePassword', compact('pembeli'));
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $pembeli = Auth::guard('pembeli')->user();

        if (!Hash::check($request->current_password, $pembeli->PASSWORD_PEMBELI)) {
            return redirect()->back()->with('error', 'Password saat ini salah.');
        }

        $pembeli->PASSWORD_PEMBELI = Hash::make($request->new_password);
        $pembeli->save();

        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }

    public function riwayatPembelian()
    {
        $pembeli = Auth::guard('pembeli')->user();
        $riwayat = $pembeli->transaksis()->with('barang')->get();

        return view('pembeli.transaction.detail', compact('riwayat', 'pembeli'));
    }

    public function point()
    {
        $pembeli = Auth::guard('pembeli')->user();
        return view('pembeli.point', compact('pembeli'));
    }

    public function beliBarangForm($id)
    {
        $pembeli = Auth::guard('pembeli')->user();
        $barang = Barang::findOrFail($id);
        return view('pembeli.beli', compact('barang', 'pembeli'));
    }

    public function beliBarang(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);
        $pembeli = Auth::guard('pembeli')->user();

        if ($barang->stok < 1) {
            return redirect()->back()->with('error', 'Stok barang habis.');
        }

        $barang->stok -= 1;
        $barang->save();

        $pembeli->POINT_PEMBELI += 10;
        $pembeli->save();

        // Tambahkan logika transaksi di sini jika dibutuhkan
        return redirect()->route('pembeli.transactions')->with('success', 'Barang berhasil dibeli!');
    }

    public function transactions(Request $request)
    {
        $pembeli = Auth::guard('pembeli')->user();

        $query = Transaksi::where('ID_PEMBELI', $pembeli->ID_PEMBELI);

        if ($request->filled('start_date')) {
            $query->whereDate('TANGGAL_TRANSAKSI', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('TANGGAL_TRANSAKSI', '<=', $request->end_date);
        }

        if ($request->sort == 'date_asc') {
            $query->orderBy('TANGGAL_TRANSAKSI', 'asc');
        } elseif ($request->sort == 'amount_desc') {
            $query->orderBy('TOTAL_TRANSAKSI', 'desc');
        } elseif ($request->sort == 'amount_asc') {
            $query->orderBy('TOTAL_TRANSAKSI', 'asc');
        } else {
            $query->orderBy('TANGGAL_TRANSAKSI', 'desc');
        }

        $transactions = $query->with('barang')->paginate(10);

        return view('pembeli.transaction', compact('transactions', 'pembeli'));
    }

    public function transactionDetail($id)
    {
        $pembeli = Auth::guard('pembeli')->user();
        $transaction = Transaksi::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                             ->where('ID_TRANSAKSI', $id)
                             ->with('barang')
                             ->firstOrFail();

        return view('pembeli.transactionDetail', compact('transaction', 'pembeli'));
    }

    public function logout()
    {
        Auth::guard('pembeli')->logout();
        return redirect()->route('home');
    }

    public function simpanRating(Request $request, $id)
    {
        $request->validate([
            'rating' => [
                'required',
                'numeric',
                'min:1',
                'max:5',
                function ($attribute, $value, $fail) {
                    // Validasi hanya menerima angka bulat atau setengah (.5)
                    $decimal = $value - floor($value);
                    if ($decimal != 0 && $decimal != 0.5) {
                        $fail('Rating hanya boleh berupa angka bulat atau setengah (contoh: 3, 3.5, 4, 4.5)');
                    }
                }
            ],
        ], [
            'rating.required' => 'Rating wajib diisi',
            'rating.numeric' => 'Rating harus berupa angka',
            'rating.min' => 'Rating minimal 1',
            'rating.max' => 'Rating maksimal 5',
        ]);

        $pembeli = Auth::guard('pembeli')->user();

        $transaksi = Transaksi::where('ID_TRANSAKSI', $id)
            ->where('ID_PEMBELI', $pembeli->ID_PEMBELI)
            ->firstOrFail();

        // ✅ Tambahkan validasi status pengiriman
        if (!in_array($transaksi->STATUS_PENGIRIMAN, ['Terkirim', 'Diambil'])) {
            return redirect()->back()->withErrors([
                'error' => 'Anda hanya dapat memberi rating setelah barang diterima.'
            ]);
        }

        $transaksi->RATING_BARANG = $request->rating;
        $transaksi->save();

        return redirect()->back()->with('success', 'Rating berhasil disimpan!');
    }


    // Helper untuk menampilkan rating dengan bintang
    public function formatRating($rating)
    {
        if (!$rating) return 'Belum ada rating';
        
        $fullStars = floor($rating);
        $hasHalfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
        
        $stars = str_repeat('★', $fullStars);
        if ($hasHalfStar) $stars .= '☆';
        $stars .= str_repeat('☆', $emptyStars);
        
        return $stars . ' (' . number_format($rating, 1) . '/5.0)';
    }

    public function simpanRatingPenitip($id, Request $request)
{
    $request->validate([
        'rating_penitip' => [
            'required',
            'numeric',
            'min:1',
            'max:5',
            function ($attribute, $value, $fail) {
                $decimal = $value - floor($value);
                if ($decimal != 0 && $decimal != 0.5) {
                    $fail('Rating hanya boleh berupa angka bulat atau setengah (contoh: 3, 3.5, 4, 4.5)');
                }
            }
        ]
    ], [
        'rating_penitip.required' => 'Rating wajib diisi',
        'rating_penitip.numeric' => 'Rating harus berupa angka',
        'rating_penitip.min' => 'Rating minimal 1',
        'rating_penitip.max' => 'Rating maksimal 5',
    ]);

    $pembeli = Auth::guard('pembeli')->user();

    $transaksi = Transaksi::where('ID_TRANSAKSI', $id)
        ->where('ID_PEMBELI', $pembeli->ID_PEMBELI)
        ->firstOrFail();

    if (!in_array($transaksi->STATUS_PENGIRIMAN, ['Terkirim', 'Diambil'])) {
        return back()->withErrors(['error' => 'Anda hanya dapat memberi rating setelah barang diterima.']);
    }

    if ($transaksi->RATING_PENITIP) {
        return back()->withErrors(['error' => 'Rating penitip sudah diberikan.']);
    }

    $transaksi->RATING_PENITIP = $request->rating_penitip;
    $transaksi->save();

    return back()->with('success', 'Rating penitip berhasil disimpan.');
}

    // public function tukarPoin(Request $request)
    // {
    //     $request->validate([
    //         'hadiah' => 'required|string',
    //     ]);

    //     $pembeli = auth()->user();

    //     // 'hadiah' berformat "nama|poin"
    //     list($namaHadiah, $poinHadiah) = explode('|', $request->hadiah);

    //     $poinHadiah = (int) $poinHadiah;

    //     if ($pembeli->POINT_PEMBELI < $poinHadiah) {
    //         return back()->withErrors('Point Anda tidak cukup untuk menukarkan hadiah ini.');
    //     }

    //     $pembeli->removePoints($poinHadiah);

    //     PointReward::create([
    //         'ID_PEMBELI' => $pembeli->ID_PEMBELI,
    //         'NAMA_HADIAH' => $namaHadiah, // buat kolom baru di tabel PointReward jika belum ada
    //         'JUMLAH_POINT' => $poinHadiah,
    //         'TANGGAL_AMBIL' => now(),
    //         'STATUS_KLAIM' => 'klaim',
    //     ]);

    //     return redirect()->back()->with('success', 'Klaim hadiah berhasil. Point Anda telah dikurangi.');
    // }

}
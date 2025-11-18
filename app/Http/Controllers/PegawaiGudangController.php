<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\AlamatPembeli;
use App\Models\Penitipan;
use App\Models\Transaksi; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PDF;

class PegawaiGudangController extends Controller
{
    // Dashboard pegawai gudang
    public function dashboard()
    {
        $totalBarang = Barang::count();
        $barangMinimum = Barang::where('stok', '<=', 5)->count();
        $barangPenitipan = Barang::where('STATUS', 'Penitipan')->count();
        $penitipanBerakhir = Penitipan::whereDate('TANGGAL_BERAKHIR', '<=', Carbon::now()->addDays(7))->count();


        $pengambilanList = Transaksi::with(['penitipan.barang'])
        ->where('STATUS_TRANSAKSI', 'Selesai')
        ->orderByDesc('TANGGAL_TRANSAKSI', 'desc') // atau TANGGAL_TRANSAKSI jika kamu punya
        ->take(100)
        ->get();

        // $pengambilanList2 = Penitipan::with('barang')
        //     ->where('STATUS_PENITIPAN', 'SELESAI')
        //     ->orderByDesc('TANGGAL_BERAKHIR')
        //     ->take(5)
        //     ->get();

        $transaksiPending = Penitipan::with(['barang.pembeli', 'penitip'])
            ->where('STATUS_PENITIPAN', 'Disiapkan')
            ->orderBy('TANGGAL_MULAI', 'desc') // ganti dengan nama kolom yang benar
            ->limit(5)
            ->get();

        // Tambahkan ini untuk jumlah transaksi hangus
        $jumlahTransaksiHangus = Penitipan::where('STATUS_PENITIPAN', 'Hangus')->count();

        return view('gudang.dashboard', compact(
            'totalBarang',
            'barangMinimum',
            'barangPenitipan', 
            'penitipanBerakhir',
            'pengambilanList',
            'transaksiPending',
            'jumlahTransaksiHangus' // jangan lupa dikirim ke view
        ));
    }

    public function detail($id)
    {
        $transaksi = Penitipan::with(['barang.foto', 'penitip', 'pembeli'])->findOrFail($id);
        return view('gudang.transaksi.detail', compact('transaksi'));
    }


    // Menampilkan daftar stok barang
    public function stok()
    {
        // Ambil data barang dengan eager loading yang lebih spesifik
        $barang = Barang::with([
            'kategori',
            'pegawai', // tambahan relasi ke pegawai
            'penitipan' => function($query) {
                $query->with(['penitip', 'pegawai']);
            }
        ])->get();
        
        return view('gudang.stok', compact('barang'));
    }

    public function index()
    {
        $search = request('search');
        
        $penitipan = Penitipan::with(['barang.kategori', 'penitip', 'pegawai'])
                            ->when($search, function($query, $search) {
                                return $query->where(function($q) use ($search) {
                                    $q->where('ID_PENITIPAN', 'like', "%{$search}%")
                                    ->orWhereHas('penitip', function($q) use ($search) {
                                        $q->where('NAMA_PENITIP', 'like', "%{$search}%");
                                    })
                                    ->orWhereHas('barang', function($q) use ($search) {
                                        $q->where('NAMA_BARANG', 'like', "%{$search}%");
                                    });
                                });
                            })
                            ->orderBy('TANGGAL_MULAI', 'desc')
                            ->get();
        
        // Ambil data untuk modal update - PERBAIKAN: Pastikan data tersedia
        $penitips = Penitip::all();
        $barangs = Barang::with('kategori')
                        ->where(function($query) {
                            $query->where('STATUS', 'Tersedia')
                                ->orWhere('STATUS', 'Penitipan');
                        })
                        ->get();
        
        return view('gudang.penitipan.index', compact('penitipan', 'penitips', 'barangs'));
    }

    public function daftarPenitipan(Request $request)
    {
        // Load relasi dengan barang menggunakan relasi hasOne
        $query = Penitipan::with(['barang.kategori', 'barangHunter', 'penitip', 'pegawai']);

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ID_PENITIPAN', 'like', '%' . $search . '%')
                ->orWhereHas('penitip', function($subQ) use ($search) {
                    $subQ->where('NAMA_PENITIP', 'like', '%' . $search . '%');
                })
                ->orWhereHas('barang', function($subQ) use ($search) {
                    $subQ->where('NAMA_BARANG', 'like', '%' . $search . '%');
                });
            });
        }

        $penitipan = $query->orderBy('TANGGAL_MULAI', 'desc')->get();

        // Filter penitipan yang memiliki data penitip dan barang yang valid
        $penitipan = $penitipan->filter(function ($item) {
            return $item->penitip !== null && 
                ($item->barang !== null || $item->barangHunter !== null);
        });

        // Update status otomatis untuk penitipan yang sudah expired
        foreach ($penitipan as $item) {
            if ($item->STATUS_PENITIPAN == 'Aktif' && $item->TANGGAL_BERAKHIR) {
                $tanggalBerakhir = \Carbon\Carbon::parse($item->TANGGAL_BERAKHIR)->endOfDay();
                $now = \Carbon\Carbon::now();
                
                // Jika sudah melewati tanggal berakhir
                if ($now->greaterThan($tanggalBerakhir)) {
                    $item->update(['STATUS_PENITIPAN' => 'Expired']);
                    $item->refresh(); // Refresh model untuk mendapatkan data terbaru
                }
            }
        }

        // ✅ TAMBAHKAN DATA YANG DIPERLUKAN UNTUK MODAL
        $penitips = Penitip::all();
        $barangs = Barang::with('kategori')
                        ->where(function($query) {
                            $query->where('STATUS', 'Tersedia')
                                ->orWhere('STATUS', 'Penitipan');
                        })
                        ->get();

        // ✅ PERBAIKI COMPACT UNTUK MENYERTAKAN SEMUA DATA
        return view('gudang.penitipan.index', compact('penitipan', 'penitips', 'barangs'));
    }

    public function createPenitipan()
    {
        $penitip = Penitip::all();
        // Ambil barang yang tersedia untuk penitipan
        $barang = Barang::where('STATUS', 'Tersedia')
                        ->whereNull('ID_PENITIPAN')
                        ->with('kategori')
                        ->get();
        
        return view('gudang.penitipan.create', compact('penitip', 'barang'));
    }

   public function storePenitipan(Request $request)
{
    // Debug: cek data yang diterima
    \Log::info('Data penitipan yang diterima:', $request->all());
    
    // Validasi input
    $validator = Validator::make($request->all(), [
        'id_penitip' => 'required|exists:penitip,ID_PENITIP',
        'id_barang' => 'required|exists:barang,ID_BARANG',
        'tanggal_mulai' => 'required|date',
    ]);

    // Jika validasi gagal
    if ($validator->fails()) {
        \Log::error('Validasi gagal:', $validator->errors()->toArray());
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $validated = $validator->validated();

    try {
        \DB::beginTransaction();

        // Cek apakah barang sudah memiliki penitipan aktif
        $barang = Barang::findOrFail($validated['id_barang']);
        
        // Debug: cek status barang
        \Log::info('Status barang sebelum penitipan:', [
            'ID_BARANG' => $barang->ID_BARANG,
            'STATUS' => $barang->STATUS,
            'ID_PENITIPAN' => $barang->ID_PENITIPAN
        ]);
        
        // Cek jika barang sudah dalam penitipan
        if ($barang->STATUS === 'Penitipan' || $barang->ID_PENITIPAN) {
            \Log::warning('Barang sudah dalam penitipan aktif', ['ID_BARANG' => $barang->ID_BARANG]);
            return redirect()->back()
                ->withErrors(['id_barang' => 'Barang ini sudah dalam penitipan aktif.'])
                ->withInput();
        }

        // Hitung tanggal berakhir (H+30 dari tanggal mulai)
        $tanggalMulai = Carbon::parse($validated['tanggal_mulai'])->startOfDay();
        $tanggalBerakhir = $tanggalMulai->copy()->addDays(30); // H+30

        // Debug: cek tanggal yang dihitung
        \Log::info('Tanggal kalkulasi:', [
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_berakhir' => $tanggalBerakhir->format('Y-m-d'),
            'durasi' => '30 hari'
        ]);

        // Cek ID_PEGAWAI yang sedang login
        $idPegawai = auth()->guard('pegawai')->id();
        $namaPegawai = auth()->guard('pegawai')->user()->NAMA_PEGAWAI;
        if (!$idPegawai) {
            throw new \Exception('Pegawai tidak terautentikasi dengan benar');
        }

        // ✅ PERBAIKAN: Data penitipan yang disesuaikan dengan timestamps = false
        $penitipanData = [
            'ID_PENITIP' => $validated['id_penitip'],
            'TANGGAL_MULAI' => $tanggalMulai->format('Y-m-d'),
            'TANGGAL_BERAKHIR' => $tanggalBerakhir->format('Y-m-d'),
            'STATUS_PENITIPAN' => 'Aktif',
            'ID_PEGAWAI' => $idPegawai,
            'ID_BARANG' => $validated['id_barang'], // ✅ Tambahkan baris ini
            'CREATED_BY' => $idPegawai,
            'CREATED_BY_NAME' => $namaPegawai,
            'CREATED_AT_FORMATTED' => now()->format('Y-m-d H:i:s'),
            'TANGGAL_UPDATE' => now()->format('Y-m-d'),
        ];
        
        // Debug: cek data penitipan yang akan disimpan
        \Log::info('Data penitipan yang akan disimpan:', $penitipanData);
        
        $penitipan = Penitipan::create($penitipanData);

        // Pastikan penitipan berhasil dibuat
        if (!$penitipan) {
            throw new \Exception('Gagal membuat record penitipan');
        }

        \Log::info('Penitipan berhasil dibuat:', [
            'ID_PENITIPAN' => $penitipan->ID_PENITIPAN,
            'data' => $penitipan->toArray()
        ]);

        // ✅ Update barang (pastikan model Barang juga tidak menggunakan timestamps jika ada error serupa)
        $updateResult = $barang->update([
            'STATUS' => 'Tersedia',
            'ID_PENITIPAN' => $penitipan->ID_PENITIPAN,
        ]);

        if (!$updateResult) {
            throw new \Exception('Gagal mengupdate status barang');
        }

        \Log::info('Barang berhasil diupdate:', [
            'ID_BARANG' => $barang->ID_BARANG,
            'STATUS_BARU' => 'Tersedia',
            'ID_PENITIPAN' => $penitipan->ID_PENITIPAN
        ]);

        \DB::commit();

        $message = 'Penitipan berhasil dibuat oleh ' . $namaPegawai . '. Periode: ' . 
                  $tanggalMulai->format('d/m/Y') . ' - ' . 
                  $tanggalBerakhir->format('d/m/Y') . ' (30 hari)';

        \Log::info('Transaksi penitipan berhasil diselesaikan');

        return redirect()->route('gudang.penitipan.index')->with('success', $message);

    } catch (\Exception $e) {
        \DB::rollback();
        \Log::error('Error saat membuat penitipan:', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()
            ->withErrors(['error' => 'Terjadi kesalahan saat membuat penitipan: ' . $e->getMessage()])
            ->withInput();
    }
}
    public function editPenitipan($id)
    {
        $penitipan = Penitipan::with(['barang.kategori', 'barangHunter', 'penitip', 'pegawai'])
                             ->findOrFail($id);
        
        // Barang yang tersedia + barang yang sudah terpilih di penitipan ini
        $barangs = Barang::with('kategori')
                        ->where(function($query) use ($penitipan) {
                            $query->where('STATUS', 'Tersedia')
                                  ->orWhere('ID_PENITIPAN', $penitipan->ID_PENITIPAN);
                        })
                        ->get();
        
        $penitips = Penitip::all();
        
        return view('gudang.penitipan.edit', compact('penitipan', 'barangs', 'penitips'));
    }

    public function detailPenitipan($id)
    {
        $penitipan = Penitipan::with(['barang.kategori', 'barangHunter', 'penitip', 'pegawai'])
                             ->findOrFail($id);

        return view('gudang.penitipan.detail', compact('penitipan'));
    }

    public function perpanjangPenitipan(Request $request, $id)
    {
        $request->validate([
            'tanggal_berakhir_baru' => 'required|date|after:today',
        ]);

        $penitipan = Penitipan::findOrFail($id);
        
        if ($penitipan->STATUS_PENITIPAN != 'Aktif') {
            return redirect()->back()->with('error', 'Hanya penitipan aktif yang dapat diperpanjang');
        }

        $tanggalBerakhirBaru = \Carbon\Carbon::parse($request->tanggal_berakhir_baru);
        $tanggalBerakhirLama = \Carbon\Carbon::parse($penitipan->TANGGAL_BERAKHIR);

        // Hitung selisih hari perpanjangan
        $perpanjangan = $tanggalBerakhirBaru->diffInDays($tanggalBerakhirLama);

        $penitipan->update([
            'TANGGAL_BERAKHIR' => $tanggalBerakhirBaru->format('Y-m-d'),
            'TANGGAL_UPDATE' => now()
        ]);

        return redirect()->route('gudang.penitipan.index')
                        ->with('success', 'Penitipan berhasil diperpanjang sampai ' . $tanggalBerakhirBaru->format('d/m/Y'));
    }

    public function akhiriPenitipan(Request $request, $id)
    {
        $request->validate([
            'status_akhir' => 'required|in:Selesai,Donasi',
        ]);

        $penitipan = Penitipan::findOrFail($id);
        
        if ($penitipan->STATUS_PENITIPAN != 'Aktif') {
            return redirect()->back()->with('error', 'Hanya penitipan aktif yang dapat diakhiri');
        }

        // Update status barang menjadi tersedia dan hapus ID_PENITIPAN
        if ($penitipan->barang) {
            $penitipan->barang->update([
                'STATUS' => 'Tersedia',
                'ID_PENITIPAN' => null
            ]);
        }

        $penitipan->update([
            'STATUS_PENITIPAN' => $request->status_akhir,
            'TANGGAL_UPDATE' => now()
        ]);

        $statusText = [
            'Selesai' => 'diselesaikan',
            'Donasi' => 'diserahkan untuk donasi'
        ];

        return redirect()->route('gudang.penitipan.index')
                        ->with('success', 'Penitipan berhasil ' . $statusText[$request->status_akhir]);
    }

    public function updatePenitipan(Request $request, $id)
{
    // Validasi yang lebih spesifik
    $validator = Validator::make($request->all(), [
        'ID_PENITIP' => 'required|exists:penitip,ID_PENITIP',
        'id_barang' => 'required|exists:barang,ID_BARANG',
        'TANGGAL_MULAI' => 'required|date',
        'TANGGAL_BERAKHIR' => 'required|date|after:TANGGAL_MULAI',
        'STATUS_PENITIPAN' => 'required|in:Aktif,Selesai,Donasi,Expired',
        'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'foto_produk2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    if ($validator->fails()) {
        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('modal_error', $id);
    }

    try {
        \DB::beginTransaction();

        $penitipan = Penitipan::findOrFail($id);
        $barangLama = $penitipan->barang;
        $barangBaru = Barang::findOrFail($request->id_barang);
        
        // Ambil data pegawai yang sedang login
        $idPegawaiUpdate = auth()->guard('pegawai')->id();
        $namaPegawaiUpdate = auth()->guard('pegawai')->user()->NAMA_PEGAWAI;
        
        // Log untuk debugging
        \Log::info('Update Penitipan:', [
            'ID_PENITIPAN' => $id,
            'barang_lama_id' => $barangLama ? $barangLama->ID_BARANG : null,
            'barang_baru_id' => $request->id_barang,
            'status_baru' => $request->STATUS_PENITIPAN,
            'updated_by' => $namaPegawaiUpdate
        ]);

        // Cek jika barang baru sudah digunakan penitipan lain (kecuali penitipan ini)
        if ($barangBaru->STATUS === 'Tersedia' && 
            $barangBaru->ID_PENITIPAN && 
            $barangBaru->ID_PENITIPAN != $penitipan->ID_PENITIPAN) {
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Barang ini sudah digunakan dalam penitipan lain.',
                    'errors' => ['id_barang' => ['Barang ini sudah digunakan dalam penitipan lain.']]
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors(['id_barang' => 'Barang ini sudah digunakan dalam penitipan lain.'])
                ->withInput()
                ->with('modal_error', $id);
        }
        
        // Handle foto upload
        $fotoUpdates = [];
        
        if ($request->hasFile('foto_produk')) {
            // Hapus foto lama jika ada
            if ($barangBaru->foto_produk && file_exists(public_path('images/fotoProduk/' . $barangBaru->foto_produk))) {
                unlink(public_path('images/fotoProduk/' . $barangBaru->foto_produk));
            }
            
            $file = $request->file('foto_produk');
            $filename = 'produk_' . $barangBaru->ID_BARANG . '_1_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/fotoProduk'), $filename);
            $fotoUpdates['foto_produk'] = $filename;
        }
        
        if ($request->hasFile('foto_produk2')) {
            // Hapus foto lama jika ada
            if ($barangBaru->foto_produk2 && file_exists(public_path('images/fotoProduk2/' . $barangBaru->foto_produk2))) {
                unlink(public_path('images/fotoProduk2/' . $barangBaru->foto_produk2));
            }
            
            $file = $request->file('foto_produk2');
            $filename = 'produk_' . $barangBaru->ID_BARANG . '_2_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/fotoProduk2'), $filename);
            $fotoUpdates['foto_produk2'] = $filename;
        }
        
        // Update foto barang jika ada
        if (!empty($fotoUpdates)) {
            $barangBaru->update($fotoUpdates);
            \Log::info('Foto barang berhasil diupdate:', $fotoUpdates);
        }
        
        // Logika update barang yang lebih robust
        if ($barangLama && $barangLama->ID_BARANG != $request->id_barang) {
            // Barang berubah - kembalikan barang lama ke status tersedia
            $barangLama->update([
                'STATUS' => 'Tersedia',
                'ID_PENITIPAN' => null
            ]);

            // Set barang baru
            $statusBarangBaru = 'Tersedia';

            // ✅ Perbaiki: tetap set ID_PENITIPAN dengan $id
            $barangBaru->update([
                'STATUS' => $statusBarangBaru,
                'ID_PENITIPAN' => $id
            ]); 

            \Log::info('Barang penitipan berubah:', [
                'barang_lama' => $barangLama->ID_BARANG . ' -> Tersedia',
                'barang_baru' => $barangBaru->ID_BARANG . ' -> ' . $statusBarangBaru
            ]);
        } else if ($barangLama) {
            // Barang sama, hanya status yang mungkin berubah
            $statusBarangBaru = 'Tersedia';

            // ✅ Tetap pastikan ID_PENITIPAN tersimpan
            $barangBaru->update([
                'STATUS' => $statusBarangBaru,
                'ID_PENITIPAN' => $id
            ]); 

            \Log::info('Status barang diupdate:', [
                'barang_id' => $barangLama->ID_BARANG,
                'status_baru' => $statusBarangBaru
            ]);
        }

        $updateData = [
            'ID_PENITIP' => $request->ID_PENITIP,
            'ID_BARANG' => $barangBaru->ID_BARANG,
            'TANGGAL_MULAI' => $request->TANGGAL_MULAI,
            'TANGGAL_BERAKHIR' => $request->TANGGAL_BERAKHIR,
            'STATUS_PENITIPAN' => $request->STATUS_PENITIPAN,
            'UPDATED_BY' => $idPegawaiUpdate,
            'UPDATED_BY_NAME' => $namaPegawaiUpdate,
            'UPDATED_AT_FORMATTED' => now(), // ✅ Simpan datetime untuk view
            'TANGGAL_UPDATE' => now()->format('Y-m-d'), // ✅ Tetap simpan date untuk keperluan lain
        ];

        // ✅ FIX: Update tanpa menambahkan updated_at
        $updated = \DB::table('penitipan')
            ->where('ID_PENITIPAN', $id)
            ->update($updateData);

        if (!$updated) {
            throw new \Exception('Gagal menyimpan perubahan penitipan');
        }

        // Refresh data penitipan
        $penitipan = Penitipan::find($id);
        
        \Log::info('Penitipan berhasil diupdate:', [
            'ID_PENITIPAN' => $id,
            'updated_by' => $namaPegawaiUpdate,
            'data_saved' => $updateData,
            'update_result' => $updated
        ]);

        \DB::commit();

        // Jika request AJAX, return JSON success
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data penitipan berhasil diperbarui oleh ' . $namaPegawaiUpdate,
                'data' => $penitipan
            ], 200);
        }

        return redirect()->route('gudang.penitipan.index')
                        ->with('success', 'Data penitipan berhasil diperbarui oleh ' . $namaPegawaiUpdate);

    } catch (\Exception $e) {
        \DB::rollback();
        \Log::error('Error saat update penitipan:', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]);
        
        $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        
        // Jika request AJAX, return JSON error
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
        
        return redirect()->back()
            ->withErrors(['error' => $errorMessage])
            ->withInput()
            ->with('modal_error', $id);
    }
}

    public function penitipanLaporan(Request $request)
    {
        // Query dasar dengan relasi yang lebih lengkap
        $query = Penitipan::with([
            'barang.kategori', 
            'penitip', 
            'pegawai'
        ]);

        // Filter berdasarkan search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ID_PENITIPAN', 'like', "%{$search}%")
                ->orWhereHas('penitip', function($subQ) use ($search) {
                    $subQ->where('NAMA_PENITIP', 'like', "%{$search}%");
                })
                ->orWhereHas('barang', function($subQ) use ($search) {
                    $subQ->where('NAMA_BARANG', 'like', "%{$search}%");
                });
            });
        }

        // Filter berdasarkan periode
        if ($request->filled('periode_dari')) {
            $query->whereDate('TANGGAL_MULAI', '>=', $request->periode_dari);
        }

        if ($request->filled('periode_sampai')) {
            $query->whereDate('TANGGAL_MULAI', '<=', $request->periode_sampai);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('STATUS_PENITIPAN', $request->status);
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->whereHas('barang', function($q) use ($request) {
                $q->where('ID_KATEGORI', $request->kategori);
            });
        }

        // Ambil data penitipan
        $penitipan = $query->orderBy('TANGGAL_MULAI', 'desc')->get();

        // Update status otomatis untuk penitipan yang sudah expired
        foreach ($penitipan as $item) {
            if ($item->STATUS_PENITIPAN == 'Aktif' && $item->TANGGAL_BERAKHIR) {
                try {
                    $tanggalBerakhir = \Carbon\Carbon::parse($item->TANGGAL_BERAKHIR)->endOfDay();
                    $now = \Carbon\Carbon::now();
                    
                    if ($now->greaterThan($tanggalBerakhir)) {
                        $item->update(['STATUS_PENITIPAN' => 'Hangus']);
                        $item->refresh();
                    }
                } catch (\Exception $e) {
                    // Handle date parsing errors
                    \Log::warning("Error parsing date for penitipan ID: {$item->ID_PENITIPAN}");
                }
            }
        }

        // Hitung statistik dengan null checks
        $statistik = [
            'total_penitipan' => $penitipan->count(),
            'penitipan_aktif' => $penitipan->where('STATUS_PENITIPAN', 'Aktif')->count(),
            'penitipan_selesai' => $penitipan->where('STATUS_PENITIPAN', 'Selesai')->count(),
            'penitipan_expired' => $penitipan->where('STATUS_PENITIPAN', 'Hangus')->count(),
            'akan_berakhir' => $penitipan->filter(function($item) {
                if ($item->STATUS_PENITIPAN == 'Aktif' && $item->TANGGAL_BERAKHIR) {
                    try {
                        $tanggalBerakhir = \Carbon\Carbon::parse($item->TANGGAL_BERAKHIR);
                        $now = \Carbon\Carbon::now();
                        return $tanggalBerakhir->diffInDays($now) <= 7 && $tanggalBerakhir->isFuture();
                    } catch (\Exception $e) {
                        return false;
                    }
                }
                return false;
            })->count(),
            'total_nilai' => $penitipan->sum(function($item) {
                return ($item->barang && $item->barang->HARGA) ? $item->barang->HARGA : 0;
            })
        ];

        // Ambil kategori untuk filter
        $kategori = KategoriBarang::all();

        // Handle export
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return $this->exportLaporanToExcel($penitipan, $statistik);
            } elseif ($request->export == 'pdf') {
                return $this->exportLaporanToPDF($penitipan, $statistik);
            }
        }

        return view('gudang.penitipan.laporan', compact('penitipan', 'statistik', 'kategori'));
    }

    public function tambahBarangForm()
    {
        $kategori = KategoriBarang::all();
        $penitip = Penitip::all();
        return view('gudang.tambahBarang.create', compact('kategori', 'penitip'));
    }

    // Simpan barang baru
    public function simpanBarang(Request $request)
    {
        // DEBUG 1: Cek data yang diterima dari form
        \Log::info('Data dari form:', $request->all());
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'kategori' => 'required|exists:kategori_barang,ID_KATEGORI',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|string|in:Tersedia,Penitipan,Donasi',
            'garansi' => 'required|in:Ya,Tidak',
            'stok' => 'required|integer|min:1',
            'penitip' => 'nullable|exists:penitip,ID_PENITIP',
            'foto1' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'foto2' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // DEBUG 2: Cek data setelah validasi
        \Log::info('Data setelah validasi:', $validated);

        // Validasi khusus untuk penitipan
        if ($validated['status'] === 'Penitipan' && empty($validated['penitip'])) {
            return redirect()->back()
                ->withErrors(['penitip' => 'Penitip harus dipilih untuk barang penitipan.'])
                ->withInput();
        }

        try {
            \DB::beginTransaction();

            // Upload foto produk
            $foto1 = $this->uploadFoto($request, 'foto1', 'fotoProduk');
            $foto2 = $this->uploadFoto($request, 'foto2', 'fotoProduk2');

            // DEBUG 3: Cek foto hasil upload
            \Log::info('Foto upload:', ['foto1' => $foto1, 'foto2' => $foto2]);

            // Untuk barang penitipan, buat record penitipan terlebih dahulu
            $penitipanId = null;
            if ($validated['status'] === 'Penitipan' && !empty($validated['penitip'])) {
                $penitipan = Penitipan::create([
                    'ID_PENITIP' => $validated['penitip'],
                    'STATUS_PENITIPAN' => 'Aktif',
                    'TANGGAL_MULAI' => now(),
                    'TANGGAL_BERAKHIR' => now()->addDays(30),
                    'ID_PEGAWAI' => auth()->guard('pegawai')->user()->ID_PEGAWAI ?? null,
                ]);

                $penitipanId = $penitipan->ID_PENITIPAN;
                \Log::info('Penitipan dibuat:', ['ID_PENITIPAN' => $penitipanId]);
            }

            // PERBAIKAN UTAMA: Handle kedua field kategori
            $kategoriId = (int) $validated['kategori'];
            \Log::info('ID Kategori yang akan disimpan:', ['kategori' => $kategoriId, 'type' => gettype($kategoriId)]);

            // DEBUG: Cek apakah kategori ada di database dan ambil nama kategorinya
            $kategoriData = \DB::table('kategori_barang')->where('ID_KATEGORI', $kategoriId)->first();
            \Log::info('Kategori exists check:', ['exists' => $kategoriData ? 'YES' : 'NO', 'data' => $kategoriData]);

            if (!$kategoriData) {
                throw new \Exception("Kategori dengan ID {$kategoriId} tidak ditemukan di database");
            }

            // GUNAKAN ELOQUENT MODEL BUKAN QUERY BUILDER RAW
            $barang = new Barang();
            $barang->NAMA_BARANG = $validated['nama'];
            $barang->DESKRIPSI = $validated['deskripsi'] ?? null;
            $barang->ID_KATEGORI = $kategoriId; // Foreign key
            $barang->KATEGORI = $kategoriData->JENIS_KATEGORI; // Nama kategori (field varchar)
            $barang->HARGA = $validated['harga'];
            $barang->STATUS = $validated['status'];
            $barang->GARANSI = $validated['garansi'];
            $barang->stok = $validated['stok'];
            $barang->foto_produk = $foto1;
            $barang->foto_produk2 = $foto2;
            $barang->ID_PENITIPAN = $penitipanId;
            $barang->ID_PEGAWAI = auth()->guard('pegawai')->user()->ID_PEGAWAI ?? null;

            // DEBUG: Log data sebelum save
            \Log::info('Data barang sebelum save:', $barang->toArray());

            // Simpan menggunakan Eloquent
            $barang->save();

            // DEBUG: Verifikasi data tersimpan
            $savedBarang = Barang::find($barang->ID_BARANG);
            \Log::info('Data tersimpan:', $savedBarang ? $savedBarang->toArray() : 'NULL');

            // Update penitipan dengan ID_BARANG jika ada
            if ($penitipanId && $barang->ID_BARANG) {
                \DB::table('penitipan')
                    ->where('ID_PENITIPAN', $penitipanId)
                    ->update(['ID_BARANG' => $barang->ID_BARANG]);
                \Log::info('Penitipan updated dengan ID_BARANG:', ['ID_BARANG' => $barang->ID_BARANG]);
            }

            \DB::commit();

            $message = 'Barang berhasil ditambahkan ke gudang.';
            return redirect()->route('gudang.stok')->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollback();
            
            \Log::error('Error lengkap:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan barang: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function exportPDF($id)
    {
        try {
            // Ambil data penitipan dengan relasi - pastikan menggunakan first() atau find()
            $penitipan = Penitipan::with(['penitip', 'barang.kategori', 'pegawai'])
                                ->where('ID_PENITIPAN', $id)
                                ->first();

            // Validasi data ditemukan
            if (!$penitipan) {
                return redirect()->back()->with('error', 'Data penitipan tidak ditemukan.');
            }

            // Hitung sisa hari jika status aktif
            $sisaHari = null;
            $statusSisaHari = null;
            
            if ($penitipan->STATUS_PENITIPAN == 'Aktif' && $penitipan->TANGGAL_BERAKHIR) {
                $tanggalBerakhir = Carbon::parse($penitipan->TANGGAL_BERAKHIR)->endOfDay();
                $sekarang = Carbon::now();
                $sisaHari = $sekarang->diffInDays($tanggalBerakhir, false);
                
                if ($sisaHari < 0) {
                    $statusSisaHari = 'terlambat';
                } elseif ($sisaHari == 0) {
                    $statusSisaHari = 'hari_ini';
                } elseif ($sisaHari <= 7) {
                    $statusSisaHari = 'segera';
                } else {
                    $statusSisaHari = 'normal';
                }
            }

            // Generate kode penitipan yang lebih readable
            $kodePenitipan = str_pad($penitipan->ID_PENITIPAN, 2, '0', STR_PAD_LEFT) . '.' . 
                            str_pad(date('m'), 2, '0', STR_PAD_LEFT) . '.' . 
                            str_pad(Carbon::parse($penitipan->TANGGAL_PENITIPAN)->format('j'), 3, '0', STR_PAD_LEFT);

            // Data untuk PDF
            $data = [
                'penitipan' => $penitipan,
                'sisaHari' => $sisaHari,
                'statusSisaHari' => $statusSisaHari,
                'tanggalGenerate' => Carbon::now(),
                'kodePenitipan' => $kodePenitipan
            ];

            // Generate PDF dengan ukuran thermal receipt
            $pdf = PDF::loadView('gudang.penitipan.pdf-nota', $data);
            
            // Set paper size untuk nota thermal (80mm width)
            $pdf->setPaper([0, 0, 226.77, 400], 'portrait'); // 80mm x ~140mm dalam points
            
            // Set options untuk optimasi
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
                'fontSubsetting' => false,
                'debugKeepTemp' => false,
                'debugCss' => false,
                'debugLayout' => false,
                'debugLayoutLines' => false,
                'debugLayoutBlocks' => false,
                'debugLayoutInline' => false,
                'debugLayoutPaddingBox' => false
            ]);

            // Nama file dengan format yang jelas
            $fileName = 'Nota_Penitipan_' . str_replace('.', '', $kodePenitipan) . '_' . 
                    Carbon::now()->format('Ymd_His') . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal menggenerate PDF: ' . $e->getMessage());
        }
    }

    public function previewPDF($id)
    {
        try {
            $penitipan = Penitipan::with(['penitip', 'barang.kategori', 'pegawai'])
                                ->where('ID_PENITIPAN', $id)
                                ->first();

            if (!$penitipan) {
                return redirect()->back()->with('error', 'Data penitipan tidak ditemukan.');
            }

            $sisaHari = null;
            $statusSisaHari = null;
            
            if ($penitipan->STATUS_PENITIPAN == 'Aktif' && $penitipan->TANGGAL_BERAKHIR) {
                $tanggalBerakhir = Carbon::parse($penitipan->TANGGAL_BERAKHIR)->endOfDay();
                $sekarang = Carbon::now();
                $sisaHari = $sekarang->diffInDays($tanggalBerakhir, false);
                
                if ($sisaHari < 0) {
                    $statusSisaHari = 'terlambat';
                } elseif ($sisaHari == 0) {
                    $statusSisaHari = 'hari_ini';
                } elseif ($sisaHari <= 7) {
                    $statusSisaHari = 'segera';
                } else {
                    $statusSisaHari = 'normal';
                }
            }

            $kodePenitipan = str_pad($penitipan->ID_PENITIPAN, 2, '0', STR_PAD_LEFT) . '.' . 
                            str_pad(date('m'), 2, '0', STR_PAD_LEFT) . '.' . 
                            str_pad(Carbon::parse($penitipan->TANGGAL_PENITIPAN)->format('j'), 3, '0', STR_PAD_LEFT);

            $data = [
                'penitipan' => $penitipan,
                'sisaHari' => $sisaHari,
                'statusSisaHari' => $statusSisaHari,
                'tanggalGenerate' => Carbon::now(),
                'kodePenitipan' => $kodePenitipan
            ];

            $pdf = PDF::loadView('gudang.penitipan.pdf-nota', $data);
            $pdf->setPaper([0, 0, 226.77, 400], 'portrait');
            
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

            return $pdf->stream('Preview_Nota_Penitipan_' . str_replace('.', '', $kodePenitipan) . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error previewing PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal preview PDF: ' . $e->getMessage());
        }
    }

    public function printNota($id)
    {
        try {
            $penitipan = Penitipan::with(['penitip', 'barang.kategori', 'pegawai'])
                                ->where('ID_PENITIPAN', $id)
                                ->first();

            if (!$penitipan) {
                return redirect()->back()->with('error', 'Data penitipan tidak ditemukan.');
            }

            $kodePenitipan = str_pad($penitipan->ID_PENITIPAN, 2, '0', STR_PAD_LEFT) . '.' . 
                            str_pad(date('m'), 2, '0', STR_PAD_LEFT) . '.' . 
                            str_pad(Carbon::parse($penitipan->TANGGAL_PENITIPAN)->format('j'), 3, '0', STR_PAD_LEFT);

            $data = [
                'penitipan' => $penitipan,
                'tanggalGenerate' => Carbon::now(),
                'kodePenitipan' => $kodePenitipan
            ];

            // Return view untuk print langsung
            return view('gudang.penitipan.print-nota', $data);

        } catch (\Exception $e) {
            \Log::error('Error loading print nota: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat nota untuk print: ' . $e->getMessage());
        }
    }

    private function uploadFoto($request, $fieldName, $folder)
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            $fileName = time() . '_' . $fieldName . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/' . $folder), $fileName);
            return $fileName;
        }
        return null;
    }

    public function markBarangTerjual(Request $request, $id)
    {
        $barang = Barang::with('penitipan')->findOrFail($id);
        
        if ($barang->STATUS !== 'Penitipan') {
            return redirect()->back()->with('error', 'Barang ini bukan barang penitipan.');
        }

        // Update status barang
        $barang->update([
            'STATUS' => 'Terjual',
            'stok' => 0
        ]);

        // Update status penitipan dengan tanggal keluar
        if ($barang->penitipan) {
            $barang->penitipan->update([
                'STATUS_PENITIPAN' => 'Selesai',
                'TANGGAL_KELUAR' => Carbon::now()
            ]);
        }

        return redirect()->route('gudang.stok')->with('success', 'Barang berhasil ditandai sebagai terjual.');
    }

    public function detailBarang($id)
    {
        $barang = Barang::with(['kategori', 'penitipan.penitip', 'penitipan.pegawai'])->findOrFail($id);

        // Produk terkait dari kategori yang sama
        $relatedProducts = Barang::where('ID_KATEGORI', $barang->ID_KATEGORI)
                                 ->where('ID_BARANG', '!=', $barang->ID_BARANG)
                                 ->limit(4)
                                 ->get();

        return view('gudang.stok.detail', compact('barang', 'relatedProducts'));
    }

    public function updateBarang(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
            'status' => 'required|string',
            'garansi' => 'required|string',
            'deskripsi' => 'nullable|string',
            'foto1' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        \Log::info("ID BARANG YANG DITERIMA: " . $id); // debug

        $barang = Barang::findOrFail($id);
        
        // Update data barang
        $barang->NAMA_BARANG = $request->nama;
        $barang->HARGA = $request->harga;
        $barang->stok = $request->stok;
        $barang->STATUS = $request->status;
        $barang->GARANSI = $request->garansi;
        $barang->DESKRIPSI = $request->deskripsi;

        // Handle upload foto 1
        if ($request->hasFile('foto1')) {
            // Hapus foto lama jika ada
            if ($barang->foto_produk && file_exists(public_path('images/fotoProduk/' . $barang->foto_produk))) {
                unlink(public_path('images/fotoProduk/' . $barang->foto_produk));
            }
            $namaFoto1 = 'foto1_' . time() . '.' . $request->file('foto1')->extension();
            $request->file('foto1')->move(public_path('images/fotoProduk'), $namaFoto1);
            $barang->foto_produk = $namaFoto1;
        }

        // Handle upload foto 2
        if ($request->hasFile('foto2')) {
            if ($barang->foto_produk2 && file_exists(public_path('images/fotoProduk2/' . $barang->foto_produk2))) {
                unlink(public_path('images/fotoProduk2/' . $barang->foto_produk2));
            }
            $namaFoto2 = 'foto2_' . time() . '.' . $request->file('foto2')->extension();
            $request->file('foto2')->move(public_path('images/fotoProduk2'), $namaFoto2);
            $barang->foto_produk2 = $namaFoto2;
        }

        $barang->save();

        return redirect()->route('gudang.stok')->with('success', 'Barang berhasil diperbarui.');
    }

    public function cariTransaksiTitipan(Request $request)
    {
        $query = Penitipan::with(['barang', 'penitip', 'pegawai']);

        // Filter berdasarkan nama penitip
        if ($request->filled('penitip_name')) {
            $query->whereHas('penitip', function ($q) use ($request) {
                $q->where('NAMA_PENITIP', 'like', '%' . $request->penitip_name . '%');
            });
        }

        // Filter berdasarkan nama barang
        if ($request->filled('barang_name')) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('NAMA_BARANG', 'like', '%' . $request->barang_name . '%');
            });
        }

        // Filter berdasarkan status penitipan
        if ($request->filled('status')) {
            $query->where('STATUS_PENITIPAN', $request->status);
        }

        // Filter berdasarkan rentang tanggal mulai
        if ($request->filled('tanggal_mulai_dari') && $request->filled('tanggal_mulai_sampai')) {
            $query->whereBetween('TANGGAL_MULAI', [
                Carbon::parse($request->tanggal_mulai_dari),
                Carbon::parse($request->tanggal_mulai_sampai)
            ]);
        }

        // Filter berdasarkan rentang tanggal berakhir
        if ($request->filled('tanggal_berakhir_dari') && $request->filled('tanggal_berakhir_sampai')) {
            $query->whereBetween('TANGGAL_BERAKHIR', [
                Carbon::parse($request->tanggal_berakhir_dari),
                Carbon::parse($request->tanggal_berakhir_sampai)
            ]);
        }

        $hasil = $query->orderBy('TANGGAL_MULAI', 'desc')->get();

        return view('gudang.penitipan.cari', compact('hasil'));
    }


    // Menampilkan profil pegawai gudang
    public function profile()
    {
        $pegawai = auth()->guard('pegawai')->user();
        $totalBarang = Barang::count();
        $barangMinimum = Barang::where('stok', '<=', 5)->count();

        return view('gudang.profile', compact('pegawai', 'totalBarang', 'barangMinimum'));
    }

    // Form edit profil
    public function edit()
    {
        $pegawai = auth()->guard('pegawai')->user();
        return view('gudang.edit', compact('pegawai'));
    }

    // Update profil pegawai gudang
    public function updateProfile(Request $request)
    {
        $pegawai = auth()->guard('pegawai')->user();

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:pegawai,EMAIL_PEGAWAI,' . $pegawai->ID_PEGAWAI . ',ID_PEGAWAI',
            'alamat' => 'required|string|max:255',
            'telepon' => 'required|string|max:13',
            'tanggal_lahir' => 'nullable|date',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $pegawai->NAMA_PEGAWAI = $request->nama;
        $pegawai->EMAIL_PEGAWAI = $request->email;
        $pegawai->ALAMAT_PEGAWAI = $request->alamat;
        $pegawai->NO_TELEPON_PEGAWAI = $request->telepon;
        
        if ($request->filled('tanggal_lahir')) {
            $pegawai->TANGGAL_LAHIR = $request->tanggal_lahir;
        }

        if ($request->filled('password')) {
            $pegawai->PASSWORD_PEGAWAI = Hash::make($request->password);
        }

        $pegawai->save();

        return redirect()->route('gudang.profile')->with('success', 'Profil berhasil diperbarui!');
    }
    
    // Method khusus untuk update password menggunakan tanggal lahir
    public function updatePasswordToDob(Request $request)
    {
        $pegawai = auth()->guard('pegawai')->user();
        
        // Verifikasi password saat ini
       if ($request->current_password !== $pegawai->PASSWORD_PEGAWAI) {
            return redirect()->back()
                        ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                        ->withInput();
        }
        
        // Cek jika tanggal lahir tersedia
        if (!$pegawai->TANGGAL_LAHIR) {
            return redirect()->back()
                        ->with('error', 'Tanggal lahir belum diatur. Silakan perbarui profil Anda terlebih dahulu.')
                        ->withInput();
        }
        
        // Buat password dari tanggal lahir (format: ddmmyyyy)
        $dobPassword = $pegawai->getDobAsPassword();
        
        // Update password
        $pegawai->PASSWORD_PEGAWAI = Hash::make($dobPassword);
        $pegawai->save();
        
        return redirect()->route('gudang.profile')
                    ->with('success', 'Password berhasil diubah menjadi tanggal lahir Anda (format: ddmmyyyy).');
    }

    public function riwayatPengambilan()
    {
        $pengambilanList = Penitipan::with('barang', 'penitip')
            ->where(function ($query) {
                $query->where('STATUS_PENITIPAN', 'Diambil Kembali')
                    ->orWhereNull('STATUS_PENITIPAN');
            })
            ->orderByDesc('TANGGAL_BERAKHIR') // atau kolom waktu lainnya
            ->paginate(10);

        return view('gudang.riwayat_pengambilan', compact('pengambilanList'));
    }

    public function handle()
    {
        $expired = Penitipan::where('STATUS_PENITIPAN', 'Siap Diambil')
            ->whereDate('TANGGAL_SIAP_AMBIL', '<=', now()->subDays(2))
            ->get();

        foreach ($expired as $transaksi) {
            $transaksi->STATUS_PENITIPAN = 'Hangus';
            $transaksi->save();

            $barang = $transaksi->barang;
            $barang->STATUS = 'Barang untuk Donasi';
            $barang->save();

            // Kirim notifikasi ke penitip dan pembeli
            // ...
        }
    }


    public function transaksiHangus()
    {
        $batasTanggal = Carbon::now()->subDays(2)->startOfDay();

        // Ambil semua transaksi dengan metode 'Ambil Sendiri',
        // tanggal transaksi lebih dari 2 hari lalu, dan status selain 'Selesai'
        $transaksiHangus = Transaksi::with(['pembeli', 'barang'])
            ->where('METODE_PENGIRIMAN', 'Ambil Sendiri')
            ->whereDate('TANGGAL_TRANSAKSI', '<=', $batasTanggal)
            ->where('STATUS_TRANSAKSI', '!=', 'Selesai') // ✅ Tambahan filter
            ->orderBy('TANGGAL_TRANSAKSI', 'desc')
            ->get();

        return view('gudang.transaksi_hangus', compact('transaksiHangus'));
    }

    public function konfirmasiHangus($id)
    {
        $transaksi = Transaksi::with('barang')->findOrFail($id);

        // Cek apakah sudah hangus
        if ($transaksi->penitipan->STATUS_PENITIPAN === 'Hangus') {
            return redirect()->back()->with('info', 'Transaksi sudah hangus sebelumnya.');
        }

        // Update status transaksi dan barang
        $transaksi->STATUS_TRANSAKSI = 'Hangus';
        $transaksi->save();

        if ($transaksi->barang) {
            $transaksi->barang->STATUS = 'Donasi';
            $transaksi->barang->save();
        }

        return redirect()->back()->with('success', 'Status transaksi diubah menjadi Hangus dan barang menjadi Donasi.');
    }

    public function riwayatDistribusi()
    {
        $riwayatDistribusi = Transaksi::with('pembeli', 'barang')
            ->where('STATUS_TRANSAKSI', 'selesai')
            ->orderByDesc('TANGGAL_TRANSAKSI')
            ->get();

        return view('gudang.distribusi.riwayat', compact('riwayatDistribusi'));
    }


    public function downloadDistribusiPdf()
    {
        $transaksiList = Transaksi::with('pembeli')
        ->where('STATUS_TRANSAKSI', 'selesai')
        ->orderBy('TANGGAL_TRANSAKSI', 'desc')
        ->get();

    return view('gudang.pdf.riwayat_distribusi', compact('transaksiList'));
    }


    public function cetakNotaKurir($id)
    {
        $transaksi = Transaksi::with([
        'pembeli.alamat', // penting!
        'pegawai',
        'barang',
    ])->findOrFail($id);

        $pdf = PDF::loadView('gudang.pdf.nota_kurir', compact('transaksi'));
        return $pdf->download('nota_kurir_' . $id . '.pdf');
    }

    public function cetakNotaAmbil($id)
    {
        $transaksi = Transaksi::with(['pembeli', 'barang'])->findOrFail($id);

        $pdf = PDF::loadView('gudang.pdf.nota_ambil', compact('transaksi'));
        return $pdf->download('nota_ambil_sendiri_' . $id . '.pdf');
    }

    public function showTransaksi($id)
    {
        $transaksi = Transaksi::with([
            'barang.pembeli',
            'penitipan.penitip',
            'penitipan.barangHunter.pegawai'
        ])->findOrFail($id);

        return view('gudang.transaksi.show', compact('transaksi'));
    }

    public function daftarTransaksiPending()
    {
        $transaksiList = Transaksi::with([
            'barang.pembeli',
            'penitipan.penitip',
            'penitipan.barangHunter.pegawai'
        ])
        ->whereIn('STATUS_TRANSAKSI', ['Disiapkan'])
        ->orderByDesc('TANGGAL_TRANSAKSI')
        ->paginate(10);

        return view('gudang.transaksi.pending', compact('transaksiList'));
    }

    
    public function formPenjadwalanPengiriman($idTransaksi)
    {
        $transaksi = Transaksi::with('barang.pembeli', 'penitipan.penitip')->findOrFail($idTransaksi);
        $kurirList = Pegawai::where('ID_ROLE', 5)->get();

        return view('gudang.transaksi.penjadwalan', [
            'transaksi' => $transaksi,
            'kurirs' => $kurirList
        ]);
    }

    public function simpanPenjadwalanPengiriman(Request $request, $idTransaksi)
    {
        $transaksi = Transaksi::findOrFail($idTransaksi);

        $request->validate([
            'kurir_id' => 'required|exists:pegawai,ID_PEGAWAI',
        ]);

        $waktuSekarang = Carbon::now(); 
        $batasWaktu = Carbon::today()->setTime(10, 0); 

        if ($waktuSekarang->greaterThanOrEqualTo($batasWaktu)) {
            return back()->withErrors(['jam' => 'Pengiriman tidak dapat dijadwalkan setelah jam 16:00. Silakan proses besok.']);
        }

        $transaksi->TANGGAL_PENGIRIMAN = $waktuSekarang->toDateString(); 
        $transaksi->STATUS_TRANSAKSI = 'Sedang Dikirim';
        $transaksi->save();

        $transaksi->pegawai()->sync([$request->kurir_id]);

        return redirect()->route('gudang.transaksi.pending')->with('success', 'Pengiriman berhasil dijadwalkan.');
    }


    public function jadwalkanPengambilanSendiri($idTransaksi)
    {
        $transaksi = Transaksi::findOrFail($idTransaksi);
        $transaksi->STATUS_TRANSAKSI = 'Selesai';
        $transaksi->TANGGAL_TRANSAKSI = now(); 
        $transaksi->save();

        return back()->with('success', 'Transaksi telah diselesaikan karena diambil langsung.');
    }

    public function konfirmasiDiterima($idTransaksi)
    {
        try {
            $transaksi = Transaksi::findOrFail($idTransaksi);
            $sekarang = Carbon::now();
            $batas = Carbon::today()->setTime(16, 0);

            \Log::info('Konfirmasi Diterima - Status Awal:', [
                'ID_TRANSAKSI' => $idTransaksi,
                'STATUS_TRANSAKSI' => $transaksi->STATUS_TRANSAKSI,
                'STATUS_PENGIRIMAN' => $transaksi->STATUS_PENGIRIMAN,
                'METODE_PENGIRIMAN' => $transaksi->METODE_PENGIRIMAN
            ]);

            // Cek jika sudah lewat jam 4 sore untuk pengiriman baru
            if ($sekarang->greaterThanOrEqualTo($batas) && $transaksi->STATUS_TRANSAKSI == 'Menunggu Pengiriman') {
                return back()->with('toast_error', 'Tidak dapat mengkonfirmasi pengiriman setelah jam 16:00.');
            }

            // Logic berdasarkan metode pengiriman
            if ($transaksi->METODE_PENGIRIMAN === 'Kurir') {
                // Untuk pengiriman via kurir
                if ($transaksi->STATUS_TRANSAKSI === 'Menunggu Pengiriman') {
                    // Mulai pengiriman
                    $transaksi->STATUS_TRANSAKSI = 'Sedang Dikirim';
                    $transaksi->STATUS_PENGIRIMAN = 'Sedang Dikirim';
                    $transaksi->TANGGAL_TRANSAKSI = now();
                    
                    // Assign kurir jika belum ada
                    $kurirId = auth()->guard('pegawai')->user()->ID_PEGAWAI ?? null;
                    if ($kurirId) {
                        $transaksi->ID_PEGAWAI = $kurirId;
                    }
                    
                    $message = 'Pengiriman berhasil dikonfirmasi dan sedang dalam proses pengiriman.';
                    
                } elseif ($transaksi->STATUS_TRANSAKSI === 'Sedang Dikirim' || 
                        $transaksi->STATUS_PENGIRIMAN === 'Sedang Dikirim' ||
                        $transaksi->STATUS_PENGIRIMAN === 'Menunggu') {
                    // Konfirmasi diterima oleh pembeli
                    $transaksi->STATUS_TRANSAKSI = 'Selesai';
                    $transaksi->STATUS_PENGIRIMAN = 'Terkirim';
                    
                    $message = 'Pengiriman berhasil dikonfirmasi telah diterima pembeli.';
                    
                } else {
                    // Status sudah selesai atau tidak valid
                    return back()->with('toast_info', 'Status transaksi sudah final atau tidak dapat diubah.');
                }
                
            } elseif ($transaksi->METODE_PENGIRIMAN === 'Ambil Sendiri') {
                // Untuk pengambilan langsung
                if (in_array($transaksi->STATUS_TRANSAKSI, ['Disiapkan', 'Menunggu Pengiriman'])) {
                    $transaksi->STATUS_TRANSAKSI = 'Selesai';
                    $transaksi->STATUS_PENGIRIMAN = 'Diambil';
                    $transaksi->TANGGAL_TRANSAKSI = now();
                    
                    $message = 'Transaksi berhasil diselesaikan - barang telah diambil pembeli.';
                    
                } else {
                    return back()->with('toast_info', 'Status transaksi sudah final atau tidak dapat diubah.');
                }
            } else {
                return back()->with('toast_error', 'Metode pengiriman tidak dikenali.');
            }

            // Simpan perubahan
            $transaksi->save();

            // Update penitipan status jika ada
            if ($transaksi->ID_PENITIPAN && $transaksi->STATUS_TRANSAKSI === 'Selesai') {
                $transaksi->updateRelatedPenitipanStatusAndDate();
            }

            \Log::info('Konfirmasi Diterima - Status Akhir:', [
                'ID_TRANSAKSI' => $idTransaksi,
                'STATUS_TRANSAKSI' => $transaksi->STATUS_TRANSAKSI,
                'STATUS_PENGIRIMAN' => $transaksi->STATUS_PENGIRIMAN,
                'message' => $message
            ]);

            return back()->with('toast_success', $message);

        } catch (\Exception $e) {
            \Log::error('Error konfirmasi diterima:', [
                'ID_TRANSAKSI' => $idTransaksi,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('toast_error', 'Terjadi kesalahan saat mengkonfirmasi: ' . $e->getMessage());
        }
    }

    // Method tambahan untuk kurir mengkonfirmasi pengiriman selesai
    public function konfirmasiPengirimanSelesai($idTransaksi)
    {
        try {
            $transaksi = Transaksi::findOrFail($idTransaksi);
            $kurirId = auth()->guard('pegawai')->user()->ID_PEGAWAI;

            // Validasi kurir yang mengkonfirmasi adalah kurir yang ditugaskan
            if ($transaksi->ID_PEGAWAI != $kurirId) {
                return back()->with('toast_error', 'Anda tidak memiliki akses untuk mengkonfirmasi transaksi ini.');
            }

            // Validasi status transaksi
            if ($transaksi->STATUS_TRANSAKSI !== 'Sedang Dikirim') {
                return back()->with('toast_error', 'Transaksi tidak dalam status sedang dikirim.');
            }

            // Update status menjadi terkirim
            $transaksi->STATUS_TRANSAKSI = 'Selesai';
            $transaksi->STATUS_PENGIRIMAN = 'Terkirim';
            $transaksi->save();

            // Update penitipan status jika ada
            if ($transaksi->ID_PENITIPAN) {
                $transaksi->updateRelatedPenitipanStatusAndDate();
            }

            \Log::info('Kurir konfirmasi pengiriman selesai:', [
                'ID_TRANSAKSI' => $idTransaksi,
                'KURIR_ID' => $kurirId,
                'STATUS_TRANSAKSI' => $transaksi->STATUS_TRANSAKSI,
                'STATUS_PENGIRIMAN' => $transaksi->STATUS_PENGIRIMAN
            ]);

            return back()->with('toast_success', 'Pengiriman berhasil dikonfirmasi selesai.');

        } catch (\Exception $e) {
            \Log::error('Error konfirmasi pengiriman selesai:', [
                'ID_TRANSAKSI' => $idTransaksi,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('toast_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function indexPenjadwalan()
    {
        $transaksiPending = Transaksi::with(['barang.pembeli'])
            ->whereIn('STATUS_TRANSAKSI', ['Disiapkan'])
            ->get();

        $transaksiSelesai = Transaksi::with(['barang.pembeli', 'pegawai.role'])
            ->whereIn('STATUS_TRANSAKSI', ['Selesai', 'Sedang Dikirim'])
            ->get();

        return view('gudang.transaksi.penjadwalan', compact('transaksiPending', 'transaksiSelesai'));
    }

    public function detailKurir($id)
    {
        $transaksi = Transaksi::with(['barang', 'barang.pembeli', 'pegawai.role'])->findOrFail($id);
        return view('gudang.transaksi.detail', compact('transaksi'));
    }

    public function show($id)
    {
        $transaksi = Transaksi::with([
            'barang',
            'barang.pembeli',
            'penitipan.penitip',
            'penitipan.barangHunter.pegawai',
            'kurir.role'
        ])->findOrFail($id);

        return view('gudang.transaksi.detail', compact('transaksi'));
    }

    
public function searchDaftarPenitipan(Request $request)
{
    $search = $request->search;

    $query = Penitipan::with(['barang.kategori', 'barangHunter', 'penitip', 'pegawai']);

  if ($search) {
    $query->where(function ($q) use ($search) {

        // Cek apakah input seperti "PNT-0004" dan ambil ID-nya
        $formattedId = null;
        if (preg_match('/PNT-(\d+)/', strtoupper($search), $matches)) {
            $formattedId = (int) ltrim($matches[1], '0');
        }

        $q->when($formattedId, function ($q2) use ($formattedId) {
            $q2->orWhere('ID_PENITIPAN', $formattedId);
        });

        // Pencarian umum
        $q->orWhere('STATUS_PENITIPAN', 'like', "%$search%")
          ->orWhereRaw("CAST(ID_PENITIPAN AS CHAR) LIKE ?", ["%$search%"])
          ->orWhereDate('TANGGAL_MULAI', $search)
          ->orWhereDate('TANGGAL_BERAKHIR', $search)
          ->orWhereRaw("DATE_FORMAT(TANGGAL_MULAI, '%d-%m-%Y') LIKE ?", ["%$search%"])
          ->orWhereRaw("DATE_FORMAT(TANGGAL_BERAKHIR, '%d-%m-%Y') LIKE ?", ["%$search%"])
          ->orWhereHas('penitip', function ($q) use ($search) {
              $q->where('NAMA_PENITIP', 'like', "%$search%");
          })
          ->orWhereHas('pegawai', function ($q) use ($search) {
              $q->where('NAMA_PEGAWAI', 'like', "%$search%");
          })
          ->orWhereHas('barang', function ($q) use ($search) {
              $q->where('NAMA_BARANG', 'like', "%$search%")
                ->orWhereHas('kategori', function ($q) use ($search) {
                    $q->where('JENIS_KATEGORI', 'like', "%$search%");
                });
          });
    });
}

    $penitipan = $query->orderBy('TANGGAL_MULAI', 'desc')->get();

    // Filter penitipan valid
    $penitipan = $penitipan->filter(function ($item) {
        return $item->penitip && ($item->barang || $item->barangHunter);
    });

    $penitips = Penitip::all();
    $barangs = Barang::with('kategori')
        ->whereIn('STATUS', ['Tersedia', 'Penitipan'])
        ->get();

    return view('gudang.penitipan.index', compact('penitipan', 'penitips', 'barangs'));
}



}


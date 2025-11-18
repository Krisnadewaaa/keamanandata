<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Penitip;
use App\Models\Pegawai;
use App\Models\Diskusi;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class CustomerServiceController extends Controller
{
    /**
     * Menampilkan dashboard customer service
     */
    public function dashboard()
    {
        $totalPenitip = Penitip::count();
        $penitipTerbaru = Penitip::orderBy('ID_PENITIP', 'desc')->take(5)->get();
        $pegawai = Auth::guard('pegawai')->user();
        
        try {
            // Pastikan tabel diskusi memiliki kolom yang diperlukan
            if (DB::getSchemaBuilder()->hasColumn('diskusi', 'ID_BARANG') && 
                DB::getSchemaBuilder()->hasColumn('diskusi', 'ID_PARENT')) {
                
                // Ambil diskusi terbaru yang belum dijawab (tanpa filter relasi barang)
                $diskusiBaru = Diskusi::whereNull('ID_PARENT')
                                ->whereDoesntHave('replies')
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                
                // Hitung total diskusi yang belum dijawab (tanpa filter relasi barang)
                $totalDiskusiBelumDijawab = Diskusi::whereNull('ID_PARENT')
                                        ->whereDoesntHave('replies')
                                        ->count();
            } else {
                // Jika kolom belum ada, gunakan array kosong
                $diskusiBaru = collect([]);
                $totalDiskusiBelumDijawab = 0;
            }
        } catch (\Exception $e) {
            // Tangani error dengan menyediakan data default
            $diskusiBaru = collect([]);
            $totalDiskusiBelumDijawab = 0;
        }
        
        return view('cs.dashboard', compact('totalPenitip', 'penitipTerbaru', 'pegawai', 'diskusiBaru', 'totalDiskusiBelumDijawab'));
    }

    /**
     * Menampilkan profil Customer Service
     */
    public function profile()
    {
        $pegawai = Auth::guard('pegawai')->user();
        return view('cs.profile', compact('pegawai'));
    }
    
    /**
     * Form edit profil
     */
    public function edit()
    {
        $pegawai = Auth::guard('pegawai')->user();
        return view('cs.edit', compact('pegawai'));
    }
    
    /**
     * Update profil customer service
     */
    public function updateProfile(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        
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
        
        return redirect()->route('cs.profile')->with('success', 'Profil berhasil diperbarui!');
    }
    
    /**
     * Method khusus untuk update password menggunakan tanggal lahir
     */
    public function updatePasswordToDob(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        
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
        
        return redirect()->route('cs.profile')
                    ->with('success', 'Password berhasil diubah menjadi tanggal lahir Anda (format: ddmmyyyy).');
    }
    
    /**
     * Menampilkan daftar penitip
     */
    public function penitipIndex(Request $request)
    {
        // Filter berdasarkan pencarian jika ada
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $penitip = Penitip::where('NAMA_PENITIP', 'LIKE', "%{$search}%")
                        ->orWhere('EMAIL_PENITIP', 'LIKE', "%{$search}%")
                        ->paginate(10);
                                
            $penitip->appends(['search' => $search]);
        } else {
            $penitip = Penitip::paginate(10);
        }
        
        return view('cs.penitip.index', compact('penitip'));
    }

    /**
     * Menampilkan detail penitip
     */
    public function penitipShow($id)
    {
        $penitip = Penitip::findOrFail($id);
        return view('cs.penitip.show', compact('penitip'));
    }

    /**
     * Menampilkan form untuk menambah penitip
     */
    public function penitipCreate()
    {
        return view('cs.penitip.create');
    }

    /**
     * Menyimpan penitip baru
     */
    public function penitipStore(Request $request)
    {
        // Validasi Input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:penitip,EMAIL_PENITIP',
            'password' => 'required|string|min:6|confirmed',
            'no_ktp' => 'required|string|max:20|unique:penitip,NO_KTP',
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput($request->except('password', 'password_confirmation', 'foto_ktp'));
        }

        // Upload foto KTP ke public/ktp
        $fotoKtpPath = null;
        if ($request->hasFile('foto_ktp')) {
            $fotoKtp = $request->file('foto_ktp');
            $fotoKtpName = time() . '_' . $fotoKtp->getClientOriginalName();

            // Pindahkan file langsung ke folder public/images/ktp
            $fotoKtp->move(public_path('images/ktp'), $fotoKtpName);

            // Path yang akan disimpan hanya relative path-nya saja
            $fotoKtpPath = 'images/ktp/' . $fotoKtpName;
        }

        // Simpan data ke database
        $penitip = new Penitip();
        $penitip->NAMA_PENITIP = $request->nama;
        $penitip->EMAIL_PENITIP = $request->email;
        $penitip->PASSWORD_PENITIP = Hash::make($request->password);
        $penitip->NO_KTP = $request->no_ktp;
        $penitip->FOTO_KTP = $fotoKtpPath;
        $penitip->RATING_PENITIP = 0.0;
        $penitip->UANG_PENITIP = 0;
        $penitip->save();

        return redirect()->route('cs.penitip.index')
                        ->with('success', 'Penitip berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit penitip
     */
    public function penitipEdit($id)
    {
        $penitip = Penitip::findOrFail($id);
        return view('cs.penitip.edit', compact('penitip'));
    }

    /**
     * Menyimpan perubahan penitip
     */
    public function penitipUpdate(Request $request, $id)
    {
        $penitip = Penitip::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:penitip,EMAIL_PENITIP,'.$id.',ID_PENITIP',
            'no_ktp' => 'required|string|max:20|unique:penitip,NO_KTP,'.$id.',ID_PENITIP',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        // Upload foto KTP baru jika ada
        if ($request->hasFile('foto_ktp')) {
            // Hapus foto lama jika ada
            if ($penitip->FOTO_KTP && file_exists(public_path($penitip->FOTO_KTP))) {
                unlink(public_path($penitip->FOTO_KTP));
            }
            
            $fotoKtp = $request->file('foto_ktp');
            $fotoKtpName = time() . '_' . $fotoKtp->getClientOriginalName();

            // Pindahkan file ke public/images/ktp
            $fotoKtp->move(public_path('images/ktp'), $fotoKtpName);

            // Path yang disimpan di database adalah relative path
            $penitip->FOTO_KTP = 'images/ktp/' . $fotoKtpName;
        }

        $penitip->NAMA_PENITIP = $request->nama;
        $penitip->EMAIL_PENITIP = $request->email;
        $penitip->NO_KTP = $request->no_ktp;
        
        // Update password jika disediakan
        if ($request->filled('password') && $request->password === $request->password_confirmation) {
            $penitip->PASSWORD_PENITIP = Hash::make($request->password);
        }
        
        $penitip->save();

        return redirect()->route('cs.penitip.index')
                        ->with('success', 'Data penitip berhasil diperbarui!');
    }

    /**
     * Menghapus penitip
     */
    public function penitipDestroy($id)
    {
        $penitip = Penitip::findOrFail($id);
        
        // Hapus foto KTP jika ada
        if ($penitip->FOTO_KTP) {
            Storage::delete('public/' . $penitip->FOTO_KTP);
        }
        
        // Periksa apakah penitip memiliki data terkait
        if ($penitip->penitipan()->count() > 0) {
            return redirect()->route('cs.penitip.index')
                            ->with('error', 'Tidak dapat menghapus penitip karena masih memiliki data penitipan.');
        }
        
        $penitip->delete();
        
        return redirect()->route('cs.penitip.index')
                        ->with('success', 'Penitip berhasil dihapus!');
    }

    public function diskusiIndex(Request $request)
    {
        try {
            // Pastikan tabel diskusi memiliki kolom yang diperlukan
            if (DB::getSchemaBuilder()->hasColumn('diskusi', 'ID_BARANG') && 
                DB::getSchemaBuilder()->hasColumn('diskusi', 'ID_PARENT')) {
                
                $query = Diskusi::whereNull('ID_PARENT'); // Hanya tampilkan parent diskusi
                
                // Eager load relasi
                $query->with(['barang', 'pembeli', 'pegawai']);
                
                // Filter berdasarkan status jawaban
                if ($request->has('status')) {
                    if ($request->status == 'answered') {
                        $query->whereHas('replies');
                    } elseif ($request->status == 'unanswered') {
                        $query->whereDoesntHave('replies');
                    }
                }
                
                // Filter berdasarkan kata kunci
                if ($request->has('search') && $request->search != '') {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->where('KOMENTAR', 'LIKE', "%{$search}%")
                        ->orWhere('NAMA_PENGIRIM', 'LIKE', "%{$search}%");
                        // Filter berdasarkan nama barang jika barang masih ada
                        if (DB::getSchemaBuilder()->hasTable('barang')) {
                            $q->orWhereHas('barang', function($q) use ($search) {
                                $q->where('NAMA_BARANG', 'LIKE', "%{$search}%");
                            });
                        }
                    });
                }
                
                // Filter berdasarkan barang
                if ($request->has('barang_id') && $request->barang_id != '') {
                    $query->where('ID_BARANG', $request->barang_id);
                }
                
                // Urutkan berdasarkan tanggal terbaru
                $query->orderBy('created_at', 'desc');
                
                $diskusi = $query->paginate(10);
                
                // Ambil daftar barang untuk filter
                $barangs = Barang::where('STATUS', 'Tersedia')->get();
            } else {
                // Jika kolom belum ada, gunakan array kosong
                $diskusi = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
                $barangs = collect([]);
            }
        } catch (\Exception $e) {
            // Tangani error dengan menyediakan data default
            $diskusi = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $barangs = collect([]);
        }
        
        return view('cs.diskusi.index', compact('diskusi', 'barangs'));
    }
    
    /**
     * Menampilkan detail diskusi
     */
    public function diskusiShow($id)
    {
        try {
            $diskusi = Diskusi::with(['barang', 'pembeli', 'pegawai', 'replies'])->findOrFail($id);
            return view('cs.diskusi.show', compact('diskusi'));
        } catch (\Exception $e) {
            return redirect()->route('cs.diskusi.index')
                            ->with('error', 'Terjadi kesalahan saat mengambil data diskusi: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan form untuk menjawab diskusi
     */
    public function diskusiReplyForm($id)
    {
        try {
            $diskusi = Diskusi::with('barang')->findOrFail($id);
            return view('cs.diskusi.reply', compact('diskusi'));
        } catch (\Exception $e) {
            return redirect()->route('cs.diskusi.index')
                            ->with('error', 'Terjadi kesalahan saat mengambil data diskusi: ' . $e->getMessage());
        }
    }
    
    /**
     * Menyimpan jawaban diskusi
     */
    public function diskusiReply(Request $request, $id)
    {
        $request->validate([
            'komentar' => 'required|string|max:255',
        ]);
        
        try {
            $parentDiskusi = Diskusi::findOrFail($id);
            
            $diskusi = new Diskusi();
            $diskusi->ID_BARANG = $parentDiskusi->ID_BARANG;
            $diskusi->ID_PARENT = $id;
            $diskusi->ID_PEGAWAI = Auth::guard('pegawai')->user()->ID_PEGAWAI;
            $diskusi->NAMA_PENGIRIM = Auth::guard('pegawai')->user()->NAMA_PEGAWAI;
            $diskusi->IS_ADMIN = true;
            $diskusi->KOMENTAR = $request->komentar;
            $diskusi->save();
            
            return redirect()->route('cs.diskusi.show', $id)
                            ->with('success', 'Jawaban berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('cs.diskusi.index')
                            ->with('error', 'Terjadi kesalahan saat menyimpan jawaban: ' . $e->getMessage());
        }
    }
    
    /**
     * Menghapus diskusi
     */
    public function diskusiDestroy($id)
    {
        try {
            $diskusi = Diskusi::findOrFail($id);
            
            // Jika diskusi adalah parent, hapus semua reply juga
            if ($diskusi->ID_PARENT === null) {
                $replies = Diskusi::where('ID_PARENT', $id)->get();
                foreach ($replies as $reply) {
                    $reply->delete();
                }
            }
            
            $diskusi->delete();
            
            return redirect()->route('cs.diskusi.index')
                            ->with('success', 'Diskusi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('cs.diskusi.index')
                            ->with('error', 'Terjadi kesalahan saat menghapus diskusi: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\Donasi;
use App\Models\Sales;
use App\Models\Barang;
use App\Models\Organisasi;
use PDF;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Penitip;
use Carbon\Carbon;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $totalPegawai = Pegawai::count();
        $totalHunter = Pegawai::where('ID_ROLE', 6)->count();
        $totalKurir = Pegawai::where('ID_ROLE', 5)->count();
        $donasiTerbaru = Donasi::orderBy('TANGGAL_DONASI', 'desc')->first();

        return view('owner.dashboard', compact('totalPegawai', 'totalHunter', 'totalKurir', 'donasiTerbaru'));
    }

    public function profile()
    {
        // Mengambil data profil owner dari guard pegawais
        $owner = auth('pegawai')->user(); 

        if (!$owner) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan atau belum login.');
        }

        return view('owner.profile', compact('owner'));
    }

    public function edit()
    {
        $owner = auth('pegawai')->user();  // Ambil data owner yang sedang login
        // Jika ingin mengedit role atau data lain terkait roles, tambahkan
        $roles = Role::all();
        return view('owner.edit-profile', compact('owner', 'roles')); // Kirim data owner dan roles ke tampilan
    }

    public function updateProfile(Request $request)
    {
        // Ambil data owner yang sedang login
        $owner = auth('pegawai')->user();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pegawai,EMAIL_PEGAWAI,' . $owner->ID_PEGAWAI . ',ID_PEGAWAI',
            'alamat' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:13',
            'tanggal_lahir' => 'nullable|date',
            'password' => 'nullable|confirmed|min:8',
        ]);

        // Jika validasi gagal, kembali ke halaman sebelumnya dengan error
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        // Update data owner
        $owner->NAMA_PEGAWAI = $request->nama;
        $owner->EMAIL_PEGAWAI = $request->email;
        $owner->ALAMAT_PEGAWAI = $request->alamat;
        $owner->NO_TELEPON_PEGAWAI = $request->no_telepon;
        
        // Update tanggal lahir jika disediakan - pastikan ini dikonversi ke Carbon
        if ($request->filled('tanggal_lahir')) {
            $owner->TANGGAL_LAHIR = Carbon::parse($request->tanggal_lahir)->format('Y-m-d');
        }

        // Ubah password jika disediakan dan valid
        if ($request->filled('password') && $request->password === $request->password_confirmation) {
            $owner->PASSWORD_PEGAWAI = Hash::make($request->password);
        }

        // Simpan perubahan data owner
        $owner->save();

        // Redirect ke halaman profil dengan pesan sukses
        return redirect()->route('owner.profile')
                        ->with('success', 'Profil berhasil diperbarui!');
    }

    // Method khusus untuk update password menggunakan tanggal lahir
    public function updatePasswordToDob(Request $request)
    {
        $owner = auth('pegawai')->user();
        
        // Verifikasi password saat ini
        if ($request->current_password !== $owner->PASSWORD_PEGAWAI) {
            return redirect()->back()
                        ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                        ->withInput();
        }
        
        // Cek jika tanggal lahir tersedia
        if (!$owner->TANGGAL_LAHIR) {
            return redirect()->back()
                        ->with('error', 'Tanggal lahir belum diatur. Silakan perbarui profil Anda terlebih dahulu.')
                        ->withInput();
        }
        
        // Buat password dari tanggal lahir (format: ddmmyyyy)
        try {
            // Pastikan TANGGAL_LAHIR adalah instance Carbon atau dikonversi
            if (is_string($owner->TANGGAL_LAHIR)) {
                $dobPassword = Carbon::parse($owner->TANGGAL_LAHIR)->format('dmY');
            } else {
                $dobPassword = $owner->TANGGAL_LAHIR->format('dmY');
            }
            
            // Update password
            $owner->PASSWORD_PEGAWAI = Hash::make($dobPassword);
            $owner->save();
            
            return redirect()->route('owner.profile')
                        ->with('success', 'Password berhasil diubah menjadi tanggal lahir Anda (format: ddmmyyyy).');
        } catch (\Exception $e) {
            return redirect()->back()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    // Daftar request donasi
    public function donationRequests()
    {
        try {
            \Log::info('Accessing donationRequests method');
            
            // Use Eloquent models instead of query builder to maintain relationships
            $donationRequests = Donasi::with(['organisasi', 'barang', 'pegawai'])
                ->whereNull('status')
                ->orderBy('TANGGAL_DONASI', 'desc')
                ->get();
                
            \Log::info('Found donation requests: ' . $donationRequests->count());

            // Use Eloquent for barang with relationships
            $barangDonasi = Barang::with(['penitipan.penitip'])
                ->where('STATUS', 'Tersedia')
                ->whereNull('ID_ORGANISASI')
                ->get();
                
            \Log::info('Found available items: ' . $barangDonasi->count());

            return view('owner.donasi.requests', compact('donationRequests', 'barangDonasi'));
            
        } catch (\Exception $e) {
            \Log::error('Error in donationRequests method: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return with empty collections to prevent undefined variable errors
            $donationRequests = collect();
            $barangDonasi = collect();
            
            return view('owner.donasi.requests', compact('donationRequests', 'barangDonasi'))
                ->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    public function approveDonation(Request $request, $id)
    {
        try {
            $donasi = Donasi::with(['organisasi'])->find($id);

            if (!$donasi) {
                return redirect()->route('owner.donasi.requests')
                                ->with('error', 'Donasi tidak ditemukan!');
            }

            // Check if the selected item is still available
            $barang = Barang::with(['penitipan.penitip'])
                        ->where('ID_BARANG', $request->barang_id)
                        ->where('STATUS', 'Tersedia')
                        ->whereNull('ID_ORGANISASI')
                        ->first();

            if (!$barang) {
                return redirect()->route('owner.donasi.requests')
                                ->with('error', 'Barang yang dipilih sudah tidak tersedia!');
            }

            // Update donation record
            $donasi->ID_BARANG = $request->barang_id;
            $donasi->status = 'approved';
            $donasi->save();

            // Update barang status to 'Donasi' and assign to organization
            $barang->STATUS = 'Donasi';
            $barang->ID_ORGANISASI = $donasi->ID_ORGANISASI;
            $barang->save();

            // Update penitipan status if exists
            if ($barang->penitipan) {
                $barang->penitipan->STATUS_PENITIPAN = 'Donasi';
                $barang->penitipan->TANGGAL_BERAKHIR = Carbon::now()->format('Y-m-d');
                $barang->penitipan->save();
            }

            // Add points to penitip if exists
            if ($barang->penitipan && $barang->penitipan->penitip) {
                $penitip = $barang->penitipan->penitip;
                $poin = 1; // 1 poin per donasi
                $penitip->POIN_PENITIP = ($penitip->POIN_PENITIP ?? 0) + $poin;
                $penitip->save();
            }

            return redirect()->route('owner.donasi.requests')
                            ->with('success', 'Donasi berhasil disetujui! Status barang telah diupdate menjadi Donasi.');
        } catch (\Exception $e) {
            \Log::error('Error in approveDonation: ' . $e->getMessage());
            return redirect()->route('owner.donasi.requests')
                            ->with('error', 'Terjadi kesalahan saat menyetujui donasi.');
        }
    }

    public function rejectDonation($id)
    {
        $donasi = Donasi::find($id);

        if (!$donasi) {
            return redirect()->route('owner.donasi.requests')
                            ->with('error', 'Donasi tidak ditemukan!');
        }

        $donasi->status = 'rejected';
        $donasi->save();

        return redirect()->route('owner.donasi.requests')
                        ->with('success', 'Donasi berhasil ditolak!');
    }

    public function donationHistory()
    {
        try {
            $donationHistory = Donasi::with(['organisasi', 'barang'])
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('TANGGAL_DONASI', 'desc')
                ->get();

            return view('owner.donasi.history', compact('donationHistory'));
        } catch (\Exception $e) {
            \Log::error('Error in donationHistory: ' . $e->getMessage());
            
            $donationHistory = collect();
            return view('owner.donasi.history', compact('donationHistory'))
                ->with('error', 'Terjadi kesalahan saat memuat riwayat donasi.');
        }
    }

    public function allocateDonation()
    {
        try {
            // Use Eloquent models to maintain relationships
            $barangDonasi = Barang::with(['penitipan.penitip'])
                                ->where('STATUS', 'Tersedia')
                                ->whereNull('ID_ORGANISASI')
                                ->get();
                                
            $organisasi = Organisasi::all();

            return view('owner.donasi.allocate', compact('barangDonasi', 'organisasi'));
        } catch (\Exception $e) {
            \Log::error('Error in allocateDonation: ' . $e->getMessage());
            
            // Provide fallback data
            $barangDonasi = collect();
            $organisasi = collect();
            
            return view('owner.donasi.allocate', compact('barangDonasi', 'organisasi'))
                ->with('error', 'Terjadi kesalahan saat memuat data alokasi donasi.');
        }
    }

        public function allocateDonationToOrganization(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'barang_id'      => 'required|exists:barang,ID_BARANG',
                'organisasi_id'  => 'required|exists:organisasi,ID_ORGANISASI',
            ]);

            // Use Eloquent to get barang with relationships
            $barang = Barang::with(['penitipan.penitip'])
                        ->where('ID_BARANG', $validated['barang_id'])
                        ->where('STATUS', 'Tersedia')
                        ->whereNull('ID_ORGANISASI')
                        ->first();

            if (!$barang) {
                return redirect()->route('owner.donasi.allocate')
                                ->with('error', 'Barang tidak ditemukan atau sudah didonasikan sebelumnya.');
            }

            // Set organisasi dan ubah status menjadi "Donasi"
            $barang->ID_ORGANISASI = $validated['organisasi_id'];
            $barang->STATUS = 'Donasi';
            $barang->save();

            // Update penitipan status if exists
            if ($barang->penitipan) {
                $barang->penitipan->STATUS_PENITIPAN = 'Donasi';
                $barang->penitipan->TANGGAL_BERAKHIR = Carbon::now()->format('Y-m-d');
                $barang->penitipan->save();
            }

            // ==================== LOGIKA POIN ====================
            if ($barang->penitipan && $barang->penitipan->penitip) {
                $penitip = $barang->penitipan->penitip;
                $poin = 1; // 1 poin per donasi
                $penitip->POIN_PENITIP = ($penitip->POIN_PENITIP ?? 0) + $poin;
                $penitip->save();
            }
            // =====================================================

            // Masukkan data ke tabel donasi
            $donasi = Donasi::create([
                'ID_ORGANISASI'   => $validated['organisasi_id'],
                'ID_BARANG'       => $barang->ID_BARANG,
                'TANGGAL_DONASI'  => now(),
                'STATUS'          => 'approved',
                'ISI_REQUEST'     => 'Alokasi langsung oleh owner', // Default description
            ]);

            return redirect()->route('owner.donasi.allocate')
                            ->with('success', 'Barang berhasil dialokasikan! Status barang telah diupdate menjadi Donasi dan penitip mendapatkan poin!');
        } catch (\Exception $e) {
            \Log::error('Error in allocateDonationToOrganization: ' . $e->getMessage());
            return redirect()->route('owner.donasi.allocate')
                            ->with('error', 'Terjadi kesalahan saat mengalokasikan donasi.');
        }
    }

    public function updateDonationInfo(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'tanggal_donasi' => 'required|date',
        ]);

        // Cari donasi berdasarkan ID
        $donasi = Donasi::find($id);

        // Cek jika tidak ditemukan
        if (!$donasi) {
            return redirect()->route('owner.donasi.requests')
                            ->with('error', 'Donasi tidak ditemukan!');
        }

        // Update tanggal donasi
        $donasi->TANGGAL_DONASI = $request->tanggal_donasi;
        $donasi->save();

        // Ambil relasi organisasi dari donasi
        $organisasi = $donasi->organisasi;

        if ($organisasi) {
            $organisasi->NAMA_ORGANISASI = $request->nama_penerima;
            $organisasi->save();
        } else {
            return redirect()->back()->with('error', 'Organisasi tidak ditemukan!');
        }

        // Kembalikan ke halaman request dengan pesan sukses
        return redirect()->route('owner.donasi.requests')
                        ->with('success', 'Informasi donasi berhasil diperbarui!');
    }
}
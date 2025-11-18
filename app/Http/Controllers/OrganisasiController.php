<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Donasi;
use App\Models\Barang;
use Illuminate\Support\Facades\Validator;

class OrganisasiController extends Controller
{
    /**
     * Menampilkan dashboard organisasi
     */
    public function dashboard()
    {
        $organisasi = Auth::guard('organisasi')->user();
        $totalDonasi = $organisasi->donasis()->count();
        $donasiTerbaru = $organisasi->donasis()->with('barang')->orderBy('TANGGAL_DONASI', 'desc')->take(5)->get();
        
        return view('organisasi.dashboard', compact('organisasi', 'totalDonasi', 'donasiTerbaru'));
    }

    /**
     * Menampilkan daftar semua donasi organisasi
     */
    public function donasiIndex(Request $request)
    {
        $organisasi = Auth::guard('organisasi')->user();
        
        // Filter berdasarkan pencarian jika ada
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $donasi = $organisasi->donasis()
                                ->with('barang', 'pegawai')
                                ->whereHas('barang', function($query) use ($search) {
                                    $query->where('NAMA_BARANG', 'LIKE', "%{$search}%");
                                })
                                ->orWhere('TANGGAL_DONASI', 'LIKE', "%{$search}%")
                                ->orWhere('ISI_REQUEST', 'LIKE', "%{$search}%")
                                ->paginate(10);
                                
            $donasi->appends(['search' => $search]);
        } else {
            $donasi = $organisasi->donasis()->with('barang', 'pegawai')->paginate(10);
        }
        
        return view('organisasi.donasi.index', compact('donasi'));
    }

    /**
     * Menampilkan detail donasi tertentu
     */
    public function donasiShow($id)
    {
        $organisasi = Auth::guard('organisasi')->user();
        $donasi = $organisasi->donasis()->with('barang', 'pegawai')->findOrFail($id);
        
        return view('organisasi.donasi.show', compact('donasi'));
    }

    /**
     * Menampilkan form untuk membuat request donasi baru
     */
    public function donasiCreate()
    {
        return view('organisasi.donasi.create');
    }

    /**
     * Menyimpan request donasi baru
     */
    public function donasiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'isi_request' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $organisasi = Auth::guard('organisasi')->user();
        
        $donasi = new Donasi();
        $donasi->ID_ORGANISASI = $organisasi->ID_ORGANISASI;
        $donasi->ISI_REQUEST = $request->isi_request;
        $donasi->TANGGAL_DONASI = now(); // Tanggal request dibuat
        $donasi->save();

        return redirect()->route('organisasi.donasi.index')
                        ->with('success', 'Request donasi berhasil dibuat!');
    }

    /**
     * Menampilkan form untuk mengedit request donasi
     */
    public function donasiEdit($id)
    {
        $organisasi = Auth::guard('organisasi')->user();
        $donasi = $organisasi->donasis()->findOrFail($id);
        
        // Hanya boleh edit jika donasi belum memiliki barang (masih dalam status request)
        if ($donasi->ID_BARANG) {
            return redirect()->route('organisasi.donasi.show', $donasi->ID_DONASI)
                            ->with('error', 'Request donasi yang sudah diproses tidak dapat diedit.');
        }
        
        return view('organisasi.donasi.edit', compact('donasi'));
    }

    /**
     * Menyimpan perubahan request donasi
     */
    public function donasiUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'isi_request' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $organisasi = Auth::guard('organisasi')->user();
        $donasi = $organisasi->donasis()->findOrFail($id);
        
        // Hanya boleh update jika donasi belum memiliki barang (masih dalam status request)
        if ($donasi->ID_BARANG) {
            return redirect()->route('organisasi.donasi.show', $donasi->ID_DONASI)
                            ->with('error', 'Request donasi yang sudah diproses tidak dapat diedit.');
        }
        
        $donasi->ISI_REQUEST = $request->isi_request;
        $donasi->save();
        
        return redirect()->route('organisasi.donasi.index')
                        ->with('success', 'Request donasi berhasil diperbarui!');
    }

    /**
     * Menghapus request donasi
     */
    public function donasiDestroy($id)
    {
        $organisasi = Auth::guard('organisasi')->user();
        $donasi = $organisasi->donasis()->findOrFail($id);
        
        // Hanya boleh hapus jika donasi belum memiliki barang (masih dalam status request)
        if ($donasi->ID_BARANG) {
            return redirect()->route('organisasi.donasi.index')
                            ->with('error', 'Request donasi yang sudah diproses tidak dapat dihapus.');
        }
        
        $donasi->delete();
        
        return redirect()->route('organisasi.donasi.index')
                        ->with('success', 'Request donasi berhasil dihapus!');
    }

    /**
     * Menampilkan profil organisasi
     */
    public function profile()
    {
        $organisasi = Auth::guard('organisasi')->user();
        return view('organisasi.profile', compact('organisasi'));
    }

    /**
     * Menampilkan form untuk mengedit profil
     */
    public function editProfile()
    {
        $organisasi = Auth::guard('organisasi')->user();
        return view('organisasi.edit-profile', compact('organisasi'));
    }

    /**
     * Menyimpan perubahan profil
     */
    public function updateProfile(Request $request)
    {
        $organisasi = Auth::guard('organisasi')->user();
        
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:organisasi,EMAIL_ORGANISASI,'.$organisasi->ID_ORGANISASI.',ID_ORGANISASI',
            'alamat' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $organisasi->NAMA_ORGANISASI = $request->nama;
        $organisasi->EMAIL_ORGANISASI = $request->email;
        $organisasi->ALAMAT_ORGANISASI = $request->alamat;
        
        // Update password jika disediakan
        if ($request->filled('password') && $request->password === $request->password_confirmation) {
            $organisasi->PASSWORD_ORGANISASI = bcrypt($request->password);
        }
        
        $organisasi->save();

        return redirect()->route('organisasi.profile')
                        ->with('success', 'Profil berhasil diperbarui!');
    }

    public function editPassword()
{
    $organisasi = auth()->guard('organisasi')->user();
    return view('organisasi.password', compact('organisasi'));
}

public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required'],
        'new_password' => ['required', 'min:6', 'confirmed'],
    ]);

    $organisasi = auth()->guard('organisasi')->user();

    if (!Hash::check($request->current_password, $organisasi->PASSWORD_ORGANISASI)) {
        return back()->with('error', 'Password saat ini salah.');
    }

    $organisasi->PASSWORD_ORGANISASI = Hash::make($request->new_password);
    $organisasi->save();

    return back()->with('success', 'Password berhasil diubah.');
}

}
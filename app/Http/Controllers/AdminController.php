<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisasi;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Menampilkan profil admin
     */
    public function profile()
    {
        $admin = auth()->guard('pegawai')->user();
        return view('admin.profile', compact('admin'));
    }
    
    /**
     * Menampilkan form edit profil
     */
    public function edit()
    {
        $admin = auth()->guard('pegawai')->user();
        return view('admin.edit', compact('admin'));
    }
    
    /**
     * Update profil admin
     */
    public function updateProfile(Request $request)
    {
        $admin = auth()->guard('pegawai')->user();
        
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:pegawai,EMAIL_PEGAWAI,' . $admin->ID_PEGAWAI . ',ID_PEGAWAI',
            'alamat' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:13',
            'tanggal_lahir' => 'nullable|date',
            'password' => 'nullable|string|min:6|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $admin->NAMA_PEGAWAI = $request->nama;
        $admin->EMAIL_PEGAWAI = $request->email;
        $admin->ALAMAT_PEGAWAI = $request->alamat;
        $admin->NO_TELEPON_PEGAWAI = $request->no_telepon;
        
        if ($request->filled('tanggal_lahir')) {
            $admin->TANGGAL_LAHIR = $request->tanggal_lahir;
        }
        
        if ($request->filled('password')) {
            $admin->PASSWORD_PEGAWAI = Hash::make($request->password);
        }
        
        $admin->save();
        
        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui!');
    }
    
    /**
     * Update password admin menjadi tanggal lahir
     */
    public function updatePasswordToDob(Request $request)
    {
        $admin = auth()->guard('pegawai')->user();
        
        // Verifikasi password saat ini
        if ($request->current_password !== $admin->PASSWORD_PEGAWAI) {
            return redirect()->back()
                        ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                        ->withInput();
        }
        
        // Cek jika tanggal lahir tersedia
        if (!$admin->TANGGAL_LAHIR) {
            return redirect()->back()
                        ->with('error', 'Tanggal lahir belum diatur. Silakan perbarui profil Anda terlebih dahulu.')
                        ->withInput();
        }
        
        // Buat password dari tanggal lahir (format: ddmmyyyy)
        $dobPassword = $admin->getDobAsPassword();
        
        // Update password
        $admin->PASSWORD_PEGAWAI = Hash::make($dobPassword);
        $admin->save();
        
        return redirect()->route('admin.profile')
                    ->with('success', 'Password berhasil diubah menjadi tanggal lahir Anda (format: ddmmyyyy).');
    }
    
    /**
     * Menampilkan dashboard admin
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Organisasi Management...

    /**
     * Menampilkan daftar organisasi dengan fitur pencarian
     */
    public function organisasiIndex(Request $request)
    {
        // Jika ada parameter pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $organisasi = Organisasi::where('NAMA_ORGANISASI', 'LIKE', "%{$search}%")
                            ->orWhere('EMAIL_ORGANISASI', 'LIKE', "%{$search}%")
                            ->orWhere('ALAMAT_ORGANISASI', 'LIKE', "%{$search}%")
                            ->paginate(10);
            
            // Jika menggunakan paginate dengan parameter pencarian, kita perlu menyimpan parameter
            $organisasi->appends(['search' => $search]);
        } else {
            // Jika tidak ada pencarian, tampilkan semua data dengan pagination
            $organisasi = Organisasi::paginate(10);
        }
        
        return view('admin.organisasi.index', compact('organisasi'));
    }

    /**
     * Menampilkan detail organisasi
     */
    public function organisasiShow($id)
    {
        $organisasi = Organisasi::findOrFail($id);
        $donasi = $organisasi->donasis; // mendapatkan semua donasi terkait organisasi
        return view('admin.organisasi.show', compact('organisasi', 'donasi'));
    }

    /**
     * Menampilkan form untuk mengedit organisasi
     */
    public function organisasiEdit($id)
    {
        $organisasi = Organisasi::findOrFail($id);
        return view('admin.organisasi.edit', compact('organisasi'));
    }

    /**
     * Menyimpan perubahan organisasi
     */
    public function organisasiUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:organisasi,EMAIL_ORGANISASI,'.$id.',ID_ORGANISASI',
            'alamat' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $organisasi = Organisasi::findOrFail($id);
        $organisasi->NAMA_ORGANISASI = $request->nama;
        $organisasi->EMAIL_ORGANISASI = $request->email;
        $organisasi->ALAMAT_ORGANISASI = $request->alamat;
        
        // Ubah password jika disediakan
        if ($request->filled('password') && $request->password === $request->password_confirmation) {
            $organisasi->PASSWORD_ORGANISASI = Hash::make($request->password);
        }
        
        $organisasi->save();

        return redirect()->route('admin.organisasi.index')
                        ->with('success', 'Organisasi berhasil diperbarui!');
    }

    /**
     * Menghapus organisasi
     */
    public function organisasiDestroy($id)
    {
        $organisasi = Organisasi::findOrFail($id);
        // Cek apakah ada donasi yang terkait sebelum menghapus
        if ($organisasi->donasis()->count() > 0) {
            return redirect()->route('admin.organisasi.index')
                            ->with('error', 'Tidak dapat menghapus organisasi yang memiliki donasi terkait.');
        }
        
        $organisasi->delete();

        return redirect()->route('admin.organisasi.index')
                        ->with('success', 'Organisasi berhasil dihapus!');
    }

    // Pegawai Management

    /**
     * Menampilkan daftar pegawai dengan fitur pencarian
     */
    public function pegawaiIndex(Request $request)
    {
        // Jika ada parameter pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $pegawai = Pegawai::where('NAMA_PEGAWAI', 'LIKE', "%{$search}%")
                            ->orWhere('EMAIL_PEGAWAI', 'LIKE', "%{$search}%")
                            ->orWhere('ALAMAT_PEGAWAI', 'LIKE', "%{$search}%")
                            ->orWhere('NO_TELEPON_PEGAWAI', 'LIKE', "%{$search}%")
                            ->paginate(10);
            
            // Jika menggunakan paginate dengan parameter pencarian, kita perlu menyimpan parameter
            $pegawai->appends(['search' => $search]);
        } else {
            // Jika tidak ada pencarian, tampilkan semua data dengan pagination
            $pegawai = Pegawai::paginate(10);
        }
        
        return view('admin.pegawai.index', compact('pegawai'));
    }

    /**
     * Menampilkan form untuk menambah pegawai baru
     */
    public function pegawaiCreate()
    {
        $roles = Role::all();
        return view('admin.pegawai.create', compact('roles'));
    }

    /**
     * Menyimpan pegawai baru
     */
    public function pegawaiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:pegawai,EMAIL_PEGAWAI',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|exists:role,ID_ROLE',
            'alamat' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:13',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput($request->except('password', 'password_confirmation'));
        }

        $pegawai = new Pegawai();
        $pegawai->NAMA_PEGAWAI = $request->nama;
        $pegawai->EMAIL_PEGAWAI = $request->email;
        $pegawai->PASSWORD_PEGAWAI = Hash::make($request->password);
        $pegawai->ID_ROLE = $request->role;
        $pegawai->ALAMAT_PEGAWAI = $request->alamat;
        $pegawai->NO_TELEPON_PEGAWAI = $request->no_telepon;
        $pegawai->save();

        return redirect()->route('admin.pegawai.index')
                        ->with('success', 'Pegawai berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail pegawai
     */
    public function pegawaiShow($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('admin.pegawai.show', compact('pegawai'));
    }

    /**
     * Menampilkan form untuk mengedit pegawai
     */
    public function pegawaiEdit($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $roles = Role::all();
        return view('admin.pegawai.edit', compact('pegawai', 'roles'));
    }

    /**
     * Menyimpan perubahan pegawai
     */
    public function pegawaiUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:pegawai,EMAIL_PEGAWAI,'.$id.',ID_PEGAWAI',
            'role' => 'required|exists:role,ID_ROLE',
            'alamat' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:13',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $pegawai = Pegawai::findOrFail($id);
        $pegawai->NAMA_PEGAWAI = $request->nama;
        $pegawai->EMAIL_PEGAWAI = $request->email;
        $pegawai->ID_ROLE = $request->role;
        $pegawai->ALAMAT_PEGAWAI = $request->alamat;
        $pegawai->NO_TELEPON_PEGAWAI = $request->no_telepon;
        
        // Ubah password jika disediakan
        if ($request->filled('password') && $request->password === $request->password_confirmation) {
            $pegawai->PASSWORD_PEGAWAI = Hash::make($request->password);
        }
        
        $pegawai->save();

        return redirect()->route('admin.pegawai.index')
                        ->with('success', 'Informasi pegawai berhasil diperbarui!');
    }

    public function pegawaiDestroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        // Jangan hapus pegawai yang sedang login
        if (auth()->guard('pegawai')->user()->ID_PEGAWAI == $id) {
            return redirect()->route('admin.pegawai.index')
                            ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $pegawai->delete();

        return redirect()->route('admin.pegawai.index')
                        ->with('success', 'Pegawai berhasil dihapus!');
    }

    /**
     * Reset password pegawai ke default (password123)
     */
    public function pegawaiResetPassword($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->PASSWORD_PEGAWAI = Hash::make('password123');
        $pegawai->save();

        return redirect()->route('admin.pegawai.index')
                        ->with('success', 'Password pegawai berhasil direset menjadi "password123"');
    }

    public function monitoring()
    {
        return view('admin.monitoring.index');
    }
}
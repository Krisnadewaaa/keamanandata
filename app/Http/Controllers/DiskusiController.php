<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Diskusi;
use App\Models\Barang;
use Illuminate\Support\Facades\Validator;

class DiskusiController extends Controller
{
    /**
     * Menampilkan daftar diskusi untuk barang tertentu
     */
    public function index($id_barang)
    {
        $barang = Barang::findOrFail($id_barang);
        $diskusi = Diskusi::where('ID_BARANG', $id_barang)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        return view('diskusi.index', compact('barang', 'diskusi'));
    }

    /**
     * Menyimpan diskusi baru
     */
    public function store(Request $request, $id_barang)
    {
        $validator = Validator::make($request->all(), [
            'komentar' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $barang = Barang::findOrFail($id_barang);
        
        $diskusi = new Diskusi();
        $diskusi->ID_BARANG = $id_barang;
        
        // Cek jika user yang login adalah pembeli
        if (Auth::guard('pembeli')->check()) {
            $diskusi->ID_PEMBELI = Auth::guard('pembeli')->user()->ID_PEMBELI;
            $diskusi->NAMA_PENGIRIM = Auth::guard('pembeli')->user()->NAMA_PEMBELI;
        } 
        // Cek jika user yang login adalah pegawai (CS)
        elseif (Auth::guard('pegawai')->check()) {
            $diskusi->ID_PEGAWAI = Auth::guard('pegawai')->user()->ID_PEGAWAI;
            $diskusi->NAMA_PENGIRIM = Auth::guard('pegawai')->user()->NAMA_PEGAWAI;
            $diskusi->IS_ADMIN = true;
        } 
        else {
            return redirect()->back()->with('error', 'Anda harus login untuk mengirim komentar.');
        }
        
        $diskusi->KOMENTAR = $request->komentar;
        $diskusi->save();

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Menampilkan form jawaban diskusi (khusus CS)
     */
    public function replyForm($id_diskusi)
    {
        $diskusi = Diskusi::findOrFail($id_diskusi);
        $barang = Barang::findOrFail($diskusi->ID_BARANG);
        
        return view('diskusi.reply', compact('diskusi', 'barang'));
    }

    /**
     * Menyimpan jawaban diskusi (khusus CS)
     */
    public function reply(Request $request, $id_diskusi)
    {
        $validator = Validator::make($request->all(), [
            'komentar' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $parentDiskusi = Diskusi::findOrFail($id_diskusi);
        
        $diskusi = new Diskusi();
        $diskusi->ID_BARANG = $parentDiskusi->ID_BARANG;
        $diskusi->ID_PARENT = $id_diskusi;
        
        // Hanya CS yang bisa menjawab
        if (Auth::guard('pegawai')->check()) {
            $diskusi->ID_PEGAWAI = Auth::guard('pegawai')->user()->ID_PEGAWAI;
            $diskusi->NAMA_PENGIRIM = Auth::guard('pegawai')->user()->NAMA_PEGAWAI;
            $diskusi->IS_ADMIN = true;
        } else {
            return redirect()->back()->with('error', 'Hanya CS yang bisa menjawab pertanyaan.');
        }
        
        $diskusi->KOMENTAR = $request->komentar;
        $diskusi->save();

        return redirect()->route('barang.show', $parentDiskusi->ID_BARANG)
                        ->with('success', 'Jawaban berhasil ditambahkan.');
    }

    /**
     * Menghapus diskusi (khusus CS)
     */
    public function destroy($id)
    {
        $diskusi = Diskusi::findOrFail($id);
        
        // Hanya CS yang bisa menghapus diskusi
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus komentar.');
        }
        
        $id_barang = $diskusi->ID_BARANG;
        $diskusi->delete();
        
        return redirect()->route('barang.show', $id_barang)
                        ->with('success', 'Komentar berhasil dihapus.');
    }
}
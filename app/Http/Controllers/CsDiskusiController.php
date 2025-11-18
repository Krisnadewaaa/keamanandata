<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diskusi;
use App\Models\Barang;
use Illuminate\Support\Facades\Auth;

class CsDiskusiController extends Controller
{
    /**
     * Menampilkan semua diskusi untuk CS
     */
    public function index(Request $request)
    {
        $query = Diskusi::with(['barang', 'pembeli', 'pegawai'])
                      ->whereNull('ID_PARENT'); // Hanya tampilkan parent diskusi
        
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
                  ->orWhere('NAMA_PENGIRIM', 'LIKE', "%{$search}%")
                  ->orWhereHas('barang', function($q) use ($search) {
                      $q->where('NAMA_BARANG', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Urutkan berdasarkan tanggal terbaru
        $query->orderBy('created_at', 'desc');
        
        $diskusi = $query->paginate(10);
        
        return view('cs.diskusi.index', compact('diskusi'));
    }
    
    /**
     * Menampilkan detail diskusi
     */
    public function show($id)
    {
        $diskusi = Diskusi::with(['barang', 'pembeli', 'pegawai', 'replies'])->findOrFail($id);
        
        return view('cs.diskusi.show', compact('diskusi'));
    }
    
    /**
     * Menampilkan form untuk menjawab diskusi
     */
    public function replyForm($id)
    {
        $diskusi = Diskusi::with('barang')->findOrFail($id);
        
        return view('cs.diskusi.reply', compact('diskusi'));
    }
    
    /**
     * Menyimpan jawaban diskusi
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'komentar' => 'required|string|max:255',
        ]);
        
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
    }
    
    /**
     * Menghapus diskusi
     */
    public function destroy($id)
    {
        $diskusi = Diskusi::findOrFail($id);
        
        // Jika diskusi adalah parent, hapus semua reply juga
        if ($diskusi->isParent()) {
            foreach ($diskusi->replies as $reply) {
                $reply->delete();
            }
        }
        
        $diskusi->delete();
        
        return redirect()->route('cs.diskusi.index')
                         ->with('success', 'Diskusi berhasil dihapus.');
    }
}
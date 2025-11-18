<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Barang::where('STATUS', 'Tersedia');
        $categories = KategoriBarang::all();
        
        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category != 'all') {
            $query->where('ID_KATEGORI', $request->category);
        }
        
        // Filter berdasarkan garansi
        if ($request->has('warranty') && $request->warranty == 1) {
            $query->where('GARANSI', 'Ya');
        }
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('NAMA_BARANG', 'LIKE', "%{$request->search}%")
                  ->orWhere('DESKRIPSI', 'LIKE', "%{$request->search}%");
        }
        
        // Pengurutan
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('NAMA_BARANG', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('NAMA_BARANG', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('HARGA', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('HARGA', 'desc');
                    break;
                default:
                    $query->orderBy('ID_BARANG', 'desc');
            }
        } else {
            $query->orderBy('ID_BARANG', 'desc');
        }
        
        $barangs = $query->paginate(12);
        
        return view('barang.index', compact('barangs', 'categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $barang = Barang::findOrFail($id);
        
        // Ambil produk terkait berdasarkan kategori
        $relatedProducts = Barang::where('ID_KATEGORI', $barang->ID_KATEGORI)
                                ->where('ID_BARANG', '!=', $id)
                                ->where('STATUS', 'Tersedia')
                                ->take(4)
                                ->get();
        
        // Load diskusi untuk produk ini
        $barang->load([
            'diskusiParent', 
            'diskusiParent.replies'
        ]);
        
        return view('barang.show', compact('barang', 'relatedProducts'));
    }

    /**
     * Menampilkan status garansi barang
     */
    public function checkWarranty($id)
    {
        $barang = Barang::findOrFail($id);
        
        $warranty = [
            'status' => $barang->GARANSI,
            'garansi_date' => $barang->tanggal_garansi,
            'under_warranty' => false,
        ];
        
        // Cek apakah masih dalam masa garansi
        if ($barang->GARANSI == 'Ya' && $barang->tanggal_garansi) {
            $warrantyDate = \Carbon\Carbon::parse($barang->tanggal_garansi);
            $warranty['under_warranty'] = now()->lt($warrantyDate);
        }
        
        return view('barang.warranty', compact('barang', 'warranty'));
    }

    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'NAMA_BARANG' => 'required|string|max:50',
            'DESKRIPSI' => 'nullable|string|max:255',
            'ID_KATEGORI' => 'nullable|integer',
            'HARGA' => 'nullable|numeric',
            'STATUS' => 'nullable|string|max:10',
            'GARANSI' => 'nullable|string|max:10',
            'tanggal_garansi' => 'nullable|date',
            'stok' => 'nullable|integer',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_produk2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',  // Validasi untuk foto kedua
        ]);

        // Upload foto_produk
        if ($request->hasFile('foto_produk')) {
            $file = $request->file('foto_produk');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/fotoProduk'), $filename);
        } else {
            $filename = 'default.jpg';
        }

        // Upload foto_produk2
        if ($request->hasFile('foto_produk2')) {
            $file2 = $request->file('foto_produk2');
            $filename2 = time() . '_' . $file2->getClientOriginalName();
            $file2->move(public_path('images/fotoProduk2'), $filename2);
        } else {
            $filename2 = 'default.jpg';
        }

        // Simpan data ke database
        Barang::create([
            'NAMA_BARANG' => $request->NAMA_BARANG,
            'DESKRIPSI' => $request->DESKRIPSI,
            'ID_KATEGORI' => $request->ID_KATEGORI,
            'HARGA' => $request->HARGA,
            'STATUS' => $request->STATUS ?? 'Tersedia',
            'GARANSI' => $request->GARANSI,
            'tanggal_garansi' => $request->tanggal_garansi,
            'stok' => $request->stok ?? 0,
            'foto_produk' => $filename,
            'foto_produk2' => $filename2,  // Simpan foto_produk2
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

}
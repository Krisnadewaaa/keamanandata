<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $barangs = Barang::where('STATUS', 'Tersedia')->paginate(12);
        $categories = KategoriBarang::all();

        $penitipsTop = DB::table('transaksi')
            ->join('barang', 'transaksi.ID_BARANG', '=', 'barang.ID_BARANG')
            ->join('penitipan', 'barang.ID_PENITIPAN', '=', 'penitipan.ID_PENITIPAN')
            ->join('penitip', 'penitipan.ID_PENITIP', '=', 'penitip.ID_PENITIP')
            ->whereNotNull('transaksi.RATING_PENITIP')
            ->select(
                'penitip.ID_PENITIP',
                'penitip.NAMA_PENITIP',
                'penitip.FOTO_PROFIL',
                DB::raw('AVG(transaksi.RATING_PENITIP) as avg_rating')
            )
            ->groupBy('penitip.ID_PENITIP', 'penitip.NAMA_PENITIP', 'penitip.FOTO_PROFIL')
            ->orderByDesc('avg_rating')
            ->limit(3)
            ->get();

        return view('home.index', compact('barangs', 'categories', 'penitipsTop'));
    }


    public function about()
    {
        return view('home.about');
    }

    public function contact()
    {
        return view('home.contact');
    }
}

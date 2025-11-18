<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use App\Models\Transaksi;

class PublikController extends Controller
{
    public function lihatRatingPenitip($id)
{
    $penitip = Penitip::select('ID_PENITIP', 'NAMA_PENITIP', 'FOTO_PROFIL')
        ->where('ID_PENITIP', $id)
        ->firstOrFail();

    // Hitung rata-rata rating dari transaksi yang punya penitipan dengan ID_PENITIP ini
    $avgRating = Transaksi::whereHas('penitipan', function ($q) use ($id) {
            $q->where('ID_PENITIP', $id);
        })
        ->whereNotNull('RATING_PENITIP')
        ->avg('RATING_PENITIP');

    // Update rating di tabel penitip
    $penitip->RATING_PENITIP = $avgRating ? round($avgRating, 2) : 0;
    $penitip->save();

    return view('home.rating-penitip', compact('penitip', 'avgRating'));
}

}

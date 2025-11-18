<?php

namespace App\Http\Controllers\cs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembeli;
use App\Models\Merchandise;
use App\Models\PointReward;

class PointRewardController extends Controller
{ 
    public function showTukarPoin()
    {
        $pembeli = auth()->user();

        // Ambil merchandise yang point-nya <= point pembeli
        $merchandises = Merchandise::all();

        return view('cs.merch.tukarPoin', compact('pembeli', 'merchandises'));
    }

    public function index()
    {
        // Ambil semua klaim beserta relasi pembeli dan merchandise
        $semuaKlaim = PointReward::with(['pembeli', 'merchandise'])->get();

        // Klaim yang belum diambil (TANGGAL_AMBIL masih null)
        $klaimBelumDiambil = PointReward::with(['pembeli', 'merchandise'])
            ->whereNull('TANGGAL_AMBIL')
            ->get();

        return view('cs.merch.klaimMerch', compact('semuaKlaim', 'klaimBelumDiambil'));
    }

    public function update($id)
    {
        $klaim = PointReward::findOrFail($id);

        if ($klaim->TANGGAL_AMBIL) {
            return redirect()->route('cs.klaim-merchandise.index')
                            ->with('info', 'Klaim sudah ditandai diambil sebelumnya.');
        }

        $klaim->TANGGAL_AMBIL = now();
        $klaim->STATUS_KLAIM = 'Sudah Diambil'; // opsional, bisa dihapus juga
        $klaim->save();

        return redirect()->route('cs.klaim-merchandise.index')
                        ->with('success', 'Klaim merchandise berhasil ditandai sudah diambil.');
    }

    public function tukarPoin(Request $request)
    {
        $request->validate([
            'hadiah' => 'required|string',
        ]);

        $pembeli = auth()->user();

        // Format hadiah: ID_MERCHANDISE|POIN
        list($idMerchandise, $poinHadiah) = explode('|', $request->hadiah);
        $idMerchandise = (int) $idMerchandise;
        $poinHadiah = (int) $poinHadiah;

        // Validasi apakah pembeli memiliki cukup poin
        if ($pembeli->POINT_PEMBELI < $poinHadiah) {
            return back()->withErrors('Point Anda tidak cukup untuk menukarkan hadiah ini.');
        }

        // Kurangi poin pembeli
        $pembeli->POINT_PEMBELI -= $poinHadiah;
        $pembeli->save();

        // Simpan klaim ke tabel point_rewards
        PointReward::create([
            'ID_PEMBELI' => $pembeli->ID_PEMBELI,
            'ID_MERCHANDISE' => $idMerchandise,
            'JUMLAH_POINT' => $poinHadiah,
            'TANGGAL_AMBIL' => null,
            'STATUS_KLAIM' => 'Belum Diambil', // bisa juga 'Belum Diambil' kalau mau default seperti field bawaan
        ]);

        return redirect()->back()->with('success', 'Klaim hadiah berhasil. Poin Anda telah dikurangi.');
    }

public function klaimBulanJuni()
{
    $klaimJuni = PointReward::with(['pembeli', 'merchandise'])
        ->whereMonth('TANGGAL_AMBIL', 6)
        ->whereYear('TANGGAL_AMBIL', 2025)
        ->get();

    return view('cs.merch.klaimJuni', compact('klaimJuni'));
}


}

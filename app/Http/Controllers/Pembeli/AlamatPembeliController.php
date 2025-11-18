<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlamatPembeli;
use Illuminate\Support\Facades\Auth;

class AlamatPembeliController extends Controller
{
    public function index(Request $request)
    {
        $pembeli = Auth::guard('pembeli')->user();
        $query = AlamatPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI);

        if ($request->filled('kota')) {
            $query->where('KOTA', 'like', '%' . $request->kota . '%');
        }

        if ($request->filled('provinsi')) {
            $query->where('PROVINSI', 'like', '%' . $request->provinsi . '%');
        }

        $alamat = $query->get();

        return view('pembeli.alamat.index', compact('alamat', 'pembeli'));
    }


    public function create()
    {
        $pembeli = Auth::guard('pembeli')->user();
        return view('pembeli.alamat.create', compact('pembeli'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ALAMAT_LENGKAP' => 'required',
            'KOTA' => 'required',
            'PROVINSI' => 'required',
        ]);

        $data = $request->only(['ALAMAT_LENGKAP', 'KOTA', 'PROVINSI']);
        $data['ID_PEMBELI'] = Auth::guard('pembeli')->id();
        $data['IS_DEFAULT'] = AlamatPembeli::where('ID_PEMBELI', $data['ID_PEMBELI'])->count() === 0;

        AlamatPembeli::create($data);

        return redirect()->route('pembeli.alamat.index')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pembeli = Auth::guard('pembeli')->user();
        $alamat = AlamatPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                ->where('ID_ALAMAT', $id)
                                ->firstOrFail();

        // Ambil data Provinsi & Kota (Kita tidak akan mengambil dari API saat edit, melainkan dari database)
        $provinsiList = AlamatPembeli::distinct()->pluck('PROVINSI');
        $kotaList = AlamatPembeli::distinct()->pluck('KOTA');

        return view('pembeli.alamat.edit', compact('alamat', 'provinsiList', 'kotaList', 'pembeli'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ALAMAT_LENGKAP' => 'required',
            'KOTA' => 'required',
            'PROVINSI' => 'required',
        ]);

        $pembeli = Auth::guard('pembeli')->user();
        $alamat = AlamatPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                ->where('ID_ALAMAT', $id)
                                ->firstOrFail();

        $alamat->update($request->only(['ALAMAT_LENGKAP', 'KOTA', 'PROVINSI', 'KODE_POS']));

        return redirect()->route('pembeli.alamat.index')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pembeli = Auth::guard('pembeli')->user();
        $alamat = AlamatPembeli::where('ID_PEMBELI', $pembeli->ID_PEMBELI)
                                ->where('ID_ALAMAT', $id)
                                ->firstOrFail();

        $alamat->delete();

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefault($id)
    {
        $pembeli = Auth::guard('pembeli')->user();
        $pembeliId = $pembeli->ID_PEMBELI;

        AlamatPembeli::where('ID_PEMBELI', $pembeliId)->update(['IS_DEFAULT' => false]);
        AlamatPembeli::where('ID_PEMBELI', $pembeliId)
                     ->where('ID_ALAMAT', $id)
                     ->update(['IS_DEFAULT' => true]);

        return back()->with('success', 'Alamat utama berhasil diubah.');
    }
}
<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Merchandise;

class MerchandiseController extends Controller
{
    public function index()
    {
        $merchandiseList = Merchandise::all();
        return view('cs.merch.daftarMerch', compact('merchandiseList'));
    }
}

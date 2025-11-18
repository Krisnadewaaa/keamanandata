<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetFormController extends Controller
{
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        $type = $request->query('type');

        return view('auth.passwords.reset-new', compact('token', 'email', 'type'));
    }

    public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'token' => 'required',
        'type' => 'required|in:pembeli,penitip,organisasi',
        'password' => 'required|min:6|confirmed',
    ]);

    $tokenData = DB::table('password_resets')->where([
        'email' => $request->email,
        'token' => $request->token,
    ])->first();

    if (!$tokenData) {
        return back()->with('error', 'Token tidak valid atau sudah kadaluarsa.');
    }

    $modelClass = match($request->type) {
        'pembeli' => \App\Models\Pembeli::class,
        'penitip' => \App\Models\Penitip::class,
        'organisasi' => \App\Models\Organisasi::class,
    };

    $fieldEmail = match($request->type) {
        'pembeli' => 'EMAIL_PEMBELI',
        'penitip' => 'EMAIL_PENITIP',
        'organisasi' => 'EMAIL_ORGANISASI',
    };

    $fieldPassword = match($request->type) {
        'pembeli' => 'PASSWORD_PEMBELI',
        'penitip' => 'PASSWORD_PENITIP',
        'organisasi' => 'PASSWORD_ORGANISASI',
    };

    $user = $modelClass::where($fieldEmail, $request->email)->first();

    if (!$user) {
        return back()->with('error', 'Pengguna tidak ditemukan.');
    }

    $user->$fieldPassword = Hash::make($request->password);
    $user->save();

    DB::table('password_resets')->where('email', $request->email)->delete();

    return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
}
}

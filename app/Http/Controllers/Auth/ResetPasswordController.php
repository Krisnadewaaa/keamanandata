<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\Organisasi;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password-form', [
            'token' => $request->token,
            'email' => $request->email,
            'type' => $request->type,
        ]);
    }



    public function submitReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
            'type' => 'required|in:pembeli,penitip,organisasi',
        ]);

        // Cek token valid
        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset || Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['token' => 'Token tidak valid atau sudah kadaluarsa.']);
        }

        $map = [
            'pembeli' => ['model' => Pembeli::class, 'column' => 'EMAIL_PEMBELI', 'password' => 'PASSWORD_PEMBELI'],
            'penitip' => ['model' => Penitip::class, 'column' => 'EMAIL_PENITIP', 'password' => 'PASSWORD_PENITIP'],
            'organisasi' => ['model' => Organisasi::class, 'column' => 'EMAIL_ORGANISASI', 'password' => 'PASSWORD_ORGANISASI'],
        ];

        $config = $map[$request->type];

        $user = $config['model']::where($config['column'], $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Pengguna tidak ditemukan.']);
        }

        // Simpan password baru
        $user->{$config['password']} = Hash::make($request->password);
        $user->save();

        // Hapus token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
    }
}

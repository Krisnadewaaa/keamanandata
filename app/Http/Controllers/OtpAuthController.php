<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtpVerification;
use App\Models\Pembeli;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // ← tambahkan ini
use App\Mail\OtpMail;

class OtpAuthController extends Controller
{
    public function showVerifyForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required',
        ]);

        // Cari OTP yang valid
        $otpRecord = OtpVerification::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('is_used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kadaluarsa.']);
        }

        // Tandai OTP sudah digunakan
        $otpRecord->update(['is_used' => true]);

        // Ambil data pendaftaran dari session
        $data = session('register_pembeli');

        if (!$data) {
            return redirect()->route('register.pembeli')
                ->withErrors(['register' => 'Data pendaftaran tidak ditemukan. Silakan daftar ulang.']);
        }

        // Buat akun pembeli
        $pembeli = new Pembeli();
        $pembeli->NAMA_PEMBELI     = $data['NAMA_PEMBELI'];
        $pembeli->EMAIL_PEMBELI    = $data['EMAIL_PEMBELI'];
        $pembeli->PASSWORD_PEMBELI = $data['PASSWORD_PEMBELI'];
        $pembeli->POINT_PEMBELI    = $data['POINT_PEMBELI'] ?? 0;
        $pembeli->save();

        // Hapus session
        session()->forget(['register_pembeli', 'otp_email']);

        // Login otomatis
        Auth::guard('pembeli')->login($pembeli);

        return redirect()->route('pembeli.dashboard')
            ->with('success', 'Registrasi berhasil! Akun Anda telah terverifikasi.');
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $otp = rand(100000, 999999);

        OtpVerification::create([
            'email'      => $request->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_used'    => false,
        ]);

        Mail::to($request->email)->send(new OtpMail($otp));

        return back()->with('success', 'OTP baru telah dikirim ke email Anda.');
    }
}

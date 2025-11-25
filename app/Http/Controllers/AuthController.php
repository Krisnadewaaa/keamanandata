<?php

namespace App\Http\Controllers;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Models\Pegawai;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'user_type' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput($request->except('password'));
        }

        $email = $request->email;
        $password = $request->password;
        $userType = $request->user_type;

        if ($userType === 'pembeli') {
            $user = Pembeli::where('EMAIL_PEMBELI', $email)->first();
            if ($user && ($user->PASSWORD_PEMBELI === $password || Hash::check($password, $user->PASSWORD_PEMBELI))) {
                Auth::guard('pembeli')->login($user);
                return redirect()->route('pembeli.dashboard');
            }
        } elseif ($userType === 'penitip') {
            $user = Penitip::where('EMAIL_PENITIP', $email)->first();
            if ($user && ($user->PASSWORD_PENITIP === $password || Hash::check($password, $user->PASSWORD_PENITIP))) {
                Auth::guard('penitip')->login($user);
                return redirect()->route('penitip.dashboard');
            }
        } elseif ($userType === 'organisasi') {
            $user = Organisasi::where('EMAIL_ORGANISASI', $email)->first();
            if ($user && ($user->PASSWORD_ORGANISASI === $password || Hash::check($password, $user->PASSWORD_ORGANISASI))) {
                Auth::guard('organisasi')->login($user);
                return redirect()->route('organisasi.dashboard');
            }
        } else {
            $pegawaiRoles = [
                'owner' => 1,
                'admin' => 2,
                'gudang' => 3,
                'customerservice' => 4,
                'kurir' => 5,
                'hunter' => 6,
            ];

            if (array_key_exists($userType, $pegawaiRoles)) {
                $user = Pegawai::where('EMAIL_PEGAWAI', $email)->first();
                if ($user && ($user->PASSWORD_PEGAWAI === $password || Hash::check($password, $user->PASSWORD_PEGAWAI))) {
                    if ($user->ID_ROLE == $pegawaiRoles[$userType]) {
                        Auth::guard('pegawai')->login($user);
                        return match ($userType) {
                            'owner' => redirect()->route('owner.dashboard'),
                            'admin' => redirect()->route('admin.dashboard'),
                            'gudang' => redirect()->route('gudang.dashboard'),
                            'customerservice' => redirect()->route('cs.dashboard'),
                            'kurir' => redirect()->route('kurir.dashboard'),
                            'hunter' => redirect()->route('hunter.dashboard'),
                        };
                    } else {
                        return back()->with('error', 'Role Anda tidak sesuai dengan pilihan login.');
                    }
                }
            }
        }

        return redirect()->back()
                        ->with('error', 'Email atau password salah.')
                        ->withInput($request->except('password'));
    }

    public function showRegisterPembeliForm()
    {
        return view('auth.register-pembeli');
    }

    public function registerPembeli(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nama'     => 'required|string|max:50',
        'email'    => 'required|email|max:50|unique:pembeli,EMAIL_PEMBELI',
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/[A-Z]/',
            'regex:/[a-z]/',
            'regex:/[0-9]/',
        ],
    ]);

    if ($validator->fails()) {
        return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($request->except('password', 'password_confirmation'));
    }

    // Simpan data ke session dulu, belum buat akun
    $registerData = [
        'NAMA_PEMBELI'     => $request->nama,
        'EMAIL_PEMBELI'    => $request->email,
        'PASSWORD_PEMBELI' => Hash::make($request->password),
        'POINT_PEMBELI'    => 0,
    ];

    session(['register_pembeli' => $registerData]);
    session(['otp_email' => $request->email]);

    // Generate & kirim OTP (kode sebelumnya yang sudah kita bahas)
    $otp = rand(100000, 999999);

    OtpVerification::create([
        'email'      => $request->email,
        'otp'        => $otp,
        'expires_at' => now()->addMinutes(10),
        'is_used'    => false,
    ]);

    Mail::to($request->email)->send(new OtpMail($otp));

    return redirect()
        ->route('otp.verify.form')
        ->with('success', 'Kode OTP telah dikirim ke email Anda. Silakan cek email untuk verifikasi.');
}

    public function showRegisterPenitipForm()
    {
        return view('auth.register-penitip');
    }

    public function registerPenitip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:penitip,EMAIL_PENITIP',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput($request->except('password', 'password_confirmation'));
        }

        $penitip = new Penitip();
        $penitip->NAMA_PENITIP = $request->nama;
        $penitip->EMAIL_PENITIP = $request->email;
        $penitip->PASSWORD_PENITIP = Hash::make($request->password);
        $penitip->RATING_PENITIP = 0.0;
        $penitip->UANG_PENITIP = 0;
        $penitip->save();

        Auth::guard('penitip')->login($penitip);

        return redirect()->route('penitip.profile')
                        ->with('success', 'Pendaftaran berhasil! Selamat datang di ReUseMart.');
    }

    public function showRegisterOrganisasiForm()
    {
        return view('auth.register-organisasi');
    }

    public function registerOrganisasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:organisasi,EMAIL_ORGANISASI',
            'password' => 'required|string|min:6|confirmed',
            'alamat' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput($request->except('password', 'password_confirmation'));
        }

        $organisasi = new Organisasi();
        $organisasi->NAMA_ORGANISASI = $request->nama;
        $organisasi->EMAIL_ORGANISASI = $request->email;
        $organisasi->PASSWORD_ORGANISASI = Hash::make($request->password);
        $organisasi->ALAMAT_ORGANISASI = $request->alamat;
        $organisasi->save();

        Auth::guard('organisasi')->login($organisasi);

        return redirect()->route('organisasi.dashboard')
                        ->with('success', 'Pendaftaran berhasil! Selamat datang di ReUseMart.');
    }

    public function logout(Request $request)
    {
        Auth::guard('pembeli')->logout();
        Auth::guard('penitip')->logout();
        Auth::guard('pegawai')->logout();
        Auth::guard('organisasi')->logout();

        Session::flush();

        return redirect()->route('home');
    }

    public function showPasswordResetForm()
    {
        return view('auth.reset-password');
    }

    public function sendResetLink(Request $request)

    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'user_type' => 'required|in:pembeli,penitip,organisasi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $email = $request->email;
        $userType = $request->user_type;

        $user = match ($userType) {
            'pembeli' => Pembeli::where('EMAIL_PEMBELI', $email)->first(),
            'penitip' => Penitip::where('EMAIL_PENITIP', $email)->first(),
            'organisasi' => Organisasi::where('EMAIL_ORGANISASI', $email)->first(),
            default => null,
        };

        if (!$user) {
            return redirect()->back()
                            ->with('error', 'Email tidak ditemukan.')
                            ->withInput();
        }

        // Simulasi pengiriman email
        return redirect()->back()
                        ->with('success', 'Link reset password telah dikirim ke email Anda.');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Ganti dengan guard yang sesuai (pegawai, pembeli, dll.)
        $user = auth()->user();

        // Verifikasi password saat ini
        if (!Hash::check($request->current_password, $user->PASSWORD_PEGAWAI)) {
            return redirect()->back()->with('error', 'Password saat ini salah.');
        }

        // Update password
        $user->PASSWORD_PEGAWAI = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }

    public function showResetForm(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'type' => 'required|in:pembeli,penitip,organisasi',
    ]);

    return view('auth.passwords.reset-multi', [
        'token' => $request->token,
        'email' => $request->email,
        'type' => $request->type,
    ]);
}

public function updatePassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'type' => 'required|in:pembeli,penitip,organisasi',
        'password' => 'required|confirmed|min:8',
    ]);

    $status = Password::broker($request->type)->reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('success', 'Password berhasil direset.')
        : back()->withErrors(['email' => __($status)]);
}

}
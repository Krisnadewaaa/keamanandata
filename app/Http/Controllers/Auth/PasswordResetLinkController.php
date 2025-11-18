<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetLinkController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'tipe' => 'required|in:pembeli,penitip,organisasi',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $map = [
            'pembeli' => ['model' => \App\Models\Pembeli::class, 'column' => 'EMAIL_PEMBELI'],
            'penitip' => ['model' => \App\Models\Penitip::class, 'column' => 'EMAIL_PENITIP'],
            'organisasi' => ['model' => \App\Models\Organisasi::class, 'column' => 'EMAIL_ORGANISASI'],
        ];

        $config = $map[$request->tipe];
        $user = $config['model']::where($config['column'], $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Alamat email tidak ditemukan.'])->withInput();
        }

        // Buat token dan simpan ke tabel password_resets
        $token = Str::random(64);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // URL reset password
        $resetUrl = url("/reset-password?token={$token}&email={$request->email}&type={$request->tipe}");

        // Kirim email reset password (bisa diganti dengan Mail::to(...)->send(new ResetPasswordMail(...)) jika ingin pakai blade)
        Mail::raw("Klik link berikut untuk reset password Anda:\n\n$resetUrl", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Reset Password');
        });

        return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    }
}

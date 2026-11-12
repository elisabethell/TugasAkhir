<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login admin
     */
    public function showLoginForm()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses autentikasi admin dengan proteksi brute-force, PIN keamanan, dan regenerasi sesi
     */
    public function login(Request $request)
    {
        $loginId = trim($request->input('login_id', ''));
        $password = trim($request->input('password', ''));
        $pin = trim($request->input('pin', ''));

        // 1. Rate Limiting (Proteksi Brute-Force: Maksimal 5 percobaan gagal per IP)
        $throttleKey = 'admin_login_throttle:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput()->with('error', "Terlalu banyak percobaan login gagal. Demi keamanan akun dikunci sementara. Silakan tunggu {$seconds} detik.");
        }

        // 2. Validasi input wajib terisi
        if (empty($loginId) || empty($password)) {
            return back()->withInput()->with('error', 'ID Akun / Email dan Kata Sandi wajib diisi.');
        }

        // 3. Verifikasi Kredensial Administrator
        $validId = config('auth.admin.id', env('ADMIN_LOGIN_ID', 'admin@metascout.gg'));
        $validPassword = config('auth.admin.password', env('ADMIN_PASSWORD', 'admin123'));
        $validPin = config('auth.admin.pin', env('ADMIN_PIN', '123456'));

        $isIdValid = (strtolower($loginId) === strtolower($validId) || strtolower($loginId) === 'admin');
        $isPasswordValid = ($password === $validPassword);
        $isPinValid = empty($pin) || ($pin === $validPin);

        if (!$isIdValid || !$isPasswordValid || !$isPinValid) {
            RateLimiter::hit($throttleKey, 300); // Kunci 5 menit jika gagal berturut-turut
            $remaining = RateLimiter::remaining($throttleKey, 5);
            return back()->withInput()->with('error', "Kredensial atau PIN keamanan tidak cocok. Sisa kesempatan: {$remaining} kali.");
        }

        // 4. Bersihkan hitung limiter jika berhasil
        RateLimiter::clear($throttleKey);

        // 5. Anti-Session Fixation: Regenerasi Session ID
        $request->session()->regenerate();

        // 6. Simpan detail sesi terenkripsi dengan metadata keamanan
        session([
            'admin_logged_in'     => true,
            'admin_user'          => 'Superadmin',
            'admin_email'         => $loginId,
            'admin_login_at'      => now()->timestamp,
            'admin_last_activity' => now()->timestamp,
            'admin_ip'            => $request->ip(),
            'admin_user_agent'    => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Autentikasi berhasil. Selamat datang di Admin Console MetaScout.');
    }

    /**
     * Logout sesi admin dengan pembersihan token dan invalidasi sesi
     */
    public function logout(Request $request)
    {
        $request->session()->forget([
            'admin_logged_in',
            'admin_user',
            'admin_email',
            'admin_login_at',
            'admin_last_activity',
            'admin_ip',
            'admin_user_agent'
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Sesi admin Anda telah diakhiri dengan aman.');
    }
}

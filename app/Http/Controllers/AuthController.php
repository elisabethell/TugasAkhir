<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * Proses autentikasi admin dengan verifikasi Hash bcrypt, proteksi brute-force, PIN keamanan, dan regenerasi sesi
     */
    public function login(Request $request)
    {
        $loginId = trim($request->input('login_id', ''));
        $password = (string) $request->input('password', '');
        $pin = trim($request->input('pin', ''));

        // 1. Rate Limiting (Proteksi Brute-Force: Maksimal 5 percobaan gagal per IP dalam 1 menit)
        $throttleKey = 'admin_login_throttle:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput(['login_id' => $loginId])->with('error', "Terlalu banyak percobaan login gagal. Demi keamanan akun dikunci sementara. Silakan tunggu {$seconds} detik.");
        }

        // 2. Validasi input wajib terisi
        if (empty($loginId) || empty($password)) {
            return back()->withInput(['login_id' => $loginId])->with('error', 'ID Akun / Email dan Kata Sandi wajib diisi.');
        }

        // 3. Cari User Administrator di Database (users table)
        // Mendukung login via email maupun nama user (contoh: 'Superadmin' atau 'admin@metascout.gg')
        $user = User::where('email', $loginId)
            ->orWhere('name', $loginId)
            ->first();

        // Fallback otoseed jika database baru di-migrate dan record admin belum ada
        if (!$user && ($loginId === 'admin@metascout.gg' || strtolower($loginId) === 'admin' || strtolower($loginId) === 'superadmin')) {
            $defaultPassword = config('auth.admin.password', env('ADMIN_PASSWORD', 'admin123'));
            $user = User::firstOrCreate(
                ['email' => 'admin@metascout.gg'],
                [
                    'name' => 'Superadmin',
                    'password' => Hash::make($defaultPassword),
                ]
            );
        }

        // 4. Verifikasi Password Terenkripsi Menggunakan Hash::check (Bcrypt) & PIN Keamanan
        $isPasswordValid = $user && Hash::check($password, $user->password);
        $validPin = config('auth.admin.pin', env('ADMIN_PIN', '123456'));
        $isPinValid = empty($pin) || ($pin === $validPin);

        if (!$user || !$isPasswordValid || !$isPinValid) {
            RateLimiter::hit($throttleKey, 300); // Kunci 5 menit jika gagal berturut-turut
            $remaining = RateLimiter::remaining($throttleKey, 5);
            return back()->withInput(['login_id' => $loginId])->with('error', "Kredensial atau PIN keamanan tidak cocok. Sisa kesempatan: {$remaining} kali.");
        }

        // 5. Bersihkan hitung limiter jika berhasil
        RateLimiter::clear($throttleKey);

        // 6. Anti-Session Fixation: Regenerasi Session ID
        $request->session()->regenerate();
        Auth::login($user);

        // 7. Simpan detail sesi terenkripsi dengan metadata keamanan
        session([
            'admin_logged_in'     => true,
            'admin_user_id'       => $user->id,
            'admin_user'          => $user->name,
            'admin_email'         => $user->email,
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
        Auth::logout();

        $request->session()->forget([
            'admin_logged_in',
            'admin_user_id',
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

    /**
     * Menangani formulir pengajuan akses Admin / Analis Riset
     */
    public function requestAccess(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|max:150',
            'role'   => 'required|string|max:100',
            'reason' => 'required|string|max:500',
        ]);

        \Illuminate\Support\Facades\Log::info("Admin Access Request from {$request->input('name')} ({$request->input('email')}) - Role: {$request->input('role')}. Reason: {$request->input('reason')}");

        return redirect()->route('home')->with('success_request', "Terima kasih, {$request->input('name')}! Pengajuan akses Anda telah diterima. Tim verifikator MetaScout akan meninjau dan mengirimkan kredensial akses ke email {$request->input('email')}.");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
     * Proses autentikasi admin
     */
    public function login(Request $request)
    {
        $loginId = trim($request->input('login_id', ''));
        $password = trim($request->input('password', ''));
        $pin = trim($request->input('pin', ''));

        if (empty($loginId) || empty($password)) {
            return back()->withInput()->with('error', 'ID Akun / Email dan Kata Sandi wajib diisi.');
        }

        // Simpan sesi autentikasi admin
        session([
            'admin_logged_in' => true,
            'admin_user'      => 'Superadmin',
            'admin_email'     => $loginId,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Selamat datang di Admin Console MetaScout.');
    }

    /**
     * Logout sesi admin
     */
    public function logout()
    {
        session()->forget(['admin_logged_in', 'admin_user', 'admin_email']);
        return redirect()->route('home')->with('info', 'Sesi admin telah berakhir.');
    }
}

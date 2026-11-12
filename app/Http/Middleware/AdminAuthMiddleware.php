<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Timeout sesi inaktif (dalam detik): 30 Menit (1800 detik)
     */
    protected int $inactivityTimeout = 1800;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah admin sudah terotentikasi di sesi
        if (!$request->session()->has('admin_logged_in') || !$request->session()->get('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Akses terbatas. Harap login terlebih dahulu untuk mengakses Admin Console.');
        }

        // 2. Cek Anti-Session Hijacking (Proteksi konsistensi IP Address)
        $sessionIp = $request->session()->get('admin_ip');
        if ($sessionIp && $sessionIp !== $request->ip()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Terdeteksi perubahan alamat IP pada sesi login Anda. Sesi dihentikan demi keamanan.');
        }

        // 3. Cek Inactivity Timeout (Kedaluwarsa otomatis jika tidak ada aktivitas selama 30 menit)
        $lastActivity = $request->session()->get('admin_last_activity');
        if ($lastActivity && (now()->timestamp - $lastActivity) > $this->inactivityTimeout) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Sesi admin telah kedaluwarsa karena tidak ada aktivitas selama 30 menit. Silakan login kembali.');
        }

        // 4. Perbarui waktu aktivitas terakhir
        $request->session()->put('admin_last_activity', now()->timestamp);

        return $next($request);
    }
}


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - MetaScout: Land of Dawn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #FAF8F8;
            color: #18181B;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between">

    <!-- 1. TOP HEADER -->
    <header class="bg-[#F8EEF0] border-b border-[#F3E8E8] px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="w-3 h-3 rounded-full bg-[#700B1A] inline-block shadow-[0_0_8px_rgba(112,11,26,0.5)]"></span>
            <span class="font-black text-sm tracking-widest text-[#18181B] uppercase">METASCOUT</span>
            <span class="bg-white border border-[#E5E7EB] text-gray-700 text-[10px] font-extrabold px-2 py-0.5 rounded uppercase tracking-wider">
                ADMIN
            </span>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="text-xs font-bold text-gray-700 hover:text-[#700B1A] flex items-center gap-1.5 transition">
                <span>←</span>
                <span>Kembali ke Beranda</span>
            </a>
            <div class="w-7 h-7 rounded-full bg-[#521018] text-white flex items-center justify-center font-black text-xs shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
        </div>
    </header>

    <!-- 2. LOGIN CARD -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-[440px] bg-white border border-[#F3E8E8] rounded-3xl p-7 sm:p-9 shadow-sm space-y-5">
            
            <!-- Shield Icon -->
            <div class="w-12 h-12 rounded-2xl bg-[#FCECEE] text-[#700B1A] flex items-center justify-center mx-auto shadow-2xs">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12 1.5a.75.75 0 01.447.149l7.5 5.25a.75.75 0 01.303.601v6.5c0 5.093-3.793 9.49-8.083 10.457a.75.75 0 01-.334 0C7.543 23.49 3.75 19.093 3.75 14V7.5a.75.75 0 01.303-.601l7.5-5.25A.75.75 0 0112 1.5zm0 6a2.25 2.25 0 00-2.25 2.25v.75H9.5a1 1 0 00-1 1v4a1 1 0 001 1h5a1 1 0 001-1v-4a1 1 0 00-1-1h-.25v-.75A2.25 2.25 0 0012 7.5zm1 3v-.75a1 1 0 10-2 0v.75h2z" clip-rule="evenodd" />
                </svg>
            </div>

            <!-- Title -->
            <div class="text-center">
                <h1 class="text-2xl font-black text-[#18181B] tracking-tight">Login Admin</h1>
            </div>

            <!-- Flash Error -->
            @if(session('error'))
                <div class="bg-[#FCECEE] border border-[#F8B4BD] text-[#700B1A] text-xs font-bold rounded-xl px-3.5 py-2.5 flex items-center gap-2">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="bg-gray-100 border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl px-3.5 py-2.5">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- ID Akun / Email -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">ID Akun / Email Terdaftar</label>
                    <div class="relative flex items-center">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                        </span>
                        <input type="text" 
                               name="login_id" 
                               value="{{ old('login_id', 'admin@metascout.gg') }}" 
                               required
                               placeholder="admin@metascout.gg atau analyst_id" 
                               class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2.5 pl-10 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] transition">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Kata Sandi (Password)</label>
                    <div class="relative flex items-center">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" 
                               id="passwordInput"
                               name="password" 
                               value="admin123"
                               required
                               placeholder="••••••••••••" 
                               class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2.5 pl-10 pr-10 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] transition">
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Security PIN -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Security PIN</label>
                    <div class="relative flex items-center">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <input type="text" 
                               name="pin" 
                               value="123456"
                               placeholder="Masukkan 6 digit kode authenticator" 
                               class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2.5 pl-10 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] transition">
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between text-[11px] pt-1">
                    <label class="flex items-center gap-2 cursor-pointer font-medium text-gray-600">
                        <input type="checkbox" name="remember" checked class="accent-[#700B1A] rounded">
                        <span>Ingat sesi perangkat ini (30 hari)</span>
                    </label>
                    <a href="#" class="font-extrabold text-[#700B1A] hover:underline">
                        Lupa Kredensial?
                    </a>
                </div>

                <!-- Help Link -->
                <p class="text-[10px] text-center text-gray-500 pt-0.5">
                    Butuh bantuan? <a href="#" class="font-bold text-[#700B1A] hover:underline">Hubungi Koordinator Data</a>
                </p>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-[#521018] hover:bg-[#3D0A11] text-white font-extrabold text-xs py-3 rounded-xl flex items-center justify-center gap-2 shadow-sm transition tracking-wider">
                    <span>Masuk ke Admin Console</span>
                    <span class="text-sm">→</span>
                </button>
            </form>

        </div>
    </main>

    <!-- 3. FOOTER -->
    <footer class="py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest border-t border-[#F3E8E8]">
        METASCOUT : LAND OF DAWN • ADMIN PORTAL
    </footer>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('passwordInput');
            if (pwd.type === 'password') {
                pwd.type = 'text';
            } else {
                pwd.type = 'password';
            }
        }
    </script>
</body>
</html>

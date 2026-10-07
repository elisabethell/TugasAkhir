<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaScout: Land of Dawn - Portal Analisis & Rekomendasi Draft MLBB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #FAF8F8;
            color: #18181B;
            -webkit-font-smoothing: antialiased;
        }
        .text-maroon { color: #6B0F1A; }
        .bg-maroon { background-color: #700B1A; }
        .bg-maroon-hover:hover { background-color: #550713; }
        .border-maroon { border-color: #700B1A; }
        .bg-maroon-subtle { background-color: #FCECEE; }
        .border-subtle { border-color: #F3E8E8; }
        .card-home {
            background-color: #FFFFFF;
            border: 1px solid #F3E8E8;
            border-radius: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 4px 12px rgba(112, 11, 26, 0.02);
        }
    </style>
</head>
<body class="min-h-screen py-6 px-3 sm:px-6">

    <div class="max-w-[1140px] mx-auto space-y-5">

        <!-- 1. TOP NAVIGATION BAR (PILL HEADER) -->
        <header class="bg-white border border-subtle rounded-full px-5 py-2.5 flex items-center justify-between shadow-sm flex-wrap gap-3">
            
            <!-- Brand Logo -->
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-maroon inline-block shadow-[0_0_8px_rgba(112,11,26,0.5)]"></span>
                <span class="font-black text-sm tracking-widest text-[#18181B] uppercase">METASCOUT</span>
            </div>

            <!-- Navigation Links -->
            <nav class="flex items-center gap-1.5 sm:gap-2 flex-wrap text-xs font-bold uppercase tracking-wider">
                <a href="{{ route('home') }}" class="bg-maroon text-white px-4 py-1.5 rounded-full transition shadow-sm">
                    HOMEPAGE
                </a>
                <a href="{{ route('counter.picks') }}" class="text-gray-600 hover:text-maroon hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
                    COUNTER PICKS
                </a>
                <a href="{{ route('draft.analyzer') }}" class="text-gray-600 hover:text-maroon hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
                    REKOMENDASI DRAFT
                </a>
                <a href="{{ route('hero.statistics') }}" class="text-gray-600 hover:text-maroon hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
                    STATISTIK HERO
                </a>
            </nav>

            <!-- Admin Login Button -->
            <div>
                <a href="{{ route('login') }}" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-800 hover:text-maroon border border-[#E5E7EB] text-[11px] font-extrabold uppercase tracking-wider px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition">
                    <span class="w-1.5 h-1.5 bg-gray-600 rounded-sm"></span>
                    <span>LOGIN ADMIN</span>
                </a>
            </div>

        </header>

        <!-- 2. HERO TITLE BANNER -->
        <section class="card-home p-6 sm:p-8 relative overflow-hidden">
            <div class="flex justify-between items-start flex-wrap gap-4 mb-6">
                <!-- Big Heading -->
                <div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-maroon tracking-tight uppercase leading-none">
                        METASCOUT: LAND OF DAWN
                    </h1>
                </div>

                <!-- Last Updated Badge -->
                <div class="bg-[#FAF8F8] border border-subtle rounded-xl px-3.5 py-2 text-right">
                    <div class="flex items-center justify-end gap-1.5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>TERAKHIR DIPERBARUI</span>
                    </div>
                    <div class="text-xs font-black text-[#18181B] mt-0.5">
                        UTC+7: 18 Juli 2026
                    </div>
                    <div class="text-[10px] font-semibold text-gray-400">
                        23:40
                    </div>
                </div>
            </div>

            <!-- Two Sub-Metric Boxes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Total Pertandingan -->
                <div class="bg-[#FAF8F8] border border-subtle rounded-2xl p-3.5 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-maroon-subtle flex items-center justify-center text-maroon flex-shrink-0">
                        <!-- Jar/Archive Icon -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">TOTAL PERTANDINGAN</span>
                        <span class="text-base font-black text-[#18181B] tracking-tight">{{ $totalGames }} GAME</span>
                    </div>
                </div>

                <!-- Patch Version -->
                <div class="bg-[#FAF8F8] border border-subtle rounded-2xl p-3.5 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-maroon-subtle flex items-center justify-center text-maroon flex-shrink-0">
                        <!-- Sliders/Tune Icon -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">PATCH</span>
                        <span class="text-base font-black text-[#18181B] tracking-tight">VERSI 1.8.94</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. METRICS CARDS (ROW OF 3) -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
            
            <!-- Game Dianalisis -->
            <div class="card-home p-5 flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">GAME DIANALISIS</span>
                    <div class="flex items-baseline">
                        <span class="text-4xl font-black text-[#18181B] tracking-tight">{{ $totalGames }}</span>
                        <span class="text-xs font-bold text-gray-500 ml-1.5 tracking-wider uppercase">MATCH</span>
                    </div>
                </div>
                <!-- Bottom Solid Bar -->
                <div class="w-full h-1 bg-maroon rounded-full mt-5"></div>
            </div>

            <!-- Rata-Rata Durasi Match -->
            <div class="card-home p-5 flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">RATA-RATA DURASI MATCH</span>
                    <div class="flex items-baseline">
                        <span class="text-4xl font-black text-[#18181B] tracking-tight">{{ $avgFormatted }}</span>
                        <span class="text-xs font-bold text-gray-500 ml-1.5 tracking-wider uppercase">MENIT</span>
                    </div>
                </div>
                <div>
                    <!-- Gradient Progress Bar -->
                    <div class="w-full h-1 bg-gradient-to-r from-maroon via-rose-300 to-gray-200 rounded-full mt-4"></div>
                    <div class="flex justify-between items-center text-[10px] font-bold text-gray-400 mt-1.5">
                        <span>Fastest: {{ $fastestFormatted }}</span>
                        <span>Longest: {{ $longestFormatted }}</span>
                    </div>
                </div>
            </div>

            <!-- Map Faction Bias -->
            <div class="card-home p-5 flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">MAP FACTION BIAS</span>
                    <div class="flex justify-between items-baseline">
                        <div>
                            <span class="text-3xl font-black text-[#18181B] tracking-tight">{{ number_format($bluePct, 1) }}%</span>
                            <span class="text-[9px] font-bold text-gray-400 block tracking-wider uppercase mt-0.5">BLUE SIDE ({{ $blueCount }} W)</span>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-black text-[#18181B] tracking-tight">{{ number_format($redPct, 1) }}%</span>
                            <span class="text-[9px] font-bold text-gray-400 block tracking-wider uppercase mt-0.5">RED SIDE ({{ $redCount }} W)</span>
                        </div>
                    </div>
                </div>
                <!-- Dual Segment Bar -->
                <div class="flex w-full h-1.5 rounded-full overflow-hidden mt-4 gap-1">
                    <div class="bg-maroon rounded-full h-full" style="width: {{ $bluePct }}%;"></div>
                    <div class="bg-rose-400 rounded-full h-full" style="width: {{ $redPct }}%;"></div>
                </div>
            </div>

        </section>

        <!-- 4. CORE FEATURES (GRID 3 CARDS) -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <!-- Card 1: Counter Picks & Analisis Lawan -->
            <div class="card-home p-6 flex flex-col justify-between hover:border-maroon/40 transition">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-maroon-subtle flex items-center justify-center text-maroon mb-4">
                        <!-- Shield / Swords Cross Icon -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-[#18181B] uppercase tracking-wide">
                        COUNTER PICKS & ANALISIS LAWAN
                    </h3>
                    <p class="text-xs text-gray-500 leading-relaxed mt-2 mb-6">
                        Pilih 1–5 hero musuh untuk mendapatkan rekomendasi counter pick terbaik beserta alasan taktis dan statistik dari dataset.
                    </p>
                </div>
                <a href="{{ route('counter.picks') }}" class="w-full bg-maroon bg-maroon-hover text-white text-xs font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition shadow-sm uppercase tracking-wider">
                    <span>BUKA COUNTER PICKS</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- Card 2: Statistik Hero & Tier List -->
            <div class="card-home p-6 flex flex-col justify-between hover:border-maroon/40 transition">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-maroon-subtle flex items-center justify-center text-maroon mb-4">
                        <!-- Bar Chart Icon -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-[#18181B] uppercase tracking-wide">
                        STATISTIK HERO & TIER LIST PRO META
                    </h3>
                    <p class="text-xs text-gray-500 leading-relaxed mt-2 mb-6">
                        Tabel analitik 120+ hero mencakup Win Rate, Pick Rate, Ban Rate, dan rasio K/D/A pada panggung turnamen internasional.
                    </p>
                </div>
                <a href="{{ route('hero.statistics') }}" class="w-full bg-white hover:bg-[#FAF8F8] border border-subtle text-gray-900 hover:text-maroon text-xs font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition shadow-sm uppercase tracking-wider">
                    <span>BUKA STATISTIK HERO</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- Card 3: Rekomendasi Draft Pick & Ban -->
            <div class="card-home p-6 flex flex-col justify-between hover:border-maroon/40 transition">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-maroon-subtle flex items-center justify-center text-maroon mb-4">
                        <!-- Sliders / Draft Strategy Icon -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-[#18181B] uppercase tracking-wide">
                        REKOMENDASI DRAFT PICK & BAN
                    </h3>
                    <p class="text-xs text-gray-500 leading-relaxed mt-2 mb-6">
                        Simulasi pemilihan hero draft dengan rekomendasi otomatis sinergi hero (Synergy Pick) dan prioritas larangan ancaman (Threat Ban).
                    </p>
                </div>
                <a href="{{ route('draft.analyzer') }}" class="w-full bg-white hover:bg-[#FAF8F8] border border-subtle text-gray-900 hover:text-maroon text-xs font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition shadow-sm uppercase tracking-wider">
                    <span>BUKA REKOMENDASI DRAFT</span>
                    <span>&rarr;</span>
                </a>
            </div>

        </section>

        <!-- 5. BOTTOM CARDS (TOP PICK/BAN & VARIAN MAP AKTIF) -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-5">

            <!-- Left: Top Pick / Ban Turnamen (lg:col-span-8) -->
            <div class="lg:col-span-8 card-home p-6 flex flex-col justify-between">
                <div>
                    <!-- Title -->
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2.5 h-2.5 rounded-full bg-maroon"></span>
                        <h2 class="text-xs font-black text-[#18181B] uppercase tracking-wider">
                            TOP PICK / BAN TURNAMEN
                        </h2>
                    </div>

                    <!-- Mini Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="pb-2.5 font-bold">HERO</th>
                                    <th class="pb-2.5 font-bold">PRIMARY ROLE</th>
                                    <th class="pb-2.5 font-bold text-center">BAN RATE</th>
                                    <th class="pb-2.5 font-bold text-center">PICK RATE</th>
                                    <th class="pb-2.5 font-bold text-center">WIN RATE</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($topHeroes as $hero)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-3">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $hero['portrait'] }}" 
                                                     alt="{{ $hero['name'] }}" 
                                                     class="w-7 h-7 rounded-full object-cover border border-rose-100 flex-shrink-0"
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($hero['name']) }}&background=700B1A&color=fff';">
                                                <strong class="font-extrabold text-[#18181B] text-xs">{{ $hero['name'] }}</strong>
                                            </div>
                                        </td>
                                        <td class="py-3 text-gray-400 font-bold text-[10px] uppercase">
                                            {{ $hero['role'] }}
                                        </td>
                                        <td class="py-3 text-center {{ $hero['highlight'] === 'ban' ? 'font-black text-red-600' : 'text-gray-500 font-semibold' }}">
                                            {{ $hero['ban_rate'] }}
                                        </td>
                                        <td class="py-3 text-center {{ $hero['highlight'] === 'pick' ? 'font-black text-[#18181B]' : 'text-gray-500 font-semibold' }}">
                                            {{ $hero['pick_rate'] }}
                                        </td>
                                        <td class="py-3 text-center {{ $hero['highlight'] === 'win' ? 'font-black text-emerald-600' : 'text-gray-500 font-semibold' }}">
                                            {{ $hero['win_rate'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Link -->
                <div class="border-t border-gray-100 pt-3.5 mt-2 flex justify-between items-center text-[10px] font-bold uppercase tracking-wider text-gray-400 flex-wrap gap-2">
                    <span>MENAMPILKAN 4 DARI 84 HERO YANG DIKONTES</span>
                    <a href="{{ route('hero.statistics') }}" class="text-maroon hover:underline font-extrabold">
                        LIHAT STATISTIK SELURUH HERO &gt;
                    </a>
                </div>
            </div>

            <!-- Right: Varian Map Aktif (lg:col-span-4) -->
            <div class="lg:col-span-4 card-home p-6 flex flex-col justify-between">
                <div>
                    <!-- Title -->
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2.5 h-2.5 rounded-full bg-maroon"></span>
                        <h2 class="text-xs font-black text-[#18181B] uppercase tracking-wider">
                            VARIAN MAP AKTIF
                        </h2>
                    </div>

                    <!-- 4 Map Variant Boxes -->
                    <div class="space-y-2.5">
                        @foreach($mapVariants as $map)
                            <div class="bg-[#FAF8F8] border border-subtle rounded-xl p-3 flex items-center gap-3.5 hover:border-maroon/30 transition">
                                <img src="{{ $map['image'] }}" 
                                     alt="{{ $map['name'] }}" 
                                     class="w-10 h-10 rounded-lg object-cover border border-subtle flex-shrink-0 shadow-sm">
                                <div class="min-w-0">
                                    <h4 class="text-xs font-black text-[#18181B] tracking-wide">{{ $map['name'] }}</h4>
                                    <p class="text-[10px] text-gray-400 font-medium truncate">{{ $map['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </section>

        <!-- 6. ADMIN CALL-TO-ACTION CARD -->
        <section class="card-home p-5 flex items-center justify-between flex-wrap gap-4 border-l-4 border-l-maroon">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-maroon-subtle text-maroon flex items-center justify-center flex-shrink-0">
                    <!-- Shield / Verified Check Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs font-black text-[#18181B] uppercase tracking-wide">
                        INGIN BERKONTRIBUSI SEBAGAI DATA ENTRY TURNAMEN ATAU ANALIS RISET?
                    </h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">
                        Portal Admin menyediakan panel kontrol untuk input match sheet resmi.
                    </p>
                </div>
            </div>

            <div>
                <a href="{{ route('login') }}" class="bg-maroon bg-maroon-hover text-white text-xs font-bold py-2.5 px-5 rounded-xl flex items-center gap-2 transition shadow-sm uppercase tracking-wider">
                    <!-- Padlock Icon -->
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>MASUK KE PORTAL ADMIN</span>
                </a>
            </div>
        </section>

        <!-- 7. FOOTER -->
        <footer class="border-t border-subtle pt-5 flex items-center justify-between flex-wrap gap-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-maroon"></span>
                <span>METASCOUT : LAND OF DAWN</span>
                <span>•</span>
                <span>ALL TIMESTAMPS IN UTC+7</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('rules') }}" class="hover:text-maroon transition">API DOCUMENTATION</a>
                <span class="text-gray-500 font-extrabold">ELLOIS KARINA HANDOYO</span>
            </div>
        </footer>

    </div>

</body>
</html>

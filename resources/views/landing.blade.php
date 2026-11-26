@extends('layouts.app')

@section('title', 'MetaScout: Land of Dawn - Rekomendasi Draft Pick & Analisis Strategi Mobile Legends')

@section('content')
<div class="space-y-8 py-2">

    <!-- 1. HERO SECTION BANNER -->
    <section class="card-custom p-6 sm:p-10 md:p-12 relative overflow-hidden bg-gradient-to-br from-white via-[#FAF8F8] to-[#FCECEE]/40 border border-[#F3E8E8]">
        <!-- Subtle Ambient Glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#700B1A]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-[#700B1A]/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-4xl space-y-6">
            
            <!-- Badges Bar -->
            <div class="flex items-center gap-2 flex-wrap text-[11px] font-black uppercase tracking-wider">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#700B1A] animate-pulse"></span>
                    DRAFT DNA &amp; RBR
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white text-gray-700 border border-[#E5E7EB] shadow-2xs">
                    DATA TURNAMEN MPL ID S18
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#F0FDF4] text-[#166534] border border-[#BBF7D0] shadow-2xs">
                    {{ $totalHeroes ?? 133 }} HERO TERDAFTAR
                </span>
            </div>

            <!-- Main Heading -->
            <div class="space-y-3">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-[#18181B] tracking-tight leading-[1.15]">
                    Rekomendasi <span class="text-[#700B1A]">Draft Pick</span> &amp; Analisis Strategi Mobile Legends
                </h1>
                <p class="text-xs sm:text-sm md:text-base text-gray-600 font-medium leading-relaxed max-w-3xl">
                    Susun komposisi tim berbasis Draft DNA dari 9 tim MPL ID Season 18. Dapatkan rekomendasi hero, analisis power spike hingga winning condition, serta referensi pertandingan asli secara real-time.
                </p>
            </div>

            <!-- Primary Call to Action Buttons -->
            <div class="flex items-center gap-3 flex-wrap pt-2">
                <!-- Rekomendasi Draft (Main Feature) -->
                <a href="{{ route('draft.recommendation') }}" 
                   class="bg-[#700B1A] hover:bg-[#550713] text-white px-6 py-3.5 rounded-2xl text-xs sm:text-sm font-extrabold uppercase tracking-wider flex items-center gap-2.5 transition shadow-md shadow-[#700B1A]/20 hover:scale-[1.01] active:scale-[0.99]">
                    <span>Mulai Draft</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                    </svg>
                </a>

                <!-- Homepage / Dashboard -->
                <a href="{{ route('home') }}" 
                   class="bg-white hover:bg-[#FAF8F8] text-[#18181B] hover:text-[#700B1A] border border-[#E5E7EB] hover:border-[#F8B4BD] px-5 py-3.5 rounded-2xl text-xs sm:text-sm font-extrabold uppercase tracking-wider flex items-center gap-2 transition shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"></path>
                    </svg>
                    <span>Lihat Ringkasan MPL</span>
                </a>

                <!-- Login Admin Button -->
                <a href="{{ route('login') }}" 
                   class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-700 hover:text-[#700B1A] border border-[#F3E8E8] px-4 py-3.5 rounded-2xl text-xs sm:text-sm font-extrabold uppercase tracking-wider flex items-center gap-1.5 transition">
                    <span class="w-1.5 h-1.5 bg-[#700B1A] rounded-full"></span>
                    <span>Admin</span>
                </a>
            </div>

            <!-- Metric Highlights Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-[#F3E8E8]">
                <div class="p-3 bg-white rounded-xl border border-[#F3E8E8]">
                    <div class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Katalog Hero</div>
                    <div class="text-lg sm:text-xl font-black text-[#18181B] mt-0.5">{{ $totalHeroes ?? 133 }} Hero</div>
                    <div class="text-[10px] text-gray-500 font-semibold">Role, laning &amp; atribut lengkap</div>
                </div>
                <div class="p-3 bg-white rounded-xl border border-[#F3E8E8]">
                    <div class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Dataset Turnamen</div>
                    <div class="text-lg sm:text-xl font-black text-[#18181B] mt-0.5">MPL ID S18</div>
                    <div class="text-[10px] text-gray-500 font-semibold">{{ $totalGames ?? 215 }} game resmi tervalidasi</div>
                </div>
                <div class="p-3 bg-white rounded-xl border border-[#F3E8E8]">
                    <div class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Acuan Draft</div>
                    <div class="text-lg sm:text-xl font-black text-[#700B1A] mt-0.5">9 Tim Pro</div>
                    <div class="text-[10px] text-gray-500 font-semibold">Profil Draft DNA &amp; hero pool tim</div>
                </div>
                <div class="p-3 bg-white rounded-xl border border-[#F3E8E8]">
                    <div class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Analisis Strategi</div>
                    <div class="text-lg sm:text-xl font-black text-emerald-700 mt-0.5">RBR Ringan</div>
                    <div class="text-[10px] text-gray-500 font-semibold">Identity, Power Spike &amp; Winning Condition</div>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. SECTION: JELAJAHI FITUR UTAMA -->
    <section class="space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-[#18181B] tracking-tight uppercase">
                    Eksplorasi Fitur MetaScout
                </h2>
                <p class="text-xs text-gray-500 font-medium">
                    Akses cepat seluruh fitur analisis draft dan data turnamen MPL ID S18.
                </p>
            </div>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-white border border-[#F3E8E8] px-3 py-1 rounded-full">
                6 Modul Tersedia
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            <!-- Card 1: Rekomendasi Draft (Utama) -->
            <div class="card-custom p-5 flex flex-col justify-between hover:shadow-md hover:border-[#F8B4BD] transition group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-[#FCECEE] text-[#700B1A] flex items-center justify-center font-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider bg-[#700B1A] text-white px-2.5 py-0.5 rounded-full shadow-2xs">
                            FITUR UTAMA
                        </span>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-[#18181B] group-hover:text-[#700B1A] transition">
                            Rekomendasi Draft &amp; Strategi
                        </h3>
                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed font-medium">
                            Simulasi pick &amp; ban 5v5 berbasis Draft DNA tim. Dilengkapi rekomendasi hero, analisis gaya main (playstyle), winning condition, dan referensi match mirip.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-[#F3E8E8]">
                    <a href="{{ route('draft.recommendation') }}" class="text-xs font-extrabold text-[#700B1A] hover:underline flex items-center gap-1.5 uppercase tracking-wider">
                        <span>Buka Simulator</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Card 2: Homepage & Dashboard Analisis -->
            <div class="card-custom p-5 flex flex-col justify-between hover:shadow-md hover:border-[#F8B4BD] transition group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-[#FAF8F8] border border-[#F3E8E8] text-[#18181B] flex items-center justify-center font-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-700 px-2.5 py-0.5 rounded-full">
                            RINGKASAN
                        </span>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-[#18181B] group-hover:text-[#700B1A] transition">
                            Overview MPL ID S18
                        </h3>
                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed font-medium">
                            Statistik utama turnamen: hero paling sering di-pick dan di-ban, rasio kemenangan Blue vs Red side, serta tren meta musim ini.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-[#F3E8E8]">
                    <a href="{{ route('home') }}" class="text-xs font-extrabold text-[#700B1A] hover:underline flex items-center gap-1.5 uppercase tracking-wider">
                        <span>Lihat Ringkasan</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Card 3: Statistik Hero -->
            <div class="card-custom p-5 flex flex-col justify-between hover:shadow-md hover:border-[#F8B4BD] transition group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-[#FAF8F8] border border-[#F3E8E8] text-[#18181B] flex items-center justify-center font-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A2.25 2.25 0 013 18.75v-5.625zM16.5 7.5V18.75m0 0A2.25 2.25 0 0114.25 21h-2.25a2.25 2.25 0 01-2.25-2.25V9.75m6 9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-2.25a2.25 2.25 0 00-2.25 2.25v1.5m6 0v7.5"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                            STATISTIK
                        </span>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-[#18181B] group-hover:text-[#700B1A] transition">
                            Tier List &amp; Performa Hero
                        </h3>
                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed font-medium">
                            Data Win Rate, Pick Rate, dan Ban Rate yang dihitung langsung dari data pertandingan asli, lengkap dengan filter role dan pengurutan.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-[#F3E8E8]">
                    <a href="{{ route('hero.statistics') }}" class="text-xs font-extrabold text-[#700B1A] hover:underline flex items-center gap-1.5 uppercase tracking-wider">
                        <span>Buka Statistik</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Card 4: List Hero -->
            <div class="card-custom p-5 flex flex-col justify-between hover:shadow-md hover:border-[#F8B4BD] transition group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-[#FAF8F8] border border-[#F3E8E8] text-[#18181B] flex items-center justify-center font-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-700 px-2.5 py-0.5 rounded-full">
                            KATALOG
                        </span>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-[#18181B] group-hover:text-[#700B1A] transition">
                            133 Hero MLBB
                        </h3>
                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed font-medium">
                            Daftar lengkap seluruh hero beserta role, fleksibilitas lane, spesialisasi, dan atribut dasarnya.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-[#F3E8E8]">
                    <a href="{{ route('heroes') }}" class="text-xs font-extrabold text-[#700B1A] hover:underline flex items-center gap-1.5 uppercase tracking-wider">
                        <span>Jelajahi Hero</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Card 5: Data Pertandingan Turnamen -->
            <div class="card-custom p-5 flex flex-col justify-between hover:shadow-md hover:border-[#F8B4BD] transition group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-[#FAF8F8] border border-[#F3E8E8] text-[#18181B] flex items-center justify-center font-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.003 0H9.497m5.003 0a3 3 0 002.993-2.658L18 7.5H6l.507 5.217A3 3 0 009.497 15.375m7.5-7.875v-1.5a2.25 2.25 0 00-2.25-2.25h-5.25a2.25 2.25 0 00-2.25 2.25v1.5m9.75 0H4.5"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider bg-[#F5F8FC] text-[#0A285F] border border-[#D0E1F5] px-2.5 py-0.5 rounded-full">
                            ARSIP MATCH
                        </span>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-[#18181B] group-hover:text-[#700B1A] transition">
                            Data Pertandingan MPL
                        </h3>
                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed font-medium">
                            Riwayat seri pertandingan MPL ID S18 (Bo3/Bo5/Bo7), lengkap dengan susunan pick &amp; ban, tim pemenang, dan durasi game.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-[#F3E8E8]">
                    <a href="{{ route('matches') }}" class="text-xs font-extrabold text-[#700B1A] hover:underline flex items-center gap-1.5 uppercase tracking-wider">
                        <span>Lihat Pertandingan</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Card 6: Rules RBR & Knowledge Base -->
            <div class="card-custom p-5 flex flex-col justify-between hover:shadow-md hover:border-[#F8B4BD] transition group">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-[#FAF8F8] border border-[#F3E8E8] text-[#18181B] flex items-center justify-center font-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200 px-2.5 py-0.5 rounded-full">
                            BASIS PENGETAHUAN
                        </span>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-[#18181B] group-hover:text-[#700B1A] transition">
                            Aturan Strategi (RBR)
                        </h3>
                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed font-medium">
                            Daftar aturan Rule-Based Reasoning yang digunakan sistem untuk membaca pola komposisi dan menentukan strategi tim.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-[#F3E8E8]">
                    <a href="{{ route('rules') }}" class="text-xs font-extrabold text-[#700B1A] hover:underline flex items-center gap-1.5 uppercase tracking-wider">
                        <span>Lihat Aturan</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- 3. SECTION: CARA KERJA SISTEM (4 LANGKAH) -->
    <section class="card-custom p-6 sm:p-8 bg-white border border-[#F3E8E8]">
        <div class="max-w-3xl mb-6">
            <span class="text-[10px] font-black text-[#700B1A] uppercase tracking-wider bg-[#FCECEE] px-3 py-1 rounded-full border border-[#F8B4BD]">
                CARA KERJA SISTEM
            </span>
            <h2 class="text-xl sm:text-2xl font-black text-[#18181B] tracking-tight uppercase mt-2">
                Alur Kerja MetaScout
            </h2>
            <p class="text-xs text-gray-600 font-medium mt-1">
                Tahapan analisis Draft DNA tim hingga menghasilkan rekomendasi hero dan rencana strategi:
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="p-4 rounded-xl bg-[#FAF8F8] border border-[#F3E8E8] relative">
                <div class="w-7 h-7 rounded-lg bg-[#700B1A] text-white flex items-center justify-center font-black text-xs mb-3 shadow-sm">
                    1
                </div>
                <h4 class="font-black text-xs uppercase text-[#18181B]">Pilih Tim (Draft DNA)</h4>
                <p class="text-[11px] text-gray-600 mt-1 font-medium leading-relaxed">
                    Tentukan 1 dari 9 tim MPL ID S18 sebagai acuan hero pool, lalu masukkan kondisi ban dan pick saat ini.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-[#FAF8F8] border border-[#F3E8E8] relative">
                <div class="w-7 h-7 rounded-lg bg-[#700B1A] text-white flex items-center justify-center font-black text-xs mb-3 shadow-sm">
                    2
                </div>
                <h4 class="font-black text-xs uppercase text-[#18181B]">Analisis Data &amp; Fallback</h4>
                <p class="text-[11px] text-gray-600 mt-1 font-medium leading-relaxed">
                    Sistem menghitung kecocokan hero dari histori tim tersebut. Jika belum ada sampel lawan (n = 0), sistem otomatis memakai data meta keseluruhan MPL ID S18.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-[#FAF8F8] border border-[#F3E8E8] relative">
                <div class="w-7 h-7 rounded-lg bg-[#700B1A] text-white flex items-center justify-center font-black text-xs mb-3 shadow-sm">
                    3
                </div>
                <h4 class="font-black text-xs uppercase text-[#18181B]">Inferensi Strategi (RBR)</h4>
                <p class="text-[11px] text-gray-600 mt-1 font-medium leading-relaxed">
                    Aturan pakar menerjemahkan komposisi hero terpilih menjadi Draft Identity, Power Spike, Playstyle, dan Winning Condition.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-[#FAF8F8] border border-[#F3E8E8] relative">
                <div class="w-7 h-7 rounded-lg bg-[#700B1A] text-white flex items-center justify-center font-black text-xs mb-3 shadow-sm">
                    4
                </div>
                <h4 class="font-black text-xs uppercase text-[#18181B]">Rekomendasi &amp; Match Mirip</h4>
                <p class="text-[11px] text-gray-600 mt-1 font-medium leading-relaxed">
                    Menampilkan urutan rekomendasi hero terbaik beserta 2–3 referensi pertandingan asli MPL ID S18 yang paling mendekati.
                </p>
            </div>

        </div>
    </section>

    <!-- 4. SECTION: ADMIN PORTAL CALLOUT BANNER -->
    <section class="card-custom p-6 sm:p-8 bg-gradient-to-r from-[#18181B] to-[#27272A] text-white border-0 flex flex-col md:flex-row items-center justify-between gap-6 shadow-md">
        <div class="space-y-2 max-w-2xl">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-300">ADMINISTRATOR & INTEGRASI API</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-black tracking-tight uppercase">
                Panel Manajemen Dataset & Sinkronisasi Liquipedia
            </h3>
            <p class="text-xs text-gray-400 font-medium leading-relaxed">
                Khusus administrator dan tim penilai: sinkronisasi dataset pertandingan resmi langsung dari Liquipedia MediaWiki DB API v3 secara on-demand, pantau status relasi database, dan audit integritas data turnamen.
            </p>
        </div>

        <div class="flex-shrink-0">
            @if(session('admin_logged_in'))
                <a href="{{ route('admin.dashboard') }}" 
                   class="bg-[#700B1A] hover:bg-[#550713] text-white px-5 py-3 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-2 transition shadow-sm">
                    <span>Masuk Admin Console</span>
                    <span>&rarr;</span>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="bg-white hover:bg-gray-100 text-[#18181B] px-5 py-3 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-2 transition shadow-sm">
                    <span>Login Admin Sekarang</span>
                    <span>&rarr;</span>
                </a>
            @endif
        </div>
    </section>

</div>
@endsection

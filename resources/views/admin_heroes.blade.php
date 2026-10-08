@extends('layouts.app')

@section('title', 'Manajemen Data Hero - MetaScout Admin')
@section('hide_navbar', true)
@section('hide_footer', true)

@section('content')
<div class="space-y-6">

    <!-- 1. ADMIN TOP NAVIGATION BAR -->
    <header class="bg-white border border-[#F3E8E8] rounded-full px-5 py-2.5 flex items-center justify-between shadow-sm flex-wrap gap-3">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="w-3 h-3 rounded-full bg-[#700B1A] inline-block shadow-[0_0_8px_rgba(112,11,26,0.5)]"></span>
            <span class="font-black text-sm tracking-widest text-[#18181B] uppercase">METASCOUT</span>
        </a>

        <!-- Admin Links -->
        <nav class="flex items-center gap-1.5 sm:gap-2 flex-wrap text-xs font-bold uppercase tracking-wider">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
                DASHBOARD
            </a>
            <a href="{{ route('matches') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
                DATASET PERTANDINGAN
            </a>
            <a href="{{ route('admin.heroes') }}" class="bg-[#700B1A] text-white px-4 py-1.5 rounded-full transition shadow-sm">
                MANAJEMEN HERO
            </a>
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
                KEMBALI KE PORTAL PUBLIK
            </a>
        </nav>

        <!-- Superadmin Info & Logout -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5 text-[11px] font-extrabold text-[#700B1A] uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                <span>SUPERADMIN</span>
            </div>
            <a href="{{ route('admin.logout') }}" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-800 hover:text-[#700B1A] border border-[#E5E7EB] text-[11px] font-extrabold uppercase px-3.5 py-1.5 rounded-full transition">
                LOGOUT
            </a>
            <div class="w-7 h-7 rounded-full bg-[#700B1A] text-white flex items-center justify-center font-black text-xs shadow-sm">
                A
            </div>
        </div>
    </header>

    <!-- 2. BREADCRUMBS & STATUS -->
    <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase tracking-wider">
            <span>ADMIN</span>
            <span>/</span>
            <span class="text-[#700B1A] font-extrabold">MANAJEMEN DATA HERO</span>
        </div>

        <div class="flex items-center gap-2 text-[11px] font-extrabold text-[#700B1A] bg-[#FCECEE] border border-[#F8B4BD] rounded-full px-3.5 py-1 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
            <span>DATABASE STATUS: SYNCHRONIZED WITH PATCH 1.9.14</span>
        </div>
    </div>

    <!-- 3. PAGE HEADING & TOP ACTION BUTTONS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#18181B] tracking-tight uppercase">
                MANAJEMEN DATA HERO
            </h1>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" 
                    onclick="toggleHeroForm()" 
                    class="bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-extrabold uppercase tracking-wider px-4 py-2.5 rounded-full shadow-sm flex items-center gap-1.5 transition">
                <span>+ TAMBAH HERO BARU</span>
            </button>
            <a href="{{ route('rules') }}" class="bg-white hover:bg-[#FAF8F8] text-[#18181B] border border-[#F3E8E8] text-xs font-extrabold uppercase tracking-wider px-4 py-2.5 rounded-full shadow-sm flex items-center gap-1.5 transition">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Audit Counter-Pick Rules</span>
            </a>
        </div>
    </div>

    <!-- 4. TAMBAH & EDIT DATA HERO (FORM CARD) -->
    <div class="card-custom p-6 space-y-6" id="heroFormCard">
        <div class="flex items-center justify-between border-b border-[#FAF0F1] pb-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#700B1A]"></span>
                <h3 class="text-sm font-black uppercase tracking-wider text-[#18181B]">
                    TAMBAH & EDIT DATA HERO
                </h3>
            </div>
            <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-500 text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase">
                Mode Form Aktif: Input Hero
            </span>
        </div>

        <form id="heroDataForm" onsubmit="handleFormSubmit(event)" class="space-y-5">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left Form Column (7 cols) -->
                <div class="lg:col-span-7 space-y-4">
                    <!-- Row: Nama Hero, Role Utama, Role Sekunder -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">
                                NAMA HERO
                            </label>
                            <input type="text" 
                                   placeholder="e.g. Suyou / Zhuxin / Marcel" 
                                   class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 px-3 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">
                                ROLE UTAMA
                            </label>
                            <select class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 px-3 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
                                <option>Mage</option>
                                <option>Tank</option>
                                <option>Fighter</option>
                                <option>Assassin</option>
                                <option>Marksman</option>
                                <option>Support</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">
                                ROLE SEKUNDER (OPSIONAL)
                            </label>
                            <select class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 px-3 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
                                <option>None</option>
                                <option>Tank</option>
                                <option>Support</option>
                                <option>Assassin</option>
                                <option>Fighter</option>
                            </select>
                        </div>
                    </div>

                    <!-- Rekomendasi Lane Utama -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1.5">
                            REKOMENDASI LANE UTAMA (MULTI-SELECT)
                        </label>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white cursor-pointer">Gold Lane</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white cursor-pointer">Mid Lane</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Exp Lane</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Jungle</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Roam</span>
                        </div>
                    </div>

                    <!-- Rekomendasi Role Utama -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1.5">
                            REKOMENDASI ROLE UTAMA (MULTI-SELECT)
                        </label>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white cursor-pointer">Assassin</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white cursor-pointer">Fighter</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white cursor-pointer">Mage</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Marksman</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Tank</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Support</span>
                        </div>
                    </div>

                    <!-- Speciality Tags -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1.5">
                            SPECIALITY TAGS
                        </label>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] cursor-pointer">Burst</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Crowd Control</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] cursor-pointer">Damage</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] cursor-pointer">Charge</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Initiator</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Reap</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Poke</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Guard</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Regen</span>
                        </div>
                    </div>

                    <!-- Power Spike Timing -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1.5">
                            POWER SPIKE TIMING
                        </label>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Early Game</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] cursor-pointer">Mid Game</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Late Game</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] cursor-pointer">Scaler</span>
                        </div>
                    </div>
                </div>

                <!-- Right Artwork Upload Column (5 cols) -->
                <div class="lg:col-span-5 space-y-3">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500">
                        UPLOAD ARTWORK / AVATAR ICON
                    </label>

                    <div class="border-2 border-dashed border-[#F3E8E8] rounded-2xl p-6 text-center space-y-3 bg-[#FAF8F8]/50 hover:bg-[#FAF8F8] transition">
                        <div class="w-10 h-10 rounded-full bg-[#FCECEE] text-[#700B1A] flex items-center justify-center mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-700">Drag & Drop icon hero di sini</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">PNG / WebP (Maks. 2MB, Rasio 1:1)</p>
                        </div>
                        <button type="button" class="bg-white border border-[#F3E8E8] hover:bg-[#FAF8F8] text-gray-700 text-xs font-bold px-3 py-1 rounded-xl shadow-xs">
                            Pilih File
                        </button>
                    </div>

                    <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#700B1A] text-white flex items-center justify-center font-black text-xs">
                            H
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-[#18181B] truncate">Preview Icon Hero</div>
                            <div class="text-[10px] text-gray-400 font-mono">harith_avatar_patch1914.webp</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Form Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#FAF0F1]">
                <button type="button" 
                        onclick="toggleHeroForm()" 
                        class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-700 border border-[#F3E8E8] px-4 py-2 rounded-xl text-xs font-bold transition">
                    Batal
                </button>
                <button type="submit" 
                        class="bg-[#700B1A] hover:bg-[#550713] text-white px-5 py-2 rounded-xl text-xs font-extrabold shadow-sm transition">
                    Simpan Data Hero
                </button>
            </div>
        </form>
    </div>

    <!-- 5. TABLE SECTION (HEROES LIST & COUNTER AUDIT) -->
    <div class="card-custom p-6 space-y-4">
        <!-- Search & Filter Controls -->
        <div class="space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" 
                           id="heroTableSearch" 
                           oninput="filterHeroTable()" 
                           placeholder="Cari hero berdasarkan nama, role, atau lane..." 
                           class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 pl-8 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A]">
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-extrabold uppercase text-gray-400">URUTKAN:</span>
                    <select class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-1.5 px-3 text-xs font-bold text-[#18181B] focus:outline-none">
                        <option>Berdasarkan Win Rate</option>
                        <option>Berdasarkan Ban Rate</option>
                        <option>Berdasarkan Nama</option>
                    </select>
                </div>
            </div>

            <!-- Role Filters -->
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <span class="text-[10px] font-extrabold uppercase text-gray-400 w-12">ROLE:</span>
                <div class="flex items-center gap-1 flex-wrap">
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#700B1A] text-white">SEMUA</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">TANK</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">FIGHTER</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">ASSASSIN</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">MAGE</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">MARKSMAN</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">SUPPORT</button>
                </div>
            </div>

            <!-- Lane Filters -->
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <span class="text-[10px] font-extrabold uppercase text-gray-400 w-12">LANE:</span>
                <div class="flex items-center gap-1 flex-wrap">
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#700B1A] text-white">SEMUA</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">EXP LANE</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">JUNGLE</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">MID LANE</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">GOLD LANE</button>
                    <button class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8]">ROAM</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-[#F3E8E8]">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-[#FAF8F8] border-b border-[#F3E8E8] text-gray-500 font-extrabold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-3 w-8 text-center">#</th>
                        <th class="py-3 px-3">HERO</th>
                        <th class="py-3 px-3">ROLE & LANE</th>
                        <th class="py-3 px-3 text-center">TIER META</th>
                        <th class="py-3 px-3 text-center">WIN RATE (PRO)</th>
                        <th class="py-3 px-3 text-center">BAN RATE (PRO)</th>
                        <th class="py-3 px-3">SPECIALITY</th>
                        <th class="py-3 px-3">ATURAN COUNTER AKTIF</th>
                        <th class="py-3 px-3 text-center">STATUS PATCH</th>
                        <th class="py-3 px-3 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F3E8E8] font-medium" id="heroTableBody">
                    @php
                        $presetHeroes = [
                            ['name' => 'Harith', 'title' => 'Time Traveler', 'role' => 'Mage', 'lane' => 'Gold Lane, Mid', 'tier' => 'Tier S', 'wr' => '75.00%', 'br' => '91.30%', 'specialty' => 'Damage, Charge', 'rules' => '12 Aturan', 'counter_desc' => 'Counters: Granger, Moskov', 'patch' => 'Buffed (Patch 1.9.14)', 'patch_color' => 'bg-[#FCECEE] text-[#700B1A]'],
                            ['name' => 'Karrie', 'title' => 'Lost Star', 'role' => 'Marksman', 'lane' => 'Gold Lane', 'tier' => 'Tier S', 'wr' => '71.40%', 'br' => '62.30%', 'specialty' => 'Damage, Burst', 'rules' => '15 Aturan', 'counter_desc' => 'Counters: Tank Meta, Baxia', 'patch' => 'Adjusted', 'patch_color' => 'bg-gray-100 text-gray-700'],
                            ['name' => 'Baxia', 'title' => 'Mystic Tortoise', 'role' => 'Tank', 'lane' => 'Jungle, Roam', 'tier' => 'Tier S', 'wr' => '65.20%', 'br' => '44.90%', 'specialty' => 'Initiator, Crowd Control', 'rules' => '18 Aturan', 'counter_desc' => 'Counters: Regen heroes', 'patch' => 'Unchanged', 'patch_color' => 'bg-gray-100 text-gray-700'],
                            ['name' => 'Terizla', 'title' => 'Executioner', 'role' => 'Fighter', 'lane' => 'Exp Lane', 'tier' => 'Tier A+', 'wr' => '60.90%', 'br' => '39.10%', 'specialty' => 'Damage, Crowd Control', 'rules' => '14 Aturan', 'counter_desc' => 'Counters: Paquito, Yu Zhong', 'patch' => 'Unchanged', 'patch_color' => 'bg-gray-100 text-gray-700'],
                            ['name' => 'Yve', 'title' => 'Astrowarden', 'role' => 'Mage', 'lane' => 'Mid Lane', 'tier' => 'Tier A+', 'wr' => '61.10%', 'br' => '30.40%', 'specialty' => 'Crowd Control, Damage', 'rules' => '10 Aturan', 'counter_desc' => 'Counters: Pharsa, Xavier', 'patch' => 'Nerfed', 'patch_color' => 'bg-red-100 text-red-700'],
                            ['name' => 'Aulus', 'title' => 'Warrior of Ferocity', 'role' => 'Fighter', 'lane' => 'Jungle, Exp', 'tier' => 'Tier A', 'wr' => '59.40%', 'br' => '15.79%', 'specialty' => 'Damage, Charge', 'rules' => '8 Aturan', 'counter_desc' => 'Late game carry', 'patch' => 'Buffed', 'patch_color' => 'bg-emerald-100 text-emerald-700'],
                            ['name' => 'Suyou', 'title' => 'Mask of the Immortal', 'role' => 'Fighter / Assassin', 'lane' => 'Jungle', 'tier' => 'Tier S', 'wr' => '37.50%', 'br' => '29.00%', 'specialty' => 'Damage, Charge', 'rules' => '6 Aturan', 'counter_desc' => 'Counters: Squishy Mage', 'patch' => 'New Hero (1.9.14)', 'patch_color' => 'bg-[#FCECEE] text-[#700B1A]'],
                            ['name' => 'Marcel', 'title' => 'Celestial Warden', 'role' => 'Support', 'lane' => 'Roam', 'tier' => 'Tier A+', 'wr' => '58.15%', 'br' => '33.30%', 'specialty' => 'Crowd Control, Support', 'rules' => '9 Aturan', 'counter_desc' => 'Counters: Dive assassins', 'patch' => 'Adjusted', 'patch_color' => 'bg-gray-100 text-gray-700'],
                        ];
                    @endphp

                    @foreach($presetHeroes as $idx => $h)
                        <tr class="hover:bg-[#FAF8F8]/80 transition hero-row">
                            <td class="py-3 px-3 text-center text-gray-400 font-bold">{{ sprintf('%02d', $idx + 1) }}</td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-[#700B1A] text-white flex items-center justify-center font-black text-xs flex-shrink-0">
                                        {{ substr($h['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-[#18181B] flex items-center gap-1.5">
                                            <span>{{ $h['name'] }}</span>
                                            @if($h['name'] === 'Suyou')
                                                <span class="bg-[#700B1A] text-white text-[8px] font-black px-1 rounded">BARU</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-gray-400">{{ $h['title'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-[#18181B]">{{ $h['role'] }}</div>
                                <div class="text-[10px] text-gray-400">{{ $h['lane'] }}</div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="bg-[#18181B] text-white font-black text-[9px] px-2 py-0.5 rounded">
                                    {{ $h['tier'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-black text-emerald-700">
                                {{ $h['wr'] }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-extrabold text-red-700">
                                {{ $h['br'] }}
                            </td>
                            <td class="py-3 px-3 text-[11px] text-gray-600">
                                <span class="bg-[#FAF8F8] border border-[#F3E8E8] px-2 py-0.5 rounded text-[10px] font-bold">
                                    {{ $h['specialty'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-[11px]">
                                <div class="font-bold text-[#18181B]">{{ $h['rules'] }}</div>
                                <div class="text-[10px] text-gray-400 truncate max-w-[140px]">{{ $h['counter_desc'] }}</div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="{{ $h['patch_color'] }} text-[9px] font-black px-2 py-0.5 rounded uppercase">
                                    {{ $h['patch'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <button type="button" 
                                        onclick="alert('Edit counter rules untuk {{ $h['name'] }}')" 
                                        class="bg-[#700B1A] hover:bg-[#550713] text-white text-[10px] font-extrabold px-2.5 py-1.5 rounded-lg flex items-center gap-1 mx-auto transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    <span>Edit & Counter</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between pt-2 flex-wrap gap-2 text-xs">
            <span class="text-gray-400 text-[11px]">Menampilkan 8 dari 124 hero terdaftar</span>
            <div class="flex items-center gap-1 font-bold">
                <button class="px-2 py-1 rounded-lg border border-[#F3E8E8] text-gray-400">&lt;</button>
                <button class="px-2.5 py-1 rounded-lg bg-[#700B1A] text-white">1</button>
                <button class="px-2.5 py-1 rounded-lg border border-[#F3E8E8] text-gray-600 hover:bg-[#FAF8F8]">2</button>
                <button class="px-2.5 py-1 rounded-lg border border-[#F3E8E8] text-gray-600 hover:bg-[#FAF8F8]">3</button>
                <span class="px-1 text-gray-400">...</span>
                <button class="px-2.5 py-1 rounded-lg border border-[#F3E8E8] text-gray-600 hover:bg-[#FAF8F8]">16</button>
                <button class="px-2 py-1 rounded-lg border border-[#F3E8E8] text-gray-600 hover:bg-[#FAF8F8]">&gt;</button>
            </div>
        </div>
    </div>

    <!-- 6. FOOTER -->
    <div class="border-t border-[#F3E8E8] pt-4 flex items-center justify-between text-[11px] font-bold text-gray-400 flex-wrap gap-2">
        <div>METASCOUT : LAND OF DAWN • ADMIN PANEL</div>
        <div>ALL TIMESTAMPS IN UTC+7</div>
    </div>

</div>

<script>
    function toggleHeroForm() {
        const form = document.getElementById('heroFormCard');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            form.scrollIntoView({ behavior: 'smooth' });
        } else {
            form.style.display = 'none';
        }
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        alert('Data hero berhasil divalidasi dan disimpan ke knowledge base RBR!');
    }

    function filterHeroTable() {
        const q = (document.getElementById('heroTableSearch').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.hero-row');
        rows.forEach(r => {
            const txt = r.innerText.toLowerCase();
            r.style.display = (!q || txt.includes(q)) ? '' : 'none';
        });
    }
</script>
@endsection

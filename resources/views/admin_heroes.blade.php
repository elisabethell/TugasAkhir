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
                    <select id="adminHeroSortSelect" onchange="sortAdminHeroTable(this.value)" class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-1.5 px-3 text-xs font-bold text-[#18181B] focus:outline-none cursor-pointer">
                        <option value="wr">Berdasarkan Win Rate</option>
                        <option value="br">Berdasarkan Ban Rate</option>
                        <option value="name">Berdasarkan Nama</option>
                    </select>
                </div>
            </div>

            <!-- Role Filters -->
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <span class="text-[10px] font-extrabold uppercase text-gray-400 w-12">ROLE:</span>
                <div class="flex items-center gap-1 flex-wrap" id="adminRoleFilterContainer">
                    <button type="button" onclick="setAdminRoleFilter('all', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#700B1A] text-white">SEMUA</button>
                    <button type="button" onclick="setAdminRoleFilter('tank', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">TANK</button>
                    <button type="button" onclick="setAdminRoleFilter('fighter', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">FIGHTER</button>
                    <button type="button" onclick="setAdminRoleFilter('assassin', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">ASSASSIN</button>
                    <button type="button" onclick="setAdminRoleFilter('mage', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">MAGE</button>
                    <button type="button" onclick="setAdminRoleFilter('marksman', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">MARKSMAN</button>
                    <button type="button" onclick="setAdminRoleFilter('support', this)" class="admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">SUPPORT</button>
                </div>
            </div>

            <!-- Lane Filters -->
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <span class="text-[10px] font-extrabold uppercase text-gray-400 w-12">LANE:</span>
                <div class="flex items-center gap-1 flex-wrap" id="adminLaneFilterContainer">
                    <button type="button" onclick="setAdminLaneFilter('all', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#700B1A] text-white">SEMUA</button>
                    <button type="button" onclick="setAdminLaneFilter('exp', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">EXP LANE</button>
                    <button type="button" onclick="setAdminLaneFilter('jungle', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">JUNGLE</button>
                    <button type="button" onclick="setAdminLaneFilter('mid', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">MID LANE</button>
                    <button type="button" onclick="setAdminLaneFilter('gold', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">GOLD LANE</button>
                    <button type="button" onclick="setAdminLaneFilter('roam', this)" class="admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]">ROAM</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-[#F3E8E8]">
            <table class="w-full text-left text-xs border-collapse" id="adminHeroTable">
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
                    @forelse($heroes as $idx => $h)
                        @php
                            $stat = $statsLookup[strtolower($h->hero_name)] ?? null;
                            $wr = $stat['win_rate'] ?? '50.0%';
                            $br = $stat['ban_rate'] ?? '5.0%';
                            $wrNum = (float)str_replace(['%', ' '], '', $wr);
                            $brNum = (float)str_replace(['%', ' '], '', $br);
                            $tier = $stat['tier'] ?? ($wrNum >= 58 ? 'Tier S' : ($wrNum >= 52 ? 'Tier A+' : 'Tier A'));
                            $specialtyStr = !empty($h->specialties) ? implode(', ', (array)$h->specialties) : 'Versatile';
                            $laneStr = $h->laning ?: 'Flex';
                        @endphp
                        <tr class="hover:bg-[#FAF8F8]/80 transition hero-row"
                            data-name="{{ strtolower($h->hero_name) }}"
                            data-role="{{ strtolower($h->primary_class . ' ' . $h->class) }}"
                            data-lane="{{ strtolower($laneStr) }}"
                            data-wr="{{ $wrNum }}"
                            data-br="{{ $brNum }}">
                            <td class="py-3 px-3 text-center text-gray-400 font-bold row-num-cell">{{ sprintf('%02d', $idx + 1) }}</td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $h->portrait }}" 
                                         alt="{{ $h->hero_name }}" 
                                         class="w-8 h-8 rounded-full object-cover bg-gray-100 border border-[#F3E8E8] shadow-2xs flex-shrink-0"
                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($h->hero_name) }}&background=700B1A&color=fff';">
                                    <div>
                                        <div class="font-extrabold text-[#18181B] flex items-center gap-1.5">
                                            <span>{{ $h->hero_name }}</span>
                                            @if($h->hero_name === 'Suyou')
                                                <span class="bg-[#700B1A] text-white text-[8px] font-black px-1 rounded">BARU</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-gray-400">{{ $h->title ?: 'Land of Dawn Hero' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-[#18181B]">{{ $h->primary_class }}</div>
                                <div class="text-[10px] text-gray-400">{{ $laneStr }}</div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="bg-[#18181B] text-white font-black text-[9px] px-2 py-0.5 rounded">
                                    {{ $tier }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-black {{ $wrNum >= 55 ? 'text-emerald-700' : 'text-gray-700' }}">
                                {{ $wr }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-extrabold {{ $brNum >= 30 ? 'text-red-700' : 'text-gray-700' }}">
                                {{ $br }}
                            </td>
                            <td class="py-3 px-3 text-[11px] text-gray-600">
                                <span class="bg-[#FAF8F8] border border-[#F3E8E8] px-2 py-0.5 rounded text-[10px] font-bold">
                                    {{ $specialtyStr }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-[11px]">
                                <div class="font-bold text-[#18181B]">Aktif</div>
                                <div class="text-[10px] text-gray-400 truncate max-w-[140px]">Rule-Based Reasoning</div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="bg-[#FCECEE] text-[#700B1A] text-[9px] font-black px-2 py-0.5 rounded uppercase">
                                    Patch 1.9.14
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <button type="button" 
                                        onclick="alert('Edit counter rules untuk {{ $h->hero_name }}')" 
                                        class="bg-[#700B1A] hover:bg-[#550713] text-white text-[10px] font-extrabold px-2.5 py-1.5 rounded-lg flex items-center gap-1 mx-auto transition cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-6 text-center text-gray-400 font-bold">Tidak ada data hero.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (Limit 20 per page, Next / Prev) -->
        <div class="flex items-center justify-between pt-4 border-t border-[#F3E8E8] flex-wrap gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 font-medium">Tampilkan:</span>
                <select id="adminHeroPerPageSelect" onchange="changeAdminHeroPerPage(this.value)" class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-lg px-2.5 py-1 text-xs font-bold text-[#18181B] focus:outline-none focus:border-[#700B1A] cursor-pointer">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                    <option value="all">Semua</option>
                </select>
                <span class="text-gray-300">|</span>
                <div class="text-gray-600 font-semibold" id="adminHeroPaginationInfo">
                    Menampilkan <span class="font-extrabold text-[#700B1A]" id="adminHeroPageStart">1</span> - <span class="font-extrabold text-[#700B1A]" id="adminHeroPageEnd">20</span> dari <span class="font-extrabold text-[#18181B]" id="adminHeroTotalRows">0</span> hero terdaftar
                </div>
            </div>

            <!-- Page Buttons (Prev, 1, 2, 3..., Next) -->
            <div class="flex items-center gap-1.5" id="adminHeroPaginationButtons">
                <!-- Dynamically generated via JS -->
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
    let currentAdminHeroPage = 1;
    let adminHeroPerPage = 20;
    let activeRoleFilter = 'all';
    let activeLaneFilter = 'all';

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

    function setAdminRoleFilter(role, btn) {
        activeRoleFilter = role;
        document.querySelectorAll('.admin-role-btn').forEach(b => {
            b.className = 'admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]';
        });
        btn.className = 'admin-role-btn px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#700B1A] text-white';
        filterHeroTable();
    }

    function setAdminLaneFilter(lane, btn) {
        activeLaneFilter = lane;
        document.querySelectorAll('.admin-lane-btn').forEach(b => {
            b.className = 'admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A]';
        });
        btn.className = 'admin-lane-btn px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#700B1A] text-white';
        filterHeroTable();
    }

    function filterHeroTable() {
        const q = (document.getElementById('heroTableSearch').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.hero-row');

        rows.forEach(r => {
            const name = r.dataset.name || '';
            const role = r.dataset.role || '';
            const lane = r.dataset.lane || '';

            const matchQuery = !q || name.includes(q) || role.includes(q) || lane.includes(q);
            const matchRole = activeRoleFilter === 'all' || role.includes(activeRoleFilter);
            const matchLane = activeLaneFilter === 'all' || lane.includes(activeLaneFilter);

            r.dataset.matched = (matchQuery && matchRole && matchLane) ? 'true' : 'false';
        });

        currentAdminHeroPage = 1;
        updateAdminHeroPagination();
    }

    function changeAdminHeroPerPage(val) {
        adminHeroPerPage = val === 'all' ? 999999 : parseInt(val);
        currentAdminHeroPage = 1;
        updateAdminHeroPagination();
    }

    function goToAdminHeroPage(page) {
        currentAdminHeroPage = page;
        updateAdminHeroPagination();
        const table = document.getElementById('adminHeroTable');
        if (table) {
            table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function sortAdminHeroTable(sortBy) {
        const tbody = document.getElementById('heroTableBody');
        const rows = Array.from(tbody.querySelectorAll('.hero-row'));

        rows.sort((a, b) => {
            if (sortBy === 'wr') {
                return (parseFloat(b.dataset.wr) || 0) - (parseFloat(a.dataset.wr) || 0);
            } else if (sortBy === 'br') {
                return (parseFloat(b.dataset.br) || 0) - (parseFloat(a.dataset.br) || 0);
            } else {
                return (a.dataset.name || '').localeCompare(b.dataset.name || '');
            }
        });

        rows.forEach(r => tbody.appendChild(r));
        updateAdminHeroPagination();
    }

    function updateAdminHeroPagination() {
        const tbody = document.getElementById('heroTableBody');
        const allRows = Array.from(tbody.querySelectorAll('.hero-row'));
        const matchedRows = allRows.filter(r => r.dataset.matched !== 'false');
        const totalMatched = matchedRows.length;
        const totalPages = Math.ceil(totalMatched / adminHeroPerPage) || 1;

        if (currentAdminHeroPage > totalPages) currentAdminHeroPage = totalPages;
        if (currentAdminHeroPage < 1) currentAdminHeroPage = 1;

        const startIdx = (currentAdminHeroPage - 1) * adminHeroPerPage;
        const endIdx = startIdx + adminHeroPerPage;

        allRows.forEach(r => r.style.display = 'none');
        matchedRows.slice(startIdx, endIdx).forEach(r => r.style.display = '');

        // Update info text
        const startElem = document.getElementById('adminHeroPageStart');
        const endElem = document.getElementById('adminHeroPageEnd');
        const totalElem = document.getElementById('adminHeroTotalRows');
        if (startElem) startElem.textContent = totalMatched > 0 ? (startIdx + 1) : 0;
        if (endElem) endElem.textContent = Math.min(endIdx, totalMatched);
        if (totalElem) totalElem.textContent = totalMatched;

        // Render buttons
        const container = document.getElementById('adminHeroPaginationButtons');
        if (!container) return;
        container.innerHTML = '';

        if (totalPages <= 1) return;

        // Prev
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.disabled = currentAdminHeroPage === 1;
        prevBtn.className = `px-3 py-1 rounded-full text-xs font-bold border transition ${
            currentAdminHeroPage === 1 
                ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' 
                : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer shadow-2xs'
        }`;
        prevBtn.innerHTML = '&larr; Prev';
        prevBtn.onclick = () => goToAdminHeroPage(currentAdminHeroPage - 1);
        container.appendChild(prevBtn);

        // Page pills
        for (let p = 1; p <= totalPages; p++) {
            if (p === 1 || p === totalPages || (p >= currentAdminHeroPage - 1 && p <= currentAdminHeroPage + 1)) {
                const pageBtn = document.createElement('button');
                pageBtn.type = 'button';
                pageBtn.className = `w-7 h-7 rounded-full text-xs font-extrabold flex items-center justify-center transition ${
                    p === currentAdminHeroPage 
                        ? 'bg-[#700B1A] text-white shadow-sm' 
                        : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer'
                }`;
                pageBtn.textContent = p;
                pageBtn.onclick = () => goToAdminHeroPage(p);
                container.appendChild(pageBtn);
            } else if (p === currentAdminHeroPage - 2 || p === currentAdminHeroPage + 2) {
                const dots = document.createElement('span');
                dots.className = 'text-gray-400 px-1 font-bold text-xs';
                dots.textContent = '...';
                container.appendChild(dots);
            }
        }

        // Next
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.disabled = currentAdminHeroPage === totalPages;
        nextBtn.className = `px-3 py-1 rounded-full text-xs font-bold border transition ${
            currentAdminHeroPage === totalPages 
                ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' 
                : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer shadow-2xs'
        }`;
        nextBtn.innerHTML = 'Next &rarr;';
        nextBtn.onclick = () => goToAdminHeroPage(currentAdminHeroPage + 1);
        container.appendChild(nextBtn);
    }

    document.addEventListener('DOMContentLoaded', function() {
        filterHeroTable();
    });
</script>
@endsection

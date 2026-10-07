@extends('layouts.app')

@section('title', 'Dataset Pertandingan Resmi - MetaScout Admin')
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
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
                DASHBOARD
            </a>
            <a href="{{ route('matches') }}" class="bg-[#700B1A] text-white px-4 py-1.5 rounded-full transition shadow-sm">
                DATASET PERTANDINGAN
            </a>
            <a href="{{ route('heroes') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
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

    <!-- 2. BREADCRUMBS & LIVE STATUS -->
    <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase tracking-wider">
            <span>ADMIN</span>
            <span>/</span>
            <span class="text-[#700B1A] font-extrabold">DATASET PERTANDINGAN</span>
        </div>

        <div class="flex items-center gap-2 text-[11px] font-extrabold text-[#700B1A] bg-[#FCECEE] border border-[#F8B4BD] rounded-full px-3.5 py-1 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
            <span>LIVE DATABASE V1.9.14</span>
        </div>
    </div>

    <!-- 3. PAGE HEADING & TOP ACTION BUTTONS -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#18181B] tracking-tight uppercase">
                DATASET PERTANDINGAN RESMI
            </h1>
            <p class="text-xs text-gray-600 mt-1 max-w-2xl font-medium">
                Kelola match sheet turnamen pro 5v5, validasi draft pick/ban, sinkronisasi power spike data, dan tambah rekaman pertandingan baru.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" 
                    onclick="toggleInputForm()" 
                    class="bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-extrabold uppercase tracking-wider px-4 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
                <span>+ INPUT MATCH SHEET BARU</span>
            </button>

            <button type="button" 
                    onclick="exportDatasetCSV()" 
                    class="bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] text-xs font-bold uppercase tracking-wider px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Export Dataset (CSV/JSON)</span>
            </button>

            <button type="button" 
                    class="bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] text-xs font-bold uppercase tracking-wider px-3.5 py-2 rounded-full shadow-sm flex items-center gap-1.5 transition">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <span>Import Bulk Replay Data</span>
            </button>
        </div>
    </div>

    <!-- 4. TWO SUMMARY STAT CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Total Rekaman Match -->
        <div class="card-custom p-5 flex items-center justify-between">
            <div>
                <div class="text-[11px] font-black uppercase tracking-wider text-gray-400 mb-1">
                    TOTAL REKAMAN MATCH
                </div>
                <div class="text-3xl font-black text-[#18181B] tracking-tight">
                    {{ $totalGames }} <span class="text-sm font-bold text-gray-500 font-sans">Game</span>
                </div>
            </div>
            <div class="w-14 h-14 rounded-full bg-[#FCECEE] flex items-center justify-center text-[#700B1A]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        </div>

        <!-- Rata-Rata Durasi -->
        <div class="card-custom p-5 flex items-center justify-between">
            <div>
                <div class="text-[11px] font-black uppercase tracking-wider text-gray-400 mb-1">
                    RATA-RATA DURASI
                </div>
                <div class="text-3xl font-black text-[#18181B] tracking-tight">
                    {{ $avgDuration }} <span class="text-sm font-bold text-gray-500 font-sans">Menit</span>
                </div>
                <div class="text-[11px] font-medium text-gray-500 mt-1">
                    Tercepat {{ $fastestDuration }} • Terlama {{ $longestDuration }}
                </div>
            </div>
            <div class="w-14 h-14 rounded-full bg-[#FCECEE] flex items-center justify-center text-[#700B1A]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- 5. FORM INPUT MATCH SHEET CEPAT -->
    <div class="card-custom p-6 space-y-5" id="quickInputForm">
        <!-- Form Title -->
        <div class="flex items-center gap-2.5 text-xs font-black uppercase tracking-wider text-[#18181B] pb-3 border-b border-[#FAF0F1]">
            <span class="w-6 h-6 rounded-lg bg-[#FCECEE] text-[#700B1A] flex items-center justify-center font-black">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </span>
            <span>FORM INPUT MATCH SHEET CEPAT</span>
        </div>

        <!-- Top Inputs: Tournament, Match ID, Round, Duration -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-extrabold uppercase text-gray-500 mb-1">Turnamen Resmi</label>
                <select class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
                    <option value="MWI x EWC 2026">MWI x EWC 2026</option>
                    <option value="MPL ID S14">MPL ID S14</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold uppercase text-gray-500 mb-1">Match ID & Stage</label>
                <input type="text" value="MWI - G070" class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-bold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
            </div>
            <div>
                <label class="block text-[10px] font-extrabold uppercase text-gray-500 mb-1">Round / Fase</label>
                <input type="text" value="Grand Finals Game 4" class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
            </div>
            <div>
                <label class="block text-[10px] font-extrabold uppercase text-gray-500 mb-1">Durasi Match (MM:SS)</label>
                <input type="text" value="16:42" class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-mono font-bold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
            </div>
        </div>

        <!-- Blue Side vs Red Side Columns -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Blue Side Card -->
            <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-xs font-black text-sky-800 uppercase tracking-wider">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-600"></span>
                        <span>BLUE SIDE TEAM</span>
                    </div>
                    <span class="bg-white border border-[#F3E8E8] text-gray-500 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">First Pick Phase</span>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Nama Tim Blue</label>
                    <input type="text" value="Team Vitality" class="w-full bg-white border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-bold text-[#18181B] focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Draft Hero Picks (5 Heroes)</label>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="bg-white border border-[#F3E8E8] text-xs font-bold px-2.5 py-1 rounded-lg">Harith <span class="text-[9px] bg-[#FCECEE] text-[#700B1A] px-1 py-0.2 rounded font-black">Gold</span></span>
                        <span class="bg-white border border-[#F3E8E8] text-xs font-bold px-2.5 py-1 rounded-lg">Terizla <span class="text-[9px] bg-[#FCECEE] text-[#700B1A] px-1 py-0.2 rounded font-black">Exp</span></span>
                        <span class="bg-white border border-[#F3E8E8] text-xs font-bold px-2.5 py-1 rounded-lg">Baxia <span class="text-[9px] bg-[#FCECEE] text-[#700B1A] px-1 py-0.2 rounded font-black">Jungle</span></span>
                        <span class="bg-white border border-[#F3E8E8] text-xs font-bold px-2.5 py-1 rounded-lg">Yve <span class="text-[9px] bg-[#FCECEE] text-[#700B1A] px-1 py-0.2 rounded font-black">Mid</span></span>
                        <span class="bg-white border border-[#F3E8E8] text-xs font-bold px-2.5 py-1 rounded-lg">Tigreal <span class="text-[9px] bg-[#FCECEE] text-[#700B1A] px-1 py-0.2 rounded font-black">Roam</span></span>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Draft Hero Bans (5 Bans)</label>
                    <div class="flex items-center gap-1.5 flex-wrap text-[11px] font-semibold text-gray-600">
                        <span class="bg-white border border-[#F3E8E8] px-2 py-0.5 rounded">Fanny</span>
                        <span class="bg-white border border-[#F3E8E8] px-2 py-0.5 rounded">Ling</span>
                        <span class="bg-white border border-[#F3E8E8] px-2 py-0.5 rounded">Nolan</span>
                        <span class="bg-white border border-[#F3E8E8] px-2 py-0.5 rounded">Roger</span>
                        <span class="bg-white border border-[#F3E8E8] px-2 py-0.5 rounded">Zhuxin</span>
                    </div>
                </div>
            </div>

            <!-- Red Side Card -->
            <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-xs font-black text-rose-800 uppercase tracking-wider">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                        <span>RED SIDE TEAM</span>
                    </div>
                    <span class="bg-white border border-[#F3E8E8] text-gray-500 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Second Pick Phase</span>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Nama Tim Red</label>
                    <input type="text" value="Falcons Vega" class="w-full bg-white border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-bold text-[#18181B] focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Draft Hero Picks (5 Heroes)</label>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="bg-white border border-[#F3E8E8] text-xs font-bold px-2.5 py-1 rounded-lg">Karrie <span class="text-[9px] bg-[#FCECEE] text-[#700B1A] px-1 py-0.2 rounded font-black">Gold</span></span>
                        <span class="bg-white border border-[#F3E8E8] text-xs font-bold px-2.5 py-1 rounded-lg">Suyou <span class="text-[9px] bg-[#FCECEE] text-[#700B1A] px-1 py-0.2 rounded font-black">Jungle</span></span>
                        <span class="bg-white border border-[#F3E8E8] text-xs font-bold px-2.5 py-1 rounded-lg">Valentina <span class="text-[9px] bg-[#FCECEE] text-[#700B1A] px-1 py-0.2 rounded font-black">Mid</span></span>
                        <span class="bg-white border border-[#F3E8E8] text-xs font-bold px-2.5 py-1 rounded-lg">Edith <span class="text-[9px] bg-[#FCECEE] text-[#700B1A] px-1 py-0.2 rounded font-black">Exp</span></span>
                        <span class="bg-white border border-[#F3E8E8] text-xs font-bold px-2.5 py-1 rounded-lg">Rafaela <span class="text-[9px] bg-[#FCECEE] text-[#700B1A] px-1 py-0.2 rounded font-black">Roam</span></span>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">Draft Hero Bans (5 Bans)</label>
                    <div class="flex items-center gap-1.5 flex-wrap text-[11px] font-semibold text-gray-600">
                        <span class="bg-white border border-[#F3E8E8] px-2 py-0.5 rounded">Claude</span>
                        <span class="bg-white border border-[#F3E8E8] px-2 py-0.5 rounded">Marcel</span>
                        <span class="bg-white border border-[#F3E8E8] px-2 py-0.5 rounded">Mathilda</span>
                        <span class="bg-white border border-[#F3E8E8] px-2 py-0.5 rounded">Joy</span>
                        <span class="bg-white border border-[#F3E8E8] px-2 py-0.5 rounded">Hayabusa</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Result & Submit Row -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-3 border-t border-[#FAF0F1]">
            <div>
                <label class="block text-[10px] font-extrabold uppercase text-gray-500 mb-1.5">PEMENANG PERTANDINGAN (MATCH RESULT)</label>
                <div class="flex items-center gap-4 text-xs font-bold text-[#18181B]">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="match_winner" value="blue" checked class="text-[#700B1A] focus:ring-[#700B1A]">
                        <span>Blue Side (Team Vitality)</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="match_winner" value="red" class="text-[#700B1A] focus:ring-[#700B1A]">
                        <span>Red Side (Falcons Vega)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" class="bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] text-xs font-bold px-4 py-2 rounded-full transition">
                    Reset Form
                </button>
                <button type="button" class="bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-extrabold px-5 py-2 rounded-full transition shadow-sm flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan & Validasi Dataset</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 6. FILTER TOOLBAR -->
    <div class="card-custom p-4 flex flex-col md:flex-row items-center justify-between gap-3 flex-wrap">
        <!-- Search Input -->
        <div class="relative w-full md:w-80 flex-shrink-0">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input type="text" 
                   id="matchSearchInput" 
                   oninput="filterMatchTable()" 
                   placeholder="Cari Match ID, Tim, atau Hero..." 
                   class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 pl-9 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] transition">
        </div>

        <!-- Filter Dropdowns -->
        <div class="flex items-center gap-2 flex-wrap w-full md:w-auto justify-start md:justify-end">
            <select id="tournamentFilter" class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-700 text-xs font-bold rounded-xl px-3 py-2 focus:outline-none cursor-pointer">
                <option value="">MWI x EWC 2026</option>
            </select>
            <select id="patchFilter" class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-700 text-xs font-bold rounded-xl px-3 py-2 focus:outline-none cursor-pointer">
                <option value="">Patch 1.9.14</option>
            </select>
            <select id="winnerFilter" onchange="filterMatchTable()" class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-700 text-xs font-bold rounded-xl px-3 py-2 focus:outline-none cursor-pointer">
                <option value="">Semua Winner</option>
                <option value="blue">Blue Side Winner</option>
                <option value="red">Red Side Winner</option>
            </select>
            <button type="button" onclick="resetMatchFilter()" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-600 hover:text-[#700B1A] border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-bold flex items-center gap-1.5 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Reset Filter</span>
            </button>
        </div>
    </div>

    <!-- 7. MATCHES TABLE -->
    <div class="card-custom overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse" id="matchesTable">
                <thead>
                    <tr class="bg-[#FAF8F8] border-b border-[#F3E8E8] text-[10px] font-black uppercase text-gray-500 tracking-wider">
                        <th class="py-3 px-4 w-10 text-center">#</th>
                        <th class="py-3 px-4">MATCH ID</th>
                        <th class="py-3 px-4">TURNAMEN & FASE</th>
                        <th class="py-3 px-4">TEAMS (BLUE VS RED)</th>
                        <th class="py-3 px-4">DURASI</th>
                        <th class="py-3 px-4">WINNER</th>
                        <th class="py-3 px-4 min-w-[300px]">PICK SHEET (BLUE / RED)</th>
                        <th class="py-3 px-4">OBJECTIVES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F3E8E8]" id="matchesTbody">
                    @foreach($matches as $m)
                        <tr class="hover:bg-[#FAF8F8] transition match-row"
                            data-search="{{ strtolower(($m->match_id ?? $m['match_id']) . ' ' . ($m->tournament ?? $m['tournament']) . ' ' . ($m->phase ?? $m['phase']) . ' ' . ($m->team_blue ?? $m['team_blue']) . ' ' . ($m->team_red ?? $m['team_red']) . ' ' . ($m->blue_picks_str ?? '') . ' ' . ($m->red_picks_str ?? '')) }}"
                            data-winner="{{ strtolower($m->winner_side ?? $m['winner_side']) }}">
                            
                            <!-- Index -->
                            <td class="py-3.5 px-4 text-center font-bold text-gray-400">
                                {{ $m->index ?? $m['index'] }}
                            </td>

                            <!-- Match ID -->
                            <td class="py-3.5 px-4 font-mono font-black text-[#700B1A]">
                                {{ $m->match_id ?? $m['match_id'] }}
                            </td>

                            <!-- Tournament & Phase -->
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-[#18181B] text-xs leading-snug">
                                    {{ $m->tournament ?? $m['tournament'] }}
                                </div>
                                <div class="text-[10px] text-gray-500 font-medium">
                                    {{ $m->phase ?? $m['phase'] }}
                                </div>
                            </td>

                            <!-- Teams -->
                            <td class="py-3.5 px-4 text-xs font-semibold text-gray-700">
                                <span class="font-bold text-[#18181B]">{{ $m->team_blue ?? $m['team_blue'] }} (Blue)</span> 
                                <span class="text-gray-400 font-normal">vs</span> 
                                <span class="font-bold text-[#18181B]">{{ $m->team_red ?? $m['team_red'] }} (Red)</span>
                            </td>

                            <!-- Duration -->
                            <td class="py-3.5 px-4 font-mono font-bold text-gray-800">
                                {{ $m->duration ?? $m['duration'] }}
                            </td>

                            <!-- Winner -->
                            <td class="py-3.5 px-4">
                                <span class="bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] text-[10px] font-extrabold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1 shadow-sm whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#700B1A]"></span>
                                    <span>{{ $m->winner ?? $m['winner'] }}</span>
                                </span>
                            </td>

                            <!-- Pick Sheet -->
                            <td class="py-3.5 px-4 text-[11px] leading-tight space-y-1.5">
                                @if(is_array($m->blue_picks) && isset($m->blue_picks[0]['hero']))
                                    <!-- Blue Side Picks -->
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="font-extrabold text-sky-800 text-[10px] uppercase w-8">Blue:</span>
                                        @foreach($m->blue_picks as $bp)
                                            <div class="inline-flex items-center gap-1 bg-[#FAF8F8] border border-[#F3E8E8] rounded-md px-1.5 py-0.5 shadow-2xs">
                                                <img src="{{ $bp['portrait'] }}" 
                                                     alt="{{ $bp['hero'] }}" 
                                                     class="w-4 h-4 rounded-full object-cover bg-gray-100"
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($bp['hero']) }}&background=700B1A&color=fff';">
                                                <span class="font-bold text-[10px] text-gray-800">{{ $bp['hero'] }}</span>
                                                <img src="{{ $bp['spell_image'] }}" 
                                                     title="{{ $bp['spell'] }}" 
                                                     alt="{{ $bp['spell'] }}" 
                                                     class="w-3.5 h-3.5 rounded object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                    <!-- Red Side Picks -->
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="font-extrabold text-rose-800 text-[10px] uppercase w-8">Red:</span>
                                        @foreach($m->red_picks as $rp)
                                            <div class="inline-flex items-center gap-1 bg-[#FAF8F8] border border-[#F3E8E8] rounded-md px-1.5 py-0.5 shadow-2xs">
                                                <img src="{{ $rp['portrait'] }}" 
                                                     alt="{{ $rp['hero'] }}" 
                                                     class="w-4 h-4 rounded-full object-cover bg-gray-100"
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($rp['hero']) }}&background=700B1A&color=fff';">
                                                <span class="font-bold text-[10px] text-gray-800">{{ $rp['hero'] }}</span>
                                                <img src="{{ $rp['spell_image'] }}" 
                                                     title="{{ $rp['spell'] }}" 
                                                     alt="{{ $rp['spell'] }}" 
                                                     class="w-3.5 h-3.5 rounded object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-gray-700">
                                        <span class="font-extrabold text-sky-800">Blue:</span> {{ is_string($m->blue_picks) ? $m->blue_picks : $m->blue_picks_str }}
                                    </div>
                                    <div class="text-gray-700">
                                        <span class="font-extrabold text-rose-800">Red:</span> {{ is_string($m->red_picks) ? $m->red_picks : $m->red_picks_str }}
                                    </div>
                                @endif
                            </td>

                            <!-- Objectives & Map -->
                            <td class="py-3.5 px-4 font-mono text-[11px] text-gray-600 leading-tight">
                                <div class="flex items-center gap-2">
                                    @if(!empty($m->map_image))
                                        <img src="{{ $m->map_image }}" 
                                             alt="{{ $m->map }}" 
                                             title="{{ $m->map }}" 
                                             class="w-8 h-8 rounded-lg object-cover border border-[#F3E8E8] shadow-2xs flex-shrink-0">
                                    @endif
                                    <div>
                                        <div class="font-bold text-[10px] text-gray-800 font-sans truncate">{{ $m->map }}</div>
                                        <div class="text-[9px] text-gray-500">{{ $m->objectives_lord }}</div>
                                        <div class="text-[9px] text-gray-400">{{ $m->objectives_turtle }}</div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div class="p-4 border-t border-[#F3E8E8] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-semibold text-gray-500">
            <div>
                Menampilkan <span class="text-[#18181B] font-bold" id="matchVisibleCount">{{ count($matches) }}</span> total match sheet terverifikasi
            </div>

            <div class="flex items-center gap-1 font-bold text-xs">
                <button type="button" class="px-2.5 py-1 rounded-lg border border-[#F3E8E8] bg-white text-gray-500 hover:bg-[#FAF8F8]">Sebelumnya</button>
                <button type="button" class="w-7 h-7 rounded-lg bg-[#700B1A] text-white">1</button>
                <button type="button" class="w-7 h-7 rounded-lg border border-[#F3E8E8] bg-white text-gray-700 hover:bg-[#FAF8F8]">2</button>
                <button type="button" class="w-7 h-7 rounded-lg border border-[#F3E8E8] bg-white text-gray-700 hover:bg-[#FAF8F8]">3</button>
                <span class="px-1 text-gray-400">...</span>
                <button type="button" class="w-7 h-7 rounded-lg border border-[#F3E8E8] bg-white text-gray-700 hover:bg-[#FAF8F8]">12</button>
                <button type="button" class="px-2.5 py-1 rounded-lg border border-[#F3E8E8] bg-white text-gray-500 hover:bg-[#FAF8F8]">Selanjutnya</button>
            </div>
        </div>
    </div>

    <!-- 8. ADMIN FOOTER -->
    <footer class="border-t border-[#F3E8E8] pt-6 pb-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-semibold text-gray-500">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
            <span class="font-extrabold text-[#18181B] tracking-wider uppercase">METASCOUT : LAND OF DAWN</span>
            <span class="text-gray-300">•</span>
            <span class="text-gray-400">ADMIN PANEL</span>
        </div>
        <div class="flex items-center gap-4 uppercase tracking-wider text-[11px] text-gray-400 font-bold">
            <span>ALL TIMESTAMPS IN UTC+7</span>
            <span>•</span>
            <span class="text-[#700B1A] font-extrabold">VERSI 1.9.14</span>
        </div>
    </footer>

</div>

@push('scripts')
<script>
    function toggleInputForm() {
        const form = document.getElementById('quickInputForm');
        form.scrollIntoView({ behavior: 'smooth' });
    }

    function filterMatchTable() {
        const query = (document.getElementById('matchSearchInput').value || '').toLowerCase().trim();
        const winner = (document.getElementById('winnerFilter').value || '').toLowerCase().trim();

        const rows = document.querySelectorAll('.match-row');
        rows.forEach(r => {
            const search = r.dataset.search || '';
            const win = r.dataset.winner || '';

            const matchQuery = !query || search.includes(query);
            const matchWin = !winner || win === winner;

            if (matchQuery && matchWin) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    function resetMatchFilter() {
        document.getElementById('matchSearchInput').value = '';
        document.getElementById('winnerFilter').value = '';
        filterMatchTable();
    }

    function exportDatasetCSV() {
        const rows = document.querySelectorAll('#matchesTable tr');
        let csv = [];
        rows.forEach(r => {
            if (r.style.display === 'none') return;
            let cells = [];
            r.querySelectorAll('th, td').forEach(c => {
                let text = c.innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/\s+/g, ' ').trim();
                cells.push('"' + text.replace(/"/g, '""') + '"');
            });
            csv.push(cells.join(','));
        });
        const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'metascout_tournament_matches.csv';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
@endpush
@endsection
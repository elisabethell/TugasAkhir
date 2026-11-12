@extends('layouts.app')

@section('title', 'Admin Console Dashboard - MetaScout')
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
            <a href="{{ route('admin.dashboard') }}" class="bg-[#700B1A] text-white px-4 py-1.5 rounded-full transition shadow-sm">
                DASHBOARD
            </a>
            <a href="{{ route('matches') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
                DATASET PERTANDINGAN
            </a>
            <a href="{{ route('admin.heroes') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
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

    <!-- 2. BREADCRUMBS & TIMESTAMP -->
    <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase tracking-wider">
            <span>ADMIN</span>
            <span>/</span>
            <span class="text-[#700B1A] font-extrabold">DASHBOARD UTAMA</span>
            <span>•</span>
            <span class="flex items-center gap-1.5 text-gray-500">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                <span>DATABASE STATUS: MPL ID SEASON 18 (PATCH 1.9.14)</span>
            </span>
        </div>

        <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-full px-3 py-0.5 text-[11px] font-extrabold text-gray-600 uppercase tracking-wider">
            PATCH VERSI 1.9.14
        </div>
    </div>

    <!-- 3. PAGE HEADING & TOP ACTION BUTTONS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-[#18181B] tracking-tight uppercase">
                METASCOUT: ADMIN CONSOLE
            </h1>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.heroes') }}" class="bg-white hover:bg-[#FAF8F8] text-[#18181B] border border-[#F3E8E8] text-xs font-extrabold uppercase tracking-wider px-4 py-2.5 rounded-full shadow-sm flex items-center gap-1.5 transition">
                <span>+ TAMBAH HERO BARU</span>
            </a>
            <a href="{{ route('matches') }}" class="bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-extrabold uppercase tracking-wider px-4 py-2.5 rounded-full shadow-sm flex items-center gap-1.5 transition">
                <span>+ TAMBAH DATASET PERTANDINGAN</span>
            </a>
        </div>
    </div>

    <!-- 4. TWO SUMMARY METRIC CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Card 1: TOTAL MATCH DATASET -->
        <div class="card-custom p-6 space-y-3">
            <div class="text-[11px] font-extrabold text-gray-400 uppercase tracking-wider">
                TOTAL MATCH DATASET
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-[#18181B]">{{ $totalGames }}</span>
                <span class="text-lg font-bold text-gray-600 uppercase">GAME</span>
            </div>
            <div class="pt-2">
                <div class="flex items-center justify-between text-[11px] font-bold text-gray-500 mb-1.5">
                    <span>Fastest: <strong class="text-[#18181B]">{{ $fastestDuration }}</strong></span>
                    <span>Longest: <strong class="text-[#18181B]">{{ $longestDuration }}</strong></span>
                </div>
                <div class="w-full bg-[#FAF0F1] h-2 rounded-full overflow-hidden">
                    <div class="bg-[#700B1A] h-full rounded-full" style="width: 78%;"></div>
                </div>
            </div>
        </div>

        <!-- Card 2: HERO TERDAFTAR -->
        <div class="card-custom p-6 space-y-3">
            <div class="text-[11px] font-extrabold text-gray-400 uppercase tracking-wider">
                HERO TERDAFTAR
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-[#18181B]">{{ $totalHeroes }}</span>
                <span class="text-lg font-bold text-gray-600 uppercase">HERO</span>
            </div>
            <div class="pt-2">
                <div class="flex items-center justify-between text-[11px] font-bold text-gray-500 mb-1.5">
                    <span>Database Synchronized</span>
                    <span class="text-emerald-600 font-extrabold">Active 100%</span>
                </div>
                <div class="w-full bg-emerald-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-600 h-full rounded-full" style="width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. RIWAYAT ENTRI DATASET TERAKHIR (TABLE SECTION) -->
    <div class="card-custom p-6 space-y-4">
        <!-- Table Header & Search -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#700B1A]"></span>
                <h3 class="text-sm font-black uppercase tracking-wider text-[#18181B]">
                    RIWAYAT ENTRI DATASET TERAKHIR
                </h3>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" 
                           id="adminSearchInput" 
                           oninput="filterAdminMatches()" 
                           placeholder="Cari match ID, tim, atau turnamen..." 
                           class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-1.5 pl-8 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] transition">
                </div>
                <button type="button" 
                        onclick="resetAdminFilter()" 
                        class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-600 hover:text-[#700B1A] border border-[#F3E8E8] text-xs font-bold px-3 py-1.5 rounded-xl flex items-center gap-1 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-[#F3E8E8]">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-[#FAF8F8] border-b border-[#F3E8E8] text-gray-500 font-extrabold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-3 w-10 text-center">#</th>
                        <th class="py-3 px-3">MATCH ID</th>
                        <th class="py-3 px-3">TURNAMEN / FASE</th>
                        <th class="py-3 px-3">TEAMS (BLUE VS RED)</th>
                        <th class="py-3 px-3">DURASI</th>
                        <th class="py-3 px-3">WINNER</th>
                        <th class="py-3 px-3">PICK & BAN SHEET</th>
                        <th class="py-3 px-3 text-center">INPUT BY</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F3E8E8] font-medium" id="adminMatchesBody">
                    @foreach($matches as $index => $m)
                        <tr class="hover:bg-[#FAF8F8]/80 transition admin-match-row"
                            data-search="{{ strtolower(($m->match_id ?? '') . ' ' . ($m->tournament ?? '') . ' ' . ($m->team_blue ?? '') . ' ' . ($m->team_red ?? '') . ' ' . ($m->winner_team ?? '')) }}">
                            <td class="py-2 px-3 text-center text-gray-400 font-bold admin-match-idx align-middle">{{ $index + 1 }}</td>
                            <td class="py-2 px-3 font-extrabold text-[#700B1A] font-mono whitespace-nowrap align-middle">
                                {{ $m->match_id ?? 'MPL18-G' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-2 px-3 align-middle">
                                <div class="font-bold text-[#18181B] leading-tight">{{ $m->tournament ?? 'MPL Indonesia Season 18' }}</div>
                                <div class="text-[10px] text-gray-400 leading-tight">{{ $m->phase ?? ('Game ' . ($index + 1)) }}</div>
                            </td>
                            <td class="py-2 px-3 whitespace-nowrap align-middle">
                                <span class="font-bold text-[#18181B]">{{ $m->team_blue ?? 'Team Blue' }}</span>
                                <span class="bg-blue-100 text-blue-700 text-[9px] font-black px-1.5 py-0.5 rounded mx-1">BLUE</span>
                                <span class="text-gray-400 text-[10px] font-bold">vs</span>
                                <span class="bg-red-100 text-red-700 text-[9px] font-black px-1.5 py-0.5 rounded mx-1">RED</span>
                                <span class="font-bold text-[#18181B]">{{ $m->team_red ?? 'Team Red' }}</span>
                            </td>
                            <td class="py-2 px-3 font-mono font-bold text-gray-700 align-middle">
                                {{ $m->duration ?? '14:32' }}
                            </td>
                            <td class="py-2 px-3 align-middle">
                                <span class="font-bold text-emerald-700">
                                    {{ $m->winner_team ?? 'Winner' }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-[11px] max-w-[280px] align-middle">
                                <div class="truncate leading-tight">
                                    <strong class="text-gray-700">Pick:</strong> 
                                    <span class="text-gray-600">{{ $m->blue_picks_str ?? '-' }}</span>
                                </div>
                                <div class="truncate text-[10px] text-gray-400 leading-tight">
                                    <strong class="text-red-700">Ban:</strong> 
                                    <span>{{ $m->blue_bans_str ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-2 px-3 text-center align-middle">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#18181B] text-white text-[9px] font-black">
                                    {{ $index % 2 === 0 ? 'SA' : 'EK' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination (Limit 10 per page, Next / Prev) -->
        <div class="flex items-center justify-between pt-4 border-t border-[#F3E8E8] flex-wrap gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 font-medium">Tampilkan:</span>
                <select id="adminMatchPerPageSelect" onchange="changeAdminMatchPerPage(this.value)" class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-lg px-2.5 py-1 text-xs font-bold text-[#18181B] focus:outline-none focus:border-[#700B1A] cursor-pointer">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="all">Semua</option>
                </select>
                <span class="text-gray-300">|</span>
                <div class="text-gray-600 font-semibold" id="adminMatchPaginationInfo">
                    Menampilkan <span class="font-extrabold text-[#700B1A]" id="adminMatchPageStart">1</span> - <span class="font-extrabold text-[#700B1A]" id="adminMatchPageEnd">10</span> dari <span class="font-extrabold text-[#18181B]" id="adminMatchTotalRows">{{ $totalGames }}</span> game turnamen resmi
                </div>
            </div>

            <!-- Page Buttons (Prev, 1, 2, 3..., Next) -->
            <div class="flex items-center gap-1.5" id="adminMatchPaginationButtons">
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
    let currentAdminMatchPage = 1;
    let adminMatchPerPage = 10;

    function filterAdminMatches() {
        const query = (document.getElementById('adminSearchInput').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.admin-match-row');
        
        rows.forEach(r => {
            const text = (r.getAttribute('data-search') || r.innerText).toLowerCase();
            r.dataset.matched = (!query || text.includes(query)) ? 'true' : 'false';
        });

        currentAdminMatchPage = 1;
        updateAdminMatchPagination();
    }

    function resetAdminFilter() {
        document.getElementById('adminSearchInput').value = '';
        filterAdminMatches();
    }

    function changeAdminMatchPerPage(val) {
        adminMatchPerPage = val === 'all' ? 999999 : parseInt(val);
        currentAdminMatchPage = 1;
        updateAdminMatchPagination();
    }

    function goToAdminMatchPage(page) {
        currentAdminMatchPage = page;
        updateAdminMatchPagination();
    }

    function updateAdminMatchPagination() {
        const tbody = document.getElementById('adminMatchesBody');
        const allRows = Array.from(tbody.querySelectorAll('.admin-match-row'));
        const matchedRows = allRows.filter(r => r.dataset.matched !== 'false');
        const totalMatched = matchedRows.length;
        const totalPages = Math.ceil(totalMatched / adminMatchPerPage) || 1;

        if (currentAdminMatchPage > totalPages) currentAdminMatchPage = totalPages;
        if (currentAdminMatchPage < 1) currentAdminMatchPage = 1;

        const startIdx = (currentAdminMatchPage - 1) * adminMatchPerPage;
        const endIdx = startIdx + adminMatchPerPage;

        allRows.forEach(r => r.style.display = 'none');
        matchedRows.slice(startIdx, endIdx).forEach(r => r.style.display = '');

        // Update info text
        const startElem = document.getElementById('adminMatchPageStart');
        const endElem = document.getElementById('adminMatchPageEnd');
        const totalElem = document.getElementById('adminMatchTotalRows');
        if (startElem) startElem.textContent = totalMatched > 0 ? (startIdx + 1) : 0;
        if (endElem) endElem.textContent = Math.min(endIdx, totalMatched);
        if (totalElem) totalElem.textContent = totalMatched;

        // Render buttons
        const container = document.getElementById('adminMatchPaginationButtons');
        if (!container) return;
        container.innerHTML = '';

        if (totalPages <= 1) return;

        // Prev
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.disabled = currentAdminMatchPage === 1;
        prevBtn.className = `px-3 py-1 rounded-full text-xs font-bold border transition ${
            currentAdminMatchPage === 1 
                ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' 
                : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer shadow-2xs'
        }`;
        prevBtn.innerHTML = '&larr; Prev';
        prevBtn.onclick = () => goToAdminMatchPage(currentAdminMatchPage - 1);
        container.appendChild(prevBtn);

        // Page pills
        for (let p = 1; p <= totalPages; p++) {
            if (p === 1 || p === totalPages || (p >= currentAdminMatchPage - 1 && p <= currentAdminMatchPage + 1)) {
                const pageBtn = document.createElement('button');
                pageBtn.type = 'button';
                pageBtn.className = `w-7 h-7 rounded-full text-xs font-extrabold flex items-center justify-center transition ${
                    p === currentAdminMatchPage 
                        ? 'bg-[#700B1A] text-white shadow-sm' 
                        : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer'
                }`;
                pageBtn.textContent = p;
                pageBtn.onclick = () => goToAdminMatchPage(p);
                container.appendChild(pageBtn);
            } else if (p === currentAdminMatchPage - 2 || p === currentAdminMatchPage + 2) {
                const dots = document.createElement('span');
                dots.className = 'text-gray-400 px-1 font-bold text-xs';
                dots.textContent = '...';
                container.appendChild(dots);
            }
        }

        // Next
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.disabled = currentAdminMatchPage === totalPages;
        nextBtn.className = `px-3 py-1 rounded-full text-xs font-bold border transition ${
            currentAdminMatchPage === totalPages 
                ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' 
                : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer shadow-2xs'
        }`;
        nextBtn.innerHTML = 'Next &rarr;';
        nextBtn.onclick = () => goToAdminMatchPage(currentAdminMatchPage + 1);
        container.appendChild(nextBtn);
    }

    document.addEventListener('DOMContentLoaded', function() {
        filterAdminMatches();
    });
</script>
@endsection

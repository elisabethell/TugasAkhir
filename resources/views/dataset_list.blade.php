@extends('layouts.app')

@section('title', 'Dataset Pertandingan - MetaScout Admin')
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
                HOMEPAGE
            </a>
            <a href="{{ route('counter.picks') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
                COUNTER PICKS
            </a>
            <a href="{{ route('draft.analyzer') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition">
                REKOMENDASI DRAFT
            </a>
            <a href="{{ route('matches') }}" class="bg-[#700B1A] text-white px-4 py-1.5 rounded-full transition shadow-sm">
                DATASET ADMIN
            </a>
        </nav>

        <!-- Superadmin Info & Logout -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5 text-[11px] font-extrabold text-[#700B1A] uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>ADMIN CONSOLE</span>
            </div>
            <a href="{{ route('admin.logout') }}" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-800 hover:text-[#700B1A] border border-[#E5E7EB] text-[11px] font-extrabold uppercase px-3.5 py-1.5 rounded-full transition">
                LOGOUT
            </a>
        </div>
    </header>

    <!-- 2. FLASH NOTIFICATIONS -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-bold shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-emerald-600 text-base">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-bold shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-rose-600 text-base">⚠</span>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- 3. PAGE HEADING & SYNC ACTIONS -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                <span>ADMIN PANEL</span>
                <span>/</span>
                <span class="text-[#700B1A] font-extrabold">LOCAL ON-DEMAND STORE</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#18181B] tracking-tight uppercase">
                DATASET MATCH & MULTI-GAME SERIES
            </h1>
            <p class="text-xs text-gray-500 font-medium mt-1">
                Data turnamen tersimpan secara lokal di MySQL. Klik tombol <strong>Details</strong> pada setiap baris untuk membuka popup card detail game, durasi, jenis map, hero picks & bans.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Tombol Buka Modal Sync Liquipedia -->
            <button type="button" 
                    onclick="openSyncModal()" 
                    class="bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-full shadow-sm flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Sinkronisasi Data Liquipedia</span>
            </button>

            <!-- Tombol Seed Sample Mockup -->
            <form action="{{ route('admin.datasets.seed') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-white hover:bg-[#FAF8F8] text-gray-700 hover:text-[#700B1A] border border-[#F3E8E8] text-xs font-bold uppercase tracking-wider px-3.5 py-2.5 rounded-full shadow-sm flex items-center gap-1.5 transition cursor-pointer"
                        title="Seed sample data Bo7 Natus Vincere vs Bigetron & Bo5 Alter Ego vs ONIC">
                    <span>⚡ Reset Sample Mockup</span>
                </button>
            </form>
        </div>
    </div>

    <!-- 4. SUMMARY METRIC BOXES -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Seri Match -->
        <div class="card-custom p-5 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">
                    TOTAL SERI PERTANDINGAN
                </div>
                <div class="text-3xl font-black text-[#18181B] tracking-tight">
                    {{ $totalSeries }} <span class="text-xs font-bold text-gray-400">Match Series</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-full bg-[#FCECEE] flex items-center justify-center text-[#700B1A]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        </div>

        <!-- Total Game Individual -->
        <div class="card-custom p-5 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">
                    TOTAL GAME INDIVIDUAL (CHILD)
                </div>
                <div class="text-3xl font-black text-[#18181B] tracking-tight">
                    {{ $totalGames }} <span class="text-xs font-bold text-gray-400">Games</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-full bg-[#FCECEE] flex items-center justify-center text-[#700B1A]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>

        <!-- Status Sinkronisasi Lokal -->
        <div class="card-custom p-5 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">
                    PENGAMAN RATE LIMIT
                </div>
                <div class="text-sm font-black text-emerald-700 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>100% On-Demand Local Sync</span>
                </div>
                <p class="text-[10px] text-gray-400 mt-1 font-medium">
                    Tidak ada HTTP request saat dashboard / frontend dimuat.
                </p>
            </div>
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-base">
                🛡
            </div>
        </div>
    </div>

    <!-- 5. MAIN TABLE: TABEL LIST DATASET MATCH (DENGAN PAGINATION LIMIT) -->
    <div class="card-custom p-5 space-y-4">
        <!-- Table Header & Search -->
        <div class="flex items-center justify-between flex-wrap gap-3 pb-2 border-b border-[#F3E8E8]">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#700B1A]"></span>
                <h3 class="text-sm font-black uppercase tracking-wider text-[#18181B]">
                    TABEL LIST DATASET MATCH
                </h3>
            </div>

            <!-- Search Filter -->
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input type="text" 
                       id="datasetSearchInput" 
                       oninput="filterDatasetTable()" 
                       placeholder="Cari match, tim..." 
                       class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-full py-1.5 pl-8 pr-3 text-xs font-medium text-[#18181B] focus:outline-none focus:border-[#700B1A] transition">
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="datasetTable">
                <thead>
                    <tr class="border-b border-[#F3E8E8] text-[10px] font-black uppercase tracking-wider text-gray-400">
                        <th class="py-3 px-3">MATCH ID</th>
                        <th class="py-3 px-3">DATE & TIME</th>
                        <th class="py-3 px-4">MATCHUP & SKOR</th>
                        <th class="py-3 px-4">FORMAT / BABAK</th>
                        <th class="py-3 px-3 text-right">DETAILS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs">
                    @forelse($seriesMatches as $match)
                        <tr class="match-row hover:bg-[#FAF8F8]/80 transition group"
                            data-search="{{ strtolower($match->match_id . ' ' . $match->team_1_name . ' ' . $match->team_2_name . ' ' . $match->stage_name) }}">
                            
                            <!-- 1. Match ID -->
                            <td class="py-4 px-3 font-mono font-bold text-gray-500 text-[11px] whitespace-nowrap">
                                {{ $match->match_id }}
                            </td>

                            <!-- 2. Date & Time (WIB) -->
                            <td class="py-4 px-3 font-semibold text-gray-700 whitespace-nowrap">
                                {{ $match->formatted_date_time }}
                            </td>

                            <!-- 3. Matchup & Skor (Natus Vincere 3 - 4 Bigetron By Vitality) -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2.5">
                                    <!-- Team 1 -->
                                    <div class="flex items-center gap-2">
                                        @if(!empty($match->team_1_logo))
                                            <img src="{{ $match->team_1_logo }}" 
                                                 alt="{{ $match->team_1_name }}" 
                                                 class="w-6 h-6 object-contain flex-shrink-0"
                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($match->team_1_name) }}&background=700B1A&color=fff';">
                                        @endif
                                        <span class="font-extrabold {{ $match->team_1_score > $match->team_2_score ? 'text-[#18181B]' : 'text-gray-500' }}">
                                            {{ $match->team_1_name }}
                                        </span>
                                    </div>

                                    <!-- Score Badge -->
                                    <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-[#18181B] font-mono font-black px-2 py-0.5 rounded text-xs shadow-2xs">
                                        {{ $match->team_1_score }} - {{ $match->team_2_score }}
                                    </span>

                                    <!-- Team 2 -->
                                    <div class="flex items-center gap-2">
                                        <span class="font-extrabold {{ $match->team_2_score > $match->team_1_score ? 'text-[#18181B]' : 'text-gray-500' }}">
                                            {{ $match->team_2_name }}
                                        </span>
                                        @if(!empty($match->team_2_logo))
                                            <img src="{{ $match->team_2_logo }}" 
                                                 alt="{{ $match->team_2_name }}" 
                                                 class="w-6 h-6 object-contain flex-shrink-0"
                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($match->team_2_name) }}&background=700B1A&color=fff';">
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- 4. FORMAT / BABAK (Murni format dan babak, tanpa jenis map) -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="font-extrabold text-[#18181B] text-xs">
                                    {{ $match->best_of }} • {{ $match->stage_name ?: 'Playoffs' }}
                                </span>
                            </td>

                            <!-- 5. DETAILS Button -->
                            <td class="py-4 px-3 text-right">
                                <button type="button" 
                                        onclick="showMatchModal({{ $match->id }})" 
                                        class="bg-[#FAF8F8] hover:bg-[#700B1A] text-gray-800 hover:text-white border border-[#E5E7EB] hover:border-[#700B1A] px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider transition shadow-2xs inline-flex items-center gap-1.5 cursor-pointer">
                                    <span>DETAILS</span>
                                    <span class="text-[10px]">&rarr;</span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">
                                <p class="text-sm font-bold">Belum ada seri pertandingan di database lokal.</p>
                                <p class="text-xs mt-1">Klik tombol <strong>Sinkronisasi Data Liquipedia</strong> di kanan atas untuk menarik data turnamen.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 6. PAGINATION FOOTER BAR (Limit 20 per page, Next / Prev) -->
        <div class="flex items-center justify-between flex-wrap gap-3 pt-4 border-t border-[#F3E8E8] text-xs">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 font-medium">Tampilkan:</span>
                <select id="datasetPerPageSelect" onchange="changeDatasetPerPage(this.value)" class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-lg px-2.5 py-1 text-xs font-bold text-[#18181B] focus:outline-none focus:border-[#700B1A] cursor-pointer">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                    <option value="all">Semua</option>
                </select>
                <span class="text-gray-300">|</span>
                <div class="text-gray-600 font-semibold" id="datasetPaginationInfo">
                    Menampilkan <span class="font-extrabold text-[#700B1A]" id="dsPageStart">1</span> - <span class="font-extrabold text-[#700B1A]" id="dsPageEnd">20</span> dari <span class="font-extrabold text-[#18181B]" id="dsTotalRows">0</span> data seri match
                </div>
            </div>

            <!-- Page Buttons (Prev, 1, 2, 3..., Next) -->
            <div class="flex items-center gap-1.5" id="datasetPaginationButtons">
                <!-- Dynamically generated via JS -->
            </div>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- 7. MODAL POPUP LIQUIPEDIA MATCH DETAIL (SESUAI GAMBAR 2 & 3 USER)          -->
<!-- ========================================================================= -->
<div id="liquipediaMatchModal" 
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-xs hidden transition-opacity"
     onclick="closeMatchModalOnBackdrop(event)">
    
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full max-h-[92vh] overflow-y-auto border border-[#E5E7EB] relative animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Close Button Top Right -->
        <button type="button" 
                onclick="closeMatchModal()" 
                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 flex items-center justify-center font-black text-sm z-20 transition cursor-pointer">
            ✕
        </button>

        <!-- Modal Dynamic Container -->
        <div id="modalContentInner" class="p-5 sm:p-6 space-y-4">
            <!-- Diisi secara dinamis via JS persis seperti Gambar 2 & 3 -->
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- 8. MODAL SINKRONISASI API LIQUIPEDIA                                      -->
<!-- ========================================================================= -->
<div id="syncConfigModal" 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-[#E5E7EB] space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
            <h3 class="text-sm font-black uppercase text-[#18181B] tracking-wider flex items-center gap-2">
                <span>🔄</span>
                <span>Sinkronisasi Liquipedia DB API v3</span>
            </h3>
            <button type="button" onclick="closeSyncModal()" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
        </div>

        <p class="text-xs text-gray-500">
            Sistem akan melakukan 1 request terukur ke endpoint <code>GET /match</code> pada wiki <code>mobilelegends</code>, lalu menyimpan seluruh detail seri & game individual ke database MySQL lokal.
        </p>

        <form action="{{ route('admin.datasets.sync') }}" method="POST" class="space-y-3" onsubmit="submitSyncForm(this)">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">
                    Nama Halaman Turnamen (Pagename):
                </label>
                <input type="text" 
                       name="pagename" 
                       value="MPL/Indonesia/Season_18" 
                       required
                       class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">
                    Batas Maksimal Record (Limit):
                </label>
                <input type="number" 
                       name="limit" 
                       value="200" 
                       min="1" 
                       max="1000" 
                       required
                       class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-semibold text-[#18181B] focus:outline-none focus:border-[#700B1A]">
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" 
                        onclick="closeSyncModal()" 
                        class="px-4 py-2 rounded-full text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        id="btnSubmitSync"
                        class="px-5 py-2 rounded-full text-xs font-bold text-white bg-[#700B1A] hover:bg-[#550713] transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <span>Mulai Sinkronkan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Master data matches dan dictionary foto hero resmi dari data_hero.csv
    const seriesMatchesData = @json($seriesMatches);
    const heroPortraitsMap = @json($heroPortraitsMap ?? []);

    // State Pagination Tabel Match Dataset
    let currentDatasetPage = 1;
    let datasetPerPage = 20;

    function filterDatasetTable() {
        const query = (document.getElementById('datasetSearchInput').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.match-row');

        rows.forEach(row => {
            const text = row.getAttribute('data-search') || '';
            const isMatched = (!query || text.includes(query));
            row.dataset.matched = isMatched ? 'true' : 'false';
        });

        currentDatasetPage = 1;
        updateDatasetPagination();
    }

    function changeDatasetPerPage(val) {
        datasetPerPage = val === 'all' ? 999999 : parseInt(val);
        currentDatasetPage = 1;
        updateDatasetPagination();
    }

    function goToDatasetPage(page) {
        currentDatasetPage = page;
        updateDatasetPagination();
        const table = document.getElementById('datasetTable');
        if (table) {
            table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function updateDatasetPagination() {
        const allRows = Array.from(document.querySelectorAll('.match-row'));
        const matchedRows = allRows.filter(r => r.dataset.matched !== 'false');
        const totalMatched = matchedRows.length;
        const totalPages = Math.ceil(totalMatched / datasetPerPage) || 1;

        if (currentDatasetPage > totalPages) currentDatasetPage = totalPages;
        if (currentDatasetPage < 1) currentDatasetPage = 1;

        const startIdx = (currentDatasetPage - 1) * datasetPerPage;
        const endIdx = startIdx + datasetPerPage;

        allRows.forEach(r => r.style.display = 'none');
        matchedRows.slice(startIdx, endIdx).forEach(r => r.style.display = '');

        // Update Info Text
        const startElem = document.getElementById('dsPageStart');
        const endElem = document.getElementById('dsPageEnd');
        const totalElem = document.getElementById('dsTotalRows');
        if (startElem) startElem.textContent = totalMatched > 0 ? (startIdx + 1) : 0;
        if (endElem) endElem.textContent = Math.min(endIdx, totalMatched);
        if (totalElem) totalElem.textContent = totalMatched;

        // Render Pagination Buttons
        const container = document.getElementById('datasetPaginationButtons');
        if (!container) return;
        container.innerHTML = '';

        if (totalPages <= 1) return;

        // Prev Button
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.disabled = currentDatasetPage === 1;
        prevBtn.className = `px-3 py-1 rounded-full text-xs font-bold border transition ${
            currentDatasetPage === 1 
                ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' 
                : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer shadow-2xs'
        }`;
        prevBtn.innerHTML = '&larr; Prev';
        prevBtn.onclick = () => goToDatasetPage(currentDatasetPage - 1);
        container.appendChild(prevBtn);

        // Numbered Pages
        for (let p = 1; p <= totalPages; p++) {
            if (p === 1 || p === totalPages || (p >= currentDatasetPage - 1 && p <= currentDatasetPage + 1)) {
                const pageBtn = document.createElement('button');
                pageBtn.type = 'button';
                pageBtn.className = `w-7 h-7 rounded-full text-xs font-extrabold flex items-center justify-center transition ${
                    p === currentDatasetPage 
                        ? 'bg-[#700B1A] text-white shadow-sm' 
                        : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer'
                }`;
                pageBtn.textContent = p;
                pageBtn.onclick = () => goToDatasetPage(p);
                container.appendChild(pageBtn);
            } else if (p === currentDatasetPage - 2 || p === currentDatasetPage + 2) {
                const dots = document.createElement('span');
                dots.className = 'text-gray-400 px-1 font-bold text-xs';
                dots.textContent = '...';
                container.appendChild(dots);
            }
        }

        // Next Button
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.disabled = currentDatasetPage === totalPages;
        nextBtn.className = `px-3 py-1 rounded-full text-xs font-bold border transition ${
            currentDatasetPage === totalPages 
                ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' 
                : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer shadow-2xs'
        }`;
        nextBtn.innerHTML = 'Next &rarr;';
        nextBtn.onclick = () => goToDatasetPage(currentDatasetPage + 1);
        container.appendChild(nextBtn);
    }

    // Modal Helpers
    function openSyncModal() {
        document.getElementById('syncConfigModal').classList.remove('hidden');
    }

    function closeSyncModal() {
        document.getElementById('syncConfigModal').classList.add('hidden');
    }

    function submitSyncForm(form) {
        const btn = document.getElementById('btnSubmitSync');
        btn.disabled = true;
        btn.innerHTML = `<span class="animate-spin inline-block mr-1">↻</span> Menyinkronkan...`;
    }

    function closeMatchModalOnBackdrop(e) {
        if (e.target.id === 'liquipediaMatchModal') {
            closeMatchModal();
        }
    }

    function closeMatchModal() {
        document.getElementById('liquipediaMatchModal').classList.add('hidden');
    }

    // Toggle Bans Visibility di dalam modal
    let isBansVisible = true;
    function toggleBansVisibility() {
        isBansVisible = !isBansVisible;
        const container = document.getElementById('modalBansContainer');
        const btnText = document.getElementById('btnToggleBansText');
        const btnIcon = document.getElementById('btnToggleBansIcon');
        
        if (container) {
            container.style.display = isBansVisible ? 'block' : 'none';
        }
        if (btnText) {
            btnText.textContent = isBansVisible ? 'Hide' : 'Show';
        }
        if (btnIcon) {
            btnIcon.textContent = isBansVisible ? '🙈' : '👁️';
        }
    }

    /**
     * Resolusi URL foto hero resmi langsung dari database/data/data_hero.csv
     */
    function resolveHeroImg(heroName, existingUrl) {
        if (existingUrl && existingUrl.startsWith('http') && !existingUrl.includes('ui-avatars.com') && !existingUrl.includes('deviantart') && !existingUrl.includes('placeholder')) {
            return existingUrl;
        }
        const clean = (heroName || '').toLowerCase().replace(/[^a-z0-9]/g, '');
        if (heroPortraitsMap[clean]) {
            return heroPortraitsMap[clean];
        }
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(heroName || 'Hero')}&background=700B1A&color=fff`;
    }

    /**
     * Helper icon dan nama jenis map persis sesuai Gambar 2 & 3
     */
    function renderMapBadge(mapName) {
        const raw = (mapName || 'Healing Turtle').trim();
        const lower = raw.toLowerCase();
        let iconSvg = '';

        if (lower.includes('cloud')) {
            // Icon Black Cloud (Flying Cloud)
            iconSvg = `<svg class="w-4 h-4 text-black inline-block flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/></svg>`;
        } else if (lower.includes('turret')) {
            // Icon Golden Turret
            iconSvg = `<svg class="w-4 h-4 text-gray-800 inline-block flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L8 6v2H6v4h2v2H6v6h12v-6h-2v-2h2v-4h-2V8h-2V6l-4-4zm-1 8h2v2h-2v-2zm0 4h2v2h-2v-2z"/></svg>`;
        } else if (lower.includes('turtle')) {
            // Icon Healing Turtle
            iconSvg = `<svg class="w-4 h-4 text-black inline-block flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 2c3.5 0 6.47 2.27 7.52 5.43L15 11l-3-3-3 3-4.52-1.57C5.53 6.27 8.5 4 12 4zm-7.93 7.85l4.31 1.5-1.5 4.31C5.1 16.32 4.14 14.3 4.07 11.85zm3.76 6.88l1.5-4.31L12 15l2.67 1.42 1.5 4.31c-1.3.62-2.73.97-4.17.97s-2.87-.35-4.17-.97zm9.35-.97l-1.5-4.31 4.31-1.5c-.07 2.45-1.03 4.47-2.81 5.81zM19.93 11.85c-.07-.75-.24-1.46-.5-2.12l-4.26 1.48 1.48 4.26c1.82-1.01 3.09-2.77 3.28-4.62z"/></svg>`;
        } else if (lower.includes('wisp')) {
            // Icon Revealing Wisps
            iconSvg = `<svg class="w-4 h-4 text-black inline-block flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l1.8 5.4 5.4 1.8-5.4 1.8-1.8 5.4-1.8-5.4-5.4-1.8 5.4-1.8zM4.5 16.5l.9 2.7 2.7.9-2.7.9-.9 2.7-.9-2.7-2.7-.9 2.7-.9zM18.5 15.5l.9 2.7 2.7.9-2.7.9-.9 2.7-.9-2.7-2.7-.9 2.7-.9z"/></svg>`;
        } else {
            iconSvg = `<span class="text-sm">🌐</span>`;
        }

        return `
            <div class="flex items-center justify-center gap-1.5 text-[11px] sm:text-xs font-bold text-[#0260a8] hover:underline cursor-pointer mt-0.5">
                ${iconSvg}
                <span>${raw}</span>
            </div>
        `;
    }

    /**
     * Menampilkan Modal Detail Pertandingan Persis Sesuai Mockup Gambar 2 & Gambar 3
     * DENGAN FILTER CEGAH DUPLIKASI GAME DAN ANTI-EMPTY BANS ROW
     */
    function showMatchModal(matchId) {
        const match = seriesMatchesData.find(m => m.id === matchId);
        if (!match) return;

        const container = document.getElementById('modalContentInner');

        // Pemenang seri
        const team1IsWinner = match.team_1_score > match.team_2_score;

        // 1. Header Waktu (November 8, 2026 - 17:30 WIB)
        let html = `
            <div class="flex flex-col items-center justify-center space-y-3">
                <div class="bg-[#f1f3f5] text-gray-800 text-[11px] font-extrabold px-3 py-1 rounded shadow-2xs inline-block tracking-wide">
                    ${match.formatted_date_time || 'November 8, 2026 - 17:30 WIB'}
                </div>

                <!-- 2. Header Skor Seri (Natus Vincere 3 : 4 Bigetron By Vitality) -->
                <div class="w-full flex items-center justify-between px-2 sm:px-4">
                    <!-- Tim Kiri -->
                    <div class="flex items-center gap-2.5 flex-1 justify-start">
                        <span class="font-black text-base sm:text-lg text-[#0260a8] tracking-tight truncate max-w-[130px] sm:max-w-[180px]">
                            ${match.team_1_name}
                        </span>
                        ${match.team_1_logo ? `<img src="${match.team_1_logo}" alt="${match.team_1_name}" class="w-8 h-8 sm:w-9 sm:h-9 object-contain flex-shrink-0">` : ''}
                    </div>

                    <!-- Center Score -->
                    <div class="text-center px-3 flex-shrink-0">
                        <div class="flex items-center justify-center gap-1.5 text-xl sm:text-2xl font-black text-[#18181B] tracking-tight">
                            ${team1IsWinner ? '<span class="text-xs text-gray-700">◀</span>' : ''}
                            <span>${match.team_1_score}</span>
                            <span class="text-gray-400 font-light">:</span>
                            <span>${match.team_2_score}</span>
                            ${!team1IsWinner ? '<span class="text-xs text-gray-700">▶</span>' : ''}
                        </div>
                        <div class="text-[11px] font-bold text-gray-500 mt-0.5">
                            (${match.best_of || 'Bo7'})
                        </div>
                    </div>

                    <!-- Tim Kanan -->
                    <div class="flex items-center gap-2.5 flex-1 justify-end">
                        ${match.team_2_logo ? `<img src="${match.team_2_logo}" alt="${match.team_2_name}" class="w-8 h-8 sm:w-9 sm:h-9 object-contain flex-shrink-0">` : ''}
                        <span class="font-black text-base sm:text-lg text-[#0260a8] tracking-tight truncate max-w-[130px] sm:max-w-[180px]">
                            ${match.team_2_name}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-100 my-1"></div>
        `;

        // 3. Daftar Baris Game (Game 1 s/d Game 7 - Sesuai Gambar 2 & 3)
        html += `<div class="space-y-3.5">`;

        const rawGames = match.games || [];
        // Urutkan game secara teratur berdasarkan game_number (1, 2, 3...)
        const games = [...rawGames].sort((a, b) => (parseInt(a.game_number) || 0) - (parseInt(b.game_number) || 0));
        const seenPickGameNums = new Set();

        games.forEach((game, gIdx) => {
            const team1Details = (game.details || []).filter(d => d.team_position === 1 && d.type === 'pick');
            const team2Details = (game.details || []).filter(d => d.team_position === 2 && d.type === 'pick');

            // Lewati jika kedua tim tidak punya pick
            if (team1Details.length === 0 && team2Details.length === 0) return;

            // Cegah duplikasi nomor game
            const gNum = game.game_number || (gIdx + 1);
            if (seenPickGameNums.has(gNum)) return;
            seenPickGameNums.add(gNum);

            const team1Won = (game.team_1_status === 'W');
            const team2Won = (game.team_2_status === 'W');

            const t1Badge = team1Won 
                ? `<span class="w-6 h-6 rounded bg-[#dcfce7] text-[#16a34a] border border-[#bbf7d0] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">W</span>`
                : `<span class="w-6 h-6 rounded bg-[#fee2e2] text-[#dc2626] border border-[#fecaca] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">L</span>`;

            const t2Badge = team2Won 
                ? `<span class="w-6 h-6 rounded bg-[#dcfce7] text-[#16a34a] border border-[#bbf7d0] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">W</span>`
                : `<span class="w-6 h-6 rounded bg-[#fee2e2] text-[#dc2626] border border-[#fecaca] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">L</span>`;

            // Helper hero avatar picks (Rounded square dengan border solid merah/biru sesuai Gambar 2)
            const renderHeroPicks = (details, sideColor) => {
                let hHtml = `<div class="flex items-center gap-1">`;
                details.forEach(d => {
                    const portrait = resolveHeroImg(d.hero_name, d.hero_portrait);
                    const borderColor = sideColor === 'Red' ? 'border-[#ef4444]' : 'border-[#3b82f6]';

                    hHtml += `
                        <div class="relative group/h flex-shrink-0">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-md border-2 ${borderColor} overflow-hidden bg-gray-100 shadow-2xs">
                                <img src="${portrait}" 
                                     alt="${d.hero_name}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; const cl = '${d.hero_name.toLowerCase().replace(/[^a-z0-9]/g, '')}'; if (heroPortraitsMap[cl]) { this.src = heroPortraitsMap[cl]; } else { this.src = 'https://ui-avatars.com/api/?name=${encodeURIComponent(d.hero_name)}&background=700B1A&color=fff'; }">
                            </div>
                            <!-- Floating Tooltip Nama Hero -->
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1.5 hidden group-hover/h:block bg-gray-900 text-white text-[10px] font-extrabold px-2 py-0.5 rounded shadow-lg whitespace-nowrap z-40 pointer-events-none">
                                ${d.hero_name}
                            </div>
                        </div>
                    `;
                });
                hHtml += `</div>`;
                return hHtml;
            };

            const t1PicksHtml = renderHeroPicks(team1Details, game.team_1_side || 'Red');
            const t2PicksHtml = renderHeroPicks(team2Details, game.team_2_side || 'Blue');

            html += `
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-gray-100 last:border-b-0">
                    <!-- Left: W/L badge + 5 hero picks Tim 1 -->
                    <div class="flex items-center gap-2">
                        ${t1Badge}
                        ${t1PicksHtml}
                    </div>

                    <!-- Center: Durasi Match & Nama Jenis Map (Sesuai Gambar 2 & 3) -->
                    <div class="text-center px-1 flex-shrink-0">
                        <div class="font-mono font-black text-xs sm:text-sm text-gray-900">
                            ${game.duration || '14:40'}
                        </div>
                        ${renderMapBadge(game.map_type)}
                    </div>

                    <!-- Right: 5 hero picks Tim 2 + W/L badge -->
                    <div class="flex items-center gap-2">
                        ${t2PicksHtml}
                        ${t2Badge}
                    </div>
                </div>
            `;
        });

        html += `</div>`;

        // 4. Banner MVP (Highlight Kuning - Persis Gambar 3: MVP: Shogun)
        const mvpName = match.series_mvp || 'Shogun';
        html += `
            <div class="bg-[#fef9c3] border border-[#fef08a] rounded-lg py-2 px-4 text-center shadow-2xs mt-3">
                <span class="font-black text-gray-900 text-xs sm:text-sm">MVP:</span>
                <span class="font-black text-[#0260a8] text-xs sm:text-sm ml-1 hover:underline cursor-pointer">${mvpName}</span>
            </div>
        `;

        // 5. Bagian Bans (dengan Toggle Show/Hide - Persis Gambar 3)
        // MENGHAPUS DUPLIKASI DAN MENGHAPUS BARIS KOSONG GAME 1
        html += `
            <div class="bg-[#fafafa] border border-gray-200 rounded-xl p-3 sm:p-4 mt-3">
                <!-- Header Bans -->
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-200">
                    <span class="w-14"></span>
                    <span class="font-black text-xs uppercase tracking-wider text-gray-800">Bans</span>
                    <button type="button" 
                            onclick="toggleBansVisibility()" 
                            class="bg-white hover:bg-gray-100 border border-gray-300 text-gray-700 text-[10px] font-bold px-2.5 py-1 rounded-md flex items-center gap-1 transition shadow-2xs cursor-pointer">
                        <span id="btnToggleBansIcon">🙈</span>
                        <span id="btnToggleBansText">Hide</span>
                    </button>
                </div>

                <!-- Bans Rows Container (Game 1 s/d Game 7) -->
                <div id="modalBansContainer" class="space-y-3">
        `;

        const seenBanGameNumbers = new Set();

        games.forEach((game, gIdx) => {
            const team1Bans = (game.details || []).filter(d => d.team_position === 1 && d.type === 'ban');
            const team2Bans = (game.details || []).filter(d => d.team_position === 2 && d.type === 'ban');

            // 1. LEWATI JIKA KEDUA TIM TIDAK MEMILIKI HERO BANS SAMA SEKALI
            if (team1Bans.length === 0 && team2Bans.length === 0) {
                return;
            }

            // 2. CEGAH DUPLIKASI NOMOR GAME (MENCEGAH DOUBLE GAME 1)
            const gNum = game.game_number || (gIdx + 1);
            if (seenBanGameNumbers.has(gNum)) {
                return;
            }
            seenBanGameNumbers.add(gNum);

            const renderHeroBans = (details, alignEnd = false) => {
                let bHtml = `<div class="flex items-center gap-1 ${alignEnd ? 'justify-end' : 'justify-start'} min-w-[125px] sm:min-w-[155px]">`;
                details.forEach(d => {
                    const portrait = resolveHeroImg(d.hero_name, d.hero_portrait);
                    bHtml += `
                        <div class="relative group/b flex-shrink-0">
                            <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-md border border-gray-300 overflow-hidden bg-gray-100 shadow-2xs">
                                <img src="${portrait}" 
                                     alt="${d.hero_name}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; const cl = '${d.hero_name.toLowerCase().replace(/[^a-z0-9]/g, '')}'; if (heroPortraitsMap[cl]) { this.src = heroPortraitsMap[cl]; } else { this.src = 'https://ui-avatars.com/api/?name=${encodeURIComponent(d.hero_name)}&background=700B1A&color=fff'; }">
                            </div>
                            <!-- Floating Tooltip Nama Hero (e.g. Freya) -->
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1.5 hidden group-hover/b:block bg-gray-900/90 text-white text-[10px] font-extrabold px-2 py-0.5 rounded shadow-lg whitespace-nowrap z-50 pointer-events-none">
                                ${d.hero_name}
                            </div>
                        </div>
                    `;
                });
                bHtml += `</div>`;
                return bHtml;
            };

            html += `
                <div class="flex items-center justify-between gap-2">
                    <!-- Left: 5 Bans Tim 1 -->
                    ${renderHeroBans(team1Bans, false)}

                    <!-- Center: Game X text -->
                    <span class="text-[11px] font-semibold text-gray-600 px-2 flex-shrink-0 text-center min-w-[50px]">
                        Game ${gNum}
                    </span>

                    <!-- Right: 5 Bans Tim 2 -->
                    ${renderHeroBans(team2Bans, true)}
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;

        container.innerHTML = html;
        isBansVisible = true;
        document.getElementById('liquipediaMatchModal').classList.remove('hidden');
    }

    // Inisialisasi Pagination saat halaman pertama kali dibuka
    document.addEventListener('DOMContentLoaded', function() {
        filterDatasetTable();
    });
</script>
@endpush
@endsection

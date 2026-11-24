@extends('layouts.app')

@section('title', 'Admin Console Dashboard - MetaScout')
@section('hide_navbar', true)
@section('hide_footer', true)

@section('content')
<div class="space-y-6">

    <!-- 1. ADMIN TOP NAVIGATION BAR -->
    <header class="bg-white border border-[#F3E8E8] rounded-full px-4 lg:px-5 py-2 flex items-center justify-between shadow-sm flex-nowrap gap-3 w-full">
        <!-- Brand Logo & Liquipedia Badge -->
        <div class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-[#700B1A] inline-block shadow-[0_0_8px_rgba(112,11,26,0.5)]"></span>
                <span class="font-black text-sm tracking-widest text-[#18181B] uppercase">METASCOUT</span>
            </a>

            <span class="text-gray-300 font-light select-none">|</span>

            <a href="https://liquipedia.net/mobilelegends" target="_blank" rel="noopener noreferrer" 
               class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#F5F8FC] hover:bg-[#EBF2FA] border border-[#D0E1F5] transition group shadow-2xs"
               title="Data Pertandingan Resmi Bersumber dari Liquipedia API">
                <img src="/images/liquipedia_logo.svg" alt="Liquipedia Logo" class="w-4 h-4 object-contain">
                <span class="font-black text-[11px] text-[#0A285F] group-hover:text-[#0072CE] tracking-wider uppercase">LIQUIPEDIA</span>
            </a>
        </div>

        <!-- Admin Links -->
        <nav class="flex items-center gap-1.5 sm:gap-2 flex-nowrap text-xs font-bold uppercase tracking-wider flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="bg-[#700B1A] text-white px-4 py-1.5 rounded-full transition shadow-sm whitespace-nowrap">
                DASHBOARD
            </a>
            <a href="{{ route('matches') }}" class="text-gray-600 hover:text-[#700B1A] hover:bg-[#FCECEE] px-3.5 py-1.5 rounded-full transition whitespace-nowrap">
 window
            </a>
        </nav>

        <!-- Superadmin Info & Logout -->
        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
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
            <a href="{{ route('matches') }}" class="bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-extrabold uppercase tracking-wider px-4 py-2.5 rounded-full shadow-sm flex items-center gap-1.5 transition">
                <span>+ KELOLA DATASET PERTANDINGAN</span>
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
                HERO DATABASE (API LIQUIPEDIA)
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-[#18181B]">{{ $totalHeroes }}</span>
                <span class="text-lg font-bold text-gray-600 uppercase">HERO</span>
            </div>
            <div class="pt-2">
                <div class="flex items-center justify-between text-[11px] font-bold text-gray-500 mb-1.5">
                    <span>Sinkronisasi Otomatis</span>
                    <span class="text-emerald-600 font-extrabold">Active 100%</span>
                </div>
                <div class="w-full bg-emerald-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-600 h-full rounded-full" style="width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. RIWAYAT ENTRI DATASET TERAKHIR (IDENTIK DENGAN TABEL LIST DATASET MATCH) -->
    <div class="card-custom p-4 sm:p-6 space-y-4">
        <!-- Table Header & Action Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#F3E8E8]">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#700B1A]"></span>
                <h3 class="text-sm font-black uppercase tracking-wider text-[#18181B]">
                    RIWAYAT ENTRI DATASET TERAKHIR
                </h3>
            </div>

            <!-- Controls: Filter Babak & Tombol Lihat Lebih Lengkap -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- Filter Babak (Playoffs, Regular Season Week 1, dst) -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-gray-500 uppercase tracking-wider">BABAK:</span>
                    <select id="adminStageFilter" 
                            onchange="filterAdminByStage(this.value)" 
                            class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl px-3 py-1.5 text-xs font-extrabold text-[#18181B] focus:outline-none focus:border-[#700B1A] transition cursor-pointer shadow-2xs">
                        <option value="all" selected>Semua Babak (All)</option>
                        <optgroup label="Playoffs">
                            <option value="playoffs">Playoffs (Semua)</option>
                            <option value="grand-final">Grand Final Playoffs</option>
                            <option value="lower-bracket-final">Lower Bracket Final</option>
                            <option value="upper-bracket-final">Upper Bracket Final</option>
                        </optgroup>
                        <optgroup label="Regular Season">
                            <option value="regular-season">Regular Season (Semua)</option>
                            <option value="week-1">Week 1 Regular Season</option>
                            <option value="week-2">Week 2 Regular Season</option>
                            <option value="week-3">Week 3 Regular Season</option>
                            <option value="week-4">Week 4 Regular Season</option>
                            <option value="week-5">Week 5 Regular Season</option>
                            <option value="week-6">Week 6 Regular Season</option>
                            <option value="week-7">Week 7 Regular Season</option>
                            <option value="week-8">Week 8 Regular Season</option>
                            <option value="week-9">Week 9 Regular Season</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Tombol Lihat Lebih Lengkap di Page Dataset Pertandingan -->
                <a href="{{ route('matches') }}" 
                   class="bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-bold px-3.5 py-1.5 rounded-full flex items-center gap-1.5 transition shadow-2xs">
                    <span>Lihat Lebih Lengkap di Page Dataset Pertandingan</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Table Container with Internal Scroll (Limit 6-7 rows visible + scroll) -->
        <div class="overflow-x-auto overflow-y-auto max-h-[460px] rounded-xl border border-[#F3E8E8] shadow-2xs">
            <table class="w-full text-left border-collapse min-w-[760px]" id="adminDatasetTable">
                <thead class="sticky top-0 bg-[#FAF8F8] z-10 shadow-2xs">
                    <tr class="border-b border-[#F3E8E8] text-[10px] font-black uppercase tracking-wider text-gray-400 select-none">
                        <th class="py-3 px-3 w-40">MATCH ID</th>
                        <th class="py-3 px-3 w-32">DATE & TIME</th>
                        <th class="py-3 px-4 min-w-[320px] text-center">MATCHUP & SKOR</th>
                        <th class="py-3 px-4 w-40">FORMAT / BABAK</th>
                        <th class="py-3 px-3 w-16 text-center">DETAILS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs" id="adminMatchesBody">
                    @forelse($seriesMatches as $match)
                        <tr class="admin-match-row hover:bg-[#FAF8F8]/80 transition group"
                            data-stage="{{ strtolower($match->formatted_stage_name) }}"
                            data-search="{{ strtolower($match->match_id . ' ' . $match->team_1_name . ' ' . $match->team_2_name . ' ' . $match->formatted_stage_name) }}">
                            
                            <!-- 1. Match ID -->
                            <td class="py-3.5 px-3 font-mono font-bold text-gray-500 text-[11px] whitespace-nowrap">
                                {{ $match->match_id }}
                            </td>

                            <!-- 2. Date & Time (Simple: 2 lines) -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <div class="font-extrabold text-gray-800 text-xs tracking-tight">
                                    {{ $match->simple_date }}
                                </div>
                                <div class="text-[11px] font-mono font-medium text-gray-400">
                                    {{ $match->simple_time }}
                                </div>
                            </td>

                            <!-- 3. Matchup & Skor (Balanced Layout) -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center justify-center gap-2 sm:gap-2.5">
                                    <!-- Team 1 (Right aligned) -->
                                    <div class="flex items-center gap-1.5 flex-1 justify-end min-w-0">
                                        <span class="font-black text-xs sm:text-sm text-right whitespace-nowrap {{ $match->team_1_score > $match->team_2_score ? 'text-[#18181B]' : 'text-gray-500' }}"
                                              title="{{ $match->team_1_name }}">
                                            {{ $match->team_1_name }}
                                        </span>
                                        @if(!empty($match->team_1_logo))
                                            <img src="{{ $match->team_1_logo }}" 
                                                 alt="{{ $match->team_1_name }}" 
                                                 referrerpolicy="no-referrer"
                                                 loading="lazy"
                                                 class="w-5 h-5 sm:w-6 sm:h-6 object-contain flex-shrink-0"
                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($match->team_1_name) }}&background=700B1A&color=fff&size=64';">
                                        @endif
                                        <img src="/images/teams/{{ strtolower($match->team_a_name) }}.png" 
                                             alt="{{ $match->team_1_name }}" 
                                             referrerpolicy="no-referrer"
                                             loading="lazy"
                                             class="w-5 h-5 sm:w-6 sm:h-6 object-contain flex-shrink-0"
                                             onerror="this.onerror=null; this.src='{{ $match->team_1_logo ?: 'https://ui-avatars.com/api/?name=' . urlencode($match->team_1_name) . '&background=700B1A&color=fff&size=64' }}';">
                                    </div>

                                    <!-- Score Badge (Pill, never wraps) -->
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center bg-[#FAF8F8] border border-[#F3E8E8] text-[#18181B] font-mono font-black px-2.5 py-0.5 rounded-md text-xs shadow-2xs whitespace-nowrap tracking-wider">
                                            {{ $match->team_1_score }} - {{ $match->team_2_score }}
                                        </span>
                                    </div>

                                    <!-- Team 2 (Left aligned) -->
                                    <div class="flex items-center gap-1.5 flex-1 justify-start min-w-0">
                                        @if(!empty($match->team_2_logo))
                                            <img src="{{ $match->team_2_logo }}" 
                                                 alt="{{ $match->team_2_name }}" 
                                                 referrerpolicy="no-referrer"
                                                 loading="lazy"
                                                 class="w-5 h-5 sm:w-6 sm:h-6 object-contain flex-shrink-0"
                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($match->team_2_name) }}&background=700B1A&color=fff&size=64';">
                                        @endif
                                        <img src="/images/teams/{{ strtolower($match->team_b_name) }}.png" 
                                             alt="{{ $match->team_2_name }}" 
                                             referrerpolicy="no-referrer"
                                             loading="lazy"
                                             class="w-5 h-5 sm:w-6 sm:h-6 object-contain flex-shrink-0"
                                             onerror="this.onerror=null; this.src='{{ $match->team_2_logo ?: 'https://ui-avatars.com/api/?name=' . urlencode($match->team_2_name) . '&background=700B1A&color=fff&size=64' }}';">
                                        <span class="font-black text-xs sm:text-sm text-left whitespace-nowrap {{ $match->team_2_score > $match->team_1_score ? 'text-[#18181B]' : 'text-gray-500' }}"
                                              title="{{ $match->team_2_name }}">
                                            {{ $match->team_2_name }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- 4. FORMAT / BABAK (Top: BO7, Bottom: Stage Name - Grand Final vs Lower Bracket Final) -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="leading-tight">
                                    <span class="font-black text-[#18181B] text-xs uppercase tracking-wider block">
                                        {{ strtoupper($match->best_of ?: 'BO7') }}
                                    </span>
                                    <span class="text-[11px] font-semibold text-gray-500 block mt-0.5">
                                        {{ $match->formatted_stage_name }}
                                    </span>
                                </div>
                            </td>

                            <!-- 5. DETAILS Button (Titik Tiga Horizontal) -->
                            <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                <button type="button" 
                                        onclick="showMatchModal({{ $match->id }})" 
                                        class="w-8 h-8 rounded-full bg-[#FAF8F8] hover:bg-[#700B1A] text-gray-500 hover:text-white border border-[#E5E7EB] hover:border-[#700B1A] inline-flex items-center justify-center transition shadow-2xs cursor-pointer"
                                        title="Lihat Detail Pertandingan">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="5" cy="12" r="2" />
                                        <circle cx="12" cy="12" r="2" />
                                        <circle cx="19" cy="12" r="2" />
                                    </svg>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">
                                <p class="text-sm font-bold">Belum ada seri pertandingan di database lokal.</p>
                                <p class="text-xs mt-1">Buka <a href="{{ route('matches') }}" class="text-[#700B1A] font-extrabold hover:underline">Kelola Dataset Pertandingan</a> untuk melakukan sinkronisasi data.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (Default Limit 7 per page, Next / Prev) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pt-4 border-t border-[#F3E8E8] gap-3 text-xs">
            <div class="flex items-center gap-2.5 flex-wrap">
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-gray-500 font-bold text-xs whitespace-nowrap">Tampilkan:</span>
                    <select id="adminMatchPerPageSelect" onchange="changeAdminMatchPerPage(this.value)" class="bg-[#FAF8F8] border border-[#E5E7EB] rounded-lg px-2.5 py-1 text-xs font-extrabold text-[#18181B] focus:outline-none focus:border-[#700B1A] transition cursor-pointer shadow-2xs">
                        <option value="7" selected>7</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">Semua</option>
                    </select>
                </div>
                <span class="text-gray-300 font-light select-none hidden sm:inline">|</span>
                <div class="text-gray-600 font-medium text-xs whitespace-nowrap" id="adminMatchPaginationInfo">
                    Menampilkan <span class="font-extrabold text-[#700B1A]" id="adminMatchPageStart">1</span> - <span class="font-extrabold text-[#700B1A]" id="adminMatchPageEnd">7</span> dari <span class="font-extrabold text-[#18181B]" id="adminMatchTotalRows">{{ $seriesMatches->count() }}</span> data seri match
                </div>
            </div>

            <!-- Page Buttons (Prev, 1, 2, 3..., Next) -->
            <div class="flex items-center gap-1.5 flex-wrap" id="adminMatchPaginationButtons">
                <!-- Dynamically generated via JS -->
            </div>
        </div>

        <!-- Link Footer Menuju Halaman Dataset Pertandingan Lengkap -->
        <div class="pt-3 border-t border-[#F3E8E8] flex items-center justify-between flex-wrap gap-2 text-xs">
            <span class="text-gray-400 font-medium">Ingin mengelola data pertandingan atau sinkronisasi API Liquipedia secara mendalam?</span>
            <a href="{{ route('matches') }}" class="font-extrabold text-[#700B1A] hover:underline flex items-center gap-1">
                <span>Buka Halaman Dataset Pertandingan Lengkap</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>

    <!-- 6. FOOTER -->
    <div class="border-t border-[#F3E8E8] pt-4 flex items-center justify-between text-[11px] font-bold text-gray-400 flex-wrap gap-2">
        <div>METASCOUT : LAND OF DAWN • ADMIN PANEL</div>
        <div>ALL TIMESTAMPS IN UTC+7</div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- 7. MODAL POPUP LIQUIPEDIA MATCH DETAIL (IDENTIK DENGAN DATASET LIST)       -->
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

<script>
    const seriesMatchesData = @json($seriesMatches);
    const heroPortraitsMap = @json($heroPortraitsMap ?? []);

    let currentAdminMatchPage = 1;
    let adminMatchPerPage = 7;

    function filterAdminByStage(stageVal) {
        const tbody = document.getElementById('adminMatchesBody');
        if (!tbody) return;
        const rows = tbody.querySelectorAll('.admin-match-row');
        const filter = (stageVal || 'all').toLowerCase().trim();

        rows.forEach(row => {
            const stage = (row.getAttribute('data-stage') || row.innerText).toLowerCase();
            let matched = false;

            if (filter === 'all') {
                matched = true;
            } else if (filter === 'playoffs') {
                matched = stage.includes('playoff') || stage.includes('final') || stage.includes('bracket');
            } else if (filter === 'grand-final') {
                matched = stage.includes('grand final');
            } else if (filter === 'lower-bracket-final') {
                matched = stage.includes('lower bracket');
            } else if (filter === 'upper-bracket-final') {
                matched = stage.includes('upper bracket');
            } else if (filter === 'regular-season') {
                matched = stage.includes('regular') || stage.includes('week');
            } else if (filter.startsWith('week-')) {
                const wNum = filter.replace('week-', '');
                matched = stage.includes(`week ${wNum}`) || stage.includes(`w${wNum}`);
            } else {
                matched = stage.includes(filter);
            }

            row.dataset.matched = matched ? 'true' : 'false';
        });

        currentAdminMatchPage = 1;
        updateAdminMatchPagination();
    }

    function changeAdminMatchPerPage(val) {
        adminMatchPerPage = val === 'all' ? 999999 : parseInt(val);
        currentAdminMatchPage = 1;
        updateAdminMatchPagination();
    }

    function goToAdminMatchPage(page) {
        currentAdminMatchPage = page;
        updateAdminMatchPagination();
        const table = document.getElementById('adminDatasetTable');
        if (table) {
            table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function updateAdminMatchPagination() {
        const tbody = document.getElementById('adminMatchesBody');
        if (!tbody) return;
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

    // Modal Helpers
    function closeMatchModalOnBackdrop(e) {
        if (e.target.id === 'liquipediaMatchModal') {
            closeMatchModal();
        }
    }

    function closeMatchModal() {
        document.getElementById('liquipediaMatchModal').classList.add('hidden');
    }

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

    function resolveMapImg(mapName, mapIcon = null) {
        if (mapIcon && typeof mapIcon === 'string' && mapIcon.startsWith('http')) {
            return mapIcon;
        }
        const lower = (mapName || '').toLowerCase();
        if (lower.includes('turret') || lower.includes('golden')) {
            return '/images/maps/golden_turret.webp';
        } else if (lower.includes('turtle') || lower.includes('healing')) {
            return '/images/maps/healing_turtle.webp';
        } else if (lower.includes('wisp') || lower.includes('revealing')) {
            return '/images/maps/revealing_wisps.webp';
        } else if (lower.includes('cloud')) {
            return '/images/maps/flying_cloud.webp';
        } else if (lower.includes('wall')) {
            return '/images/maps/broken_wall.webp';
        } else if (lower.includes('grass')) {
            return '/images/maps/dangerous_grass.webp';
        } else if (lower.includes('river')) {
            return '/images/maps/expanding_river.webp';
        }
        return '/images/maps/flying_cloud.webp';
    }

    function renderMapBadge(mapName, mapIcon = null) {
        const raw = (mapName || 'Flying Cloud').trim();
        const imgUrl = resolveMapImg(raw, mapIcon);

        return `
            <div class="flex items-center justify-center gap-1.5 text-[11px] sm:text-xs font-bold text-[#0260a8] hover:underline cursor-pointer mt-0.5">
                <img src="${imgUrl}" 
                     alt="${raw}" 
                     class="w-4 h-4 object-contain inline-block flex-shrink-0 rounded-xs" 
                     onerror="this.style.display='none';">
                <span>${raw}</span>
            </div>
        `;
    }

    function showMatchModal(matchId) {
        const match = seriesMatchesData.find(m => m.id === matchId);
        if (!match) return;

        const container = document.getElementById('modalContentInner');
        const team1IsWinner = match.team_1_score > match.team_2_score;

        const dateHeader = (match.simple_date && match.simple_time) 
            ? `${match.simple_date} • ${match.simple_time}` 
            : (match.formatted_date_time || '08 Nov 2026 • 17:30 WIB');

        let html = `
            <div class="flex flex-col items-center justify-center space-y-3">
                <div class="bg-[#f1f3f5] text-gray-800 text-[11px] font-extrabold px-3 py-1 rounded shadow-2xs inline-block tracking-wide">
                    ${dateHeader}
                </div>

                <div class="w-full flex items-center justify-between px-2 sm:px-4">
                    <div class="flex items-center gap-2.5 flex-1 justify-start">
                        <span class="font-black text-base sm:text-lg text-[#0260a8] tracking-tight truncate max-w-[130px] sm:max-w-[180px]">
                            ${match.team_1_name}
                        </span>
                        ${match.team_1_logo ? `<img src="${match.team_1_logo}" alt="${match.team_1_name}" referrerpolicy="no-referrer" loading="lazy" class="w-8 h-8 sm:w-9 sm:h-9 object-contain flex-shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(match.team_1_name)}&background=700B1A&color=fff&size=64';">` : ''}
                        <img src="/images/teams/${(match.team_1_name || '').toLowerCase()}.png" 
                             alt="${match.team_1_name}" 
                             referrerpolicy="no-referrer" 
                             loading="lazy" 
                             class="w-8 h-8 sm:w-9 sm:h-9 object-contain flex-shrink-0" 
                             onerror="this.onerror=null; this.src='${match.team_1_logo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(match.team_1_name) + '&background=700B1A&color=fff&size=64'}';">
                    </div>

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

                    <div class="flex items-center gap-2.5 flex-1 justify-end">
                        ${match.team_2_logo ? `<img src="${match.team_2_logo}" alt="${match.team_2_name}" referrerpolicy="no-referrer" loading="lazy" class="w-8 h-8 sm:w-9 sm:h-9 object-contain flex-shrink-0" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(match.team_2_name)}&background=700B1A&color=fff&size=64';">` : ''}
                        <img src="/images/teams/${(match.team_2_name || '').toLowerCase()}.png" 
                             alt="${match.team_2_name}" 
                             referrerpolicy="no-referrer" 
                             loading="lazy" 
                             class="w-8 h-8 sm:w-9 sm:h-9 object-contain flex-shrink-0" 
                             onerror="this.onerror=null; this.src='${match.team_2_logo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(match.team_2_name) + '&background=700B1A&color=fff&size=64'}';">
                        <span class="font-black text-base sm:text-lg text-[#0260a8] tracking-tight truncate max-w-[130px] sm:max-w-[180px]">
                            ${match.team_2_name}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-100 my-1"></div>
        `;

        html += `<div class="space-y-3.5">`;

        const rawGames = match.games || [];
        const games = [...rawGames].sort((a, b) => (parseInt(a.game_number) || 0) - (parseInt(b.game_number) || 0));
        const seenPickGameNums = new Set();

        games.forEach((game, gIdx) => {
            let team1Details = (game.details || []).filter(d => d.team_position === 1 && d.type === 'pick');
            let team2Details = (game.details || []).filter(d => d.team_position === 2 && d.type === 'pick');
        if (games.length === 0) {
            html += `
                <div class="py-8 px-4 text-center bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl text-xs font-semibold text-gray-500">
                    Belum ada data game individual yang tersimpan untuk pertandingan ini.
                </div>
            `;
        } else {
            html += `<div class="space-y-3.5">`;
            const seenPickGameNums = new Set();

            if (team1Details.length === 0 && Array.isArray(game.team_1_picks) && game.team_1_picks.length > 0) {
                team1Details = game.team_1_picks.map(h => ({ hero_name: h, hero_portrait: resolveHeroImg(h, '') }));
            }
            if (team2Details.length === 0 && Array.isArray(game.team_2_picks) && game.team_2_picks.length > 0) {
                team2Details = game.team_2_picks.map(h => ({ hero_name: h, hero_portrait: resolveHeroImg(h, '') }));
            }
            games.forEach((game, gIdx) => {
                let team1Details = (game.details || []).filter(d => d.team_position === 1 && d.type === 'pick');
                let team2Details = (game.details || []).filter(d => d.team_position === 2 && d.type === 'pick');

            if (team1Details.length === 0 && team2Details.length === 0) return;
                if (team1Details.length === 0 && Array.isArray(game.team_1_picks) && game.team_1_picks.length > 0) {
                    team1Details = game.team_1_picks.map(h => ({ hero_name: h, hero_portrait: resolveHeroImg(h, '') }));
                }
                if (team2Details.length === 0 && Array.isArray(game.team_2_picks) && game.team_2_picks.length > 0) {
                    team2Details = game.team_2_picks.map(h => ({ hero_name: h, hero_portrait: resolveHeroImg(h, '') }));
                }

            const gNum = game.game_number || (gIdx + 1);
            if (seenPickGameNums.has(gNum)) return;
            seenPickGameNums.add(gNum);
                if (team1Details.length === 0 && team2Details.length === 0) return;

            const team1Won = (game.team_1_status === 'W');
            const team2Won = (game.team_2_status === 'W');
                const gNum = game.game_number || (gIdx + 1);
                if (seenPickGameNums.has(gNum)) return;
                seenPickGameNums.add(gNum);

            const t1Badge = team1Won 
                ? `<span class="w-6 h-6 rounded bg-[#dcfce7] text-[#16a34a] border border-[#bbf7d0] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">W</span>`
                : `<span class="w-6 h-6 rounded bg-[#fee2e2] text-[#dc2626] border border-[#fecaca] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">L</span>`;
                const team1Won = (game.team_1_status === 'W');
                const team2Won = (game.team_2_status === 'W');

            const t2Badge = team2Won 
                ? `<span class="w-6 h-6 rounded bg-[#dcfce7] text-[#16a34a] border border-[#bbf7d0] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">W</span>`
                : `<span class="w-6 h-6 rounded bg-[#fee2e2] text-[#dc2626] border border-[#fecaca] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">L</span>`;
                const t1Badge = team1Won 
                    ? `<span class="w-6 h-6 rounded bg-[#dcfce7] text-[#16a34a] border border-[#bbf7d0] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">W</span>`
                    : `<span class="w-6 h-6 rounded bg-[#fee2e2] text-[#dc2626] border border-[#fecaca] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">L</span>`;

            const renderHeroPicks = (details, sideColor) => {
                let hHtml = `<div class="flex items-center gap-1">`;
                details.forEach(d => {
                    const portrait = resolveHeroImg(d.hero_name, d.hero_portrait);
                    const borderColor = sideColor === 'Red' ? 'border-[#ef4444]' : 'border-[#3b82f6]';
                const t2Badge = team2Won 
                    ? `<span class="w-6 h-6 rounded bg-[#dcfce7] text-[#16a34a] border border-[#bbf7d0] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">W</span>`
                    : `<span class="w-6 h-6 rounded bg-[#fee2e2] text-[#dc2626] border border-[#fecaca] font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">L</span>`;

                    hHtml += `
                        <div class="relative group/h flex-shrink-0">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-md border-2 ${borderColor} overflow-hidden bg-gray-100 shadow-2xs">
                                <img src="${portrait}" 
                                     alt="${d.hero_name}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; const cl = '${d.hero_name.toLowerCase().replace(/[^a-z0-9]/g, '')}'; if (heroPortraitsMap[cl]) { this.src = heroPortraitsMap[cl]; } else { this.src = 'https://ui-avatars.com/api/?name=${encodeURIComponent(d.hero_name)}&background=700B1A&color=fff'; }">
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
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1.5 hidden group-hover/h:block bg-gray-900 text-white text-[10px] font-extrabold px-2 py-0.5 rounded shadow-lg whitespace-nowrap z-40 pointer-events-none">
                                    ${d.hero_name}
                                </div>
                            </div>
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1.5 hidden group-hover/h:block bg-gray-900 text-white text-[10px] font-extrabold px-2 py-0.5 rounded shadow-lg whitespace-nowrap z-40 pointer-events-none">
                                ${d.hero_name}
                            </div>
                        </div>
                    `;
                });
                hHtml += `</div>`;
                return hHtml;
            };
                        `;
                    });
                    hHtml += `</div>`;
                    return hHtml;
                };

            const t1PicksHtml = renderHeroPicks(team1Details, game.team_1_side || 'Red');
            const t2PicksHtml = renderHeroPicks(team2Details, game.team_2_side || 'Blue');
                const t1PicksHtml = renderHeroPicks(team1Details, game.team_1_side || 'Red');
                const t2PicksHtml = renderHeroPicks(team2Details, game.team_2_side || 'Blue');

            html += `
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center gap-2">
                        ${t1Badge}
                        ${t1PicksHtml}
                    </div>
                html += `
                    <div class="flex items-center justify-between gap-2 py-1.5 border-b border-gray-100 last:border-b-0">
                        <div class="flex items-center gap-2">
                            ${t1Badge}
                            ${t1PicksHtml}
                        </div>

                    <div class="text-center px-1 flex-shrink-0">
                        <div class="font-mono font-black text-xs sm:text-sm text-gray-900">
                            ${game.duration || '14:40'}
                        <div class="text-center px-1 flex-shrink-0">
                            <div class="font-mono font-black text-xs sm:text-sm text-gray-900">
                                ${game.duration ? game.duration : '<span class="text-[10px] text-gray-400 font-sans">Data tidak tersedia</span>'}
                            </div>
                            ${renderMapBadge(game.map_type, game.map_icon)}
                        </div>
                        ${renderMapBadge(game.map_type, game.map_icon)}

                        <div class="flex items-center gap-2">
                            ${t2PicksHtml}
                            ${t2Badge}
                        </div>
                    </div>
                `;
            });

                    <div class="flex items-center gap-2">
                        ${t2PicksHtml}
                        ${t2Badge}
                    </div>
            html += `</div>`;
        }

        if (match.series_mvp && match.series_mvp.trim()) {
            html += `
                <div class="bg-[#fef9c3] border border-[#fef08a] rounded-lg py-2 px-4 text-center shadow-2xs mt-3">
                    <span class="font-black text-gray-900 text-xs sm:text-sm">MVP:</span>
                    <span class="font-black text-[#0260a8] text-xs sm:text-sm ml-1 hover:underline cursor-pointer">${match.series_mvp}</span>
                </div>
            `;
        });
        }

        html += `</div>`;

        const mvpName = match.series_mvp || 'Shogun';
        html += `
            <div class="bg-[#fef9c3] border border-[#fef08a] rounded-lg py-2 px-4 text-center shadow-2xs mt-3">
                <span class="font-black text-gray-900 text-xs sm:text-sm">MVP:</span>
                <span class="font-black text-[#0260a8] text-xs sm:text-sm ml-1 hover:underline cursor-pointer">${mvpName}</span>
            </div>
        `;

        html += `
            <div class="bg-[#fafafa] border border-gray-200 rounded-xl p-3 sm:p-4 mt-3">
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

                <div id="modalBansContainer" class="space-y-3">
        `;

        const seenBanGameNumbers = new Set();

        games.forEach((game, gIdx) => {
            let team1Bans = (game.details || []).filter(d => d.team_position === 1 && d.type === 'ban');
            let team2Bans = (game.details || []).filter(d => d.team_position === 2 && d.type === 'ban');

            if (team1Bans.length === 0 && Array.isArray(game.team_1_bans) && game.team_1_bans.length > 0) {
                team1Bans = game.team_1_bans.map(h => ({ hero_name: h, hero_portrait: resolveHeroImg(h, '') }));
            }
            if (team2Bans.length === 0 && Array.isArray(game.team_2_bans) && game.team_2_bans.length > 0) {
                team2Bans = game.team_2_bans.map(h => ({ hero_name: h, hero_portrait: resolveHeroImg(h, '') }));
            }

            if (team1Bans.length === 0 && team2Bans.length === 0) return;

            const gNum = game.game_number || (gIdx + 1);
            if (seenBanGameNumbers.has(gNum)) return;
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
                    ${renderHeroBans(team1Bans, false)}
                    <span class="text-[11px] font-semibold text-gray-600 px-2 flex-shrink-0 text-center min-w-[50px]">
                        Game ${gNum}
                    </span>
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

    document.addEventListener('DOMContentLoaded', function() {
        const filterElem = document.getElementById('adminStageFilter');
        filterAdminByStage(filterElem ? filterElem.value : 'all');
    });
</script>
@endsection

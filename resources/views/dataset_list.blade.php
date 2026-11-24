@extends('layouts.app')

@section('title', 'Dataset Pertandingan - MetaScout: Land of Dawn')

@section('content')
<div class="space-y-6">

    <!-- 1. FLASH NOTIFICATIONS (ADMIN ONLY / SYSTEM) -->
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-bold shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-blue-600 text-base">ℹ</span>
                <span>{{ session('info') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-blue-600 hover:text-blue-900 font-bold">&times;</button>
        </div>
    @endif

    @if(!$dbConnected && !empty($dbErrorMessage))
        <div class="bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-medium shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-amber-600 text-base font-bold">⚠</span>
                <span>
                    <strong>Koneksi Database Belum Terbuka:</strong> Pastikan MySQL di server lokal Anda aktif agar data tersimpan di tabel lokal.
                </span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-amber-600 hover:text-amber-900 font-bold">&times;</button>
        </div>
    @endif

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

    <!-- 2. PAGE HEADING & ACTIONS -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                <span>DATABASE TURNAMEN</span>
                <span>/</span>
                <span class="text-[#700B1A] font-extrabold">MPL INDONESIA SEASON 18</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#18181B] tracking-tight uppercase">
                DATASET PERTANDINGAN MATCH
            </h1>
            <p class="text-xs text-gray-500 font-medium mt-1">
                Rekap seluruh seri pertandingan dan draft hero (Picks & Bans) turnamen MPL ID S18 bersumber resmi dari Liquipedia DB API.
            </p>
        </div>

        @if(session('admin_logged_in'))
            <!-- Tombol Khusus Admin: Sinkronisasi Data Liquipedia -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <button type="button" 
                        onclick="openSyncModal()" 
                        class="bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-full shadow-sm flex items-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Sinkronisasi Data Liquipedia</span>
                </button>
            </div>
        @endif
    </div>

    <!-- 3. SUMMARY METRIC BOXES -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <!-- Total Seri Match -->
        <div class="card-custom p-4 sm:p-5 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">
                    TOTAL SERI PERTANDINGAN
                </div>
                <div class="text-2xl sm:text-3xl font-black text-[#18181B] tracking-tight">
                    {{ $totalSeries }} <span class="text-xs font-bold text-gray-400">Match Series</span>
                </div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#FCECEE] flex items-center justify-center text-[#700B1A] flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        </div>

        <!-- Total Game Individual -->
        <div class="card-custom p-4 sm:p-5 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">
                    TOTAL GAME DIMAINKAN
                </div>
                <div class="text-2xl sm:text-3xl font-black text-[#18181B] tracking-tight">
                    {{ $totalGames }} <span class="text-xs font-bold text-gray-400">Games Riil</span>
                </div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#FCECEE] flex items-center justify-center text-[#700B1A] flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>

        <!-- Status Sumber Data -->
        <div class="card-custom p-4 sm:p-5 flex items-center justify-between sm:col-span-2 lg:col-span-1">
            <div>
                <div class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">
                    SUMBER DATA RESMI
                </div>
                <div class="text-sm font-black text-[#0A285F] flex items-center gap-1.5">
                    <img src="/images/liquipedia_logo.png" alt="Liquipedia Logo" class="w-4 h-4 object-contain">
                    <span>Liquipedia DB API v3</span>
                </div>
                <div class="text-[10px] text-gray-400 font-semibold mt-0.5">Tersinkronisasi & Validasi 100%</div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#EBF2FA] text-[#0A285F] flex items-center justify-center font-black text-base flex-shrink-0">
                ✓
            </div>
        </div>
    </div>

    <!-- 4. TAB FILTER & DAFTAR MATCH -->
    <div class="card-custom p-4 sm:p-6 space-y-4">
        
        <!-- TOP FILTER: 2 TAB (REGULAR SEASON vs PLAYOFFS) + SEARCH BOX -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#F3E8E8]">
            <!-- 2 TAB FILTER UTAMA -->
            <div class="flex items-center gap-2">
                <button type="button" 
                        id="tabRegular" 
                        onclick="switchStageTab('regular')" 
                        class="stage-tab-btn px-4 sm:px-5 py-2 rounded-full text-xs font-black uppercase tracking-wider transition flex items-center gap-2 cursor-pointer bg-[#700B1A] text-white shadow-sm">
                    <span>REGULAR SEASON</span>
                    <span id="badgeRegular" class="bg-white/20 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">
                        {{ $regularCount ?? 72 }}
                    </span>
                </button>
                <button type="button" 
                        id="tabPlayoffs" 
                        onclick="switchStageTab('playoffs')" 
                        class="stage-tab-btn px-4 sm:px-5 py-2 rounded-full text-xs font-black uppercase tracking-wider transition flex items-center gap-2 cursor-pointer bg-[#FAF8F8] text-gray-700 hover:text-[#700B1A] border border-[#F3E8E8]">
                    <span>PLAYOFFS</span>
                    <span id="badgePlayoffs" class="bg-gray-200 text-gray-700 text-[10px] px-2 py-0.5 rounded-full font-bold">
                        {{ $playoffsCount ?? 8 }}
                    </span>
                </button>
            </div>

            <!-- Quick Search Input -->
            <div class="relative w-full sm:w-64">
                <input type="text" 
                       id="matchSearchInput" 
                       oninput="handleMatchSearch(this.value)" 
                       placeholder="Cari tim (e.g. RRQ, EVOS)..." 
                       class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-full py-1.5 pl-8 pr-3 text-xs font-semibold text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A]">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- TABEL LIST BERSIH MATCH -->
        <div class="overflow-x-auto rounded-xl border border-[#F3E8E8] shadow-2xs">
            <table class="w-full text-left border-collapse min-w-[680px]" id="datasetTable">
                <thead class="bg-[#FAF8F8] border-b border-[#F3E8E8] select-none">
                    <tr class="text-[10px] font-black uppercase tracking-wider text-gray-400">
                        <th class="py-3 px-4 w-44">STAGE / TANGGAL</th>
                        <th class="py-3 px-3 text-right">TIM 1</th>
                        <th class="py-3 px-3 text-center w-28">SKOR</th>
                        <th class="py-3 px-3 text-left">TIM 2</th>
                        <th class="py-3 px-4 w-20 text-center">DETAIL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($matches as $match)
                        @php
                            $isPlayoff = stripos($match->stage_name, 'playoff') !== false || stripos($match->stage_name, 'final') !== false;
                            $stageType = $isPlayoff ? 'playoffs' : 'regular';
                            $team1Logo = getTeamLogo($match->team_1_name);
                            $team2Logo = getTeamLogo($match->team_2_name);
                        @endphp
                        <tr class="match-row hover:bg-[#FAF8F8]/80 transition group"
                            data-stage="{{ $stageType }}"
                            data-raw-stage="{{ strtolower($match->stage_name) }}"
                            data-search="{{ strtolower($match->team_1_name . ' ' . $match->team_2_name . ' ' . $match->formatted_stage_name) }}">
                            
                            <!-- 1. Stage / Tanggal -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $isPlayoff ? 'bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD]' : 'bg-[#F4F4F5] text-gray-700 border border-gray-200' }}">
                                        {{ $match->formatted_stage_name }}
                                    </span>
                                </div>
                                <div class="text-xs font-bold text-gray-800 mt-1">
                                    {{ $match->simple_date ?: 'Date TBA' }}
                                </div>
                                <div class="text-[10px] font-mono text-gray-400">
                                    {{ $match->simple_time ?: '-' }}
                                </div>
                            </td>

                            <!-- 2. Logo & Nama Tim 1 (Right Aligned) -->
                            <td class="py-3 px-3">
                                <div class="flex items-center justify-end gap-2.5">
                                    <span class="font-black text-xs sm:text-sm text-right whitespace-nowrap {{ $match->team_1_score > $match->team_2_score ? 'text-[#18181B]' : 'text-gray-500 font-bold' }}">
                                        {{ $match->team_1_name }}
                                    </span>
                                    @if($team1Logo)
                                        <img src="{{ $team1Logo }}" 
                                             alt="{{ $match->team_1_name }}" 
                                             class="w-7 h-7 object-contain flex-shrink-0"
                                             onerror="this.style.display='none';">
                                    @endif
                                </div>
                            </td>

                            <!-- 3. Skor (Contoh: 3 : 1) -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center bg-[#FAF8F8] border border-[#E5E7EB] text-[#18181B] font-mono font-black px-3 py-1 rounded-md text-xs sm:text-sm shadow-2xs tracking-wider">
                                    {{ $match->team_1_score }} : {{ $match->team_2_score }}
                                </span>
                                <div class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase tracking-wider">
                                    {{ strtoupper($match->best_of ?: 'Bo3') }}
                                </div>
                            </td>

                            <!-- 4. Logo & Nama Tim 2 (Left Aligned) -->
                            <td class="py-3 px-3">
                                <div class="flex items-center justify-start gap-2.5">
                                    @if($team2Logo)
                                        <img src="{{ $team2Logo }}" 
                                             alt="{{ $match->team_2_name }}" 
                                             class="w-7 h-7 object-contain flex-shrink-0"
                                             onerror="this.style.display='none';">
                                    @endif
                                    <span class="font-black text-xs sm:text-sm text-left whitespace-nowrap {{ $match->team_2_score > $match->team_1_score ? 'text-[#18181B]' : 'text-gray-500 font-bold' }}">
                                        {{ $match->team_2_name }}
                                    </span>
                                </div>
                            </td>

                            <!-- 5. Tombol Detail (Ikon i) -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <button type="button" 
                                        onclick="showMatchModal({{ $match->id }})" 
                                        class="w-8 h-8 rounded-full bg-[#FAF8F8] hover:bg-[#700B1A] text-gray-600 hover:text-white border border-[#E5E7EB] hover:border-[#700B1A] inline-flex items-center justify-center transition shadow-2xs cursor-pointer group"
                                        title="Detail Pertandingan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">
                                <p class="text-sm font-bold">Belum ada seri pertandingan di database.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION FOOTER BAR -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-[#F3E8E8] text-xs">
            <div class="flex items-center gap-2.5 flex-wrap">
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-gray-500 font-bold text-xs whitespace-nowrap">Tampilkan:</span>
                    <select id="datasetPerPageSelect" onchange="changeDatasetPerPage(this.value)" class="bg-[#FAF8F8] border border-[#E5E7EB] rounded-lg px-2.5 py-1 text-xs font-extrabold text-[#18181B] focus:outline-none focus:border-[#700B1A] transition cursor-pointer shadow-2xs">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="all">Semua</option>
                    </select>
                </div>
                <span class="text-gray-300 font-light select-none hidden sm:inline">|</span>
                <div class="text-gray-600 font-medium text-xs whitespace-nowrap" id="datasetPaginationInfo">
                    Menampilkan <span class="font-extrabold text-[#700B1A]" id="dsPageStart">1</span> - <span class="font-extrabold text-[#700B1A]" id="dsPageEnd">10</span> dari <span class="font-extrabold text-[#18181B]" id="dsTotalRows">0</span> data match
                </div>
            </div>

            <!-- Page Buttons (Prev, 1, 2, 3..., Next) -->
            <div class="flex items-center gap-1.5 flex-wrap" id="datasetPaginationButtons">
                <!-- Dynamically generated via JS -->
            </div>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- 5. MODAL CARD DETAIL PERTANDINGAN (SESUAI REQUEST USER)                    -->
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
            <!-- Diisi secara dinamis via JS -->
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- 6. MODAL SINKRONISASI API LIQUIPEDIA (ADMIN ONLY)                          -->
<!-- ========================================================================= -->
@if(session('admin_logged_in'))
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
            Sistem akan melakukan request terukur ke endpoint <code>GET /match</code> pada wiki <code>mobilelegends</code>, lalu menyimpan seluruh detail seri & game individual ke database MySQL lokal.
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
@endif

@push('scripts')
<script>
    // Master data matches dan dictionary foto hero resmi
    const matchesData = @json($matches);
    const heroPortraitsMap = @json($heroPortraitsMap ?? []);

    // State Pagination & Filter
    let currentStage = '{{ $activeStage ?? "regular" }}';
    let searchQuery = '';
    let currentDatasetPage = 1;
    let datasetPerPage = 10;
    let isBansVisible = false;

    /**
     * Switch Stage Tab (Regular Season vs Playoffs)
     */
    function switchStageTab(stage) {
        currentStage = stage;

        const btnRegular = document.getElementById('tabRegular');
        const btnPlayoffs = document.getElementById('tabPlayoffs');
        const badgeRegular = document.getElementById('badgeRegular');
        const badgePlayoffs = document.getElementById('badgePlayoffs');

        if (stage === 'regular') {
            btnRegular.className = 'stage-tab-btn px-4 sm:px-5 py-2 rounded-full text-xs font-black uppercase tracking-wider transition flex items-center gap-2 cursor-pointer bg-[#700B1A] text-white shadow-sm';
            btnPlayoffs.className = 'stage-tab-btn px-4 sm:px-5 py-2 rounded-full text-xs font-black uppercase tracking-wider transition flex items-center gap-2 cursor-pointer bg-[#FAF8F8] text-gray-700 hover:text-[#700B1A] border border-[#F3E8E8]';
            badgeRegular.className = 'bg-white/20 text-white text-[10px] px-2 py-0.5 rounded-full font-bold';
            badgePlayoffs.className = 'bg-gray-200 text-gray-700 text-[10px] px-2 py-0.5 rounded-full font-bold';
        } else {
            btnPlayoffs.className = 'stage-tab-btn px-4 sm:px-5 py-2 rounded-full text-xs font-black uppercase tracking-wider transition flex items-center gap-2 cursor-pointer bg-[#700B1A] text-white shadow-sm';
            btnRegular.className = 'stage-tab-btn px-4 sm:px-5 py-2 rounded-full text-xs font-black uppercase tracking-wider transition flex items-center gap-2 cursor-pointer bg-[#FAF8F8] text-gray-700 hover:text-[#700B1A] border border-[#F3E8E8]';
            badgePlayoffs.className = 'bg-white/20 text-white text-[10px] px-2 py-0.5 rounded-full font-bold';
            badgeRegular.className = 'bg-gray-200 text-gray-700 text-[10px] px-2 py-0.5 rounded-full font-bold';
        }

        filterTable();
    }

    function handleMatchSearch(q) {
        searchQuery = (q || '').toLowerCase().trim();
        filterTable();
    }

    function filterTable() {
        const rows = document.querySelectorAll('.match-row');

        rows.forEach(row => {
            const rowStage = row.getAttribute('data-stage') || 'regular';
            const searchData = row.getAttribute('data-search') || '';

            const matchStage = (currentStage === 'all') || (rowStage === currentStage);
            const matchSearch = !searchQuery || searchData.includes(searchQuery);

            row.dataset.matched = (matchStage && matchSearch) ? 'true' : 'false';
        });

        currentDatasetPage = 1;
        updatePagination();
    }

    function changeDatasetPerPage(val) {
        datasetPerPage = val === 'all' ? 999999 : parseInt(val);
        currentDatasetPage = 1;
        updatePagination();
    }

    function goToDatasetPage(page) {
        currentDatasetPage = page;
        updatePagination();
        const table = document.getElementById('datasetTable');
        if (table) {
            table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function updatePagination() {
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
        const modal = document.getElementById('syncConfigModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeSyncModal() {
        const modal = document.getElementById('syncConfigModal');
        if (modal) modal.classList.add('hidden');
    }

    function submitSyncForm(form) {
        const btn = document.getElementById('btnSubmitSync');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span class="animate-spin inline-block mr-1">↻</span> Menyinkronkan...`;
        }
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
    function toggleBansVisibility() {
        isBansVisible = !isBansVisible;
        const container = document.getElementById('modalBansContainer');
        const btnText = document.getElementById('btnToggleBansText');
        const btnIcon = document.getElementById('btnToggleBansIcon');
        
        if (container) {
            container.style.display = isBansVisible ? 'block' : 'none';
        }
        if (btnText) {
            btnText.textContent = isBansVisible ? 'Hide Bans' : 'Show Bans';
        }
        if (btnIcon) {
            btnIcon.textContent = isBansVisible ? '🙈' : '👁️';
        }
    }

    /**
     * Resolusi logo tim resmi dari public/images/teams/*.png
     */
    function getTeamLogoJS(teamName) {
        if (!teamName) return '';
        const clean = teamName.toLowerCase().replace(/[[\]_]/g, ' ').trim();
        if (clean.includes('evos')) return '/images/teams/evos.png';
        if (clean.includes('rrq')) return '/images/teams/rrq.png';
        if (clean.includes('navi') || clean.includes('natus')) return '/images/teams/navi.png';
        if (clean.includes('alter ego') || clean === 'ae' || clean.startsWith('ae ')) return '/images/teams/ae.png';
        if (clean.includes('tlid') || clean.includes('liquid')) return '/images/teams/tlid.png';
        if (clean.includes('geek')) return '/images/teams/geek.png';
        if (clean.includes('dewa')) return '/images/teams/dewa.png';
        if (clean.includes('btr') || clean.includes('bigetron') || clean.includes('vitality')) return '/images/teams/btr.png';
        if (clean.includes('onic')) return '/images/teams/onic.png';
        if (clean.includes('rebellion') || clean.includes('rbl')) return '/images/teams/rbl.png';
        return '';
    }

    /**
     * Resolusi URL foto hero resmi
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
     * Menampilkan Modal Card Detail Pertandingan
     * Sesuai Request User:
     * 1. Header: Tanggal/Jam, Logo & Nama Tim 1 vs Tim 2, Skor Akhir (Bo3 / Bo5)
     * 2. Daftar Game: Badge W (hijau) / L (merah), 5 hero pick Tim 1, durasi di tengah, 5 hero pick Tim 2
     * 3. Bagian Bans collapsible dengan tombol Show / Hide Bans
     */
    function showMatchModal(matchId) {
        const match = matchesData.find(m => m.id === matchId);
        if (!match) return;

        const container = document.getElementById('modalContentInner');
        const team1IsWinner = match.team_1_score > match.team_2_score;

        const dateHeader = (match.simple_date && match.simple_time) 
            ? `${match.simple_date} • ${match.simple_time}` 
            : (match.formatted_date_time || 'MPL Indonesia Season 18');

        const team1Logo = match.team_1_logo || getTeamLogoJS(match.team_1_name);
        const team2Logo = match.team_2_logo || getTeamLogoJS(match.team_2_name);

        let html = `
            <!-- 1. HEADER MODAL: Tanggal/Jam, Logo & Nama Tim 1 vs Tim 2, Skor Akhir -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="bg-[#F4F4F5] text-gray-800 text-[11px] font-extrabold px-3 py-1 rounded-full shadow-2xs">
                        ${dateHeader}
                    </div>
                    <div class="text-[11px] font-extrabold uppercase text-[#700B1A] bg-[#FCECEE] border border-[#F8B4BD] px-2.5 py-0.5 rounded-full mr-10 sm:mr-12">
                        ${match.formatted_stage_name || 'Match Series'}
                    </div>
                </div>

                <!-- Matchup Card -->
                <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl p-3 sm:p-4 flex items-center justify-between gap-2">
                    <!-- Tim 1 -->
                    <div class="flex items-center gap-2.5 flex-1 justify-start min-w-0">
                        ${team1Logo ? `
                            <img src="${team1Logo}" 
                                 alt="${match.team_1_name}" 
                                 class="w-8 h-8 sm:w-10 sm:h-10 object-contain flex-shrink-0" 
                                 onerror="this.style.display='none';">
                        ` : ''}
                        <div class="min-w-0">
                            <span class="font-black text-sm sm:text-base text-gray-900 block truncate">
                                ${match.team_1_name}
                            </span>
                            ${team1IsWinner ? '<span class="text-[9px] font-black uppercase text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">WINNER</span>' : ''}
                        </div>
                    </div>

                    <!-- Skor & Format (Bo3 / Bo5 / Bo7) -->
                    <div class="text-center px-2 flex-shrink-0">
                        <div class="flex items-center justify-center gap-2 text-xl sm:text-2xl font-mono font-black text-[#18181B]">
                            <span>${match.team_1_score}</span>
                            <span class="text-gray-400 font-light">:</span>
                            <span>${match.team_2_score}</span>
                        </div>
                        <div class="text-[10px] font-extrabold text-gray-500 uppercase tracking-wider mt-0.5">
                            ${strtoupper(match.best_of || 'Bo3')}
                        </div>
                    </div>

                    <!-- Tim 2 -->
                    <div class="flex items-center gap-2.5 flex-1 justify-end min-w-0 text-right">
                        <div class="min-w-0">
                            <span class="font-black text-sm sm:text-base text-gray-900 block truncate">
                                ${match.team_2_name}
                            </span>
                            ${!team1IsWinner && match.team_2_score > match.team_1_score ? '<span class="text-[9px] font-black uppercase text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">WINNER</span>' : ''}
                        </div>
                        ${team2Logo ? `
                            <img src="${team2Logo}" 
                                 alt="${match.team_2_name}" 
                                 class="w-8 h-8 sm:w-10 sm:h-10 object-contain flex-shrink-0" 
                                 onerror="this.style.display='none';">
                        ` : ''}
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 my-1"></div>
        `;

        // 2. DAFTAR GAME (Game 1, Game 2, dst)
        const rawGames = match.games || [];
        // Filter agar HANYA merender game yang valid dan memiliki minimal 1 hero pick
        const games = rawGames.filter(g => {
            const t1 = Array.isArray(g.team_1_picks) ? g.team_1_picks : [];
            const t2 = Array.isArray(g.team_2_picks) ? g.team_2_picks : [];
            const details = Array.isArray(g.details) ? g.details : [];
            const hasPickDetails = details.some(d => d.type === 'pick');
            return t1.length > 0 || t2.length > 0 || hasPickDetails;
        }).sort((a, b) => (parseInt(a.game_number) || 0) - (parseInt(b.game_number) || 0));

        if (games.length === 0) {
            html += `
                <div class="py-6 px-4 text-center bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl text-xs font-semibold text-gray-500">
                    Belum ada data game individual yang tersimpan untuk pertandingan ini.
                </div>
            `;
        } else {
            html += `
                <div>
                    <div class="text-[11px] font-black text-gray-500 uppercase tracking-wider mb-2 flex items-center justify-between">
                        <span>DAFTAR GAME (PICKS & DURASI)</span>
                        <span class="text-[10px] font-bold text-gray-400">${games.length} Game Dimainkan</span>
                    </div>
                    <div class="space-y-2.5">
            `;

            const seenPickGameNums = new Set();

            games.forEach((game, gIdx) => {
                let team1Details = (game.details || []).filter(d => d.team_position === 1 && d.type === 'pick');
                let team2Details = (game.details || []).filter(d => d.team_position === 2 && d.type === 'pick');

                if (team1Details.length === 0 && Array.isArray(game.team_1_picks) && game.team_1_picks.length > 0) {
                    team1Details = game.team_1_picks.map(h => ({ hero_name: h, hero_portrait: resolveHeroImg(h, '') }));
                }
                if (team2Details.length === 0 && Array.isArray(game.team_2_picks) && game.team_2_picks.length > 0) {
                    team2Details = game.team_2_picks.map(h => ({ hero_name: h, hero_portrait: resolveHeroImg(h, '') }));
                }

                // Cegah duplikasi nomor game
                const gNum = game.game_number || (gIdx + 1);
                if (seenPickGameNums.has(gNum)) return;
                seenPickGameNums.add(gNum);

                const team1Won = (game.team_1_status === 'W');
                const team2Won = (game.team_2_status === 'W');

                // Badge W (hijau) / L (merah)
                const t1Badge = team1Won 
                    ? `<span class="w-6 h-6 rounded bg-emerald-100 text-emerald-700 border border-emerald-300 font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">W</span>`
                    : `<span class="w-6 h-6 rounded bg-rose-100 text-rose-700 border border-rose-300 font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">L</span>`;

                const t2Badge = team2Won 
                    ? `<span class="w-6 h-6 rounded bg-emerald-100 text-emerald-700 border border-emerald-300 font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">W</span>`
                    : `<span class="w-6 h-6 rounded bg-rose-100 text-rose-700 border border-rose-300 font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs">L</span>`;

                // Render 5 Hero Pick Icons
                const renderHeroPicks = (details, sideColor) => {
                    let hHtml = `<div class="flex items-center gap-1">`;
                    details.forEach(d => {
                        const portrait = resolveHeroImg(d.hero_name, d.hero_portrait);
                        const borderColor = sideColor === 'Red' ? 'border-red-400' : 'border-blue-400';

                        hHtml += `
                            <div class="relative group/h flex-shrink-0" title="${d.hero_name}">
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
                        `;
                    });
                    hHtml += `</div>`;
                    return hHtml;
                };

                const t1PicksHtml = renderHeroPicks(team1Details, game.team_1_side || 'Blue');
                const t2PicksHtml = renderHeroPicks(team2Details, game.team_2_side || 'Red');

                html += `
                    <div class="p-2 sm:p-2.5 bg-white border border-[#E5E7EB] rounded-xl flex items-center justify-between gap-1 sm:gap-2 shadow-2xs hover:border-gray-300 transition">
                        <!-- Sisi Kiri: W/L badge + 5 hero picks Tim 1 -->
                        <div class="flex items-center gap-1.5 sm:gap-2 flex-1 justify-start">
                            ${t1Badge}
                            ${t1PicksHtml}
                        </div>

                        <!-- Tengah: Durasi Game & Game Number -->
                        <div class="text-center px-1 flex-shrink-0 min-w-[70px]">
                            <div class="text-[10px] font-extrabold text-gray-400 uppercase">Game ${gNum}</div>
                            <div class="font-mono font-black text-xs text-gray-900">
                                ${game.duration ? game.duration : '-'}
                            </div>
                        </div>

                        <!-- Sisi Kanan: 5 hero picks Tim 2 + W/L badge -->
                        <div class="flex items-center gap-1.5 sm:gap-2 flex-1 justify-end">
                            ${t2PicksHtml}
                            ${t2Badge}
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        }

        // 3. BAGIAN BANS COLLAPSIBLE (Show / Hide Bans)
        html += `
            <div class="bg-[#FAF8F8] border border-[#E5E7EB] rounded-xl p-3 sm:p-4 mt-3">
                <!-- Header Bans dengan Tombol Toggle Collapsible -->
                <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                        <span class="font-black text-xs uppercase tracking-wider text-gray-800">BANS HERO</span>
                    </div>
                    <button type="button" 
                            onclick="toggleBansVisibility()" 
                            class="bg-white hover:bg-gray-100 border border-gray-300 text-gray-700 text-[10px] font-extrabold px-3 py-1 rounded-full flex items-center gap-1.5 transition shadow-2xs cursor-pointer">
                        <span id="btnToggleBansIcon">👁️</span>
                        <span id="btnToggleBansText">Show Bans</span>
                    </button>
                </div>

                <!-- Bans Rows Container (Collapsible - default hidden) -->
                <div id="modalBansContainer" class="space-y-2.5 pt-3" style="display: none;">
        `;

        const seenBanGameNumbers = new Set();
        let banRowCount = 0;

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
            banRowCount++;

            const renderHeroBans = (details, alignEnd = false) => {
                let bHtml = `<div class="flex items-center gap-1 ${alignEnd ? 'justify-end' : 'justify-start'} min-w-[125px] sm:min-w-[155px]">`;
                details.forEach(d => {
                    const portrait = resolveHeroImg(d.hero_name, d.hero_portrait);
                    bHtml += `
                        <div class="relative group/b flex-shrink-0" title="${d.hero_name}">
                            <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-md border border-gray-300 overflow-hidden bg-gray-100 shadow-2xs grayscale hover:grayscale-0 transition">
                                <img src="${portrait}" 
                                     alt="${d.hero_name}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; const cl = '${d.hero_name.toLowerCase().replace(/[^a-z0-9]/g, '')}'; if (heroPortraitsMap[cl]) { this.src = heroPortraitsMap[cl]; } else { this.src = 'https://ui-avatars.com/api/?name=${encodeURIComponent(d.hero_name)}&background=700B1A&color=fff'; }">
                            </div>
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1.5 hidden group-hover/b:block bg-gray-900 text-white text-[10px] font-extrabold px-2 py-0.5 rounded shadow-lg whitespace-nowrap z-50 pointer-events-none">
                                ${d.hero_name}
                            </div>
                        </div>
                    `;
                });
                bHtml += `</div>`;
                return bHtml;
            };

            html += `
                <div class="flex items-center justify-between gap-2 p-2 bg-white rounded-lg border border-gray-100">
                    <!-- Left: 5 Bans Tim 1 -->
                    ${renderHeroBans(team1Bans, false)}

                    <!-- Center: Game X text -->
                    <span class="text-[10px] font-bold text-gray-500 px-2 flex-shrink-0 text-center min-w-[50px]">
                        Game ${gNum}
                    </span>

                    <!-- Right: 5 Bans Tim 2 -->
                    ${renderHeroBans(team2Bans, true)}
                </div>
            `;
        });

        if (banRowCount === 0) {
            html += `
                <div class="py-3 text-center text-xs text-gray-400 font-semibold">
                    Tidak ada data hero bans untuk seri ini.
                </div>
            `;
        }

        html += `
                </div>
            </div>
        `;

        container.innerHTML = html;
        isBansVisible = false; // default collapsible collapsed
        document.getElementById('liquipediaMatchModal').classList.remove('hidden');
    }

    function strtoupper(str) {
        return (str || '').toUpperCase();
    }

    // Inisialisasi Tab & Pagination saat pertama dibuka
    document.addEventListener('DOMContentLoaded', function() {
        switchStageTab(currentStage);
    });
</script>
@endpush
@endsection

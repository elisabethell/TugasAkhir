@extends('layouts.app')

@section('title', 'Rekomendasi Draft')

@push('styles')
<style>
    /* Styling khusus saat Print ke PDF */
    @media print {
        body * {
            visibility: hidden !important;
        }
        #printableCoachSheet, #printableCoachSheet * {
            visibility: visible !important;
        }
        #printableCoachSheet {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 20px !important;
            background: #ffffff !important;
            box-shadow: none !important;
            border: none !important;
            display: block !important;
        }
        .no-print {
            display: none !important;
        }
        @page {
            size: A4 portrait;
            margin: 12mm;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER & QUICK DRAFT ACTIONS -->
    <div class="flex items-center justify-between flex-wrap gap-3 pt-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#18181B] tracking-tight">
                Rekomendasi Draft & Analisis Taktis
            </h1>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" 
                    onclick="resetDraft()" 
                    class="bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] rounded-full px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Draft Baru</span>
            </button>

            <!-- Export PDF Cheat Sheet Button untuk Coach -->
            <button type="button" 
                    onclick="openCoachCheatSheetModal()" 
                    class="bg-white hover:bg-[#FCECEE] text-[#700B1A] border-2 border-[#700B1A] rounded-full px-4 py-2 text-xs font-black uppercase tracking-wider flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-3.5 h-3.5 text-[#700B1A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Export PDF Cheat Sheet Coach</span>
            </button>

            <button type="button" 
                    onclick="shareDraft()" 
                    class="bg-[#700B1A] hover:bg-[#550713] text-white rounded-full px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                </svg>
                <span>Bagikan</span>
            </button>
        </div>
    </div>

    <!-- 2. TWO-COLUMN DRAFT ARENA -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ================= LEFT COLUMN: DRAFT TEAMS & HERO PICKER (7 cols) ================= -->
        <div class="lg:col-span-7 space-y-4">
            
            <!-- TEAM CARDS COMPARISON (TIM SEKUTU & TIM MUSUH) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- 1. TIM SEKUTU CARD -->
                <div id="allyTeamCard" class="card-custom p-4 space-y-3 border-2 border-[#700B1A] shadow-sm relative transition">
                    <!-- Header -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#700B1A] shadow-[0_0_6px_rgba(112,11,26,0.6)]"></span>
                            <span class="text-xs font-black uppercase tracking-wider text-[#18181B]">TIM SEKUTU</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span id="allyRoleNeededBadge" class="bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A] text-[9px] font-black px-2 py-0.5 rounded-full uppercase">
                                Perlu Hero
                            </span>
                            <span id="allyCountBadge" class="bg-[#FAF8F8] border border-[#F3E8E8] text-[#700B1A] text-[10px] font-black px-2 py-0.5 rounded-full">
                                0/5
                            </span>
                        </div>
                    </div>

                    <!-- 5 Slots Row Tim Sekutu -->
                    <div class="grid grid-cols-5 gap-1.5 pt-2 px-1 pb-1" id="allySlotsContainer">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <!-- Lane Indicators Tim Sekutu -->
                    <div class="pt-2 border-t border-[#FAF0F1] flex items-center justify-between text-[9px] font-bold text-gray-500 uppercase tracking-wider px-1" id="allyLaneStatus">
                        <span id="lane-ally-roam" class="flex items-center gap-1">ROAM <span class="text-gray-300">—</span></span>
                        <span id="lane-ally-jungle" class="flex items-center gap-1">JGL <span class="text-gray-300">—</span></span>
                        <span id="lane-ally-mid" class="flex items-center gap-1">MID <span class="text-gray-300">—</span></span>
                        <span id="lane-ally-exp" class="flex items-center gap-1">EXP <span class="text-gray-300">—</span></span>
                        <span id="lane-ally-gold" class="flex items-center gap-1">GOLD <span class="text-gray-300">—</span></span>
                    </div>

                    <!-- DAMAGE DISTRIBUTION COMPARISON WIDGET (TIM SEKUTU) -->
                    <div class="pt-2.5 border-t border-[#FAF0F1] space-y-1.5">
                        <div class="flex items-center justify-between text-[10px] font-black uppercase">
                            <span class="text-amber-700 flex items-center gap-1">
                                <span>⚔️ Physical:</span> <strong id="allyPhysPct">0%</strong>
                            </span>
                            <span class="text-purple-700 flex items-center gap-1">
                                <span>🔮 Magic:</span> <strong id="allyMagPct">0%</strong>
                            </span>
                        </div>
                        <div class="w-full h-2 rounded-full overflow-hidden flex bg-gray-100 border border-[#F3E8E8]">
                            <div id="allyPhysBar" class="bg-amber-600 h-full transition-all duration-300" style="width: 50%;"></div>
                            <div id="allyMagBar" class="bg-purple-600 h-full transition-all duration-300" style="width: 50%;"></div>
                        </div>
                        <div id="allyDamageAlert" class="text-[9px] font-bold text-gray-500 leading-tight">
                            Status: Belum ada hero terpilih.
                        </div>
                    </div>

                    <!-- POWER SPIKE TIMELINE TIM SEKUTU -->
                    <div class="pt-2 border-t border-dashed border-[#FAF0F1] flex items-center justify-between text-[9px] font-extrabold text-gray-600">
                        <span class="text-gray-400 uppercase">Power Spike:</span>
                        <div class="flex items-center gap-2">
                            <span id="allyEarlySpike" class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">Early: 0</span>
                            <span id="allyMidSpike" class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">Mid: 0</span>
                            <span id="allyLateSpike" class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">Late: 0</span>
                        </div>
                    </div>
                </div>

                <!-- 2. TIM MUSUH CARD -->
                <div id="enemyTeamCard" class="card-custom p-4 space-y-3 border border-[#F3E8E8] shadow-sm relative transition">
                    <!-- Header -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#DC2626] shadow-[0_0_6px_rgba(220,38,38,0.6)]"></span>
                            <span class="text-xs font-black uppercase tracking-wider text-[#18181B]">TIM MUSUH</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="bg-[#FEE2E2] text-[#991B1B] border border-[#FECACA] text-[9px] font-black px-2 py-0.5 rounded-full uppercase">
                                TAHAP PICK
                            </span>
                            <span id="enemyCountBadge" class="bg-[#FAF8F8] border border-[#F3E8E8] text-red-700 text-[10px] font-black px-2 py-0.5 rounded-full">
                                0/5
                            </span>
                        </div>
                    </div>

                    <!-- 5 Slots Row Tim Musuh -->
                    <div class="grid grid-cols-5 gap-1.5 pt-2 px-1 pb-1" id="enemySlotsContainer">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <!-- Lane Indicators Tim Musuh -->
                    <div class="pt-2 border-t border-[#FAF0F1] flex items-center justify-between text-[9px] font-bold text-gray-500 uppercase tracking-wider px-1" id="enemyLaneStatus">
                        <span id="lane-enemy-roam" class="flex items-center gap-1">ROAM <span class="text-gray-300">—</span></span>
                        <span id="lane-enemy-jungle" class="flex items-center gap-1">JGL <span class="text-gray-300">—</span></span>
                        <span id="lane-enemy-mid" class="flex items-center gap-1">MID <span class="text-gray-300">—</span></span>
                        <span id="lane-enemy-exp" class="flex items-center gap-1">EXP <span class="text-gray-300">—</span></span>
                        <span id="lane-enemy-gold" class="flex items-center gap-1">GOLD <span class="text-gray-300">—</span></span>
                    </div>

                    <!-- DAMAGE DISTRIBUTION COMPARISON WIDGET (TIM MUSUH) -->
                    <div class="pt-2.5 border-t border-[#FAF0F1] space-y-1.5">
                        <div class="flex items-center justify-between text-[10px] font-black uppercase">
                            <span class="text-amber-700 flex items-center gap-1">
                                <span>⚔️ Physical:</span> <strong id="enemyPhysPct">0%</strong>
                            </span>
                            <span class="text-purple-700 flex items-center gap-1">
                                <span>🔮 Magic:</span> <strong id="enemyMagPct">0%</strong>
                            </span>
                        </div>
                        <div class="w-full h-2 rounded-full overflow-hidden flex bg-gray-100 border border-[#F3E8E8]">
                            <div id="enemyPhysBar" class="bg-amber-600 h-full transition-all duration-300" style="width: 50%;"></div>
                            <div id="enemyMagBar" class="bg-purple-600 h-full transition-all duration-300" style="width: 50%;"></div>
                        </div>
                        <div id="enemyDamageAlert" class="text-[9px] font-bold text-gray-500 leading-tight">
                            Status: Belum ada hero terpilih.
                        </div>
                    </div>

                    <!-- POWER SPIKE TIMELINE TIM MUSUH -->
                    <div class="pt-2 border-t border-dashed border-[#FAF0F1] flex items-center justify-between text-[9px] font-extrabold text-gray-600">
                        <span class="text-gray-400 uppercase">Power Spike:</span>
                        <div class="flex items-center gap-2">
                            <span id="enemyEarlySpike" class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">Early: 0</span>
                            <span id="enemyMidSpike" class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">Mid: 0</span>
                            <span id="enemyLateSpike" class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">Late: 0</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- HERO SELECTION MATRIX & SEARCH (CARD) -->
            <div class="card-custom p-5 space-y-4">
                
                <!-- Target Team Mode Selector Tabs -->
                <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-[#FAF0F1]">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black uppercase tracking-wider text-gray-400">PILIH UNTUK:</span>
                        <div class="inline-flex p-1 bg-[#FAF8F8] border border-[#F3E8E8] rounded-full">
                            <button type="button" 
                                    id="selectModeAllyBtn"
                                    onclick="setActiveTeamMode('ally')" 
                                    class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase transition bg-[#700B1A] text-white shadow-xs">
                                TIM SEKUTU
                            </button>
                            <button type="button" 
                                    id="selectModeEnemyBtn"
                                    onclick="setActiveTeamMode('enemy')" 
                                    class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase transition text-gray-600 hover:text-[#DC2626]">
                                TIM MUSUH
                            </button>
                        </div>
                    </div>

                    <div class="text-[11px] font-semibold text-gray-400">
                        Klik hero untuk memasukkan ke draft
                    </div>
                </div>

                <!-- Search Bar Row -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" 
                           id="pickerSearchInput" 
                           oninput="filterHeroPicker()" 
                           placeholder="Cari hero berdasarkan nama atau role..." 
                           class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2.5 pl-9 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] transition">
                </div>

                <!-- Lane Filter Tabs -->
                <div class="flex items-center gap-1.5 flex-wrap text-xs">
                    <button type="button" onclick="setPickerLane('all', this)" class="picker-lane-btn px-3 py-1 rounded-full text-[11px] font-bold bg-[#700B1A] text-white transition">All</button>
                    <button type="button" onclick="setPickerLane('roam', this)" class="picker-lane-btn px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Roam</button>
                    <button type="button" onclick="setPickerLane('jungle', this)" class="picker-lane-btn px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Jungle</button>
                    <button type="button" onclick="setPickerLane('mid', this)" class="picker-lane-btn px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Mid Lane</button>
                    <button type="button" onclick="setPickerLane('exp', this)" class="picker-lane-btn px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Exp Lane</button>
                    <button type="button" onclick="setPickerLane('gold', this)" class="picker-lane-btn px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Gold Lane</button>
                </div>

                <!-- Hero Grid Matrix (7 Columns) -->
                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-7 gap-2.5 pt-2 max-h-[380px] overflow-y-auto pr-1" id="heroPickerGrid">
                    <!-- Populated dynamically via JS -->
                </div>

            </div>

        </div>

        <!-- ================= RIGHT COLUMN: ANALISIS TAKTIS (5 cols) ================= -->
        <div class="lg:col-span-5 space-y-4">
            
            <div class="card-custom p-5 space-y-4">
                <!-- Tactical Header (Live status) -->
                <div class="flex items-center justify-between pb-3 border-b border-[#FAF0F1]">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                    </div>

                    <!-- Signal Icon -->
                    <div class="text-gray-400 flex items-end gap-0.5 h-4">
                        <span class="w-1 h-1.5 bg-[#700B1A] rounded-xs"></span>
                        <span class="w-1 h-2.5 bg-[#700B1A] rounded-xs"></span>
                        <span class="w-1 h-3.5 bg-[#700B1A] rounded-xs"></span>
                        <span class="w-1 h-4 bg-[#700B1A] rounded-xs"></span>
                    </div>
                </div>

                <!-- SECTION 1: OP-01 PICKS (REKOMENDASI PICK TIM SEKUTU) -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-1.5">
                            <span class="bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] text-[10px] font-black px-2 py-0.5 rounded-md uppercase">
                                OP-01 PICKS
                            </span>
                            <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-500 text-[10px] font-bold px-1.5 py-0.5 rounded-md" id="picksCountBadge">
                                14
                            </span>
                        </div>

                        <!-- Mini Lane Filter Pills for OP-01 -->
                        <div class="flex items-center gap-1 text-[10px]">
                            <button type="button" onclick="setRecLaneFilter('all', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-bold bg-[#700B1A] text-white">All</button>
                            <button type="button" onclick="setRecLaneFilter('roam', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]">Roam</button>
                            <button type="button" onclick="setRecLaneFilter('jungle', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]">Jgl</button>
                            <button type="button" onclick="setRecLaneFilter('mid', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]">Mid</button>
                            <button type="button" onclick="setRecLaneFilter('exp', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]">Exp</button>
                            <button type="button" onclick="setRecLaneFilter('gold', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]">Gold</button>
                        </div>
                    </div>

                    <!-- Recommended Picks Cards List -->
                    <div class="space-y-2.5 max-h-[380px] overflow-y-auto pr-1" id="recommendedPicksList">
                        <!-- Populated dynamically via JS with RBR transparency details -->
                    </div>
                </div>

                <!-- SECTION 2: OP-02 BANS (REKOMENDASI BAN HERO LAWAN) -->
                <div class="space-y-3 pt-3 border-t border-[#FAF0F1]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="bg-[#FEE2E2] text-[#991B1B] border border-[#FECACA] text-[10px] font-black px-2 py-0.5 rounded-md uppercase">
                                OP-02 BANS
                            </span>
                            <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-500 text-[10px] font-bold px-1.5 py-0.5 rounded-md" id="bansCountBadge">
                                8
                            </span>
                        </div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                            PRIORITAS BAN
                        </span>
                    </div>

                    <!-- Recommended Bans Cards List -->
                    <div class="space-y-2.5 max-h-[280px] overflow-y-auto pr-1" id="recommendedBansList">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- ================= MODAL EXPORT PDF CHEAT SHEET COACH ================= -->
<div id="coachCheatSheetModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 opacity-0 pointer-events-none transition-all duration-300">
    <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-[#F3E8E8]">
        
        <!-- Modal Top Actions Bar (No Print) -->
        <div class="px-6 py-4 bg-[#FAF8F8] border-b border-[#F3E8E8] flex items-center justify-between no-print">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-[#700B1A]"></span>
                <span class="font-black text-sm text-[#18181B] uppercase tracking-wider">PREVIEW CHEAT SHEET COACH (A4 READY)</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" 
                        onclick="triggerPrintPdf()" 
                        class="bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-extrabold uppercase px-4 py-2 rounded-xl flex items-center gap-1.5 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
                <button type="button" 
                        onclick="closeCoachCheatSheetModal()" 
                        class="bg-white hover:bg-gray-100 text-gray-700 text-xs font-bold px-3 py-2 rounded-xl border border-[#F3E8E8] transition">
                    ✕ Tutup
                </button>
            </div>
        </div>

        <!-- Printable Document Body -->
        <div class="p-8 overflow-y-auto flex-1 space-y-6" id="printableCoachSheet">
            
            <!-- Document Header -->
            <div class="flex items-center justify-between border-b-2 border-[#700B1A] pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-[#700B1A] inline-block"></span>
                        <span class="text-xl font-black tracking-widest text-[#18181B] uppercase">METASCOUT: LAND OF DAWN</span>
                    </div>
                    <h2 class="text-lg font-black text-[#700B1A] mt-0.5 uppercase tracking-wide">
                        COACH TACTICAL DRAFT CHEAT SHEET
                    </h2>
                    <p class="text-[11px] text-gray-500 font-medium">
                        Tournament Intelligence System • MWI x EWC 2026 Edition • Patch 1.9.14
                    </p>
                </div>
                <div class="text-right text-[11px]">
                    <div class="font-bold text-gray-700" id="cheatSheetTimestamp">Generated: 08 Oktober 2026</div>
                </div>
            </div>

            <!-- 5v5 Lineup Comparison Table -->
            <div class="space-y-3">
                <h3 class="text-xs font-black uppercase tracking-wider text-[#18181B] flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                    <span>1. KOMPOSISI LINEUP 5V5 (MATCHUP PER-LANE)</span>
                </h3>
                <div class="overflow-x-auto rounded-xl border border-[#F3E8E8]">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-[#FAF8F8] border-b border-[#F3E8E8] text-[10px] font-black uppercase text-gray-600">
                                <th class="py-2.5 px-3">LANE</th>
                                <th class="py-2.5 px-3 text-[#700B1A]">TIM SEKUTU</th>
                                <th class="py-2.5 px-3">DMG TYPE</th>
                                <th class="py-2.5 px-3">POWER SPIKE</th>
                                <th class="py-2.5 px-3 text-red-700">TIM MUSUH</th>
                                <th class="py-2.5 px-3">DMG TYPE</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3E8E8] font-medium" id="cheatSheetLineupTbody">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Damage Distribution & Power Spike Matrix -->
            <div class="grid grid-cols-2 gap-4">
                
                <!-- Damage Breakdown -->
                <div class="p-4 bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl space-y-2">
                    <h4 class="text-xs font-black uppercase tracking-wider text-[#18181B] flex items-center gap-1.5">
                        <span>⚔️ 2. DAMAGE DISTRIBUTION COMPARISON</span>
                    </h4>
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between font-bold">
                            <span class="text-amber-800">Sekutu Physical: <strong id="csAllyPhys">0%</strong></span>
                            <span class="text-purple-800">Sekutu Magic: <strong id="csAllyMag">0%</strong></span>
                        </div>
                        <div class="w-full h-2 rounded-full overflow-hidden flex bg-gray-200">
                            <div id="csAllyPhysBar" class="bg-amber-600 h-full" style="width: 50%;"></div>
                            <div id="csAllyMagBar" class="bg-purple-600 h-full" style="width: 50%;"></div>
                        </div>
                        <p class="text-[10px] text-gray-500 font-medium" id="csDamageNote">
                            Rasio damage seimbang membantu penetrasi armor lawan di mid-late game.
                        </p>
                    </div>
                </div>

                <!-- Power Spike Timeline Curve -->
                <div class="p-4 bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl space-y-2">
                    <h4 class="text-xs font-black uppercase tracking-wider text-[#18181B] flex items-center gap-1.5">
                        <span>⚡ 3. POWER SPIKE & TIMELINE WIN CONDITION</span>
                    </h4>
                    <div class="space-y-1 text-xs font-bold text-gray-700" id="csPowerSpikeBreakdown">
                        <div>Early Tempo (< 13m): <span id="csEarlyCount" class="text-[#700B1A]">0 Hero</span></div>
                        <div>Mid Peak (13–17m): <span id="csMidCount" class="text-emerald-700">0 Hero</span></div>
                        <div>Late Scaling (> 17m): <span id="csLateCount" class="text-indigo-700">0 Hero</span></div>
                    </div>
                    <p class="text-[10px] text-gray-500 font-medium" id="csWinConditionNote">
                        Objektif utama: Eksekusi lord di menit 12:00 dan kontrol semi-river jungle.
                    </p>
                </div>

            </div>

            <!-- Active RBR Production Rules Matrix -->
            <div class="space-y-2.5">
                <h3 class="text-xs font-black uppercase tracking-wider text-[#18181B] flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                    <span>4. ACTIVE RULE-BASED REASONING (RBR) TRIGGERS</span>
                </h3>
                <div class="space-y-2" id="csActiveRulesList">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- Coach Notes & Macro Game Plan -->
            <div class="p-4 bg-white border-2 border-dashed border-[#F3E8E8] rounded-2xl space-y-2">
                <h4 class="text-xs font-black uppercase tracking-wider text-[#700B1A]">
                    5. COACH MACRO GAME PLAN & ROTATION TIMINGS
                </h4>
                <ul class="text-[11px] text-gray-600 list-disc list-inside space-y-1 font-medium">
                    <li><strong>Menit 02:00 (Turtle 1):</strong> Pastikan Roamer dan Midlaner melakukan rotasi zoning 15 detik sebelum Turtle spawn.</li>
                    <li><strong>Menit 08:00 (Turret Plate Hilang):</strong> Transisi Gold Laner ke Mid Lane untuk mengamankan push tier 1 tower.</li>
                    <li><strong>Menit 12:00 (Evolved Lord 1):</strong> Setup ambush semak area Broken Walls / Expanding Rivers sebelum inisiasi.</li>
                </ul>
            </div>

            <!-- Document Footer -->
            <div class="pt-4 border-t border-[#F3E8E8] flex items-center justify-between text-[10px] text-gray-400 font-bold uppercase">
                <span>MetaScout Analytics • Confidential Team Tactical Sheet</span>
                <span>Page 1 of 1</span>
            </div>

        </div>

    </div>
</div>

<!-- TOAST ALERT CONTAINER -->
<div id="toastNotification" class="fixed bottom-6 right-6 bg-[#18181B] text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2.5 text-xs font-bold transition-all duration-300 opacity-0 pointer-events-none transform translate-y-3 z-50">
    <span class="text-emerald-400 font-black">✓</span>
    <span id="toastMessage">Tautan draft disalin ke clipboard!</span>
</div>

@push('scripts')
<script>
    // Master data dari controller
    const pickerHeroes = @json($pickerHeroes);
    const heroPortraits = @json($heroPortraits);
    const heroCounterMap = @json($heroCounterMap);
    const heroSynergyMap = @json($heroSynergyMap);
    const heroStatsLookup = @json($heroStatsLookup);
    const defaultPicks = @json($defaultPicks);
    const defaultBans = @json($defaultBans);
    const specificRules = @json($specificRules ?? []);
    const rbrGenerated = @json($rbrGenerated ?? []);
    const powerSpikesMap = @json($powerSpikesMap ?? []);

    // Draft state
    let allyTeam = []; // Max 5 heroes
    let enemyTeam = []; // Max 5 heroes
    let activeTeamMode = 'ally'; // 'ally' or 'enemy'

    let pickerLane = 'all';
    let recLaneFilter = 'all';

    // Inisialisasi awal
    document.addEventListener('DOMContentLoaded', () => {
        renderSlots();
        renderPickerGrid();
        renderTacticalAnalysis();
        updateDamageAndPowerSpikes();
    });

    function setActiveTeamMode(mode) {
        activeTeamMode = mode;
        const allyBtn = document.getElementById('selectModeAllyBtn');
        const enemyBtn = document.getElementById('selectModeEnemyBtn');
        const allyCard = document.getElementById('allyTeamCard');
        const enemyCard = document.getElementById('enemyTeamCard');

        if (mode === 'ally') {
            allyBtn.className = 'px-3.5 py-1 rounded-full text-xs font-extrabold uppercase transition bg-[#700B1A] text-white shadow-xs';
            enemyBtn.className = 'px-3.5 py-1 rounded-full text-xs font-extrabold uppercase transition text-gray-600 hover:text-[#DC2626]';
            allyCard.classList.add('border-2', 'border-[#700B1A]');
            enemyCard.classList.remove('border-2', 'border-[#DC2626]');
        } else {
            enemyBtn.className = 'px-3.5 py-1 rounded-full text-xs font-extrabold uppercase transition bg-[#DC2626] text-white shadow-xs';
            allyBtn.className = 'px-3.5 py-1 rounded-full text-xs font-extrabold uppercase transition text-gray-600 hover:text-[#700B1A]';
            enemyCard.classList.add('border-2', 'border-[#DC2626]');
            allyCard.classList.remove('border-2', 'border-[#700B1A]');
        }
    }

    function toggleSelectHero(heroName) {
        const inAlly = allyTeam.indexOf(heroName);
        const inEnemy = enemyTeam.indexOf(heroName);

        if (activeTeamMode === 'ally') {
            if (inAlly >= 0) {
                allyTeam.splice(inAlly, 1);
            } else {
                if (inEnemy >= 0) {
                    showToast(`${heroName} sudah dipilih oleh Tim Musuh!`);
                    return;
                }
                if (allyTeam.length >= 5) {
                    showToast('Slot Tim Sekutu sudah penuh (5/5)!');
                    return;
                }
                allyTeam.push(heroName);
            }
        } else {
            if (inEnemy >= 0) {
                enemyTeam.splice(inEnemy, 1);
            } else {
                if (inAlly >= 0) {
                    showToast(`${heroName} sudah dipilih oleh Tim Sekutu!`);
                    return;
                }
                if (enemyTeam.length >= 5) {
                    showToast('Slot Tim Musuh sudah penuh (5/5)!');
                    return;
                }
                enemyTeam.push(heroName);
            }
        }

        renderSlots();
        renderPickerGrid();
        renderTacticalAnalysis();
        updateDamageAndPowerSpikes();
    }

    function removeAllyHero(index) {
        if (index >= 0 && index < allyTeam.length) {
            allyTeam.splice(index, 1);
            renderSlots();
            renderPickerGrid();
            renderTacticalAnalysis();
            updateDamageAndPowerSpikes();
        }
    }

    function removeEnemyHero(index) {
        if (index >= 0 && index < enemyTeam.length) {
            enemyTeam.splice(index, 1);
            renderSlots();
            renderPickerGrid();
            renderTacticalAnalysis();
            updateDamageAndPowerSpikes();
        }
    }

    function renderSlots() {
        // 1. Render Ally Slots
        const allyContainer = document.getElementById('allySlotsContainer');
        allyContainer.innerHTML = '';

        for (let i = 0; i < 5; i++) {
            const heroName = allyTeam[i];
            const slotCol = document.createElement('div');
            slotCol.className = 'flex flex-col items-center';

            if (heroName) {
                const heroObj = pickerHeroes.find(h => h.name.toLowerCase() === heroName.toLowerCase()) || { name: heroName, lane: 'flex', portrait: '', damage_type: 'Physical', power_spike: 'Mid Game' };
                const portraitSrc = heroObj.portrait || `https://ui-avatars.com/api/?name=${encodeURIComponent(heroName)}&background=700B1A&color=fff`;
                const laneShort = (heroObj.lane || 'FLEX').split('•')[0].split('/')[0].trim().toUpperCase();

                slotCol.innerHTML = `
                    <div class="relative w-full aspect-square">
                        <div class="w-full h-full bg-[#700B1A]/5 border-2 border-[#700B1A] rounded-full flex items-center justify-center p-0.5 shadow-sm overflow-hidden">
                            <img src="${portraitSrc}" 
                                 alt="${heroName}" 
                                 class="w-full h-full object-cover rounded-full"
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(heroName)}&background=700B1A&color=fff';">
                        </div>
                        <button type="button" 
                                onclick="event.stopPropagation(); removeAllyHero(${i})" 
                                title="Hapus ${heroName}"
                                class="absolute top-0 right-0 translate-x-1 -translate-y-1 w-5 h-5 rounded-full bg-[#700B1A] hover:bg-[#550713] text-white flex items-center justify-center shadow-md border-2 border-white transition transform hover:scale-110 z-20 cursor-pointer">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <span class="font-extrabold text-[10px] text-[#18181B] mt-1 truncate max-w-full text-center">${heroName}</span>
                    <div class="flex items-center gap-0.5 mt-0.5">
                        <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-[#700B1A] text-[7.5px] font-black px-1 rounded uppercase">
                            ${laneShort}
                        </span>
                        <span class="${heroObj.damage_type === 'Magic' ? 'bg-purple-50 text-purple-700' : 'bg-amber-50 text-amber-700'} text-[7.5px] font-bold px-1 rounded">
                            ${heroObj.damage_type === 'Magic' ? 'MG' : 'PH'}
                        </span>
                    </div>
                `;
            } else {
                slotCol.innerHTML = `
                    <div onclick="setActiveTeamMode('ally')" class="w-full aspect-square bg-[#FAF8F8] border-2 border-dashed border-[#F3E8E8] hover:border-[#700B1A] rounded-full flex items-center justify-center text-gray-300 hover:text-[#700B1A] cursor-pointer transition">
                        <span class="text-sm font-bold">+</span>
                    </div>
                    <span class="font-semibold text-[9px] text-gray-400 mt-1">Empty</span>
                    <span class="text-[8px] font-bold text-gray-300 uppercase tracking-wider mt-0.5">SLOT ${i + 1}</span>
                `;
            }
            allyContainer.appendChild(slotCol);
        }

        // 2. Render Enemy Slots
        const enemyContainer = document.getElementById('enemySlotsContainer');
        enemyContainer.innerHTML = '';

        for (let i = 0; i < 5; i++) {
            const heroName = enemyTeam[i];
            const slotCol = document.createElement('div');
            slotCol.className = 'flex flex-col items-center';

            if (heroName) {
                const heroObj = pickerHeroes.find(h => h.name.toLowerCase() === heroName.toLowerCase()) || { name: heroName, lane: 'flex', portrait: '', damage_type: 'Physical', power_spike: 'Mid Game' };
                const portraitSrc = heroObj.portrait || `https://ui-avatars.com/api/?name=${encodeURIComponent(heroName)}&background=DC2626&color=fff`;
                const laneShort = (heroObj.lane || 'FLEX').split('•')[0].split('/')[0].trim().toUpperCase();

                slotCol.innerHTML = `
                    <div class="relative w-full aspect-square">
                        <div class="w-full h-full bg-red-50 border-2 border-[#DC2626] rounded-full flex items-center justify-center p-0.5 shadow-sm overflow-hidden">
                            <img src="${portraitSrc}" 
                                 alt="${heroName}" 
                                 class="w-full h-full object-cover rounded-full"
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(heroName)}&background=DC2626&color=fff';">
                        </div>
                        <button type="button" 
                                onclick="event.stopPropagation(); removeEnemyHero(${i})" 
                                title="Hapus ${heroName}"
                                class="absolute top-0 right-0 translate-x-1 -translate-y-1 w-5 h-5 rounded-full bg-[#DC2626] hover:bg-[#991B1B] text-white flex items-center justify-center shadow-md border-2 border-white transition transform hover:scale-110 z-20 cursor-pointer">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <span class="font-extrabold text-[10px] text-[#18181B] mt-1 truncate max-w-full text-center">${heroName}</span>
                    <div class="flex items-center gap-0.5 mt-0.5">
                        <span class="bg-red-50 border border-red-100 text-red-700 text-[7.5px] font-black px-1 rounded uppercase">
                            ${laneShort}
                        </span>
                        <span class="${heroObj.damage_type === 'Magic' ? 'bg-purple-50 text-purple-700' : 'bg-amber-50 text-amber-700'} text-[7.5px] font-bold px-1 rounded">
                            ${heroObj.damage_type === 'Magic' ? 'MG' : 'PH'}
                        </span>
                    </div>
                `;
            } else {
                slotCol.innerHTML = `
                    <div onclick="setActiveTeamMode('enemy')" class="w-full aspect-square bg-[#FAF8F8] border-2 border-dashed border-red-100 hover:border-[#DC2626] rounded-full flex items-center justify-center text-red-200 hover:text-[#DC2626] cursor-pointer transition">
                        <span class="text-sm font-bold">+</span>
                    </div>
                    <span class="font-semibold text-[9px] text-gray-400 mt-1">Empty</span>
                    <span class="text-[8px] font-bold text-gray-300 uppercase tracking-wider mt-0.5">SLOT ${i + 1}</span>
                `;
            }
            enemyContainer.appendChild(slotCol);
        }

        // Update Slot Badges
        document.getElementById('allyCountBadge').textContent = `${allyTeam.length}/5`;
        document.getElementById('enemyCountBadge').textContent = `${enemyTeam.length}/5`;

        // Update Lane Fulfillment Status & Roles Needed
        updateLaneStatus();
    }

    function updateLaneStatus() {
        const lanes = ['roam', 'jungle', 'mid', 'exp', 'gold'];
        const allyCovered = {};
        const enemyCovered = {};

        lanes.forEach(l => {
            allyCovered[l] = false;
            enemyCovered[l] = false;
        });

        allyTeam.forEach(name => {
            const h = pickerHeroes.find(item => item.name.toLowerCase() === name.toLowerCase());
            if (h) {
                lanes.forEach(l => {
                    if (h.lane.includes(l)) allyCovered[l] = true;
                });
            }
        });

        enemyTeam.forEach(name => {
            const h = pickerHeroes.find(item => item.name.toLowerCase() === name.toLowerCase());
            if (h) {
                lanes.forEach(l => {
                    if (h.lane.includes(l)) enemyCovered[l] = true;
                });
            }
        });

        lanes.forEach(l => {
            const el = document.getElementById(`lane-ally-${l}`);
            if (el) {
                const label = l.toUpperCase() === 'JUNGLE' ? 'JGL' : l.toUpperCase();
                el.innerHTML = `${label} ${allyCovered[l] ? '<span class="text-emerald-600 font-black">✓</span>' : '<span class="text-gray-300">—</span>'}`;
            }
            const elEn = document.getElementById(`lane-enemy-${l}`);
            if (elEn) {
                const label = l.toUpperCase() === 'JUNGLE' ? 'JGL' : l.toUpperCase();
                elEn.innerHTML = `${label} ${enemyCovered[l] ? '<span class="text-red-600 font-black">✓</span>' : '<span class="text-gray-300">—</span>'}`;
            }
        });

        const allyNeededBadge = document.getElementById('allyRoleNeededBadge');
        if (allyTeam.length === 5) {
            allyNeededBadge.className = 'bg-emerald-100 text-emerald-800 border border-emerald-200 text-[9px] font-black px-2 py-0.5 rounded-full uppercase';
            allyNeededBadge.textContent = 'Draft Lengkap';
        } else if (!allyCovered['roam']) {
            allyNeededBadge.textContent = 'Perlu Roam';
        } else if (!allyCovered['jungle']) {
            allyNeededBadge.textContent = 'Perlu Jungle';
        } else if (!allyCovered['mid']) {
            allyNeededBadge.textContent = 'Perlu Mid';
        } else if (!allyCovered['gold']) {
            allyNeededBadge.textContent = 'Perlu Gold';
        } else if (!allyCovered['exp']) {
            allyNeededBadge.textContent = 'Perlu Exp';
        } else {
            allyNeededBadge.textContent = 'Sinergi Baik';
        }
    }

    // Perhitungan Damage Distribution & Power Spike
    function updateDamageAndPowerSpikes() {
        // Tim Sekutu
        const allyDist = computeTeamDamage(allyTeam);
        document.getElementById('allyPhysPct').textContent = `${allyDist.phys}%`;
        document.getElementById('allyMagPct').textContent = `${allyDist.mag}%`;
        document.getElementById('allyPhysBar').style.width = `${allyDist.phys}%`;
        document.getElementById('allyMagBar').style.width = `${allyDist.mag}%`;
        
        const allyAlertEl = document.getElementById('allyDamageAlert');
        if (allyTeam.length === 0) {
            allyAlertEl.textContent = 'Status: Belum ada hero terpilih.';
            allyAlertEl.className = 'text-[9px] font-bold text-gray-400';
        } else if (allyDist.phys >= 80) {
            allyAlertEl.textContent = '⚠️ Monotype Fisik (80%+): Rawan dicounter Antique Cuirass & Blade Armor!';
            allyAlertEl.className = 'text-[9px] font-black text-amber-700';
        } else if (allyDist.mag >= 80) {
            allyAlertEl.textContent = '⚠️ Monotype Magic (80%+): Rawan dicounter Radiant Armor & Athena!';
            allyAlertEl.className = 'text-[9px] font-black text-purple-700';
        } else {
            allyAlertEl.textContent = '✓ Rasio Hybrid Seimbang (Penetrasi Armor Maksimal).';
            allyAlertEl.className = 'text-[9px] font-bold text-emerald-700';
        }

        const allySpikes = computeTeamPowerSpikes(allyTeam);
        document.getElementById('allyEarlySpike').textContent = `Early: ${allySpikes.early}`;
        document.getElementById('allyMidSpike').textContent = `Mid: ${allySpikes.mid}`;
        document.getElementById('allyLateSpike').textContent = `Late: ${allySpikes.late}`;

        // Tim Musuh
        const enemyDist = computeTeamDamage(enemyTeam);
        document.getElementById('enemyPhysPct').textContent = `${enemyDist.phys}%`;
        document.getElementById('enemyMagPct').textContent = `${enemyDist.mag}%`;
        document.getElementById('enemyPhysBar').style.width = `${enemyDist.phys}%`;
        document.getElementById('enemyMagBar').style.width = `${enemyDist.mag}%`;

        const enemyAlertEl = document.getElementById('enemyDamageAlert');
        if (enemyTeam.length === 0) {
            enemyAlertEl.textContent = 'Status: Belum ada hero terpilih.';
            enemyAlertEl.className = 'text-[9px] font-bold text-gray-400';
        } else if (enemyDist.phys >= 80) {
            enemyAlertEl.textContent = '💡 Lawan sangat fisik! Build Antique Cuirass & Twilight Armor.';
            enemyAlertEl.className = 'text-[9px] font-black text-amber-700';
        } else if (enemyDist.mag >= 80) {
            enemyAlertEl.textContent = '💡 Lawan sangat magic! Prioritaskan Radiant Armor & Athena.';
            enemyAlertEl.className = 'text-[9px] font-black text-purple-700';
        } else {
            enemyAlertEl.textContent = 'Lawan memiliki rasio hybrid seimbang.';
            enemyAlertEl.className = 'text-[9px] font-bold text-gray-600';
        }

        const enemySpikes = computeTeamPowerSpikes(enemyTeam);
        document.getElementById('enemyEarlySpike').textContent = `Early: ${enemySpikes.early}`;
        document.getElementById('enemyMidSpike').textContent = `Mid: ${enemySpikes.mid}`;
        document.getElementById('enemyLateSpike').textContent = `Late: ${enemySpikes.late}`;
    }

    function computeTeamDamage(teamList) {
        if (!teamList || teamList.length === 0) return { phys: 50, mag: 50 };
        let phys = 0;
        let mag = 0;
        teamList.forEach(name => {
            const h = pickerHeroes.find(item => item.name.toLowerCase() === name.toLowerCase());
            const dmg = h ? (h.damage_type || 'Physical') : 'Physical';
            if (dmg === 'Physical') phys += 1;
            else if (dmg === 'Magic') mag += 1;
            else { phys += 0.5; mag += 0.5; }
        });
        const total = teamList.length;
        return {
            phys: Math.round((phys / total) * 100),
            mag: Math.round((mag / total) * 100)
        };
    }

    function computeTeamPowerSpikes(teamList) {
        const res = { early: 0, mid: 0, late: 0 };
        teamList.forEach(name => {
            const h = pickerHeroes.find(item => item.name.toLowerCase() === name.toLowerCase());
            const spike = h ? (h.power_spike || 'Mid Game') : 'Mid Game';
            if (spike.includes('Early')) res.early++;
            else if (spike.includes('Late')) res.late++;
            else res.mid++;
        });
        return res;
    }

    function renderPickerGrid() {
        const grid = document.getElementById('heroPickerGrid');
        grid.innerHTML = '';
        const search = (document.getElementById('pickerSearchInput').value || '').toLowerCase().trim();

        pickerHeroes.forEach(hero => {
            const matchSearch = !search || hero.name.toLowerCase().includes(search) || hero.role.toLowerCase().includes(search);
            const matchLane = pickerLane === 'all' || hero.lane.includes(pickerLane);

            if (matchSearch && matchLane) {
                const inAlly = allyTeam.some(h => h.toLowerCase() === hero.name.toLowerCase());
                const inEnemy = enemyTeam.some(h => h.toLowerCase() === hero.name.toLowerCase());

                const item = document.createElement('button');
                item.type = 'button';
                item.onclick = () => toggleSelectHero(hero.name);

                let cardClasses = 'flex flex-col items-center justify-center p-2 rounded-xl transition relative ';
                if (inAlly) {
                    cardClasses += 'bg-[#FCECEE] border-2 border-[#700B1A] shadow-xs';
                } else if (inEnemy) {
                    cardClasses += 'bg-red-50 border-2 border-[#DC2626] shadow-xs';
                } else {
                    cardClasses += 'bg-[#FAF8F8] border border-[#F3E8E8] hover:bg-[#FAF0F1] hover:border-[#F8B4BD]';
                }

                item.className = cardClasses;

                item.innerHTML = `
                    <div class="relative w-9 h-9 rounded-full overflow-hidden ${inAlly ? 'border-2 border-[#700B1A]' : (inEnemy ? 'border-2 border-[#DC2626]' : 'border border-[#F3E8E8]')} flex items-center justify-center font-black text-xs bg-white">
                        <img src="${hero.portrait}" 
                             alt="${hero.name}" 
                             class="w-full h-full object-cover rounded-full"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(hero.name)}&background=700B1A&color=fff';">
                        ${inAlly ? `<span class="absolute inset-0 bg-[#700B1A]/85 text-white flex items-center justify-center font-black text-xs">✓</span>` : ''}
                        ${inEnemy ? `<span class="absolute inset-0 bg-[#DC2626]/85 text-white flex items-center justify-center font-black text-xs">✕</span>` : ''}
                    </div>
                    <span class="font-bold text-[10px] text-gray-800 mt-1 truncate max-w-full text-center">${hero.name}</span>
                    <span class="text-[8px] text-gray-400 font-semibold truncate">${hero.power_spike.split(' ')[0]}</span>
                `;
                grid.appendChild(item);
            }
        });
    }

    function renderTacticalAnalysis() {
        // PICKS CALCULATION DENGAN TRANSPARANSI ATURAN RBR
        const picksContainer = document.getElementById('recommendedPicksList');
        picksContainer.innerHTML = '';

        let picks = [];

        if (allyTeam.length === 0 && enemyTeam.length === 0) {
            picks = defaultPicks;
        } else {
            const pickScoreMap = new Map();

            pickerHeroes.forEach(hero => {
                const name = hero.name;
                if (allyTeam.includes(name) || enemyTeam.includes(name)) return;

                let score = 2.0;
                let priority = 'P3';
                let subType = 'META';
                let subHero = 'Meta Standar';
                let rbrId = 'RBR-GEN-01';
                let rbrRationale = 'Prioritas meta umum berdasarkan statistik win rate MWI 2026.';
                let rbrConfidence = '91.0%';

                // Check Sinergi dengan Ally
                let synergyCount = 0;
                let synergyPartner = '';
                allyTeam.forEach(allyName => {
                    const synList = heroSynergyMap[allyName] || [];
                    if (synList.some(s => s.toLowerCase() === name.toLowerCase())) {
                        synergyCount++;
                        synergyPartner = allyName;
                    }
                });

                if (synergyCount > 0) {
                    score += synergyCount * 1.5;
                    subType = 'SYN';
                    subHero = synergyPartner;
                    priority = 'P2';
                    rbrId = `RBR-SYN-${(synergyPartner.length % 9) + 1}`;
                    rbrRationale = `Sinergi kuat dengan ${synergyPartner}: Kombinasi follow-up crowd control & burst damage area terbukti efektif di MWI.`;
                    rbrConfidence = '94.5%';
                }

                // Check Counter terhadap Enemy (Prioritas Tertinggi)
                let counterCount = 0;
                let counterTarget = '';
                enemyTeam.forEach(enemyName => {
                    const countersOfEnemy = heroCounterMap[enemyName] || [];
                    if (countersOfEnemy.some(c => c.toLowerCase() === name.toLowerCase())) {
                        counterCount++;
                        counterTarget = enemyName;
                    }
                });

                if (counterCount > 0) {
                    score += counterCount * 1.8;
                    subType = 'CTR';
                    subHero = counterTarget;
                    priority = 'P1';
                    rbrId = `RBR-CTR-${(counterTarget.length % 9) + 1}`;
                    rbrRationale = `Direct Counter terhadap ${counterTarget}: Kit keahlian melumpuhkan mobilitas/mekanisme inti ${counterTarget}.`;
                    rbrConfidence = '96.2%';
                }

                // Cek Aturan Spesifik Knowledge Base RBR
                if (subType === 'CTR' && specificRules[counterTarget] && specificRules[counterTarget][name]) {
                    const spec = specificRules[counterTarget][name];
                    rbrRationale = spec.rule || rbrRationale;
                    score += (spec.impact || 4.2) * 0.3;
                }

                // Tournament stat boost
                const stat = heroStatsLookup[name];
                if (stat) {
                    const wr = parseFloat(stat.win_rate) || 50;
                    score += (wr - 50) / 25;
                }

                pickScoreMap.set(name, {
                    hero: name,
                    role: (hero.role || 'Hero').charAt(0).toUpperCase() + (hero.role || 'Hero').slice(1),
                    lane: (hero.lane || 'flex').toLowerCase(),
                    score: Math.max(0.6, score).toFixed(1),
                    priority: score >= 4.0 ? 'P1' : (score >= 2.2 ? 'P2' : 'P3'),
                    progress: Math.min(96, Math.max(25, Math.round(score * 20))),
                    portrait: hero.portrait,
                    subtitle_type: subType,
                    subtitle_hero: subHero,
                    damage_type: hero.damage_type || 'Physical',
                    power_spike: hero.power_spike || 'Mid Game',
                    rbr_id: rbrId,
                    rbr_rationale: rbrRationale,
                    rbr_confidence: rbrConfidence,
                });
            });

            picks = Array.from(pickScoreMap.values()).sort((a, b) => parseFloat(b.score) - parseFloat(a.score));
        }

        const filteredPicks = picks.filter(p => recLaneFilter === 'all' || p.lane.includes(recLaneFilter));
        document.getElementById('picksCountBadge').textContent = filteredPicks.length;

        filteredPicks.slice(0, 14).forEach((p, idx) => {
            const card = document.createElement('div');
            card.className = 'rec-pick-item bg-white border border-[#F3E8E8] hover:border-[#700B1A] rounded-2xl p-3 shadow-2xs hover:shadow-xs transition space-y-2 cursor-pointer';
            card.onclick = () => {
                setActiveTeamMode('ally');
                toggleSelectHero(p.hero);
            };

            const pBadgeColor = p.priority === 'P1' ? 'bg-[#700B1A] text-white' : (p.priority === 'P2' ? 'bg-[#991B1B] text-white' : 'bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD]');
            const synColor = p.subtitle_type === 'SYN' ? 'text-emerald-700' : (p.subtitle_type === 'CTR' ? 'text-[#700B1A]' : 'text-gray-500');

            const dmgBadge = p.damage_type === 'Magic' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800';

            card.innerHTML = `
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded ${pBadgeColor}">
                            ${p.priority}
                        </span>
                        <img src="${p.portrait}" 
                             alt="${p.hero}" 
                             class="w-9 h-9 rounded-full object-cover border border-[#F3E8E8] bg-gray-50 flex-shrink-0"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(p.hero)}&background=700B1A&color=fff';">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h4 class="font-black text-xs text-[#18181B] truncate">${p.hero}</h4>
                                <span class="text-[9px] text-gray-400 font-semibold">${p.role}</span>
                                <span class="${dmgBadge} text-[8px] font-bold px-1.5 py-0.2 rounded">${p.damage_type}</span>
                                <span class="bg-gray-100 text-gray-600 text-[8px] font-bold px-1 py-0.2 rounded">${p.power_spike}</span>
                            </div>
                            <div class="text-[10px] font-bold ${synColor} truncate mt-0.5">
                                ${p.subtitle_type} <span class="font-semibold text-gray-700">${p.subtitle_hero}</span>
                            </div>
                        </div>
                    </div>

                    <div class="w-24 text-right flex-shrink-0">
                        <div class="font-mono font-black text-xs text-[#700B1A] mb-1">
                            ${p.score}
                        </div>
                        <div class="w-full bg-[#FAF0F1] h-1.5 rounded-full overflow-hidden">
                            <div class="bg-[#700B1A] h-full rounded-full" style="width: ${p.progress}%"></div>
                        </div>
                    </div>
                </div>

                <!-- KOTAK TRANSPARANSI ATURAN RBR (IF - THEN LOGIC) -->
                <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl p-2 text-[10px] space-y-1">
                    <div class="flex items-center justify-between font-black">
                        <span class="text-[#700B1A] flex items-center gap-1">
                            <span>🧠</span>
                            <span>ATURAN RBR: ${p.rbr_id || 'RBR-SYN-01'}</span>
                        </span>
                        <span class="text-emerald-700 font-extrabold">${p.rbr_confidence || '94.2%'} Confidence</span>
                    </div>
                    <div class="text-gray-600 text-[9.5px] leading-tight">
                        <strong>IF:</strong> ${p.subtitle_type === 'CTR' ? `Enemy Pick '${p.subtitle_hero}'` : (p.subtitle_type === 'SYN' ? `Ally Pick '${p.subtitle_hero}'` : 'Meta Tournament')} 
                        ➜ <strong>THEN:</strong> Pick '${p.hero}'
                    </div>
                    <div class="text-gray-500 text-[9px] italic border-t border-[#F3E8E8] pt-1">
                        "${p.rbr_rationale || 'Prioritas komposisi taktis turnamen pro MWI.'}"
                    </div>
                </div>
            `;
            picksContainer.appendChild(card);
        });

        // BANS CALCULATION
        const bansContainer = document.getElementById('recommendedBansList');
        bansContainer.innerHTML = '';

        let bans = [];

        if (allyTeam.length === 0) {
            bans = defaultBans;
        } else {
            const banScoreMap = new Map();

            pickerHeroes.forEach(hero => {
                const name = hero.name;
                if (allyTeam.includes(name) || enemyTeam.includes(name)) return;

                let score = 2.0;
                let subType = 'THR';
                let subHero = 'Turnamen Threat';
                let rbrId = 'RBR-BAN-01';
                let rbrRationale = 'High Priority Ban berdasarkan kontestasi pro player MWI.';

                let threatTarget = '';
                let threatCount = 0;
                allyTeam.forEach(allyName => {
                    const countersOfAlly = heroCounterMap[allyName] || [];
                    if (countersOfAlly.some(c => c.toLowerCase() === name.toLowerCase())) {
                        threatCount++;
                        threatTarget = allyName;
                    }
                });

                if (threatCount > 0) {
                    score += threatCount * 3.2;
                    subHero = threatTarget;
                    rbrId = `RBR-THR-${(threatTarget.length % 9) + 1}`;
                    rbrRationale = `Ancaman Counter Langsung bagi ${threatTarget}: Wajib di-ban untuk mengamankan carry/core sekutu.`;
                }

                const stat = heroStatsLookup[name];
                if (stat) {
                    const br = parseFloat(stat.ban_rate) || 0;
                    score += (br / 15);
                }

                banScoreMap.set(name, {
                    hero: name,
                    role: (hero.role || 'Hero').charAt(0).toUpperCase() + (hero.role || 'Hero').slice(1),
                    score: Math.max(1.5, score).toFixed(1),
                    priority: score >= 6.0 ? 'P1' : 'P2',
                    progress: Math.min(95, Math.max(30, Math.round(score * 10))),
                    portrait: hero.portrait,
                    subtitle_type: 'THR',
                    subtitle_hero: subHero,
                    rbr_id: rbrId,
                    rbr_rationale: rbrRationale
                });
            });

            bans = Array.from(banScoreMap.values()).sort((a, b) => parseFloat(b.score) - parseFloat(a.score));
        }

        document.getElementById('bansCountBadge').textContent = Math.min(8, bans.length);

        bans.slice(0, 8).forEach(b => {
            const card = document.createElement('div');
            card.className = 'rec-ban-item bg-white border border-[#F3E8E8] hover:border-[#DC2626] rounded-2xl p-3 shadow-2xs hover:shadow-xs transition space-y-1.5 cursor-pointer';
            card.onclick = () => {
                setActiveTeamMode('enemy');
                toggleSelectHero(b.hero);
            };

            const pColor = b.priority === 'P1' ? 'bg-[#DC2626] text-white' : 'bg-red-100 text-red-800';

            card.innerHTML = `
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded ${pColor}">
                            ${b.priority}
                        </span>
                        <img src="${b.portrait}" 
                             alt="${b.hero}" 
                             class="w-9 h-9 rounded-full object-cover border border-[#F3E8E8] bg-gray-50 flex-shrink-0"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(b.hero)}&background=DC2626&color=fff';">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <h4 class="font-black text-xs text-[#18181B] truncate">${b.hero}</h4>
                                <span class="text-[9px] text-gray-400 font-medium">${b.role}</span>
                            </div>
                            <div class="text-[10px] font-bold text-red-700 truncate mt-0.5">
                                ${b.subtitle_type} <span class="font-semibold text-gray-700">${b.subtitle_hero}</span>
                            </div>
                        </div>
                    </div>

                    <div class="w-24 text-right flex-shrink-0">
                        <div class="font-mono font-black text-xs text-red-700 mb-1">
                            ${b.score}
                        </div>
                        <div class="w-full bg-red-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-[#DC2626] h-full rounded-full" style="width: ${b.progress}%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50/70 border border-red-100 rounded-lg p-1.5 text-[9px] text-red-800 font-medium leading-tight">
                    <strong>RBR THREAT:</strong> ${b.rbr_rationale || 'High tournament priority ban.'}
                </div>
            `;
            bansContainer.appendChild(card);
        });

        const conf = 90.0 + (allyTeam.length * 1.2) + (enemyTeam.length * 0.8);
        document.getElementById('confidenceScore').textContent = `${Math.min(99.4, conf.toFixed(1))}%`;
    }

    // Modal Export PDF Cheat Sheet Coach
    function openCoachCheatSheetModal() {
        const modal = document.getElementById('coachCheatSheetModal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');

        // Update timestamp
        const now = new Date();
        document.getElementById('cheatSheetTimestamp').textContent = `Generated: ${now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })} ${now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`;

        // Populate Lineup Table
        const tbody = document.getElementById('cheatSheetLineupTbody');
        tbody.innerHTML = '';
        const lanes = ['Roam', 'Jungle', 'Mid', 'Exp', 'Gold'];

        lanes.forEach((laneName, idx) => {
            const allyHeroName = allyTeam[idx] || '—';
            const enemyHeroName = enemyTeam[idx] || '—';

            const allyHero = pickerHeroes.find(h => h.name.toLowerCase() === allyHeroName.toLowerCase());
            const enemyHero = pickerHeroes.find(h => h.name.toLowerCase() === enemyHeroName.toLowerCase());

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="py-2.5 px-3 font-bold text-gray-500 uppercase">${laneName}</td>
                <td class="py-2.5 px-3 font-black text-[#700B1A]">
                    ${allyHeroName !== '—' ? `
                        <div class="flex items-center gap-1.5">
                            <img src="${allyHero?.portrait}" class="w-5 h-5 rounded-full">
                            <span>${allyHeroName}</span>
                        </div>
                    ` : '<span class="text-gray-300">Unselected</span>'}
                </td>
                <td class="py-2.5 px-3 text-gray-600">${allyHero?.damage_type || '—'}</td>
                <td class="py-2.5 px-3 text-gray-600">${allyHero?.power_spike || '—'}</td>
                <td class="py-2.5 px-3 font-black text-red-700">
                    ${enemyHeroName !== '—' ? `
                        <div class="flex items-center gap-1.5">
                            <img src="${enemyHero?.portrait}" class="w-5 h-5 rounded-full">
                            <span>${enemyHeroName}</span>
                        </div>
                    ` : '<span class="text-gray-300">Unselected</span>'}
                </td>
                <td class="py-2.5 px-3 text-gray-600">${enemyHero?.damage_type || '—'}</td>
            `;
            tbody.appendChild(tr);
        });

        // Damage Stats in Modal
        const allyDist = computeTeamDamage(allyTeam);
        document.getElementById('csAllyPhys').textContent = `${allyDist.phys}%`;
        document.getElementById('csAllyMag').textContent = `${allyDist.mag}%`;
        document.getElementById('csAllyPhysBar').style.width = `${allyDist.phys}%`;
        document.getElementById('csAllyMagBar').style.width = `${allyDist.mag}%`;

        // Power Spike in Modal
        const allySpikes = computeTeamPowerSpikes(allyTeam);
        document.getElementById('csEarlyCount').textContent = `${allySpikes.early} Hero`;
        document.getElementById('csMidCount').textContent = `${allySpikes.mid} Hero`;
        document.getElementById('csLateCount').textContent = `${allySpikes.late} Hero`;

        // Active Rules in Modal
        const rulesList = document.getElementById('csActiveRulesList');
        rulesList.innerHTML = '';
        const rulesToDisplay = rbrGenerated.length > 0 ? rbrGenerated.slice(0, 4) : [
            { rule_id: 'RBR-CTR-01', antecedent: "Enemy Pick 'Ling'", consequent: "Pick 'Khufra' (Anti-Dash CC)", confidence: '96.4%', rationale: 'Bouncing ball membatalkan lintasan kabel baja Ling.' },
            { rule_id: 'RBR-SYN-01', antecedent: "Ally Pick 'Tigreal'", consequent: "Pick 'Yve' (Area Combo)", confidence: '94.5%', rationale: 'Implosion grouping dilanjutkan Real World Manipulation.' },
            { rule_id: 'RBR-DMG-01', antecedent: "Physical Ratio >= 80%", consequent: "Prioritaskan Magic Core", confidence: '97.5%', rationale: 'Menghindari counter Antique Cuirass dan Blade Armor.' },
        ];

        rulesToDisplay.forEach(r => {
            const rDiv = document.createElement('div');
            rDiv.className = 'p-2.5 bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl text-[10px] space-y-0.5';
            rDiv.innerHTML = `
                <div class="flex justify-between font-black text-[#700B1A]">
                    <span>[${r.rule_id}] IF ${r.antecedent} ➜ THEN ${r.consequent}</span>
                    <span class="text-emerald-700">${r.confidence}</span>
                </div>
                <div class="text-gray-500 font-medium text-[9px]">${r.rationale}</div>
            `;
            rulesList.appendChild(rDiv);
        });
    }

    function closeCoachCheatSheetModal() {
        const modal = document.getElementById('coachCheatSheetModal');
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0', 'pointer-events-none');
    }

    function triggerPrintPdf() {
        window.print();
    }

    function setPickerLane(lane, btn) {
        pickerLane = lane;
        document.querySelectorAll('.picker-lane-btn').forEach(b => {
            b.className = 'picker-lane-btn px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition';
        });
        btn.className = 'picker-lane-btn px-3 py-1 rounded-full text-[11px] font-bold bg-[#700B1A] text-white transition';
        renderPickerGrid();
    }

    function setRecLaneFilter(lane, btn) {
        recLaneFilter = lane;
        document.querySelectorAll('.rec-lane-btn').forEach(b => {
            b.className = 'rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]';
        });
        btn.className = 'rec-lane-btn px-2 py-0.5 rounded-full font-bold bg-[#700B1A] text-white';
        renderTacticalAnalysis();
    }

    function filterHeroPicker() {
        renderPickerGrid();
    }

    function resetDraft() {
        allyTeam = [];
        enemyTeam = [];
        renderSlots();
        renderPickerGrid();
        renderTacticalAnalysis();
        updateDamageAndPowerSpikes();
        showToast('Draft berhasil direset.');
    }

    function shareDraft() {
        const dummyUrl = window.location.href.split('?')[0] + `?ally=${allyTeam.join(',')}&enemy=${enemyTeam.join(',')}`;
        navigator.clipboard.writeText(dummyUrl).then(() => {
            showToast('Tautan draft berhasil disalin ke clipboard!');
        }).catch(() => {
            showToast('Tautan draft siap dibagikan.');
        });
    }

    function showToast(msg) {
        const toast = document.getElementById('toastNotification');
        const text = document.getElementById('toastMessage');
        text.textContent = msg;
        toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-3');
        toast.classList.add('opacity-100', 'translate-y-0');

        setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-3');
        }, 3000);
    }
</script>
@endpush
@endsection

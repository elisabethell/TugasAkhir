@extends('layouts.app')

@section('title', 'Rekomendasi Draft')

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
                    class="bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] rounded-full px-3.5 py-1.5 sm:py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Draft Baru</span>
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

    <!-- ================= 1. COMPACT DRAFT DNA TIM SELECTOR & MINI SUMMARY (MPL ID S18) ================= -->
    <div class="card-custom p-3 sm:p-3.5 border-2 border-[#700B1A]/35 ring-2 ring-[#700B1A]/10 shadow-sm bg-white transition-all" id="teamDnaSelectorContainer">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3" id="teamDnaProfileCard">
            
            <!-- Left: Dropdown Tim & Win Rate / Playstyle Tag -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="w-6 h-6 rounded-full bg-[#700B1A] text-white flex items-center justify-center text-[11px] font-black shadow-xs">
                        1
                    </span>
                    <span class="text-xs font-black uppercase text-[#18181B] tracking-wider">
                        PILIH TIM / DRAFT DNA:
                    </span>
                </div>

                <!-- Dropdown Select 9 Tim MPL ID S18 -->
                <div class="relative flex-shrink-0">
                    <select id="teamSelectDropdown" 
                            onchange="selectTeamDna(this.value)" 
                            class="appearance-none bg-[#FAF8F8] hover:bg-[#FAF0F1] border-2 border-[#700B1A] ring-4 ring-[#700B1A]/15 text-gray-900 text-xs font-black rounded-xl pl-3 pr-7 py-1.5 shadow-2xs cursor-pointer focus:outline-none focus:ring-4 focus:ring-[#700B1A]/25 transition">
                        <option value="" selected>-- Pilih Tim / Draft DNA --</option>
                        @foreach($allTeamsDna ?? [] as $t)
                            <option value="{{ $t['team_name'] }}">
                                {{ $t['team_name'] }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Current Team Logo inside Select Input -->
                    <div class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none">
                        <img id="teamSelectedLogoImg" src="/images/teams/default.png" alt="Logo" class="w-4 h-4 object-contain hidden" onerror="this.src='/images/teams/default.png'">
                    </div>
                    <!-- Caret Icon -->
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                <!-- Badge Win Rate Tim -->
                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl text-xs font-black shadow-2xs">
                    <span class="text-gray-400 text-[10px] font-black uppercase">Win Rate:</span>
                    <span id="compactTeamWr" class="text-[#700B1A] font-black text-xs">-</span>
                    <span id="compactTeamRecord" class="text-gray-400 text-[9.5px] font-medium"></span>
                </div>
            </div>

            <!-- Right: Compact Top Hero Pool & Top Bans (Baris Compact) -->
            <div class="flex items-center gap-3.5 flex-wrap pt-1 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                <!-- Top Hero Pool -->
                <div class="flex items-center gap-1.5">
                    <span class="text-[9.5px] font-black uppercase text-gray-400 flex-shrink-0">
                        Top Hero Pool:
                    </span>
                    <div id="compactTopHeroPool" class="flex items-center gap-1">
                        <span class="text-xs font-bold text-gray-400">-</span>
                    </div>
                </div>

                <span class="h-4 w-px bg-gray-200 hidden sm:inline-block"></span>

                <!-- Top Bans -->
                <div class="flex items-center gap-1.5">
                    <span class="text-[9.5px] font-black uppercase text-red-500 flex-shrink-0 flex items-center gap-1">
                        <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/></svg>
                        <span>Top Bans:</span>
                    </span>
                    <div id="compactTopBans" class="flex items-center gap-1">
                        <span class="text-xs font-bold text-gray-400">-</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- PANDUAN LANGKAH 1: WAJIB PILIH TIM SEBELUM SIMULASI DRAFT -->
    <div id="draftStepLockBanner" class="bg-[#FCECEE]/75 border border-[#F8B4BD] rounded-2xl px-4 py-3 flex items-center justify-between flex-wrap gap-2 shadow-2xs">
        <div class="flex items-center gap-2.5">
            <span class="w-6 h-6 rounded-full bg-[#700B1A] text-white flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </span>
            <p class="text-xs font-bold text-[#700B1A]">
                Pilih Tim (Draft DNA) terlebih dahulu untuk memulai simulasi rekomendasi draft.
            </p>
        </div>
        <button type="button" onclick="document.getElementById('teamSelectDropdown')?.focus()" class="text-[11px] font-black uppercase tracking-wider text-white bg-[#700B1A] hover:bg-[#550713] px-3 py-1.5 rounded-xl transition cursor-pointer">
            Pilih Tim di Atas &uarr;
        </button>
    </div>

    <!-- 2. TWO-COLUMN DRAFT ARENA (TERKUNCI HINGGA TIM DIPILIH) -->
    <div id="draftSimulationLockWrapper" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start opacity-50 pointer-events-none select-none transition-all duration-200">

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

                    <!-- PANEL ANALISIS STRATEGI (OUTPUT RBR RINGAN) -->
                    <div class="pt-2.5 border-t border-[#FAF0F1] space-y-2" id="allyIdentitySection">
                        <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-wider flex-wrap gap-1">
                            <span class="text-[#700B1A] flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#700B1A] animate-pulse"></span>
                                <span>Analisis Strategi (RBR Ringan)</span>
                            </span>
                            <span class="text-[9px] bg-[#FAF8F8] border border-[#F3E8E8] text-gray-500 px-1.5 py-0.5 rounded font-bold">
                                Real-Time Engine
                            </span>
                        </div>

                        <!-- Empty State sebelum ada hero sekutu terpilih -->
                        <div id="allyStrategyEmptyState" class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl p-3 text-center">
                            <p class="text-[11px] text-gray-500 font-medium leading-snug">
                                Pilih minimal 1 hero sekutu untuk menganalisis Draft Identity, Power Spike, dan Winning Condition.
                            </p>
                        </div>

                        <!-- Hasil Analisis Strategi (Ditampilkan setelah minimal 1 hero sekutu di-pick) -->
                        <div id="allyStrategyResultContent" class="space-y-2 hidden">
                            <!-- 3 Badges Terstruktur: Draft Identity, Power Spike, Cara Main -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5">
                                <!-- 1. Draft Identity (Badge Utama) -->
                                <div class="p-2 rounded-xl bg-white border border-[#F3E8E8] shadow-2xs">
                                    <div class="text-[8.5px] font-black uppercase text-gray-400 tracking-wider">Draft Identity:</div>
                                    <div class="pt-0.5" id="allyIdentityBadge">
                                        <span id="strategyDraftIdentity" class="text-[10.5px] font-black text-[#700B1A] truncate block" title="Draft Identity">-</span>
                                        <span id="allyIdentityBadgeText" class="hidden">Draft Belum Terbentuk</span>
                                    </div>
                                </div>

                                <!-- 2. Power Spike (Fase Game) -->
                                <div class="p-2 rounded-xl bg-white border border-[#F3E8E8] shadow-2xs">
                                    <div class="text-[8.5px] font-black uppercase text-gray-400 tracking-wider">Power Spike:</div>
                                    <div class="pt-0.5">
                                        <span id="strategyPowerSpike" class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-indigo-50 border border-indigo-200 text-indigo-700 font-black text-[9.5px]">-</span>
                                        <span id="allyPowerSpikeBadge" class="hidden">-</span>
                                    </div>
                                </div>

                                <!-- 3. Cara Main / Playstyle -->
                                <div class="p-2 rounded-xl bg-white border border-[#F3E8E8] shadow-2xs">
                                    <div class="text-[8.5px] font-black uppercase text-gray-400 tracking-wider">Cara Main:</div>
                                    <div class="pt-0.5">
                                        <span id="strategyPlaystyle" class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-amber-50 border border-amber-200 text-amber-800 font-black text-[9.5px]">-</span>
                                        <span id="allyPlaystyleBadge" class="hidden">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Winning Condition (Kotak Teks Singkat & Jelas) -->
                            <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl p-2.5 space-y-1">
                                <div class="text-[10px] font-extrabold text-[#700B1A] flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-[#700B1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Winning Condition:</span>
                                </div>
                                <p id="strategyWinningCondition" class="text-xs text-gray-800 leading-snug font-medium"></p>
                                <p id="allyWinConditionText" class="hidden"></p>
                                <div id="allyGamePlanBox" class="text-[9px] text-gray-500 pt-1 border-t border-gray-200/70 leading-tight">
                                    <strong>Rencana Makro:</strong> <span id="allyGamePlanText">Kuasai lane dasar dan amankan objektif sungai.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DAMAGE DISTRIBUTION COMPARISON WIDGET (TIM SEKUTU) -->
                    <div class="pt-2.5 border-t border-[#FAF0F1] space-y-1.5">
                        <div class="flex items-center justify-between text-[10px] font-black uppercase">
                            <span class="text-amber-700 flex items-center gap-1">
                                <span>PHYSICAL:</span> <strong id="allyPhysPct">0%</strong>
                            </span>
                            <span class="text-purple-700 flex items-center gap-1">
                                <span>MAGIC:</span> <strong id="allyMagPct">0%</strong>
                            </span>
                        </div>
                        <div class="w-full h-1.5 rounded-full overflow-hidden flex bg-gray-100 border border-[#F3E8E8] transition-all" id="allyDamageBarContainer">
                            <div id="allyPhysBar" class="bg-amber-500 h-full transition-all duration-300" style="width: 0%;"></div>
                            <div id="allyMagBar" class="bg-purple-600 h-full transition-all duration-300" style="width: 0%;"></div>
                        </div>
                    </div>

                    <!-- POWER SPIKE 3-SEGMENT BAR & INTERPRETASI (TIM SEKUTU) -->
                    <div class="pt-2 border-t border-dashed border-[#FAF0F1] space-y-1.5" id="allyPowerSpikeSection">
                        <div class="flex items-center justify-between text-[9px] font-extrabold text-gray-600">
                            <span class="text-gray-400 uppercase font-black tracking-wider" title="Fase Power Spike: Early (0–5m), Mid (5–12m), Late (12+m / Trinity Marksman)">Power Spike (MPL S18):</span>
                            <div id="allyPowerSpikeCounts" class="text-[9px] font-semibold text-gray-500 hidden"></div>
                        </div>
                        <div id="allyPowerSpikeContent">
                            <div class="text-[9.5px] text-gray-400 italic py-0.5">Belum ada data</div>
                        </div>
                    </div>

                    <!-- PANEL ALERT KOMPOSISI RULE-BASED (TIM SEKUTU) -->
                    <div class="pt-2 border-t border-[#FAF0F1] space-y-1.5">
                        <div class="flex items-center justify-between text-[9px] font-extrabold text-gray-600">
                            <span class="text-gray-400 uppercase font-black tracking-wider">Alert Komposisi Tim:</span>
                        </div>
                        <div id="allyCompositionAlerts" class="space-y-1.5">
                            <div class="text-[9.5px] font-medium text-gray-400 italic py-0.5">
                                Status: Belum ada hero terpilih.
                            </div>
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

                    <!-- IDENTITAS DRAFT & CARA LAWAN MUSUH -->
                    <div class="pt-2.5 border-t border-[#FAF0F1] space-y-2" id="enemyIdentitySection">
                        <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-wider">
                            <span class="text-gray-400">Gaya Main Lawan:</span>
                            <span id="enemyIdentityBadge" class="bg-gray-100 text-gray-700 border border-gray-200 text-[9px] font-black px-2 py-0.5 rounded-md flex items-center gap-1">
                                <span id="enemyIdentityBadgeText">Draft Belum Terdeteksi</span>
                            </span>
                        </div>
                        <div class="bg-red-50/60 border border-red-100 rounded-xl p-2.5 space-y-1 text-[10px]">
                            <div class="font-extrabold text-[#DC2626] flex items-center gap-1 text-[10.5px]">
                                <span>Cara Menghadapi / Counter Lawan:</span>
                            </div>
                            <p id="enemyCounterAdviceText" class="text-gray-700 leading-snug font-medium">
                                Pilih hero musuh untuk menganalisis titik lemah dan strategi mengalahkannya.
                            </p>
                            <div id="enemyWeaknessBox" class="text-[9px] text-red-800/80 pt-1 border-t border-red-200/70 leading-tight">
                                <strong>Titik Lemah Musuh:</strong> <span id="enemyWeaknessText">Menunggu pemilihan hero lawan.</span>
                            </div>
                        </div>
                    </div>

                    <!-- DAMAGE DISTRIBUTION COMPARISON WIDGET (TIM MUSUH) -->
                    <div class="pt-2.5 border-t border-[#FAF0F1] space-y-1.5">
                        <div class="flex items-center justify-between text-[10px] font-black uppercase">
                            <span class="text-amber-700 flex items-center gap-1">
                                <span>PHYSICAL:</span> <strong id="enemyPhysPct">0%</strong>
                            </span>
                            <span class="text-purple-700 flex items-center gap-1">
                                <span>MAGIC:</span> <strong id="enemyMagPct">0%</strong>
                            </span>
                        </div>
                        <div class="w-full h-1.5 rounded-full overflow-hidden flex bg-gray-100 border border-[#F3E8E8] transition-all" id="enemyDamageBarContainer">
                            <div id="enemyPhysBar" class="bg-amber-500 h-full transition-all duration-300" style="width: 0%;"></div>
                            <div id="enemyMagBar" class="bg-purple-600 h-full transition-all duration-300" style="width: 0%;"></div>
                        </div>
                    </div>

                    <!-- POWER SPIKE 3-SEGMENT BAR & INTERPRETASI (TIM MUSUH) -->
                    <div class="pt-2 border-t border-dashed border-[#FAF0F1] space-y-1.5" id="enemyPowerSpikeSection">
                        <div class="flex items-center justify-between text-[9px] font-extrabold text-gray-600">
                            <span class="text-gray-400 uppercase font-black tracking-wider" title="Fase Power Spike: Early (0–5m), Mid (5–12m), Late (12+m / Trinity Marksman)">Power Spike (MPL S18):</span>
                            <div id="enemyPowerSpikeCounts" class="text-[9px] font-semibold text-gray-500 hidden"></div>
                        </div>
                        <div id="enemyPowerSpikeContent">
                            <div class="text-[9.5px] text-gray-400 italic py-0.5">Belum ada data</div>
                        </div>
                    </div>

                    <!-- PANEL ALERT KOMPOSISI RULE-BASED (TIM MUSUH) -->
                    <div class="pt-2 border-t border-[#FAF0F1] space-y-1.5">
                        <div class="flex items-center justify-between text-[9px] font-extrabold text-gray-600">
                            <span class="text-gray-400 uppercase font-black tracking-wider">Alert Komposisi Lawan:</span>
                        </div>
                        <div id="enemyCompositionAlerts" class="space-y-1.5">
                            <div class="text-[9.5px] font-medium text-gray-400 italic py-0.5">
                                Status: Belum ada hero terpilih.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- PERBANDINGAN POWER SPIKE & TEMPO ANTAR TIM -->
            <div id="powerSpikeComparisonBanner" class="card-custom p-3.5 bg-gradient-to-r from-[#FAF8F8] via-[#FCECEE]/40 to-[#FAF8F8] border border-[#F3E8E8] rounded-xl flex items-center justify-between gap-3 text-xs shadow-xs">
                <div class="flex items-center gap-2.5">
                    <span class="px-2 py-0.5 rounded-md bg-[#700B1A]/10 text-[#700B1A] flex items-center justify-center font-black text-[9px] shrink-0 tracking-wider">TEMPO</span>
                    <div>
                        <span class="text-[9.5px] font-black uppercase tracking-wider text-gray-400 block">Komparasi Kurva Power Spike & Tempo Pertandingan:</span>
                        <p id="powerSpikeComparisonSummary" class="text-xs font-extrabold text-[#18181B] leading-snug mt-0.5">
                            Menunggu pemilihan hero untuk membandingkan tempo dan kurva kekuatan kedua tim.
                        </p>
                    </div>
                </div>
                <div id="tempoAdvantageBadge" class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-gray-100 text-gray-600 border border-gray-200 uppercase whitespace-nowrap shrink-0">
                    Netral
                </div>
            </div>

            <!-- HERO SELECTION MATRIX & SEARCH (CARD) -->
            <div class="card-custom p-4 sm:p-5 space-y-3.5 scroll-mt-20" id="heroPickerCard">
                
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

                    <div id="pickerStatusHint" class="text-[11px] font-bold text-gray-500">
                        Target: <span class="text-[#700B1A]">Tim Sekutu (Slot 1)</span>
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
                           class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 pl-9 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] transition">
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
                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-7 gap-2 pt-1 max-h-[360px] overflow-y-auto pr-1" id="heroPickerGrid">
                    <!-- Populated dynamically via JS -->
                </div>

            </div>

        </div>

        <!-- ================= RIGHT COLUMN: ANALISIS TAKTIS (5 cols, STICKY) ================= -->
        <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-16 self-start">
            
            <div class="card-custom p-5 space-y-4">
                <!-- Tactical Header (Live status) -->
                <div class="flex items-center justify-between pb-3 border-b border-[#FAF0F1] gap-2 flex-wrap">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black text-[#18181B] tracking-tight">Rekomendasi Sistem Pakar</h3>
                            <button type="button" 
                                    onclick="openLegendModal()" 
                                    title="Panduan Istilah & Legenda Rekomendasi" 
                                    class="w-5 h-5 rounded-full bg-[#FAF0F1] hover:bg-[#FCECEE] text-[#700B1A] font-extrabold text-[11px] flex items-center justify-center border border-[#F8B4BD] transition cursor-pointer shadow-2xs font-serif italic">
                                i
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-500 font-medium mt-0.5" id="tacticalDraftStatus">
                            Draft kosong: Menampilkan rekomendasi meta dasar turnamen MPL ID S18
                        </p>
                    </div>

                    <!-- Header Actions: Jejak Inferensi & Engine RBR Status -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" 
                                onclick="openInferenceTraceModal()" 
                                title="Buka drawer jejak inferensi lengkap: Working Memory, Conflict Set, dan langkah kalkulasi skor" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#FAF0F1] hover:bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] rounded-xl text-xs font-black transition cursor-pointer shadow-2xs">
                            <span>Jejak Inferensi</span>
                        </button>
                        <div class="flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full shadow-2xs">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span class="text-[10px] font-bold text-emerald-800 tracking-wide">Engine RBR: Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Thin Loading State Bar (Forward Chaining Sync Indicator) -->
                <div id="recLoadingBar" class="h-0.5 w-full bg-[#700B1A] transition-all duration-300 opacity-0 rounded-full"></div>

                <!-- SAW FORMULA TRANSPARENCY BANNER -->
                <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl px-3 py-2 flex items-center justify-between gap-2 relative group/sawformula">
                    <div class="text-[10px] font-mono font-bold text-[#700B1A] leading-snug" id="sawActiveFormulaText">
                        Score = 0.40·DNA + 0.35·Comp + 0.25·Counter <span class="text-gray-500 font-sans font-semibold">(Bayesian Shrinkage m=5)</span>
                    </div>
                    <button type="button"
                            onclick="document.getElementById('sawFormulaPopover')?.classList.toggle('hidden')"
                            aria-label="Penjelasan Rumus SAW dan Bayesian Shrinkage"
                            class="w-4 h-4 rounded-full bg-[#FCECEE] hover:bg-[#700B1A] text-[#700B1A] hover:text-white border border-[#F8B4BD] text-[10px] font-serif italic font-black flex items-center justify-center flex-shrink-0 transition cursor-pointer">
                        i
                    </button>
                    <div id="sawFormulaPopover" class="hidden group-hover/sawformula:block absolute right-0 top-full mt-1.5 w-72 sm:w-80 bg-white border border-[#F8B4BD] rounded-2xl shadow-xl p-3 text-[10.5px] text-gray-600 z-40 space-y-1.5">
                        <div class="font-black text-[#700B1A] uppercase text-[9.5px] tracking-wider">Transparansi Rumus Hybrid SAW</div>
                        <p><strong>DNA (0.40):</strong> Preferensi pick tim disusutkan ke meta global MPL S18 (<code class="font-mono text-[#700B1A]">m = 5</code>).</p>
                        <p><strong>Comp (0.35):</strong> Penutupan gap L1 5 atribut Coach terhadap Target Profil pemenang <code class="font-mono text-[#700B1A]">T_k</code>.</p>
                        <p><strong>Counter (0.25):</strong> Bayesian win rate head-to-head melawan hero musuh terpilih.</p>
                        <p class="text-[9.5px] text-gray-500 pt-1 border-t border-gray-100">Jika Sekutu atau Musuh masih kosong, bobot komponen aktif dinormalisasi ulang agar total tetap 1.00.</p>
                    </div>
                </div>

                <!-- SECTION 1: REKOMENDASI PICK -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-1.5">
                            <span class="bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] text-[10px] font-black px-2.5 py-0.5 rounded-md uppercase tracking-wider">
                                Rekomendasi Pick (Top 10 SAW)
                            </span>
                            <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-500 text-[10px] font-bold px-1.5 py-0.5 rounded-md" id="picksCountBadge">
                                10
                            </span>
                        </div>

                        <!-- Mini Lane Filter Pills for Rekomendasi Pick -->
                        <div class="flex items-center gap-1 text-[10px]">
                            <button type="button" onclick="setRecLaneFilter('all', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-bold bg-[#700B1A] text-white">All</button>
                            <button type="button" onclick="setRecLaneFilter('roam', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]">Roam</button>
                            <button type="button" onclick="setRecLaneFilter('jungle', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]">Jgl</button>
                            <button type="button" onclick="setRecLaneFilter('mid', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]">Mid</button>
                            <button type="button" onclick="setRecLaneFilter('exp', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]">Exp</button>
                            <button type="button" onclick="setRecLaneFilter('gold', this)" class="rec-lane-btn px-2 py-0.5 rounded-full font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE]">Gold</button>
                        </div>
                    </div>

                    <!-- Info Lane yang Dibutuhkan (Lapisan 1 Hard Constraint Filter) -->
                    <div id="neededLanesInfoBanner" class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl px-3 py-1.5 flex items-center justify-between gap-2 text-[10.5px]">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-gray-500 font-extrabold text-[9.5px] uppercase tracking-wider flex items-center gap-1">
                                <svg class="w-3 h-3 text-[#700B1A]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                                </svg>
                                <span>Lane Dibutuhkan:</span>
                            </span>
                            <div id="neededLanesBadgeList" class="flex items-center gap-1 flex-wrap">
                                <span class="bg-[#FAF0F1] text-[#700B1A] border border-[#F8B4BD] px-1.5 py-0.5 rounded text-[9px] font-black">EXP Lane</span>
                                <span class="bg-[#FAF0F1] text-[#700B1A] border border-[#F8B4BD] px-1.5 py-0.5 rounded text-[9px] font-black">Jungle</span>
                                <span class="bg-[#FAF0F1] text-[#700B1A] border border-[#F8B4BD] px-1.5 py-0.5 rounded text-[9px] font-black">Mid Lane</span>
                                <span class="bg-[#FAF0F1] text-[#700B1A] border border-[#F8B4BD] px-1.5 py-0.5 rounded text-[9px] font-black">Gold Lane</span>
                                <span class="bg-[#FAF0F1] text-[#700B1A] border border-[#F8B4BD] px-1.5 py-0.5 rounded text-[9px] font-black">Roaming</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recommended Picks Cards List (Compact Cards & Accordion Expand) -->
                    <div class="space-y-2 max-h-[460px] overflow-y-auto pr-1" id="recommendedPicksList">
                        <!-- Populated dynamically via JS with compact cards and accordion expand -->
                    </div>
                </div>

                <!-- SECTION 2: REKOMENDASI BAN -->
                <div class="space-y-3 pt-3 border-t border-[#FAF0F1]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="bg-[#FEE2E2] text-[#991B1B] border border-[#FECACA] text-[10px] font-black px-2.5 py-0.5 rounded-md uppercase tracking-wider">
                                Rekomendasi Ban
                            </span>
                            <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-500 text-[10px] font-bold px-1.5 py-0.5 rounded-md" id="bansCountBadge">
                                8
                            </span>
                        </div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                            Prioritas Ban Musuh
                        </span>
                    </div>

                    <!-- Recommended Bans Cards List -->
                    <div class="space-y-2.5 max-h-[280px] overflow-y-auto pr-1" id="recommendedBansList">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>

                <!-- FOOTER PANEL BASIS DATA EMPIRIS NYATA (FASE 3 - PERSYARATAN 4) -->
                <div class="pt-3 border-t border-[#FAF0F1] flex items-center justify-between text-[10px] text-gray-500 font-medium">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Basis data: <strong>MPL ID S18</strong> • <strong>{{ $datasetTotalGames ?? 215 }} game</strong> • Diperbarui <strong>{{ $datasetLastUpdated ?? '13 November 2026' }}</strong></span>
                    </div>
                    <span class="text-gray-400 font-mono text-[9px]">Patch 1.9.14</span>
                </div>

            </div>

            <!-- ================= SECTION: REFERENSI MATCH MIRIP (MPL ID S18) ================= -->
            <div class="card-custom p-4 space-y-3 bg-white border border-[#F3E8E8] rounded-2xl shadow-sm" id="similarMatchesSectionCard">
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#700B1A]"></span>
                        <h4 class="text-xs font-black uppercase tracking-wider text-[#18181B] flex items-center gap-1.5 flex-wrap">
                            <span>Referensi Match Mirip (MPL ID S18)</span>
                            <span class="sr-only">PERTANDINGAN MIRIP TURNAMEN</span>
                        </h4>
                    </div>
                    <span id="similarMatchesCountBadge" class="text-[9.5px] bg-[#FAF8F8] border border-[#F3E8E8] text-[#700B1A] px-2 py-0.5 rounded-full font-bold">
                        0 Pertandingan
                    </span>
                </div>
                <p class="text-[11px] text-gray-500 font-medium">
                    Pertandingan turnamen MPL ID S18 yang paling relevan dengan komposisi draft saat ini:
                </p>

                <!-- Container Mini-Cards Match Mirip -->
                <div id="similarMatchesContainer" class="space-y-2.5">
                    <!-- Populated dynamically via JS renderSimilarMatches() -->
                </div>
            </div>

            <!-- SECTION 3: EXPLAINABLE AI (XAI) & RULE TRACE AUDIT LOG -->
            <div class="card-custom p-4 bg-[#FAF8F8]/90 border border-[#F3E8E8] rounded-2xl space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#700B1A]"></span>
                        <h4 class="text-xs font-black uppercase tracking-wider text-[#18181B]">
                            EXPLAINABLE AI (XAI) • AUDIT LOG PENALARAN RBR
                        </h4>
                    </div>
                    <a href="{{ route('rules') }}" target="_blank" class="text-[10px] font-black text-[#700B1A] hover:underline flex items-center gap-1">
                        <span>Rule Inspector Dosen &gt;</span>
                    </a>
                </div>
                <p class="text-[11px] text-gray-500 font-medium">
                    Jejak logis inferensi forward-chaining berbasis basis aturan turnamen MPL ID S18 untuk verifikasi sidang skripsi:
                </p>
                <div id="ruleTraceLogs" class="space-y-2 max-h-[260px] overflow-y-auto pr-1">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

        </div>

    </div>

</div>

<!-- ================= MODAL LEGENDA & PANDUAN ISTILAH RBR ================= -->
<div id="legendModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 opacity-0 pointer-events-none transition-all duration-300">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden border border-[#F3E8E8]">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-[#FAF8F8] border-b border-[#F3E8E8] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-[#700B1A]"></span>
                <div>
                    <h3 class="font-black text-sm text-[#18181B] uppercase tracking-wider">Panduan Istilah & Legenda Rekomendasi</h3>
                    <p class="text-[11px] text-gray-500 font-medium">Definisi metrik dan bobot penalaran sistem pakar MetaScout (MPL ID S18)</p>
                </div>
            </div>
            <button type="button" onclick="closeLegendModal()" class="w-8 h-8 rounded-full bg-white hover:bg-gray-100 border border-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-6 space-y-4 overflow-y-auto text-xs text-gray-700 leading-relaxed">
            
            <!-- 1. Prioritas P1, P2, P3 -->
            <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-4 space-y-2.5">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                    <h4 class="font-black text-xs text-[#700B1A] uppercase tracking-wider">Tingkat Prioritas Rekomendasi (P1 / P2 / P3)</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 pt-1">
                    <div class="bg-white p-3 rounded-xl border border-gray-100 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-[#700B1A] text-white">P1</span>
                            <span class="font-extrabold text-[#18181B] text-[11px]">Prioritas Utama</span>
                        </div>
                        <p class="text-[10px] text-gray-500 leading-snug">Skor &ge; 4.0. Direct counter lawan, sinergi kombo utama, atau pemenuhan aturan mutlak komposisi tim (misal: Mage Wajib).</p>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-gray-100 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-[#991B1B] text-white">P2</span>
                            <span class="font-extrabold text-[#18181B] text-[11px]">Prioritas Menengah</span>
                        </div>
                        <p class="text-[10px] text-gray-500 leading-snug">Skor 2.2 – 3.9. Sinergi sekunder yang solid, kestabilan laning, atau opsi adaptif fleksibel.</p>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-gray-100 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD]">P3</span>
                            <span class="font-extrabold text-[#18181B] text-[11px]">Pilihan Fleksibel</span>
                        </div>
                        <p class="text-[10px] text-gray-500 leading-snug">Skor &lt; 2.2. Rekomendasi cadangan/situasional berbasis rata-rata win rate meta umum.</p>
                    </div>
                </div>
            </div>

            <!-- 2. Jenis Sinyal Taktis (THR, META, SYN, CTR) -->
            <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-4 space-y-2.5">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                    <h4 class="font-black text-xs text-[#700B1A] uppercase tracking-wider">Jenis Sinyal Rekomendasi (Badge Taktis)</h4>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                    <div class="bg-white p-3 rounded-xl border border-gray-100 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-red-100 text-red-800">THR (Threat)</span>
                            <span class="font-bold text-[#18181B] text-[11px]">Ancaman Lawan</span>
                        </div>
                        <p class="text-[10px] text-gray-500 leading-snug">Hero musuh yang terdeteksi sebagai ancaman terbesar bagi draft sekutu. Sangat disarankan untuk segera di-ban.</p>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-gray-100 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-gray-100 text-gray-800">META</span>
                            <span class="font-bold text-[#18181B] text-[11px]">Standar Turnamen</span>
                        </div>
                        <p class="text-[10px] text-gray-500 leading-snug">Hero dengan tingkat kontestasi tinggi dan performa stabil sepanjang turnamen profesional MPL ID Season 18.</p>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-gray-100 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-800">SYN (Synergy)</span>
                            <span class="font-bold text-[#18181B] text-[11px]">Sinergi Sekutu</span>
                        </div>
                        <p class="text-[10px] text-gray-500 leading-snug">Memiliki kombinasi skill komplementer (*wombo-combo* CC, proteksi, atau follow-up burst) dengan hero sekutu yang sudah dipilih.</p>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-gray-100 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-[#FCECEE] text-[#700B1A]">CTR (Counter)</span>
                            <span class="font-bold text-[#18181B] text-[11px]">Counter Musuh</span>
                        </div>
                        <p class="text-[10px] text-gray-500 leading-snug">Memiliki kit mekanik yang secara langsung melumpuhkan kemampuan inti atau mobilitas hero yang dipilih tim lawan.</p>
                    </div>
                </div>
            </div>

            <!-- 3. Arti Skor & Angka Persen Ban -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-4 space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                        <h4 class="font-black text-xs text-[#700B1A] uppercase tracking-wider">Arti Skor Rekomendasi (0.0 - 5.0)</h4>
                    </div>
                    <p class="text-[10px] text-gray-600 leading-relaxed">
                        Nilai kalkulasi sistem pakar RBR (*Rule-Based Reasoning*) yang merefleksikan bobot kecocokan draft saat ini. Menggabungkan aturan wajib komposisi tim, efektivitas counter pick, penguatan sinergi rekan, dan win rate empiris data MPL ID S18.
                    </p>
                </div>
                <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-4 space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                        <h4 class="font-black text-xs text-[#700B1A] uppercase tracking-wider">Arti Angka Persen Ban (% Ban Rate)</h4>
                    </div>
                    <p class="text-[10px] text-gray-600 leading-relaxed">
                        Persentase frekuensi suatu hero dilarang bermain (*banned*) oleh tim profesional selama seluruh ronde kompetisi MPL ID Season 18. Angka tinggi (misal &gt;70%) menandakan hero tersebut dianggap sangat berbahaya atau overpower dalam meta pro.
                    </p>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 bg-[#FAF8F8] border-t border-[#F3E8E8] flex justify-end">
            <button type="button" onclick="closeLegendModal()" class="px-4 py-1.5 bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-bold rounded-xl transition cursor-pointer">
                Tutup Panduan
            </button>
        </div>

    </div>
</div>

<!-- ================= MODAL JEJAK INFERENSI (FASE 3 - PERSYARATAN 3) ================= -->
<div id="inferenceTraceModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 opacity-0 pointer-events-none transition-all duration-300">
    <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-[#F3E8E8]">
        
        <!-- Header -->
        <div class="px-6 py-4 bg-[#FAF8F8] border-b border-[#F3E8E8] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-3.5 h-3.5 rounded-full bg-[#700B1A]"></span>
                <div>
                    <h3 class="font-black text-sm text-[#18181B] uppercase tracking-wider">Jejak Inferensi Forward Chaining & State Penalaran AI</h3>
                    <p class="text-[11px] text-gray-500 font-medium">Transparansi Sistem Pakar: Working Memory ➜ Conflict Set ➜ Rule Terpilih ➜ Kalkulasi Skor</p>
                </div>
            </div>
            <button type="button" onclick="closeInferenceTraceModal()" class="w-8 h-8 rounded-full bg-white hover:bg-gray-100 border border-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- 4-Stage Sequential Pipeline Tabs -->
        <div class="px-6 py-2.5 bg-[#FAF0F1]/60 border-b border-[#F8B4BD]/40 flex items-center gap-2 flex-wrap">
            <button type="button" id="traceTabBtn-wm" onclick="switchTraceTab('working_memory')" class="trace-tab-btn px-3 py-1 rounded-full text-xs font-black uppercase transition bg-[#700B1A] text-white shadow-2xs">
                1. Working Memory (State)
            </button>
            <button type="button" id="traceTabBtn-cs" onclick="switchTraceTab('conflict_set')" class="trace-tab-btn px-3 py-1 rounded-full text-xs font-bold uppercase transition bg-white text-gray-600 border border-[#F3E8E8] hover:bg-[#FAF8F8]">
                2. Conflict Set (Evaluasi Rule)
            </button>
            <button type="button" id="traceTabBtn-sr" onclick="switchTraceTab('selected_rules')" class="trace-tab-btn px-3 py-1 rounded-full text-xs font-bold uppercase transition bg-white text-gray-600 border border-[#F3E8E8] hover:bg-[#FAF8F8]">
                3. Rule Terpilih & Prioritas
            </button>
            <button type="button" id="traceTabBtn-st" onclick="switchTraceTab('score_steps')" class="trace-tab-btn px-3 py-1 rounded-full text-xs font-bold uppercase transition bg-white text-gray-600 border border-[#F3E8E8] hover:bg-[#FAF8F8]">
                4. Langkah Kalkulasi Skor
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 overflow-y-auto flex-1 space-y-4 text-xs text-gray-700 leading-relaxed">
            
            <!-- STAGE 1: WORKING MEMORY -->
            <div id="traceContent-working_memory" class="space-y-4">
                <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-200/60 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                            <h4 class="font-black text-xs text-[#700B1A] uppercase tracking-wider">Working Memory: State Draft Saat Ini</h4>
                        </div>
                        <span class="text-[10px] text-gray-400 font-mono" id="wmStatusTimestamp">Siklus Forward Chaining Aktif</span>
                    </div>

                    <!-- Teams Overview Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Ally State -->
                        <div class="bg-white p-3 rounded-xl border border-[#F3E8E8] space-y-2">
                            <div class="flex justify-between items-center text-[11px] font-black">
                                <span class="text-[#700B1A] uppercase tracking-wider">Tim Sekutu</span>
                                <span id="wmAllyCountBadge" class="bg-[#FAF8F8] px-2 py-0.5 rounded-full border border-[#F3E8E8] text-[10px]">0/5 Terpilih</span>
                            </div>
                            <div id="wmAllyList" class="text-[11px] space-y-1 text-gray-600">
                                <em>Belum ada hero sekutu terpilih</em>
                            </div>
                            <div class="pt-2 border-t border-gray-100 flex justify-between text-[10px] font-bold">
                                <span>Rasio Damage:</span>
                                <span id="wmAllyDamageRatio">Phys 0% / Mag 0%</span>
                            </div>
                            <div class="flex justify-between text-[10px] text-gray-500">
                                <span>Distribusi Spike:</span>
                                <span id="wmAllySpikes" class="font-mono">Belum ada data</span>
                            </div>
                        </div>

                        <!-- Enemy State -->
                        <div class="bg-white p-3 rounded-xl border border-[#F3E8E8] space-y-2">
                            <div class="flex justify-between items-center text-[11px] font-black">
                                <span class="text-red-700 uppercase tracking-wider">Tim Musuh</span>
                                <span id="wmEnemyCountBadge" class="bg-[#FAF8F8] px-2 py-0.5 rounded-full border border-[#F3E8E8] text-[10px]">0/5 Terpilih</span>
                            </div>
                            <div id="wmEnemyList" class="text-[11px] space-y-1 text-gray-600">
                                <em>Belum ada hero musuh terpilih</em>
                            </div>
                            <div class="pt-2 border-t border-gray-100 flex justify-between text-[10px] font-bold">
                                <span>Rasio Damage:</span>
                                <span id="wmEnemyDamageRatio">Phys 0% / Mag 0%</span>
                            </div>
                            <div class="flex justify-between text-[10px] text-gray-500">
                                <span>Distribusi Spike:</span>
                                <span id="wmEnemySpikes" class="font-mono">Belum ada data</span>
                            </div>
                        </div>
                    </div>

                    <!-- Composition Facts & Needs -->
                    <div class="bg-white p-3.5 rounded-xl border border-[#F3E8E8] space-y-2">
                        <h5 class="font-black text-[11px] text-[#18181B] uppercase tracking-wider">Fakta Komposisi & Variabel Penalaran</h5>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-[10px]" id="wmFactsGrid">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- STAGE 2: CONFLICT SET (EVALUASI RULE) -->
            <div id="traceContent-conflict_set" class="hidden space-y-4">
                <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-200/60 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                            <h4 class="font-black text-xs text-[#700B1A] uppercase tracking-wider">Conflict Set: Evaluasi Seluruh Aturan Produksi RBR</h4>
                        </div>
                        <span class="text-[10px] text-gray-500 font-bold" id="conflictSetCountBadge">10 Rule Dievaluasi</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-normal">
                        Daftar seluruh aturan sistem pakar yang diproses oleh mesin inferensi forward chaining. Aturan dengan kondisi premis terpenuhi (Fired) dimasukkan ke Conflict Set untuk diperingkat.
                    </p>

                    <div class="overflow-x-auto rounded-xl border border-[#F3E8E8] bg-white">
                        <table class="w-full text-left text-[11px] border-collapse">
                            <thead>
                                <tr class="bg-[#FAF8F8] border-b border-[#F3E8E8] text-[9.5px] font-black uppercase text-gray-600">
                                    <th class="py-2.5 px-3">RULE ID & NAMA</th>
                                    <th class="py-2.5 px-3">KONDISI PREMIS (NILAI AKTUAL)</th>
                                    <th class="py-2.5 px-3 text-center">STATUS</th>
                                    <th class="py-2.5 px-3 text-right">BOBOT</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F3E8E8]" id="conflictSetTableBody">
                                <!-- Populated dynamically via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- STAGE 3: RULE TERPILIH & PRIORITAS -->
            <div id="traceContent-selected_rules" class="hidden space-y-4">
                <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-200/60 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                            <h4 class="font-black text-xs text-[#700B1A] uppercase tracking-wider">Rule Terpilih & Strategi Resolusi Konflik</h4>
                        </div>
                        <span class="text-[10px] text-emerald-700 font-bold font-mono">Expert Hierarchy Priority</span>
                    </div>

                    <div class="space-y-2.5" id="selectedRulesList">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>
            </div>

            <!-- STAGE 4: LANGKAH PERHITUNGAN SKOR -->
            <div id="traceContent-score_steps" class="hidden space-y-4">
                <div class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-200/60 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                            <h4 class="font-black text-xs text-[#700B1A] uppercase tracking-wider">Breakdown Skor Hybrid SAW + Bayesian Shrinkage (Formula Transparan)</h4>
                        </div>
                        <span class="text-[10px] text-[#700B1A] font-bold">Audit Formula Skripsi</span>
                    </div>

                    <div class="space-y-3">
                        <!-- Step 1 -->
                        <div class="bg-white p-3.5 rounded-xl border border-[#F3E8E8] space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-[11px] text-[#18181B] flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-[#700B1A] text-white flex items-center justify-center text-[10px] font-black">1</span>
                                    <span>S_dna: Bayesian Shrinkage Preferensi Draft DNA Tim</span>
                                </span>
                                <span class="text-[10px] font-mono text-gray-500">m = 5 (Pseudo-count)</span>
                            </div>
                            <div class="p-2 bg-[#FAF8F8] rounded-lg font-mono text-[10.5px] text-[#700B1A] font-bold text-center">
                                p̂_team(h) = (n_team(h) + 5 × p_global(h)) / (N_team + 5)
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="bg-white p-3.5 rounded-xl border border-[#F3E8E8] space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-[11px] text-[#18181B] flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-blue-700 text-white flex items-center justify-center text-[10px] font-black">2</span>
                                    <span>S_comp: Penutupan Gap 5 Atribut Coach vs Target T_k</span>
                                </span>
                                <span class="text-[10px] font-mono text-blue-800">5 Atribut (0–1)</span>
                            </div>
                            <div class="p-2 bg-blue-50 text-blue-900 rounded-lg font-mono text-[10.5px] font-bold text-center">
                                Δ(h) = D(C) − D(C + h), dengan D(C) = (1/5) × Σ|T_k − A_k(C)|
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="bg-white p-3.5 rounded-xl border border-[#F3E8E8] space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-[11px] text-[#18181B] flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-[#700B1A] text-white flex items-center justify-center text-[10px] font-black">3</span>
                                    <span>S_counter: Bayesian Shrinkage Win Rate vs Musuh</span>
                                </span>
                                <span class="text-[10px] font-mono text-[#700B1A]">Head-to-Head MPL S18</span>
                            </div>
                            <div class="p-2 bg-[#FAF8F8] text-[#700B1A] rounded-lg font-mono text-[10.5px] font-bold text-center">
                                ŵ(h|e) = (menang_vs_e + 5 × w_global(h)) / (game_vs_e + 5)
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="bg-white p-3.5 rounded-xl border border-[#F3E8E8] space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-[11px] text-[#18181B] flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-700 text-white flex items-center justify-center text-[10px] font-black">4</span>
                                    <span>Normalisasi Min-Max &amp; Skor Akhir SAW (Tanpa Confidence Statis)</span>
                                </span>
                                <span class="text-[10px] font-mono text-emerald-800">Final Aggregation</span>
                            </div>
                            <div class="p-2 bg-emerald-50 text-emerald-900 rounded-lg font-mono text-[10.5px] font-bold text-center">
                                Score(h) = (0.40 × S_dna_norm) + (0.35 × S_comp_norm) + (0.25 × S_counter_norm)
                            </div>
                            <p class="text-[9.5px] text-gray-500 italic pt-1">
                                Komponen RBR bertugas murni sebagai Lapisan Penjelasan Strategi berbasis angka rata-rata 5 atribut Coach vs Target T_k dan komposisi lawan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="px-6 py-3 bg-[#FAF8F8] border-t border-[#F3E8E8] flex items-center justify-between text-[11px] text-gray-500">
            <span>Verifikasi Akademik Sidang Skripsi • Forward Chaining Pipeline</span>
            <button type="button" onclick="closeInferenceTraceModal()" class="px-4 py-1.5 bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-bold rounded-xl transition cursor-pointer">
                Tutup Jejak Inferensi
            </button>
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
    const initialInferenceData = @json($initialInference ?? null);
    const allTeamsDna = @json($allTeamsDna ?? []);

    let selectedTeamName = ''; // Default kosong sebelum user memilih tim / Draft DNA
    const teamsDnaMap = {};
    if (Array.isArray(allTeamsDna)) {
        allTeamsDna.forEach(t => {
            if (t && t.team_name) {
                teamsDnaMap[t.team_name.toLowerCase()] = t;
            }
        });
    }

    let latestInferenceData = initialInferenceData;
    let currentRecommendations = (initialInferenceData && initialInferenceData.saw_recommendations) ? initialInferenceData.saw_recommendations : @json($defaultPicks ?? []);
    let expandedRecHeroId = null;
    let currentTraceTab = 'working_memory';

    // Draft state (Default kosong tanpa auto-pick hero)
    let allyTeam = []; // Slot sekutu kosong
    let enemyTeam = []; // Slot lawan kosong
    let allyLanes = []; // Lane spesifik
    let enemyLanes = []; // Lane spesifik
    let allyBans = []; // 5 Ban hero sekutu
    let enemyBans = []; // 5 Ban hero musuh
    let activeTeamMode = 'ally'; // 'ally', 'enemy', or 'ban'
    let activeTarget = { team: 'ally', index: 0, type: 'pick' }; // Target pengisian slot aktif

    let pickerLane = 'all';
    let recLaneFilter = 'all';

    // Inisialisasi awal
    document.addEventListener('DOMContentLoaded', () => {
        renderSlots();
        renderBanSlots();
        renderPickerGrid();
        updateDamageAndPowerSpikes();
        renderTacticalAnalysis();
        updateTeamDnaUI();
        updateDraftLockState();
        if (latestInferenceData?.similar_matches) {
            renderSimilarMatches(latestInferenceData.similar_matches);
        }
    });

    function updateDraftLockState() {
        const isUnlocked = Boolean(selectedTeamName);
        const lockWrapper = document.getElementById('draftSimulationLockWrapper');
        const lockBanner = document.getElementById('draftStepLockBanner');
        const selectorCard = document.getElementById('teamDnaSelectorContainer');
        const selectEl = document.getElementById('teamSelectDropdown');

        if (lockWrapper) {
            lockWrapper.classList.toggle('opacity-50', !isUnlocked);
            lockWrapper.classList.toggle('pointer-events-none', !isUnlocked);
            lockWrapper.classList.toggle('select-none', !isUnlocked);
            lockWrapper.setAttribute('aria-disabled', isUnlocked ? 'false' : 'true');
        }

        if (lockBanner) {
            lockBanner.classList.toggle('hidden', isUnlocked);
        }

        if (selectorCard) {
            if (isUnlocked) {
                selectorCard.classList.remove('border-2', 'border-[#700B1A]/35', 'ring-2', 'ring-[#700B1A]/10');
                selectorCard.classList.add('border', 'border-[#F3E8E8]');
            } else {
                selectorCard.classList.remove('border', 'border-[#F3E8E8]');
                selectorCard.classList.add('border-2', 'border-[#700B1A]/35', 'ring-2', 'ring-[#700B1A]/10');
            }
        }

        if (selectEl) {
            if (isUnlocked) {
                selectEl.classList.remove('border-2', 'border-[#700B1A]', 'ring-4', 'ring-[#700B1A]/15');
                selectEl.classList.add('border', 'border-[#F8B4BD]', 'hover:border-[#700B1A]');
            } else {
                selectEl.classList.remove('border', 'border-[#F8B4BD]', 'hover:border-[#700B1A]');
                selectEl.classList.add('border-2', 'border-[#700B1A]', 'ring-4', 'ring-[#700B1A]/15');
            }
        }
    }

    function ensureTeamSelected() {
        if (selectedTeamName) return true;
        showToast('Pilih Tim (Draft DNA) terlebih dahulu untuk memulai simulasi draft.');
        document.getElementById('teamSelectDropdown')?.focus();
        return false;
    }

    // Kontrol Pemilihan Tim / Draft DNA (Step 1)
    function selectTeamDna(teamName) {
        selectedTeamName = (teamName || '').trim();
        updateTeamDnaUI();
        updateDraftLockState();

        if (!selectedTeamName) {
            resetDraftStateToInitial();
            showToast('Draft DNA tim dikosongkan. Silakan pilih tim terlebih dahulu.');
            return;
        }

        syncServerDraft();
        showToast(`Draft DNA tim ${selectedTeamName} diaktifkan.`);
    }

    function updateTeamDnaUI() {
        const selectEl = document.getElementById('teamSelectDropdown');
        if (selectEl && selectEl.value !== selectedTeamName) {
            selectEl.value = selectedTeamName;
        }

        const logoImg = document.getElementById('teamSelectedLogoImg');
        const wrEl = document.getElementById('compactTeamWr');
        const recEl = document.getElementById('compactTeamRecord');
        const heroPoolContainer = document.getElementById('compactTopHeroPool');
        const bansContainer = document.getElementById('compactTopBans');
        const similarTeamBadge = document.getElementById('similarMatchesTeamBadge');

        if (!selectedTeamName) {
            if (logoImg) {
                logoImg.classList.add('hidden');
                logoImg.src = '/images/teams/default.png';
            }
            if (selectEl) {
                selectEl.classList.remove('pl-8');
                selectEl.classList.add('pl-3');
            }
            if (wrEl) wrEl.textContent = '-';
            if (recEl) recEl.textContent = '';
            if (heroPoolContainer) heroPoolContainer.innerHTML = '<span class="text-xs font-bold text-gray-400">-</span>';
            if (bansContainer) bansContainer.innerHTML = '<span class="text-xs font-bold text-gray-400">-</span>';
            if (similarTeamBadge) similarTeamBadge.textContent = 'MPL ID S18';
            return;
        }

        if (selectEl) {
            selectEl.classList.remove('pl-3');
            selectEl.classList.add('pl-8');
        }
        if (logoImg) logoImg.classList.remove('hidden');

        const dna = teamsDnaMap[selectedTeamName.toLowerCase()];
        if (!dna) {
            fetch(`/api/teams/${encodeURIComponent(selectedTeamName)}/dna`)
                .then(r => r.json())
                .then(res => {
                    if (res && res.data) {
                        teamsDnaMap[selectedTeamName.toLowerCase()] = res.data;
                        updateTeamDnaUI();
                    }
                })
                .catch(err => console.warn('Failed to fetch team DNA:', err));
            return;
        }

        // Update logo tim di dropdown
        if (logoImg && dna.team_logo) {
            logoImg.src = dna.team_logo;
        }

        // Update Win Rate & Record tim
        if (wrEl) wrEl.textContent = dna.win_rate_formatted || (dna.win_rate + '%');
        if (recEl) recEl.textContent = `(${dna.wins ?? 0}W - ${dna.losses ?? 0}L)`;

        // Update Top Hero Pool mini icons (4 hero pool signature)
        if (heroPoolContainer && Array.isArray(dna.top_heroes)) {
            const top4 = dna.top_heroes.slice(0, 4);
            heroPoolContainer.innerHTML = top4.length > 0 ? top4.map(h => `
                <div class="relative group flex items-center cursor-pointer" title="${h.hero} (${h.picks} Pick • WR ${h.win_rate_formatted || h.win_rate + '%'})">
                    <img src="${h.portrait}" alt="${h.hero}" class="w-6 h-6 rounded-lg object-cover border border-[#F3E8E8] bg-gray-100 shadow-2xs hover:border-[#700B1A] transition" onerror="this.src='/images/heroes/default.png'">
                </div>
            `).join('') : '<span class="text-xs font-bold text-gray-400">-</span>';
        }

        // Update Top Bans mini icons (3 hero ban signature)
        if (bansContainer && Array.isArray(dna.top_bans)) {
            const top3 = dna.top_bans.slice(0, 3);
            bansContainer.innerHTML = top3.length > 0 ? top3.map(b => `
                <div class="relative group flex items-center cursor-pointer" title="${b.hero} (${b.bans} Ban • ${b.ban_rate_formatted || b.ban_rate + '%'})">
                    <img src="${b.portrait}" alt="${b.hero}" class="w-6 h-6 rounded-lg object-cover border border-red-200 bg-gray-100 shadow-2xs hover:border-red-500 transition" onerror="this.src='/images/heroes/default.png'">
                </div>
            `).join('') : '<span class="text-xs font-bold text-gray-400">-</span>';
        }

        if (similarTeamBadge) similarTeamBadge.textContent = selectedTeamName;
    }

    function renderTeamDnaProfileCard(dna) {
        updateTeamDnaUI();
    }

    function resolveDefaultLane(heroObj) {
        if (!heroObj || !heroObj.lane) return 'EXP';
        const str = heroObj.lane.toLowerCase();
        if (str.includes('roam')) return 'ROAM';
        if (str.includes('mid')) return 'MID';
        if (str.includes('jungle')) return 'JGL';
        if (str.includes('gold')) return 'GOLD';
        if (str.includes('exp')) return 'EXP';
        return 'EXP';
    }

    function changeSlotLane(team, slotIdx, newLane) {
        if (!ensureTeamSelected()) return;
        if (team === 'ally') {
            allyLanes[slotIdx] = newLane;
        } else {
            enemyLanes[slotIdx] = newLane;
        }
        updateLaneStatus();
        updateDamageAndPowerSpikes();
        renderTacticalAnalysis();
        syncServerDraft();
    }

    // Pemilihan slot target secara langsung dengan visual highlight (FASE 5)
    function selectSlotTarget(team, index) {
        if (!ensureTeamSelected()) return;
        activeTarget = { team: team, index: index, type: 'pick' };
        activeTeamMode = team;
        updateTeamModeUI();
        renderSlots();

        const hintEl = document.getElementById('pickerStatusHint');
        if (hintEl) {
            hintEl.innerHTML = `Target: <span class="${team === 'ally' ? 'text-[#700B1A]' : 'text-red-700'} font-extrabold">${team === 'ally' ? 'Tim Sekutu' : 'Tim Musuh'} (Slot ${index + 1})</span>`;
        }

        const searchInput = document.getElementById('pickerSearchInput');
        if (searchInput) searchInput.focus();
        const pickerCard = document.getElementById('heroPickerCard');
        if (pickerCard) pickerCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function setActiveTeamMode(mode) {
        if (!ensureTeamSelected()) return;
        activeTeamMode = (mode === 'enemy') ? 'enemy' : 'ally';
        activeTarget = { team: activeTeamMode, index: Math.min(4, activeTeamMode === 'ally' ? allyTeam.length : enemyTeam.length), type: 'pick' };
        updateTeamModeUI();
        renderSlots();

        const hintEl = document.getElementById('pickerStatusHint');
        if (hintEl) {
            hintEl.innerHTML = `Target: <span class="${activeTeamMode === 'ally' ? 'text-[#700B1A]' : 'text-red-700'} font-extrabold">${activeTeamMode === 'ally' ? 'Tim Sekutu' : 'Tim Musuh'}</span>`;
        }
    }

    function updateTeamModeUI() {
        const allyBtn = document.getElementById('selectModeAllyBtn');
        const enemyBtn = document.getElementById('selectModeEnemyBtn');
        const allyCard = document.getElementById('allyTeamCard');
        const enemyCard = document.getElementById('enemyTeamCard');

        if (activeTeamMode === 'ally') {
            if (allyBtn) allyBtn.className = 'px-3.5 py-1 rounded-full text-xs font-extrabold uppercase transition bg-[#700B1A] text-white shadow-xs';
            if (enemyBtn) enemyBtn.className = 'px-3.5 py-1 rounded-full text-xs font-extrabold uppercase transition text-gray-600 hover:text-[#DC2626]';
            if (allyCard) allyCard.classList.add('border-2', 'border-[#700B1A]');
            if (enemyCard) enemyCard.classList.remove('border-2', 'border-[#DC2626]');
        } else {
            if (enemyBtn) enemyBtn.className = 'px-3.5 py-1 rounded-full text-xs font-extrabold uppercase transition bg-[#DC2626] text-white shadow-xs';
            if (allyBtn) allyBtn.className = 'px-3.5 py-1 rounded-full text-xs font-extrabold uppercase transition text-gray-600 hover:text-[#700B1A]';
            if (enemyCard) enemyCard.classList.add('border-2', 'border-[#DC2626]');
            if (allyCard) allyCard.classList.remove('border-2', 'border-[#700B1A]');
        }
    }

    function toggleSelectHero(heroName) {
        if (!ensureTeamSelected()) return;
        const heroObj = pickerHeroes.find(h => h.name.toLowerCase() === heroName.toLowerCase());
        const inAlly = allyTeam.indexOf(heroName);
        const inEnemy = enemyTeam.indexOf(heroName);

        if (activeTeamMode === 'ally') {
            if (inAlly >= 0) {
                allyTeam.splice(inAlly, 1);
                allyLanes.splice(inAlly, 1);
                activeTarget.index = Math.min(inAlly, allyTeam.length);
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
                allyLanes.push(resolveDefaultLane(heroObj));
                if (allyTeam.length < 5) {
                    activeTarget.index = allyTeam.length;
                } else {
                    activeTeamMode = 'enemy';
                    activeTarget = { team: 'enemy', index: Math.min(4, enemyTeam.length), type: 'pick' };
                    updateTeamModeUI();
                }
            }
        } else {
            if (inEnemy >= 0) {
                enemyTeam.splice(inEnemy, 1);
                enemyLanes.splice(inEnemy, 1);
                activeTarget.index = Math.min(inEnemy, enemyTeam.length);
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
                enemyLanes.push(resolveDefaultLane(heroObj));
                if (enemyTeam.length < 5) {
                    activeTarget.index = enemyTeam.length;
                } else {
                    if (allyTeam.length < 5) {
                        activeTeamMode = 'ally';
                        activeTarget = { team: 'ally', index: allyTeam.length, type: 'pick' };
                        updateTeamModeUI();
                    }
                }
            }
        }

        renderSlots();
        renderPickerGrid();
        updateDamageAndPowerSpikes();
        renderTacticalAnalysis();
        syncServerDraft();
    }

    function removeAllyHero(index) {
        if (index >= 0 && index < allyTeam.length) {
            allyTeam.splice(index, 1);
            allyLanes.splice(index, 1);
            activeTarget = { team: 'ally', index: index, type: 'pick' };
            activeTeamMode = 'ally';
            updateTeamModeUI();
            renderSlots();
            renderPickerGrid();
            updateDamageAndPowerSpikes();
            renderTacticalAnalysis();
            syncServerDraft();
        }
    }

    function removeEnemyHero(index) {
        if (index >= 0 && index < enemyTeam.length) {
            enemyTeam.splice(index, 1);
            enemyLanes.splice(index, 1);
            activeTarget = { team: 'enemy', index: index, type: 'pick' };
            activeTeamMode = 'enemy';
            updateTeamModeUI();
            renderSlots();
            renderPickerGrid();
            updateDamageAndPowerSpikes();
            renderTacticalAnalysis();
            syncServerDraft();
        }
    }

    function removeAllyBan(index) {
        if (index >= 0 && index < allyBans.length) {
            allyBans.splice(index, 1);
            renderBanSlots();
            renderPickerGrid();
            syncServerDraft();
        }
    }

    function removeEnemyBan(index) {
        if (index >= 0 && index < enemyBans.length) {
            enemyBans.splice(index, 1);
            renderBanSlots();
            renderPickerGrid();
            syncServerDraft();
        }
    }

    // Render 5 Slot Ban MPL per tim (FASE 5)
    function renderBanSlots() {}

    function computeLocalCoachAverages(teamList) {
        if (!teamList || teamList.length === 0) return null;
        const attrs = ['sustainability', 'attack_power', 'crowd_control', 'mobility', 'lane_control'];
        const sums = { sustainability: 0, attack_power: 0, crowd_control: 0, mobility: 0, lane_control: 0 };
        teamList.forEach(name => {
            const h = pickerHeroes.find(item => item.name.toLowerCase() === (name || '').toLowerCase());
            const ca = h?.coach_attrs || {};
            attrs.forEach(a => {
                sums[a] += Number(ca[a] ?? 5);
            });
        });
        const avg = {};
        attrs.forEach(a => {
            avg[a] = Number((sums[a] / teamList.length).toFixed(1));
        });
        return avg;
    }

    function evaluateLocalQuantitativeExplanation(allyList, enemyList) {
        if (!allyList || allyList.length === 0) return null;
        const labels = {
            sustainability: 'Sustainability',
            attack_power: 'Attack Power',
            crowd_control: 'Crowd Control',
            mobility: 'Mobility',
            lane_control: 'Lane Control'
        };
        const allyAvg = computeLocalCoachAverages(allyList);
        const enemyAvg = computeLocalCoachAverages(enemyList);
        const targetRaw = latestInferenceData?.saw_meta?.target_profile || { sustainability: 0.5, attack_power: 0.5, crowd_control: 0.5, mobility: 0.5, lane_control: 0.5 };
        const targetAvg = {};
        Object.keys(labels).forEach(k => {
            targetAvg[k] = Number(((Number(targetRaw[k] ?? 0.5)) * 10).toFixed(1));
        });

        const warnings = [];
        Object.keys(labels).forEach((k, idx) => {
            const diff = Number((targetAvg[k] - allyAvg[k]).toFixed(1));
            if (diff > 1.5) {
                warnings.push({
                    rule_id: `RULE-00${idx + 1}`,
                    attribute: k,
                    label: labels[k],
                    ally_avg: allyAvg[k],
                    target_avg: targetAvg[k],
                    diff: diff,
                    severity: diff >= 2.5 ? 'critical' : 'warning',
                    message: `${labels[k]} tim masih kurang (rata-rata ${allyAvg[k].toFixed(1)} vs target pemenang ${targetAvg[k].toFixed(1)})`
                });
            }
        });
        warnings.sort((a, b) => b.diff - a.diff);

        const sorted = Object.entries(allyAvg).sort((a, b) => b[1] - a[1]);
        const top2 = sorted.slice(0, 2);
        const pairKey = [top2[0][0], top2[1][0]].sort().join('+');
        const map = {
            'crowd_control+sustainability': { identity: 'Teamfight / Front-to-Back', note: 'Fokus war 5v5 di area objektif Turtle/Lord dengan inisiasi Crowd Control dan ketahanan frontline.' },
            'attack_power+mobility': { identity: 'Pick-off / Skirmish', note: 'Rotasi cepat antarlane untuk menculik core lawan sebelum teamfight terbuka.' },
            'lane_control+sustainability': { identity: 'Macro / Split Push', note: 'Tarik-ulur lane serta unggul kontrol map dan tekanan objektif turret.' },
            'attack_power+crowd_control': { identity: 'Burst Combo / Wombo', note: 'Kunci target menggunakan Crowd Control lalu eksekusi burst damage serempak.' },
            'attack_power+lane_control': { identity: 'Siege & Lane Pressure', note: 'Tekan lane lewat clear wave cepat dan damage tinggi untuk meruntuhkan turret.' },
            'crowd_control+mobility': { identity: 'Roam Catch & Engage', note: 'Manfaatkan mobilitas tinggi untuk membuka inisiasi mendadak dan menangkap lawan out-of-position.' },
            'lane_control+mobility': { identity: 'Fast Rotation & Map Control', note: 'Dominasi rotasi sungai dan gelombang minion untuk mengendalikan tempo objektif.' },
            'attack_power+sustainability': { identity: 'Bruiser / Sustained Brawl', note: 'Menangkan pertarungan jarak dekat berkepanjangan dengan kombinasi damage dan ketahanan.' },
            'crowd_control+lane_control': { identity: 'Zone Control & Objective Trap', note: 'Kunci area jalur minion dan lorong pit Turtle/Lord dengan kontrol zona berlapis.' },
            'mobility+sustainability': { identity: 'Tempo Disengage & Sustain', note: 'Pancing cooldown lawan lalu reset posisi dengan mobilitas dan ketahanan tinggi.' }
        };
        const matched = map[pairKey] || { identity: 'Balanced Adaptive', note: 'Komposisi seimbang dengan fleksibilitas strategi antar fase permainan.' };

        let winningCondition = `${matched.note} (Atribut tertinggi: ${labels[top2[0][0]]} ${top2[0][1].toFixed(1)} & ${labels[top2[1][0]]} ${top2[1][1].toFixed(1)}).`;
        if (enemyAvg) {
            const teamfightDiff = (allyAvg.crowd_control + allyAvg.sustainability) - (enemyAvg.crowd_control + enemyAvg.sustainability);
            const laneDiff = allyAvg.lane_control - enemyAvg.lane_control;
            const pickoffDiff = (allyAvg.mobility + allyAvg.attack_power) - (enemyAvg.mobility + enemyAvg.attack_power);
            if (teamfightDiff >= laneDiff && teamfightDiff >= pickoffDiff && teamfightDiff > 0.3) {
                winningCondition = `Paksa teamfight 5v5 di area pit objektif — unggul Crowd Control (${allyAvg.crowd_control.toFixed(1)} vs ${enemyAvg.crowd_control.toFixed(1)}) & Sustainability (${allyAvg.sustainability.toFixed(1)} vs ${enemyAvg.sustainability.toFixed(1)}).`;
            } else if (laneDiff >= pickoffDiff && laneDiff > 0.3) {
                winningCondition = `Hindari war langsung, manfaatkan keunggulan Lane Control (${allyAvg.lane_control.toFixed(1)} vs ${enemyAvg.lane_control.toFixed(1)}) untuk clear wave & split push.`;
            } else if (pickoffDiff > 0.3) {
                winningCondition = `Ciptakan skirmish & pick-off sebelum war terbuka — unggul Mobility (${allyAvg.mobility.toFixed(1)} vs ${enemyAvg.mobility.toFixed(1)}) & Attack Power (${allyAvg.attack_power.toFixed(1)} vs ${enemyAvg.attack_power.toFixed(1)}).`;
            }
        }

        return {
            has_ally: true,
            draft_identity: matched.identity,
            playstyle: matched.identity,
            playstyle_note: matched.note,
            winning_condition: winningCondition,
            top_two_attributes: [
                { attribute: top2[0][0], label: labels[top2[0][0]], value: top2[0][1] },
                { attribute: top2[1][0], label: labels[top2[1][0]], value: top2[1][1] }
            ],
            ally_averages: allyAvg,
            enemy_averages: enemyAvg,
            target_averages: targetAvg,
            attribute_warnings: warnings
        };
    }

    function renderDraftIdentities() {
        const identData = latestInferenceData?.draft_identity;
        let allyIdent = identData?.ally;
        let enemyIdent = identData?.enemy;

        if (!allyIdent || allyTeam.length === 0) allyIdent = evaluateLocalDraftIdentity(allyTeam, 'ally');
        if (!enemyIdent || enemyTeam.length === 0) enemyIdent = evaluateLocalDraftIdentity(enemyTeam, 'enemy');

        const aBadge = document.getElementById('allyIdentityBadge');
        const aBadgeText = document.getElementById('allyIdentityBadgeText');
        const aPowerSpikeBadge = document.getElementById('allyPowerSpikeBadge');
        const aPlaystyleBadge = document.getElementById('allyPlaystyleBadge');
        const aWinText = document.getElementById('allyWinConditionText');
        const aPlanText = document.getElementById('allyGamePlanText');

        // Elemen tampilan RBR Penjelasan Strategi Berbasis Angka Nyata
        const stratDraftIdent = document.getElementById('strategyDraftIdentity');
        const stratPowerSpike = document.getElementById('strategyPowerSpike');
        const stratPlaystyle = document.getElementById('strategyPlaystyle');
        const stratWinningCond = document.getElementById('strategyWinningCondition');

        const emptyStateEl = document.getElementById('allyStrategyEmptyState');
        const resultContentEl = document.getElementById('allyStrategyResultContent');

        if (allyTeam.length === 0) {
            if (emptyStateEl) emptyStateEl.classList.remove('hidden');
            if (resultContentEl) resultContentEl.classList.add('hidden');
            if (stratDraftIdent) stratDraftIdent.textContent = '-';
            if (stratPowerSpike) stratPowerSpike.textContent = '-';
            if (stratPlaystyle) stratPlaystyle.textContent = '-';
            if (stratWinningCond) stratWinningCond.textContent = '';
            if (aBadgeText) aBadgeText.textContent = 'Draft Belum Terbentuk';
            if (aPowerSpikeBadge) aPowerSpikeBadge.textContent = '-';
            if (aPlaystyleBadge) aPlaystyleBadge.textContent = '-';
            if (aWinText) aWinText.textContent = '';
        } else {
            if (emptyStateEl) emptyStateEl.classList.add('hidden');
            if (resultContentEl) resultContentEl.classList.remove('hidden');

            const serverExp = latestInferenceData?.rbr_explanation;
            const rbrExp = (serverExp && serverExp.has_ally) ? serverExp : evaluateLocalQuantitativeExplanation(allyTeam, enemyTeam);
            const strat = latestInferenceData?.draft_strategy || identData?.strategy || identData;
            const allySpikes = computeTeamPowerSpikes(allyTeam);
            const spikeInterp = interpretTeamPowerSpike(allySpikes, allyTeam.length);

            const identityText = rbrExp?.draft_identity || strat?.draft_identity || allyIdent.archetype;
            const top2Text = (rbrExp?.top_two_attributes && rbrExp.top_two_attributes.length >= 2)
                ? `${rbrExp.top_two_attributes[0].label} (${Number(rbrExp.top_two_attributes[0].value).toFixed(1)}) & ${rbrExp.top_two_attributes[1].label} (${Number(rbrExp.top_two_attributes[1].value).toFixed(1)})`
                : (rbrExp?.playstyle || strat?.playstyle || '-');
            const spikeText = strat?.power_spike || spikeInterp.badge || '-';
            const winText = rbrExp?.winning_condition || strat?.winning_condition || allyIdent.win_condition;

            if (stratDraftIdent) stratDraftIdent.textContent = identityText;
            if (stratPowerSpike) stratPowerSpike.textContent = spikeText;
            if (stratPlaystyle) stratPlaystyle.textContent = top2Text;
            if (stratWinningCond) stratWinningCond.textContent = winText;

            if (aBadgeText) aBadgeText.textContent = identityText;
            if (aPowerSpikeBadge) aPowerSpikeBadge.textContent = spikeText;
            if (aPlaystyleBadge) aPlaystyleBadge.textContent = top2Text;
            if (aWinText) aWinText.textContent = winText;

            if (aPlanText && rbrExp?.ally_averages) {
                const av = rbrExp.ally_averages;
                aPlanText.textContent = `Rata-rata 5 Atribut: Sus ${Number(av.sustainability).toFixed(1)} • Atk ${Number(av.attack_power).toFixed(1)} • CC ${Number(av.crowd_control).toFixed(1)} • Mob ${Number(av.mobility).toFixed(1)} • Lane ${Number(av.lane_control).toFixed(1)}`;
            } else if (aPlanText) {
                aPlanText.textContent = allyIdent.game_plan;
            }
        }

        if (aBadge) {
            aBadge.className = `${allyIdent.badge_color || 'bg-gray-100 text-gray-700 border-gray-200'} border text-[9px] font-black px-2 py-0.5 rounded-md flex items-center gap-1`;
        }

        const eBadge = document.getElementById('enemyIdentityBadge');
        const eBadgeText = document.getElementById('enemyIdentityBadgeText');
        const eAdviceText = document.getElementById('enemyCounterAdviceText');
        const eWeaknessText = document.getElementById('enemyWeaknessText');

        if (eBadgeText) eBadgeText.textContent = enemyIdent.archetype;
        if (eBadge) {
            eBadge.className = `${enemyIdent.badge_color || 'bg-gray-100 text-gray-700 border-gray-200'} border text-[9px] font-black px-2 py-0.5 rounded-md flex items-center gap-1`;
        }
        if (eAdviceText) eAdviceText.textContent = enemyIdent.counter_advice;
        if (eWeaknessText) {
            eWeaknessText.textContent = (enemyIdent.key_weaknesses && enemyIdent.key_weaknesses.length > 0) ? 
                enemyIdent.key_weaknesses.join(' • ') : (enemyTeam.length > 0 ? 'Komposisi relatif seimbang' : 'Menunggu pemilihan hero lawan.');
        }
    }

    // Render Pertandingan Mirip Turnamen Pro (MPL ID S18)
    function renderSimilarMatches(matches) {
        const container = document.getElementById('similarMatchesContainer');
        const countBadge = document.getElementById('similarMatchesCountBadge') || document.getElementById('similarMatchesCountText');
        const teamBadge = document.getElementById('similarMatchesTeamBadge');
        if (!container) return;

        if (teamBadge) teamBadge.textContent = selectedTeamName || 'MPL ID S18';

        if (!matches || !Array.isArray(matches) || matches.length === 0) {
            const emptyMsg = (!selectedTeamName && allyTeam.length === 0)
                ? 'Pilih tim Draft DNA atau minimal 1 hero sekutu untuk melihat referensi pertandingan yang cocok di MPL ID S18.'
                : 'Belum ada pertandingan referensi yang cocok di database turnamen MPL ID S18.';
            container.innerHTML = `
                <div class="p-3 text-center text-xs text-gray-400 bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl font-medium">
                    ${emptyMsg}
                </div>
            `;
            if (countBadge) countBadge.textContent = '0 Pertandingan';
            return;
        }

        if (countBadge) countBadge.textContent = `${matches.length} Pertandingan`;

        const html = matches.map(m => {
            const isWin = (m.team_status === 'W');
            const statusBadgeClass = isWin 
                ? 'bg-emerald-100 text-emerald-800 border-emerald-200' 
                : 'bg-rose-100 text-rose-800 border-rose-200';
            const matchedBadgeClass = (m.matched_count > 0)
                ? 'bg-purple-100 text-purple-800 border-purple-200'
                : 'bg-gray-100 text-gray-700 border-gray-200';

            const teamPicksHtml = (m.team_picks_with_portraits || []).map(p => {
                const isMatch = (m.matching_heroes || []).includes((p.name || '').toLowerCase());
                const borderClass = isMatch ? 'ring-2 ring-[#700B1A] scale-105' : 'border border-gray-200';
                return `
                    <div class="relative group/pick flex flex-col items-center">
                        <img src="${p.portrait}" alt="${p.name}" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg object-cover ${borderClass}" onerror="this.src='/images/heroes/default.png'">
                        ${isMatch ? '<span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-[#700B1A]"></span>' : ''}
                        <span class="text-[8px] text-gray-600 truncate max-w-[38px] mt-0.5">${p.name}</span>
                    </div>
                `;
            }).join('');

            const enemyPicksHtml = (m.enemy_picks_with_portraits || []).map(p => {
                return `
                    <div class="relative group/pick flex flex-col items-center opacity-85">
                        <img src="${p.portrait}" alt="${p.name}" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg object-cover border border-gray-200" onerror="this.src='/images/heroes/default.png'">
                        <span class="text-[8px] text-gray-500 truncate max-w-[38px] mt-0.5">${p.name}</span>
                    </div>
                `;
            }).join('');

            return `
                <div class="p-3 bg-white border border-[#F3E8E8] hover:border-[#700B1A]/40 rounded-xl space-y-2 transition shadow-2xs">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[9px] bg-[#FAF8F8] border border-[#F3E8E8] text-gray-700 px-2 py-0.5 rounded-md font-bold">
                                ${m.stage || 'Regular Season'}
                            </span>
                            <span class="text-[9px] text-gray-400 font-mono">
                                Durasi: ${m.duration || '15:20'}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] px-2 py-0.5 rounded-full font-black border ${matchedBadgeClass}">
                                ${m.matched_label || (m.matched_count + ' Hero Sama')}
                            </span>
                            <span class="text-[9px] px-2 py-0.5 rounded-full font-black border ${statusBadgeClass}">
                                ${m.status_label || (isWin ? 'MENANG' : 'KALAH')}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-[#FAF8F8] p-2 rounded-lg gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <img src="${m.team_1_logo}" alt="${m.team_1_name}" class="w-5 h-5 object-contain flex-shrink-0" onerror="this.src='/images/teams/default.png'">
                            <span class="text-[11px] font-black text-gray-800 truncate">${m.team_1_name}</span>
                        </div>
                        <div class="px-2 py-0.5 bg-white border border-[#F3E8E8] rounded text-xs font-black text-[#700B1A] flex-shrink-0">
                            ${m.score}
                        </div>
                        <div class="flex items-center gap-2 min-w-0 justify-end">
                            <span class="text-[11px] font-black text-gray-800 truncate">${m.team_2_name}</span>
                            <img src="${m.team_2_logo}" alt="${m.team_2_name}" class="w-5 h-5 object-contain flex-shrink-0" onerror="this.src='/images/teams/default.png'">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1 border-t border-gray-100">
                        <div>
                            <div class="text-[9px] font-black text-gray-500 uppercase tracking-wider mb-1">
                                Picks ${selectedTeamName || 'Tim'}:
                            </div>
                            <div class="flex items-center gap-1 overflow-x-auto pb-0.5">
                                ${teamPicksHtml}
                            </div>
                        </div>
                        <div>
                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-wider mb-1">
                                Picks Lawan:
                            </div>
                            <div class="flex items-center gap-1 overflow-x-auto pb-0.5">
                                ${enemyPicksHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = html;
    }

    function evaluateLocalDraftIdentity(teamList, teamType) {
        if (!teamList || teamList.length === 0) {
            return {
                archetype: teamType === 'ally' ? 'Draft Belum Terbentuk' : 'Draft Lawan Belum Terdeteksi',
                badge_color: 'bg-gray-100 text-gray-700 border-gray-200',
                icon: '',
                win_condition: teamType === 'ally' ? 'Pilih hero sekutu untuk merumuskan win condition dan rencana permainan tim.' : 'Pilih hero musuh untuk menganalisis strategi mengalahkannya.',
                game_plan: 'Kuasai lane dasar dan amankan objektif sungai.',
                counter_advice: 'Pilih hero musuh untuk menganalisis titik lemah dan strategi mengalahkannya.',
                key_weaknesses: []
            };
        }

        const ccHeroes = ['tigreal', 'atlas', 'carmilla', 'akai', 'khufra', 'ruby', 'kaja', 'franco', 'chou', 'minotaur', 'lolita', 'gloo', 'fredrinn', 'arlott', 'belerick', 'gatotkaca'];
        const aoeHeroes = ['yve', 'pharsa', 'xavier', 'vexana', 'cecilion', 'lylia', 'claude', 'hanabi', 'odette', 'aurora', 'gord', 'luoyi'];
        const pickoffHeroes = ['saber', 'kaja', 'franco', 'chou', 'ling', 'fanny', 'hayabusa', 'helcurt', 'natalia', 'gusion', 'lancelot', 'suyou', 'harley', 'selena', 'nolan', 'yin', 'karina', 'aamon'];
        const sustainHeroes = ['estes', 'floryn', 'angela', 'rafaela', 'minotaur', 'carmilla', 'baxia', 'hylos', 'barats', 'uranus', 'fredrinn', 'ruby', 'yuzhong', 'terizla', 'esmeralda', 'hilda'];
        const pokeHeroes = ['pharsa', 'xavier', 'novaria', 'beatrix', 'brody', 'change', 'selena', 'valir', 'lesley', 'zhuxin'];
        const earlyHeroes = ['martis', 'hilda', 'grock', 'jawhead', 'fanny', 'selena', 'dyrroth', 'terizla', 'thamuz', 'freya', 'paquito'];
        const lateHeroes = ['claude', 'karrie', 'moskov', 'miya', 'layla', 'bruno', 'irithel', 'cecilion', 'aldous', 'natan', 'lesley'];

        let ccCount = 0, aoeCount = 0, pickoffCount = 0, sustainCount = 0, pokeCount = 0, earlyCount = 0, lateCount = 0;

        teamList.forEach(name => {
            const low = (name || '').toLowerCase().replace(/[^a-z0-9]/g, '');
            if (ccHeroes.includes(low)) ccCount++;
            if (aoeHeroes.includes(low)) aoeCount++;
            if (pickoffHeroes.includes(low)) pickoffCount++;
            if (sustainHeroes.includes(low)) sustainCount++;
            if (pokeHeroes.includes(low)) pokeCount++;
            if (earlyHeroes.includes(low)) earlyCount++;
            if (lateHeroes.includes(low)) lateCount++;
        });

        if (ccCount >= 2 && aoeCount >= 1) {
            return {
                archetype: 'AoE Teamfight (Wombo Combo)',
                badge_color: 'bg-purple-100 text-purple-900 border-purple-200',
                icon: '',
                win_condition: 'Dominasi pertarungan 5v5 di ruang terbuka pada kontes Turtle & Lord menit 10+.',
                game_plan: 'Buka inisiasi menggunakan CC area garis depan, lalu follow-up seluruh burst damage area serempak.',
                counter_advice: 'Hindari berkumpul di lorong sempit. Gunakan spell Purify atau hero anti-CC (Diggie) dan pancing inisiasi mereka.',
                key_weaknesses: ['Rentan split-push saat cooldown skill ultimate', 'Ketergantungan tinggi pada inisiasi tank']
            };
        } else if (pickoffCount >= 2 || (pickoffCount >= 1 && ccCount >= 1)) {
            return {
                archetype: 'Pick-Off & Single Target Ambush',
                badge_color: 'bg-rose-100 text-rose-900 border-rose-200',
                icon: '',
                win_condition: 'Eliminasi instan carry/mage musuh di semi-river jungle sebelum objektif dimulai.',
                game_plan: 'Kuasai vision semak-semak dan jalankan gank 2-3 pemain pada musuh yang overextend.',
                counter_advice: 'Jaga jarak formasi dari semak tanpa vision. Beli Antique Cuirass dan jangan rotasi sendirian tanpa roamer.',
                key_weaknesses: ['Lemah dalam 5v5 frontal jika target utama gagal di-burst instan']
            };
        } else if (sustainCount >= 2) {
            return {
                archetype: 'Sustain & Frontline Brawl',
                badge_color: 'bg-emerald-100 text-emerald-900 border-emerald-200',
                icon: '',
                win_condition: 'Paksa pertempuran berkepanjangan (extended fight) hingga sumber daya lawan terkuras.',
                game_plan: 'Bergerak rapat bersama healer/frontliner. Serap damage musuh sambil perlahan merebut area Lord.',
                counter_advice: 'Wajib beli item anti-heal (Sea Halberd / Glowing Wand / Dominance Ice). Hindari trade damage lama.',
                key_weaknesses: ['Rentan true damage (Karrie/Lesley) dan debuff anti-heal']
            };
        } else if (pokeCount >= 2) {
            return {
                archetype: 'Poke & High-Ground Siege',
                badge_color: 'bg-amber-100 text-amber-900 border-amber-200',
                icon: '',
                win_condition: 'Kikis HP musuh dari jarak aman sebelum objektif dan hancurkan base turret tanpa kontak dekat.',
                game_plan: 'Gunakan skill jarak jauh untuk membersihkan minion dan mencicil formasi musuh.',
                counter_advice: 'Gunakan hero assassin atau inisiator penyelam (dive-in) seperti Ling, Joy, atau Chou untuk menutup jarak.',
                key_weaknesses: ['Sangat rapuh bila didekati hero burst jarak dekat']
            };
        } else if (earlyCount >= 2) {
            return {
                archetype: 'Early Tempo & Jungle Snowball',
                badge_color: 'bg-orange-100 text-orange-900 border-orange-200',
                icon: '',
                win_condition: 'Amankan keunggulan net worth di fase laning menit 0-5 dan runtuhkan turret sebelum Marksman musuh menyelesaikan Trinity core item.',
                game_plan: 'Invasi buff jungle lawan sejak level 1-2, amankan seluruh turtle, dan hancurkan outer turret secepatnya.',
                counter_advice: 'Bermain bertahan di bawah turret pada 5 menit awal (manfaatkan shield turret). Hindari kontes berisiko dan stall menuju fase late game menit 12+.',
                key_weaknesses: ['Potensi power spike menurun jika pertandingan melewati menit 12 (fase Evolved Lord & Trinity MM)']
            };
        } else if (lateCount >= 2) {
            return {
                archetype: 'Late-Game Hypercarry Scaling',
                badge_color: 'bg-indigo-100 text-indigo-900 border-indigo-200',
                icon: '',
                win_condition: 'Lindungi carry hingga menyelesaikan build Trinity (Corrosion, DHS, Golden Staff) untuk membalikkan teamfight menit 12+.',
                game_plan: 'Farming aman, minimalkan kematian dini, dan hindari war yang tidak menguntungkan posisi.',
                counter_advice: 'Gank carry musuh secara terus menerus di early game (0–5m). Rebut turret gold lane dan akhiri game sebelum item Trinity selesai.',
                key_weaknesses: ['Early game sangat lemah dan bergantung pada ruang farming']
            };
        } else {
            return {
                archetype: 'Flex & Balanced Tournament Meta',
                badge_color: 'bg-blue-100 text-blue-900 border-blue-200',
                icon: '',
                win_condition: 'Eksekusi rotasi lane standar, pertahankan keseimbangan map, dan menangkan war berbasis posisi.',
                game_plan: 'Jalankan rotasi proaktif ke side lane, jaga formasi segitiga (tank di depan, dps di belakang).',
                counter_advice: 'Eksploitasi hero musuh yang terpisah dari formasi atau serang saat skill escape mereka cooldown.',
                key_weaknesses: ['Memerlukan koordinasi tim yang disiplin untuk fokus objektif']
            };
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
                const curLane = allyLanes[i] || resolveDefaultLane(heroObj);

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
                    <div class="flex items-center justify-center mt-0.5">
                        <select onchange="changeSlotLane('ally', ${i}, this.value)" 
                                title="Pilih Lane Spesifik (Flex Pick)"
                                class="bg-white border border-[#F3E8E8] hover:border-[#700B1A] text-[#700B1A] text-[7.5px] font-black rounded px-1.5 py-0.2 focus:outline-none cursor-pointer shadow-2xs">
                            <option value="ROAM" ${curLane === 'ROAM' ? 'selected' : ''}>ROAM</option>
                            <option value="JGL" ${curLane === 'JGL' ? 'selected' : ''}>JGL</option>
                            <option value="MID" ${curLane === 'MID' ? 'selected' : ''}>MID</option>
                            <option value="EXP" ${curLane === 'EXP' ? 'selected' : ''}>EXP</option>
                            <option value="GOLD" ${curLane === 'GOLD' ? 'selected' : ''}>GOLD</option>
                        </select>
                    </div>
                `;
            } else {
                const isActive = (activeTarget.team === 'ally' && activeTarget.index === i && activeTarget.type === 'pick');
                slotCol.innerHTML = `
                    <div onclick="selectSlotTarget('ally', ${i}, 'pick')" 
                         class="w-full aspect-square ${isActive ? 'bg-[#FCECEE] border-2 border-[#700B1A] ring-4 ring-[#700B1A]/30 scale-105 shadow-md' : 'bg-[#FAF8F8] border-2 border-dashed border-[#F3E8E8] hover:border-[#700B1A]'} rounded-full flex flex-col items-center justify-center text-gray-300 hover:text-[#700B1A] cursor-pointer transition">
                        ${isActive ? '<span class="text-[8.5px] font-black text-[#700B1A] animate-pulse">PILIH</span>' : '<span class="text-sm font-bold">+</span>'}
                    </div>
                    <span class="font-semibold text-[9px] ${isActive ? 'text-[#700B1A] font-black' : 'text-gray-400'} mt-1">Empty</span>
                    <span class="text-[8px] font-bold ${isActive ? 'text-[#700B1A]' : 'text-gray-300'} uppercase tracking-wider mt-0.5">SLOT ${i + 1}</span>
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
                const curLane = enemyLanes[i] || resolveDefaultLane(heroObj);

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
                    <div class="flex items-center justify-center mt-0.5">
                        <select onchange="changeSlotLane('enemy', ${i}, this.value)" 
                                title="Pilih Lane Spesifik (Flex Pick)"
                                class="bg-white border border-red-200 hover:border-[#DC2626] text-red-700 text-[7.5px] font-black rounded px-1.5 py-0.2 focus:outline-none cursor-pointer shadow-2xs">
                            <option value="ROAM" ${curLane === 'ROAM' ? 'selected' : ''}>ROAM</option>
                            <option value="JGL" ${curLane === 'JGL' ? 'selected' : ''}>JGL</option>
                            <option value="MID" ${curLane === 'MID' ? 'selected' : ''}>MID</option>
                            <option value="EXP" ${curLane === 'EXP' ? 'selected' : ''}>EXP</option>
                            <option value="GOLD" ${curLane === 'GOLD' ? 'selected' : ''}>GOLD</option>
                        </select>
                    </div>
                `;
            } else {
                const isActive = (activeTarget.team === 'enemy' && activeTarget.index === i && activeTarget.type === 'pick');
                slotCol.innerHTML = `
                    <div onclick="selectSlotTarget('enemy', ${i}, 'pick')" 
                         class="w-full aspect-square ${isActive ? 'bg-red-50 border-2 border-[#DC2626] ring-4 ring-[#DC2626]/30 scale-105 shadow-md' : 'bg-[#FAF8F8] border-2 border-dashed border-red-100 hover:border-[#DC2626]'} rounded-full flex flex-col items-center justify-center text-red-200 hover:text-[#DC2626] cursor-pointer transition">
                        ${isActive ? '<span class="text-[8.5px] font-black text-[#DC2626] animate-pulse">PILIH</span>' : '<span class="text-sm font-bold">+</span>'}
                    </div>
                    <span class="font-semibold text-[9px] ${isActive ? 'text-red-700 font-black' : 'text-gray-400'} mt-1">Empty</span>
                    <span class="text-[8px] font-bold ${isActive ? 'text-red-700' : 'text-gray-300'} uppercase tracking-wider mt-0.5">SLOT ${i + 1}</span>
                `;
            }
            enemyContainer.appendChild(slotCol);
        }

        // Update Slot Badges
        const allyBadge = document.getElementById('allyCountBadge');
        if (allyBadge) allyBadge.textContent = `${allyTeam.length}/5`;
        const enemyBadge = document.getElementById('enemyCountBadge');
        if (enemyBadge) enemyBadge.textContent = `${enemyTeam.length}/5`;

        // Update Lane Fulfillment Status & Roles Needed
        updateLaneStatus();
    }

    function updateLaneStatus() {
        const laneKeys = ['roam', 'jungle', 'mid', 'exp', 'gold'];
        const allyCovered = { roam: false, jungle: false, mid: false, exp: false, gold: false };
        const enemyCovered = { roam: false, jungle: false, mid: false, exp: false, gold: false };

        allyLanes.forEach(lane => {
            const l = (lane || '').toLowerCase();
            if (l === 'roam') allyCovered.roam = true;
            if (l === 'jgl' || l === 'jungle') allyCovered.jungle = true;
            if (l === 'mid') allyCovered.mid = true;
            if (l === 'exp') allyCovered.exp = true;
            if (l === 'gold') allyCovered.gold = true;
        });

        enemyLanes.forEach(lane => {
            const l = (lane || '').toLowerCase();
            if (l === 'roam') enemyCovered.roam = true;
            if (l === 'jgl' || l === 'jungle') enemyCovered.jungle = true;
            if (l === 'mid') enemyCovered.mid = true;
            if (l === 'exp') enemyCovered.exp = true;
            if (l === 'gold') enemyCovered.gold = true;
        });

        laneKeys.forEach(l => {
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
        } else if (!allyCovered.mid) {
            allyNeededBadge.className = 'bg-amber-100 text-amber-800 border border-amber-300 text-[9px] font-black px-2 py-0.5 rounded-full uppercase';
            allyNeededBadge.textContent = 'Wajib Mid / Mage';
        } else if (!allyCovered.roam) {
            allyNeededBadge.className = 'bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A] text-[9px] font-black px-2 py-0.5 rounded-full uppercase';
            allyNeededBadge.textContent = 'Perlu Roam';
        } else if (!allyCovered.jungle) {
            allyNeededBadge.textContent = 'Perlu Jungle';
        } else if (!allyCovered.gold) {
            allyNeededBadge.textContent = 'Perlu Gold';
        } else if (!allyCovered.exp) {
            allyNeededBadge.textContent = 'Perlu Exp';
        } else {
            allyNeededBadge.textContent = 'Sinergi Baik';
        }
    }

    // Daftar inisiator Crowd Control (CC) area/lockdown
    const HARD_CC_HEROES = [
        'tigreal', 'atlas', 'carmilla', 'akai', 'khufra', 'ruby', 'terizla', 'kaja',
        'franco', 'chou', 'gatotkaca', 'belerick', 'gloo', 'lolita', 'minotaur',
        'badang', 'guinevere', 'jawhead', 'edith', 'hylos', 'mathilda', 'silvanna',
        'valir', 'aurora', 'nana', 'vexana'
    ];

    function interpretTeamPowerSpike(spikes, totalCount) {
        if (!totalCount || totalCount === 0) {
            return { text: 'Belum ada data', dominant: null };
        }
        const early = spikes.early || 0;
        const mid = spikes.mid || 0;
        const late = spikes.late || 0;

        if (mid > 0 && late === 0 && early <= mid) {
            return { text: 'Unggul di Mid, lemah di Late.', dominant: 'Mid' };
        } else if (early > 0 && late === 0 && mid === 0) {
            return { text: 'Dominan di Early, lemah di Late.', dominant: 'Early' };
        } else if (early > mid && early > late) {
            return { text: 'Dominan di Early tempo (0–5m), prioritaskan invasi & snowball outer turret shield.', dominant: 'Early' };
        } else if (mid > early && mid >= late) {
            return { text: 'Unggul di Mid game (5–12m), puncak rotasi gank & perebutan objektif Turtle/Lord.', dominant: 'Mid' };
        } else if (late > early && late >= mid) {
            return { text: 'Unggul di Late game (12+m / Trinity MM), pertahankan wave untuk scaling penuh hingga item Trinity jadi.', dominant: 'Late' };
        } else {
            return { text: 'Kurva power spike stabil di seluruh fase permainan.', dominant: 'Balanced' };
        }
    }

    function compareTeamsPowerSpike(allySpikes, allyCount, enemySpikes, enemyCount) {
        if ((!allyCount || allyCount === 0) && (!enemyCount || enemyCount === 0)) {
            return {
                summary: 'Menunggu pemilihan hero untuk membandingkan tempo dan kurva kekuatan kedua tim.',
                badge: 'Netral'
            };
        }
        if (!allyCount || allyCount === 0) {
            return {
                summary: 'Menunggu pemilihan hero Tim Sekutu untuk analisis komparasi tempo pertandingan.',
                badge: 'Netral'
            };
        }
        if (!enemyCount || enemyCount === 0) {
            return {
                summary: 'Menunggu pemilihan hero Tim Musuh untuk analisis komparasi tempo pertandingan.',
                badge: 'Netral'
            };
        }

        const aEarly = allySpikes.early || 0;
        const aMid = allySpikes.mid || 0;
        const aLate = allySpikes.late || 0;

        const eEarly = enemySpikes.early || 0;
        const eMid = enemySpikes.mid || 0;
        const eLate = enemySpikes.late || 0;

        if (aEarly > eEarly && eLate > aLate) {
            return {
                summary: 'Kesimpulan: Tim Sekutu unggul di Early tempo (0–5m); Tim Musuh mendominasi Late scaling (12+m / Trinity MM). Sekutu wajib selesaikan game cepat sebelum carry musuh scaling!',
                badge: 'Early Sekutu vs Late Musuh'
            };
        }
        if (eEarly > aEarly && aLate > eLate) {
            return {
                summary: 'Kesimpulan: Tim Musuh menekan di Early game (0–5m); Tim Sekutu mendominasi Late scaling (12+m / Trinity MM). Bertahan di bawah turret hingga Marksman menyelesaikan Trinity build!',
                badge: 'Late Scaling Sekutu'
            };
        }
        if (aMid > eMid && eLate > aLate) {
            return {
                summary: 'Kesimpulan: Tim Sekutu unggul di Mid game peak (5–12m); waspadai lonjakan kekuatan Tim Musuh saat memasuki Late game (12+m / Trinity MM).',
                badge: 'Mid Peak Sekutu'
            };
        }
        if (eMid > aMid && aLate > eLate) {
            return {
                summary: 'Kesimpulan: Tim Musuh kuat di perebutan Lord menit 8–12; Tim Sekutu unggul saat game memasuki Late game (12+m / Trinity MM).',
                badge: 'Late Advantage Sekutu'
            };
        }
        if (aEarly > eEarly && aMid >= eMid) {
            return {
                summary: 'Kesimpulan: Tim Sekutu mendominasi tempo Early & Mid game, pegang kendali Turtle dan Lord pertama!',
                badge: 'Dominasi Sekutu'
            };
        }
        if (eEarly > aEarly && eMid >= aMid) {
            return {
                summary: 'Kesimpulan: Tim Musuh mendominasi tempo Early & Mid game, waspadai invasi agresif ke jungle sekutu.',
                badge: 'Dominasi Musuh'
            };
        }
        if (aLate > eLate) {
            return {
                summary: 'Kesimpulan: Tim Sekutu unggul di Late game (12+m / Trinity MM); formasi teamfight 5v5 sangat mematikan di fase akhir saat carry mencapai item optimal.',
                badge: 'Late Sekutu'
            };
        }
        if (eLate > aLate) {
            return {
                summary: 'Kesimpulan: Tim Musuh unggul di Late game (12+m / Trinity MM); hindari memperpanjang pertandingan setelah menit 12 jika objektif turret belum diamankan.',
                badge: 'Late Musuh'
            };
        }

        return {
            summary: 'Kesimpulan: Kurva power spike kedua tim relatif seimbang; hasil laga ditentukan oleh eksekusi teamfight dan draft counter.',
            badge: 'Seimbang'
        };
    }

    function renderSingleTeamPowerSpike(teamKey, teamList, spikes) {
        const countsEl = document.getElementById(teamKey + 'PowerSpikeCounts');
        const contentEl = document.getElementById(teamKey + 'PowerSpikeContent');
        if (!contentEl) return;

        if (!teamList || teamList.length === 0) {
            if (countsEl) countsEl.classList.add('hidden');
            contentEl.innerHTML = '<div class="text-[9.5px] text-gray-400 italic py-0.5">Belum ada data</div>';
            return;
        }

        const total = teamList.length;
        const early = spikes.early || 0;
        const mid = spikes.mid || 0;
        const late = spikes.late || 0;

        if (countsEl) {
            countsEl.classList.remove('hidden');
            countsEl.innerHTML = `<span class="text-teal-700 font-extrabold">E: ${early}</span> • <span class="text-blue-700 font-extrabold">M: ${mid}</span> • <span class="text-purple-700 font-extrabold">L: ${late}</span>`;
        }

        const earlyPct = Math.round((early / total) * 100);
        const midPct = Math.round((mid / total) * 100);
        const latePct = Math.max(0, 100 - earlyPct - midPct);

        const interp = interpretTeamPowerSpike(spikes, total);

        contentEl.innerHTML = `
            <div class="w-full h-2 rounded-full overflow-hidden flex bg-gray-100 border border-[#F3E8E8]">
                <div class="bg-teal-500 h-full transition-all duration-300" style="width: ${earlyPct}%;" title="Early: ${early} hero (${earlyPct}%)"></div>
                <div class="bg-blue-500 h-full transition-all duration-300" style="width: ${midPct}%;" title="Mid: ${mid} hero (${midPct}%)"></div>
                <div class="bg-purple-600 h-full transition-all duration-300" style="width: ${latePct}%;" title="Late: ${late} hero (${latePct}%)"></div>
            </div>
            <div class="text-[9.5px] font-bold text-[#18181B] leading-tight pt-1">
                ${interp.text}
            </div>
        `;
    }

    function renderTeamCompositionAlerts(teamType, teamList, teamLanes, damageDist) {
        const container = document.getElementById(teamType + 'CompositionAlerts');
        if (!container) return;

        if (!teamList || teamList.length === 0) {
            container.innerHTML = '<div class="text-[9.5px] font-medium text-gray-400 italic py-0.5">Status: Belum ada hero terpilih.</div>';
            return;
        }

        const count = teamList.length;
        const alerts = [];

        // Jika Tim Sekutu: gunakan Flag Peringatan Kuantitatif berbasis 5 Atribut Coach vs Target Profil T_k
        if (teamType === 'ally') {
            const serverExp = latestInferenceData?.rbr_explanation;
            const rbrExp = (serverExp && serverExp.has_ally) ? serverExp : evaluateLocalQuantitativeExplanation(allyTeam, enemyTeam);

            if (rbrExp && Array.isArray(rbrExp.attribute_warnings) && rbrExp.attribute_warnings.length > 0) {
                rbrExp.attribute_warnings.forEach(w => {
                    alerts.push({
                        rule_id: w.rule_id || 'RULE-001',
                        severity: w.severity || 'warning',
                        title: `Defisit ${w.label}`,
                        message: w.message
                    });
                });
            }

            if (count >= 2 && damageDist.phys >= 100) {
                alerts.push({
                    rule_id: 'RULE-003',
                    severity: 'warning',
                    title: 'Damage 100% Physical',
                    message: 'Komposisi 100% Physical Damage: lawan mudah menumpuk Physical Defense.'
                });
            } else if (count >= 2 && damageDist.mag >= 100) {
                alerts.push({
                    rule_id: 'RULE-003',
                    severity: 'warning',
                    title: 'Damage 100% Magic',
                    message: 'Komposisi 100% Magic Damage: lawan mudah menumpuk Magic Defense.'
                });
            }

            if (alerts.length === 0) {
                alerts.push({
                    rule_id: 'RULE-OK',
                    severity: 'info',
                    title: 'Komposisi Mendekati Target Pemenang',
                    message: 'Seluruh 5 atribut komposisi tim seimbang dan mendekati/melampaui Target Profil pemenang T_k.'
                });
            }
        } else {
            let frontlineCount = 0;
            let ccCount = 0;
            let hasMage = false;

            teamList.forEach((name, idx) => {
                const lane = (teamLanes[idx] || '').toUpperCase();
                const h = pickerHeroes.find(item => item.name.toLowerCase() === name.toLowerCase());
                const role = h && h.role ? h.role.toLowerCase() : '';
                const dmg = h ? (h.damage_type || 'Physical') : 'Physical';

                if (role.includes('mage') || lane === 'MID' || dmg === 'Magic') hasMage = true;
                if (role.includes('tank') || role.includes('fighter') || lane === 'ROAM') frontlineCount++;
                if (HARD_CC_HEROES.includes(name.toLowerCase())) ccCount++;
            });

            if (count >= 3 && (!hasMage || damageDist.mag < 15)) {
                alerts.push({
                    rule_id: 'RULE-003',
                    severity: 'warning',
                    title: 'Defisit Magic Damage',
                    message: `Belum ada Magic Damage (${damageDist.mag}%) di tim musuh.`
                });
            }
            if (count >= 3 && frontlineCount === 0) {
                alerts.push({
                    rule_id: 'RULE-002',
                    severity: 'critical',
                    title: 'Lawan Tanpa Frontliner',
                    message: 'Tim musuh belum memiliki Frontliner / Tank yang kuat.'
                });
            }
            if (count >= 3 && ccCount === 0) {
                alerts.push({
                    rule_id: 'RULE-001',
                    severity: 'warning',
                    title: 'Lawan Minim Crowd Control',
                    message: 'Tim musuh minim inisiator Crowd Control.'
                });
            }
        }

        if (alerts.length === 0) {
            container.innerHTML = `<div class="text-[9.5px] font-medium text-gray-500 italic py-0.5">Komposisi ${count} hero dalam evaluasi forward chaining.</div>`;
            return;
        }

        let html = '';
        alerts.forEach(alert => {
            if (alert.severity === 'critical') {
                html += `
                    <div class="bg-red-50/90 border border-red-300 text-red-900 rounded-lg p-2 text-[9.5px] leading-tight flex items-start gap-1.5 shadow-xs">
                        <span class="w-4 h-4 rounded-full bg-red-600 text-white flex items-center justify-center font-black text-[9px] shrink-0 mt-0.5">!</span>
                        <div>
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <span class="font-black text-[9px] bg-red-200/80 text-red-900 px-1.5 py-0.2 rounded uppercase">[${alert.rule_id}]</span>
                                <span class="font-extrabold text-[8.5px] text-red-700 uppercase tracking-wider">CRITICAL ALERT</span>
                            </div>
                            <p class="font-bold">${alert.message}</p>
                        </div>
                    </div>
                `;
            } else if (alert.severity === 'warning') {
                html += `
                    <div class="bg-amber-50/90 border border-amber-300 text-amber-900 rounded-lg p-2 text-[9.5px] leading-tight flex items-start gap-1.5 shadow-xs">
                        <span class="w-4 h-4 rounded-full bg-amber-600 text-white flex items-center justify-center font-black text-[9px] shrink-0 mt-0.5">!</span>
                        <div>
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <span class="font-black text-[9px] bg-amber-200/80 text-amber-900 px-1.5 py-0.2 rounded uppercase">[${alert.rule_id}]</span>
                                <span class="font-extrabold text-[8.5px] text-amber-700 uppercase tracking-wider">WARNING</span>
                            </div>
                            <p class="font-bold">${alert.message}</p>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="bg-emerald-50/90 border border-emerald-300 text-emerald-900 rounded-lg p-2 text-[9.5px] leading-tight flex items-start gap-1.5 shadow-xs">
                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black text-[9px] shrink-0 mt-0.5">✓</span>
                        <div>
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <span class="font-black text-[9px] bg-emerald-200/80 text-emerald-900 px-1.5 py-0.2 rounded uppercase">[${alert.rule_id}]</span>
                                <span class="font-extrabold text-[8.5px] text-emerald-700 uppercase tracking-wider">KOMPOSISI STABIL</span>
                            </div>
                            <p class="font-bold">${alert.message}</p>
                        </div>
                    </div>
                `;
            }
        });

        container.innerHTML = html;
    }

    // Perhitungan Damage Distribution & Power Spike
    function updateDamageAndPowerSpikes() {
        // Tim Sekutu Damage
        const allyDist = computeTeamDamage(allyTeam);
        const allyPhysEl = document.getElementById('allyPhysPct');
        const allyMagEl = document.getElementById('allyMagPct');
        const allyPhysBar = document.getElementById('allyPhysBar');
        const allyMagBar = document.getElementById('allyMagBar');
        const allyBarContainer = document.getElementById('allyDamageBarContainer');

        if (allyTeam.length === 0) {
            if (allyPhysEl) allyPhysEl.textContent = '0%';
            if (allyMagEl) allyMagEl.textContent = '0%';
            if (allyPhysBar) allyPhysBar.style.width = '0%';
            if (allyMagBar) allyMagBar.style.width = '0%';
            if (allyBarContainer) allyBarContainer.className = 'w-full h-1.5 rounded-full overflow-hidden flex bg-gray-100 border border-[#F3E8E8] transition-all';
        } else {
            if (allyPhysEl) allyPhysEl.textContent = `${allyDist.phys}%`;
            if (allyMagEl) allyMagEl.textContent = `${allyDist.mag}%`;
            if (allyPhysBar) allyPhysBar.style.width = `${allyDist.phys}%`;
            if (allyMagBar) allyMagBar.style.width = `${allyDist.mag}%`;
            if (allyBarContainer) allyBarContainer.className = 'w-full h-2 rounded-full overflow-hidden flex bg-gray-100 border border-[#F3E8E8] transition-all';
        }

        // Tim Sekutu Power Spike 3-Segment Bar & Counts
        const allySpikes = computeTeamPowerSpikes(allyTeam);
        renderSingleTeamPowerSpike('ally', allyTeam, allySpikes);

        // Tim Sekutu Alert Komposisi Rule-Based
        renderTeamCompositionAlerts('ally', allyTeam, allyLanes, allyDist);

        // Tim Musuh Damage
        const enemyDist = computeTeamDamage(enemyTeam);
        const enemyPhysEl = document.getElementById('enemyPhysPct');
        const enemyMagEl = document.getElementById('enemyMagPct');
        const enemyPhysBar = document.getElementById('enemyPhysBar');
        const enemyMagBar = document.getElementById('enemyMagBar');
        const enemyBarContainer = document.getElementById('enemyDamageBarContainer');

        if (enemyTeam.length === 0) {
            if (enemyPhysEl) enemyPhysEl.textContent = '0%';
            if (enemyMagEl) enemyMagEl.textContent = '0%';
            if (enemyPhysBar) enemyPhysBar.style.width = '0%';
            if (enemyMagBar) enemyMagBar.style.width = '0%';
            if (enemyBarContainer) enemyBarContainer.className = 'w-full h-1.5 rounded-full overflow-hidden flex bg-gray-100 border border-[#F3E8E8] transition-all';
        } else {
            if (enemyPhysEl) enemyPhysEl.textContent = `${enemyDist.phys}%`;
            if (enemyMagEl) enemyMagEl.textContent = `${enemyDist.mag}%`;
            if (enemyPhysBar) enemyPhysBar.style.width = `${enemyDist.phys}%`;
            if (enemyMagBar) enemyMagBar.style.width = `${enemyDist.mag}%`;
            if (enemyBarContainer) enemyBarContainer.className = 'w-full h-2 rounded-full overflow-hidden flex bg-gray-100 border border-[#F3E8E8] transition-all';
        }

        // Tim Musuh Power Spike 3-Segment Bar & Counts
        const enemySpikes = computeTeamPowerSpikes(enemyTeam);
        renderSingleTeamPowerSpike('enemy', enemyTeam, enemySpikes);

        // Tim Musuh Alert Komposisi Rule-Based
        renderTeamCompositionAlerts('enemy', enemyTeam, enemyLanes, enemyDist);

        // Komparasi Power Spike & Tempo Antar Tim
        const comparison = compareTeamsPowerSpike(allySpikes, allyTeam.length, enemySpikes, enemyTeam.length);
        const compSummaryEl = document.getElementById('powerSpikeComparisonSummary');
        const tempoBadgeEl = document.getElementById('tempoAdvantageBadge');
        if (compSummaryEl) {
            compSummaryEl.textContent = comparison.summary;
        }
        if (tempoBadgeEl) {
            tempoBadgeEl.textContent = comparison.badge;
            if (comparison.badge.includes('Sekutu')) {
                tempoBadgeEl.className = 'hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-[#700B1A]/10 text-[#700B1A] border border-[#700B1A]/20 uppercase whitespace-nowrap shrink-0';
            } else if (comparison.badge.includes('Musuh')) {
                tempoBadgeEl.className = 'hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-red-100 text-red-700 border border-red-200 uppercase whitespace-nowrap shrink-0';
            } else {
                tempoBadgeEl.className = 'hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-gray-100 text-gray-600 border border-gray-200 uppercase whitespace-nowrap shrink-0';
            }
        }
    }

    function computeTeamDamage(teamList) {
        if (!teamList || teamList.length === 0) return { phys: 0, mag: 0 };
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
        const physPct = Math.round((phys / total) * 100);
        return {
            phys: physPct,
            mag: 100 - physPct
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
        if (!grid) return;
        grid.innerHTML = '';
        const searchInput = document.getElementById('pickerSearchInput');
        const search = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const allBans = [...allyBans, ...enemyBans];

        pickerHeroes.forEach(hero => {
            const hName = hero.name || '';
            const hRole = hero.role || '';
            const hLane = hero.lane || '';
            const matchSearch = !search || hName.toLowerCase().includes(search) || hRole.toLowerCase().includes(search);
            const matchLane = pickerLane === 'all' || hLane.includes(pickerLane);

            if (matchSearch && matchLane) {
                const hNameLow = hName.toLowerCase();
                const inAlly = allyTeam.some(h => (h || '').toLowerCase() === hNameLow);
                const inEnemy = enemyTeam.some(h => (h || '').toLowerCase() === hNameLow);
                const inAllyBan = allyBans.some(h => (h || '').toLowerCase() === hNameLow);
                const inEnemyBan = enemyBans.some(h => (h || '').toLowerCase() === hNameLow);
                const isBanned = inAllyBan || inEnemyBan;

                const item = document.createElement('button');
                item.type = 'button';
                item.onclick = () => toggleSelectHero(hero.name);

                let cardClasses = 'flex flex-col items-center justify-center p-2 rounded-xl transition relative ';
                if (inAlly) {
                    cardClasses += 'bg-[#FCECEE] border-2 border-[#700B1A] shadow-xs';
                } else if (inEnemy) {
                    cardClasses += 'bg-red-50 border-2 border-[#DC2626] shadow-xs';
                } else if (isBanned) {
                    cardClasses += 'bg-gray-100 border border-gray-300 opacity-60';
                } else {
                    cardClasses += 'bg-[#FAF8F8] border border-[#F3E8E8] hover:bg-[#FAF0F1] hover:border-[#F8B4BD]';
                }

                item.className = cardClasses;

                const spikeText = isBanned ? 'BANNED' : (hero.power_spike ? hero.power_spike.split(' ')[0] : 'Mid');

                item.innerHTML = `
                    <div class="relative w-9 h-9 rounded-full overflow-hidden ${inAlly ? 'border-2 border-[#700B1A]' : (inEnemy ? 'border-2 border-[#DC2626]' : (isBanned ? 'border-2 border-red-700' : 'border border-[#F3E8E8]'))} flex items-center justify-center font-black text-xs bg-white">
                        <img src="${hero.portrait}" 
                             alt="${hero.name}" 
                             class="w-full h-full object-cover rounded-full ${isBanned ? 'grayscale' : ''}"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(hero.name)}&background=700B1A&color=fff';">
                        ${inAlly ? `<span class="absolute inset-0 bg-[#700B1A]/85 text-white flex items-center justify-center font-black text-xs">✓</span>` : ''}
                        ${inEnemy ? `<span class="absolute inset-0 bg-[#DC2626]/85 text-white flex items-center justify-center font-black text-xs">✕</span>` : ''}
                        ${isBanned ? `<span class="absolute inset-0 bg-red-950/80 text-white flex items-center justify-center font-black text-[9px] uppercase tracking-wider" title="Hero di-ban">BAN</span>` : ''}
                    </div>
                    <span class="font-bold text-[10px] text-gray-800 mt-1 truncate max-w-full text-center ${isBanned ? 'line-through text-gray-400' : ''}">${hero.name}</span>
                    <span class="text-[8px] text-gray-400 font-semibold truncate">${spikeText}</span>
                `;
                grid.appendChild(item);
            }
        });
    }

    function renderTacticalAnalysis() {
        // Update Tactical Draft Status Subtitle di Header Panel
        const draftStatusEl = document.getElementById('tacticalDraftStatus');
        if (draftStatusEl) {
            if (allyTeam.length === 0 && enemyTeam.length === 0) {
                draftStatusEl.textContent = 'Draft kosong: Menampilkan rekomendasi meta dasar turnamen MPL ID S18';
            } else {
                draftStatusEl.textContent = `Berdasarkan ${allyTeam.length} hero sekutu, ${enemyTeam.length} hero musuh`;
            }
        }

        // Render Identitas Draft & Cara Bermain Tim/Lawan (RBR Ringan Strategy Engine)
        renderDraftIdentities();
        renderSimilarMatches(latestInferenceData?.similar_matches || []);

        // PICKS CALCULATION DENGAN TRANSPARANSI ATURAN RBR & CONSTRAINT MUTLAK
        const picksContainer = document.getElementById('recommendedPicksList');
        picksContainer.innerHTML = '';

        const traceContainer = document.getElementById('ruleTraceLogs');
        if (traceContainer) traceContainer.innerHTML = '';
        const ruleTraces = [];

        // 1. Evaluasi Komposisi Tim Sekutu (Mage Wajib & Frontline Wajib)
        const allyHasMage = allyTeam.some((name, idx) => {
            const lane = (allyLanes[idx] || '').toUpperCase();
            if (lane === 'MID') return true;
            const h = pickerHeroes.find(item => item.name.toLowerCase() === name.toLowerCase());
            return h && (h.damage_type === 'Magic' || (h.role && h.role.toLowerCase() === 'mage'));
        });

        const allyDamage = computeTeamDamage(allyTeam);
        const needsMageMandatory = (allyTeam.length >= 3 && (!allyHasMage || allyDamage.mag < 15));

        const allyHasFrontline = allyTeam.some((name, idx) => {
            const lane = (allyLanes[idx] || '').toUpperCase();
            if (lane === 'ROAM' || lane === 'EXP') return true;
            const h = pickerHeroes.find(item => item.name.toLowerCase() === name.toLowerCase());
            return h && (h.role && (h.role.toLowerCase().includes('tank') || h.role.toLowerCase().includes('fighter')));
        });
        const needsFrontlineMandatory = (allyTeam.length >= 3 && !allyHasFrontline);

        // Evaluasi dan render alert komposisi untuk kedua tim
        renderTeamCompositionAlerts('ally', allyTeam, allyLanes, allyDamage);
        renderTeamCompositionAlerts('enemy', enemyTeam, enemyLanes, computeTeamDamage(enemyTeam));

        // Catat Aturan Komposisi ke Rule Traces jika aktif
        if (needsMageMandatory) {
            ruleTraces.push({
                code: 'RBR-COMP-01',
                badge: 'KOMPOSISI MUTLAK (MAGE WAJIB)',
                condition: `Ally Picked ${allyTeam.length} Hero TANPA Mage / Midlaner (Magic: ${allyDamage.mag}%)`,
                rec: "Wajib Rekomendasi Hero Mage (Yve, Pharsa, Valentina, Xavier, Zhuxin)",
                confidence: '98.0%',
                desc: 'Aturan mutlak penyeimbang damage magic agar musuh tidak stacking physical defense armor.'
            });
        }
        if (needsFrontlineMandatory) {
            ruleTraces.push({
                code: 'RBR-COMP-02',
                badge: 'FRONT-LINE MANDATORY',
                condition: `Ally Picked ${allyTeam.length} Hero TANPA Tank / Frontline Durability`,
                rec: "Wajib Rekomendasi Roamer / Frontline (Tigreal, Baxia, Hylos)",
                confidence: '95.0%',
                desc: 'Aturan durabilitas garis depan untuk mengamankan retribusi kontes turtle/lord.'
            });
        }

        // Update teks rumus aktif SAW di header rekomendasi
        const formulaBannerEl = document.getElementById('sawActiveFormulaText');
        if (formulaBannerEl) {
            const w = latestInferenceData?.saw_meta?.weights;
            if (w && (allyTeam.length > 0 || enemyTeam.length > 0)) {
                formulaBannerEl.innerHTML = `Score = 0.40·DNA + 0.35·Comp + 0.25·Counter <span class="text-gray-500 font-sans font-semibold">(Aktif: ${Number(w.alpha).toFixed(2)}·DNA + ${Number(w.beta).toFixed(2)}·Comp + ${Number(w.gamma).toFixed(2)}·Ctr • m=5)</span>`;
            } else {
                formulaBannerEl.innerHTML = `Score = 0.40·DNA + 0.35·Comp + 0.25·Counter <span class="text-gray-500 font-sans font-semibold">(Bayesian Shrinkage m=5)</span>`;
            }
        }

        // Update Info Lane Dibutuhkan (Lapisan 1 Hard Constraint Filter)
        const neededLanesContainer = document.getElementById('neededLanesBadgeList');
        if (neededLanesContainer) {
            const neededLanes = (latestInferenceData && Array.isArray(latestInferenceData.needed_lanes)) 
                ? latestInferenceData.needed_lanes 
                : ((latestInferenceData && latestInferenceData.saw_meta && Array.isArray(latestInferenceData.saw_meta.needed_lanes))
                    ? latestInferenceData.saw_meta.needed_lanes 
                    : (initialInferenceData && Array.isArray(initialInferenceData.needed_lanes) ? initialInferenceData.needed_lanes : ['EXP Lane', 'Jungle', 'Mid Lane', 'Gold Lane', 'Roaming']));

            if (allyTeam.length >= 5) {
                neededLanesContainer.innerHTML = `<span class="bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded text-[9px] font-bold">Lengkap (5/5 Hero Terpilih)</span>`;
            } else if (Array.isArray(neededLanes)) {
                if (neededLanes.length === 0) {
                    neededLanesContainer.innerHTML = `<span class="bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded text-[9px] font-bold">Lengkap (Semua 5 Lane Terisi)</span>`;
                } else {
                    neededLanesContainer.innerHTML = neededLanes.map(lane => 
                        `<span class="bg-[#FAF0F1] text-[#700B1A] border border-[#F8B4BD] px-1.5 py-0.5 rounded text-[9px] font-black">${lane}</span>`
                    ).join(' ');
                }
            }
        }

        let picks = [];
        const sawSource = (latestInferenceData && Array.isArray(latestInferenceData.saw_recommendations) && latestInferenceData.saw_recommendations.length > 0)
            ? latestInferenceData.saw_recommendations
            : currentRecommendations;

        if (sawSource && sawSource.length > 0) {
            const allBans = [...allyBans, ...enemyBans];
            picks = sawSource.filter(rec => {
                const n = (rec.nama || rec.hero || '').toLowerCase();
                const inAlly = allyTeam.some(a => a.toLowerCase() === n);
                const inEnemy = enemyTeam.some(e => e.toLowerCase() === n);
                const isBan = allBans.some(b => b.toLowerCase() === n);
                return !inAlly && !inEnemy && !isBan;
            });
        }

        // Fallback jika belum ada data SAW dari server
        if (picks.length === 0) {
            picks = defaultPicks;
        }

        const filteredPicks = picks.filter(p => {
            if (recLaneFilter === 'all') return true;
            const lFilter = recLaneFilter.toLowerCase();
            const flex = [p.lane_1, p.lane_2, ...(p.lane_flex || []), p.lane].filter(Boolean);
            const matchFlex = flex.some(l => String(l).toLowerCase().includes(lFilter));
            const matchRole = `${p.role || ''} ${p.role_1 || ''} ${p.role_2 || ''}`.toLowerCase().includes(lFilter);
            return matchFlex || matchRole;
        });

        const top10Picks = filteredPicks.slice(0, 10);
        const picksBadge = document.getElementById('picksCountBadge');
        if (picksBadge) picksBadge.textContent = top10Picks.length;

        const sawWeights = latestInferenceData?.saw_meta?.weights || { alpha: 1.0, beta: 0.0, gamma: 0.0 };

        // RENDER TOP 10 KARTU REKOMENDASI HYBRID SAW + BAYESIAN SHRINKAGE (TANPA CONFIDENCE STATIS)
        top10Picks.forEach((p, idx) => {
            const pHeroName = p.nama || p.hero;
            const cardKey = p.hero_id || pHeroName;
            const isExpanded = (expandedRecHeroId === cardKey || expandedRecHeroId === pHeroName);

            const totalScoreVal = (p.total_score != null)
                ? parseFloat(p.total_score)
                : (p.saw_score != null ? parseFloat(p.saw_score) * 100.0 : 50.0);
            const scoreFormatted = totalScoreVal.toFixed(1);
            const sawScoreDecimal = (p.saw_score != null) ? parseFloat(p.saw_score) : (totalScoreVal / 100.0);
            const progressPct = Math.min(100, Math.max(8, Math.round(totalScoreVal)));

            const rankBadgeColor = idx === 0
                ? 'bg-[#700B1A] text-white'
                : (idx < 3 ? 'bg-[#991B1B] text-white' : 'bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD]');

            const laneLabel = (p.lane_1 || (p.lane_flex && p.lane_flex[0]) || p.lane || 'Flex').toUpperCase();
            const roleLabel = [p.role_1 || p.role || 'Hero', p.role_2].filter(Boolean).join(' / ');

            const sDna = p.s_dna || { raw: 0, norm: 0.5, weight: sawWeights.alpha, n_team: 0, n_global: 0, p_global: 0 };
            const sComp = p.s_comp || { active: false, raw: null, norm: null, weight: sawWeights.beta, gap_before: null, gap_after: null };
            const sCounter = p.s_counter || { active: false, raw: null, norm: null, weight: sawWeights.gamma, w_global: 0.5, matchups: [] };

            const dnaNormPct = Math.round((Number(sDna.norm ?? 0.5)) * 100);
            const compNormPct = sComp.active && sComp.norm != null ? Math.round(Number(sComp.norm) * 100) : null;
            const ctrNormPct = sCounter.active && sCounter.norm != null ? Math.round(Number(sCounter.norm) * 100) : null;

            const isTeamPoolPick = Number(sDna.n_team || 0) > 0;
            const comfortBadgeHtml = isTeamPoolPick
                ? `<span class="bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] text-[8px] font-black px-1.5 py-0.2 rounded flex items-center gap-0.5 shadow-2xs flex-shrink-0" title="Dipick ${sDna.n_team}x oleh ${selectedTeamName || 'tim terpilih'} di MPL ID S18">
                    <span>DNA Tim (${sDna.n_team}x)</span>
                   </span>`
                : '';

            const coachRaw = p.coach_attributes?.raw || {
                sustainability: 5,
                attack_power: 5,
                crowd_control: 5,
                mobility: 5,
                lane_control: 5
            };

            const wAlpha = Number(sDna.weight ?? sawWeights.alpha ?? 0.40);
            const wBeta = Number(sComp.weight ?? sawWeights.beta ?? 0);
            const wGamma = Number(sCounter.weight ?? sawWeights.gamma ?? 0);

            const card = document.createElement('div');
            card.className = `rec-pick-item bg-white border ${isExpanded ? 'border-[#700B1A] ring-1 ring-[#700B1A]/20 shadow-xs' : 'border-[#F3E8E8] hover:border-[#700B1A] shadow-2xs'} rounded-2xl overflow-hidden transition-all duration-200`;

            card.innerHTML = `
                <!-- 1. COMPACT HERO ROW WITH SAW BREAKDOWN -->
                <div class="px-3 py-2.5 flex items-center justify-between gap-2.5 cursor-pointer select-none" onclick="toggleRecCard('${cardKey}')">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <span class="text-[9.5px] font-black px-1.5 py-0.5 rounded flex-shrink-0 ${rankBadgeColor}" title="Peringkat #${idx + 1} Hybrid SAW">
                            #${idx + 1}
                        </span>
                        <div class="relative w-10 h-10 rounded-full overflow-hidden flex-shrink-0 border border-[#F3E8E8] bg-gray-50 shadow-2xs">
                            <img src="${p.portrait || ''}" 
                                 alt="${pHeroName}" 
                                 class="w-full h-full object-cover rounded-full"
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(pHeroName)}&background=700B1A&color=fff';">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h4 class="font-black text-xs text-[#18181B] truncate">${pHeroName}</h4>
                                <span class="text-[9.5px] text-gray-500 font-semibold truncate">${roleLabel}</span>
                                <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-600 text-[8px] font-bold px-1.5 py-0.2 rounded">
                                    ${laneLabel}
                                </span>
                                ${comfortBadgeHtml}
                            </div>
                            <!-- Breakdown Transparan 3 Komponen SAW -->
                            <div class="flex items-center gap-1 mt-1 flex-wrap text-[8.5px] font-bold">
                                <span class="bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] px-1.5 py-0.2 rounded" title="S_dna (Bayesian Shrinkage m=5): p̂=${Number(sDna.raw || 0).toFixed(4)}">
                                    DNA: ${dnaNormPct}%
                                </span>
                                <span class="${compNormPct !== null ? 'bg-blue-50 text-blue-800 border-blue-200' : 'bg-gray-50 text-gray-400 border-gray-200'} border px-1.5 py-0.2 rounded" title="${sComp.active ? `S_comp Δ(h)=${Number(sComp.raw || 0).toFixed(4)}` : 'Pilih minimal 1 hero sekutu untuk mengaktifkan S_comp'}">
                                    Comp: ${compNormPct !== null ? compNormPct + '%' : '—'}
                                </span>
                                <span class="${ctrNormPct !== null ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-gray-50 text-gray-400 border-gray-200'} border px-1.5 py-0.2 rounded" title="${sCounter.active ? `S_counter ŵ=${Number(sCounter.raw || 0).toFixed(4)}` : 'Pilih minimal 1 hero musuh untuk mengaktifkan S_counter'}">
                                    Counter: ${ctrNormPct !== null ? ctrNormPct + '%' : '—'}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right side: Total SAW Score & chevron -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-20 text-right">
                            <div class="flex items-center justify-end gap-0.5">
                                <span class="font-mono font-black text-xs text-[#700B1A]" title="Total Skor SAW">${scoreFormatted}</span>
                                <span class="text-[8px] text-gray-400 font-bold">/100</span>
                            </div>
                            <div class="w-full bg-[#FAF0F1] h-1.5 rounded-full overflow-hidden mt-0.5">
                                <div class="bg-[#700B1A] h-full rounded-full transition-all duration-300" style="width: ${progressPct}%"></div>
                            </div>
                            <div class="text-[8px] font-mono text-gray-400 mt-0.5 text-right">
                                SAW ${sawScoreDecimal.toFixed(3)}
                            </div>
                        </div>
                        <button type="button" class="w-6 h-6 rounded-full bg-gray-50 hover:bg-[#FCECEE] text-gray-400 hover:text-[#700B1A] flex items-center justify-center transition cursor-pointer">
                            <svg class="w-3 h-3 transition-transform duration-200 ${isExpanded ? 'rotate-180 text-[#700B1A]' : ''}" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 2. ACCORDION EXPANDED BODY (RINCIAN SAW + 5 ATRIBUT COACH) -->
                ${isExpanded ? `
                <div class="border-t border-[#FAF0F1] bg-[#FAF8F8]/70 p-3 space-y-3 text-xs">
                    <!-- a. Rincian Skor SAW -->
                    <div class="bg-white border border-[#F3E8E8] rounded-xl p-2.5 space-y-2 shadow-2xs">
                        <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-wider">
                            <span class="text-[#18181B]">Breakdown Skor Hybrid SAW</span>
                            <span class="font-mono text-[#700B1A] font-extrabold">${scoreFormatted} / 100</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[10px]">
                            <div class="space-y-1 p-2 rounded-lg bg-[#FAF8F8] border border-[#F3E8E8]">
                                <div class="flex justify-between font-bold text-[#700B1A]">
                                    <span>DNA (w=${wAlpha.toFixed(2)})</span>
                                    <span class="font-mono">${dnaNormPct}%</span>
                                </div>
                                <div class="w-full bg-[#FCECEE] h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-[#700B1A] h-full rounded-full" style="width: ${dnaNormPct}%"></div>
                                </div>
                                <div class="text-[8.5px] text-gray-500 font-mono">p̂=${Number(sDna.raw || 0).toFixed(4)} • Tim: ${sDna.n_team || 0}x</div>
                            </div>

                            <div class="space-y-1 p-2 rounded-lg bg-[#FAF8F8] border border-[#F3E8E8]">
                                <div class="flex justify-between font-bold text-blue-800">
                                    <span>Comp (w=${wBeta.toFixed(2)})</span>
                                    <span class="font-mono">${compNormPct !== null ? compNormPct + '%' : 'Nonaktif'}</span>
                                </div>
                                <div class="w-full bg-blue-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-blue-600 h-full rounded-full" style="width: ${compNormPct ?? 0}%"></div>
                                </div>
                                <div class="text-[8.5px] text-gray-500 font-mono">${sComp.active ? `Δ=${Number(sComp.raw || 0).toFixed(4)}` : 'Menunggu ally pick'}</div>
                            </div>

                            <div class="space-y-1 p-2 rounded-lg bg-[#FAF8F8] border border-[#F3E8E8]">
                                <div class="flex justify-between font-bold text-emerald-800">
                                    <span>Counter (w=${wGamma.toFixed(2)})</span>
                                    <span class="font-mono">${ctrNormPct !== null ? ctrNormPct + '%' : 'Nonaktif'}</span>
                                </div>
                                <div class="w-full bg-emerald-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-emerald-600 h-full rounded-full" style="width: ${ctrNormPct ?? 0}%"></div>
                                </div>
                                <div class="text-[8.5px] text-gray-500 font-mono">${sCounter.active ? `ŵ=${Number(sCounter.raw || 0).toFixed(4)}` : 'Menunggu enemy pick'}</div>
                            </div>
                        </div>
                        <div class="text-[9px] text-gray-500 font-mono pt-1 border-t border-gray-100 text-center">
                            Score = (${wAlpha.toFixed(2)} × ${Number(sDna.norm ?? 0.5).toFixed(2)}) + (${wBeta.toFixed(2)} × ${Number(sComp.norm ?? 0).toFixed(2)}) + (${wGamma.toFixed(2)} × ${Number(sCounter.norm ?? 0).toFixed(2)}) = ${sawScoreDecimal.toFixed(4)}
                        </div>
                    </div>

                    <!-- b. Profil 5 Atribut Kuantitatif Coach -->
                    <div class="bg-white border border-[#F3E8E8] rounded-xl p-2.5 space-y-2 shadow-2xs">
                        <div class="text-[10px] font-black uppercase tracking-wider text-gray-700 flex items-center justify-between">
                            <span>Profil 5 Atribut Coach (Skala 0–10)</span>
                            <span class="text-[9px] font-mono text-gray-400">Tabel heroes</span>
                        </div>
                        <div class="grid grid-cols-5 gap-1.5 text-center text-[9.5px]">
                            <div class="p-1.5 rounded-lg bg-[#FAF8F8] border border-[#F3E8E8]">
                                <div class="text-[8px] font-bold text-gray-400 uppercase">Sus</div>
                                <div class="font-black text-[#18181B]">${coachRaw.sustainability}</div>
                            </div>
                            <div class="p-1.5 rounded-lg bg-[#FAF8F8] border border-[#F3E8E8]">
                                <div class="text-[8px] font-bold text-gray-400 uppercase">Atk</div>
                                <div class="font-black text-[#18181B]">${coachRaw.attack_power}</div>
                            </div>
                            <div class="p-1.5 rounded-lg bg-[#FAF8F8] border border-[#F3E8E8]">
                                <div class="text-[8px] font-bold text-gray-400 uppercase">CC</div>
                                <div class="font-black text-[#18181B]">${coachRaw.crowd_control}</div>
                            </div>
                            <div class="p-1.5 rounded-lg bg-[#FAF8F8] border border-[#F3E8E8]">
                                <div class="text-[8px] font-bold text-gray-400 uppercase">Mob</div>
                                <div class="font-black text-[#18181B]">${coachRaw.mobility}</div>
                            </div>
                            <div class="p-1.5 rounded-lg bg-[#FAF8F8] border border-[#F3E8E8]">
                                <div class="text-[8px] font-bold text-gray-400 uppercase">Lane</div>
                                <div class="font-black text-[#18181B]">${coachRaw.lane_control}</div>
                            </div>
                        </div>
                        ${sCounter.active && Array.isArray(sCounter.matchups) && sCounter.matchups.length > 0 ? `
                            <div class="pt-1 border-t border-gray-100 flex items-center gap-1.5 flex-wrap text-[9.5px]">
                                <span class="font-bold text-gray-600">H2H Shrinkage:</span>
                                ${sCounter.matchups.map(m => `<span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-[8.5px]">vs ${m.enemy}: ${(Number(m.shrunk_wr) * 100).toFixed(1)}% (${m.wins}W/${m.games}G)</span>`).join('')}
                            </div>
                        ` : ''}
                    </div>

                    <!-- c. Action button -->
                    <div class="flex items-center justify-end pt-1">
                        <button type="button" onclick="event.stopPropagation(); selectRecommendedHero('${pHeroName}')" class="px-3.5 py-1.5 bg-[#700B1A] hover:bg-[#550713] text-white rounded-xl text-xs font-black uppercase tracking-wider transition cursor-pointer shadow-xs">
                            + Masukkan ke Slot Sekutu
                        </button>
                    </div>
                </div>
                ` : ''}
            `;
            picksContainer.appendChild(card);
        });

        // RENDER KOTAK EXPLAINABLE AI (XAI) / LAPISAN PENJELASAN RBR BERBASIS ANGKA NYATA
        if (traceContainer) {
            const serverExp = latestInferenceData?.rbr_explanation;
            const rbrExp = (serverExp && serverExp.has_ally) ? serverExp : evaluateLocalQuantitativeExplanation(allyTeam, enemyTeam);

            if (rbrExp && rbrExp.has_ally) {
                const items = [];
                if (Array.isArray(rbrExp.attribute_warnings)) {
                    rbrExp.attribute_warnings.forEach(w => {
                        items.push(`
                            <div class="bg-white border border-amber-200 rounded-xl p-2.5 shadow-2xs space-y-1">
                                <div class="flex items-center justify-between text-[10px] font-black">
                                    <span class="text-[#700B1A] flex items-center gap-1">
                                        <span class="bg-[#FCECEE] text-[#700B1A] px-1.5 py-0.5 rounded text-[8.5px] font-black">${w.rule_id || 'RULE-001'}</span>
                                        <span class="text-gray-800 uppercase tracking-wider">DEFISIT ${String(w.label || '').toUpperCase()}</span>
                                    </span>
                                    <span class="text-amber-700 font-mono text-[9.5px]">Gap -${Number(w.diff).toFixed(1)}</span>
                                </div>
                                <div class="text-[10px] text-gray-700 leading-snug">${w.message}</div>
                            </div>
                        `);
                    });
                }

                if (rbrExp.top_two_attributes && rbrExp.top_two_attributes.length >= 2) {
                    const t1 = rbrExp.top_two_attributes[0];
                    const t2 = rbrExp.top_two_attributes[1];
                    items.push(`
                        <div class="bg-white border border-[#F3E8E8] rounded-xl p-2.5 shadow-2xs space-y-1">
                            <div class="flex items-center justify-between text-[10px] font-black">
                                <span class="text-[#700B1A] flex items-center gap-1">
                                    <span class="bg-[#FCECEE] text-[#700B1A] px-1.5 py-0.5 rounded text-[8.5px] font-black">RULE-004</span>
                                    <span class="text-gray-800 uppercase tracking-wider">DRAFT IDENTITY &amp; PLAYSTYLE</span>
                                </span>
                                <span class="text-[#700B1A] font-mono text-[9.5px]">Top 2 Atribut</span>
                            </div>
                            <div class="text-[10px] text-gray-700 leading-snug">
                                <strong>IF:</strong> 2 atribut tertinggi adalah <strong>${t1.label} (${Number(t1.value).toFixed(1)})</strong> &amp; <strong>${t2.label} (${Number(t2.value).toFixed(1)})</strong><br>
                                <strong>THEN:</strong> ${rbrExp.draft_identity} — ${rbrExp.playstyle_note}
                            </div>
                        </div>
                    `);
                }

                if (rbrExp.winning_condition) {
                    items.push(`
                        <div class="bg-white border border-[#F3E8E8] rounded-xl p-2.5 shadow-2xs space-y-1">
                            <div class="flex items-center justify-between text-[10px] font-black">
                                <span class="text-[#700B1A] flex items-center gap-1">
                                    <span class="bg-[#FCECEE] text-[#700B1A] px-1.5 py-0.5 rounded text-[8.5px] font-black">RULE-007</span>
                                    <span class="text-gray-800 uppercase tracking-wider">WINNING CONDITION</span>
                                </span>
                                <span class="text-emerald-700 font-mono text-[9.5px]">Perbandingan Angka</span>
                            </div>
                            <div class="text-[10px] text-gray-700 leading-snug">${rbrExp.winning_condition}</div>
                        </div>
                    `);
                }

                traceContainer.innerHTML = items.join('');
            } else {
                traceContainer.innerHTML = `
                    <div class="p-3 bg-white border border-dashed border-[#F3E8E8] rounded-xl text-center text-gray-400 text-xs">
                        Pilih minimal 1 hero sekutu untuk menampilkan interpretasi strategi berbasis 5 atribut Coach.
                    </div>
                `;
            }
        }

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
                let rbrRationale = 'High Priority Ban berdasarkan kontestasi pro player MPL ID S18.';

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
                let banRateStr = '—';
                if (stat) {
                    const br = parseFloat(stat.ban_rate) || 0;
                    score += (br / 15);
                    banRateStr = br > 0 ? `${br.toFixed(1)}%` : '—';
                }

                if (threatCount === 0) {
                    subHero = banRateStr !== '—' ? `Turnamen Ban (${banRateStr})` : 'Prioritas Turnamen';
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
                    ban_rate: banRateStr,
                    rbr_id: rbrId,
                    rbr_rationale: rbrRationale
                });
            });

            bans = Array.from(banScoreMap.values()).sort((a, b) => parseFloat(b.score) - parseFloat(a.score));
        }

        const bansBadge = document.getElementById('bansCountBadge');
        if (bansBadge) bansBadge.textContent = Math.min(8, bans.length);

        bans.slice(0, 8).forEach(b => {
            const card = document.createElement('div');
            card.className = 'rec-ban-item bg-white border border-[#F3E8E8] hover:border-[#DC2626] rounded-2xl p-3 shadow-2xs hover:shadow-xs transition space-y-1.5 cursor-pointer';
            card.onclick = () => {
                setActiveTeamMode('enemy');
                toggleSelectHero(b.hero);
            };

            const pColor = b.priority === 'P1' ? 'bg-[#DC2626] text-white' : 'bg-red-100 text-red-800';
            const pTitle = b.priority === 'P1' ? 'Prioritas Utama Ban: Ancaman counter langsung atau ban rate tinggi' : 'Prioritas Sekunder Ban';

            const banRoleHtml = (b.role) ? 
                `<span class="text-[9px] text-gray-400 font-medium">${b.role}</span>` : '';

            const banRateHtml = (b.ban_rate && b.ban_rate !== '—') ?
                `<span class="bg-red-50 text-red-700 border border-red-200 text-[8px] font-extrabold px-1.5 py-0.2 rounded" title="Persentase Ban Turnamen MPL ID S18">Ban: ${b.ban_rate}</span>` : '';

            const subHeroText = b.subtitle_hero || '';

            card.innerHTML = `
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded ${pColor}" title="${pTitle}">
                            ${b.priority}
                        </span>
                        <img src="${b.portrait}" 
                             alt="${b.hero}" 
                             class="w-9 h-9 rounded-full object-cover border border-[#F3E8E8] bg-gray-50 flex-shrink-0"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(b.hero)}&background=DC2626&color=fff';">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h4 class="font-black text-xs text-[#18181B] truncate">${b.hero}</h4>
                                ${banRoleHtml}
                                ${banRateHtml}
                            </div>
                            <div class="text-[10px] font-bold text-red-700 truncate mt-0.5">
                                <span title="Jenis Sinyal">${b.subtitle_type || 'THR'}</span> ${subHeroText ? `<span class="font-semibold text-gray-700">${subHeroText}</span>` : ''}
                            </div>
                        </div>
                    </div>

                    <div class="w-24 text-right flex-shrink-0">
                        <div class="font-mono font-black text-xs text-red-700 mb-1" title="Skor Ancaman Ban">
                            ${b.score}
                        </div>
                        <div class="w-full bg-red-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-[#DC2626] h-full rounded-full" style="width: ${b.progress}%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50/70 border border-red-100 rounded-lg p-1.5 text-[9px] text-red-800 font-medium leading-tight">
                    <strong>RBR THREAT:</strong> ${b.rbr_rationale || 'High tournament priority ban berdasarkan kontestasi pro player MPL ID S18.'}
                </div>
            `;
            bansContainer.appendChild(card);
        });

        const confEl = document.getElementById('confidenceScore');
        if (confEl) {
            const conf = 90.0 + (allyTeam.length * 1.2) + (enemyTeam.length * 0.8);
            confEl.textContent = `${Math.min(99.4, conf.toFixed(1))}%`;
        }
    }

    // Modal Panduan Istilah & Legenda RBR
    function openLegendModal() {
        const modal = document.getElementById('legendModal');
        if (modal) {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
        }
    }

    function closeLegendModal() {
        const modal = document.getElementById('legendModal');
        if (modal) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
        }
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

    function toggleRecCard(heroId) {
        if (expandedRecHeroId === heroId) {
            expandedRecHeroId = null;
        } else {
            expandedRecHeroId = heroId;
        }
        renderTacticalAnalysis();
    }

    function selectRecommendedHero(heroName) {
        setActiveTeamMode('ally');
        toggleSelectHero(heroName);
    }

    function resolvePrimaryReasonChip(p, counters, synergies, firedRules, needsMage, needsFrontline) {
        if (needsMage && (p.damage_type === 'Magic' || (p.role && p.role.toLowerCase().includes('mage')))) {
            return { text: 'MAGE WAJIB', class: 'bg-amber-100 text-amber-900 border border-amber-300' };
        }
        if (counters && counters.length > 0) {
            return { text: 'CTR vs ' + counters[0].vs, class: 'bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD]' };
        }
        if (synergies && synergies.length > 0) {
            return { text: 'SYN + ' + synergies[0].dengan, class: 'bg-emerald-50 text-emerald-800 border border-emerald-200' };
        }
        if (firedRules && firedRules.some(r => r.rule_id === 'RBR-COMP-02')) {
            return { text: 'FRONTLINE', class: 'bg-blue-50 text-blue-800 border border-blue-200' };
        }
        if (firedRules && firedRules.some(r => r.rule_id === 'RBR-POW-01')) {
            return { text: 'EARLY TEMPO', class: 'bg-teal-50 text-teal-800 border border-teal-200' };
        }
        if (firedRules && firedRules.some(r => r.rule_id === 'RBR-POW-02')) {
            return { text: 'LATE SCALE', class: 'bg-purple-50 text-purple-800 border border-purple-200' };
        }
        return { text: 'META S18', class: 'bg-gray-100 text-gray-700 border border-gray-200' };
    }

    function renderSynergyBadges(synList) {
        if (!synList || synList.length === 0) {
            return '<span class="text-gray-400 italic text-[9.5px]">Belum ada data sinergi duet di turnamen</span>';
        }
        return synList.map(s => {
            const extra = s.win_rate ? ` (${s.win_rate}${s.picks ? `, ${s.picks}G` : ''})` : '';
            return `
                <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 px-1.5 py-0.5 rounded font-black text-[9px]" title="Sinergi Duet Liquipedia: ${s.source === 'team_dna' ? 'Data Historis Tim' : 'Data Liga MPL S18'}">
                    +${(s.nilai || 12).toFixed(1)} dgn ${s.dengan}${extra}
                </span>
            `;
        }).join(' ');
    }

    function renderCounterBadges(ctrList) {
        if (!ctrList || ctrList.length === 0) {
            return '<span class="text-gray-400 italic text-[9.5px]">Belum ada data head-to-head di turnamen</span>';
        }
        return ctrList.map(c => {
            const extra = c.win_rate ? ` (${c.win_rate}${c.matches ? `, ${c.matches}G` : ''})` : '';
            return `
                <span class="bg-[#FCECEE] text-[#700B1A] border border-[#F8B4BD] px-1.5 py-0.5 rounded font-black text-[9px]" title="Head-to-Head Liquipedia: ${c.source === 'team_h2h' ? 'Data Historis Tim' : (c.source === 'league_h2h' ? 'Data Liga MPL S18' : 'Signature Tim')}">
                    +${(c.nilai || 14).toFixed(1)} vs ${c.vs}${extra}
                </span>
            `;
        }).join(' ');
    }

    function renderFiredRulesList(rules) {
        if (!rules || rules.length === 0) {
            return '<div class="text-[9.5px] text-gray-400 italic">Tidak ada aturan spesifik yang menyala untuk hero ini</div>';
        }
        return rules.map(r => `
            <div class="p-2 bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl text-[9.5px] space-y-1">
                <div class="flex items-center justify-between font-black">
                    <a href="{{ route('rules') }}#${r.rule_id}" target="_blank" class="text-[#700B1A] hover:underline flex items-center gap-1" title="Lihat di Knowledge Base Rules RBR">
                        <span class="bg-[#FCECEE] text-[#700B1A] px-1.5 py-0.2 rounded text-[8.5px] font-black">${r.rule_id}</span>
                        <span class="text-gray-800">${r.nama}</span>
                        <span class="text-[9px] text-gray-400">↗</span>
                    </a>
                    <span class="text-emerald-700 font-mono font-bold">+${(r.kontribusi_skor || 0).toFixed(1)} Poin</span>
                </div>
                <div class="text-gray-600 leading-tight">
                    <strong>Kondisi Aktual:</strong> ${r.kondisi || 'Kondisi premis terpenuhi'}
                </div>
            </div>
        `).join('');
    }

    function renderUnfiredRulesList(rules) {
        if (!rules || rules.length === 0) {
            return '<div class="text-[9px] text-gray-400 italic">Semua aturan relevan telah menyala</div>';
        }
        return rules.slice(0, 4).map(r => `
            <div class="text-[9px] text-gray-500 bg-gray-50 p-1.5 rounded-lg border border-gray-100 flex items-start justify-between gap-1.5">
                <span class="font-bold text-gray-700">[${r.rule_id}] ${r.nama}:</span>
                <span class="italic text-gray-400 text-right">${r.alasan_tidak_menyala || 'Kondisi draft belum terpenuhi'}</span>
            </div>
        `).join('');
    }

    function openInferenceTraceModal() {
        const modal = document.getElementById('inferenceTraceModal');
        if (modal) {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
            updateInferenceModalContent();
        }
    }

    function closeInferenceTraceModal() {
        const modal = document.getElementById('inferenceTraceModal');
        if (modal) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
        }
    }

    function switchTraceTab(tabId) {
        currentTraceTab = tabId;
        const tabs = ['working_memory', 'conflict_set', 'selected_rules', 'score_steps'];
        const btnIds = {
            working_memory: 'traceTabBtn-wm',
            conflict_set: 'traceTabBtn-cs',
            selected_rules: 'traceTabBtn-sr',
            score_steps: 'traceTabBtn-st'
        };

        tabs.forEach(t => {
            const contentEl = document.getElementById(`traceContent-${t}`);
            const btnEl = document.getElementById(btnIds[t]);
            if (contentEl) {
                if (t === tabId) {
                    contentEl.classList.remove('hidden');
                } else {
                    contentEl.classList.add('hidden');
                }
            }
            if (btnEl) {
                if (t === tabId) {
                    btnEl.className = 'trace-tab-btn px-3 py-1 rounded-full text-xs font-black uppercase transition bg-[#700B1A] text-white shadow-2xs';
                } else {
                    btnEl.className = 'trace-tab-btn px-3 py-1 rounded-full text-xs font-bold uppercase transition bg-white text-gray-600 border border-[#F3E8E8] hover:bg-[#FAF8F8]';
                }
            }
        });
    }

    function updateInferenceModalContent() {
        const wm = (latestInferenceData && latestInferenceData.working_memory) ? latestInferenceData.working_memory : {
            ally_count: allyTeam.length,
            enemy_count: enemyTeam.length,
            ally_heroes: allyTeam.map((h, i) => ({ name: h, role: 'Hero', lane: allyLanes[i] || 'EXP' })),
            enemy_heroes: enemyTeam.map((h, i) => ({ name: h, role: 'Hero', lane: enemyLanes[i] || 'EXP' })),
            ally_damage: computeTeamDamage(allyTeam),
            enemy_damage: computeTeamDamage(enemyTeam),
            ally_power_spikes: computeTeamPowerSpikes(allyTeam),
            enemy_power_spikes: computeTeamPowerSpikes(enemyTeam),
            ally_lanes_filled: allyLanes,
            enemy_lanes_filled: enemyLanes,
            ally_has_frontline: allyTeam.length > 0,
            ally_has_cc: allyTeam.length > 0,
            magic_deficit: false,
            frontline_deficit: false,
            excluded_heroes: [...allyTeam, ...enemyTeam],
            map_variant: 'Standar'
        };

        // 1. Working Memory Tab
        const wmAllyBadge = document.getElementById('wmAllyCountBadge');
        if (wmAllyBadge) wmAllyBadge.textContent = `${wm.ally_count}/5 Terpilih`;
        const wmEnemyBadge = document.getElementById('wmEnemyCountBadge');
        if (wmEnemyBadge) wmEnemyBadge.textContent = `${wm.enemy_count}/5 Terpilih`;

        const wmAllyList = document.getElementById('wmAllyList');
        if (wmAllyList) {
            if (!wm.ally_heroes || wm.ally_heroes.length === 0) {
                wmAllyList.innerHTML = '<span class="text-gray-400 italic">Belum ada hero sekutu terpilih</span>';
            } else {
                wmAllyList.innerHTML = wm.ally_heroes.map(h => `
                    <div class="flex items-center justify-between py-1 border-b border-gray-50">
                        <span class="font-black text-[#18181B]">${h.name}</span>
                        <span class="text-gray-500 font-mono text-[9.5px]">${h.lane || 'Flex'} • ${h.role || 'Hero'}</span>
                    </div>
                `).join('');
            }
        }

        const wmEnemyList = document.getElementById('wmEnemyList');
        if (wmEnemyList) {
            if (!wm.enemy_heroes || wm.enemy_heroes.length === 0) {
                wmEnemyList.innerHTML = '<span class="text-gray-400 italic">Belum ada hero musuh terpilih</span>';
            } else {
                wmEnemyList.innerHTML = wm.enemy_heroes.map(h => `
                    <div class="flex items-center justify-between py-1 border-b border-gray-50">
                        <span class="font-black text-red-700">${h.name}</span>
                        <span class="text-gray-500 font-mono text-[9.5px]">${h.lane || 'Flex'} • ${h.role || 'Hero'}</span>
                    </div>
                `).join('');
            }
        }

        const wmAllyDmg = document.getElementById('wmAllyDamageRatio');
        if (wmAllyDmg) wmAllyDmg.textContent = `Phys ${wm.ally_damage?.phys || 0}% / Mag ${wm.ally_damage?.mag || 0}%`;
        const wmEnemyDmg = document.getElementById('wmEnemyDamageRatio');
        if (wmEnemyDmg) wmEnemyDmg.textContent = `Phys ${wm.enemy_damage?.phys || 0}% / Mag ${wm.enemy_damage?.mag || 0}%`;

        const wmAllySpikes = document.getElementById('wmAllySpikes');
        if (wmAllySpikes) {
            wmAllySpikes.textContent = (allyTeam.length === 0) ? 'Belum ada data' : `Early: ${wm.ally_power_spikes?.early || 0} | Mid: ${wm.ally_power_spikes?.mid || 0} | Late: ${wm.ally_power_spikes?.late || 0}`;
        }
        const wmEnemySpikes = document.getElementById('wmEnemySpikes');
        if (wmEnemySpikes) {
            wmEnemySpikes.textContent = (enemyTeam.length === 0) ? 'Belum ada data' : `Early: ${wm.enemy_power_spikes?.early || 0} | Mid: ${wm.enemy_power_spikes?.mid || 0} | Late: ${wm.enemy_power_spikes?.late || 0}`;
        }

        const wmFactsGrid = document.getElementById('wmFactsGrid');
        if (wmFactsGrid) {
            wmFactsGrid.innerHTML = `
                <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="text-gray-400 text-[9px] font-black uppercase">STATUS MAGIC</div>
                    <div class="font-black text-[11px] mt-0.5 ${wm.magic_deficit ? 'text-amber-700' : 'text-emerald-700'}">
                        ${wm.magic_deficit ? 'Defisit (Wajib Mage)' : 'Aman (Tercukupi)'}
                    </div>
                </div>
                <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="text-gray-400 text-[9px] font-black uppercase">STATUS FRONTLINE</div>
                    <div class="font-black text-[11px] mt-0.5 ${wm.frontline_deficit ? 'text-amber-700' : 'text-emerald-700'}">
                        ${wm.frontline_deficit ? 'Defisit (Perlu Tank)' : 'Tersedia'}
                    </div>
                </div>
                <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="text-gray-400 text-[9px] font-black uppercase">INISIATOR CC</div>
                    <div class="font-black text-[11px] mt-0.5 ${wm.ally_has_cc ? 'text-emerald-700' : 'text-gray-600'}">
                        ${wm.ally_has_cc ? 'Tersedia' : 'Belum Ada'}
                    </div>
                </div>
                <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="text-gray-400 text-[9px] font-black uppercase">HERO TEREKSKLUSI</div>
                    <div class="font-black text-[11px] text-[#700B1A] mt-0.5">
                        ${(wm.excluded_heroes || []).length} Hero
                    </div>
                </div>
            `;
        }

        // 2. Conflict Set Tab
        const ruleEvals = (latestInferenceData && latestInferenceData.rule_evaluations) ? latestInferenceData.rule_evaluations : {};
        const conflictTbody = document.getElementById('conflictSetTableBody');
        if (conflictTbody) {
            const ruleEntries = Object.values(ruleEvals);
            const countBadge = document.getElementById('conflictSetCountBadge');
            if (countBadge) countBadge.textContent = `${ruleEntries.length} Rule Dievaluasi`;

            if (ruleEntries.length === 0) {
                conflictTbody.innerHTML = '<tr><td colspan="4" class="p-3 text-center text-gray-400">Tidak ada aturan yang dievaluasi</td></tr>';
            } else {
                conflictTbody.innerHTML = ruleEntries.map(r => `
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="py-2.5 px-3">
                            <div class="font-black text-[#18181B] flex items-center gap-1.5">
                                <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-[#700B1A] px-1.5 py-0.2 rounded text-[9px] font-black">${r.rule_id}</span>
                                <span>${r.nama}</span>
                            </div>
                        </td>
                        <td class="py-2.5 px-3 text-gray-600">
                            ${r.kondisi}
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            ${r.fired ? 
                                '<span class="bg-emerald-100 text-emerald-800 border border-emerald-200 text-[9px] font-black px-2 py-0.5 rounded-full">AKTIF</span>' : 
                                '<span class="bg-gray-100 text-gray-500 text-[9px] font-semibold px-2 py-0.5 rounded-full">TIDAK MENYALA</span>'}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-gray-500 text-[10px]">
                            Explanation Layer
                        </td>
                    </tr>
                `).join('');
            }
        }

        // 3. Selected Rules Tab
        const selectedList = document.getElementById('selectedRulesList');
        if (selectedList) {
            const firedList = Object.values(ruleEvals).filter(r => r.fired);
            if (firedList.length === 0) {
                selectedList.innerHTML = '<div class="p-3 bg-white rounded-xl border border-dashed border-[#F3E8E8] text-center text-gray-400">Belum ada aturan aktif pada kondisi draft saat ini.</div>';
            } else {
                selectedList.innerHTML = firedList.map((r, i) => `
                    <div class="p-3 bg-white rounded-xl border border-[#F3E8E8] shadow-2xs space-y-1">
                        <div class="flex items-center justify-between font-black">
                            <span class="text-[#700B1A] flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-[#700B1A] text-white flex items-center justify-center text-[10px]">${i + 1}</span>
                                <span>[${r.rule_id}] ${r.nama}</span>
                            </span>
                            <span class="text-emerald-700 font-mono font-bold text-[10px]">Interpretasi Aktif</span>
                        </div>
                        <div class="text-[10px] text-gray-600 pl-6.5">
                            <strong>Kondisi Aktual:</strong> ${r.kondisi}
                        </div>
                    </div>
                `).join('');
            }
        }
    }

    function resetDraftStateToInitial() {
        allyTeam = [];
        enemyTeam = [];
        allyLanes = [];
        enemyLanes = [];
        allyBans = [];
        enemyBans = [];
        activeTarget = { team: 'ally', index: 0, type: 'pick' };
        activeTeamMode = 'ally';
        updateTeamModeUI();
        expandedRecHeroId = null;
        currentRecommendations = (initialInferenceData && initialInferenceData.saw_recommendations) ? initialInferenceData.saw_recommendations : @json($defaultPicks ?? []);
        latestInferenceData = initialInferenceData;
        renderSlots();
        renderBanSlots();
        renderPickerGrid();
        updateDamageAndPowerSpikes();
        renderTacticalAnalysis();
        updateInferenceModalContent();
        renderSimilarMatches(latestInferenceData?.similar_matches || []);
    }

    function resetDraft() {
        resetDraftStateToInitial();
        updateDraftLockState();
        if (selectedTeamName) {
            syncServerDraft();
        }
        showToast('Draft berhasil direset.');
    }

    function syncServerDraft() {
        const loadingBar = document.getElementById('recLoadingBar');
        if (loadingBar) {
            loadingBar.classList.remove('opacity-0');
            loadingBar.classList.add('opacity-100', 'animate-pulse');
        }

        const allBans = [...allyBans, ...enemyBans];

        fetch('/api/analyze-draft', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                my_team: allyTeam,
                enemy_team: enemyTeam,
                my_lanes: allyLanes,
                enemy_lanes: enemyLanes,
                banned: allBans,
                team_name: selectedTeamName
            })
        }).then(res => res.json()).then(data => {
            if (data && (data.saw_recommendations || data.recommendations)) {
                currentRecommendations = data.saw_recommendations || data.recommendations;
                latestInferenceData = data.inference || data;
                if (data.needed_lanes) {
                    latestInferenceData.needed_lanes = data.needed_lanes;
                }
                if (data.filled_lanes) {
                    latestInferenceData.filled_lanes = data.filled_lanes;
                }
                if (data.rbr_explanation && !latestInferenceData.rbr_explanation) {
                    latestInferenceData.rbr_explanation = data.rbr_explanation;
                }
                if (data.saw_meta && !latestInferenceData.saw_meta) {
                    latestInferenceData.saw_meta = data.saw_meta;
                }
            }
            if (data && data.team_dna) {
                teamsDnaMap[data.team_dna.team_name.toLowerCase()] = data.team_dna;
                if (selectedTeamName && selectedTeamName.toLowerCase() === data.team_dna.team_name.toLowerCase()) {
                    renderTeamDnaProfileCard(data.team_dna);
                }
            }
            renderTacticalAnalysis();
            updateInferenceModalContent();
        }).catch(err => {
            console.warn('Sync offline fallback active:', err);
        }).finally(() => {
            if (loadingBar) {
                loadingBar.classList.remove('opacity-100', 'animate-pulse');
                loadingBar.classList.add('opacity-0');
            }
        });
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

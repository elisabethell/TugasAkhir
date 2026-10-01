@extends('layouts.app')

@section('title', 'MetaScout: Land of Dawn - Rekomendasi Draft & Analisis Gameplay RBR')

@section('content')
<div class="space-y-8">
    
    <!-- HEADER HERO SECTION -->
    <div class="card-custom p-6 sm:p-8 bg-gradient-to-r from-[#111827] via-[#1e1b4b] to-[#0f172a] border border-gray-800 shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-950/80 border border-cyan-500/30 text-cyan-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <span>⚡ Expert System • Rule-Based Reasoning (RBR)</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                MetaScout: <span class="bg-gradient-to-r from-[#00f2ff] via-[#38bdf8] to-[#818cf8] bg-clip-text text-transparent">Land of Dawn</span>
            </h1>
            <p class="text-gray-300 mt-2 text-sm sm:text-base leading-relaxed">
                Rekomendasi draft hero & analisis gameplay taktis Mobile Legends (Power Spike, Playstyle, & Winning Conditions) berbasis 
                analisis data turnamen resmi pro <span class="text-cyan-400 font-semibold">MWI X EWC 2026</span> dan mesin inferensi aturan cerdas.
            </p>
            <div class="mt-4 flex flex-wrap gap-2 text-xs">
                <span class="bg-gray-800/80 text-gray-300 px-3 py-1 rounded-lg border border-gray-700">🔓 Bebas Digunakan (Tanpa Login)</span>
                <span class="bg-gray-800/80 text-gray-300 px-3 py-1 rounded-lg border border-gray-700">🎯 Formasi 5 Role Kompetitif</span>
                <span class="bg-gray-800/80 text-gray-300 px-3 py-1 rounded-lg border border-gray-700">🗺️ 4 Varian Map Dinamis</span>
            </div>
        </div>
        <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10 bg-gradient-to-l from-cyan-500 to-transparent pointer-events-none hidden md:block"></div>
    </div>

    <!-- QUICK PRESETS BAR -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">⚡ Preset Cepat:</span>
            <button type="button" onclick="loadPreset('pickoff')" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg text-xs font-medium text-cyan-300 transition">
                🎯 Pick-Off Ambush
            </button>
            <button type="button" onclick="loadPreset('teamfight')" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg text-xs font-medium text-purple-300 transition">
                💥 Teamfight AoE Wiping
            </button>
            <button type="button" onclick="loadPreset('lategame')" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg text-xs font-medium text-amber-300 transition">
                🛡️ UBE / Protect Carry (Late Scaling)
            </button>
            <button type="button" onclick="loadPreset('splitpush')" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-lg text-xs font-medium text-emerald-300 transition">
                🏃 Split Push Macro
            </button>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="clearDraft()" class="px-3 py-1.5 bg-red-950/40 hover:bg-red-900/60 border border-red-800/50 rounded-lg text-xs font-medium text-red-300 transition">
                🗑️ Kosongkan Draft
            </button>
        </div>
    </div>

    <!-- IN-GAME STYLE DRAFT PICKER INTERFACE -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- TIM KITA (BLUE SIDE) - 5 SLOTS -->
        <div class="lg:col-span-12 card-custom p-6 border-cyan-500/20 shadow-lg">
            <div class="flex justify-between items-center mb-5 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-cyan-400 shadow-[0_0_10px_#00f2ff]"></span>
                    <h2 class="text-lg font-bold text-white tracking-wide">
                        Komposisi Tim Kita <span class="text-xs text-cyan-400 font-normal ml-2">(Pilih 5 Hero Sesuai Role Lane)</span>
                    </h2>
                </div>

                <!-- SELECTOR VARIAN MAP -->
                <div class="flex items-center gap-2 bg-gray-900 px-3 py-1.5 rounded-lg border border-gray-800 text-xs">
                    <span class="text-gray-400 font-medium">🗺️ Varian Map:</span>
                    <select id="map-select" onchange="runLiveAnalysis()" class="bg-gray-800 border-none text-white rounded px-2 py-1 text-xs focus:ring-1 focus:ring-cyan-500">
                        @foreach($maps as $mapName => $mapImg)
                            <option value="{{ $mapName }}" {{ $defaultMap === $mapName ? 'selected' : '' }}>
                                {{ $mapName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 5 SLOTS HERO GRID (IN-GAME STYLE) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5">
                @php
                    $lanes = [
                        'exp'    => ['name' => 'EXP Lane', 'role' => 'Fighter / Durability', 'color' => 'emerald'],
                        'jungle' => ['name' => 'Jungler', 'role' => 'Assassin / Retribution', 'color' => 'amber'],
                        'mid'    => ['name' => 'Mid Lane', 'role' => 'Mage / AoE Burst', 'color' => 'purple'],
                        'gold'   => ['name' => 'Gold Lane', 'role' => 'Marksman / DPS Late', 'color' => 'yellow'],
                        'roam'   => ['name' => 'Roamer', 'role' => 'Tank / Crowd Control', 'color' => 'cyan'],
                    ];
                @endphp

                @foreach($lanes as $laneKey => $info)
                    @php
                        $heroName = $defaultMyTeam[$laneKey] ?? null;
                        $normName = $heroName ? preg_replace('/[^a-z0-9]/', '', strtolower($heroName)) : null;
                        $portrait = $normName ? ($heroPortraits[$normName] ?? asset('images/heroes/' . $normName . '.png')) : null;
                    @endphp

                    <div class="group relative bg-[#0f141c] hover:bg-[#161d29] border border-gray-800 hover:border-cyan-500/50 rounded-xl p-3.5 flex flex-col items-center justify-between text-center transition cursor-pointer"
                         onclick="openHeroPicker('my', '{{ $laneKey }}')" id="slot-card-my-{{ $laneKey }}">
                        
                        <div class="w-full flex justify-between items-center text-[11px] mb-2 font-bold uppercase tracking-wider text-gray-400">
                            <span>{{ $info['name'] }}</span>
                            <span class="text-[10px] text-gray-500 font-normal">Ganti ↻</span>
                        </div>

                        <!-- HERO PORTRAIT AVATAR -->
                        <div class="relative my-2">
                            <img id="avatar-my-{{ $laneKey }}"
                                 src="{{ $portrait ?? 'https://via.placeholder.com/64?text=' . strtoupper($laneKey) }}" 
                                 class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-2 border-cyan-400/40 shadow-lg group-hover:scale-105 transition"
                                 alt="{{ $heroName ?? 'Pilih Hero' }}"
                                 onerror="this.src='https://via.placeholder.com/64?text=ML'">
                            <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full bg-cyan-500 border-2 border-[#0B0E14]"></span>
                        </div>

                        <!-- HERO NAME & CLASS -->
                        <div class="mt-1 w-full">
                            <h3 id="name-my-{{ $laneKey }}" class="text-sm font-bold text-white truncate">
                                {{ $heroName ?? 'Pilih Hero' }}
                            </h3>
                            <p id="desc-my-{{ $laneKey }}" class="text-[10px] text-gray-400 truncate mt-0.5">
                                {{ $info['role'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- TOGGLE LAWAN (OPSIONAL) -->
            <div class="mt-5 pt-4 border-t border-gray-800 flex justify-between items-center flex-wrap gap-3">
                <button type="button" onclick="toggleEnemySection()" class="text-xs font-semibold text-gray-400 hover:text-cyan-400 transition flex items-center gap-1.5">
                    <span id="enemy-toggle-icon">▶</span>
                    <span>Tambah Draft Hero Lawan (Opsional - Analisis Counter Matchup)</span>
                </button>
                <button type="button" onclick="runLiveAnalysis()" class="px-5 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-black font-extrabold rounded-lg text-xs tracking-wider uppercase transition shadow-[0_0_15px_rgba(0,242,255,0.3)]">
                    🚀 Jalankan Analisis RBR
                </button>
            </div>

            <!-- ENEMY DRAFT SLOTS (COLLAPSIBLE) -->
            <div id="enemy-section" class="hidden mt-4 pt-4 border-t border-gray-800/50">
                <p class="text-xs text-red-400 font-bold mb-3 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Tim Lawan (Red Side)
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                    @foreach($lanes as $laneKey => $info)
                        @php
                            $enemyHero = $defaultEnemyTeam[$laneKey] ?? null;
                            $enNorm = $enemyHero ? preg_replace('/[^a-z0-9]/', '', strtolower($enemyHero)) : null;
                            $enPortrait = $enNorm ? ($heroPortraits[$enNorm] ?? asset('images/heroes/' . $enNorm . '.png')) : null;
                        @endphp
                        <div class="bg-[#121015] border border-red-950/60 hover:border-red-500/50 rounded-lg p-2.5 flex items-center gap-3 cursor-pointer transition"
                             onclick="openHeroPicker('enemy', '{{ $laneKey }}')" id="slot-card-enemy-{{ $laneKey }}">
                            <img id="avatar-enemy-{{ $laneKey }}"
                                 src="{{ $enPortrait ?? 'https://via.placeholder.com/40?text=EN' }}" 
                                 class="w-10 h-10 rounded-full object-cover border border-red-400/40"
                                 onerror="this.src='https://via.placeholder.com/40?text=EN'">
                            <div class="min-w-0 flex-1">
                                <span class="text-[9px] font-bold text-red-400 block uppercase">{{ $info['name'] }}</span>
                                <h4 id="name-enemy-{{ $laneKey }}" class="text-xs font-bold text-gray-200 truncate">
                                    {{ $enemyHero ?? 'Kosong' }}
                                </h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

    <!-- HASIL ANALISIS RBR (DASHBOARD GAMEPLAY & WINNING CONDITION) -->
    <div id="analysis-container" class="space-y-6">

        <!-- 1. BANNER ARCHETYPE & POWER SPIKE -->
        <div class="card-custom p-6 bg-gradient-to-r from-[#131b2e] to-[#111827] border-cyan-500/30">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                
                <div class="md:col-span-7 space-y-2">
                    <span class="text-[11px] font-bold text-cyan-400 tracking-wider uppercase">Klasifikasi Gaya Bermain (RBR Archetype)</span>
                    <h2 id="res-archetype" class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-3">
                        {{ $initialAnalysis['archetype'] }}
                    </h2>
                    <p class="text-xs text-gray-400">
                        Ditentukan melalui evaluasi Forward Chaining aturan atribut kelas, specialty burst/CC, dan mobilitas sidelaner.
                    </p>
                </div>

                <div class="md:col-span-5 flex md:justify-end gap-3 flex-wrap">
                    <div class="bg-gray-900/80 px-4 py-2.5 rounded-xl border border-gray-800 text-right">
                        <span class="text-[10px] text-gray-400 block uppercase font-bold">Fase Puncak (Power Spike)</span>
                        <span id="res-spike-focus" class="text-sm font-extrabold text-amber-400">
                            {{ $initialAnalysis['power_spike_focus'] }}
                        </span>
                    </div>

                    <div class="bg-gray-900/80 px-4 py-2.5 rounded-xl border border-gray-800 text-right">
                        <span class="text-[10px] text-gray-400 block uppercase font-bold">Rasio Damage Fisik / Sihir</span>
                        <span id="res-damage-ratio" class="text-sm font-extrabold text-cyan-300">
                            {{ $initialAnalysis['damage_ratio']['physical_count'] }} Physical • {{ $initialAnalysis['damage_ratio']['magic_count'] }} Magic
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- 2. PANDUAN GAMEPLAY & WINNING CONDITIONS (KONDISI KEMENANGAN) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- KOLOM KIRI: WINNING CONDITIONS & TAKTIK OBJEKTIF -->
            <div class="lg:col-span-7 card-custom p-6 border-indigo-500/20 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>🏆 Kondisi Kemenangan & Panduan Taktis (Winning Conditions)</span>
                    </h3>
                    <span class="text-[10px] bg-indigo-950 text-indigo-300 px-2.5 py-0.5 rounded font-semibold border border-indigo-800">
                        Pedoman Gameplay
                    </span>
                </div>

                <!-- DAFTAR KONDISI KEMENANGAN RBR -->
                <div id="res-winning-conditions" class="space-y-3 text-sm">
                    @foreach($initialAnalysis['winning_conditions'] as $wc)
                        <div class="p-3.5 rounded-xl bg-gray-900/60 border border-gray-800/80 flex items-start gap-3">
                            <span class="text-cyan-400 text-lg leading-none mt-0.5">📌</span>
                            <span class="text-gray-200 leading-relaxed font-medium">{{ $wc }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- MAP SYNERGY STRATEGY -->
                <div id="res-map-synergy-box" class="p-4 rounded-xl bg-cyan-950/20 border border-cyan-800/40 text-xs">
                    <div class="flex items-center gap-2 text-cyan-400 font-bold mb-1">
                        <span>🗺️ Taktik Adaptasi Map:</span>
                    </div>
                    <p id="res-map-synergy" class="text-gray-300 leading-relaxed">
                        {{ $initialAnalysis['map_synergy'] ?? 'Tidak ada aturan map spesifik.' }}
                    </p>
                </div>

                <!-- PERINGATAN / KELEMAHAN DRAFT (WARNINGS) -->
                <div id="res-warnings-box" class="space-y-2 {{ empty($initialAnalysis['warnings']) ? 'hidden' : '' }}">
                    <span class="text-xs font-bold text-red-400 uppercase tracking-wider block">⚠️ Evaluasi Kelemahan Draft:</span>
                    <div id="res-warnings" class="space-y-2 text-xs">
                        @foreach($initialAnalysis['warnings'] as $w)
                            <div class="p-2.5 bg-red-950/30 border border-red-800/50 rounded-lg text-red-300 flex items-center gap-2 font-medium">
                                <span>⚠️</span>
                                <span>{{ $w['message'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: POWER SPIKE TIMELINE & RATINGS RADAR -->
            <div class="lg:col-span-5 card-custom p-6 space-y-6">
                
                <!-- POWER SPIKE CURVE TIMELINE -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center justify-between">
                        <span>📈 Kurva Kekuatan (Power Spike Curve)</span>
                        <span class="text-[10px] text-gray-400 font-normal">0 - 15+ Menit</span>
                    </h4>
                    
                    <div class="space-y-3 text-xs">
                        <!-- Early Game -->
                        <div>
                            <div class="flex justify-between text-gray-300 mb-1 font-semibold">
                                <span>Early Game (0 - 5 Menit)</span>
                                <span id="val-spike-early">{{ $initialAnalysis['power_spike_scores']['early'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-800 rounded-full h-2 overflow-hidden">
                                <div id="bar-spike-early" class="bg-emerald-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $initialAnalysis['power_spike_scores']['early'] }}%;"></div>
                            </div>
                        </div>

                        <!-- Mid Game -->
                        <div>
                            <div class="flex justify-between text-gray-300 mb-1 font-semibold">
                                <span>Mid Game (5 - 12 Menit)</span>
                                <span id="val-spike-mid">{{ $initialAnalysis['power_spike_scores']['mid'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-800 rounded-full h-2 overflow-hidden">
                                <div id="bar-spike-mid" class="bg-amber-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $initialAnalysis['power_spike_scores']['mid'] }}%;"></div>
                            </div>
                        </div>

                        <!-- Late Game -->
                        <div>
                            <div class="flex justify-between text-gray-300 mb-1 font-semibold">
                                <span>Late Game (12+ Menit)</span>
                                <span id="val-spike-late">{{ $initialAnalysis['power_spike_scores']['late'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-800 rounded-full h-2 overflow-hidden">
                                <div id="bar-spike-late" class="bg-indigo-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $initialAnalysis['power_spike_scores']['late'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KOMPETENSI KOMPOSISI TIM (0 - 100) -->
                <div class="pt-4 border-t border-gray-800">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3">
                        📊 Indeks Efektivitas Komposisi
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-gray-900/60 p-2.5 rounded-lg border border-gray-800">
                            <span class="text-gray-400 block text-[10px]">Teamfight (AoE)</span>
                            <span id="score-teamfight" class="text-sm font-bold text-purple-400">{{ $initialAnalysis['ratings']['teamfight'] }}/100</span>
                        </div>
                        <div class="bg-gray-900/60 p-2.5 rounded-lg border border-gray-800">
                            <span class="text-gray-400 block text-[10px]">Pick-Off (Culik)</span>
                            <span id="score-pickoff" class="text-sm font-bold text-cyan-400">{{ $initialAnalysis['ratings']['pick_off'] }}/100</span>
                        </div>
                        <div class="bg-gray-900/60 p-2.5 rounded-lg border border-gray-800">
                            <span class="text-gray-400 block text-[10px]">Split Push & Objektif</span>
                            <span id="score-splitpush" class="text-sm font-bold text-emerald-400">{{ $initialAnalysis['ratings']['split_push'] }}/100</span>
                        </div>
                        <div class="bg-gray-900/60 p-2.5 rounded-lg border border-gray-800">
                            <span class="text-gray-400 block text-[10px]">Poke & Pengepungan</span>
                            <span id="score-poke" class="text-sm font-bold text-yellow-400">{{ $initialAnalysis['ratings']['poke_siege'] }}/100</span>
                        </div>
                        <div class="bg-gray-900/60 p-2.5 rounded-lg border border-gray-800">
                            <span class="text-gray-400 block text-[10px]">Frontline / Daya Tahan</span>
                            <span id="score-frontline" class="text-sm font-bold text-blue-400">{{ $initialAnalysis['ratings']['frontline'] }}/100</span>
                        </div>
                        <div class="bg-gray-900/60 p-2.5 rounded-lg border border-gray-800">
                            <span class="text-gray-400 block text-[10px]">Crowd Control (Stun)</span>
                            <span id="score-cc" class="text-sm font-bold text-pink-400">{{ $initialAnalysis['ratings']['cc'] }}/100</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- 3. TRANSPARANSI ATURAN RBR (FORWARD CHAINING EXPLAINABILITY) -->
        <div class="card-custom p-6 border-gray-800">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>🧠 Rekam Jejak Aturan RBR (Inference Engine Trace)</span>
                    </h3>
                    <p class="text-xs text-gray-400">Aturan berbasis pengetahuan (Knowledge Base) yang terpicu untuk menghasilkan kesimpulan ini.</p>
                </div>
                <a href="{{ route('rules') }}" class="text-xs text-cyan-400 hover:underline">Lihat Semua Aturan di Database &raquo;</a>
            </div>

            <div id="res-fired-rules" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                @foreach($initialAnalysis['fired_rules'] as $rule)
                    <div class="p-3 bg-gray-900/70 border border-gray-800 rounded-lg space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-cyan-400 font-mono text-[11px]">{{ $rule['rule_id'] }}: {{ $rule['title'] }}</span>
                            <span class="text-[10px] text-emerald-400 font-semibold">Aktif ●</span>
                        </div>
                        <p class="text-gray-400"><strong class="text-gray-300">IF:</strong> {{ $rule['condition'] }}</p>
                        <p class="text-gray-300"><strong class="text-cyan-400">THEN:</strong> {{ $rule['action'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

<!-- HERO PICKER MODAL -->
<div id="hero-picker-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="card-custom bg-[#0f141c] border border-gray-700 w-full max-w-4xl max-h-[90vh] flex flex-col rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        
        <!-- MODAL HEADER -->
        <div class="p-5 border-b border-gray-800 flex justify-between items-center bg-[#151b26]">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>Pilih Hero untuk Slot:</span>
                    <span id="modal-slot-title" class="text-cyan-400 uppercase">EXP Lane</span>
                </h3>
                <p class="text-xs text-gray-400">Pilih hero yang sesuai untuk melengkapi komposisi draft.</p>
            </div>
            <button type="button" onclick="closeHeroPicker()" class="text-gray-400 hover:text-white text-2xl font-bold px-2">&times;</button>
        </div>

        <!-- SEARCH & ROLE FILTER TABS -->
        <div class="p-4 bg-[#111722] border-b border-gray-800 flex flex-wrap gap-3 items-center justify-between">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" id="hero-search-input" onkeyup="filterHeroesGrid()" placeholder="Cari nama hero (misal: Claude, Fanny, Chou)..."
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3.5 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500">
            </div>
            <div class="flex gap-1.5 flex-wrap text-xs" id="lane-filter-buttons">
                <button type="button" onclick="setModalLaneFilter('all')" class="lane-filter-btn active px-3 py-1.5 rounded-lg bg-cyan-600 text-white font-semibold">Semua</button>
                <button type="button" onclick="setModalLaneFilter('exp')" class="lane-filter-btn px-3 py-1.5 rounded-lg bg-gray-800 text-gray-300 hover:bg-gray-700">EXP</button>
                <button type="button" onclick="setModalLaneFilter('jungle')" class="lane-filter-btn px-3 py-1.5 rounded-lg bg-gray-800 text-gray-300 hover:bg-gray-700">Jungle</button>
                <button type="button" onclick="setModalLaneFilter('mid')" class="lane-filter-btn px-3 py-1.5 rounded-lg bg-gray-800 text-gray-300 hover:bg-gray-700">Mid</button>
                <button type="button" onclick="setModalLaneFilter('gold')" class="lane-filter-btn px-3 py-1.5 rounded-lg bg-gray-800 text-gray-300 hover:bg-gray-700">Gold</button>
                <button type="button" onclick="setModalLaneFilter('roam')" class="lane-filter-btn px-3 py-1.5 rounded-lg bg-gray-800 text-gray-300 hover:bg-gray-700">Roam</button>
            </div>
        </div>

        <!-- HEROES GRID -->
        <div class="p-5 overflow-y-auto max-h-[60vh] grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3" id="modal-heroes-grid">
            @foreach($heroes as $hero)
                @php
                    $cleanName = preg_replace('/[^a-z0-9]/', '', strtolower($hero->hero_name));
                    $pUrl = $heroPortraits[$cleanName] ?? asset('images/heroes/' . $cleanName . '.png');
                @endphp
                <div class="hero-item-card bg-[#161d2a] hover:bg-cyan-950/60 border border-gray-800 hover:border-cyan-500 rounded-xl p-2.5 flex flex-col items-center text-center cursor-pointer transition group"
                     data-name="{{ strtolower($hero->hero_name) }}"
                     data-lane="{{ strtolower($hero->laning ?? '') }}"
                     data-class="{{ strtolower($hero->class ?? '') }}"
                     onclick="selectHeroForActiveSlot('{{ $hero->hero_name }}', '{{ $pUrl }}', '{{ $hero->class }}')">
                    
                    <img src="{{ $pUrl }}" 
                         class="w-14 h-14 rounded-full object-cover border border-gray-700 group-hover:scale-105 transition"
                         alt="{{ $hero->hero_name }}"
                         onerror="this.src='https://via.placeholder.com/56?text=ML'">
                         
                    <span class="text-xs font-bold text-gray-200 mt-2 truncate w-full group-hover:text-cyan-300">{{ $hero->hero_name }}</span>
                    <span class="text-[9px] text-gray-500 truncate w-full">{{ $hero->class }}</span>
                </div>
            @endforeach
        </div>

    </div>
</div>

@push('scripts')
<script>
    // State Draft saat ini
    const currentDraft = {
        my: { ...@json($defaultMyTeam) },
        enemy: { ...@json($defaultEnemyTeam) },
        map: '{{ $defaultMap }}'
    };

    let activeModalTarget = { side: 'my', lane: 'exp' };
    let currentLaneFilter = 'all';

    // Buka Modal Picker
    function openHeroPicker(side, lane) {
        activeModalTarget = { side, lane };
        document.getElementById('modal-slot-title').innerText = `${side === 'my' ? 'Tim Kita' : 'Tim Lawan'} • ${lane.toUpperCase()} Lane`;
        
        // Sesuaikan filter default dengan lane yang diklik
        setModalLaneFilter(lane);

        document.getElementById('hero-picker-modal').classList.remove('hidden');
        document.getElementById('hero-picker-modal').classList.add('flex');
        document.getElementById('hero-search-input').value = '';
        document.getElementById('hero-search-input').focus();
    }

    function closeHeroPicker() {
        document.getElementById('hero-picker-modal').classList.add('hidden');
        document.getElementById('hero-picker-modal').classList.remove('flex');
    }

    // Filter Hero di Modal
    function setModalLaneFilter(lane) {
        currentLaneFilter = lane;
        const buttons = document.querySelectorAll('.lane-filter-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-cyan-600', 'text-white');
            btn.classList.add('bg-gray-800', 'text-gray-300');
        });
        
        // Temukan button yang cocok
        buttons.forEach(btn => {
            if (btn.innerText.toLowerCase().includes(lane) || (lane === 'all' && btn.innerText.toLowerCase().includes('semua'))) {
                btn.classList.add('bg-cyan-600', 'text-white');
                btn.classList.remove('bg-gray-800', 'text-gray-300');
            }
        });

        filterHeroesGrid();
    }

    function filterHeroesGrid() {
        const query = document.getElementById('hero-search-input').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.hero-item-card');

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const lane = card.getAttribute('data-lane');

            const matchQuery = !query || name.includes(query);
            const matchLane = currentLaneFilter === 'all' || lane.includes(currentLaneFilter);

            if (matchQuery && matchLane) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Pilih Hero ke Slot Aktif
    function selectHeroForActiveSlot(heroName, portraitUrl, heroClass) {
        const { side, lane } = activeModalTarget;
        currentDraft[side][lane] = heroName;

        // Update Tampilan Slot
        const avatarEl = document.getElementById(`avatar-${side}-${lane}`);
        const nameEl = document.getElementById(`name-${side}-${lane}`);
        const descEl = document.getElementById(`desc-${side}-${lane}`);

        if (avatarEl) avatarEl.src = portraitUrl;
        if (nameEl) nameEl.innerText = heroName;
        if (descEl) descEl.innerText = heroClass;

        closeHeroPicker();

        // Jalankan Analisis RBR Realtime!
        runLiveAnalysis();
    }

    // Jalankan Analisis RBR via API POST
    function runLiveAnalysis() {
        const mapSelect = document.getElementById('map-select');
        const selectedMap = mapSelect ? mapSelect.value : currentDraft.map;

        const payload = {
            my_team: Object.values(currentDraft.my).filter(Boolean),
            enemy_team: Object.values(currentDraft.enemy).filter(Boolean),
            map: selectedMap,
            _token: '{{ csrf_token() }}'
        };

        fetch('{{ route("draft.analyze.api") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            updateAnalysisDashboard(data);
        })
        .catch(err => {
            console.error('Error analyzing draft:', err);
        });
    }

    // Perbarui UI Dashboard dengan Hasil RBR
    function updateAnalysisDashboard(data) {
        // 1. Archetype & Spike
        document.getElementById('res-archetype').innerText = data.archetype;
        document.getElementById('res-spike-focus').innerText = data.power_spike_focus;
        document.getElementById('res-damage-ratio').innerText = `${data.damage_ratio.physical_count} Physical • ${data.damage_ratio.magic_count} Magic`;

        // 2. Winning Conditions List
        const wcContainer = document.getElementById('res-winning-conditions');
        let wcHtml = '';
        data.winning_conditions.forEach(wc => {
            wcHtml += `
                <div class="p-3.5 rounded-xl bg-gray-900/60 border border-gray-800/80 flex items-start gap-3">
                    <span class="text-cyan-400 text-lg leading-none mt-0.5">📌</span>
                    <span class="text-gray-200 leading-relaxed font-medium">${wc}</span>
                </div>
            `;
        });
        wcContainer.innerHTML = wcHtml;

        // 3. Map Synergy
        const mapEl = document.getElementById('res-map-synergy');
        if (mapEl) {
            mapEl.innerText = data.map_synergy || 'Varian map standar. Tidak ada sinergi terrain khusus.';
        }

        // 4. Warnings
        const warningsBox = document.getElementById('res-warnings-box');
        const warningsList = document.getElementById('res-warnings');
        if (data.warnings && data.warnings.length > 0) {
            warningsBox.classList.remove('hidden');
            let wHtml = '';
            data.warnings.forEach(w => {
                wHtml += `
                    <div class="p-2.5 bg-red-950/30 border border-red-800/50 rounded-lg text-red-300 flex items-center gap-2 font-medium">
                        <span>⚠️</span>
                        <span>${w.message}</span>
                    </div>
                `;
            });
            warningsList.innerHTML = wHtml;
        } else {
            warningsBox.classList.add('hidden');
        }

        // 5. Kurva Power Spike
        document.getElementById('val-spike-early').innerText = `${data.power_spike_scores.early}%`;
        document.getElementById('bar-spike-early').style.width = `${data.power_spike_scores.early}%`;

        document.getElementById('val-spike-mid').innerText = `${data.power_spike_scores.mid}%`;
        document.getElementById('bar-spike-mid').style.width = `${data.power_spike_scores.mid}%`;

        document.getElementById('val-spike-late').innerText = `${data.power_spike_scores.late}%`;
        document.getElementById('bar-spike-late').style.width = `${data.power_spike_scores.late}%`;

        // 6. Ratings
        document.getElementById('score-teamfight').innerText = `${data.ratings.teamfight}/100`;
        document.getElementById('score-pickoff').innerText = `${data.ratings.pick_off}/100`;
        document.getElementById('score-splitpush').innerText = `${data.ratings.split_push}/100`;
        document.getElementById('score-poke').innerText = `${data.ratings.poke_siege}/100`;
        document.getElementById('score-frontline').innerText = `${data.ratings.frontline}/100`;
        document.getElementById('score-cc').innerText = `${data.ratings.cc}/100`;

        // 7. Fired Rules
        const rulesContainer = document.getElementById('res-fired-rules');
        let rulesHtml = '';
        data.fired_rules.forEach(r => {
            rulesHtml += `
                <div class="p-3 bg-gray-900/70 border border-gray-800 rounded-lg space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-cyan-400 font-mono text-[11px]">${r.rule_id}: ${r.title}</span>
                        <span class="text-[10px] text-emerald-400 font-semibold">Aktif ●</span>
                    </div>
                    <p class="text-gray-400"><strong class="text-gray-300">IF:</strong> ${r.condition}</p>
                    <p class="text-gray-300"><strong class="text-cyan-400">THEN:</strong> ${r.action}</p>
                </div>
            `;
        });
        rulesContainer.innerHTML = rulesHtml;
    }

    // Presets
    const presets = {
        pickoff: {
            my: { exp: 'Chou', jungle: 'Saber', mid: 'Eudora', gold: 'Claude', roam: 'Franco' },
            enemy: { exp: 'Terizla', jungle: 'Baxia', mid: 'Yve', gold: 'Karrie', roam: 'Tigreal' },
            map: 'Dangerous Grass'
        },
        teamfight: {
            my: { exp: 'Terizla', jungle: 'Baxia', mid: 'Pharsa', gold: 'Claude', roam: 'Tigreal' },
            enemy: { exp: 'Ruby', jungle: 'Fanny', mid: 'Novaria', gold: 'Karrie', roam: 'Franco' },
            map: 'Broken Walls'
        },
        lategame: {
            my: { exp: 'Gatotkaca', jungle: 'Fredrinn', mid: 'Cecilion', gold: 'Moskov', roam: 'Angela' },
            enemy: { exp: 'Paquito', jungle: 'Martis', mid: 'Lylia', gold: 'Brody', roam: 'Chou' },
            map: 'Expanding Rivers'
        },
        splitpush: {
            my: { exp: 'Benedetta', jungle: 'Hayabusa', mid: 'Novaria', gold: 'Beatrix', roam: 'Chou' },
            enemy: { exp: 'Terizla', jungle: 'Fredrinn', mid: 'Yve', gold: 'Claude', roam: 'Belerick' },
            map: 'Flying Clouds'
        }
    };

    function loadPreset(key) {
        const p = presets[key];
        if (!p) return;

        currentDraft.my = { ...p.my };
        currentDraft.enemy = { ...p.enemy };
        currentDraft.map = p.map;

        const mapSelect = document.getElementById('map-select');
        if (mapSelect) mapSelect.value = p.map;

        // Update Slot Tim Kita
        for (const [lane, hero] of Object.entries(p.my)) {
            const nameEl = document.getElementById(`name-my-${lane}`);
            const avatarEl = document.getElementById(`avatar-my-${lane}`);
            if (nameEl) nameEl.innerText = hero;
            if (avatarEl) {
                const clean = hero.toLowerCase().replace(/[^a-z0-9]/g, '');
                avatarEl.src = `https://akmweb.youngjoygame.com/web/svnres/img/mlbb/...`; // fallback or trigger live analysis
            }
        }

        // Trigger Analisis Live
        runLiveAnalysis();
        location.reload(); // Reload to render proper portrait URLs effortlessly or let API populate
    }

    function clearDraft() {
        for (const lane of ['exp', 'jungle', 'mid', 'gold', 'roam']) {
            currentDraft.my[lane] = '';
            const nameEl = document.getElementById(`name-my-${lane}`);
            if (nameEl) nameEl.innerText = 'Pilih Hero';
        }
        runLiveAnalysis();
    }

    function toggleEnemySection() {
        const sec = document.getElementById('enemy-section');
        const icon = document.getElementById('enemy-toggle-icon');
        if (sec.classList.contains('hidden')) {
            sec.classList.remove('hidden');
            icon.innerText = '▼';
        } else {
            sec.classList.add('hidden');
            icon.innerText = '▶';
        }
    }
</script>
@endpush
@endsection

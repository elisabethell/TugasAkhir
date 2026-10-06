@extends('layouts.app')

@section('title', 'Counter Pick & Rekomendasi Draft - MetaScout: Land of Dawn')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER & RESET DRAFT -->
    <div class="flex items-center justify-between flex-wrap gap-3 pt-2">
        <h1 class="text-2xl sm:text-3xl font-black text-[#18181B] tracking-tight">
            Counter Pick & Rekomendasi Draft
        </h1>

        <button type="button" 
                onclick="resetDraft()" 
                class="bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] rounded-full px-4 py-2 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-sm transition">
            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>RESET DRAFT</span>
        </button>
    </div>

    <!-- 2. TWO-COLUMN MAIN CONTENT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ================= LEFT COLUMN: SELECT ENEMY HEROES (5 cols) ================= -->
        <div class="lg:col-span-5 space-y-4">
            
            <div class="card-custom p-5 space-y-4">
                <!-- Header -->
                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#18181B]">
                    <svg class="w-4 h-4 text-[#700B1A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>SELECT ENEMY HEROES (1–5)</span>
                </div>

                <!-- 5 Slots Row -->
                <div class="grid grid-cols-5 gap-2" id="selectedSlotsContainer">
                    <!-- Populated dynamically via JS -->
                </div>

                <!-- Search & Clear Row -->
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" 
                               id="pickerSearchInput" 
                               oninput="filterHeroPicker()" 
                               placeholder="Search heroes..." 
                               class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 pl-8 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] transition">
                    </div>
                    <button type="button" 
                            id="clearSelectedBtn" 
                            onclick="clearAllSelected()" 
                            class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-600 hover:text-[#700B1A] border border-[#F3E8E8] text-[11px] font-extrabold uppercase px-3 py-2 rounded-xl transition flex-shrink-0">
                        CLEAR (1)
                    </button>
                </div>

                <!-- Lane Filter Row -->
                <div class="flex items-center gap-1.5 flex-wrap text-xs">
                    <button type="button" onclick="setPickerLane('all', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#700B1A] text-white transition">All</button>
                    <button type="button" onclick="setPickerLane('gold', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Gold Lane</button>
                    <button type="button" onclick="setPickerLane('mid', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Mid Lane</button>
                    <button type="button" onclick="setPickerLane('exp', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Exp Lane</button>
                    <button type="button" onclick="setPickerLane('jungle', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Jungle</button>
                    <button type="button" onclick="setPickerLane('roam', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Roam</button>
                </div>

                <!-- Hero Grid Picker (6 columns) -->
                <div class="grid grid-cols-6 gap-2 pt-2 max-h-[380px] overflow-y-auto pr-1" id="pickerGrid">
                    <!-- Dynamic Hero Cards -->
                </div>

            </div>

        </div>

        <!-- ================= RIGHT COLUMN: RECOMMENDED COUNTER PICKS (7 cols) ================= -->
        <div class="lg:col-span-7 space-y-4">
            
            <div class="card-custom p-5 space-y-4">
                <!-- Header -->
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="text-sm font-black uppercase tracking-wider text-[#18181B]">
                        RECOMMENDED COUNTER PICKS
                    </h3>
                </div>

                <!-- Filter Toolbar (Lane & Role) -->
                <div class="space-y-2 text-xs border-b border-[#FAF0F1] pb-3">
                    <!-- Lane Filters -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-12 text-[10px] font-extrabold uppercase text-gray-400">LANE:</span>
                        <div class="flex items-center gap-1 flex-wrap">
                            <button type="button" onclick="setRecLane('all', this)" class="rec-lane-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#700B1A] text-white transition">All</button>
                            <button type="button" onclick="setRecLane('gold', this)" class="rec-lane-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Gold</button>
                            <button type="button" onclick="setRecLane('mid', this)" class="rec-lane-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Mid</button>
                            <button type="button" onclick="setRecLane('exp', this)" class="rec-lane-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Exp</button>
                            <button type="button" onclick="setRecLane('jungle', this)" class="rec-lane-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Jungle</button>
                            <button type="button" onclick="setRecLane('roam', this)" class="rec-lane-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Roam</button>
                        </div>
                    </div>

                    <!-- Role Filters -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-12 text-[10px] font-extrabold uppercase text-gray-400">ROLE:</span>
                        <div class="flex items-center gap-1 flex-wrap">
                            <button type="button" onclick="setRecRole('all', this)" class="rec-role-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">All</button>
                            <button type="button" onclick="setRecRole('tank', this)" class="rec-role-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Tank</button>
                            <button type="button" onclick="setRecRole('fighter', this)" class="rec-role-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Fighter</button>
                            <button type="button" onclick="setRecRole('assassin', this)" class="rec-role-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Assassin</button>
                            <button type="button" onclick="setRecRole('mage', this)" class="rec-role-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Mage</button>
                            <button type="button" onclick="setRecRole('marksman', this)" class="rec-role-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#700B1A] text-white transition">Marksman</button>
                            <button type="button" onclick="setRecRole('support', this)" class="rec-role-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Support</button>
                        </div>
                    </div>
                </div>

                <!-- Recommendation Cards List -->
                <div class="space-y-3" id="recommendationsList">
                    @foreach($defaultRecommendations as $rec)
                        <div class="rec-card bg-white border border-[#F3E8E8] rounded-2xl p-4 shadow-sm hover:shadow-md transition relative {{ $rec['accent_border'] ? 'border-l-4 border-l-[#700B1A]' : '' }}"
                             data-lane="{{ strtolower($rec['lane']) }}"
                             data-role="{{ strtolower($rec['role']) }}">
                            
                            <div class="flex items-start justify-between gap-3">
                                <!-- Left Info with Avatar -->
                                <div class="flex items-start gap-3 min-w-0">
                                    @if(!empty($rec['portrait']))
                                        <img src="{{ $rec['portrait'] }}" 
                                             alt="{{ $rec['hero'] }}" 
                                             class="w-11 h-11 rounded-xl object-cover bg-gray-100 border border-[#F3E8E8] flex-shrink-0"
                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($rec['hero']) }}&background=700B1A&color=fff';">
                                    @else
                                        <div class="w-11 h-11 rounded-xl bg-[#FAF8F8] border border-[#F3E8E8] text-[#700B1A] font-black text-sm flex items-center justify-center flex-shrink-0">
                                            {{ $rec['initial'] }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-extrabold text-sm text-[#18181B] truncate">
                                                {{ $rec['hero'] }}
                                            </h4>
                                            @if(!empty($rec['tier']))
                                                <span class="bg-[#700B1A] text-white text-[9px] font-black px-1.5 py-0.2 rounded-md">
                                                    {{ $rec['tier'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-gray-500 font-medium truncate mt-0.5">
                                            Role: {{ $rec['role'] }} • Lane: {{ $rec['lane'] }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Win Rate Right -->
                                <div class="text-right flex-shrink-0">
                                    <div class="font-mono font-black text-sm text-[#18181B]">
                                        {{ $rec['win_rate'] }}
                                    </div>
                                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">
                                        {{ $rec['win_rate_label'] }}
                                    </div>
                                </div>
                            </div>

                            <!-- Impact Index Bar -->
                            <div class="mt-3 pt-2.5 border-t border-[#FAF0F1]">
                                <div class="flex items-center justify-between text-[10px] font-bold text-gray-400 mb-1">
                                    <span class="uppercase tracking-wider">COUNTER IMPACT INDEX</span>
                                    <span class="text-[#18181B] font-extrabold">{{ $rec['impact_score'] }} / 5.0</span>
                                </div>
                                <div class="w-full bg-[#FAF0F1] h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-[#700B1A] h-full rounded-full" style="width: {{ $rec['impact_progress'] }}%"></div>
                                </div>
                            </div>

                            <!-- Rule Reason Badge -->
                            <div class="flex items-center justify-between text-[11px] font-medium text-gray-600 mt-2.5 pt-2 border-t border-dashed border-[#FAF0F1] flex-wrap gap-2">
                                <div class="flex items-center gap-1.5 text-xs text-[#18181B] font-semibold">
                                    @if($rec['rule_icon'] === 'check')
                                        <span class="text-emerald-600 font-bold">✔</span>
                                    @elseif($rec['rule_icon'] === 'lock')
                                        <span>🔒</span>
                                    @elseif($rec['rule_icon'] === 'arrow')
                                        <span>🏹</span>
                                    @else
                                        <span class="text-red-600">🚫</span>
                                    @endif
                                    <span>{{ $rec['rule_text'] }}</span>
                                </div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    {{ $rec['samples'] }}
                                </span>
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>

            <!-- Synthesis Confidence Card -->
            <div class="card-custom p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5 text-xs font-bold text-gray-700">
                    <span class="text-[#700B1A] text-base">📈</span>
                    <span>Data synthesized from 69 games</span>
                </div>
                <div class="text-right">
                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">
                        ALGORITHM CONFIDENCE
                    </div>
                    <div class="text-sm font-black text-[#18181B]">
                        94.2%
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    // Master data for hero picker dari database/data/data_hero.csv
    const pickerHeroes = @json($pickerHeroes);

    let selectedHeroes = ['Beatrix'];
    let pickerLane = 'all';
    let recLane = 'all';
    let recRole = 'marksman';

    function renderSlots() {
        const container = document.getElementById('selectedSlotsContainer');
        container.innerHTML = '';

        for (let i = 0; i < 5; i++) {
            const heroName = selectedHeroes[i];
            const slotCol = document.createElement('div');
            slotCol.className = 'flex flex-col items-center';

            if (heroName) {
                const heroObj = pickerHeroes.find(h => h.name.toLowerCase() === heroName.toLowerCase()) || { name: heroName, lane: 'flex', portrait: '' };
                const portraitSrc = heroObj.portrait || `https://ui-avatars.com/api/?name=${encodeURIComponent(heroName)}&background=700B1A&color=fff`;
                slotCol.innerHTML = `
                    <div class="relative w-full aspect-square bg-[#700B1A]/5 border-2 border-[#700B1A] rounded-xl flex items-center justify-center p-0.5 shadow-sm overflow-hidden">
                        <img src="${portraitSrc}" 
                             alt="${heroName}" 
                             class="w-full h-full object-cover rounded-lg"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(heroName)}&background=700B1A&color=fff';">
                        <button type="button" 
                                onclick="removeHero('${heroName}')" 
                                class="absolute top-1 right-1 w-4 h-4 rounded-full bg-[#700B1A] text-white text-[9px] font-black flex items-center justify-center shadow">
                            ✕
                        </button>
                    </div>
                    <span class="font-extrabold text-[11px] text-[#18181B] mt-1 truncate max-w-full">${heroName}</span>
                    <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-500 text-[8px] font-black px-1.5 py-0.2 rounded uppercase mt-0.5">
                        ${(heroObj.lane || 'FLEX').split('•')[0].trim().toUpperCase()}
                    </span>
                `;
            } else {
                slotCol.innerHTML = `
                    <div class="w-full aspect-square bg-[#FAF8F8] border-2 border-dashed border-[#F3E8E8] rounded-xl flex items-center justify-center text-gray-300">
                        <span class="text-base font-light">+</span>
                    </div>
                    <span class="font-semibold text-[10px] text-gray-400 mt-1">Empty</span>
                    <span class="text-[8px] font-bold text-gray-300 uppercase tracking-wider mt-0.5">SLOT ${i + 1}</span>
                `;
            }
            container.appendChild(slotCol);
        }

        document.getElementById('clearSelectedBtn').textContent = `CLEAR (${selectedHeroes.length})`;
        renderPickerGrid();
    }

    function renderPickerGrid() {
        const grid = document.getElementById('pickerGrid');
        grid.innerHTML = '';
        const search = (document.getElementById('pickerSearchInput').value || '').toLowerCase().trim();

        pickerHeroes.forEach(hero => {
            const matchSearch = !search || hero.name.toLowerCase().includes(search);
            const matchLane = pickerLane === 'all' || hero.lane.includes(pickerLane);

            if (matchSearch && matchLane) {
                const isSelected = selectedHeroes.some(h => h.toLowerCase() === hero.name.toLowerCase());
                const item = document.createElement('button');
                item.type = 'button';
                item.onclick = () => toggleSelectHero(hero.name);
                item.className = `flex flex-col items-center justify-center p-2 rounded-xl transition ${
                    isSelected 
                    ? 'bg-[#FCECEE] border-2 border-[#700B1A] shadow-sm' 
                    : 'bg-[#FAF8F8] border border-[#F3E8E8] hover:bg-[#FAF0F1] hover:border-[#F8B4BD]'
                }`;

                item.innerHTML = `
                    <div class="relative w-8 h-8 rounded-lg overflow-hidden ${isSelected ? 'border-2 border-[#700B1A]' : 'border border-[#F3E8E8]'} flex items-center justify-center font-black text-xs bg-white">
                        <img src="${hero.portrait}" 
                             alt="${hero.name}" 
                             class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(hero.name)}&background=700B1A&color=fff';">
                        ${isSelected ? `<span class="absolute inset-0 bg-[#700B1A]/80 text-white flex items-center justify-center font-black text-sm">✓</span>` : ''}
                    </div>
                    <span class="font-bold text-[10px] text-gray-700 mt-1 truncate max-w-full">${hero.name}</span>
                `;
                grid.appendChild(item);
            }
        });
    }

    function toggleSelectHero(heroName) {
        if (selectedHeroes.includes(heroName)) {
            selectedHeroes = selectedHeroes.filter(h => h !== heroName);
        } else {
            if (selectedHeroes.length < 5) {
                selectedHeroes.push(heroName);
            }
        }
        renderSlots();
    }

    function removeHero(heroName) {
        selectedHeroes = selectedHeroes.filter(h => h !== heroName);
        renderSlots();
    }

    function clearAllSelected() {
        selectedHeroes = [];
        renderSlots();
    }

    function resetDraft() {
        selectedHeroes = ['Beatrix'];
        renderSlots();
    }

    function setPickerLane(lane, btn) {
        pickerLane = lane;
        document.querySelectorAll('.picker-lane-btn').forEach(b => {
            b.className = 'picker-lane-btn px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition';
        });
        btn.className = 'picker-lane-btn px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#700B1A] text-white transition';
        renderPickerGrid();
    }

    function filterHeroPicker() {
        renderPickerGrid();
    }

    function setRecLane(lane, btn) {
        recLane = lane;
        document.querySelectorAll('.rec-lane-btn').forEach(b => {
            b.className = 'rec-lane-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition';
        });
        btn.className = 'rec-lane-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#700B1A] text-white transition';
        filterRecCards();
    }

    function setRecRole(role, btn) {
        recRole = role;
        document.querySelectorAll('.rec-role-btn').forEach(b => {
            b.className = 'rec-role-btn px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8] hover:bg-[#FCECEE] transition';
        });
        btn.className = 'rec-role-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#700B1A] text-white transition';
        filterRecCards();
    }

    function filterRecCards() {
        const cards = document.querySelectorAll('.rec-card');
        cards.forEach(c => {
            const lane = c.dataset.lane || '';
            const role = c.dataset.role || '';
            const matchLane = recLane === 'all' || lane.includes(recLane);
            const matchRole = recRole === 'all' || role.includes(recRole);
            if (matchLane && matchRole) {
                c.style.display = 'block';
            } else {
                c.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderSlots();
        filterRecCards();
    });
</script>
@endpush
@endsection

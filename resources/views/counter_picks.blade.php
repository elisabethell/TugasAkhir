@extends('layouts.app')

@section('title', 'Counter Picks - MetaScout: Land of Dawn')

@section('content')
<div class="space-y-5">

    <!-- 1. HEADER SECTION -->
    <div class="flex items-center justify-between flex-wrap gap-3 pt-1">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#18181B] tracking-tight">
                Mobile Legends Counter Pick
            </h1>
            <p class="text-xs text-gray-500 font-medium mt-0.5">
                Strategize your team composition with optimal hero counters based on tournament statistics & rule-based reasoning.
            </p>
        </div>

        <button type="button" 
                onclick="resetDraft()" 
                class="bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-2xs hover:border-[#700B1A] transition">
            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>RESET COUNTER PICKS</span>
        </button>
    </div>

    <!-- 2. TWO-COLUMN MAIN CONTENT (LEFT: SELECT ENEMY HEROES | RIGHT: RECOMMENDED COUNTER PICKS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- ================= LEFT COLUMN: SELECT ENEMY HEROES (5 COLS) ================= -->
        <div class="lg:col-span-5 space-y-4">
            
            <div class="card-custom p-4 sm:p-5 space-y-4">
                <!-- Section Header -->
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-[#18181B]">
                        Select Enemy Heroes (1-5)
                    </h3>
                </div>

                <!-- Enemy Team Slots Header -->
                <div class="flex items-center justify-between text-xs font-extrabold text-gray-700 pt-1">
                    <span class="flex items-center gap-1.5">
                        <span>☠️</span>
                        <span class="text-gray-800">Enemy team</span>
                        <span id="enemySlotCounter" class="text-gray-400 font-semibold">(2/5)</span>
                    </span>
                    <a href="{{ route('draft.recommendation') }}" class="text-[10px] text-[#700B1A] hover:underline font-bold flex items-center gap-1">
                        <span>Planning a full draft? Try Rekom Draft &rarr;</span>
                    </a>
                </div>

                <!-- 5 Circular Slots Row -->
                <div class="grid grid-cols-5 gap-2 pt-1 pb-1" id="selectedSlotsContainer">
                    <!-- Populated dynamically via JS -->
                </div>

                <!-- Search Input & Clear Button -->
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
                            class="bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-black uppercase px-3.5 py-2 rounded-xl transition flex-shrink-0 shadow-2xs">
                        Clear (2)
                    </button>
                </div>

                <!-- Lane Filter Pills (Left Side) -->
                <div class="flex items-center gap-1.5 flex-wrap text-xs">
                    <button type="button" onclick="setPickerLane('all', this)" class="picker-lane-btn px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white transition shadow-2xs">All</button>
                    <button type="button" onclick="setPickerLane('gold', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Gold</button>
                    <button type="button" onclick="setPickerLane('mid', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Mid</button>
                    <button type="button" onclick="setPickerLane('exp', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Exp</button>
                    <button type="button" onclick="setPickerLane('jungle', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Jungle</button>
                    <button type="button" onclick="setPickerLane('roam', this)" class="picker-lane-btn px-2.5 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Roam</button>
                </div>

                <!-- Hero Grid Picker (6 columns circular avatar buttons) -->
                <div class="grid grid-cols-6 gap-2 pt-1 max-h-[440px] overflow-y-auto pr-1" id="pickerGrid">
                    <!-- Populated dynamically via JS -->
                </div>

            </div>

        </div>

        <!-- ================= RIGHT COLUMN: RECOMMENDED COUNTER PICKS (7 COLS) ================= -->
        <div class="lg:col-span-7 space-y-4">
            
            <div class="card-custom p-4 sm:p-5 space-y-3.5">
                <!-- Header -->
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="text-sm font-black uppercase tracking-wider text-[#18181B]">
                        Recommended Counter Picks
                    </h3>
                </div>

                <!-- Filter Toolbar: Lane & Role -->
                <div class="flex items-center gap-3 flex-wrap text-xs bg-[#FAF8F8] border border-[#F3E8E8] p-2.5 rounded-xl">
                    <!-- Lane Pills -->
                    <div class="flex items-center gap-1 flex-wrap">
                        <span class="text-[10px] font-black uppercase text-gray-400 mr-1">LANE</span>
                        <button type="button" onclick="setRecLane('all', this)" class="rec-lane-btn px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#700B1A] text-white transition">All</button>
                        <button type="button" onclick="setRecLane('gold', this)" class="rec-lane-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Gold</button>
                        <button type="button" onclick="setRecLane('mid', this)" class="rec-lane-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Mid</button>
                        <button type="button" onclick="setRecLane('exp', this)" class="rec-lane-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Exp</button>
                        <button type="button" onclick="setRecLane('jungle', this)" class="rec-lane-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Jungle</button>
                        <button type="button" onclick="setRecLane('roam', this)" class="rec-lane-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Roam</button>
                    </div>

                    <span class="text-gray-300">|</span>

                    <!-- Role Pills -->
                    <div class="flex items-center gap-1 flex-wrap">
                        <span class="text-[10px] font-black uppercase text-gray-400 mr-1">ROLE</span>
                        <button type="button" onclick="setRecRole('all', this)" class="rec-role-btn px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#700B1A] text-white transition">All</button>
                        <button type="button" onclick="setRecRole('tank', this)" class="rec-role-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Tank</button>
                        <button type="button" onclick="setRecRole('fighter', this)" class="rec-role-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Fighter</button>
                        <button type="button" onclick="setRecRole('assassin', this)" class="rec-role-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Assassin</button>
                        <button type="button" onclick="setRecRole('mage', this)" class="rec-role-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Mage</button>
                        <button type="button" onclick="setRecRole('marksman', this)" class="rec-role-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Marksman</button>
                        <button type="button" onclick="setRecRole('support', this)" class="rec-role-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition">Support</button>
                    </div>
                </div>

                <!-- Subtitle Info -->
                <div class="text-[11px] text-gray-500 font-medium">
                    Sorted by counter score &bull; Counter score bars are relative to the top result
                </div>

                <!-- Recommendation Cards List -->
                <div class="space-y-3 pt-1" id="recommendationsList">
                    <!-- Populated dynamically via JS -->
                </div>

            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    // Master data dari controller
    const pickerHeroes = @json($pickerHeroes);
    const defaultRecommendations = @json($defaultRecommendations);
    const heroCounterMap = @json($heroCounterMap);
    const heroStatsLookup = @json($heroStatsLookup);
    const specificRules = @json($specificRules);

    // Initial state (Pre-populate Freya & Gloo agar kartu counter pick langsung aktif sesuai screenshot)
    let selectedHeroes = ['Freya', 'Gloo'];
    let pickerLane = 'all';
    let recLane = 'all';
    let recRole = 'all';

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
                    <div class="relative w-full aspect-square">
                        <div class="w-full h-full bg-[#FAF8F8] border-2 border-[#700B1A] rounded-full flex items-center justify-center p-0.5 shadow-sm overflow-hidden">
                            <img src="${portraitSrc}" 
                                 alt="${heroName}" 
                                 class="w-full h-full object-cover rounded-full"
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(heroName)}&background=700B1A&color=fff';">
                        </div>
                        <button type="button" 
                                onclick="removeHero('${heroName}')" 
                                title="Hapus ${heroName}"
                                class="absolute top-0 right-0 translate-x-1 -translate-y-1 w-4 h-4 rounded-full bg-[#DC2626] hover:bg-[#B91C1C] text-white flex items-center justify-center shadow-md border border-white transition transform hover:scale-110 z-20 cursor-pointer">
                            <svg class="w-2 h-2" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <span class="font-extrabold text-[10px] text-[#18181B] mt-1 truncate max-w-full text-center">${heroName}</span>
                `;
            } else {
                slotCol.innerHTML = `
                    <div class="w-full aspect-square bg-[#FAF8F8] border-2 border-dashed border-[#F3E8E8] rounded-full flex items-center justify-center text-gray-300">
                        <span class="text-xs font-bold">+</span>
                    </div>
                    <span class="font-semibold text-[9px] text-gray-400 mt-1">Empty</span>
                `;
            }
            container.appendChild(slotCol);
        }

        document.getElementById('enemySlotCounter').textContent = `(${selectedHeroes.length}/5)`;
        const clearBtn = document.getElementById('clearSelectedBtn');
        clearBtn.textContent = `Clear (${selectedHeroes.length})`;
        if (selectedHeroes.length === 0) {
            clearBtn.className = 'bg-gray-200 text-gray-400 text-xs font-black uppercase px-3.5 py-2 rounded-xl flex-shrink-0 cursor-not-allowed';
            clearBtn.disabled = true;
        } else {
            clearBtn.className = 'bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-black uppercase px-3.5 py-2 rounded-xl transition flex-shrink-0 shadow-2xs cursor-pointer';
            clearBtn.disabled = false;
        }

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
                item.className = 'flex flex-col items-center justify-center p-1 rounded-xl transition group';

                item.innerHTML = `
                    <div class="relative w-9 h-9 rounded-full overflow-hidden ${isSelected ? 'border-2 border-[#700B1A] ring-2 ring-[#700B1A]/20' : 'border border-[#F3E8E8] group-hover:border-[#700B1A]'} flex items-center justify-center bg-white transition">
                        <img src="${hero.portrait}" 
                             alt="${hero.name}" 
                             class="w-full h-full object-cover rounded-full"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(hero.name)}&background=700B1A&color=fff';">
                        ${isSelected ? `<span class="absolute inset-0 bg-[#700B1A]/85 text-white flex items-center justify-center font-black text-xs">✓</span>` : ''}
                    </div>
                    <span class="font-bold text-[9px] text-gray-700 mt-1 truncate max-w-full text-center ${isSelected ? 'text-[#700B1A] font-black' : ''}">${hero.name}</span>
                `;
                grid.appendChild(item);
            }
        });
    }

    function renderRecommendations() {
        const container = document.getElementById('recommendationsList');
        if (!container) return;
        container.innerHTML = '';

        let recs = [];

        if (selectedHeroes.length === 0) {
            // Tampilkan default recommendations jika belum ada musuh terpilih
            recs = defaultRecommendations.map((r, idx) => ({
                hero: r.hero,
                tier: r.tier || (idx === 0 ? 'Tier S' : (idx === 1 ? 'Tier A' : 'Tier B')),
                win_rate: r.win_rate || '51.2%',
                win_rate_label: 'Tournament Win Rate',
                role: r.role || 'Hero',
                lane: r.lane || 'Flex',
                counter_score: (9.9 - (idx * 0.2)).toFixed(1),
                progress_pct: Math.max(30, Math.round(99 - (idx * 4))),
                portrait: r.portrait,
                countered_enemies: ['Meta Priority'],
                rule_text: r.rule_text || 'Prioritas meta turnamen MPL & MWI',
                accent_border: r.accent_border
            }));
        } else {
            const collectedCounters = new Map();

            selectedHeroes.forEach(enemyName => {
                // 1. Cek specific rules (RBR Knowledge Base)
                const specific = specificRules[enemyName] || {};
                Object.keys(specific).forEach(cName => {
                    const ruleInfo = specific[cName];
                    const baseHero = heroStatsLookup[cName] || pickerHeroes.find(h => h.name.toLowerCase() === cName.toLowerCase()) || {};
                    const cur = collectedCounters.get(cName) || {
                        hero: cName,
                        tier: ruleInfo.tier || baseHero.tier || 'Tier A',
                        win_rate: baseHero.win_rate || '52.0%',
                        win_rate_label: 'Tournament Win Rate',
                        role: baseHero.role || 'Hero',
                        lane: baseHero.lane || 'Flex',
                        raw_score: 5.0 + (ruleInfo.impact || 4.2),
                        portrait: baseHero.portrait || '',
                        rule_text: ruleInfo.rule,
                        countered_enemies: []
                    };
                    if (!cur.countered_enemies.includes(enemyName)) {
                        cur.countered_enemies.push(enemyName);
                    }
                    cur.raw_score += 2.0; // Bonus bila meng-counter lebih dari 1 musuh
                    collectedCounters.set(cName, cur);
                });

                // 2. Cek dataset counters dari dataset MLBB
                const rawCounters = heroCounterMap[enemyName] || [];
                rawCounters.forEach(cName => {
                    if (!cName) return;
                    const baseHero = heroStatsLookup[cName] || pickerHeroes.find(h => h.name.toLowerCase() === cName.toLowerCase());
                    if (!baseHero) return;

                    const cur = collectedCounters.get(cName) || {
                        hero: cName,
                        tier: baseHero.tier || 'Tier B',
                        win_rate: baseHero.win_rate || '51.0%',
                        win_rate_label: 'Tournament Win Rate',
                        role: baseHero.role || 'Hero',
                        lane: baseHero.lane || 'Flex',
                        raw_score: 3.5 + (parseFloat(baseHero.win_rate) || 50) / 25,
                        portrait: baseHero.portrait || '',
                        rule_text: `Direct counter terhadap mekanik utama ${enemyName} berdasarkan dataset turnamen`,
                        countered_enemies: []
                    };
                    if (!cur.countered_enemies.includes(enemyName)) {
                        cur.countered_enemies.push(enemyName);
                    }
                    cur.raw_score += 1.5;
                    collectedCounters.set(cName, cur);
                });
            });

            // Urutkan berdasarkan raw_score tertinggi
            let sorted = Array.from(collectedCounters.values()).sort((a, b) => b.raw_score - a.raw_score);

            if (sorted.length === 0) {
                sorted = defaultRecommendations.map((r, idx) => ({
                    hero: r.hero,
                    tier: r.tier || 'Tier A',
                    win_rate: r.win_rate || '51.2%',
                    win_rate_label: 'Tournament Win Rate',
                    role: r.role || 'Hero',
                    lane: r.lane || 'Flex',
                    raw_score: 8.0 - idx,
                    portrait: r.portrait,
                    countered_enemies: [selectedHeroes[0] || 'Lawan'],
                    rule_text: r.rule_text || 'Prioritas counter turnamen'
                }));
            }

            // Normalisasi counter score relatif ke peringkat #1 (misal 9.9 untuk top result)
            const topScore = sorted[0].raw_score;
            recs = sorted.slice(0, 10).map((item, idx) => {
                const relativeFraction = item.raw_score / topScore;
                const scoreVal = Math.max(7.5, (9.9 - (idx * 0.35) * (1.1 - relativeFraction * 0.1))).toFixed(1);
                const progressPct = Math.min(99, Math.max(30, Math.round(relativeFraction * 99)));
                return {
                    ...item,
                    counter_score: idx === 0 ? '9.9' : scoreVal,
                    progress_pct: idx === 0 ? 99 : progressPct
                };
            });
        }

        // Render Kartu Counter Sesuai Mockup Screenshot
        recs.forEach(rec => {
            const card = document.createElement('div');
            card.className = 'rec-card bg-white border border-[#F3E8E8] hover:border-[#700B1A] rounded-2xl p-4 shadow-2xs hover:shadow-xs transition space-y-2';
            card.dataset.lane = (rec.lane || '').toLowerCase();
            card.dataset.role = (rec.role || '').toLowerCase();

            // Tier Badge Styling
            const tierStr = (rec.tier || 'Tier A').toUpperCase();
            let tierClass = 'bg-[#700B1A] text-white';
            if (tierStr.includes('TIER S')) tierClass = 'bg-purple-700 text-white';
            else if (tierStr.includes('TIER A')) tierClass = 'bg-amber-600 text-white';
            else if (tierStr.includes('TIER B')) tierClass = 'bg-blue-600 text-white';
            else if (tierStr.includes('TIER C')) tierClass = 'bg-indigo-600 text-white';
            else if (tierStr.includes('TIER D')) tierClass = 'bg-emerald-600 text-white';

            // Counter Badges
            const badgesHtml = rec.countered_enemies.map(en => `
                <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-[#700B1A] font-extrabold text-[10px] px-2.5 py-0.5 rounded-lg flex items-center gap-1">
                    <span>counters</span>
                    <strong class="text-[#18181B]">${en}</strong>
                </span>
            `).join('');

            card.innerHTML = `
                <!-- Baris 1: Avatar, Hero Name, Tier Badge, Win Rate, dan Counter Score -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <img src="${rec.portrait}" 
                             alt="${rec.hero}" 
                             class="w-10 h-10 rounded-full object-cover bg-gray-50 border border-[#F3E8E8] flex-shrink-0"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(rec.hero)}&background=700B1A&color=fff';">
                        
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-black text-sm text-[#18181B] truncate">${rec.hero}</h4>
                                <span class="${tierClass} text-[8.5px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">${rec.tier || 'Tier A'}</span>
                                <span class="text-[10px] text-gray-500 font-semibold">${rec.win_rate} &bull; ${rec.win_rate_label}</span>
                            </div>
                            <!-- Baris 2: Role & Lane Subtitle -->
                            <p class="text-[10.5px] text-gray-500 font-medium truncate mt-0.5">
                                Role: <span class="text-gray-800 font-bold">${rec.role}</span> &bull; Lane: <span class="text-gray-800 font-bold">${rec.lane}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Counter Score Right -->
                    <div class="text-right flex-shrink-0">
                        <span class="font-mono font-black text-sm text-[#700B1A]">
                            ${rec.counter_score}
                        </span>
                    </div>
                </div>

                <!-- Baris 3: Thin Horizontal Score Bar -->
                <div class="w-full bg-[#FAF0F1] h-1.5 rounded-full overflow-hidden mt-1">
                    <div class="bg-[#700B1A] h-full rounded-full transition-all duration-300" style="width: ${rec.progress_pct}%"></div>
                </div>

                <!-- Baris 4: Counter Badges ([counters Gloo], [counters Freya]) & Rationale -->
                <div class="pt-1 flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        ${badgesHtml}
                    </div>
                    <span class="text-[9.5px] text-gray-400 font-semibold italic truncate max-w-xs">
                        "${rec.rule_text}"
                    </span>
                </div>
            `;
            container.appendChild(card);
        });

        filterRecCards();
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
        renderRecommendations();
    }

    function removeHero(heroName) {
        selectedHeroes = selectedHeroes.filter(h => h !== heroName);
        renderSlots();
        renderRecommendations();
    }

    function clearAllSelected() {
        selectedHeroes = [];
        renderSlots();
        renderRecommendations();
    }

    function resetDraft() {
        selectedHeroes = [];
        renderSlots();
        renderRecommendations();
    }

    function setPickerLane(lane, btn) {
        pickerLane = lane;
        document.querySelectorAll('.picker-lane-btn').forEach(b => {
            b.className = 'picker-lane-btn px-2.5 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition';
        });
        btn.className = 'picker-lane-btn px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white transition shadow-2xs';
        renderPickerGrid();
    }

    function filterHeroPicker() {
        renderPickerGrid();
    }

    function setRecLane(lane, btn) {
        recLane = lane;
        document.querySelectorAll('.rec-lane-btn').forEach(b => {
            b.className = 'rec-lane-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition';
        });
        btn.className = 'rec-lane-btn px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#700B1A] text-white transition';
        filterRecCards();
    }

    function setRecRole(role, btn) {
        recRole = role;
        document.querySelectorAll('.rec-role-btn').forEach(b => {
            b.className = 'rec-role-btn px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] transition';
        });
        btn.className = 'rec-role-btn px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#700B1A] text-white transition';
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
        renderRecommendations();
    });
</script>
@endpush
@endsection

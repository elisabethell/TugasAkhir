@extends('layouts.app')

@section('title', 'Statistik Hero & Tier List Pro Meta - MetaScout: Land of Dawn')

@section('content')
<div class="space-y-6">

    <!-- 1. BREADCRUMBS & REGISTERED BADGE -->
    <div class="flex items-center justify-between flex-wrap gap-3 pt-2">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase tracking-wider">
            <a href="{{ route('home') }}" class="hover:text-[#700B1A] transition">HOME</a>
            <span>/</span>
            <span class="text-[#700B1A] font-extrabold">STATISTIK HERO</span>
        </div>

        <div class="flex items-center gap-2 text-xs font-bold text-gray-700 bg-white border border-[#F3E8E8] rounded-full px-4 py-1.5 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="uppercase tracking-wider text-[11px] font-extrabold text-[#18181B]">133 HEROES REGISTERED</span>
        </div>
    </div>

    <!-- 1.5. STAGE TABS (SESUAI LIQUIPEDIA OVERALL / REGULAR / PLAYOFFS) -->
    <div class="flex items-center gap-2 flex-wrap border-b border-[#F3E8E8] pb-2">
        <a href="{{ route('hero.statistics', ['stage' => 'overall']) }}" 
           class="px-4 py-2 text-xs font-black rounded-xl transition flex items-center gap-2 {{ ($activeStage ?? 'overall') === 'overall' ? 'bg-[#700B1A] text-white shadow-sm' : 'bg-white hover:bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8]' }}">
            <span>Overall</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($activeStage ?? 'overall') === 'overall' ? 'bg-white/20 text-white' : 'bg-[#FAF8F8] text-gray-500' }}">
                {{ $totalOverallGames ?? 215 }} Games
            </span>
        </a>
        <a href="{{ route('hero.statistics', ['stage' => 'regular']) }}" 
           class="px-4 py-2 text-xs font-black rounded-xl transition flex items-center gap-2 {{ ($activeStage ?? 'overall') === 'regular' ? 'bg-[#700B1A] text-white shadow-sm' : 'bg-white hover:bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8]' }}">
            <span>Regular Season</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($activeStage ?? 'overall') === 'regular' ? 'bg-white/20 text-white' : 'bg-[#FAF8F8] text-gray-500' }}">
                {{ $totalRegularGames ?? 174 }} Games
            </span>
        </a>
        <a href="{{ route('hero.statistics', ['stage' => 'playoffs']) }}" 
           class="px-4 py-2 text-xs font-black rounded-xl transition flex items-center gap-2 {{ ($activeStage ?? 'overall') === 'playoffs' ? 'bg-[#700B1A] text-white shadow-sm' : 'bg-white hover:bg-[#FAF8F8] text-gray-600 border border-[#F3E8E8]' }}">
            <span>Playoffs</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($activeStage ?? 'overall') === 'playoffs' ? 'bg-white/20 text-white' : 'bg-[#FAF8F8] text-gray-500' }}">
                {{ $totalPlayoffGames ?? 41 }} Games
            </span>
        </a>
    </div>

    <!-- 2. SEARCH & FILTER TOOLBAR -->
    <div class="card-custom p-4 flex flex-col md:flex-row items-center justify-between gap-3 flex-wrap">
        
        <!-- Search Input -->
        <div class="relative w-full md:w-72 flex-shrink-0">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input type="text" 
                   id="statSearchInput" 
                   oninput="filterStatsTable()" 
                   placeholder="Search hero name..." 
                   class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 pl-9 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] transition">
        </div>

        <!-- Dropdowns & Actions -->
        <div class="flex items-center gap-2 flex-wrap w-full md:w-auto justify-start md:justify-end">
            <!-- Role Dropdown -->
            <select id="roleSelect" onchange="filterStatsTable()" class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-700 text-xs font-bold rounded-xl px-3 py-2 focus:outline-none focus:border-[#700B1A] cursor-pointer">
                <option value="">ROLE (ALL)</option>
                <option value="Mage">Mage</option>
                <option value="Marksman">Marksman</option>
                <option value="Tank">Tank</option>
                <option value="Fighter">Fighter</option>
                <option value="Assassin">Assassin</option>
                <option value="Support">Support</option>
            </select>

            <!-- Lane Dropdown -->
            <select id="laneSelect" onchange="filterStatsTable()" class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-700 text-xs font-bold rounded-xl px-3 py-2 focus:outline-none focus:border-[#700B1A] cursor-pointer">
                <option value="">LANE (ALL)</option>
                <option value="Gold Lane">Gold Lane</option>
                <option value="Mid">Mid Lane</option>
                <option value="Exp Lane">Exp Lane</option>
                <option value="Jungle">Jungle</option>
                <option value="Roam">Roam</option>
            </select>

            <!-- Reset Button -->
            <button type="button" onclick="resetStatsFilters()" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-600 hover:text-[#700B1A] border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-bold flex items-center gap-1.5 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>RESET FILTERS</span>
            </button>
        </div>

    </div>

    <!-- 3. LIQUIPEDIA STYLE HERO STATISTICS TABLE -->
    <div class="card-custom overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse" id="statsTable">
                <thead>
                    <!-- LEVEL 1 HEADER: KATEGORI METRIK -->
                    <tr class="bg-[#FAF8F8] border-b border-[#F3E8E8] text-[10px] font-black uppercase text-gray-500 tracking-wider select-none text-center">
                        <th rowspan="2" class="py-2.5 px-3 w-10 text-center border-r border-[#F3E8E8]">#</th>
                        <th rowspan="2" class="py-2.5 px-3.5 text-left border-r border-[#F3E8E8] cursor-pointer hover:text-[#700B1A] transition select-none" onclick="sortTable('name')">
                            HERO <span id="sort-icon-name" class="text-gray-400">⇅</span>
                        </th>
                        <th colspan="5" class="py-2 px-3 border-r border-[#F3E8E8] bg-amber-50/50 text-amber-900">
                            PICKS
                        </th>
                        <th colspan="4" class="py-2 px-3 border-r border-[#F3E8E8] bg-blue-50/50 text-blue-900">
                            BLUE SIDE
                        </th>
                        <th colspan="4" class="py-2 px-3 border-r border-[#F3E8E8] bg-red-50/50 text-red-900">
                            RED SIDE
                        </th>
                        <th colspan="2" class="py-2 px-3 border-r border-[#F3E8E8] bg-rose-50/50 text-rose-900">
                            BANS
                        </th>
                        <th colspan="2" class="py-2 px-3 border-r border-[#F3E8E8] bg-purple-50/50 text-purple-900">
                            PICKS & BANS
                        </th>
                        <th rowspan="2" class="py-2.5 px-3.5 text-left cursor-pointer hover:text-[#700B1A] transition select-none" onclick="sortTable('role')">
                            ROLE & LANE <span id="sort-icon-role" class="text-gray-400">⇅</span>
                        </th>
                    </tr>

                    <!-- LEVEL 2 HEADER: SUB-METRIK PERSIS LIQUIPEDIA -->
                    <tr class="bg-[#FAF8F8] border-b border-[#F3E8E8] text-[9.5px] font-black uppercase text-gray-600 tracking-wider select-none text-center">
                        <!-- Picks Sub-columns -->
                        <th class="py-1.5 px-2.5 bg-amber-50/30 cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable('picks', true)" title="Total Picks">
                            &Sigma; <span id="sort-icon-picks" class="text-[#700B1A] font-black">&darr;</span>
                        </th>
                        <th class="py-1.5 px-2 bg-amber-50/30 cursor-pointer hover:text-[#700B1A] transition text-emerald-700" onclick="sortTable('wins', true)" title="Wins">
                            W <span id="sort-icon-wins" class="text-gray-400 font-normal">⇅</span>
                        </th>
                        <th class="py-1.5 px-2 bg-amber-50/30 cursor-pointer hover:text-[#700B1A] transition text-red-600" onclick="sortTable('losses', true)" title="Losses">
                            L <span id="sort-icon-losses" class="text-gray-400 font-normal">⇅</span>
                        </th>
                        <th class="py-1.5 px-2.5 bg-amber-50/30 cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable('wr', true)" title="Win Rate %">
                            WR <span id="sort-icon-wr" class="text-gray-400 font-normal">⇅</span>
                        </th>
                        <th class="py-1.5 px-2.5 bg-amber-50/30 border-r border-[#F3E8E8] cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable('pr', true)" title="Pick Rate %T">
                            %T <span id="sort-icon-pr" class="text-gray-400 font-normal">⇅</span>
                        </th>

                        <!-- Blue Side Sub-columns -->
                        <th class="py-1.5 px-2 bg-blue-50/30 cursor-pointer hover:text-[#700B1A] transition font-bold text-blue-800" onclick="sortTable('blue_picks', true)" title="Blue Side Picks">
                            &Sigma; <span id="sort-icon-blue_picks" class="text-gray-400 font-normal">⇅</span>
                        </th>
                        <th class="py-1.5 px-2 bg-blue-50/30 text-emerald-700" title="Blue Side Wins">W</th>
                        <th class="py-1.5 px-2 bg-blue-50/30 text-red-600" title="Blue Side Losses">L</th>
                        <th class="py-1.5 px-2.5 bg-blue-50/30 border-r border-[#F3E8E8] cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable('blue_wr', true)" title="Blue Side Win Rate %">
                            WR <span id="sort-icon-blue_wr" class="text-gray-400 font-normal">⇅</span>
                        </th>

                        <!-- Red Side Sub-columns -->
                        <th class="py-1.5 px-2 bg-red-50/30 cursor-pointer hover:text-[#700B1A] transition font-bold text-red-800" onclick="sortTable('red_picks', true)" title="Red Side Picks">
                            &Sigma; <span id="sort-icon-red_picks" class="text-gray-400 font-normal">⇅</span>
                        </th>
                        <th class="py-1.5 px-2 bg-red-50/30 text-emerald-700" title="Red Side Wins">W</th>
                        <th class="py-1.5 px-2 bg-red-50/30 text-red-600" title="Red Side Losses">L</th>
                        <th class="py-1.5 px-2.5 bg-red-50/30 border-r border-[#F3E8E8] cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable('red_wr', true)" title="Red Side Win Rate %">
                            WR <span id="sort-icon-red_wr" class="text-gray-400 font-normal">⇅</span>
                        </th>

                        <!-- Bans Sub-columns -->
                        <th class="py-1.5 px-2.5 bg-rose-50/30 cursor-pointer hover:text-[#700B1A] transition font-bold text-rose-800" onclick="sortTable('bans', true)" title="Total Bans">
                            &Sigma; <span id="sort-icon-bans" class="text-gray-400 font-normal">⇅</span>
                        </th>
                        <th class="py-1.5 px-2.5 bg-rose-50/30 border-r border-[#F3E8E8] cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable('br', true)" title="Ban Rate %T">
                            %T <span id="sort-icon-br" class="text-gray-400 font-normal">⇅</span>
                        </th>

                        <!-- Picks & Bans Sub-columns -->
                        <th class="py-1.5 px-2.5 bg-purple-50/30 cursor-pointer hover:text-[#700B1A] transition font-bold text-purple-800" onclick="sortTable('pb', true)" title="Total Picks + Bans">
                            &Sigma; <span id="sort-icon-pb" class="text-gray-400 font-normal">⇅</span>
                        </th>
                        <th class="py-1.5 px-2.5 bg-purple-50/30 border-r border-[#F3E8E8] cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable('pbr', true)" title="Contest Rate %T">
                            %T <span id="sort-icon-pbr" class="text-gray-400 font-normal">⇅</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F3E8E8]" id="statsTbody">
                    @foreach($heroRows as $idx => $row)
                        <tr class="hover:bg-[#FAF8F8]/90 transition stat-table-row {{ $idx % 2 === 1 ? 'bg-[#FCFAFA]/40' : 'bg-white' }}"
                            data-name="{{ strtolower($row['name']) }}"
                            data-role="{{ strtolower($row['role']) }}"
                            data-lane="{{ strtolower(implode(' ', (array)$row['lane'])) }}"
                            data-spec="{{ strtolower(implode(' ', (array)$row['speciality'])) }}"
                            data-picks="{{ $row['picks'] }}"
                            data-wins="{{ $row['wins'] }}"
                            data-losses="{{ $row['losses'] }}"
                            data-wr="{{ $row['wr'] }}"
                            data-pr="{{ $row['pick_rate'] }}"
                            data-blue_picks="{{ $row['blue_picks'] }}"
                            data-blue_wr="{{ $row['blue_wr'] }}"
                            data-red_picks="{{ $row['red_picks'] }}"
                            data-red_wr="{{ $row['red_wr'] }}"
                            data-bans="{{ $row['bans'] }}"
                            data-br="{{ $row['ban_rate'] }}"
                            data-pb="{{ $row['picks_bans'] }}"
                            data-pbr="{{ $row['picks_bans_rate'] }}">
                            
                            <!-- Index -->
                            <td class="py-1.5 px-2.5 text-center font-bold text-gray-400 text-xs align-middle border-r border-[#F3E8E8] stat-index-cell">
                                {{ $idx + 1 }}
                            </td>

                            <!-- Hero Name with Avatar Box -->
                            <td class="py-1.5 px-3 align-middle border-r border-[#F3E8E8] whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if(!empty($row['portrait']))
                                        <img src="{{ $row['portrait'] }}" 
                                             alt="{{ $row['name'] }}" 
                                             class="w-6 h-6 rounded-full object-cover border border-[#F3E8E8] flex-shrink-0"
                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}&background=700B1A&color=fff';">
                                    @else
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs shadow-sm flex-shrink-0 {{ $row['initial_bg'] }}">
                                            {{ $row['initial'] }}
                                        </div>
                                    @endif
                                    <span class="font-extrabold text-[#18181B] text-xs">
                                        {{ $row['name'] }}
                                    </span>
                                </div>
                            </td>

                            <!-- Picks: Σ, W, L, WR, %T -->
                            <td class="py-1.5 px-2.5 text-center font-extrabold text-[#18181B] bg-amber-50/20 font-mono text-xs">
                                {{ $row['picks'] }}
                            </td>
                            <td class="py-1.5 px-2 text-center text-emerald-700 font-bold bg-amber-50/20 font-mono text-xs">
                                {{ $row['wins'] }}
                            </td>
                            <td class="py-1.5 px-2 text-center text-red-600 font-bold bg-amber-50/20 font-mono text-xs">
                                {{ $row['losses'] }}
                            </td>
                            <td class="py-1.5 px-2.5 text-center font-mono font-extrabold bg-amber-50/20 text-xs {{ $row['wr'] >= 58 ? 'text-emerald-700' : ($row['wr'] >= 50 ? 'text-gray-700' : 'text-red-600') }}">
                                {{ number_format($row['wr'], 2) }}%
                            </td>
                            <td class="py-1.5 px-2.5 text-center font-mono font-bold text-gray-700 bg-amber-50/20 border-r border-[#F3E8E8] text-xs">
                                {{ number_format($row['pick_rate'], 2) }}%
                            </td>

                            <!-- Blue Side: Σ, W, L, WR -->
                            <td class="py-1.5 px-2 text-center font-bold text-blue-800 bg-blue-50/20 font-mono text-xs">
                                {{ $row['blue_picks'] }}
                            </td>
                            <td class="py-1.5 px-2 text-center text-emerald-700 bg-blue-50/20 font-mono text-xs">
                                {{ $row['blue_wins'] }}
                            </td>
                            <td class="py-1.5 px-2 text-center text-red-600 bg-blue-50/20 font-mono text-xs">
                                {{ $row['blue_losses'] }}
                            </td>
                            <td class="py-1.5 px-2.5 text-center font-mono font-extrabold bg-blue-50/20 border-r border-[#F3E8E8] text-xs {{ $row['blue_wr'] >= 58 ? 'text-emerald-700' : ($row['blue_wr'] >= 50 ? 'text-gray-700' : 'text-red-600') }}">
                                {{ number_format($row['blue_wr'], 2) }}%
                            </td>

                            <!-- Red Side: Σ, W, L, WR -->
                            <td class="py-1.5 px-2 text-center font-bold text-red-800 bg-red-50/20 font-mono text-xs">
                                {{ $row['red_picks'] }}
                            </td>
                            <td class="py-1.5 px-2 text-center text-emerald-700 bg-red-50/20 font-mono text-xs">
                                {{ $row['red_wins'] }}
                            </td>
                            <td class="py-1.5 px-2 text-center text-red-600 bg-red-50/20 font-mono text-xs">
                                {{ $row['red_losses'] }}
                            </td>
                            <td class="py-1.5 px-2.5 text-center font-mono font-extrabold bg-red-50/20 border-r border-[#F3E8E8] text-xs {{ $row['red_wr'] >= 58 ? 'text-emerald-700' : ($row['red_wr'] >= 50 ? 'text-gray-700' : 'text-red-600') }}">
                                {{ number_format($row['red_wr'], 2) }}%
                            </td>

                            <!-- Bans: Σ, %T -->
                            <td class="py-1.5 px-2.5 text-center font-extrabold text-rose-800 bg-rose-50/20 font-mono text-xs">
                                {{ $row['bans'] }}
                            </td>
                            <td class="py-1.5 px-2.5 text-center font-mono font-bold text-gray-700 bg-rose-50/20 border-r border-[#F3E8E8] text-xs">
                                {{ number_format($row['ban_rate'], 2) }}%
                            </td>

                            <!-- Picks & Bans: Σ, %T -->
                            <td class="py-1.5 px-2.5 text-center font-extrabold text-purple-900 bg-purple-50/20 font-mono text-xs">
                                {{ $row['picks_bans'] }}
                            </td>
                            <td class="py-1.5 px-2.5 text-center font-mono font-bold text-gray-700 bg-purple-50/20 border-r border-[#F3E8E8] text-xs">
                                {{ number_format($row['picks_bans_rate'], 2) }}%
                            </td>

                            <!-- Role & Lane -->
                            <td class="py-1.5 px-3 align-middle whitespace-nowrap">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-700 text-[10px] font-bold px-2 py-0.5 rounded leading-tight">
                                        {{ $row['role'] }}
                                    </span>
                                    @foreach((array)$row['lane'] as $l)
                                        <span class="bg-gray-50 border border-gray-200 text-gray-500 text-[9.5px] font-semibold px-1.5 py-0.5 rounded leading-tight">
                                            {{ $l }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- 3.5. PAGINATION FOOTER BAR -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-3 sm:px-6 sm:py-3.5 border-t border-[#F3E8E8] bg-white text-xs">
            <div class="flex items-center gap-2.5 flex-wrap">
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-gray-500 font-bold text-xs whitespace-nowrap">Tampilkan:</span>
                    <select id="statsPerPageSelect" 
                            onchange="changeStatsPerPage(this.value)" 
                            class="bg-[#FAF8F8] border border-[#E5E7EB] rounded-lg px-2.5 py-1 text-xs font-extrabold text-[#18181B] focus:outline-none focus:border-[#700B1A] transition cursor-pointer shadow-2xs">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="all">Semua</option>
                    </select>
                </div>
                <span class="text-gray-300 font-light select-none hidden sm:inline">|</span>
                <div class="h-4 w-px bg-[#E5E7EB] hidden sm:block"></div>
                <div class="text-gray-600 font-medium text-xs whitespace-nowrap" id="statsPaginationInfo">
                    Menampilkan <span class="font-extrabold text-[#700B1A]" id="statsPageStart">1</span> - <span class="font-extrabold text-[#700B1A]" id="statsPageEnd">25</span> dari <span class="font-extrabold text-[#18181B]" id="statsTotalRows">0</span> hero
                </div>
            </div>

            <!-- Page Buttons (Prev, 1, 2, 3..., Next) -->
            <div class="flex items-center gap-1.5 flex-wrap" id="statsPaginationButtons">
                <!-- Dynamically generated via JS -->
            </div>
        </div>
    </div>

    <!-- 4. THREE BOTTOM INSIGHT CARDS (COMPACT & PROPORTIONAL) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
        
        <!-- Insight Card 1: Dominant Win Rate -->
        <div class="card-custom p-3.5 sm:p-4 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-5 h-5 rounded-md bg-[#FCECEE] text-[#700B1A] flex items-center justify-center font-black text-xs">!</span>
                    <h4 class="font-extrabold text-xs sm:text-sm text-[#18181B]">Power Spike & Win Rate</h4>
                </div>
                <p class="text-[11px] text-gray-600 leading-snug">
                    @if(!empty($topWrHero))
                        {{ $topWrHero['name'] }} memimpin performa dengan Win Rate {{ number_format($topWrHero['wr'], 1) }}% ({{ $topWrHero['wins'] }} menang dari {{ $topWrHero['picks'] }} game) pada data turnamen MPL ID S18.
                    @else
                        Statistik performa win rate turnamen dihitung secara dinamis dari database.
                    @endif
                </p>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-gray-400 pt-2.5 mt-2 border-t border-[#FAF0F1]">
                <span class="uppercase tracking-wider">WIN RATE TERTINGGI</span>
                <a href="{{ route('draft.analyzer') }}" class="text-[#700B1A] hover:underline flex items-center gap-1 font-extrabold">
                    <span>SIMULASI DRAFT &gt;</span>
                </a>
            </div>
        </div>

        <!-- Insight Card 2: Priority Permabans -->
        <div class="card-custom p-3.5 sm:p-4 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-5 h-5 rounded-md bg-[#FCECEE] text-[#700B1A] flex items-center justify-center font-black text-xs">X</span>
                    <h4 class="font-extrabold text-xs sm:text-sm text-[#18181B]">Priority Permabans</h4>
                </div>
                <p class="text-[11px] text-gray-600 leading-snug">
                    @if(!empty($topBanHero))
                        Ban rate {{ number_format($topBanHero['ban_rate'], 1) }}% {{ $topBanHero['name'] }} ({{ $topBanHero['bans'] }} ban dari {{ $totalGames }} game) menjadikannya prioritas ban utama pada turnamen MPL ID S18.
                    @else
                        Data prioritas ban dihitung secara dinamis dari pertandingan turnamen.
                    @endif
                </p>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-gray-400 pt-2.5 mt-2 border-t border-[#FAF0F1]">
                <span class="uppercase tracking-wider">BAN RATE TINGGI</span>
                <a href="{{ route('draft.analyzer') }}" class="text-[#700B1A] hover:underline flex items-center gap-1 font-extrabold">
                    <span>SIMULASI DRAFT &gt;</span>
                </a>
            </div>
        </div>

        <!-- Insight Card 3: Most Picked Hero -->
        <div class="card-custom p-3.5 sm:p-4 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-5 h-5 rounded-md bg-[#ECFDF5] text-emerald-700 flex items-center justify-center font-black text-xs">+</span>
                    <h4 class="font-extrabold text-xs sm:text-sm text-[#18181B]">Most Picked Hero</h4>
                </div>
                <p class="text-[11px] text-gray-600 leading-snug">
                    @if(!empty($topPickHero))
                        {{ $topPickHero['name'] }} menjadi hero paling banyak dipilih dengan {{ $topPickHero['picks'] }} kali pick (Pick Rate {{ number_format($topPickHero['pick_rate'], 1) }}%).
                    @else
                        Menghitung hero dengan pick rate tertinggi dari database turnamen.
                    @endif
                </p>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-gray-400 pt-2.5 mt-2 border-t border-[#FAF0F1]">
                <span class="uppercase tracking-wider">PICK RATE TERTINGGI</span>
                <a href="{{ route('matches') }}" class="text-[#700B1A] hover:underline flex items-center gap-1 font-extrabold">
                    <span>ANALISIS SERI &gt;</span>
                </a>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    let currentStatsPage = 1;
    let statsPerPage = 25;

    function filterStatsTable() {
        const query = (document.getElementById('statSearchInput').value || '').toLowerCase().trim();
        const role = (document.getElementById('roleSelect').value || '').toLowerCase().trim();
        const lane = (document.getElementById('laneSelect').value || '').toLowerCase().trim();

        const rows = document.querySelectorAll('.stat-table-row');
        rows.forEach(row => {
            const nameText = row.dataset.name || '';
            const roleText = row.dataset.role || '';
            const laneText = row.dataset.lane || '';

            const matchQuery = !query || nameText.includes(query);
            const matchRole = !role || roleText.includes(role);
            const matchLane = !lane || laneText.includes(lane);

            row.dataset.matched = (matchQuery && matchRole && matchLane) ? 'true' : 'false';
        });

        currentStatsPage = 1;
        updateStatsPagination();
    }

    function changeStatsPerPage(val) {
        statsPerPage = val === 'all' ? 999999 : parseInt(val);
        currentStatsPage = 1;
        updateStatsPagination();
    }

    function goToStatsPage(page) {
        currentStatsPage = page;
        updateStatsPagination();
        const table = document.getElementById('statsTable');
        if (table) {
            table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function updateStatsPagination() {
        const tbody = document.getElementById('statsTbody');
        const allRows = Array.from(tbody.querySelectorAll('.stat-table-row'));
        const matchedRows = allRows.filter(r => r.dataset.matched !== 'false');
        const totalMatched = matchedRows.length;
        const totalPages = Math.ceil(totalMatched / statsPerPage) || 1;

        if (currentStatsPage > totalPages) currentStatsPage = totalPages;
        if (currentStatsPage < 1) currentStatsPage = 1;

        const startIdx = (currentStatsPage - 1) * statsPerPage;
        const endIdx = startIdx + statsPerPage;

        allRows.forEach(r => r.style.display = 'none');
        matchedRows.slice(startIdx, endIdx).forEach(r => r.style.display = '');

        // Update Info Counter
        const startElem = document.getElementById('statsPageStart');
        const endElem = document.getElementById('statsPageEnd');
        const totalElem = document.getElementById('statsTotalRows');
        if (startElem) startElem.textContent = totalMatched > 0 ? (startIdx + 1) : 0;
        if (endElem) endElem.textContent = Math.min(endIdx, totalMatched);
        if (totalElem) totalElem.textContent = totalMatched;

        // Render Pagination Buttons
        const container = document.getElementById('statsPaginationButtons');
        if (!container) return;
        container.innerHTML = '';

        if (totalPages <= 1) return;

        // Prev Button
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.disabled = currentStatsPage === 1;
        prevBtn.className = `px-3 py-1 rounded-full text-xs font-bold border transition ${
            currentStatsPage === 1 
                ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' 
                : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer shadow-2xs'
        }`;
        prevBtn.innerHTML = '&larr; Prev';
        prevBtn.onclick = () => goToStatsPage(currentStatsPage - 1);
        container.appendChild(prevBtn);

        // Numbered Page Buttons
        for (let p = 1; p <= totalPages; p++) {
            if (p === 1 || p === totalPages || (p >= currentStatsPage - 1 && p <= currentStatsPage + 1)) {
                const pageBtn = document.createElement('button');
                pageBtn.type = 'button';
                pageBtn.className = `w-7 h-7 rounded-full text-xs font-extrabold flex items-center justify-center transition ${
                    p === currentStatsPage 
                        ? 'bg-[#700B1A] text-white shadow-sm' 
                        : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer'
                }`;
                pageBtn.textContent = p;
                pageBtn.onclick = () => goToStatsPage(p);
                container.appendChild(pageBtn);
            } else if (p === currentStatsPage - 2 || p === currentStatsPage + 2) {
                const dots = document.createElement('span');
                dots.className = 'text-gray-400 px-1 font-bold text-xs';
                dots.textContent = '...';
                container.appendChild(dots);
            }
        }

        // Next Button
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.disabled = currentStatsPage === totalPages;
        nextBtn.className = `px-3 py-1 rounded-full text-xs font-bold border transition ${
            currentStatsPage === totalPages 
                ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' 
                : 'bg-white hover:bg-[#FAF8F8] text-gray-700 border-[#F3E8E8] hover:text-[#700B1A] cursor-pointer shadow-2xs'
        }`;
        nextBtn.innerHTML = 'Next &rarr;';
        nextBtn.onclick = () => goToStatsPage(currentStatsPage + 1);
        container.appendChild(nextBtn);
    }

    function resetStatsFilters() {
        document.getElementById('statSearchInput').value = '';
        document.getElementById('roleSelect').value = '';
        document.getElementById('laneSelect').value = '';
        filterStatsTable();
    }

    let currentSortKey = 'picks'; // Default sort key sesuai Liquipedia
    let currentSortDir = 'desc';

    function sortTable(key, isNumeric = false) {
        const tbody = document.getElementById('statsTbody');
        const rows = Array.from(tbody.querySelectorAll('.stat-table-row'));

        if (currentSortKey === key) {
            currentSortDir = (currentSortDir === 'desc') ? 'asc' : 'desc';
        } else {
            currentSortKey = key;
            currentSortDir = isNumeric ? 'desc' : 'asc';
        }

        const isAsc = (currentSortDir === 'asc');

        rows.sort((a, b) => {
            if (isNumeric) {
                const valA = parseFloat(a.dataset[key]) || 0;
                const valB = parseFloat(b.dataset[key]) || 0;
                if (valA !== valB) {
                    return isAsc ? (valA - valB) : (valB - valA);
                }
                // Tie breaker: Picks, WR, Name
                const picksA = parseFloat(a.dataset.picks) || 0;
                const picksB = parseFloat(b.dataset.picks) || 0;
                if (picksA !== picksB) return picksB - picksA;
                return (a.dataset.name || '').localeCompare(b.dataset.name || '');
            } else {
                const valA = a.dataset[key] || '';
                const valB = b.dataset[key] || '';
                if (valA !== valB) {
                    return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
                }
                return (a.dataset.name || '').localeCompare(b.dataset.name || '');
            }
        });

        // Susun ulang baris di tbody dan sinkronkan nomor urut (#)
        rows.forEach((row, idx) => {
            tbody.appendChild(row);
            const indexCell = row.querySelector('.stat-index-cell');
            if (indexCell) {
                indexCell.textContent = idx + 1;
            }
        });

        updateSortIcons(currentSortKey, currentSortDir);
        currentStatsPage = 1;
        updateStatsPagination();
    }

    function updateSortIcons(activeKey, dir) {
        const sortKeys = ['name', 'role', 'picks', 'wins', 'losses', 'wr', 'pr', 'blue_picks', 'blue_wr', 'red_picks', 'red_wr', 'bans', 'br', 'pb', 'pbr'];
        sortKeys.forEach(k => {
            const iconEl = document.getElementById(`sort-icon-${k}`);
            if (!iconEl) return;
            if (k === activeKey) {
                iconEl.className = 'text-[#700B1A] font-black';
                iconEl.innerHTML = (dir === 'desc') ? '&darr;' : '&uarr;';
            } else {
                iconEl.className = 'text-gray-400 font-normal';
                iconEl.innerHTML = '&#8645;';
            }
        });
    }

    // Inisialisasi saat load
    document.addEventListener('DOMContentLoaded', function() {
        filterStatsTable();
    });
</script>
@endpush
@endsection

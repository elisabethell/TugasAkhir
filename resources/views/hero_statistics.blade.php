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

            <!-- Speciality Dropdown -->
            <select id="specSelect" onchange="filterStatsTable()" class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-700 text-xs font-bold rounded-xl px-3 py-2 focus:outline-none focus:border-[#700B1A] cursor-pointer">
                <option value="">SPECIALITY (ALL)</option>
                <option value="Damage">Damage</option>
                <option value="Crowd Control">Crowd Control</option>
                <option value="Charge">Charge</option>
                <option value="Burst">Burst</option>
                <option value="Initiator">Initiator</option>
                <option value="Regen">Regen</option>
                <option value="Guard">Guard</option>
            </select>

            <!-- Reset Button -->
            <button type="button" onclick="resetStatsFilters()" class="bg-[#FAF8F8] hover:bg-[#FCECEE] text-gray-600 hover:text-[#700B1A] border border-[#F3E8E8] rounded-xl px-3 py-2 text-xs font-bold flex items-center gap-1.5 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>RESET FILTERS</span>
            </button>

            <!-- Export CSV -->
            <button type="button" onclick="exportTableToCSV('metascout_hero_statistics.csv')" class="bg-[#700B1A] hover:bg-[#550713] text-white rounded-xl px-4 py-2 text-xs font-bold flex items-center gap-1.5 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>EXPORT CSV</span>
            </button>
        </div>

    </div>

    <!-- 3. STATISTICS TABLE -->
    <div class="card-custom overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse" id="statsTable">
                <thead>
                    <tr class="bg-[#FAF8F8] border-b border-[#F3E8E8] text-[10px] font-black uppercase text-gray-500 tracking-wider">
                        <th class="py-2.5 px-3 w-10 text-center">#</th>
                        <th class="py-2.5 px-3.5 cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable(1)">
                            HERO NAME <span class="text-gray-400">⇅</span>
                        </th>
                        <th class="py-2.5 px-3.5 cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable(2, true)">
                            WIN RATE <span class="text-[#700B1A]">↓</span>
                        </th>
                        <th class="py-2.5 px-3.5 cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable(3, true)">
                            PICK RATE <span class="text-gray-400">⇅</span>
                        </th>
                        <th class="py-2.5 px-3.5 cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable(4, true)">
                            BAN RATE <span class="text-gray-400">⇅</span>
                        </th>
                        <th class="py-2.5 px-3.5 cursor-pointer hover:text-[#700B1A] transition" onclick="sortTable(5)">
                            ROLE <span class="text-gray-400">⇅</span>
                        </th>
                        <th class="py-2.5 px-3.5">LANE</th>
                        <th class="py-2.5 px-3.5">SPECIALITY</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F3E8E8]" id="statsTbody">
                    @foreach($heroRows as $idx => $row)
                        <tr class="hover:bg-[#FAF8F8]/80 transition stat-table-row"
                            data-name="{{ strtolower($row['name']) }}"
                            data-role="{{ strtolower($row['role']) }}"
                            data-lane="{{ strtolower(implode(' ', (array)$row['lane'])) }}"
                            data-spec="{{ strtolower(implode(' ', (array)$row['speciality'])) }}"
                            data-wr="{{ $row['wr'] }}"
                            data-pr="{{ $row['pick_rate'] }}"
                            data-br="{{ $row['ban_rate'] }}">
                            
                            <!-- Index -->
                            <td class="py-2 px-3 text-center font-bold text-gray-400 text-xs align-middle">
                                {{ $idx + 1 }}
                            </td>

                            <!-- Hero Name with Avatar Box -->
                            <td class="py-2 px-3.5 align-middle">
                                <div class="flex items-center gap-2.5">
                                    @if(!empty($row['portrait']))
                                        <img src="{{ $row['portrait'] }}" 
                                             alt="{{ $row['name'] }}" 
                                             class="w-7 h-7 rounded-full object-cover border border-[#F3E8E8] flex-shrink-0"
                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}&background=700B1A&color=fff';">
                                    @else
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center font-black text-xs shadow-sm flex-shrink-0 {{ $row['initial_bg'] }}">
                                            {{ $row['initial'] }}
                                        </div>
                                    @endif
                                    <span class="font-extrabold text-[#18181B] text-xs leading-none">
                                        {{ $row['name'] }}
                                    </span>
                                </div>
                            </td>

                            <!-- Win Rate -->
                            <td class="py-2 px-3.5 font-mono align-middle">
                                <div class="flex flex-col justify-center leading-tight">
                                    @if($row['wr'] >= 58)
                                        <div class="font-extrabold text-emerald-600 text-xs leading-none">
                                            {{ number_format($row['wr'], 2) }}%
                                        </div>
                                        <div class="text-[8.5px] font-extrabold text-emerald-600 tracking-wider mt-0.5">HIGH</div>
                                    @elseif($row['wr'] >= 50)
                                        <div class="font-extrabold text-gray-700 text-xs leading-none">
                                            {{ number_format($row['wr'], 2) }}%
                                        </div>
                                        <div class="text-[8.5px] font-bold text-gray-500 tracking-wider mt-0.5">AVERAGE</div>
                                    @else
                                        <div class="font-extrabold text-red-600 text-xs leading-none">
                                            {{ number_format($row['wr'], 2) }}%
                                        </div>
                                        <div class="text-[8.5px] font-extrabold text-red-600 tracking-wider mt-0.5">LOW</div>
                                    @endif
                                </div>
                            </td>

                            <!-- Pick Rate -->
                            <td class="py-2 px-3.5 font-mono align-middle">
                                <div class="flex flex-col justify-center leading-tight">
                                    <div class="font-bold text-gray-800 text-xs leading-none">
                                        {{ number_format($row['pick_rate'], 2) }}%
                                    </div>
                                    <div class="text-[8.5px] font-semibold text-gray-400 uppercase tracking-wider mt-0.5">
                                        {{ $row['pick_tag'] }}
                                    </div>
                                </div>
                            </td>

                            <!-- Ban Rate -->
                            <td class="py-2 px-3.5 font-mono align-middle">
                                <div class="flex flex-col justify-center leading-tight">
                                    @if($row['ban_rate'] >= 50)
                                        <div class="font-extrabold text-red-600 text-xs leading-none">
                                            {{ number_format($row['ban_rate'], 2) }}%
                                        </div>
                                        <div class="text-[8.5px] font-extrabold text-red-600 uppercase tracking-wider mt-0.5">
                                            OFTEN BANNED
                                        </div>
                                    @elseif($row['ban_rate'] >= 20)
                                        <div class="font-bold text-gray-700 text-xs leading-none">
                                            {{ number_format($row['ban_rate'], 2) }}%
                                        </div>
                                        <div class="text-[8.5px] font-semibold text-gray-400 uppercase tracking-wider mt-0.5">
                                            MODERATE
                                        </div>
                                    @else
                                        <div class="font-bold text-gray-700 text-xs leading-none">
                                            {{ number_format($row['ban_rate'], 2) }}%
                                        </div>
                                        <div class="text-[8.5px] font-semibold text-gray-400 uppercase tracking-wider mt-0.5">
                                            LOW
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="py-2 px-3.5 text-gray-700 font-medium text-xs align-middle">
                                <span class="leading-none">{{ $row['role'] }}</span>
                            </td>

                            <!-- Lane Pills -->
                            <td class="py-2 px-3.5 align-middle">
                                <div class="flex items-center gap-1 flex-wrap">
                                    @foreach((array)$row['lane'] as $l)
                                        <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-600 text-[10px] font-semibold px-2 py-0.5 rounded leading-tight">
                                            {{ $l }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Speciality Pills -->
                            <td class="py-2 px-3.5 align-middle">
                                <div class="flex items-center gap-1 flex-wrap">
                                    @foreach((array)$row['speciality'] as $s)
                                        <span class="bg-[#FAF8F8] border border-[#F3E8E8] text-gray-600 text-[10px] font-semibold px-2 py-0.5 rounded leading-tight">
                                            {{ $s }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- 3.5. PAGINATION FOOTER BAR (Limit default 10 per page, Next / Prev) -->
        <div class="flex items-center justify-between flex-wrap gap-3 pt-3.5 border-t border-[#F3E8E8] text-xs">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 font-medium">Tampilkan:</span>
                <select id="statsPerPageSelect" onchange="changeStatsPerPage(this.value)" class="bg-[#FAF8F8] border border-[#F3E8E8] rounded-lg px-2.5 py-1 text-xs font-bold text-[#18181B] focus:outline-none focus:border-[#700B1A] cursor-pointer">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="all">Semua</option>
                </select>
                <span class="text-gray-300">|</span>
                <div class="text-gray-600 font-semibold" id="statsPaginationInfo">
                    Menampilkan <span class="font-extrabold text-[#700B1A]" id="statsPageStart">1</span> - <span class="font-extrabold text-[#700B1A]" id="statsPageEnd">10</span> dari <span class="font-extrabold text-[#18181B]" id="statsTotalRows">0</span> hero
                </div>
            </div>

            <!-- Page Buttons (Prev, 1, 2, 3..., Next) -->
            <div class="flex items-center gap-1.5" id="statsPaginationButtons">
                <!-- Dynamically generated via JS -->
            </div>
        </div>
    </div>

    <!-- 4. THREE BOTTOM INSIGHT CARDS (COMPACT & PROPORTIONAL) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
        
        <!-- Insight Card 1: Power Spike Insight -->
        <div class="card-custom p-3.5 sm:p-4 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-5 h-5 rounded-md bg-[#FCECEE] text-[#700B1A] flex items-center justify-center font-black text-xs">!</span>
                    <h4 class="font-extrabold text-xs sm:text-sm text-[#18181B]">Power Spike Insight</h4>
                </div>
                <p class="text-[11px] text-gray-600 leading-snug">
                    Harith dan Karrie mencatat 70%+ win rate dominan ketika dipasangkan dengan setup roamer CC keras seperti Tigreal atau Baxia.
                </p>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-gray-400 pt-2.5 mt-2 border-t border-[#FAF0F1]">
                <span class="uppercase tracking-wider">FASE DRAFT 1-3</span>
                <a href="{{ route('draft.analyzer') }}" class="text-[#700B1A] hover:underline flex items-center gap-1 font-extrabold">
                    <span>LIHAT DETAIL &gt;</span>
                </a>
            </div>
        </div>

        <!-- Insight Card 2: Priority Permabans -->
        <div class="card-custom p-3.5 sm:p-4 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-5 h-5 rounded-md bg-[#FCECEE] text-[#700B1A] flex items-center justify-center font-black text-xs">⊘</span>
                    <h4 class="font-extrabold text-xs sm:text-sm text-[#18181B]">Priority Permabans</h4>
                </div>
                <p class="text-[11px] text-gray-600 leading-snug">
                    Ban rate 91.3% Harith menjadikannya ancaman mutlak di patch 1.9.14, memaksa tim sisi merah merelakan first phase bans.
                </p>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-gray-400 pt-2.5 mt-2 border-t border-[#FAF0F1]">
                <span class="uppercase tracking-wider">BAN RATE TINGGI</span>
                <a href="{{ route('draft.analyzer') }}" class="text-[#700B1A] hover:underline flex items-center gap-1 font-extrabold">
                    <span>SIMULASI DRAFT &gt;</span>
                </a>
            </div>
        </div>

        <!-- Insight Card 3: Surprise Pocket Picks -->
        <div class="card-custom p-3.5 sm:p-4 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-5 h-5 rounded-md bg-[#ECFDF5] text-emerald-700 flex items-center justify-center font-black text-xs">✪</span>
                    <h4 class="font-extrabold text-xs sm:text-sm text-[#18181B]">Surprise Pocket Picks</h4>
                </div>
                <p class="text-[11px] text-gray-600 leading-snug">
                    Aulus dan Rafaela mempertahankan tingkat kemenangan 59%+ meskipun pick rate di bawah 2%, sangat efektif sebagai counter-draft situasional.
                </p>
            </div>
            <div class="flex items-center justify-between text-[10px] font-bold text-gray-400 pt-2.5 mt-2 border-t border-[#FAF0F1]">
                <span class="uppercase tracking-wider">EFFICIENCY RATING</span>
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
    let statsPerPage = 10;

    function filterStatsTable() {
        const query = (document.getElementById('statSearchInput').value || '').toLowerCase().trim();
        const role = (document.getElementById('roleSelect').value || '').toLowerCase().trim();
        const lane = (document.getElementById('laneSelect').value || '').toLowerCase().trim();
        const spec = (document.getElementById('specSelect').value || '').toLowerCase().trim();

        const rows = document.querySelectorAll('.stat-table-row');
        rows.forEach(row => {
            const nameText = row.dataset.name || '';
            const roleText = row.dataset.role || '';
            const laneText = row.dataset.lane || '';
            const specText = row.dataset.spec || '';

            const matchQuery = !query || nameText.includes(query);
            const matchRole = !role || roleText.includes(role);
            const matchLane = !lane || laneText.includes(lane);
            const matchSpec = !spec || specText.includes(spec);

            row.dataset.matched = (matchQuery && matchRole && matchLane && matchSpec) ? 'true' : 'false';
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
        document.getElementById('specSelect').value = '';
        filterStatsTable();
    }

    function sortTable(colIndex, isNumeric = false) {
        const tbody = document.getElementById('statsTbody');
        const rows = Array.from(tbody.querySelectorAll('.stat-table-row'));
        const isAsc = tbody.dataset.sortOrder !== 'asc';
        tbody.dataset.sortOrder = isAsc ? 'asc' : 'desc';

        rows.sort((a, b) => {
            let valA, valB;
            if (colIndex === 1) { // Name
                valA = a.dataset.name;
                valB = b.dataset.name;
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (colIndex === 2) { // Win Rate
                valA = parseFloat(a.dataset.wr) || 0;
                valB = parseFloat(b.dataset.wr) || 0;
            } else if (colIndex === 3) { // Pick Rate
                valA = parseFloat(a.dataset.pr) || 0;
                valB = parseFloat(b.dataset.pr) || 0;
            } else if (colIndex === 4) { // Ban Rate
                valA = parseFloat(a.dataset.br) || 0;
                valB = parseFloat(b.dataset.br) || 0;
            } else if (colIndex === 5) { // Role
                valA = a.dataset.role;
                valB = b.dataset.role;
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            }
            return isAsc ? (valA - valB) : (valB - valA);
        });

        rows.forEach(row => tbody.appendChild(row));
        updateStatsPagination();
    }

    function exportTableToCSV(filename) {
        const rows = document.querySelectorAll('#statsTable tr');
        let csv = [];
        
        rows.forEach(row => {
            if (row.style.display === 'none') return;
            let rowData = [];
            row.querySelectorAll('th, td').forEach(cell => {
                let text = cell.innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/\s+/g, ' ').trim();
                rowData.push('"' + text.replace(/"/g, '""') + '"');
            });
            csv.push(rowData.join(','));
        });

        const csvContent = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv.join('\n'));
        const link = document.createElement('a');
        link.setAttribute('href', csvContent);
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Inisialisasi saat load
    document.addEventListener('DOMContentLoaded', function() {
        filterStatsTable();
    });
</script>
@endpush
@endsection

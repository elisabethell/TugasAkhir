@extends('layouts.app')

@section('title', 'List Hero Directory - MetaScout: Land of Dawn')

@section('content')
<div class="space-y-6">

    <!-- 1. PAGE TITLE & BADGE -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 pt-2">
        <div>
            <div class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-[#700B1A] mb-1">
                <span class="w-2 h-2 rounded-full bg-[#700B1A]"></span>
                <span>TOURNAMENT DATABASE • PATCH 1.9.14</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-[#18181B] tracking-tight">
                List Hero Directory
            </h1>
        </div>

        <div class="flex items-center gap-2 text-xs font-bold text-gray-700 bg-white border border-[#F3E8E8] rounded-full px-4 py-1.5 shadow-sm self-start sm:self-auto">
            <svg class="w-4 h-4 text-[#700B1A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="tracking-wider uppercase text-[10px] text-gray-500 font-semibold">TOTAL:</span>
            <span class="font-extrabold text-[#18181B]" id="header-total-count">{{ count($heroes) ?: 124 }}</span>
        </div>
    </div>

    <!-- 2. SEARCH BAR -->
    <div class="relative">
        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </span>
        <input type="text" 
               id="heroSearchInput" 
               oninput="applyHeroFilters()" 
               placeholder="Search heroes by name or tactical role..." 
               class="w-full bg-white border border-[#F3E8E8] rounded-2xl py-3 pl-11 pr-4 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] shadow-sm transition">
    </div>

    <!-- 3. ROLE & LANE FILTER BUTTONS -->
    <div class="card-custom p-4 sm:p-5 space-y-3.5">
        <!-- Role Filter Row -->
        <div class="flex items-center gap-2.5 flex-wrap text-xs">
            <span class="w-16 font-extrabold text-[11px] text-gray-500 uppercase tracking-wider">ROLE:</span>
            <div class="flex items-center gap-1.5 flex-wrap" id="roleFilterButtons">
                <button type="button" onclick="setRoleFilter('all', this)" class="role-filter-btn px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white transition shadow-sm">All</button>
                <button type="button" onclick="setRoleFilter('tank', this)" class="role-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Tank</button>
                <button type="button" onclick="setRoleFilter('fighter', this)" class="role-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Fighter</button>
                <button type="button" onclick="setRoleFilter('assassin', this)" class="role-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Assassin</button>
                <button type="button" onclick="setRoleFilter('mage', this)" class="role-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Mage</button>
                <button type="button" onclick="setRoleFilter('marksman', this)" class="role-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Marksman</button>
                <button type="button" onclick="setRoleFilter('support', this)" class="role-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Support</button>
            </div>
        </div>

        <!-- Lane Filter Row -->
        <div class="flex items-center gap-2.5 flex-wrap text-xs">
            <span class="w-16 font-extrabold text-[11px] text-gray-500 uppercase tracking-wider">LANE:</span>
            <div class="flex items-center gap-1.5 flex-wrap" id="laneFilterButtons">
                <button type="button" onclick="setLaneFilter('all', this)" class="lane-filter-btn px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white transition shadow-sm">All</button>
                <button type="button" onclick="setLaneFilter('exp', this)" class="lane-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Exp Lane</button>
                <button type="button" onclick="setLaneFilter('jungle', this)" class="lane-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Jungle</button>
                <button type="button" onclick="setLaneFilter('mid', this)" class="lane-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Mid Lane</button>
                <button type="button" onclick="setLaneFilter('gold', this)" class="lane-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Gold Lane</button>
                <button type="button" onclick="setLaneFilter('roam', this)" class="lane-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition">Roam</button>
            </div>
        </div>
    </div>

    <!-- 4. FILTER SUMMARY STATUS -->
    <div class="flex items-center justify-between text-xs font-bold px-1 text-gray-500 flex-wrap gap-2">
        <div class="uppercase tracking-wider text-[11px]">
            <span>FILTERED VIEW:</span> 
            <span class="text-[#18181B]" id="filterStatusLabel">ALL ROLES • ALL LANES</span>
        </div>
        <div class="text-gray-400 font-semibold text-[11px]">
            Showing <span class="text-[#18181B] font-bold" id="visibleCounter">24</span> of <span class="text-[#18181B] font-bold" id="totalCounter">{{ count($heroes) ?: 124 }}</span> heroes
        </div>
    </div>

    <!-- 5. HERO GRID (4 COLUMNS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="heroesGrid">
        @forelse($heroes as $hero)
            <div class="hero-card bg-white border border-[#F3E8E8] rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-[#F8B4BD] transition flex flex-col justify-between"
                 data-name="{{ strtolower($hero->hero_name) }}"
                 data-role="{{ strtolower($hero->class) }}"
                 data-lane="{{ strtolower($hero->laning) }}"
                 data-spec="{{ strtolower(implode(' ', (array)$hero->specialties)) }}">
                
                <div class="flex items-start gap-3.5">
                    <!-- Avatar with Tag -->
                    <div class="relative flex-shrink-0">
                        <img src="{{ $hero->portrait }}" 
                             alt="{{ $hero->hero_name }}" 
                             class="w-12 h-12 rounded-full object-cover bg-gray-100 border border-[#F3E8E8] shadow-sm"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($hero->hero_name) }}&background=700B1A&color=fff';">
                        
                        <!-- Role Tag Pill Badge -->
                        <span class="absolute -bottom-1 -right-1 text-[9px] font-black px-1 py-0.2 rounded-full border shadow-sm {{ $hero->tag_color }}">
                            {{ $hero->tag }}
                        </span>
                    </div>

                    <!-- Hero Info -->
                    <div class="min-w-0 flex-1">
                        <h3 class="font-extrabold text-sm text-[#18181B] truncate leading-snug">
                            {{ $hero->hero_name }}
                        </h3>
                        <p class="text-[11px] font-medium text-gray-500 truncate mt-0.5">
                            {{ $hero->primary_class }} • {{ $hero->laning ?: 'Flex' }}
                        </p>
                    </div>
                </div>

                <!-- Specialties Tags -->
                <div class="flex items-center gap-1.5 mt-3 pt-2.5 border-t border-[#FAF0F1]">
                    @if(!empty($hero->specialties))
                        @foreach($hero->specialties as $sIdx => $spec)
                            @if($sIdx == 0)
                                <span class="bg-[#FDE8EB] text-[#700B1A] text-[10px] font-bold px-2 py-0.5 rounded-md">
                                    {{ $spec }}
                                </span>
                            @else
                                <span class="bg-[#F4F4F5] text-gray-700 text-[10px] font-medium px-2 py-0.5 rounded-md">
                                    {{ $spec }}
                                </span>
                            @endif
                        @endforeach
                    @else
                        <span class="bg-[#FDE8EB] text-[#700B1A] text-[10px] font-bold px-2 py-0.5 rounded-md">
                            Versatile
                        </span>
                    @endif
                </div>

            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-400 font-semibold">
                Tidak ada data hero yang ditemukan.
            </div>
        @endforelse
    </div>

    <!-- 6. LOAD MORE & DRAFT AI BUTTON -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-[#F3E8E8]">
        <div></div>

        <div class="flex items-center gap-3">
            <button type="button" 
                    id="loadMoreBtn" 
                    onclick="loadMoreHeroes()" 
                    class="bg-white hover:bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] text-xs font-bold uppercase tracking-wider px-5 py-2.5 rounded-full shadow-sm flex items-center gap-2 transition">
                <span>LOAD MORE HEROES</span>
                <span class="text-[11px] text-gray-400 font-semibold" id="loadMoreInfo">(SHOWING 24 OF {{ count($heroes) ?: 124 }})</span>
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <a href="{{ route('draft.analyzer') }}" 
               class="bg-[#700B1A] hover:bg-[#550713] text-white text-xs font-extrabold uppercase tracking-wider px-5 py-2.5 rounded-full shadow-sm flex items-center gap-2 transition">
                <span>Buka Draft AI</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>
    </div>

</div>

@push('scripts')
<script>
    let currentRole = 'all';
    let currentLane = 'all';
    let visibleLimit = 24;
    const cards = Array.from(document.querySelectorAll('.hero-card'));

    function setRoleFilter(role, btn) {
        currentRole = role;
        document.querySelectorAll('.role-filter-btn').forEach(b => {
            b.className = 'role-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition';
        });
        btn.className = 'role-filter-btn px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white transition shadow-sm';
        applyHeroFilters();
    }

    function setLaneFilter(lane, btn) {
        currentLane = lane;
        document.querySelectorAll('.lane-filter-btn').forEach(b => {
            b.className = 'lane-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF8F8] text-gray-700 border border-[#F3E8E8] hover:bg-[#FCECEE] hover:text-[#700B1A] transition';
        });
        btn.className = 'lane-filter-btn px-3 py-1 rounded-full text-xs font-bold bg-[#700B1A] text-white transition shadow-sm';
        applyHeroFilters();
    }

    function applyHeroFilters() {
        const query = (document.getElementById('heroSearchInput').value || '').toLowerCase().trim();
        let matched = [];

        cards.forEach(card => {
            const name = card.dataset.name || '';
            const role = card.dataset.role || '';
            const lane = card.dataset.lane || '';
            const spec = card.dataset.spec || '';

            const matchQuery = !query || name.includes(query) || role.includes(query) || lane.includes(query) || spec.includes(query);
            const matchRole = currentRole === 'all' || role.includes(currentRole);
            const matchLane = currentLane === 'all' || lane.includes(currentLane);

            if (matchQuery && matchRole && matchLane) {
                matched.push(card);
            } else {
                card.style.display = 'none';
            }
        });

        // Paginate display
        matched.forEach((card, idx) => {
            if (idx < visibleLimit) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });

        // Update counters
        const visibleCount = Math.min(matched.length, visibleLimit);
        document.getElementById('visibleCounter').textContent = visibleCount;
        document.getElementById('totalCounter').textContent = matched.length;
        document.getElementById('loadMoreInfo').textContent = `(SHOWING ${visibleCount} OF ${matched.length})`;

        // Update status label
        const roleText = currentRole === 'all' ? 'ALL ROLES' : currentRole.toUpperCase();
        const laneText = currentLane === 'all' ? 'ALL LANES' : currentLane.toUpperCase() + ' LANE';
        document.getElementById('filterStatusLabel').textContent = `${roleText} • ${laneText}`;

        // Toggle load more button
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (visibleLimit >= matched.length) {
            loadMoreBtn.style.display = 'none';
        } else {
            loadMoreBtn.style.display = 'inline-flex';
        }
    }

    function loadMoreHeroes() {
        visibleLimit += 24;
        applyHeroFilters();
    }

    // Run initial filter on page load
    document.addEventListener('DOMContentLoaded', () => {
        applyHeroFilters();
    });
</script>
@endpush
@endsection
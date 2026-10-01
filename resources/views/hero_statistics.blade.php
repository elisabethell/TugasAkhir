@extends('layouts.app')

@section('title', 'Statistik Pro Hero MWI 2026 - MetaScout: Land of Dawn')

@section('content')
<div class="space-y-6">

    <!-- HEADER & OVERALL STATS SUMMARY -->
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-cyan-950 text-cyan-400 text-xs font-semibold uppercase tracking-wider mb-2 border border-cyan-800">
                <span>🏆 Data Resmi Turnamen MWI X EWC 2026</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">
                Statistik Pro Hero & Meta Turnamen
            </h1>
            <p class="text-gray-400 text-sm mt-1 max-w-2xl">
                Distribusi Pick, Ban, Win Rate, dan Analisis Bias Sisi (Blue vs Red Side) dari seluruh 69 pertandingan turnamen internasional MLBB.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('matches') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 rounded-lg text-xs font-semibold transition">
                🏆 Lihat Riwayat Seri Match
            </a>
            <a href="{{ route('draft.analyzer') }}" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-black font-extrabold rounded-lg text-xs tracking-wider uppercase transition shadow-[0_0_15px_rgba(0,242,255,0.25)]">
                🎮 Rekomendasi Draft (RBR)
            </a>
        </div>
    </div>

    <!-- SIDE BIAS SUMMARY BAR (LIQUEPEDIA STYLE) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-custom p-4 bg-gray-900/60 flex items-center justify-between border-gray-800">
            <div>
                <span class="text-xs text-gray-400 font-semibold block uppercase">Total Pertandingan</span>
                <span class="text-2xl font-black text-white">{{ $summary['total_games'] }} Game</span>
            </div>
            <span class="text-3xl">🎮</span>
        </div>

        <div class="card-custom p-4 bg-blue-950/20 border-blue-900/40 flex items-center justify-between">
            <div>
                <span class="text-xs text-blue-400 font-semibold block uppercase">💙 Blue Side Dominance</span>
                <span class="text-2xl font-black text-blue-300">{{ $summary['blue_wins'] }}W - {{ $summary['blue_losses'] }}L</span>
                <span class="text-xs font-bold text-blue-400 block mt-0.5">Win Rate: {{ $summary['blue_wr'] }}%</span>
            </div>
            <div class="text-right">
                <span class="text-xs bg-blue-900/50 text-blue-300 px-2 py-1 rounded font-mono font-bold">42.03%</span>
            </div>
        </div>

        <div class="card-custom p-4 bg-red-950/20 border-red-900/40 flex items-center justify-between">
            <div>
                <span class="text-xs text-red-400 font-semibold block uppercase">❤️ Red Side Dominance</span>
                <span class="text-2xl font-black text-red-300">{{ $summary['red_wins'] }}W - {{ $summary['red_losses'] }}L</span>
                <span class="text-xs font-bold text-red-400 block mt-0.5">Win Rate: {{ $summary['red_wr'] }}%</span>
            </div>
            <div class="text-right">
                <span class="text-xs bg-red-900/50 text-red-300 px-2 py-1 rounded font-mono font-bold">57.97%</span>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="card-custom p-4 bg-[#121620] border-gray-800 flex justify-between items-center flex-wrap gap-4">
        <div class="flex-1 min-w-[240px]">
            <input type="text" id="stats-search" onkeyup="filterStatsTable()" placeholder="Cari nama hero (misal: Claude, Karrie, Esmeralda)..." 
                   class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3.5 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500">
        </div>
        <div class="flex items-center gap-1.5 text-xs flex-wrap">
            <span class="text-gray-400 mr-1 font-semibold">Filter Role:</span>
            <button type="button" onclick="filterStatsByClass('all')" class="class-filter-btn px-2.5 py-1 rounded bg-cyan-600 text-white font-bold">Semua</button>
            <button type="button" onclick="filterStatsByClass('tank')" class="class-filter-btn px-2.5 py-1 rounded bg-gray-800 text-gray-300 hover:bg-gray-700">Tank</button>
            <button type="button" onclick="filterStatsByClass('fighter')" class="class-filter-btn px-2.5 py-1 rounded bg-gray-800 text-gray-300 hover:bg-gray-700">Fighter</button>
            <button type="button" onclick="filterStatsByClass('assassin')" class="class-filter-btn px-2.5 py-1 rounded bg-gray-800 text-gray-300 hover:bg-gray-700">Assassin</button>
            <button type="button" onclick="filterStatsByClass('mage')" class="class-filter-btn px-2.5 py-1 rounded bg-gray-800 text-gray-300 hover:bg-gray-700">Mage</button>
            <button type="button" onclick="filterStatsByClass('marksman')" class="class-filter-btn px-2.5 py-1 rounded bg-gray-800 text-gray-300 hover:bg-gray-700">Marksman</button>
            <button type="button" onclick="filterStatsByClass('support')" class="class-filter-btn px-2.5 py-1 rounded bg-gray-800 text-gray-300 hover:bg-gray-700">Support</button>
        </div>
    </div>

    <!-- LIQUEPEDIA-STYLE TABLE -->
    <div class="card-custom bg-[#121620] border-gray-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse whitespace-nowrap" id="stats-table">
                <thead>
                    <tr class="bg-[#1a2233] text-gray-300 border-b border-gray-700 font-bold uppercase text-[11px]">
                        <th rowspan="2" class="p-3 text-center border-r border-gray-800 w-12">#</th>
                        <th rowspan="2" class="p-3 border-r border-gray-800 min-w-[160px]">Hero</th>
                        <th colspan="5" class="p-2 text-center bg-gray-800/60 border-r border-gray-800">Total Picks</th>
                        <th colspan="4" class="p-2 text-center bg-blue-950/40 text-blue-300 border-r border-gray-800">Blue Side</th>
                        <th colspan="4" class="p-2 text-center bg-red-950/40 text-red-300 border-r border-gray-800">Red Side</th>
                        <th colspan="2" class="p-2 text-center bg-gray-800/60 border-r border-gray-800">Bans</th>
                        <th colspan="2" class="p-2 text-center bg-cyan-950/40 text-cyan-300">Picks & Bans</th>
                    </tr>
                    <tr class="bg-[#151c2a] text-gray-400 border-b border-gray-800 text-[10px] font-semibold">
                        <!-- Picks -->
                        <th class="p-2 text-center">Σ</th>
                        <th class="p-2 text-center text-emerald-400">W</th>
                        <th class="p-2 text-center text-red-400">L</th>
                        <th class="p-2 text-center">WR %</th>
                        <th class="p-2 text-center border-r border-gray-800">%T</th>
                        <!-- Blue -->
                        <th class="p-2 text-center">Σ</th>
                        <th class="p-2 text-center text-emerald-400">W</th>
                        <th class="p-2 text-center text-red-400">L</th>
                        <th class="p-2 text-center border-r border-gray-800">WR %</th>
                        <!-- Red -->
                        <th class="p-2 text-center">Σ</th>
                        <th class="p-2 text-center text-emerald-400">W</th>
                        <th class="p-2 text-center text-red-400">L</th>
                        <th class="p-2 text-center border-r border-gray-800">WR %</th>
                        <!-- Bans -->
                        <th class="p-2 text-center">Σ</th>
                        <th class="p-2 text-center border-r border-gray-800">%T</th>
                        <!-- Pick + Ban -->
                        <th class="p-2 text-center">Σ</th>
                        <th class="p-2 text-center">%T</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60" id="stats-tbody">
                    @php $rank = 1; @endphp
                    @foreach($stats as $heroKey => $s)
                        <tr class="hover:bg-[#182133] transition stat-row" 
                            data-name="{{ strtolower($s['name']) }}"
                            data-class="{{ strtolower($s['class']) }}">
                            <td class="p-2.5 text-center text-gray-500 font-mono border-r border-gray-800/60">{{ $rank++ }}</td>
                            <td class="p-2.5 border-r border-gray-800/60">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $s['portrait'] }}" 
                                         class="w-7 h-7 rounded-full object-cover border border-gray-700 bg-gray-800"
                                         alt="{{ $s['name'] }}"
                                         onerror="this.src='https://via.placeholder.com/28?text=H'">
                                    <div>
                                        <span class="font-bold text-gray-200 block leading-tight">{{ $s['name'] }}</span>
                                        <span class="text-[9px] text-gray-400">{{ $s['class'] }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Total Picks -->
                            <td class="p-2.5 text-center font-bold text-cyan-400">{{ $s['picks'] }}</td>
                            <td class="p-2.5 text-center text-emerald-400 font-medium">{{ $s['wins'] }}</td>
                            <td class="p-2.5 text-center text-red-400 font-medium">{{ $s['losses'] }}</td>
                            <td class="p-2.5 text-center font-bold {{ $s['wr'] >= 55 ? 'text-emerald-400 font-extrabold' : ($s['wr'] <= 45 && $s['picks'] > 0 ? 'text-red-400' : 'text-gray-300') }}">
                                {{ $s['picks'] > 0 ? number_format($s['wr'], 2) . '%' : '-' }}
                            </td>
                            <td class="p-2.5 text-center text-gray-400 border-r border-gray-800/60">{{ number_format($s['pick_rate'], 2) }}%</td>

                            <!-- Blue Side -->
                            <td class="p-2.5 text-center font-medium text-blue-300">{{ $s['blue_picks'] }}</td>
                            <td class="p-2.5 text-center text-emerald-400 font-medium">{{ $s['blue_wins'] }}</td>
                            <td class="p-2.5 text-center text-red-400 font-medium">{{ $s['blue_losses'] }}</td>
                            <td class="p-2.5 text-center border-r border-gray-800/60 font-semibold text-gray-300">
                                {{ $s['blue_picks'] > 0 ? number_format($s['blue_wr'], 2) . '%' : '-' }}
                            </td>

                            <!-- Red Side -->
                            <td class="p-2.5 text-center font-medium text-red-300">{{ $s['red_picks'] }}</td>
                            <td class="p-2.5 text-center text-emerald-400 font-medium">{{ $s['red_wins'] }}</td>
                            <td class="p-2.5 text-center text-red-400 font-medium">{{ $s['red_losses'] }}</td>
                            <td class="p-2.5 text-center border-r border-gray-800/60 font-semibold text-gray-300">
                                {{ $s['red_picks'] > 0 ? number_format($s['red_wr'], 2) . '%' : '-' }}
                            </td>

                            <!-- Bans -->
                            <td class="p-2.5 text-center font-bold text-amber-400">{{ $s['bans'] }}</td>
                            <td class="p-2.5 text-center text-gray-400 border-r border-gray-800/60">{{ number_format($s['ban_rate'], 2) }}%</td>

                            <!-- Contest -->
                            <td class="p-2.5 text-center font-extrabold text-white">{{ $s['contest_count'] }}</td>
                            <td class="p-2.5 text-center font-bold text-cyan-300">{{ number_format($s['contest_rate'], 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    let activeClassFilter = 'all';

    function filterStatsTable() {
        const query = document.getElementById('stats-search').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.stat-row');

        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const heroClass = row.getAttribute('data-class');

            const matchQuery = !query || name.includes(query);
            const matchClass = activeClassFilter === 'all' || heroClass.includes(activeClassFilter);

            if (matchQuery && matchClass) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function filterStatsByClass(cls) {
        activeClassFilter = cls;
        const buttons = document.querySelectorAll('.class-filter-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-cyan-600', 'text-white');
            btn.classList.add('bg-gray-800', 'text-gray-300');
            if (btn.innerText.toLowerCase().includes(cls) || (cls === 'all' && btn.innerText.toLowerCase().includes('semua'))) {
                btn.classList.add('bg-cyan-600', 'text-white');
                btn.classList.remove('bg-gray-800', 'text-gray-300');
            }
        });
        filterStatsTable();
    }
</script>
@endpush
@endsection

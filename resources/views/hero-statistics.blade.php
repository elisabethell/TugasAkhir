{{-- resources/views/hero-statistics.blade.php --}}
@extends('layouts.app')

@section('title', 'Hero Statistics - MLBB Analytics')

@section('content')
<div class="max-w-7xl mx-auto p-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white">📊 HERO STATISTICS</h1>
        <p class="text-gray-500 mt-1">Data based on Mythical Glory+ matches • Updated Feb 21, 2026</p>
    </div>
    
    <!-- Filters -->
    <div class="bg-[#161e2d] rounded-xl p-4 border border-gray-800 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2">
                <span class="text-gray-400 text-sm">Role:</span>
                <select id="role-filter" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-white">
                    <option value="all" {{ $role == 'all' ? 'selected' : '' }}>All Roles</option>
                    @foreach(['fighter', 'assassin', 'mage', 'marksman', 'tank', 'support'] as $r)
                        <option value="{{ $r }}" {{ $role == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <span class="text-gray-400 text-sm">Sort by:</span>
                <select id="sort-filter" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-white">
                    <option value="picks" {{ $sort == 'picks' ? 'selected' : '' }}>Most Picks</option>
                    <option value="win_rate" {{ $sort == 'win_rate' ? 'selected' : '' }}>Win Rate</option>
                    <option value="pick_rate" {{ $sort == 'pick_rate' ? 'selected' : '' }}>Pick Rate</option>
                    <option value="ban_rate" {{ $sort == 'ban_rate' ? 'selected' : '' }}>Ban Rate</option>
                </select>
            </div>
            
            <div class="ml-auto text-sm text-gray-500">
                Total Heroes: <span class="text-[#00f2ff] font-bold">{{ count($heroStats) }}</span>
            </div>
        </div>
    </div>
    
    <!-- Statistics Table -->
    <div class="bg-[#161e2d] rounded-xl border border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="text-left p-4 text-gray-400 text-sm">#</th>
                        <th class="text-left p-4 text-gray-400 text-sm">Hero</th>
                        <th class="text-left p-4 text-gray-400 text-sm">Role</th>
                        <th class="text-right p-4 text-gray-400 text-sm">Picks</th>
                        <th class="text-right p-4 text-gray-400 text-sm">Bans</th>
                        <th class="text-right p-4 text-gray-400 text-sm">Win Rate</th>
                        <th class="text-right p-4 text-gray-400 text-sm">Pick Rate</th>
                        <th class="text-right p-4 text-gray-400 text-sm">Ban Rate</th>
                        <th class="text-center p-4 text-gray-400 text-sm">Tier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($heroStats as $index => $stat)
                    <tr class="hover:bg-gray-800/30 transition">
                        <td class="p-4 text-gray-500">{{ $index + 1 }}</td>
                        <td class="p-4 font-semibold text-white">{{ $stat['hero'] }}</td>
                        <td class="p-4 text-gray-300">{{ $stat['role'] }}</td>
                        <td class="p-4 text-right text-white">{{ number_format($stat['picks']) }}</td>
                        <td class="p-4 text-right text-white">{{ number_format($stat['bans']) }}</td>
                        <td class="p-4 text-right {{ $stat['win_rate'] > 52 ? 'text-green-400' : ($stat['win_rate'] > 50 ? 'text-yellow-400' : 'text-red-400') }}">
                            {{ number_format($stat['win_rate'], 1) }}%
                        </td>
                        <td class="p-4 text-right text-[#00f2ff]">{{ number_format($stat['pick_rate'], 1) }}%</td>
                        <td class="p-4 text-right text-purple-400">{{ number_format($stat['ban_rate'], 1) }}%</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-1 rounded text-xs font-bold
                                @if($stat['tier'] == 'S') bg-red-500/20 text-red-400
                                @elseif($stat['tier'] == 'A') bg-orange-500/20 text-orange-400
                                @elseif($stat['tier'] == 'B') bg-yellow-500/20 text-yellow-400
                                @elseif($stat['tier'] == 'C') bg-blue-500/20 text-blue-400
                                @else bg-gray-500/20 text-gray-400
                                @endif">
                                {{ $stat['tier'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-gray-500">
                            No heroes found for the selected filter
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        @php
            $avgWinRate = collect($heroStats)->avg('win_rate');
            $totalPicks = collect($heroStats)->sum('picks');
            $totalBans = collect($heroStats)->sum('bans');
            $topTier = collect($heroStats)->where('tier', 'S')->count();
        @endphp
        
        <div class="bg-[#161e2d] rounded-lg p-4 border border-gray-800">
            <div class="text-sm text-gray-500">Average Win Rate</div>
            <div class="text-2xl font-bold text-green-400">{{ number_format($avgWinRate, 1) }}%</div>
        </div>
        <div class="bg-[#161e2d] rounded-lg p-4 border border-gray-800">
            <div class="text-sm text-gray-500">Total Picks</div>
            <div class="text-2xl font-bold text-[#00f2ff]">{{ number_format($totalPicks) }}</div>
        </div>
        <div class="bg-[#161e2d] rounded-lg p-4 border border-gray-800">
            <div class="text-sm text-gray-500">Total Bans</div>
            <div class="text-2xl font-bold text-purple-400">{{ number_format($totalBans) }}</div>
        </div>
        <div class="bg-[#161e2d] rounded-lg p-4 border border-gray-800">
            <div class="text-sm text-gray-500">S-Tier Heroes</div>
            <div class="text-2xl font-bold text-red-400">{{ $topTier }}</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('role-filter').addEventListener('change', function() {
    const role = this.value;
    const sort = document.getElementById('sort-filter').value;
    window.location.href = `{{ route('hero.statistics') }}?role=${role}&sort=${sort}`;
});

document.getElementById('sort-filter').addEventListener('change', function() {
    const sort = this.value;
    const role = document.getElementById('role-filter').value;
    window.location.href = `{{ route('hero.statistics') }}?role=${role}&sort=${sort}`;
});
</script>
@endpush
@endsection
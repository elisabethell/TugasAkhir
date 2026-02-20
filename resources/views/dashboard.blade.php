{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'MLBB Meta Analytics Dashboard')

@section('content')
<div class="max-w-7xl mx-auto p-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-black bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
                MLBB Meta Analytics
            </h1>
            <p class="text-gray-500 mt-1">Patch {{ $currentPatch }} • Last updated {{ $lastUpdated }}</p>
        </div>
        <div class="flex space-x-2">
            <span class="px-3 py-1 bg-[#00f2ff]/20 text-[#00f2ff] rounded-full text-sm">Mythical Glory+</span>
            <span class="px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-sm">15,420 Matches</span>
        </div>
    </div>

    <!-- Global Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-[#161e2d] rounded-lg p-4 border border-gray-800">
            <div class="text-sm text-gray-500">Total Heroes</div>
            <div class="text-2xl font-bold text-white">{{ $globalStats['total_heroes'] }}</div>
        </div>
        <div class="bg-[#161e2d] rounded-lg p-4 border border-gray-800">
            <div class="text-sm text-gray-500">Matches</div>
            <div class="text-2xl font-bold text-white">{{ number_format($globalStats['total_matches']) }}</div>
        </div>
        <div class="bg-[#161e2d] rounded-lg p-4 border border-gray-800">
            <div class="text-sm text-gray-500">Avg Game</div>
            <div class="text-2xl font-bold text-white">{{ $globalStats['avg_game_duration'] }}</div>
        </div>
        <div class="bg-[#161e2d] rounded-lg p-4 border border-gray-800">
            <div class="text-sm text-gray-500">Top Role</div>
            <div class="text-2xl font-bold text-[#00f2ff]">{{ $globalStats['most_picked_role'] }}</div>
        </div>
        <div class="bg-[#161e2d] rounded-lg p-4 border border-gray-800">
            <div class="text-sm text-gray-500">Most Banned</div>
            <div class="text-2xl font-bold text-red-400">{{ $globalStats['most_banned_hero'] }}</div>
        </div>
        <div class="bg-[#161e2d] rounded-lg p-4 border border-gray-800">
            <div class="text-sm text-gray-500">Best WR</div>
            <div class="text-2xl font-bold text-green-400">{{ $globalStats['highest_win_rate'] }}</div>
        </div>
    </div>

    <!-- Hero Stats Table -->
    <div class="bg-[#161e2d] rounded-xl border border-gray-800 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center">
            <h2 class="text-xl font-bold">🔥 META HEROES</h2>
            <a href="{{ route('hero.statistics') }}" class="text-[#00f2ff] text-sm hover:underline">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800/50">
                    <tr>
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
                    @foreach($heroStats as $stat)
                    <tr class="hover:bg-gray-800/30">
                        <td class="p-4 font-semibold">{{ $stat['hero'] }}</td>
                        <td class="p-4">{{ $stat['role'] }}</td>
                        <td class="p-4 text-right">{{ number_format($stat['picks']) }}</td>
                        <td class="p-4 text-right">{{ number_format($stat['bans']) }}</td>
                        <td class="p-4 text-right {{ $stat['win_rate'] > 52 ? 'text-green-400' : 'text-yellow-400' }}">{{ $stat['win_rate'] }}%</td>
                        <td class="p-4 text-right text-[#00f2ff]">{{ $stat['pick_rate'] }}%</td>
                        <td class="p-4 text-right text-purple-400">{{ $stat['ban_rate'] }}%</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-1 rounded text-xs font-bold
                                @if($stat['tier'] == 'S') bg-red-500/20 text-red-400
                                @elseif($stat['tier'] == 'A') bg-orange-500/20 text-orange-400
                                @else bg-gray-500/20 text-gray-400
                                @endif">
                                {{ $stat['tier'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Meta Insights -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-[#161e2d] rounded-xl p-6 border border-gray-800">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <span class="w-1 h-6 bg-[#00f2ff] rounded-full mr-3"></span>
                META INSIGHTS
            </h2>
            <div class="space-y-4">
                @foreach($metaInsights as $insight)
                <div class="flex items-start space-x-3 p-3 bg-gray-800/30 rounded-lg">
                    <span class="text-2xl">{{ $insight['icon'] }}</span>
                    <div>
                        <div class="font-semibold text-sm">{{ $insight['type'] }}</div>
                        <p class="text-sm text-gray-400">{{ $insight['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <div class="bg-[#161e2d] rounded-xl p-6 border border-gray-800">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <span class="w-1 h-6 bg-purple-400 rounded-full mr-3"></span>
                TOP HEROES BY ROLE
            </h2>
            <div class="grid grid-cols-2 gap-4">
                @foreach($topByRole as $role => $hero)
                @if($hero)
                <div class="bg-gray-800/30 p-3 rounded-lg">
                    <div class="text-sm text-gray-500">{{ $role }}</div>
                    <div class="font-bold text-[#00f2ff]">{{ $hero->nama_hero }}</div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
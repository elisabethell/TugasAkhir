@extends('layouts.app')

@section('content')
<div class="space-y-6 text-gray-300">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
        <div>
            <h1 class="text-4xl font-black italic tracking-tighter text-white leading-none">
                MLBB PRO MATCH ANALYTICS
            </h1>
            <p class="text-[10px] font-mono tracking-[0.3em] text-cyan-500/60 mt-2 uppercase">
                Season 31 • Real-time Data via Liquipedia • MPL ID S13 + MDL ID S9
            </p>
        </div>
        <div class="flex flex-col items-end">
            <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1 font-mono">Current Patch</span>
            <div class="px-4 py-2 bg-orange-500/10 border border-orange-500/40 rounded text-orange-500 font-black tracking-tighter text-xl shadow-[0_0_15px_rgba(249,115,22,0.1)]">
                v1.9.58 — Season 31
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gray-900/40 border border-gray-800 p-5 rounded-sm border-l-4 border-l-cyan-500">
            <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Total Matches Analyzed</p>
            <div class="text-4xl font-black text-cyan-400 leading-none">1,284</div>
            <p class="text-[10px] text-green-400 mt-2 font-mono tracking-tighter">↑ +47 THIS WEEK</p>
        </div>

        <div class="bg-gray-900/40 border border-gray-800 p-5 rounded-sm">
            <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Unique Heroes Played</p>
            <div class="text-4xl font-black text-white leading-none">68</div>
            <p class="text-[10px] text-gray-600 mt-2 font-mono">OF 123 TOTAL HEROES</p>
        </div>

        <div class="bg-gray-900/40 border border-gray-800 p-5 rounded-sm">
            <p class="text-[10px] uppercase font-bold text-gray-500 mb-1 text-orange-400">Avg Match Duration</p>
            <div class="text-4xl font-black text-orange-500 leading-none tracking-tighter">14:32</div>
            <p class="text-[10px] text-red-500 mt-2 font-mono tracking-tighter">↓ 1:12 VS PREV PATCH</p>
        </div>

        <div class="bg-gray-900/40 border border-gray-800 p-5 rounded-sm border-r-4 border-r-green-500 text-right">
            <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Data Freshness</p>
            <div class="text-2xl font-black text-green-400 leading-none font-mono">03/06/2026 05:52</div>
            <p class="text-[10px] text-gray-600 mt-3 font-mono">AUTO-REFRESH EVERY 5 MIN</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <div class="lg:col-span-7 bg-gray-900/20 border border-gray-800 rounded-sm">
            <div class="flex justify-between items-center p-4 border-b border-gray-800 bg-gray-800/30">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-4 bg-cyan-500"></div>
                    <h2 class="text-xs font-black uppercase tracking-widest text-white">Most Picked Heroes</h2>
                </div>
                <div class="flex gap-2">
                    <button class="text-[9px] bg-cyan-500/20 border border-cyan-500/50 text-cyan-400 px-2 py-1 rounded-sm uppercase font-bold">All Tournaments</button>
                    <button class="text-[9px] text-gray-500 px-2 py-1 uppercase font-bold hover:text-gray-300">MPL ID S13</button>
                    <button class="text-[9px] text-gray-500 px-2 py-1 uppercase font-bold hover:text-gray-300">By Role</button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                @php
                    $heroes = [
                        ['name' => 'Nolan', 'role' => 'ASSASSIN · EXP', 'pr' => 78.5, 'count' => '1,008'],
                        ['name' => 'Arlott', 'role' => 'FIGHTER · EXP', 'pr' => 72.3, 'count' => '928'],
                        ['name' => 'Cici', 'role' => 'FIGHTER · EXP', 'pr' => 67.7, 'count' => '869'],
                        ['name' => 'Minotaur', 'role' => 'TANK · ROAM', 'pr' => 64.6, 'count' => '829'],
                    ];
                @endphp

                @foreach($heroes as $hero)
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between items-end mb-1 text-[11px] font-bold">
                        <div class="flex items-baseline gap-2">
                            <span class="text-white text-sm tracking-tight">{{ $hero['name'] }}</span>
                            <span class="text-gray-600 font-mono text-[9px] uppercase">{{ $hero['role'] }}</span>
                        </div>
                        <div class="flex gap-4 items-baseline">
                            <span class="text-cyan-400 font-mono text-sm italic">{{ $hero['pr'] }}%</span>
                            <span class="text-gray-700 font-mono text-[9px]">{{ $hero['count'] }} / 1,284</span>
                        </div>
                    </div>
                    <div class="h-[6px] w-full bg-gray-800 rounded-full overflow-hidden flex">
                        <div class="h-full bg-gradient-to-r from-cyan-600 to-cyan-400 shadow-[0_0_8px_rgba(6,182,212,0.4)] rounded-full" style="width: {{ $hero['pr'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-5 space-y-6">
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-gray-900/40 border border-gray-800 p-3 rounded-sm text-center">
                    <p class="text-[9px] text-gray-500 uppercase font-bold tracking-tighter">Tournaments</p>
                    <p class="text-xl font-black text-cyan-500">2</p>
                    <p class="text-[8px] text-gray-700 italic">Active Now</p>
                </div>
                <div class="bg-gray-900/40 border border-gray-800 p-3 rounded-sm text-center">
                    <p class="text-[9px] text-gray-500 uppercase font-bold tracking-tighter text-white">Teams</p>
                    <p class="text-xl font-black text-white">24</p>
                    <p class="text-[8px] text-gray-700 italic font-mono">Competing</p>
                </div>
                <div class="bg-gray-900/40 border border-gray-800 p-3 rounded-sm text-center border-r-orange-500/50 border-r-2">
                    <p class="text-[9px] text-gray-500 uppercase font-bold tracking-tighter">Regions</p>
                    <p class="text-xl font-black text-orange-500 leading-none">1</p>
                    <p class="text-[8px] text-gray-700 italic">Indonesia</p>
                </div>
            </div>

            <div class="bg-gray-900/20 border border-gray-800 rounded-sm overflow-hidden">
                <div class="flex items-center gap-3 p-4 border-b border-gray-800 bg-gray-800/30">
                    <div class="w-1 h-4 bg-cyan-500"></div>
                    <h2 class="text-xs font-black uppercase tracking-widest text-white">Ongoing Tournaments</h2>
                </div>
                <div class="p-4 space-y-3 font-mono">
                    <div class="p-3 bg-gray-900/60 border border-gray-800 rounded flex justify-between items-center group hover:border-cyan-500/30 transition-all cursor-pointer">
                        <div>
                            <h4 class="text-xs font-bold text-gray-300">MPL ID Season 13</h4>
                            <p class="text-[9px] text-gray-600 uppercase mt-1 italic">Regular Season — Week 3 of 8</p>
                            <span class="text-[8px] bg-green-500/10 text-green-500 px-1 mt-2 inline-block rounded">● LIVE</span>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] text-gray-500">Mar 8 — May 19</p>
                            <p class="text-[9px] text-cyan-500 font-bold mt-1 uppercase">Next: Mar 7</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        
        <div class="bg-gray-900/20 border border-gray-800 rounded-sm">
            <div class="flex justify-between items-center p-4 border-b border-gray-800 bg-gray-800/30">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-4 bg-cyan-500"></div>
                    <h2 class="text-xs font-black uppercase tracking-widest text-white">Win Rate Leaders</h2>
                </div>
                <span class="text-[9px] text-gray-600 font-mono uppercase">Min. 20 Games</span>
            </div>
            
            <div class="p-4 space-y-4">
                <div class="grid grid-cols-5 text-[9px] font-bold text-gray-600 uppercase mb-2 px-1">
                    <div class="col-span-2">Hero</div>
                    <div class="text-center">WR%</div>
                    <div class="text-center">GP</div>
                    <div class="text-right">Rate</div>
                </div>

                @php
                    $wrLeaders = [
                        ['name' => 'Diggie', 'role' => 'Support · Roam', 'wr' => 68.4, 'gp' => 38, 'color' => 'bg-green-500'],
                        ['name' => 'Nolan', 'role' => 'Assassin · EXP', 'wr' => 63.7, 'gp' => 91, 'color' => 'bg-green-500'],
                        ['name' => 'Chip', 'role' => 'Tank · Roam', 'wr' => 57.2, 'gp' => 54, 'color' => 'bg-cyan-500'],
                        ['name' => 'Arlott', 'role' => 'Fighter · EXP', 'wr' => 53.1, 'gp' => 84, 'color' => 'bg-cyan-500'],
                        ['name' => 'Joy', 'role' => 'Assassin · JG', 'wr' => 38.6, 'gp' => 22, 'color' => 'bg-red-500'],
                    ];
                @endphp

                @foreach($wrLeaders as $hero)
                <div class="grid grid-cols-5 items-center px-1 group">
                    <div class="col-span-2">
                        <p class="text-xs font-bold text-white group-hover:text-cyan-400 transition-colors">{{ $hero['name'] }}</p>
                        <p class="text-[8px] text-gray-600 uppercase leading-none">{{ $hero['role'] }}</p>
                    </div>
                    <div class="text-xs font-black text-center {{ $hero['wr'] > 60 ? 'text-green-400' : ($hero['wr'] < 45 ? 'text-red-500' : 'text-gray-300') }}">
                        {{ $hero['wr'] }}%
                    </div>
                    <div class="text-xs font-mono text-gray-500 text-center">{{ $hero['gp'] }}</div>
                    <div class="flex justify-end">
                        <div class="h-1 w-12 bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full {{ $hero['color'] }}" style="width: {{ $hero['wr'] }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-gray-900/20 border border-gray-800 rounded-sm">
            <div class="flex justify-between items-center p-4 border-b border-gray-800 bg-gray-800/30">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-4 bg-cyan-500"></div>
                    <h2 class="text-xs font-black uppercase tracking-widest text-white">Meta Insights</h2>
                </div>
                <span class="text-[8px] text-gray-600 font-mono">AI-generated · patch v1.9.58</span>
            </div>

            <div class="p-4 space-y-3">
                <div class="flex gap-3 p-3 bg-gray-900/40 border-l-2 border-cyan-500 rounded-sm">
                    <span class="text-[8px] font-black bg-cyan-500 text-black px-1 h-fit py-0.5 rounded-sm">META</span>
                    <p class="text-[10px] leading-relaxed text-gray-400">
                        Jungle Emblem <span class="text-white font-bold">'Swift'</span> adoption up +32% among Assassin junglers since v1.9.58.
                    </p>
                </div>
                <div class="flex gap-3 p-3 bg-gray-900/40 border-l-2 border-orange-500 rounded-sm">
                    <span class="text-[8px] font-black bg-orange-500 text-black px-1 h-fit py-0.5 rounded-sm uppercase">Buff</span>
                    <p class="text-[10px] leading-relaxed text-gray-400">
                        <span class="text-white font-bold">Diggie</span> win rate surged +15% post-shield buff. Now dominant in roam meta.
                    </p>
                </div>
                <div class="flex gap-3 p-3 bg-gray-900/40 border-l-2 border-red-500 rounded-sm">
                    <span class="text-[8px] font-black bg-red-500 text-white px-1 h-fit py-0.5 rounded-sm uppercase">Ban</span>
                    <p class="text-[10px] leading-relaxed text-gray-400">
                        <span class="text-white font-bold">Mathilda & Joy</span> remain tier-1 threats. Expected in blue side first rotation.
                    </p>
                </div>
                <div class="flex gap-3 p-3 bg-gray-900/40 border-l-2 border-green-500 rounded-sm">
                    <span class="text-[8px] font-black bg-green-500 text-black px-1 h-fit py-0.5 rounded-sm uppercase">Pick</span>
                    <p class="text-[10px] leading-relaxed text-gray-400">
                        <span class="text-white font-bold">EXP lane fighters</span> dominate — 3 of top 5 picks are fighter/EXP compositions.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-gray-900/20 border border-gray-800 rounded-sm">
                <div class="flex justify-between items-center p-4 border-b border-gray-800 bg-gray-800/30">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-4 bg-cyan-500"></div>
                        <h2 class="text-xs font-black uppercase tracking-widest text-white">Upcoming Events</h2>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    <div class="p-3 bg-gray-800/20 border border-gray-800 rounded-sm group hover:border-orange-500/50 transition-all">
                        <div class="flex justify-between items-start">
                            <h4 class="text-[11px] font-bold text-gray-300">MPL PH Season 13</h4>
                            <span class="text-[10px] text-orange-500 font-mono">Mar 15, 2026</span>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-[8px] text-gray-600 uppercase font-bold tracking-widest">Regular Season</span>
                            <span class="text-[9px] text-orange-400/80 font-bold uppercase italic">● Upcoming</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900/20 border border-gray-800 rounded-sm">
                <div class="flex justify-between items-center p-4 border-b border-gray-800 bg-gray-800/30">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-4 bg-red-500"></div>
                        <h2 class="text-xs font-black uppercase tracking-widest text-white">Priority Bans</h2>
                    </div>
                    <span class="text-[9px] text-gray-600 font-mono uppercase">v1.9.58</span>
                </div>
                <div class="p-4 space-y-4">
                    @php
                        $bans = [
                            ['rank' => '#1', 'name' => 'Joy', 'role' => 'ASSASSIN · JUNGLE', 'rate' => 47.7],
                            ['rank' => '#2', 'name' => 'Valentina', 'role' => 'MAGE · MID', 'rate' => 43.1],
                            ['rank' => '#3', 'name' => 'Arlott', 'role' => 'FIGHTER · EXP', 'rate' => 33.8],
                        ];
                    @endphp
                    @foreach($bans as $ban)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-mono font-bold text-red-500/50 italic">{{ $ban['rank'] }}</span>
                            <div>
                                <p class="text-xs font-bold text-gray-300 group-hover:text-red-400">{{ $ban['name'] }}</p>
                                <p class="text-[8px] text-gray-600 uppercase font-mono">{{ $ban['role'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="h-[3px] w-12 bg-gray-800 rounded-full">
                                <div class="h-full bg-red-500 shadow-[0_0_5px_rgba(239,68,68,0.5)]" style="width: {{ $ban['rate'] }}%"></div>
                            </div>
                            <span class="text-xs font-black text-red-500 font-mono">{{ $ban['rate'] }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
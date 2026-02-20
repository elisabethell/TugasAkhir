{{-- resources/views/tier-list.blade.php --}}
@extends('layouts.app')

@section('title', 'Tier List - MLBB Meta Analytics')

@section('content')
<div class="max-w-7xl mx-auto p-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white">MOBILE LEGENDS: META HERO TIER LIST</h1>
        <p class="text-gray-500 mt-1 flex items-center">
            <span class="inline-block w-2 h-2 bg-green-400 rounded-full mr-2"></span>
            Live Sync • Calculated based on Tournament Matches (Liquipedia) • Updated Feb 21, 2026
        </p>
    </div>
    
    <!-- Tabs -->
    <div class="flex items-center space-x-6 mb-6 border-b border-gray-800 pb-2">
        <button class="text-[#00f2ff] font-medium pb-2 border-b-2 border-[#00f2ff]">Open tier lists</button>
        <button class="text-gray-400 hover:text-white">Default list</button>
        <button class="text-gray-400 hover:text-white">Role list</button>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar - Roles & Lanes -->
        <div class="lg:col-span-1">
            <!-- Roles -->
            <div class="bg-[#161e2d] rounded-xl p-4 border border-gray-800 mb-4">
                <h3 class="font-bold text-lg mb-3 text-white">Roles</h3>
                <div class="space-y-2">
                    @foreach($roles as $role)
                    <div class="flex items-center justify-between p-2 hover:bg-gray-800 rounded-lg transition cursor-pointer">
                        <span class="text-gray-300">{{ $role }}</span>
                        <span class="text-xs px-2 py-1 bg-gray-800 rounded-full text-gray-400">0</span>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Lanes -->
            <div class="bg-[#161e2d] rounded-xl p-4 border border-gray-800">
                <h3 class="font-bold text-lg mb-3 text-white">Lanes</h3>
                <div class="space-y-2">
                    @foreach($lanes as $lane)
                    <div class="flex items-center justify-between p-2 hover:bg-gray-800 rounded-lg transition cursor-pointer">
                        <span class="text-gray-300">{{ $lane }}</span>
                        <span class="text-xs px-2 py-1 bg-gray-800 rounded-full text-gray-400">{{ $laneCounts[$lane] ?? 0 }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Quick Filters -->
            <div class="bg-[#161e2d] rounded-xl p-4 border border-gray-800 mt-4">
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-xs">S-Tier</span>
                    <span class="px-3 py-1 bg-orange-500/20 text-orange-400 rounded-full text-xs">A-Tier</span>
                    <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs">B-Tier</span>
                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs">C-Tier</span>
                </div>
            </div>
        </div>
        
        <!-- Main Content - Tier List -->
        <div class="lg:col-span-3">
            <!-- Tier S -->
            <div class="bg-[#161e2d] rounded-xl border border-gray-800 overflow-hidden mb-4">
                <div class="bg-gradient-to-r from-red-500/20 to-red-600/20 px-4 py-3 border-b border-gray-800 flex items-center">
                    <span class="text-2xl font-black text-red-400 mr-3">S</span>
                    <span class="text-gray-400 text-sm">Tier</span>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($tierData['S'] as $hero)
                        <div class="bg-gray-800/50 p-3 rounded-lg hover:bg-gray-800 transition group cursor-pointer">
                            <div class="font-semibold text-white group-hover:text-[#00f2ff]">{{ $hero['name'] }}</div>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-xs text-gray-500">{{ $hero['role'] }}</span>
                                <span class="text-xs text-gray-500">{{ $hero['lane'] }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2 text-xs">
                                <span class="text-green-400">{{ $hero['win_rate'] }}% WR</span>
                                <span class="text-[#00f2ff]">{{ $hero['pick_rate'] }}% PR</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Tier A -->
            <div class="bg-[#161e2d] rounded-xl border border-gray-800 overflow-hidden mb-4">
                <div class="bg-gradient-to-r from-orange-500/20 to-orange-600/20 px-4 py-3 border-b border-gray-800 flex items-center">
                    <span class="text-2xl font-black text-orange-400 mr-3">A</span>
                    <span class="text-gray-400 text-sm">Tier</span>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($tierData['A'] as $hero)
                        <div class="bg-gray-800/50 p-3 rounded-lg hover:bg-gray-800 transition group cursor-pointer">
                            <div class="font-semibold text-white group-hover:text-[#00f2ff]">{{ $hero['name'] }}</div>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-xs text-gray-500">{{ $hero['role'] }}</span>
                                <span class="text-xs text-gray-500">{{ $hero['lane'] }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2 text-xs">
                                <span class="text-green-400">{{ $hero['win_rate'] }}% WR</span>
                                <span class="text-[#00f2ff]">{{ $hero['pick_rate'] }}% PR</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Tier B -->
            <div class="bg-[#161e2d] rounded-xl border border-gray-800 overflow-hidden mb-4">
                <div class="bg-gradient-to-r from-yellow-500/20 to-yellow-600/20 px-4 py-3 border-b border-gray-800 flex items-center">
                    <span class="text-2xl font-black text-yellow-400 mr-3">B</span>
                    <span class="text-gray-400 text-sm">Tier</span>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($tierData['B'] as $hero)
                        <div class="bg-gray-800/50 p-3 rounded-lg hover:bg-gray-800 transition group cursor-pointer">
                            <div class="font-semibold text-white group-hover:text-[#00f2ff]">{{ $hero['name'] }}</div>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-xs text-gray-500">{{ $hero['role'] }}</span>
                                <span class="text-xs text-gray-500">{{ $hero['lane'] }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2 text-xs">
                                <span class="text-green-400">{{ $hero['win_rate'] }}% WR</span>
                                <span class="text-[#00f2ff]">{{ $hero['pick_rate'] }}% PR</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Tier C -->
            <div class="bg-[#161e2d] rounded-xl border border-gray-800 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500/20 to-blue-600/20 px-4 py-3 border-b border-gray-800 flex items-center">
                    <span class="text-2xl font-black text-blue-400 mr-3">C</span>
                    <span class="text-gray-400 text-sm">Tier</span>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($tierData['C'] as $hero)
                        <div class="bg-gray-800/50 p-3 rounded-lg hover:bg-gray-800 transition group cursor-pointer">
                            <div class="font-semibold text-white group-hover:text-[#00f2ff]">{{ $hero['name'] }}</div>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-xs text-gray-500">{{ $hero['role'] }}</span>
                                <span class="text-xs text-gray-500">{{ $hero['lane'] }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-2 text-xs">
                                <span class="text-green-400">{{ $hero['win_rate'] }}% WR</span>
                                <span class="text-[#00f2ff]">{{ $hero['pick_rate'] }}% PR</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
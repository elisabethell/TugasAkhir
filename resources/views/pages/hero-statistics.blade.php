@extends('layouts.app')

@section('title', 'Hero Statistics - MLBB Analytics')

@section('content')
{{-- Navbar sudah ada di layouts.app, jadi tidak perlu dipanggil lagi di sini --}}

<div class="max-w-7xl mx-auto p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white">📊 HERO STATISTICS</h1>
        <p class="text-gray-500 mt-1">Data synced from API to Local Database • Patch 1.8.92</p>
    </div>
    
    <div class="bg-[#161e2d] rounded-xl p-4 border border-gray-800 mb-6">
        <form action="{{ route('hero.statistics') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex items-center space-x-2">
                <span class="text-gray-400 text-sm">Role:</span>
                <select name="role" onchange="this.form.submit()" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-white focus:outline-none focus:border-[#00f2ff]">
                    <option value="all" {{ $role == 'all' ? 'selected' : '' }}>All Roles</option>
                    @foreach(['fighter', 'assassin', 'mage', 'marksman', 'tank', 'support'] as $r)
                        <option value="{{ $r }}" {{ $role == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ml-auto text-sm text-gray-500">
                Total Heroes: <span class="text-[#00f2ff] font-bold">{{ count($heroStats) }}</span>
            </div>
        </form>
    </div>
    
    <div class="bg-[#161e2d] rounded-xl border border-gray-800 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="p-4 text-gray-400 text-xs uppercase tracking-wider">Hero</th>
                        <th class="p-4 text-gray-400 text-xs uppercase tracking-wider">Role</th>
                        <th class="p-4 text-gray-400 text-xs uppercase tracking-wider">Damage Type</th>
                        <th class="p-4 text-gray-400 text-xs uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($heroStats as $hero)
                    <tr class="hover:bg-gray-800/30 transition border-b border-gray-800">
                        <td class="p-4">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $hero->image_url }}" class="w-10 h-10 rounded-full border border-gray-700 object-cover bg-gray-900" alt="{{ $hero->name }}">
                                <span class="font-bold text-white uppercase">{{ $hero->name }}</span>
                            </div>
                        </td>
                        <td class="p-4 capitalize text-gray-300">
                            {{ $hero->role_1 }}{{ $hero->role_2 ? ' / '.$hero->role_2 : '' }}
                        </td>
                        <td class="p-4">
                            <span class="text-xs px-2 py-1 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                {{ $hero->damage_type ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <button class="text-[#00f2ff] hover:underline text-sm">Details</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500 italic">No heroes found. Try syncing data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
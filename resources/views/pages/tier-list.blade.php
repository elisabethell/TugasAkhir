@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold neon-text">Hero Tier List</h1>
    <p class="text-gray-500 text-sm mt-1">Based on Latest Professional Match Data</p>
</div>

<div class="mb-10">
    <div class="flex items-center gap-3 mb-6">
        <span class="bg-red-600 text-white font-black px-3 py-1 rounded text-xl shadow-[0_0_15px_rgba(220,38,38,0.5)]">S</span>
        <h2 class="text-xl font-bold tracking-widest text-gray-300 uppercase">Tier Heroes</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($sTier as $hero)
        <div class="card-custom p-4 flex flex-col gap-3 group hover:scale-[1.02] transition-transform cursor-pointer">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-black text-lg group-hover:text-cyan-400 transition">{{ $hero['name'] }}</h3>
                    <div class="flex gap-2 text-[10px] text-gray-500 font-bold uppercase tracking-tighter">
                        <span>{{ $hero['role'] }}</span>
                        <span>•</span>
                        <span>{{ $hero['lane'] }}</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-lg overflow-hidden border border-gray-700 bg-gray-800">
                    <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $hero['name'] }}" alt="{{ $hero['name'] }}">
                </div>
            </div>

            <div class="flex justify-between items-center mt-2 border-t border-gray-800 pt-3">
                <div class="text-center">
                    <p class="text-[10px] text-gray-600 font-bold uppercase">Win Rate</p>
                    <p class="text-green-400 font-black">{{ $hero['wr'] }}%</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] text-gray-600 font-bold uppercase">Pick Rate</p>
                    <p class="text-cyan-400 font-black">{{ $hero['pr'] }}%</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
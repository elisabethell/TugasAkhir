{{-- resources/views/draft-simulator.blade.php --}}
@extends('layouts.app')

@section('title', 'Draft Simulator - Coming Soon')

@section('content')
<div class="max-w-7xl mx-auto p-8">
    <div class="bg-[#161e2d] rounded-xl p-12 border border-gray-800 text-center">
        <div class="text-8xl mb-6">🎮</div>
        <h1 class="text-3xl font-bold mb-4">Draft Simulator</h1>
        <p class="text-gray-400 mb-8 max-w-lg mx-auto">
            Fitur Draft Simulator sedang dalam pengembangan. 
            Nantikan update selanjutnya untuk mencoba simulasi drafting!
        </p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('planner') }}" class="bg-[#00f2ff] text-black px-6 py-3 rounded-lg font-semibold hover:bg-[#00d9e6] transition">
                Coba Team Planner
            </a>
            <a href="{{ route('dashboard') }}" class="bg-gray-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
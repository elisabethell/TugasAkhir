{{-- resources/views/planner.blade.php --}}
@extends('layouts.app')

@section('title', 'Planner - M7 World Championship 2026')

@section('content')
<div class="max-w-7xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-8">Strategy Planner</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Team Composition Planner -->
        <div class="bg-[#161e2d] rounded-xl border border-gray-800 p-6">
            <h2 class="text-xl font-bold mb-4">Team Composition Planner</h2>
            
            @php
                $roles = ['Jungler', 'Roamer', 'Gold Lane', 'EXP Lane', 'Mid Lane'];
            @endphp
            
            @foreach($roles as $role)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-500 mb-2">{{ $role }}</label>
                <select class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 text-white">
                    <option value="">Select Hero</option>
                    @foreach($heroes->where('role_1', strtolower($role))->take(5) as $hero)
                    <option value="{{ $hero->nama_hero }}">{{ $hero->nama_hero }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
            
            <button class="w-full bg-[#00f2ff] text-black font-semibold py-2 rounded-lg mt-4 hover:bg-[#00d9e6] transition">
                Analyze Composition
            </button>
        </div>
        
        <!-- Strategy Notes -->
        <div class="bg-[#161e2d] rounded-xl border border-gray-800 p-6">
            <h2 class="text-xl font-bold mb-4">Strategy Notes</h2>
            <textarea class="w-full h-48 bg-gray-800 border border-gray-700 rounded-lg p-3 text-white" 
                      placeholder="Write your strategy notes here..."></textarea>
            
            <div class="flex space-x-2 mt-4">
                <button class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Save Notes
                </button>
                <button class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Load Template
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
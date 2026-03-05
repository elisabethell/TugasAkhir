{{-- resources/views/components/navbar.blade.php --}}
<nav class="border-b border-gray-800 bg-[#0B0E14]/90 sticky top-0 z-50 backdrop-blur">
    <div class="max-w-7xl mx-auto px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-8">
                <h1 class="text-xl font-bold bg-gradient-to-r from-[#00f2ff] to-[#8a2be2] bg-clip-text text-transparent">
                    PocaStats
                </h1>
                <div class="flex space-x-6">
                    {{-- Link Dashboard --}}
                    <a href="{{ route('dashboard') }}" 
                       class="{{ request()->routeIs('dashboard') ? 'text-[#00f2ff] font-medium border-b-2 border-[#00f2ff] pb-1' : 'text-gray-400 hover:text-white transition' }}">
                        Dashboard
                    </a>

                    {{-- Link Hero Stats --}}
                    <a href="{{ route('hero.statistics') }}" 
                       class="{{ request()->routeIs('hero.statistics') ? 'text-[#00f2ff] font-medium border-b-2 border-[#00f2ff] pb-1' : 'text-gray-400 hover:text-white transition' }}">
                        Hero Stats
                    </a>

                    {{-- Link Tier List --}}
                    <a href="{{ route('tier.list') }}" 
                       class="{{ request()->routeIs('tier.list') ? 'text-[#00f2ff] font-medium border-b-2 border-[#00f2ff] pb-1' : 'text-gray-400 hover:text-white transition' }}">
                        Tier List
                    </a>

                    {{-- Link Draft Analyzer --}}
                    <a href="{{ route('draft.analyzer') }}" 
                       class="{{ request()->routeIs('draft.analyzer') ? 'text-[#00f2ff] font-medium border-b-2 border-[#00f2ff] pb-1' : 'text-gray-400 hover:text-white transition' }}">
                        Draft Analyzer
                    </a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-xs bg-gray-800 px-3 py-1 rounded-full text-[#00f2ff]">Patch 1.8.92</span>
                <span class="text-xs text-green-400">● Live</span>
            </div>
        </div>
    </div>
</nav>
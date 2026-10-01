{{-- resources/views/components/navbar.blade.php --}}
<nav class="border-b border-gray-800 bg-[#0B0E14]/95 sticky top-0 z-50 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-6 lg:space-x-8">
                <a href="{{ route('draft.analyzer') }}" class="flex items-center space-x-2">
                    <span class="text-xl font-extrabold bg-gradient-to-r from-[#00f2ff] via-[#38bdf8] to-[#818cf8] bg-clip-text text-transparent tracking-wide">
                        MetaScout
                    </span>
                    <span class="text-xs bg-indigo-950 text-indigo-300 font-semibold px-2 py-0.5 rounded border border-indigo-800 hidden sm:inline-block">
                        Land of Dawn
                    </span>
                </a>
                
                <div class="hidden md:flex space-x-5 text-sm font-medium">
                    {{-- Rekomendasi Draft (Home) --}}
                    <a href="{{ route('draft.analyzer') }}" 
                       class="{{ request()->routeIs('draft.analyzer') ? 'text-[#00f2ff] border-b-2 border-[#00f2ff] pb-1' : 'text-gray-400 hover:text-white transition' }}">
                        🎮 Rekomendasi Draft
                    </a>

                    {{-- Pro Hero Stats (Liquipedia Style) --}}
                    <a href="{{ route('hero.statistics') }}" 
                       class="{{ request()->routeIs('hero.statistics') ? 'text-[#00f2ff] border-b-2 border-[#00f2ff] pb-1' : 'text-gray-400 hover:text-white transition' }}">
                        📊 Statistik Pro Meta
                    </a>

                    {{-- Riwayat Pertandingan Seri --}}
                    <a href="{{ route('matches') }}" 
                       class="{{ request()->routeIs('matches') ? 'text-[#00f2ff] border-b-2 border-[#00f2ff] pb-1' : 'text-gray-400 hover:text-white transition' }}">
                        🏆 Riwayat Seri Match
                    </a>

                    {{-- Kamus Data Hero --}}
                    <a href="{{ route('heroes') }}" 
                       class="{{ request()->routeIs('heroes') ? 'text-[#00f2ff] border-b-2 border-[#00f2ff] pb-1' : 'text-gray-400 hover:text-white transition' }}">
                        📖 Kamus Hero
                    </a>

                    {{-- Knowledge Base RBR --}}
                    <a href="{{ route('rules') }}" 
                       class="{{ request()->routeIs('rules') ? 'text-[#00f2ff] border-b-2 border-[#00f2ff] pb-1' : 'text-gray-400 hover:text-white transition' }}">
                        🧠 Aturan RBR
                    </a>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <span class="text-xs bg-cyan-950 text-cyan-300 border border-cyan-800 px-2.5 py-1 rounded-full font-medium hidden sm:inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    RBR Engine v2.0
                </span>
                <span class="text-xs bg-gray-800 text-gray-300 px-2.5 py-1 rounded-full font-medium">
                    MWI EWC 2026
                </span>
            </div>
        </div>
    </div>
</nav>
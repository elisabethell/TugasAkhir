@extends('layouts.app')

@section('title', 'Rule Inspector Dosen - Knowledge Base RBR MetaScout')

@section('content')
<div class="space-y-6">

    <!-- 1. ACADEMIC HEADER & DOSEN INSPECTOR TITLE -->
    <div class="card-custom p-6 sm:p-8 bg-gradient-to-r from-[#18181B] via-[#2A050B] to-[#700B1A] border border-[#F8B4BD]/30 shadow-md text-white relative overflow-hidden">
        <div class="max-w-4xl relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-rose-200 text-xs font-black uppercase tracking-wider mb-3 border border-white/20">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>PANEL VERIFIKASI SIDANG SKRIPSI • RULE INSPECTOR DOSEN</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
                Pangkalan Pengetahuan Aturan (RBR Rule Inspector)
            </h1>
            <p class="text-gray-200 text-xs sm:text-sm mt-2.5 leading-relaxed font-medium">
                Daftar basis aturan formal <strong class="text-white font-mono bg-white/20 px-1.5 py-0.5 rounded">IF-THEN</strong> pada tabel database <code class="text-rose-200 bg-black/40 px-1.5 py-0.5 rounded font-mono">rbr_rules</code>.
                Aturan-aturan ini berfungsi sebagai <strong>Lapisan Penjelasan Strategi (Explanation Layer)</strong> berbasis 5 atribut kuantitatif Coach (<em>Sustainability, Attack Power, Crowd Control, Mobility, Lane Control</em>) yang melengkapi peringkat Hybrid SAW + Bayesian Shrinkage tanpa bobot confidence statis.
            </p>

            <!-- Metrics Pills -->
            <div class="mt-5 flex flex-wrap items-center gap-2.5 text-xs font-bold">
                <span class="bg-black/40 text-gray-200 px-3 py-1.5 rounded-xl border border-white/10 flex items-center gap-1.5">
                    <span class="text-rose-300">Peringkat:</span> Hybrid SAW + Bayesian Shrinkage (m=5)
                </span>
                <span class="bg-black/40 text-gray-200 px-3 py-1.5 rounded-xl border border-white/10 flex items-center gap-1.5">
                    <span class="text-rose-300">Peran RBR:</span> Interpretasi Strategi (5 Atribut Coach)
                </span>
                <span class="bg-emerald-500/20 text-emerald-300 px-3 py-1.5 rounded-xl border border-emerald-500/30 font-black flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Total: {{ $allRulesCount }} Aturan Terdaftar
                </span>
            </div>
        </div>

        <!-- Decorative Watermark Background Icon -->
        <div class="absolute right-[-20px] bottom-[-30px] opacity-10 pointer-events-none select-none text-white">
            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
            </svg>
        </div>
    </div>

    <!-- 2. SEARCH & CATEGORY FILTER TABS -->
    <div class="card-custom p-4 flex flex-col md:flex-row items-center justify-between gap-3 flex-wrap">
        
        <!-- Search Input -->
        <div class="relative w-full md:w-80 flex-shrink-0">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input type="text" 
                   id="ruleSearchInput" 
                   oninput="filterRules()" 
                   placeholder="Cari kode aturan (RULE-001), kategori, premis IF, atau kesimpulan THEN..." 
                   class="w-full bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl py-2 pl-9 pr-3 text-xs font-medium text-[#18181B] placeholder-gray-400 focus:outline-none focus:border-[#700B1A] transition">
        </div>

        <!-- Category Filter Buttons -->
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" onclick="setCategoryFilter('all', this)" class="category-btn bg-[#700B1A] text-white px-3 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider shadow-sm transition">
                Semua ({{ $allRulesCount }})
            </button>
            <button type="button" onclick="setCategoryFilter('composition', this)" class="category-btn bg-[#FAF8F8] text-gray-700 hover:bg-[#FCECEE] hover:text-[#700B1A] border border-[#F3E8E8] px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition">
                Komposisi Mutlak ({{ $compositionCount }})
            </button>
            <button type="button" onclick="setCategoryFilter('synergy', this)" class="category-btn bg-[#FAF8F8] text-gray-700 hover:bg-[#FCECEE] hover:text-[#700B1A] border border-[#F3E8E8] px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition">
                Sinergi ({{ $synergyCount }})
            </button>
            <button type="button" onclick="setCategoryFilter('counter', this)" class="category-btn bg-[#FAF8F8] text-gray-700 hover:bg-[#FCECEE] hover:text-[#700B1A] border border-[#F3E8E8] px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition">
                Counter Pick ({{ $counterCount }})
            </button>
            <button type="button" onclick="setCategoryFilter('powerspike', this)" class="category-btn bg-[#FAF8F8] text-gray-700 hover:bg-[#FCECEE] hover:text-[#700B1A] border border-[#F3E8E8] px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition">
                Power Spike & Map ({{ $powerSpikeCount }})
            </button>
        </div>

    </div>

    <!-- 4. LIST OF ACADEMIC RBR RULES (2 COLUMNS CARDS) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="rulesListContainer">
        @foreach($rules as $rule)
            @php
                $cat = strtolower($rule->category ?? 'synergy');
                $catBadgeStyle = match($cat) {
                    'composition' => 'bg-amber-100 text-amber-900 border-amber-300',
                    'counter'     => 'bg-rose-100 text-rose-900 border-rose-300',
                    'powerspike'  => 'bg-indigo-100 text-indigo-900 border-indigo-300',
                    default       => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                };
                $displayCode = $rule->rule_code ?: 'RULE-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT);
            @endphp
            <div class="rule-card card-custom relative p-5 space-y-3.5 hover:border-[#700B1A]/40 transition shadow-sm bg-white"
                 data-category="{{ $cat }}"
                 data-search="{{ strtolower($displayCode . ' ' . ($rule->category ?? '') . ' ' . $rule->if_condition . ' ' . $rule->then_recommendation . ' ' . ($rule->description ?? '')) }}">
                
                <!-- Card Header -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-black text-xs px-2.5 py-1 rounded-md bg-[#FAF8F8] border border-[#F3E8E8] text-[#700B1A]">
                            {{ $displayCode }}
                        </span>
                        <span class="text-[9.5px] font-black uppercase px-2 py-0.5 rounded-full border {{ $catBadgeStyle }}">
                            {{ strtoupper($rule->category ?: 'SYNERGY') }}
                        </span>
                    </div>

                    <!-- Explanation Layer Indicator (Tanpa Confidence Statis) -->
                    <span class="text-[10px] font-bold text-gray-500 bg-[#FAF8F8] border border-[#F3E8E8] px-2.5 py-1 rounded-lg">
                        Interpretasi 5 Atribut Coach
                    </span>
                </div>

                <!-- Premise & Conclusion Blocks -->
                <div class="space-y-2 text-xs">
                    <!-- IF Condition (Premise) -->
                    <div class="p-3 bg-[#FAF8F8] border border-[#F3E8E8] rounded-xl space-y-1">
                        <span class="text-[9.5px] font-black text-amber-700 uppercase tracking-wider block flex items-center gap-1">
                            <span>PREMIS (KONDISI IF):</span>
                        </span>
                        <p class="text-[#18181B] font-mono text-[11px] leading-relaxed">
                            {{ $rule->if_condition }}
                        </p>
                    </div>

                    <!-- THEN Action (Conclusion) -->
                    <div class="p-3 bg-[#FCECEE]/40 border border-[#F8B4BD]/60 rounded-xl space-y-1">
                        <span class="text-[9.5px] font-black text-[#700B1A] uppercase tracking-wider block flex items-center gap-1">
                            <span>KESIMPULAN (AKSI THEN & REKOMENDASI):</span>
                        </span>
                        <p class="text-[#700B1A] font-semibold text-[11px] leading-relaxed">
                            {{ $rule->then_recommendation }}
                        </p>
                    </div>
                </div>

                <!-- Academic Justification / Description -->
                @if(!empty($rule->description))
                    <div class="pt-2 border-t border-gray-100 text-[10.5px] text-gray-500 leading-relaxed">
                        <span class="font-bold text-gray-700">Justifikasi Akademik:</span> {{ $rule->description }}
                    </div>
                @endif

                <!-- Card Bottom Action -->
                <div class="flex items-center justify-end pt-1 text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                    <a href="{{ route('draft.recommendation') }}" class="text-[#700B1A] hover:underline font-extrabold">
                        Uji di Simulator &rarr;
                    </a>
                </div>

            </div>
        @endforeach
    </div>

</div>

@push('scripts')
<script>
    let currentCategory = 'all';

    function setCategoryFilter(category, btn) {
        currentCategory = category.toLowerCase();
        
        document.querySelectorAll('.category-btn').forEach(b => {
            b.className = 'category-btn bg-[#FAF8F8] text-gray-700 hover:bg-[#FCECEE] hover:text-[#700B1A] border border-[#F3E8E8] px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition';
        });
        btn.className = 'category-btn bg-[#700B1A] text-white px-3 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider shadow-sm transition';

        filterRules();
    }

    function filterRules() {
        const query = (document.getElementById('ruleSearchInput')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.rule-card');

        cards.forEach(card => {
            const cardCat = (card.dataset.category || '').toLowerCase();
            const cardSearch = (card.dataset.search || '').toLowerCase();

            const matchCat = (currentCategory === 'all' || cardCat.includes(currentCategory));
            const matchQuery = (!query || cardSearch.includes(query));

            if (matchCat && matchQuery) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endpush
@endsection

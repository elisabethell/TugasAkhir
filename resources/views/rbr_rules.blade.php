@extends('layouts.app')

@section('title', 'Knowledge Base Aturan RBR - MetaScout: Land of Dawn')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACADEMIC CONTEXT -->
    <div class="card-custom p-6 sm:p-8 bg-gradient-to-r from-[#111827] via-[#1e1b4b] to-[#0f172a] border border-gray-800">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-cyan-950 text-cyan-400 text-xs font-semibold uppercase tracking-wider mb-2 border border-cyan-800">
                <span>🧠 Transparansi Akademik • Expert System Knowledge Base</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">
                Pangkalan Pengetahuan Aturan (RBR Rules)
            </h1>
            <p class="text-gray-300 text-sm mt-2 leading-relaxed">
                Daftar basis aturan formal <strong class="text-cyan-400 font-mono">IF-THEN</strong> yang disimpan pada tabel database <code class="text-gray-200 bg-gray-800 px-1 py-0.5 rounded">rbr_rules</code>.
                Aturan ini digunakan oleh mesin inferensi <em>Forward Chaining</em> untuk menganalisis komposisi draft hero, memprediksi kurva <em>power spike</em>, dan merumuskan panduan gameplay serta <em>winning condition</em> taktis.
            </p>
            <div class="mt-4 flex flex-wrap gap-2 text-xs">
                <span class="bg-gray-800/80 text-gray-300 px-3 py-1 rounded-lg border border-gray-700">Metode: Rule-Based Reasoning (RBR)</span>
                <span class="bg-gray-800/80 text-gray-300 px-3 py-1 rounded-lg border border-gray-700">Mesin Inferensi: Forward Chaining</span>
                <span class="bg-cyan-950 text-cyan-300 px-3 py-1 rounded-lg border border-cyan-800 font-bold">Total: {{ count($rules) }} Aturan Aktif</span>
            </div>
        </div>
    </div>

    <!-- FILTER / SEARCH BAR -->
    <div class="card-custom p-4 bg-[#121620] border-gray-800 flex justify-between items-center flex-wrap gap-4">
        <div class="flex-1 min-w-[240px]">
            <input type="text" id="rule-search" onkeyup="filterRules()" placeholder="Cari isi aturan IF atau THEN (misal: Pick-Off, Teamfight, Broken Walls)..." 
                   class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3.5 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500">
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('draft.analyzer') }}" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-black font-extrabold rounded-lg text-xs tracking-wider uppercase transition shadow-[0_0_15px_rgba(0,242,255,0.25)]">
                🎮 Coba di Rekomendasi Draft
            </a>
        </div>
    </div>

    <!-- DAFTAR ATURAN RBR -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="rules-container">
        @foreach($rules as $rule)
            <div class="rule-card card-custom p-5 bg-[#121620] border-gray-800 hover:border-cyan-500/50 transition space-y-3"
                 data-content="{{ strtolower($rule->kondisi_if . ' ' . $rule->kesimpulan_then) }}">
                
                <div class="flex items-center justify-between border-b border-gray-800/80 pb-2.5">
                    <span class="text-xs font-mono font-bold text-cyan-400">
                        Rule #{{ $rule->id_rule }}
                    </span>
                    <span class="text-[10px] px-2 py-0.5 rounded font-semibold {{ $rule->status_aktif ? 'bg-emerald-950 text-emerald-300 border border-emerald-800' : 'bg-gray-800 text-gray-500' }}">
                        {{ $rule->status_aktif ? 'Aktif ●' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="space-y-2 text-xs">
                    <!-- IF CONDITION -->
                    <div class="p-3 bg-gray-900/80 border border-gray-800 rounded-lg">
                        <span class="text-[10px] font-black text-amber-400 uppercase tracking-wider block mb-1">
                            PREMIS (KONDISI IF):
                        </span>
                        <p class="text-gray-200 leading-relaxed font-mono text-[11px]">
                            {{ $rule->kondisi_if }}
                        </p>
                    </div>

                    <!-- THEN CONCLUSION -->
                    <div class="p-3 bg-cyan-950/20 border border-cyan-900/40 rounded-lg">
                        <span class="text-[10px] font-black text-cyan-400 uppercase tracking-wider block mb-1">
                            KESIMPULAN (AKSI THEN):
                        </span>
                        <p class="text-cyan-100 leading-relaxed">
                            {{ $rule->kesimpulan_then }}
                        </p>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

</div>

@push('scripts')
<script>
    function filterRules() {
        const query = document.getElementById('rule-search').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.rule-card');

        cards.forEach(card => {
            const content = card.getAttribute('data-content');
            if (!query || content.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endpush
@endsection

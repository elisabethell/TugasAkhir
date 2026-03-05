{{-- resources/views/draft-analyzer.blade.php --}}
@extends('layouts.app')

@section('title', 'Draft Analyzer - MLBB Meta Analytics')

@section('content')
<div class="max-w-7xl mx-auto p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white">🎮 Draft Analyzer</h1>
        <p class="text-gray-500">Select your team composition to get winning strategy analysis</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Hero Selection -->
        <div class="lg:col-span-1">
            <div class="bg-[#161e2d] rounded-xl p-6 border border-gray-800">
                <h2 class="text-lg font-bold mb-4">Your Team</h2>
                
                <div class="space-y-3 mb-6" id="team-slots">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-500 w-6">{{ $i }}.</span>
                        <select class="hero-select flex-1 bg-gray-800 border border-gray-700 rounded-lg p-2 text-white text-sm" data-slot="{{ $i }}">
                            <option value="">Select Hero</option>
                            @foreach($heroes as $role => $roleHeroes)
                                <optgroup label="{{ ucfirst($role) }}">
                                    @foreach($roleHeroes as $hero)
                                    <option value="{{ $hero->nama_hero }}">{{ $hero->nama_hero }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    @endfor
                </div>
                
                <button id="analyze-btn" class="w-full bg-[#00f2ff] text-black font-semibold py-3 rounded-lg hover:bg-[#00d9e6] transition">
                    Analyze Composition
                </button>
            </div>
        </div>
        
        <!-- Analysis Results -->
        <div class="lg:col-span-2">
            <div class="bg-[#161e2d] rounded-xl p-6 border border-gray-800" id="analysis-result">
                <div class="text-center py-12 text-gray-500">
                    <span class="text-6xl mb-4 block">🎯</span>
                    <p>Select 5 heroes and click Analyze to see winning strategy</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('analyze-btn').addEventListener('click', function() {
    const selects = document.querySelectorAll('.hero-select');
    const team = [];
    
    selects.forEach(select => {
        if (select.value) {
            team.push(select.value);
        }
    });
    
    if (team.length < 5) {
        alert('Please select all 5 heroes');
        return;
    }
    
    // Show loading
    document.getElementById('analysis-result').innerHTML = `
        <div class="text-center py-12">
            <div class="animate-spin text-4xl mb-4">⏳</div>
            <p class="text-gray-400">Analyzing composition...</p>
        </div>
    `;
    
    // Simulate analysis (nanti pake AJAX ke backend)
    setTimeout(() => {
        const result = generateAnalysis(team);
        displayAnalysis(result);
    }, 1500);
});

function generateAnalysis(team) {
    // Simulasi hasil analisis
    const types = ['Team Fight', 'Pick Off', 'Split Push'];
    const spikes = ['Early Game', 'Mid Game', 'Late Game'];
    const strengths = ['S (Excellent)', 'A (Strong)', 'B (Average)'];
    
    const randomType = types[Math.floor(Math.random() * types.length)];
    const randomSpike = spikes[Math.floor(Math.random() * spikes.length)];
    const randomStrength = strengths[Math.floor(Math.random() * strengths.length)];
    
    return {
        composition: randomType + ' Composition',
        power_spike: randomSpike + ' Focus',
        team_fight: randomStrength,
        push_power: randomStrength,
        pick_off: Math.random() > 0.5 ? 'High' : 'Medium',
        scaling: randomSpike == 'Late Game' ? 'Late Game (Scales well)' : 'Early-Mid Game',
        weaknesses: [
            'Lack of Crowd Control',
            'No Frontline/Tank'
        ],
        recommendations: [
            { type: 'Strategy', desc: 'Force early objectives and end before 15 minutes' },
            { type: 'Tactic', desc: 'Look for picks before attempting Lord' },
            { type: 'Warning', desc: 'Avoid prolonged team fights, composition is weak against AoE' }
        ]
    };
}

function displayAnalysis(result) {
    let weaknessesHtml = '';
    result.weaknesses.forEach(w => {
        weaknessesHtml += `<li class="text-red-400">⚠️ ${w}</li>`;
    });
    
    let recommendationsHtml = '';
    result.recommendations.forEach(r => {
        let color = r.type == 'Strategy' ? 'text-[#00f2ff]' : (r.type == 'Tactic' ? 'text-green-400' : 'text-yellow-400');
        recommendationsHtml += `
            <div class="flex items-start space-x-2 p-2 bg-gray-800/30 rounded">
                <span class="${color} font-bold">${r.type}</span>
                <span class="text-sm text-gray-300">${r.desc}</span>
            </div>
        `;
    });
    
    document.getElementById('analysis-result').innerHTML = `
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <span class="w-1 h-6 bg-[#00f2ff] rounded-full mr-3"></span>
            Draft Analysis Results
        </h2>
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-800/30 p-3 rounded-lg">
                <div class="text-sm text-gray-500">Composition</div>
                <div class="font-bold text-[#00f2ff]">${result.composition}</div>
            </div>
            <div class="bg-gray-800/30 p-3 rounded-lg">
                <div class="text-sm text-gray-500">Power Spike</div>
                <div class="font-bold text-yellow-400">${result.power_spike}</div>
            </div>
            <div class="bg-gray-800/30 p-3 rounded-lg">
                <div class="text-sm text-gray-500">Team Fight</div>
                <div class="font-bold text-purple-400">${result.team_fight}</div>
            </div>
            <div class="bg-gray-800/30 p-3 rounded-lg">
                <div class="text-sm text-gray-500">Push Power</div>
                <div class="font-bold text-orange-400">${result.push_power}</div>
            </div>
            <div class="bg-gray-800/30 p-3 rounded-lg">
                <div class="text-sm text-gray-500">Pick Off</div>
                <div class="font-bold text-green-400">${result.pick_off}</div>
            </div>
            <div class="bg-gray-800/30 p-3 rounded-lg">
                <div class="text-sm text-gray-500">Scaling</div>
                <div class="font-bold text-blue-400">${result.scaling}</div>
            </div>
        </div>
        
        <div class="mb-4">
            <h3 class="font-semibold mb-2 text-red-400">Weaknesses</h3>
            <ul class="space-y-1">
                ${weaknessesHtml}
            </ul>
        </div>
        
        <div>
            <h3 class="font-semibold mb-2 text-[#00f2ff]">Recommendations</h3>
            <div class="space-y-2">
                ${recommendationsHtml}
            </div>
        </div>
    `;
}
</script>
@endpush
@endsection
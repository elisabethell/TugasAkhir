<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>MLBB Analytics</title>
</head>
<body class="bg-[#0b1120] text-white font-sans">
    <div class="max-w-7xl mx-auto p-8">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">M7 World Championship 2026</h1>
                <p class="text-gray-400">Tournament Overview • Heroes Data</p>
            </div>
            <button class="bg-[#00f2ff] text-black px-6 py-2 rounded-lg font-bold shadow-[0_0_15px_rgba(0,242,255,0.5)]">
                Update Live
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($heroes as $hero)
            <div class="bg-[#161e2d] border border-gray-800 p-5 rounded-xl hover:border-[#00f2ff] transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold group-hover:text-[#00f2ff]">{{ $hero->nama_hero }}</h3>
                    <span class="text-xs bg-gray-700 px-2 py-1 rounded">{{ $hero->role_1 }}</span>
                </div>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-400">
                        <span>Damage Source</span>
                        <span class="text-white">{{ $hero->damage_source }}</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Best Lane</span>
                        <span class="text-[#00f2ff]">{{ $hero->{'lane_recommendation(s)_1'} }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pertandingan MLBB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-10">
    <h1 class="text-2xl font-bold text-cyan-400 mb-6">Daftar Pertandingan Liquipedia</h1>
    
    <div class="overflow-x-auto bg-gray-800 rounded-lg shadow">
        <table class="w-full text-left">
            <thead class="bg-gray-700 text-gray-300">
                <tr>
                    <th class="p-4">Turnamen</th>
                    <th class="p-4">Pertandingan</th>
                    <th class="p-4">Skor</th>
                    <th class="p-4">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
            @foreach($matches as $match)
<tr class="hover:bg-gray-700/50 border-b border-gray-800">
    {{-- Kolom 1: Turnamen & Tanggal --}}
    <td class="p-4">
        <div class="text-sm font-bold text-cyan-400">{{ $match['tournament'] ?? '-' }}</div>
        <div class="text-[10px] text-gray-500">{{ $match['date'] ?? '-' }}</div>
    </td>
    
    {{-- Kolom 2: Picks & Bans (Sesuai Struktur Screenshot Kamu) --}}
    <td class="p-4">
        @if(isset($match['extradata']))
            <div class="flex flex-col gap-1">
                {{-- Team 1 Picks --}}
                <div class="text-[10px]">
                    <span class="text-blue-400 font-bold uppercase">Picks:</span> 
                    {{ $match['extradata']['team1champion1'] ?? '-' }}, 
                    {{ $match['extradata']['team1champion2'] ?? '-' }}, 
                    {{ $match['extradata']['team1champion3'] ?? '-' }}
                </div>
                {{-- Bans (Hanya contoh 2 ban) --}}
                <div class="text-[10px]">
                    <span class="text-red-400 font-bold uppercase">Bans:</span> 
                    {{ $match['extradata']['team1ban1'] ?? '-' }}, 
                    {{ $match['extradata']['team2ban1'] ?? '-' }}
                </div>
            </div>
        @else
            <span class="text-xs text-gray-600">No Hero Data</span>
        @endif
    </td>
    
    {{-- Kolom 3: Skor --}}
    <td class="p-4 text-center">
        <span class="bg-gray-900 px-3 py-1 rounded border border-gray-700 font-bold text-cyan-400">
            {{ $match['opponent1score'] ?? 0 }} - {{ $match['opponent2score'] ?? 0 }}
        </span>
    </td>
</tr>
@endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
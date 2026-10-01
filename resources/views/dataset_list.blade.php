<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaScout - Riwayat Pertandingan Seri MWI</title>
    <style>
        body { font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif; padding: 25px 20px; background-color: #f1f4f9; color: #2c3e50; line-height: 1.5; }
        .container { max-width: 1240px; margin: 0 auto; }
        
        /* Header & Top Bar */
        .top-bar { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
        h2 { color: #1e293b; margin: 0 0 6px 0; font-size: 24px; font-weight: 700; }
        p.subtitle { color: #64748b; margin: 0; font-size: 14px; }
        
        .action-group { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #1e293b; color: white; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid transparent; cursor: pointer; transition: all 0.2s ease; }
        .btn:hover { background: #0f172a; }
        .btn-outline { background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; }
        .btn-outline:hover { background: #f8fafc; border-color: #94a3b8; }
        
        .stats-summary { background: #ffffff; border-radius: 10px; padding: 12px 18px; margin-bottom: 25px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .stat-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #475569; }
        .stat-badge span { background: #e2e8f0; color: #0f172a; padding: 2px 8px; border-radius: 999px; font-size: 12px; }

        /* Series Card */
        .series-card { background: #ffffff; border-radius: 14px; box-shadow: 0 4px 14px rgba(0,0,0,0.06); margin-bottom: 30px; overflow: hidden; border: 1px solid #e2e8f0; }
        
        /* Series Header */
        .series-header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 16px 22px; }
        .series-meta { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; font-size: 12px; color: #94a3b8; }
        .series-meta-item { display: inline-flex; align-items: center; gap: 5px; background: rgba(255,255,255,0.08); padding: 4px 10px; border-radius: 6px; }
        .series-meta-item.bracket { color: #facc15; font-weight: 700; background: rgba(250, 204, 21, 0.15); border: 1px solid rgba(250, 204, 21, 0.3); }

        .series-matchup { display: flex; justify-content: space-between; align-items: center; gap: 15px; }
        .series-team { flex: 1; display: flex; align-items: center; gap: 10px; font-size: 18px; font-weight: 700; }
        .series-team.team-right { justify-content: flex-end; }
        
        .series-score-box { background: rgba(255, 255, 255, 0.12); padding: 6px 18px; border-radius: 10px; font-size: 22px; font-weight: 800; letter-spacing: 2px; color: #f8fafc; border: 1px solid rgba(255,255,255,0.2); white-space: nowrap; }
        .series-score-box .sep { color: #94a3b8; margin: 0 4px; font-weight: 400; }
        
        .badge-winner { background: #10b981; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; }
        .badge-loser { background: rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; border: 1px solid rgba(239, 68, 68, 0.4); }

        /* Series Body */
        .series-body { padding: 18px 20px; }
        
        /* Game Tabs */
        .game-tabs { display: flex; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; }
        .tab-btn { background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; }
        .tab-btn:hover { background: #f1f5f9; color: #0f172a; border-color: #94a3b8; }
        .tab-btn.active { background: #2563eb; color: #ffffff; border-color: #2563eb; box-shadow: 0 2px 6px rgba(37,99,235,0.25); }
        .tab-btn .tab-sub { font-size: 11px; opacity: 0.85; font-weight: 400; }

        /* Game Section */
        .game-section { margin-bottom: 22px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .game-section:last-child { margin-bottom: 0; }
        
        .game-strip { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 10px 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; font-size: 13px; }
        .game-strip-left { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .game-pill { background: #1e293b; color: white; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        
        .map-badge { display: inline-flex; align-items: center; gap: 6px; background: #ffffff; padding: 3px 10px; border-radius: 6px; border: 1px solid #e2e8f0; font-weight: 600; font-size: 12px; color: #334155; }
        .map-thumb { width: 28px; height: 18px; border-radius: 3px; object-fit: cover; border: 1px solid #cbd5e1; }
        
        .game-strip-right { display: flex; align-items: center; gap: 12px; font-size: 12px; }
        .game-winner-text { font-weight: 700; }
        .game-winner-text.blue { color: #2563eb; }
        .game-winner-text.red { color: #dc2626; }

        /* Unified Match Table */
        .table-wrap { overflow-x: auto; padding: 10px 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; text-align: center; white-space: nowrap; }
        th, td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; }
        
        .th-side-blue { background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 13px; border-right: 2px solid #cbd5e1; text-align: left; padding-left: 14px; }
        .th-role { background: #f8fafc; color: #64748b; font-weight: 700; font-size: 11px; text-transform: uppercase; width: 70px; }
        .th-side-red { background: #fef2f2; color: #b91c1c; font-weight: 700; font-size: 13px; border-left: 2px solid #cbd5e1; text-align: left; padding-left: 14px; }
        
        .sub-header th { font-size: 11px; color: #64748b; background: #f8fafc; font-weight: 600; padding: 6px 10px; }
        .sub-header th.border-r { border-right: 2px solid #cbd5e1; }
        .sub-header th.border-l { border-left: 2px solid #cbd5e1; }
        
        .td-blue { background-color: #fafcff; }
        .td-blue-player { text-align: left; font-weight: 700; color: #1e3a8a; }
        .td-role { background-color: #f8fafc; font-weight: 700; color: #475569; }
        .td-red { background-color: #fffbfb; }
        .td-red-player { text-align: left; font-weight: 700; color: #991b1b; }
        
        .border-r { border-right: 2px solid #e2e8f0; }
        .border-l { border-left: 2px solid #e2e8f0; }

        .flex-cell { display: inline-flex; align-items: center; gap: 7px; vertical-align: middle; }
        .hero-avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1.5px solid #cbd5e1; background: #e2e8f0; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .spell-avatar { width: 24px; height: 24px; border-radius: 6px; object-fit: cover; border: 1px solid #cbd5e1; background: #e2e8f0; flex-shrink: 0; }
        .ban-avatar { width: 20px; height: 20px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1; opacity: 0.9; }
        
        .badge-kda { font-weight: 700; color: #0f172a; }
        .badge-lane-tag { background: #e2e8f0; color: #334155; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; display: inline-block; }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="top-bar">
            <div>
                <h2>Riwayat Pertandingan Seri - MetaScout</h2>
                <p class="subtitle">Visualisasi terpadu data turnamen MWI X EWC 2026 per seri (Best-of) & 1 tabel utuh per game.</p>
            </div>
            <div class="action-group">
                <a href="/" class="btn">&laquo; Kembali ke Data Hero</a>
                <button type="button" class="btn btn-outline" onclick="expandAllGames()">📜 Buka Semua Game</button>
                <button type="button" class="btn btn-outline" onclick="compactAllGames()">📑 Mode Ringkas (Tab)</button>
            </div>
        </div>

        <div class="stats-summary">
            <div class="stat-badge">🏆 Total Seri Turnamen: <span>{{ count($seriesList) }} Seri</span></div>
            <div class="stat-badge">🎮 Total Game: <span>{{ collect($seriesList)->sum(fn($s) => count($s['games'])) }} Game</span></div>
            <div class="stat-badge">🗺️ Varian Map: <span>Broken Walls, Dangerous Grass, Expanding Rivers, Flying Clouds</span></div>
        </div>

        @php
            $mapImages = [
                'broken walls'     => 'broken_wall.webp',
                'broken wall'      => 'broken_wall.webp',
                'dangerous grass'  => 'dangerous_grass.webp',
                'expanding rivers' => 'expanding_river.webp',
                'expanding river'  => 'expanding_river.webp',
                'flying clouds'    => 'flying_cloud.webp',
                'flying cloud'     => 'flying_cloud.webp',
            ];

            $getHeroImg = function($heroName) use ($heroPortraits) {
                if (!$heroName || $heroName === '-') return null;
                $clean = preg_replace('/[^a-z0-9]/', '', strtolower($heroName));
                return $heroPortraits[$clean] ?? asset('images/heroes/' . $clean . '.png');
            };
        @endphp

        @foreach($seriesList as $sIdx => $series)
            @php
                $totalGames = count($series['games']);
                $t1Win = $series['score1'] > $series['score2'];
            @endphp

            <div class="series-card" id="series-card-{{ $sIdx }}">
                
                <!-- SERIES HEADER (RINGKASAN SERI) -->
                <div class="series-header">
                    <div class="series-meta">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <span class="series-meta-item bracket">🏆 {{ $series['bracket'] }}</span>
                            <span class="series-meta-item">📅 {{ $series['date'] }}</span>
                            <span class="series-meta-item">🎮 Best of {{ $totalGames <= 2 ? '3' : ($totalGames <= 3 && str_contains($series['bracket'], 'Quarter') ? '3' : ($totalGames <= 5 ? '5' : '7')) }} ({{ $totalGames }} Game)</span>
                        </div>
                        <div>
                            <span style="font-weight: 600; color: #e2e8f0;">Seri #{{ $sIdx + 1 }}</span>
                        </div>
                    </div>

                    <div class="series-matchup">
                        <div class="series-team">
                            <span style="color: #60a5fa;">{{ $series['team1'] }}</span>
                            @if($t1Win)
                                <span class="badge-winner">WINNER</span>
                            @else
                                <span class="badge-loser">DEFEAT</span>
                            @endif
                        </div>

                        <div class="series-score-box">
                            <span style="color: {{ $t1Win ? '#34d399' : '#f87171' }};">{{ $series['score1'] }}</span>
                            <span class="sep">-</span>
                            <span style="color: {{ !$t1Win ? '#34d399' : '#f87171' }};">{{ $series['score2'] }}</span>
                        </div>

                        <div class="series-team team-right">
                            @if(!$t1Win)
                                <span class="badge-winner">WINNER</span>
                            @else
                                <span class="badge-loser">DEFEAT</span>
                            @endif
                            <span style="color: #f87171;">{{ $series['team2'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- SERIES BODY -->
                <div class="series-body">
                    
                    <!-- TAB SELECTOR GAME -->
                    <div class="game-tabs" id="tabs-{{ $sIdx }}">
                        @foreach($series['games'] as $gIdx => $game)
                            @php
                                $gSample = $game->first();
                                $gWin = $game->where('win_lose', 'Win')->first();
                                $winTeam = $gWin ? $gWin->team : '-';
                            @endphp
                            <button type="button" 
                                    class="tab-btn {{ $gIdx === 0 ? 'active' : '' }}" 
                                    onclick="switchGame({{ $sIdx }}, {{ $gIdx }})">
                                🎮 Game {{ $gIdx + 1 }}
                                <span class="tab-sub">({{ $winTeam }} Win)</span>
                            </button>
                        @endforeach
                        <button type="button" 
                                class="tab-btn btn-all-tab" 
                                onclick="switchGame({{ $sIdx }}, 'all')">
                            👁️ Tampilkan Semua Game
                        </button>
                    </div>

                    <!-- GAME SECTIONS (1 TABEL LENGKAP PER GAME) -->
                    @foreach($series['games'] as $gIdx => $game)
                        @php
                            $blueTeam = $game->where('side', 'Blue')->values();
                            $redTeam = $game->where('side', 'Red')->values();

                            $blueRecord = $blueTeam->first();
                            $redRecord = $redTeam->first();

                            $blueTeamName = $blueRecord->team ?? ($redRecord->opponent ?? 'Blue Team');
                            $redTeamName = $redRecord->team ?? ($blueRecord->opponent ?? 'Red Team');
                            $blueWin = $blueRecord && $blueRecord->win_lose == 'Win';

                            $sample = $game->first();
                            $mapNorm = strtolower(trim($sample->map));
                            $mapFile = $mapImages[$mapNorm] ?? 'broken_wall.webp';

                            $rolesOrder = ['Explane', 'Jungler', 'Midlane', 'Goldlane', 'Roam'];
                            $blueByRole = $blueTeam->keyBy('role');
                            $redByRole = $redTeam->keyBy('role');
                        @endphp

                        <div class="game-section game-item-{{ $sIdx }}" 
                             id="game-item-{{ $sIdx }}-{{ $gIdx }}" 
                             style="display: {{ $gIdx === 0 ? 'block' : 'none' }};">
                            
                            <!-- STRIP INFO GAME -->
                            <div class="game-strip">
                                <div class="game-strip-left">
                                    <span class="game-pill">Game {{ $gIdx + 1 }}</span>
                                    
                                    <span class="map-badge">
                                        <img src="{{ asset('images/maps/' . $mapFile) }}" 
                                             class="map-thumb" 
                                             alt="{{ $sample->map }}"
                                             onerror="this.style.display='none'">
                                        <span>{{ $sample->map }}</span>
                                    </span>
                                    
                                    <span style="color: #64748b; font-weight: 600;">⏱️ {{ str_replace('.', ':', $sample->duration) }}</span>
                                    <span style="color: #64748b;">•</span>
                                    <span>Skor Game: <strong>{{ $blueRecord ? $blueRecord->skor_game : $sample->skor_game }}</strong></span>
                                </div>

                                <div class="game-strip-right">
                                    <span>Pemenang Game:</span>
                                    @if($blueWin)
                                        <span class="game-winner-text blue">💙 {{ $blueTeamName }} (Blue)</span>
                                    @else
                                        <span class="game-winner-text red">❤️ {{ $redTeamName }} (Red)</span>
                                    @endif
                                </div>
                            </div>

                            <!-- 1 TABEL PERTANDINGAN UTUH (BLUE vs RED) -->
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th colspan="5" class="th-side-blue">
                                                💙 Blue Side: {{ $blueTeamName }}
                                                @if($blueWin) <span class="badge-winner" style="font-size: 10px; margin-left: 6px;">WIN</span> @endif
                                            </th>
                                            <th class="th-role">Role</th>
                                            <th colspan="5" class="th-side-red">
                                                ❤️ Red Side: {{ $redTeamName }}
                                                @if(!$blueWin) <span class="badge-winner" style="font-size: 10px; margin-left: 6px;">WIN</span> @endif
                                            </th>
                                        </tr>
                                        <tr class="sub-header">
                                            <th style="text-align: left; padding-left: 14px;">Player</th>
                                            <th>Hero</th>
                                            <th>Ban</th>
                                            <th>Battle Spell</th>
                                            <th class="border-r">K/D/A</th>
                                            <th class="th-role">Lane</th>
                                            <th class="border-l" style="text-align: left; padding-left: 14px;">Player</th>
                                            <th>Hero</th>
                                            <th>Ban</th>
                                            <th>Battle Spell</th>
                                            <th>K/D/A</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rolesOrder as $role)
                                            @php
                                                $b = $blueByRole->get($role);
                                                $r = $redByRole->get($role);

                                                $bHeroImg = $b ? $getHeroImg($b->hero) : null;
                                                $rHeroImg = $r ? $getHeroImg($r->hero) : null;

                                                $bSpellImg = $b ? asset('images/spell/' . strtolower(trim($b->spell)) . '.jpeg') : null;
                                                $rSpellImg = $r ? asset('images/spell/' . strtolower(trim($r->spell)) . '.jpeg') : null;

                                                $bBanImg = ($b && $b->hero_ban) ? $getHeroImg($b->hero_ban) : null;
                                                $rBanImg = ($r && $r->hero_ban) ? $getHeroImg($r->hero_ban) : null;
                                            @endphp
                                            <tr>
                                                <!-- BLUE SIDE -->
                                                <td class="td-blue td-blue-player" style="padding-left: 14px;">
                                                    {{ $b->player ?? '-' }}
                                                </td>
                                                <td class="td-blue">
                                                    @if($b)
                                                        <div class="flex-cell">
                                                            <img src="{{ $bHeroImg }}" 
                                                                 class="hero-avatar" 
                                                                 alt="{{ $b->hero }}"
                                                                 onerror="this.src='https://via.placeholder.com/28?text=H'">
                                                            <strong style="color: #1e293b;">{{ $b->hero }}</strong>
                                                        </div>
                                                    @else - @endif
                                                </td>
                                                <td class="td-blue" style="color: #64748b; font-size: 11px;">
                                                    @if($b && $b->hero_ban)
                                                        <div class="flex-cell" style="justify-content: center;">
                                                            @if($bBanImg)
                                                                <img src="{{ $bBanImg }}" class="ban-avatar" onerror="this.style.display='none'">
                                                            @endif
                                                            <span>{{ $b->hero_ban }}</span>
                                                        </div>
                                                    @else - @endif
                                                </td>
                                                <td class="td-blue">
                                                    @if($b)
                                                        <div class="flex-cell">
                                                            <img src="{{ $bSpellImg }}" 
                                                                 class="spell-avatar" 
                                                                 alt="{{ $b->spell }}"
                                                                 onerror="this.src='https://via.placeholder.com/24?text=S'">
                                                            <span>{{ $b->spell }}</span>
                                                        </div>
                                                    @else - @endif
                                                </td>
                                                <td class="td-blue border-r">
                                                    <span class="badge-kda">{{ $b ? "{$b->kill_stat}/{$b->death_stat}/{$b->assist_stat}" : '-' }}</span>
                                                </td>

                                                <!-- ROLE TENGAH -->
                                                <td class="td-role">
                                                    <span class="badge-lane-tag">{{ $role }}</span>
                                                </td>

                                                <!-- RED SIDE -->
                                                <td class="td-red border-l td-red-player" style="padding-left: 14px;">
                                                    {{ $r->player ?? '-' }}
                                                </td>
                                                <td class="td-red">
                                                    @if($r)
                                                        <div class="flex-cell">
                                                            <img src="{{ $rHeroImg }}" 
                                                                 class="hero-avatar" 
                                                                 alt="{{ $r->hero }}"
                                                                 onerror="this.src='https://via.placeholder.com/28?text=H'">
                                                            <strong style="color: #1e293b;">{{ $r->hero }}</strong>
                                                        </div>
                                                    @else - @endif
                                                </td>
                                                <td class="td-red" style="color: #64748b; font-size: 11px;">
                                                    @if($r && $r->hero_ban)
                                                        <div class="flex-cell" style="justify-content: center;">
                                                            @if($rBanImg)
                                                                <img src="{{ $rBanImg }}" class="ban-avatar" onerror="this.style.display='none'">
                                                            @endif
                                                            <span>{{ $r->hero_ban }}</span>
                                                        </div>
                                                    @else - @endif
                                                </td>
                                                <td class="td-red">
                                                    @if($r)
                                                        <div class="flex-cell">
                                                            <img src="{{ $rSpellImg }}" 
                                                                 class="spell-avatar" 
                                                                 alt="{{ $r->spell }}"
                                                                 onerror="this.src='https://via.placeholder.com/24?text=S'">
                                                            <span>{{ $r->spell }}</span>
                                                        </div>
                                                    @else - @endif
                                                </td>
                                                <td class="td-red">
                                                    <span class="badge-kda">{{ $r ? "{$r->kill_stat}/{$r->death_stat}/{$r->assist_stat}" : '-' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>
        @endforeach

    </div>

    <script>
        function switchGame(seriesIdx, targetGameIdx) {
            const card = document.getElementById('series-card-' + seriesIdx);
            if (!card) return;

            const tabs = card.querySelectorAll('.tab-btn');
            const sections = card.querySelectorAll('.game-item-' + seriesIdx);

            tabs.forEach((tab, idx) => {
                tab.classList.remove('active');
                if (targetGameIdx === 'all') {
                    if (tab.classList.contains('btn-all-tab')) tab.classList.add('active');
                } else if (idx === targetGameIdx) {
                    tab.classList.add('active');
                }
            });

            sections.forEach((sec, idx) => {
                if (targetGameIdx === 'all' || idx === targetGameIdx) {
                    sec.style.display = 'block';
                } else {
                    sec.style.display = 'none';
                }
            });
        }

        function expandAllGames() {
            document.querySelectorAll('.series-card').forEach((card, sIdx) => {
                switchGame(sIdx, 'all');
            });
        }

        function compactAllGames() {
            document.querySelectorAll('.series-card').forEach((card, sIdx) => {
                switchGame(sIdx, 0);
            });
        }
    </script>

</body>
</html>
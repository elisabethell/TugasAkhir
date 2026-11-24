<?php

namespace App\Services;

use App\Models\MatchDataset;
use App\Models\MatchGame;
use App\Services\CsvDataService;
use Illuminate\Support\Collection;

class TeamDraftDnaService
{
    public const MPL_TEAMS = [
        'Alter Ego',
        'Bigetron By Vitality',
        'Dewa United',
        'EVOS',
        'Geek Fam',
        'Natus Vincere',
        'ONIC',
        'RRQ',
        'Team Liquid ID',
    ];

    protected CsvDataService $csvService;
    protected static array $cache = [];

    public function __construct(CsvDataService $csvService)
    {
        $this->csvService = $csvService;
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }

    /**
     * Normalisasi nama tim / alias menjadi nama tim resmi turnamen MPL ID S18
     * Default ke 'Alter Ego' jika parameter kosong atau Overall Meta dihapus
     */
    public function normalizeTeamName(?string $rawName): ?string
    {
        if (empty($rawName) || strtolower(trim($rawName)) === 'overall meta' || strtolower(trim($rawName)) === 'all') {
            return 'Alter Ego';
        }

        $clean = strtolower(trim(str_replace(['[[', ']]', '_'], ['', '', ' '], $rawName)));

        if (str_contains($clean, 'alter ego') || $clean === 'ae' || str_starts_with($clean, 'ae ')) {
            return 'Alter Ego';
        }
        if (str_contains($clean, 'onic')) {
            return 'ONIC';
        }
        if (str_contains($clean, 'rrq')) {
            return 'RRQ';
        }
        if (str_contains($clean, 'evos')) {
            return 'EVOS';
        }
        if (str_contains($clean, 'geek')) {
            return 'Geek Fam';
        }
        if (str_contains($clean, 'bigetron') || str_contains($clean, 'vitality') || $clean === 'btr' || str_starts_with($clean, 'btr ')) {
            return 'Bigetron By Vitality';
        }
        if (str_contains($clean, 'natus') || str_contains($clean, 'navi')) {
            return 'Natus Vincere';
        }
        if (str_contains($clean, 'rebellion') || $clean === 'rbl') {
            return 'Rebellion';
        }
        if (str_contains($clean, 'liquid') || $clean === 'tlid' || str_starts_with($clean, 'tlid ')) {
            return 'Team Liquid ID';
        }
        if (str_contains($clean, 'dewa')) {
            return 'Dewa United';
        }

        return ucwords($clean);
    }

    /**
     * Menganalisis data historis pertandingan tim di MPL ID S18 (match_datasets & match_games)
     * Mengembalikan profil "Draft DNA" lengkap:
     * - Total Game & Win Rate
     * - top_heroes (Top 5-8 hero pool signature & win rate)
     * - top_bans (Top 5 hero yang paling sering di-ban tim atau lawan)
     * - playstyle_tag (Deteksi otomatis gaya main)
     * - Matriks Sinergi & Counter internal tim untuk Association Rule Mining (ARM)
     */
    public function getTeamDraftDna(?string $teamInput): ?array
    {
        $teamName = $this->normalizeTeamName($teamInput);
        if (!$teamName || !in_array($teamName, self::MPL_TEAMS)) {
            return null;
        }

        if (isset(self::$cache[$teamName])) {
            return self::$cache[$teamName];
        }

        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('match_datasets') || !\Illuminate\Support\Facades\Schema::hasTable('match_games')) {
                return $this->getBaselineTeamDna($teamName);
            }
        } catch (\Throwable $e) {
            return $this->getBaselineTeamDna($teamName);
        }

        // Ambil data pertandingan yang melibatkan tim ini
        $matches = MatchDataset::with(['games' => function($q) {
            $q->where(function($sub) {
                $sub->whereNotNull('team_1_picks')
                    ->where('team_1_picks', '!=', '[]')
                    ->where('team_1_picks', '!=', '');
            })->orWhere(function($sub) {
                $sub->whereNotNull('team_2_picks')
                    ->where('team_2_picks', '!=', '[]')
                    ->where('team_2_picks', '!=', '');
            });
        }])
        ->where(function($q) use ($teamName) {
            $q->where('team_1_name', 'like', "%{$teamName}%")
              ->orWhere('team_2_name', 'like', "%{$teamName}%");
        })
        ->get();

        if ($matches->isEmpty()) {
            return $this->getBaselineTeamDna($teamName);
        }

        $allHeroes = $this->csvService->getHeroes()->keyBy(fn($h) => strtolower($h->hero_name));

        $totalGames = 0;
        $wins = 0;
        $losses = 0;

        $heroPicksCount = [];
        $heroWinsCount = [];

        $bansByTeamCount = [];
        $bansByEnemyCount = [];
        $totalBansCount = [];

        // Matriks Sinergi Tim: pasangan hero yang dipick bersama
        $teamSynergyPairs = [];

        // Matriks Counter Tim: hero tim vs hero musuh di match yang sama
        $teamCounterMatchups = [];

        // Counter untuk deteksi gaya main (Playstyle Tag)
        $archetypes = [
            'Double Fighter'         => 0,
            'Tank Jungler'           => 0,
            'Assassin Snowball'      => 0,
            'Late Game Scaling'      => 0,
            'Support Utility (UBE)'  => 0,
            'Early Game Aggro'       => 0,
        ];

        $knownHeavyTanks = ['fredrinn', 'baxia', 'akai', 'barats', 'hylos', 'gloo', 'belerick', 'uranus', 'grock', 'tigreal'];
        $knownAssassins = ['ling', 'fanny', 'hayabusa', 'nolan', 'joy', 'lancelot', 'suyou', 'helcurt', 'gusion', 'aamon', 'karina', 'benedetta'];
        $knownLateScalers = ['claude', 'moskov', 'karrie', 'harith', 'cecilion', 'xavier', 'lesley', 'bruno', 'natan', 'irithel', 'miya', 'layla', 'hanabi', 'aldous'];
        $knownHealerSupports = ['floryn', 'angela', 'mathilda', 'estes', 'rafaela', 'diggie', 'faramis', 'carmilla'];
        $knownEarlyAggro = ['hilda', 'grock', 'dyrroth', 'martis', 'terizla', 'yu zhong', 'khaleed', 'jawhead', 'chou', 'faramis', 'selena'];

        foreach ($matches as $match) {
            $isTeam1 = (strcasecmp($match->team_1_name, $teamName) === 0 || stripos($match->team_1_name, $teamName) !== false);

            foreach ($match->games as $game) {
                $myPicks = $isTeam1 ? $game->team_1_picks : $game->team_2_picks;
                $enemyPicks = $isTeam1 ? $game->team_2_picks : $game->team_1_picks;

                if (empty($myPicks) || !is_array($myPicks)) {
                    continue;
                }

                $totalGames++;
                $isWin = $isTeam1 ? ($game->team_1_status === 'W') : ($game->team_2_status === 'W');
                if ($isWin) {
                    $wins++;
                } else {
                    $losses++;
                }

                // 1. Akumulasi Hero Picks tim
                $cleanPicks = [];
                foreach ($myPicks as $h) {
                    $hName = trim((string)$h);
                    if (empty($hName)) continue;
                    $cleanPicks[] = $hName;

                    $heroPicksCount[$hName] = ($heroPicksCount[$hName] ?? 0) + 1;
                    if ($isWin) {
                        $heroWinsCount[$hName] = ($heroWinsCount[$hName] ?? 0) + 1;
                    }
                }

                // 2. Akumulasi Sinergi Co-occurrence tim
                $pickCount = count($cleanPicks);
                for ($i = 0; $i < $pickCount; $i++) {
                    for ($j = $i + 1; $j < $pickCount; $j++) {
                        $p1 = $cleanPicks[$i];
                        $p2 = $cleanPicks[$j];
                        $pairKey1 = strtolower($p1 . '|' . $p2);
                        $pairKey2 = strtolower($p2 . '|' . $p1);

                        if (!isset($teamSynergyPairs[$pairKey1])) {
                            $teamSynergyPairs[$pairKey1] = ['hero_a' => $p1, 'hero_b' => $p2, 'picks' => 0, 'wins' => 0];
                        }
                        $teamSynergyPairs[$pairKey1]['picks']++;
                        if ($isWin) {
                            $teamSynergyPairs[$pairKey1]['wins']++;
                        }

                        // Simpan referensi bolak-balik untuk lookup cepat
                        $teamSynergyPairs[$pairKey2] = &$teamSynergyPairs[$pairKey1];
                    }
                }

                // 3. Akumulasi Head-to-Head Counter tim vs Musuh
                if (!empty($enemyPicks) && is_array($enemyPicks)) {
                    foreach ($cleanPicks as $myH) {
                        foreach ($enemyPicks as $eH) {
                            $eHName = trim((string)$eH);
                            if (empty($eHName)) continue;

                            $h2hKey = strtolower($myH . '|vs|' . $eHName);
                            if (!isset($teamCounterMatchups[$h2hKey])) {
                                $teamCounterMatchups[$h2hKey] = ['hero' => $myH, 'enemy' => $eHName, 'matches' => 0, 'wins' => 0];
                            }
                            $teamCounterMatchups[$h2hKey]['matches']++;
                            if ($isWin) {
                                $teamCounterMatchups[$h2hKey]['wins']++;
                            }
                        }
                    }
                }

                // 4. Akumulasi Bans (Banned oleh tim ini & Banned oleh musuh saat menghadapi tim ini)
                $myBans = $isTeam1 ? ($game->team_1_bans ?? []) : ($game->team_2_bans ?? []);
                $enemyBans = $isTeam1 ? ($game->team_2_bans ?? []) : ($game->team_1_bans ?? []);

                if (is_array($myBans)) {
                    foreach ($myBans as $b) {
                        $bName = trim((string)$b);
                        if (!empty($bName)) {
                            $bansByTeamCount[$bName] = ($bansByTeamCount[$bName] ?? 0) + 1;
                            $totalBansCount[$bName] = ($totalBansCount[$bName] ?? 0) + 1;
                        }
                    }
                }

                if (is_array($enemyBans)) {
                    foreach ($enemyBans as $b) {
                        $bName = trim((string)$b);
                        if (!empty($bName)) {
                            $bansByEnemyCount[$bName] = ($bansByEnemyCount[$bName] ?? 0) + 1;
                            $totalBansCount[$bName] = ($totalBansCount[$bName] ?? 0) + 1;
                        }
                    }
                }

                // 5. Analisis Komposisi Game untuk Deteksi Playstyle
                $fighterCount = 0;
                $tankCount = 0;
                $assassinCount = 0;
                $scalerCount = 0;
                $supportCount = 0;
                $earlyCount = 0;

                foreach ($cleanPicks as $hName) {
                    $hObj = $allHeroes[strtolower($hName)] ?? null;
                    $cls = $hObj ? strtolower($hObj->primary_class ?: $hObj->class) : '';
                    $hLower = strtolower($hName);

                    if (str_contains($cls, 'fighter')) $fighterCount++;
                    if (str_contains($cls, 'tank') || in_array($hLower, $knownHeavyTanks)) $tankCount++;
                    if (in_array($hLower, $knownAssassins) || str_contains($cls, 'assassin')) $assassinCount++;
                    if (in_array($hLower, $knownLateScalers) || str_contains($cls, 'marksman')) $scalerCount++;
                    if (in_array($hLower, $knownHealerSupports) || str_contains($cls, 'support')) $supportCount++;
                    if (in_array($hLower, $knownEarlyAggro)) $earlyCount++;
                }

                if ($fighterCount >= 2) $archetypes['Double Fighter']++;
                if ($tankCount >= 2) $archetypes['Tank Jungler']++;
                if ($assassinCount >= 1) $archetypes['Assassin Snowball']++;
                if ($scalerCount >= 2) $archetypes['Late Game Scaling']++;
                if ($supportCount >= 1) $archetypes['Support Utility (UBE)']++;
                if ($earlyCount >= 2) $archetypes['Early Game Aggro']++;
            }
        }

        if ($totalGames === 0) {
            return null;
        }

        $overallWinRate = round(($wins / $totalGames) * 100, 1);

        // A. Format Top Heroes (5–8 Hero Paling Sering Di-pick)
        $topHeroesList = [];
        $heroPoolKeys = [];
        $heroStatsMap = [];

        arsort($heroPicksCount);
        foreach ($heroPicksCount as $hName => $picks) {
            $hWins = $heroWinsCount[$hName] ?? 0;
            $hLosses = $picks - $hWins;
            $hWr = round(($hWins / $picks) * 100, 1);
            $pickRate = round(($picks / $totalGames) * 100, 1);

            $hObj = $allHeroes[strtolower($hName)] ?? null;
            $portrait = $hObj ? $hObj->portrait : $this->csvService->resolveHeroPortrait($hName);
            $role = $hObj ? ($hObj->primary_class ?: $hObj->class) : 'Hero';
            $lane = $hObj ? ($hObj->laning ?: 'Flex') : 'Flex';

            $heroEntry = [
                'hero'               => $hName,
                'picks'              => $picks,
                'wins'               => $hWins,
                'losses'             => $hLosses,
                'win_rate'           => $hWr,
                'win_rate_formatted' => $hWr . '%',
                'pick_rate'          => $pickRate,
                'pick_rate_formatted'=> $pickRate . '%',
                'portrait'           => $portrait,
                'role'               => $role,
                'lane'               => $lane,
            ];

            $heroStatsMap[strtolower($hName)] = $heroEntry;

            if (count($topHeroesList) < 8) {
                $topHeroesList[] = $heroEntry;
                $heroPoolKeys[] = strtolower($hName);
            }
        }

        // B. Format Top Bans (Top 5 Hero Paling Sering Di-ban tim / lawan)
        $topBansList = [];
        arsort($totalBansCount);
        foreach ($totalBansCount as $bName => $bCount) {
            if (count($topBansList) >= 5) break;

            $bRate = round(($bCount / $totalGames) * 100, 1);
            $hObj = $allHeroes[strtolower($bName)] ?? null;
            $portrait = $hObj ? $hObj->portrait : $this->csvService->resolveHeroPortrait($bName);

            $topBansList[] = [
                'hero'               => $bName,
                'bans'               => $bCount,
                'ban_rate'           => $bRate,
                'ban_rate_formatted' => $bRate . '%',
                'banned_by_team'     => $bansByTeamCount[$bName] ?? 0,
                'banned_by_enemy'    => $bansByEnemyCount[$bName] ?? 0,
                'portrait'           => $portrait,
            ];
        }

        // C. Deteksi Playstyle Tag Otomatis
        arsort($archetypes);
        $primaryPlaystyle = array_key_first($archetypes);
        $primaryPlaystyleCount = $archetypes[$primaryPlaystyle];
        $primaryPlaystylePct = round(($primaryPlaystyleCount / max(1, $totalGames)) * 100);

        $playstyleDescriptions = [
            'Double Fighter'        => "Kerap mengandalkan formasi 2 Fighter untuk dominasi pertarungan objektif Turtle dan invasi jungle.",
            'Tank Jungler'          => "Mengutamakan frontline tebal dengan Tank Jungler sustain tinggi untuk penguasaan retribution dan lord.",
            'Assassin Snowball'     => "Berfokus pada Assassin berkecepatan tinggi untuk pick-off instan dan mematikan pergerakan backline lawan.",
            'Late Game Scaling'     => "Memilih komposisi scaling dengan proteksi maksimal menuju fase late game (12+ menit).",
            'Support Utility (UBE)' => "Menggunakan Support Healer / Enabler untuk formasi UBE solid dalam teamfight berkelanjutan.",
            'Early Game Aggro'      => "Menekan tempo permainan sejak menit awal (0-5 menit) dengan inisiator agresif.",
        ];

        $compositionBreakdown = [];
        foreach ($archetypes as $archName => $cnt) {
            $compositionBreakdown[] = [
                'name'       => $archName,
                'count'      => $cnt,
                'percentage' => round(($cnt / max(1, $totalGames)) * 100, 1),
            ];
        }

        // Hitung win rate persentase sinergi & counter
        $computedSynergies = [];
        foreach ($teamSynergyPairs as $k => $item) {
            $computedSynergies[$k] = [
                'hero_a'   => $item['hero_a'],
                'hero_b'   => $item['hero_b'],
                'picks'    => $item['picks'],
                'wins'     => $item['wins'],
                'win_rate' => round(($item['wins'] / max(1, $item['picks'])) * 100, 1),
            ];
        }

        $computedCounters = [];
        foreach ($teamCounterMatchups as $k => $item) {
            $computedCounters[$k] = [
                'hero'     => $item['hero'],
                'enemy'    => $item['enemy'],
                'matches'  => $item['matches'],
                'wins'     => $item['wins'],
                'losses'   => $item['matches'] - $item['wins'],
                'win_rate' => round(($item['wins'] / max(1, $item['matches'])) * 100, 1),
            ];
        }

        $result = [
            'team_name'             => $teamName,
            'team_logo'             => getTeamLogo($teamName),
            'total_games'           => $totalGames,
            'wins'                  => $wins,
            'losses'                => $losses,
            'win_rate'              => $overallWinRate,
            'win_rate_formatted'    => $overallWinRate . '%',
            'top_heroes'            => $topHeroesList,
            'top_bans'              => $topBansList,
            'playstyle_tag'         => $primaryPlaystyle,
            'playstyle_frequency'   => "{$primaryPlaystylePct}% Game ({$primaryPlaystyleCount}/{$totalGames})",
            'playstyle_description' => $playstyleDescriptions[$primaryPlaystyle] ?? "Gaya main kompetitif MPL ID S18.",
            'composition_breakdown' => $compositionBreakdown,
            'hero_pool_keys'        => $heroPoolKeys,
            'hero_stats'            => $heroStatsMap,
            'synergies'             => $computedSynergies,
            'counters'              => $computedCounters,
        ];

        self::$cache[$teamName] = $result;
        return $result;
    }

    /**
     * Mengambil daftar seluruh tim peserta MPL ID S18 beserta ringkasan Draft DNA
     */
    public function getAllTeamsDna(): array
    {
        $teams = [
            'Alter Ego',
            'Bigetron By Vitality',
            'Dewa United',
            'EVOS',
            'Geek Fam',
            'Natus Vincere',
            'ONIC',
            'RRQ',
            'Team Liquid ID',
        ];

        $results = [];
        foreach ($teams as $t) {
            $dna = $this->getTeamDraftDna($t);
            if ($dna) {
                $results[] = [
                    'team_name'             => $dna['team_name'],
                    'team_logo'             => $dna['team_logo'],
                    'total_games'           => $dna['total_games'],
                    'wins'                  => $dna['wins'],
                    'losses'                => $dna['losses'],
                    'win_rate'              => $dna['win_rate'],
                    'win_rate_formatted'    => $dna['win_rate_formatted'],
                    'playstyle_tag'         => $dna['playstyle_tag'],
                    'playstyle_frequency'   => $dna['playstyle_frequency'],
                    'playstyle_description' => $dna['playstyle_description'],
                    'top_heroes'            => $dna['top_heroes'],
                    'top_bans'              => $dna['top_bans'],
                    'top_hero_names'        => array_column(array_slice($dna['top_heroes'], 0, 5), 'hero'),
                ];
            }
        }

        return $results;
    }

    /**
     * Profil dasar / fallback Draft DNA untuk 9 tim MPL ID Season 18
     */
    public function getBaselineTeamDna(string $teamName): array
    {
        $tag = match($teamName) {
            'Alter Ego' => 'Double Fighter',
            'Bigetron By Vitality', 'ONIC' => 'Late Game Scaling',
            'RRQ', 'EVOS' => 'Assassin Snowball',
            'Geek Fam', 'Natus Vincere' => 'Double Fighter',
            'Team Liquid ID' => 'Late Game Scaling',
            'Dewa United' => 'Tank Jungler',
            default => 'Balanced Teamfight',
        };

        return [
            'team_name'             => $teamName,
            'team_logo'             => getTeamLogo($teamName),
            'total_games'           => 0,
            'wins'                  => 0,
            'losses'                => 0,
            'win_rate'              => 50.0,
            'win_rate_formatted'    => '50.0%',
            'top_heroes'            => [],
            'top_bans'              => [],
            'hero_pool_keys'        => [],
            'hero_stats'            => [],
            'playstyle_tag'         => $tag,
            'playstyle_frequency'   => 0,
            'playstyle_description' => "Draft DNA untuk {$teamName} (fokus playstyle turnamen {$tag}).",
            'composition_breakdown' => [],
            'synergies'             => [],
            'counters'              => [],
        ];
    }

    /**
     * Mencari 2-3 pertandingan asli turnamen MPL ID S18 yang paling mirip dengan draft saat ini.
     * Prioritas: match milik tim yang dipilih (team_name) dengan irisan hero pick terbanyak.
     * Jika hero pick belum ada di simulator, kembalikan 3 pertandingan terbaik / terbaru tim tersebut.
     *
     * @param array $currentPicks Hero yang sedang di-pick di simulator
     * @param string|null $teamInput Nama tim yang dipilih (default 'Alter Ego')
     * @param int $limit Jumlah game yang dikembalikan (default 3)
     * @return array
     */
    public function getSimilarMatches(array $currentPicks = [], ?string $teamInput = 'Alter Ego', int $limit = 3): array
    {
        $teamName = $this->normalizeTeamName($teamInput) ?: 'Alter Ego';

        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('match_games') || !\Illuminate\Support\Facades\Schema::hasTable('match_datasets')) {
                return [];
            }
        } catch (\Throwable $e) {
            return [];
        }

        $cleanCurrentPicks = array_values(array_filter(array_map(function($p) {
            return strtolower(trim((string)$p));
        }, $currentPicks)));

        $games = MatchGame::with('dataset')
            ->whereHas('dataset', function($sub) use ($teamName) {
                $sub->where('team_1_name', 'like', "%{$teamName}%")
                    ->orWhere('team_2_name', 'like', "%{$teamName}%");
            })
            ->whereNotNull('team_1_picks')
            ->where('team_1_picks', '!=', '[]')
            ->where('team_1_picks', '!=', '')
            ->get();

        if ($games->isEmpty()) {
            return [];
        }

        $csv = app(CsvDataService::class);
        $scored = [];
        foreach ($games as $game) {
            $dataset = $game->dataset;
            $t1Name = $dataset->team_1_name ?? 'Team 1';
            $t2Name = $dataset->team_2_name ?? 'Team 2';

            $isTeam1 = (stripos($t1Name, $teamName) !== false);
            $myPicks = $isTeam1 ? $game->team_1_picks : $game->team_2_picks;
            $enemyPicks = $isTeam1 ? $game->team_2_picks : $game->team_1_picks;

            if (empty($myPicks) || !is_array($myPicks)) {
                continue;
            }

            $myStatus = $isTeam1 ? ($game->team_1_status ?? 'W') : ($game->team_2_status ?? 'L');
            $enemyStatus = ($myStatus === 'W') ? 'L' : 'W';

            $myPicksClean = array_map(fn($p) => strtolower(trim((string)$p)), $myPicks);
            $matchingHeroes = !empty($cleanCurrentPicks) ? array_values(array_intersect($cleanCurrentPicks, $myPicksClean)) : [];
            $matchCount = count($matchingHeroes);

            $stage = !empty($dataset->formatted_stage_name) ? $dataset->formatted_stage_name : (!empty($dataset->stage_name) ? $dataset->stage_name : 'Regular Season');
            $score = ($dataset && $dataset->team_1_score !== null && $dataset->team_2_score !== null)
                ? "{$dataset->team_1_score} : {$dataset->team_2_score}"
                : "Game {$game->game_number}";
            $duration = !empty($game->duration) ? $game->duration : '15:20';

            $teamPicksWithPortraits = array_map(function($p) use ($csv) {
                return [
                    'name'     => $p,
                    'portrait' => $csv->resolveHeroPortrait($p),
                ];
            }, array_values($myPicks));

            $enemyPicksWithPortraits = array_map(function($p) use ($csv) {
                return [
                    'name'     => $p,
                    'portrait' => $csv->resolveHeroPortrait($p),
                ];
            }, is_array($enemyPicks) ? array_values($enemyPicks) : []);

            $scored[] = [
                'game_id'                   => $game->id,
                'match_id'                  => $game->match_id ?? ($dataset->match_id ?? 'M-MPL'),
                'game_number'               => $game->game_number,
                'stage'                     => $stage,
                'team_1_name'               => $t1Name,
                'team_2_name'               => $t2Name,
                'team_1_logo'               => getTeamLogo($t1Name),
                'team_2_logo'               => getTeamLogo($t2Name),
                'score'                     => $score,
                'team_status'               => $myStatus,
                'enemy_status'              => $enemyStatus,
                'status_label'              => ($myStatus === 'W') ? 'MENANG' : 'KALAH',
                'duration'                  => $duration,
                'team_picks'                => array_values($myPicks),
                'enemy_picks'               => is_array($enemyPicks) ? array_values($enemyPicks) : [],
                'team_picks_with_portraits' => $teamPicksWithPortraits,
                'enemy_picks_with_portraits'=> $enemyPicksWithPortraits,
                'matched_count'             => $matchCount,
                'matched_label'             => $matchCount > 0 ? "Mirip {$matchCount} Hero" : "Pertandingan Referensi",
                'matching_heroes'           => $matchingHeroes,
                'is_win'                    => ($myStatus === 'W'),
            ];
        }

        // Sorting:
        // Jika ada hero terpilih di simulator, utamakan matched_count terbanyak, lalu game menang (W), lalu id terbaru
        // Jika simulator kosong, utamakan game menang (W), lalu id terbaru
        usort($scored, function($a, $b) use ($cleanCurrentPicks) {
            if (!empty($cleanCurrentPicks)) {
                if ($b['matched_count'] !== $a['matched_count']) {
                    return $b['matched_count'] <=> $a['matched_count'];
                }
            }
            if ($b['is_win'] !== $a['is_win']) {
                return $b['is_win'] ? 1 : -1;
            }
            return $b['game_id'] <=> $a['game_id'];
        });

        return array_slice($scored, 0, $limit);
    }
}

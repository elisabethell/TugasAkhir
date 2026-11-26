<?php

namespace App\Services;

use App\Models\Hero;
use App\Models\MatchGame;
use Illuminate\Support\Facades\Schema;

/**
 * DraftRecommendationService
 *
 * Mesin Rekomendasi Draft Pick & Ban berbasis arsitektur:
 * Hybrid Simple Additive Weighting (SAW) + Bayesian Shrinkage
 * terintegrasi dengan data MPL ID S18 dan 5 atribut kuantitatif Coach.
 */
class DraftRecommendationService extends DraftInferenceService
{
    /**
     * Konstanta Bobot SAW & Pseudo-count Bayesian Shrinkage.
     * Dapat disesuaikan dengan mudah setelah bobot AHP dari Coach ditetapkan.
     */
    public const ALPHA = 0.40;      // Bobot Draft DNA (Preferensi Tim)
    public const BETA = 0.35;       // Bobot Kecocokan Komposisi (5 Atribut Coach)
    public const GAMMA = 0.25;      // Bobot Counter Musuh (Head-to-Head)
    public const M_SHRINKAGE = 5;   // Pseudo-count (m) untuk Bayesian Shrinkage

    public const COACH_ATTRIBUTES = [
        'sustainability',
        'attack_power',
        'crowd_control',
        'mobility',
        'lane_control',
    ];

    /**
     * Menghitung Top N rekomendasi hero menggunakan arsitektur 4-lapis
     * Hybrid SAW + Bayesian Shrinkage berbasis dataset MPL ID S18 dan 5 atribut Coach.
     *
     * @param array       $allyPicks    Hero sekutu yang sudah di-pick
     * @param array       $enemyPicks   Hero musuh yang sudah di-pick
     * @param array       $bannedHeroes Hero yang sudah di-ban
     * @param string|null $teamName     Nama tim acuan Draft DNA (opsional)
     * @param int         $limit        Jumlah kandidat teratas yang dikembalikan (default: 10)
     * @return array
     */
    public function recommend(
        array $allyPicks = [],
        array $enemyPicks = [],
        array $bannedHeroes = [],
        ?string $teamName = null,
        int $limit = 10
    ): array {
        $cleanAlly = $this->sanitizeNames($allyPicks);
        $cleanEnemy = $this->sanitizeNames($enemyPicks);
        $cleanBanned = $this->sanitizeNames($bannedHeroes);

        $normalizedTeam = null;
        if ($teamName !== null && trim($teamName) !== '') {
            $normalizedTeam = $this->teamDnaService->normalizeTeamName($teamName);
        }

        // Ambil seluruh katalog hero (prioritaskan tabel heroes, fallback ke CsvDataService jika kosong)
        $allHeroes = $this->loadHeroCatalog();
        $heroByKey = [];
        foreach ($allHeroes as $hero) {
            $heroByKey[$this->heroKey($hero->hero_name)] = $hero;
        }

        // Lapisan 1 — Filter (Hard Constraint):
        // Eksklusi hero yang sudah ada di banned_heroes, ally_picks, atau enemy_picks
        $excludedKeys = [];
        foreach (array_merge($cleanBanned, $cleanAlly, $cleanEnemy) as $exName) {
            $excludedKeys[$this->heroKey($exName)] = true;
        }

        $candidates = [];
        foreach ($allHeroes as $hero) {
            $key = $this->heroKey($hero->hero_name);
            if (!isset($excludedKeys[$key])) {
                $candidates[$key] = $hero;
            }
        }

        // Agregasi data pertandingan MPL ID S18
        $matchAgg = $this->aggregateMplMatchStats($normalizedTeam, $heroByKey);

        $hasComp = count($cleanAlly) > 0;
        $hasCounter = count($cleanEnemy) > 0;

        // Normalisasi ulang bobot SAW agar total bobot komponen aktif == 1.0
        $rawAlpha = self::ALPHA;
        $rawBeta = $hasComp ? self::BETA : 0.0;
        $rawGamma = $hasCounter ? self::GAMMA : 0.0;
        $weightSum = max(1e-9, $rawAlpha + $rawBeta + $rawGamma);

        $wAlpha = $rawAlpha / $weightSum;
        $wBeta = $rawBeta / $weightSum;
        $wGamma = $rawGamma / $weightSum;

        // Hitung Target Profil Tim Pemenang T_k (dengan shrinkage jika sampel menang tim < 5)
        $targetProfile = $this->computeWinningTargetProfile(
            $matchAgg['team_winning_vectors'],
            $matchAgg['global_winning_vectors']
        );

        // Hitung profil rata-rata tim sekutu saat ini A_k(C) dan jaraknya D(C)
        $allyCount = count($cleanAlly);
        $allyProfile = null;
        $currentGap = 0.0;

        if ($hasComp) {
            $allyVectors = [];
            foreach ($cleanAlly as $allyName) {
                $aKey = $this->heroKey($allyName);
                $allyHero = $heroByKey[$aKey] ?? null;
                $allyVectors[] = $this->extractNormalizedAttributes($allyHero);
            }
            $allyProfile = $this->meanVector($allyVectors);
            $currentGap = $this->l1Distance($targetProfile, $allyProfile);
        }

        // Lapisan 2 — Hitung komponen skor mentah S_dna, S_comp, S_counter per kandidat
        $rawDna = [];
        $rawComp = [];
        $rawCounter = [];
        $candidateMeta = [];

        $m = self::M_SHRINKAGE;
        $totalGlobalPicks = max(1, $matchAgg['total_global_picks']);
        $totalTeamPicks = $matchAgg['total_team_picks'];

        foreach ($candidates as $hKey => $hero) {
            // a. S_dna(h) — Bayesian Shrinkage Preferensi Tim
            $nGlobal = $matchAgg['global_pick_counts'][$hKey] ?? 0;
            $nTeam = $matchAgg['team_pick_counts'][$hKey] ?? 0;
            $pGlobal = $matchAgg['total_global_picks'] > 0 ? ($nGlobal / $totalGlobalPicks) : 0.0;
            $sDna = ($nTeam + ($m * $pGlobal)) / ($totalTeamPicks + $m);
            $rawDna[$hKey] = $sDna;

            // b. S_comp(h) — Penutupan Gap terhadap Target Profil Tim Pemenang T_k
            $heroAttrs = $this->extractNormalizedAttributes($hero);
            $sComp = 0.0;
            $gapAfter = null;

            if ($hasComp && $allyProfile !== null) {
                $simulatedProfile = [];
                foreach (self::COACH_ATTRIBUTES as $attr) {
                    $simulatedProfile[$attr] = (($allyCount * $allyProfile[$attr]) + $heroAttrs[$attr]) / ($allyCount + 1);
                }
                $gapAfter = $this->l1Distance($targetProfile, $simulatedProfile);
                $sComp = $currentGap - $gapAfter; // Delta(h) = D(C) - D(C+h)
                $rawComp[$hKey] = $sComp;
            }

            // c. S_counter(h) — Bayesian Shrinkage Win Rate vs Musuh
            $hGamesGlobal = $matchAgg['global_game_counts'][$hKey] ?? 0;
            $hWinsGlobal = $matchAgg['global_win_counts'][$hKey] ?? 0;
            $wGlobal = $hGamesGlobal > 0 ? ($hWinsGlobal / $hGamesGlobal) : 0.5;

            $sCounter = 0.0;
            $matchupDetails = [];

            if ($hasCounter) {
                $sumEnemyWr = 0.0;
                foreach ($cleanEnemy as $enemyName) {
                    $eKey = $this->heroKey($enemyName);
                    $h2h = $matchAgg['matchups'][$hKey][$eKey] ?? ['games' => 0, 'wins' => 0];
                    $gameVsE = $h2h['games'];
                    $winVsE = $h2h['wins'];
                    $wHat = ($winVsE + ($m * $wGlobal)) / ($gameVsE + $m);
                    $sumEnemyWr += $wHat;

                    $matchupDetails[] = [
                        'enemy'       => $enemyName,
                        'games'       => $gameVsE,
                        'wins'        => $winVsE,
                        'shrunk_wr'   => round($wHat, 4),
                    ];
                }
                $sCounter = $sumEnemyWr / count($cleanEnemy);
                $rawCounter[$hKey] = $sCounter;
            }

            $candidateMeta[$hKey] = [
                'hero'           => $hero,
                'attrs_norm'     => $heroAttrs,
                'n_team'         => $nTeam,
                'n_global'       => $nGlobal,
                'p_global'       => $pGlobal,
                'w_global'       => $wGlobal,
                'gap_after'      => $gapAfter,
                'matchup_detail' => $matchupDetails,
            ];
        }

        // Lapisan 4 — Normalisasi Min-Max ke rentang [0, 1] (tangani max == min => 0.5)
        $normDna = $this->minMaxNormalize($rawDna);
        $normComp = $hasComp ? $this->minMaxNormalize($rawComp) : [];
        $normCounter = $hasCounter ? $this->minMaxNormalize($rawCounter) : [];

        $scoredResults = [];
        foreach ($candidates as $hKey => $hero) {
            $sDnaNorm = $normDna[$hKey] ?? 0.5;
            $sCompNorm = $hasComp ? ($normComp[$hKey] ?? 0.5) : 0.0;
            $sCounterNorm = $hasCounter ? ($normCounter[$hKey] ?? 0.5) : 0.0;

            $sawScore = ($wAlpha * $sDnaNorm) + ($wBeta * $sCompNorm) + ($wGamma * $sCounterNorm);
            $meta = $candidateMeta[$hKey];

            $scoredResults[] = [
                'hero_id'     => (string) ($hero->hero_id ?? $hero->id ?? $hKey),
                'hero'        => $hero->hero_name,
                'nama'        => $hero->hero_name,
                'portrait'    => $hero->portrait ?? '',
                'role'        => $hero->role_1 ?: ($hero->primary_class ?? $hero->class ?? 'Hero'),
                'role_1'      => $hero->role_1 ?? ($hero->primary_class ?? null),
                'role_2'      => $hero->role_2 ?? null,
                'lane_1'      => $hero->lane_1 ?? null,
                'lane_2'      => $hero->lane_2 ?? null,
                'saw_score'   => round($sawScore, 4),
                'total_score' => round($sawScore * 100, 1),
                's_dna'       => [
                    'raw'      => round($rawDna[$hKey] ?? 0.0, 4),
                    'norm'     => round($sDnaNorm, 4),
                    'weight'   => round($wAlpha, 4),
                    'n_team'   => $meta['n_team'],
                    'n_global' => $meta['n_global'],
                    'p_global' => round($meta['p_global'], 4),
                ],
                's_comp'      => [
                    'active'     => $hasComp,
                    'raw'        => $hasComp ? round($rawComp[$hKey] ?? 0.0, 4) : null,
                    'norm'       => $hasComp ? round($sCompNorm, 4) : null,
                    'weight'     => round($wBeta, 4),
                    'gap_before' => $hasComp ? round($currentGap, 4) : null,
                    'gap_after'  => $meta['gap_after'] !== null ? round($meta['gap_after'], 4) : null,
                ],
                's_counter'   => [
                    'active'   => $hasCounter,
                    'raw'      => $hasCounter ? round($rawCounter[$hKey] ?? 0.0, 4) : null,
                    'norm'     => $hasCounter ? round($sCounterNorm, 4) : null,
                    'weight'   => round($wGamma, 4),
                    'w_global' => round($meta['w_global'], 4),
                    'matchups' => $meta['matchup_detail'],
                ],
                'coach_attributes' => [
                    'raw' => [
                        'sustainability' => (int) ($hero->sustainability ?? 5),
                        'attack_power'   => (int) ($hero->attack_power ?? 5),
                        'crowd_control'  => (int) ($hero->crowd_control ?? 5),
                        'mobility'       => (int) ($hero->mobility ?? 5),
                        'lane_control'   => (int) ($hero->lane_control ?? 5),
                    ],
                    'normalized' => $meta['attrs_norm'],
                ],
            ];
        }

        // Urutkan dari Score(h) tertinggi (tie-break: s_dna raw desc, lalu nama asc)
        usort($scoredResults, function (array $a, array $b) {
            if ($b['saw_score'] !== $a['saw_score']) {
                return $b['saw_score'] <=> $a['saw_score'];
            }
            if ($b['s_dna']['raw'] !== $a['s_dna']['raw']) {
                return $b['s_dna']['raw'] <=> $a['s_dna']['raw'];
            }
            return strcmp($a['hero'], $b['hero']);
        });

        return [
            'recommendations' => array_slice($scoredResults, 0, max(1, $limit)),
            'total_candidates' => count($scoredResults),
            'team_name'       => $normalizedTeam,
            'weights'         => [
                'alpha'       => round($wAlpha, 4),
                'beta'        => round($wBeta, 4),
                'gamma'       => round($wGamma, 4),
                'base_alpha'  => self::ALPHA,
                'base_beta'   => self::BETA,
                'base_gamma'  => self::GAMMA,
                'm_shrinkage' => self::M_SHRINKAGE,
            ],
            'active_components' => [
                's_dna'     => true,
                's_comp'    => $hasComp,
                's_counter' => $hasCounter,
            ],
            'target_profile' => $targetProfile,
            'ally_profile'   => $allyProfile,
            'current_gap'    => $hasComp ? round($currentGap, 4) : null,
        ];
    }

    /**
     * Override evaluateDraft untuk melampirkan hasil Hybrid SAW + Bayesian Shrinkage
     * sekaligus mempertahankan kompatibilitas penuh dengan RBR & API kontrak yang ada.
     */
    public function evaluateDraft(
        array $allyTeam = [],
        array $enemyTeam = [],
        array $bannedHeroes = [],
        array $allyLanes = [],
        array $enemyLanes = [],
        ?string $map = null,
        ?string $teamName = null
    ): array {
        $base = parent::evaluateDraft(
            $allyTeam,
            $enemyTeam,
            $bannedHeroes,
            $allyLanes,
            $enemyLanes,
            $map,
            $teamName
        );

        $sawTeam = ($teamName === null) ? 'Alter Ego' : $teamName;
        $sawResultAll = $this->recommend($allyTeam, $enemyTeam, $bannedHeroes, $sawTeam, 250);

        // Petakan skor SAW ke setiap kandidat di recommendations agar UI punya rincian transparan
        $sawByHero = [];
        foreach ($sawResultAll['recommendations'] as $item) {
            $sawByHero[$this->heroKey($item['hero'])] = $item;
        }

        $base['recommendations'] = array_map(function (array $rec) use ($sawByHero) {
            $key = $this->heroKey($rec['nama'] ?? $rec['hero'] ?? '');
            $saw = $sawByHero[$key] ?? null;
            if ($saw) {
                $rec['saw_score'] = $saw['saw_score'];
                $rec['s_dna'] = $saw['s_dna'];
                $rec['s_comp'] = $saw['s_comp'];
                $rec['s_counter'] = $saw['s_counter'];
                $rec['coach_attributes'] = $saw['coach_attributes'];
            }
            return $rec;
        }, $base['recommendations']);

        $base['saw_recommendations'] = array_slice($sawResultAll['recommendations'], 0, 10);
        $base['saw_meta'] = [
            'weights'           => $sawResultAll['weights'],
            'active_components' => $sawResultAll['active_components'],
            'target_profile'    => $sawResultAll['target_profile'],
            'ally_profile'      => $sawResultAll['ally_profile'],
            'current_gap'       => $sawResultAll['current_gap'],
            'total_candidates'  => $sawResultAll['total_candidates'],
        ];

        return $base;
    }

    /**
     * Menganalisis profil Draft DNA spesifik tim (MPL ID Season 18)
     */
    public function getTeamDraftDna(?string $teamName): ?array
    {
        return $this->teamDnaService->getTeamDraftDna($teamName);
    }

    /**
     * Mengambil daftar profil Draft DNA seluruh tim peserta MPL ID Season 18
     */
    public function getAllTeamsDna(): array
    {
        return $this->teamDnaService->getAllTeamsDna();
    }

    /**
     * Mencari 2-3 pertandingan asli turnamen MPL ID S18 yang paling mirip dengan draft saat ini
     */
    public function getSimilarMatches(array $currentPicks = [], ?string $teamName = 'Alter Ego', int $limit = 3): array
    {
        return $this->teamDnaService->getSimilarMatches($currentPicks, $teamName, $limit);
    }

    /**
     * Memuat katalog hero dari tabel heroes (prioritas utama) dengan fallback ke CsvDataService.
     */
    protected function loadHeroCatalog(): array
    {
        try {
            if (Schema::hasTable('heroes')) {
                $dbHeroes = Hero::all();
                if ($dbHeroes->isNotEmpty()) {
                    $csvByKey = $this->csvService->getHeroes()->keyBy(fn ($h) => $this->heroKey($h->hero_name));

                    return $dbHeroes->map(function (Hero $row) use ($csvByKey) {
                        $key = $this->heroKey($row->hero_name);
                        $csvFallback = $csvByKey[$key] ?? null;

                        return (object) [
                            'id'             => (int) $row->id,
                            'hero_id'        => trim((string) ($row->hero_id ?? '')),
                            'hero_name'      => trim((string) $row->hero_name),
                            'portrait'       => $csvFallback->portrait ?? $this->csvService->resolveHeroPortrait($row->hero_name, $row->portrait),
                            'class'          => (string) ($row->class ?? ($csvFallback->class ?? 'Hero')),
                            'primary_class'  => $csvFallback->primary_class ?? trim(explode('/', (string) ($row->class ?? 'Hero'))[0]),
                            'role_1'         => $row->role_1 ?? ($csvFallback->role_1 ?? null),
                            'role_2'         => $row->role_2 ?? ($csvFallback->role_2 ?? null),
                            'lane_1'         => $row->lane_1 ?? ($csvFallback->lane_1 ?? null),
                            'lane_2'         => $row->lane_2 ?? ($csvFallback->lane_2 ?? null),
                            'sustainability' => (int) ($row->sustainability ?? 5),
                            'attack_power'   => (int) ($row->attack_power ?? 5),
                            'crowd_control'  => (int) ($row->crowd_control ?? 5),
                            'mobility'       => (int) ($row->mobility ?? 5),
                            'lane_control'   => (int) ($row->lane_control ?? 5),
                        ];
                    })->values()->all();
                }
            }
        } catch (\Throwable $e) {
            // Fallback ke CsvDataService bila DB belum siap
        }

        return $this->csvService->getHeroes()->values()->all();
    }

    /**
     * Agregasi statistik pick global, pick tim, win rate, komposisi pemenang, dan H2H matchup dari match_games MPL ID S18.
     */
    protected function aggregateMplMatchStats(?string $teamName, array $heroByKey): array
    {
        $globalPickCounts = [];
        $globalGameCounts = [];
        $globalWinCounts = [];
        $teamPickCounts = [];
        $matchups = [];
        $globalWinningVectors = [];
        $teamWinningVectors = [];
        $totalGlobalPicks = 0;
        $totalTeamPicks = 0;

        try {
            if (Schema::hasTable('match_games') && Schema::hasTable('match_datasets')) {
                $games = MatchGame::with('dataset')
                    ->whereNotNull('team_1_picks')
                    ->where('team_1_picks', '!=', '[]')
                    ->where('team_1_picks', '!=', '')
                    ->whereNotNull('team_2_picks')
                    ->where('team_2_picks', '!=', '[]')
                    ->where('team_2_picks', '!=', '')
                    ->get();

                foreach ($games as $game) {
                    $dataset = $game->dataset;
                    $t1Name = $dataset->team_1_name ?? '';
                    $t2Name = $dataset->team_2_name ?? '';

                    $p1 = is_array($game->team_1_picks) ? $this->sanitizeNames($game->team_1_picks) : [];
                    $p2 = is_array($game->team_2_picks) ? $this->sanitizeNames($game->team_2_picks) : [];
                    if (empty($p1) || empty($p2)) {
                        continue;
                    }

                    $t1Win = (($game->team_1_status ?? '') === 'W');
                    $t2Win = (($game->team_2_status ?? '') === 'W');

                    $t1IsSelected = $teamName !== null && $this->matchesTeamName($t1Name, $teamName);
                    $t2IsSelected = $teamName !== null && $this->matchesTeamName($t2Name, $teamName);

                    $sides = [
                        ['picks' => $p1, 'enemy' => $p2, 'win' => $t1Win, 'is_team' => $t1IsSelected],
                        ['picks' => $p2, 'enemy' => $p1, 'win' => $t2Win, 'is_team' => $t2IsSelected],
                    ];

                    foreach ($sides as $side) {
                        $sideVectors = [];

                        foreach ($side['picks'] as $pickName) {
                            $hKey = $this->heroKey($pickName);
                            if ($hKey === '') {
                                continue;
                            }

                            $globalPickCounts[$hKey] = ($globalPickCounts[$hKey] ?? 0) + 1;
                            $globalGameCounts[$hKey] = ($globalGameCounts[$hKey] ?? 0) + 1;
                            if ($side['win']) {
                                $globalWinCounts[$hKey] = ($globalWinCounts[$hKey] ?? 0) + 1;
                            }
                            $totalGlobalPicks++;

                            if ($side['is_team']) {
                                $teamPickCounts[$hKey] = ($teamPickCounts[$hKey] ?? 0) + 1;
                                $totalTeamPicks++;
                            }

                            $sideVectors[] = $this->extractNormalizedAttributes($heroByKey[$hKey] ?? null);

                            // Catat head-to-head melawan setiap hero lawan di sisi seberang
                            foreach ($side['enemy'] as $enemyName) {
                                $eKey = $this->heroKey($enemyName);
                                if ($eKey === '') {
                                    continue;
                                }
                                if (!isset($matchups[$hKey][$eKey])) {
                                    $matchups[$hKey][$eKey] = ['games' => 0, 'wins' => 0];
                                }
                                $matchups[$hKey][$eKey]['games']++;
                                if ($side['win']) {
                                    $matchups[$hKey][$eKey]['wins']++;
                                }
                            }
                        }

                        // Simpan vektor rata-rata komposisi pemenang untuk Target Profil T_k
                        if ($side['win'] && !empty($sideVectors)) {
                            $compMean = $this->meanVector($sideVectors);
                            $globalWinningVectors[] = $compMean;
                            if ($side['is_team']) {
                                $teamWinningVectors[] = $compMean;
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Kembalikan agregasi kosong bila tabel belum siap
        }

        return [
            'global_pick_counts'     => $globalPickCounts,
            'global_game_counts'     => $globalGameCounts,
            'global_win_counts'      => $globalWinCounts,
            'team_pick_counts'       => $teamPickCounts,
            'matchups'               => $matchups,
            'global_winning_vectors' => $globalWinningVectors,
            'team_winning_vectors'   => $teamWinningVectors,
            'total_global_picks'     => $totalGlobalPicks,
            'total_team_picks'       => $totalTeamPicks,
        ];
    }

    /**
     * Menghitung Target Profil Tim Pemenang T_k dengan Bayesian Shrinkage
     * jika sampel game menang tim < 5.
     */
    protected function computeWinningTargetProfile(array $teamWinningVectors, array $globalWinningVectors): array
    {
        $neutral = array_fill_keys(self::COACH_ATTRIBUTES, 0.5);
        $globalMean = !empty($globalWinningVectors) ? $this->meanVector($globalWinningVectors) : $neutral;

        $nWinTeam = count($teamWinningVectors);
        if ($nWinTeam === 0) {
            return $globalMean;
        }

        $teamMean = $this->meanVector($teamWinningVectors);
        if ($nWinTeam >= 5) {
            return $teamMean;
        }

        $m = self::M_SHRINKAGE;
        $shrunk = [];
        foreach (self::COACH_ATTRIBUTES as $attr) {
            $shrunk[$attr] = (($nWinTeam * $teamMean[$attr]) + ($m * $globalMean[$attr])) / ($nWinTeam + $m);
        }

        return $shrunk;
    }

    /**
     * Mengekstrak 5 atribut Coach dari hero ke rentang [0, 1] (dibagi 10).
     */
    protected function extractNormalizedAttributes(?object $hero): array
    {
        $attrs = [];
        foreach (self::COACH_ATTRIBUTES as $attr) {
            $raw = $hero ? (float) ($hero->{$attr} ?? 5) : 5.0;
            $attrs[$attr] = max(0.0, min(1.0, $raw / 10.0));
        }
        return $attrs;
    }

    /**
     * Rata-rata vektor 5 atribut dari sekumpulan hero/komposisi.
     */
    protected function meanVector(array $vectors): array
    {
        $count = count($vectors);
        if ($count === 0) {
            return array_fill_keys(self::COACH_ATTRIBUTES, 0.5);
        }

        $sums = array_fill_keys(self::COACH_ATTRIBUTES, 0.0);
        foreach ($vectors as $vec) {
            foreach (self::COACH_ATTRIBUTES as $attr) {
                $sums[$attr] += (float) ($vec[$attr] ?? 0.5);
            }
        }

        $mean = [];
        foreach (self::COACH_ATTRIBUTES as $attr) {
            $mean[$attr] = $sums[$attr] / $count;
        }

        return $mean;
    }

    /**
     * Jarak L1 rata-rata 5 dimensi: D(C) = (1/5) * sum(|T_k - A_k(C)|).
     */
    protected function l1Distance(array $target, array $current): float
    {
        $sum = 0.0;
        foreach (self::COACH_ATTRIBUTES as $attr) {
            $sum += abs(($target[$attr] ?? 0.5) - ($current[$attr] ?? 0.5));
        }
        return $sum / count(self::COACH_ATTRIBUTES);
    }

    /**
     * Normalisasi Min-Max ke [0, 1]. Jika max == min, beri nilai 0.5 agar tidak division by zero.
     */
    protected function minMaxNormalize(array $values): array
    {
        if (empty($values)) {
            return [];
        }

        $min = min($values);
        $max = max($values);
        $range = $max - $min;

        $normalized = [];
        if (abs($range) < 1e-12) {
            foreach ($values as $key => $val) {
                $normalized[$key] = 0.5;
            }
            return $normalized;
        }

        foreach ($values as $key => $val) {
            $normalized[$key] = ($val - $min) / $range;
        }

        return $normalized;
    }

    protected function sanitizeNames(array $names): array
    {
        return array_values(array_filter(array_map(fn ($v) => trim((string) $v), $names), fn ($v) => $v !== ''));
    }

    protected function heroKey(string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim($name))) ?? '';
    }

    protected function matchesTeamName(string $candidateName, string $targetTeam): bool
    {
        if ($candidateName === '' || $targetTeam === '') {
            return false;
        }
        $normalizedCandidate = $this->teamDnaService->normalizeTeamName($candidateName);
        return strcasecmp($normalizedCandidate, $targetTeam) === 0
            || stripos($candidateName, $targetTeam) !== false;
    }
}

<?php

namespace App\Services;

use App\Models\Hero;
use App\Models\Dataset;
use App\Models\RbrRule;

class RbrInferenceEngine
{
    /**
     * Menganalisis komposisi tim menggunakan Rule-Based Reasoning (RBR) Forward Chaining.
     *
     * @param array $myTeam Array nama hero tim kita (maksimal 5)
     * @param array $enemyTeam Array nama hero tim lawan (opsional, maksimal 5)
     * @param string|null $map Varian map (Broken Walls, Dangerous Grass, Expanding Rivers, Flying Clouds)
     * @return array
     */
    public function analyze(array $myTeam, array $enemyTeam = [], ?string $map = null): array
    {
        // 1. Ambil data entitas hero dari database
        $cleanNames = array_map('trim', array_filter($myTeam));
        $heroes = Hero::whereIn('hero_name', $cleanNames)->get();

        $enemyCleanNames = array_map('trim', array_filter($enemyTeam));
        $enemyHeroes = !empty($enemyCleanNames) ? Hero::whereIn('hero_name', $enemyCleanNames)->get() : collect();

        // 2. Ekstraksi Fitur & Atribut Tim
        $classes = [];
        $specialties = [];
        $lanings = [];
        $powerSpikeScores = ['early' => 0, 'mid' => 0, 'late' => 0];

        $teamfightScore = 0;
        $pickOffScore = 0;
        $splitPushScore = 0;
        $pokeScore = 0;
        $frontlineScore = 0;
        $ccScore = 0;
        $physicalCount = 0;
        $magicCount = 0;

        foreach ($heroes as $h) {
            $heroClasses = array_map('trim', explode(',', strtolower($h->class ?? '')));
            $heroSpecs = array_map('trim', explode(',', strtolower($h->specialty ?? '')));
            $heroLanes = array_map('trim', explode(',', strtolower($h->laning ?? '')));

            $classes = array_merge($classes, $heroClasses);
            $specialties = array_merge($specialties, $heroSpecs);
            $lanings = array_merge($lanings, $heroLanes);

            // Hitung distribusi damage
            if (in_array('mage', $heroClasses) || in_array('magic', $heroSpecs)) {
                $magicCount++;
            } else {
                $physicalCount++;
            }

            // Frontline
            if (in_array('tank', $heroClasses) || in_array('fighter', $heroClasses) || in_array('guard', $heroSpecs)) {
                $frontlineScore += 25;
            }

            // Crowd Control
            if (in_array('control', $heroSpecs) || in_array('initiator', $heroSpecs)) {
                $ccScore += 25;
            }

            // Evaluasi Skor Archetype per Hero
            // Pick-off: Assassin, Burst, Finisher, Charge, Single target
            if (in_array('assassin', $heroClasses) || in_array('burst', $heroSpecs) || in_array('finisher', $heroSpecs) || in_array('charge', $heroSpecs)) {
                $pickOffScore += 22;
            }

            // Teamfight: Tank, Crowd Control, Initiator, AoE Damage (Mage, Area Fighter)
            if (in_array('tank', $heroClasses) || in_array('control', $heroSpecs) || in_array('initiator', $heroSpecs) || in_array('mage', $heroClasses)) {
                $teamfightScore += 20;
            }

            // Split push: High mobility, Push specialty, Fighter/Assassin solo laner
            if (in_array('push', $heroSpecs) || in_array('chase', $heroSpecs) || in_array('marksman', $heroClasses)) {
                $splitPushScore += 18;
            }

            // Poke / Siege: Mage Poke, Marksman range
            if (in_array('poke', $heroSpecs) || in_array('marksman', $heroClasses)) {
                $pokeScore += 20;
            }

            // Power Spike
            if (in_array('marksman', $heroClasses) || str_contains(strtolower($h->hero_name), 'cecilion') || str_contains(strtolower($h->hero_name), 'aldous') || str_contains(strtolower($h->hero_name), 'claude') || str_contains(strtolower($h->hero_name), 'karrie') || str_contains(strtolower($h->hero_name), 'moskov')) {
                $powerSpikeScores['late'] += 35;
                $powerSpikeScores['mid'] += 15;
            } elseif (in_array('assassin', $heroClasses) || in_array('burst', $heroSpecs)) {
                $powerSpikeScores['mid'] += 30;
                $powerSpikeScores['early'] += 20;
            } elseif (in_array('fighter', $heroClasses) && (str_contains(strtolower($h->hero_name), 'martis') || str_contains(strtolower($h->hero_name), 'dyrroth') || str_contains(strtolower($h->hero_name), 'hilda') || str_contains(strtolower($h->hero_name), 'jawhead'))) {
                $powerSpikeScores['early'] += 35;
                $powerSpikeScores['mid'] += 20;
            } else {
                $powerSpikeScores['mid'] += 25;
                $powerSpikeScores['early'] += 15;
                $powerSpikeScores['late'] += 15;
            }
        }

        // Normalisasi Skor ke skala 0 - 100
        $teamfightScore = min(100, $teamfightScore);
        $pickOffScore = min(100, $pickOffScore);
        $splitPushScore = min(100, $splitPushScore);
        $pokeScore = min(100, $pokeScore);
        $frontlineScore = min(100, $frontlineScore);
        $ccScore = min(100, $ccScore);

        // 3. INFERENCE ENGINE (Evaluasi Rule-Based Reasoning)
        $firedRules = [];
        $archetype = 'Flexible / Balanced Standard';
        $powerSpikeFocus = 'Mid Game Transition';
        $winningConditions = [];
        $warnings = [];
        $mapSynergy = null;

        // Ambil rules dari database
        $dbRules = RbrRule::where('status_aktif', 1)->get();

        // Rule R01: Pick-off vs Teamfight
        $assassinCount = count(array_filter($classes, fn($c) => $c === 'assassin'));
        $tankCount = count(array_filter($classes, fn($c) => $c === 'tank'));
        $marksmanCount = count(array_filter($classes, fn($c) => $c === 'marksman'));
        $supportCount = count(array_filter($classes, fn($c) => $c === 'support'));
        $healerNames = ['angela', 'rafaela', 'estes', 'floryn', 'mathilda'];
        $hasHealer = $heroes->contains(fn($h) => in_array(strtolower($h->hero_name), $healerNames));

        // Rule Archetype RBR
        if ($hasHealer && $marksmanCount >= 1) {
            $archetype = 'Protect the Hypercarry (UBE Strategy)';
            $firedRules[] = [
                'rule_id' => 'RBR-05',
                'title' => 'Protect the Hypercarry Strategy',
                'condition' => 'Tim memiliki Healer/Support Enabler dan Hard Marksman Carry',
                'action' => 'Fokus memberikan proteksi dan ruang farming penuh kepada Gold Lane.',
            ];
            $winningConditions[] = '4 pemain bertugas sebagai tameng dan pembuat ruang (space maker) bagi Carry utama di menit 0-10. Jangan biarkan Carry mati terculik sebelum memiliki 3 core item.';
            $winningConditions[] = 'Ketika Carry sudah jadi di menit 12+, berkumpul dalam formasi rapat di belakang Tank dan menangkan war penentuan di pit Lord.';
        } elseif ($pickOffScore > $teamfightScore && $pickOffScore >= 50) {
            $archetype = 'Pick-Off (Culik Lawan)';
            $firedRules[] = [
                'rule_id' => 'RBR-01',
                'title' => 'Pick-Off Dominance Rule',
                'condition' => 'Skor Pick-off (' . $pickOffScore . ') melebihi Teamfight (' . $teamfightScore . ') dan Burst Singletarget Tinggi',
                'action' => 'Hindari war terbuka 5v5. Manfaatkan bush ambush untuk mengeliminasi 1 musuh sebelum objektif.',
            ];
            $winningConditions[] = 'Gaya bermain utama adalah Pick-Off: Jangan memaksakan teamfight besar 5v5 secara terbuka di area lapang.';
            $winningConditions[] = 'Sebelum kontes objektif besar (Turtle atau Lord), kuasai semak-semak sungai (bush control) dan culik 1 hero musuh terlebih dahulu untuk menciptakan keunggulan jumlah 5v4.';
            $winningConditions[] = 'Target prioritas eliminasi adalah Jungler musuh (untuk mematikan Retribution lawan) atau Goldlaner musuh sebelum war dimulai.';
        } elseif ($teamfightScore >= 50) {
            $archetype = 'Teamfight / AoE Wiping';
            $firedRules[] = [
                'rule_id' => 'RBR-02',
                'title' => 'Teamfight Wiping Rule',
                'condition' => 'Skor Teamfight (' . $teamfightScore . ') tinggi dengan Crowd Control & Area Damage memadai',
                'action' => 'Pancing musuh berkumpul di pit Turtle / Lord, lalu inisiasi combo CC berantai.',
            ];
            $winningConditions[] = 'Pancing musuh berkumpul di choke point atau lorong sempit sekitar pit Turtle / Lord.';
            $winningConditions[] = 'Inisiasi dengan combo Crowd Control berantai, lalu timpakan seluruh Burst/AoE Damage secara bersamaan untuk meratakan musuh.';
        } elseif ($splitPushScore >= 50) {
            $archetype = 'Split Push & Macro Pressure';
            $firedRules[] = [
                'rule_id' => 'RBR-03',
                'title' => 'Split Push Macro Rule',
                'condition' => 'Mobilitas hero tinggi dan memiliki keunggulan laning/pushing independen',
                'action' => 'Terapkan strategi 4-1 split push, pecah konsentrasi musuh di map.',
            ];
            $winningConditions[] = 'Terapkan strategi 4-1: Biarkan 4 hero menahan musuh di mid lane/objektif, sementara 1 hero mobile mendobrak turret samping.';
        } else {
            $archetype = 'Balanced Objective Skirmish';
            $winningConditions[] = 'Komposisi seimbang: Mainkan tempo objektif teratur sesuai kemunculan Turtle dan Lord.';
        }

        // Rule Power Spike RBR
        if ($powerSpikeScores['late'] > $powerSpikeScores['early'] && $powerSpikeScores['late'] >= 50) {
            $powerSpikeFocus = 'Late Game Scaling (12+ Menit)';
            $firedRules[] = [
                'rule_id' => 'RBR-06',
                'title' => 'Late Game Scaling Rule',
                'condition' => 'Tim memiliki >= 2 hero yang bergantung pada item late game',
                'action' => 'Prioritaskan farming wave minions aman, hindari kontes berisiko saat tertinggal gold.',
            ];
            $winningConditions[] = 'Draft ini sangat kuat di late game! Early game prioritaskan scaling dan amankan wave minions, hindari perang konyol sebelum menit ke-10.';
        } elseif ($powerSpikeScores['early'] > $powerSpikeScores['late'] && $powerSpikeScores['early'] >= 50) {
            $powerSpikeFocus = 'Early Game Snowball (0 - 8 Menit)';
            $firedRules[] = [
                'rule_id' => 'RBR-07',
                'title' => 'Early Game Snowball Rule',
                'condition' => 'Tim memiliki dominasi early hero kuat (Martis, Dyrroth, Hilda, dll)',
                'action' => 'Invasi buff musuh sejak menit 1, amankan First Blood & Turtle pertama pada 2:00.',
            ];
            $winningConditions[] = 'Wajib bermain agresif sejak menit pertama! Rebut Turtle pertama pada menit 2:00 dan kunci kemenangan sebelum menit ke-15.';
        } else {
            $powerSpikeFocus = 'Mid Game Peak (8 - 14 Menit)';
            $firedRules[] = [
                'rule_id' => 'RBR-08',
                'title' => 'Mid Game Peak Rule',
                'condition' => 'Hero inti mencapai lonjakan kekuatan pada level 4 dan 1-2 core items',
                'action' => 'Maksimalkan tempo transisi di menit 8-12 untuk membongkar outer turret dan mengamankan Lord pertama.',
            ];
            $winningConditions[] = 'Manfaatkan momentum transisi di menit 8-12 untuk menumbangkan outer & inner turret serta amankan Lord pertama.';
        }

        // Rule Pengecekan Kelemahan (Warnings)
        if ($frontlineScore < 30) {
            $warnings[] = [
                'rule_id' => 'RBR-10',
                'level' => 'danger',
                'message' => 'Kekurangan Frontline / Badan: Tim tidak memiliki Tank tebal atau initiator murni. Waspadai inisiasi instan dari bush!',
            ];
        }
        if ($ccScore < 30) {
            $warnings[] = [
                'rule_id' => 'RBR-12',
                'level' => 'warning',
                'message' => 'Kekurangan Hard Crowd Control: Hero lincah lawan (Fanny, Ling, Harith, Joy) akan leluasa bermanuver tanpa hambatan.',
            ];
        }
        if ($magicCount === 0 && count($heroes) >= 4) {
            $warnings[] = [
                'rule_id' => 'RBR-11',
                'level' => 'warning',
                'message' => 'Draft Full Physical: Lawan dapat dengan mudah membeli Antique Cuirass & Blade Armor untuk meredam seluruh damage tim.',
            ];
        }

        // Rule Map Synergy
        if ($map) {
            $mNorm = strtolower($map);
            if (str_contains($mNorm, 'broken')) {
                $firedRules[] = [
                    'rule_id' => 'RBR-13',
                    'title' => 'Map Synergy: Broken Walls',
                    'condition' => 'Pertandingan dimainkan pada Broken Walls',
                    'action' => 'Pecahan dinding sempit melipatgandakan efek tabrakan tembok dan keuntungan hero ber-terrain kabel.',
                ];
                $mapSynergy = 'Broken Walls: Dinding terpecah-pecah sangat menguntungkan hero dengan mekanik tabrakan tembok (Grock, Moskov, Badang) dan mobilitas kabel Fanny.';
            } elseif (str_contains($mNorm, 'grass') || str_contains($mNorm, 'dangerous')) {
                $firedRules[] = [
                    'rule_id' => 'RBR-14',
                    'title' => 'Map Synergy: Dangerous Grass',
                    'condition' => 'Pertandingan dimainkan pada Dangerous Grass',
                    'action' => 'Semak lebat memperkuat strategi bush ambush dan perangkap ganking.',
                ];
                $mapSynergy = 'Dangerous Grass: Lebatnya semak memberi keuntungan maksimal bagi gaya bermain Pick-Off dan ambush semak. Wajib selalu face-check dengan skill.';
            } elseif (str_contains($mNorm, 'river') || str_contains($mNorm, 'expanding')) {
                $firedRules[] = [
                    'rule_id' => 'RBR-15',
                    'title' => 'Map Synergy: Expanding Rivers',
                    'condition' => 'Pertandingan dimainkan pada Expanding Rivers',
                    'action' => 'Sungai luas mempercepat rotasi antar sidelane dan perpindahan pit Turtle/Lord.',
                ];
                $mapSynergy = 'Expanding Rivers: Jalur sungai yang melebar mempercepat rotasi ganking antara Gold Lane dan EXP Lane dalam hitungan detik.';
            } elseif (str_contains($mNorm, 'cloud') || str_contains($mNorm, 'flying')) {
                $firedRules[] = [
                    'rule_id' => 'RBR-16',
                    'title' => 'Map Synergy: Flying Clouds',
                    'condition' => 'Pertandingan dimainkan pada Flying Clouds',
                    'action' => 'Zona angin pendorong memperkuat kiting dan akselerasi retreat.',
                ];
                $mapSynergy = 'Flying Clouds: Arus angin awan memberi akselerasi movement speed yang ideal untuk repositioning saat teamfight dinamis.';
            }
        }

        // 4. Counter Analysis (jika tim lawan dipilih)
        $counterAdvantages = [];
        if ($enemyHeroes->isNotEmpty()) {
            foreach ($heroes as $myH) {
                $rawCounters = $myH->counters;
                if (!empty($rawCounters)) {
                    foreach ($enemyHeroes as $enH) {
                        if (stripos($rawCounters, $enH->hero_name) !== false) {
                            $counterAdvantages[] = [
                                'my_hero' => $myH->hero_name,
                                'counters_hero' => $enH->hero_name,
                                'note' => "{$myH->hero_name} secara alami meng-counter {$enH->hero_name}.",
                            ];
                        }
                    }
                }
            }
        }

        // 5. Rekomendasi Hero Pelengkap (Jika hero < 5)
        $recommendations = [];
        if (count($heroes) < 5) {
            $recommendations = $this->getHeroRecommendations($heroes, $enemyHeroes);
        }

        return [
            'heroes_analyzed'     => $heroes,
            'archetype'           => $archetype,
            'power_spike_focus'   => $powerSpikeFocus,
            'power_spike_scores'  => [
                'early' => min(100, $powerSpikeScores['early']),
                'mid'   => min(100, $powerSpikeScores['mid']),
                'late'  => min(100, $powerSpikeScores['late']),
            ],
            'ratings' => [
                'teamfight'  => $teamfightScore,
                'pick_off'   => $pickOffScore,
                'split_push' => $splitPushScore,
                'poke_siege' => $pokeScore,
                'frontline'  => $frontlineScore,
                'cc'         => $ccScore,
            ],
            'damage_ratio' => [
                'physical_count' => $physicalCount,
                'magic_count'    => $magicCount,
            ],
            'winning_conditions'  => $winningConditions,
            'warnings'            => $warnings,
            'map_synergy'         => $mapSynergy,
            'fired_rules'         => $firedRules,
            'counter_advantages'  => $counterAdvantages,
            'recommendations'     => $recommendations,
        ];
    }

    /**
     * Merekomendasikan hero terbaik untuk melengkapi role yang kosong berdasarkan data hero & turnamen.
     */
    private function getHeroRecommendations($currentHeroes, $enemyHeroes): array
    {
        $selectedNames = $currentHeroes->pluck('hero_name')->toArray();
        $lanesPresent = $currentHeroes->pluck('laning')->toArray();

        // Cari hero yang sering dipick dan memiliki win rate tinggi di turnamen
        $topProHeroes = Dataset::select('hero')
            ->selectRaw('count(*) as picks, sum(case when win_lose = "Win" then 1 else 0 end) as wins')
            ->groupBy('hero')
            ->havingRaw('picks >= 10')
            ->orderByRaw('(wins / picks) DESC')
            ->get();

        $recommended = [];
        foreach ($topProHeroes as $pro) {
            if (in_array($pro->hero, $selectedNames)) continue;
            
            $heroModel = Hero::where('hero_name', $pro->hero)->first();
            if (!$heroModel) continue;

            $wr = round(($pro->wins / $pro->picks) * 100, 1);
            $recommended[] = [
                'hero_name' => $heroModel->hero_name,
                'class'     => $heroModel->class,
                'laning'    => $heroModel->laning,
                'portrait'  => $heroModel->portrait,
                'pro_wr'    => $wr . '%',
                'pro_picks' => $pro->picks . 'x',
                'reason'    => "Meta pick turnamen (WR: {$wr}% dari {$pro->picks} match).",
            ];

            if (count($recommended) >= 4) break;
        }

        return $recommended;
    }
}

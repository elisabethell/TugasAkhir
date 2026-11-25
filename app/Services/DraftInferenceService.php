<?php

namespace App\Services;

use App\Models\RbrRule;
use App\Services\CsvDataService;
use App\Services\TeamDraftDnaService;
use Illuminate\Support\Collection;

class DraftInferenceService
{
    protected CsvDataService $csvService;
    protected TeamDraftDnaService $teamDnaService;

    // Mapping hero dengan damage type dominan Magic
    protected array $knownMagic = [
        'harith', 'yve', 'valentina', 'pharsa', 'xavier', 'alice', 'cecilion',
        'kagura', 'kadita', 'lylia', 'zhuxin', 'zetian', 'lunox', 'esmeralda',
        'gusion', 'aamon', 'karina', 'joy', 'julian', 'guinevere', 'silvanna',
        'phoveus', 'baxia', 'gloo', 'hylos', 'belerick', 'mathilda', 'angela',
        'diggie', 'estes', 'floryn', 'rafaela', 'carmilla', 'kaja', 'faramis',
        'cyclops', 'eudora', 'gord', 'harley', 'vale', 'vexana', 'novaria',
        'aurora', 'chang\'e', 'odette', 'nana', 'luo yi', 'luoyi'
    ];

    // Mapping hero dengan tipe damage campuran (Mixed)
    protected array $knownMixed = [
        'karrie', 'edith', 'natan', 'kimmy', 'lesley', 'bane', 'paquito', 'alpha'
    ];

    public function __construct(CsvDataService $csvService, ?TeamDraftDnaService $teamDnaService = null)
    {
        $this->csvService = $csvService;
        $this->teamDnaService = $teamDnaService ?? app(TeamDraftDnaService::class);
        RbrRule::seedDefaultRules();
    }

    /**
     * Mengevaluasi draft state menggunakan forward chaining RBR terintegrasi Bayesian data-driven.
     *
     * @param array $allyTeam Nama hero sekutu (0-5)
     * @param array $enemyTeam Nama hero musuh (0-5)
     * @param array $bannedHeroes Nama hero yang di-ban
     * @param array $allyLanes Lane hero sekutu (opsional, contoh: ['ROAM', 'MID'])
     * @param array $enemyLanes Lane hero musuh (opsional)
     * @param string|null $map Varian map (opsional)
     * @param string|null $teamName Nama tim untuk evaluasi Draft DNA (opsional)
     * @return array
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
        $allHeroes = $this->csvService->getHeroes();
        $tournamentStats = $this->csvService->getTournamentStatistics();
        $powerSpikesMap = $this->csvService->getEmpiricalPowerSpikes();

        // Ambil profil Draft DNA historis untuk 9 tim MPL ID S18 (default ke 'Alter Ego' jika tidak ada parameter)
        $normalizedTeam = !empty($teamName) ? $this->teamDnaService->normalizeTeamName($teamName) : 'Alter Ego';
        $teamDna = $this->teamDnaService->getTeamDraftDna($normalizedTeam);

        // 1. Inisialisasi Working Memory
        $workingMemory = $this->buildWorkingMemory(
            $allyTeam,
            $enemyTeam,
            $bannedHeroes,
            $allyLanes,
            $enemyLanes,
            $map,
            $allHeroes,
            $powerSpikesMap
        );

        // 2. Evaluasi Semua Rule RBR terhadap Working Memory
        $ruleEvaluations = $this->evaluateRules($workingMemory);

        // 3. Evaluasi dan Pembobotan Hybrid untuk Setiap Kandidat Hero
        $candidates = $this->evaluateCandidates(
            $allHeroes,
            $workingMemory,
            $ruleEvaluations,
            $tournamentStats,
            $powerSpikesMap,
            $teamDna
        );

        // 4. Alert Komposisi Rule-Based untuk Tim Sekutu & Tim Musuh
        $allyAlerts = $this->evaluateCompositionAlerts($workingMemory['ally_heroes'], $workingMemory['ally_lanes_filled'], 'ally');
        $enemyAlerts = $this->evaluateCompositionAlerts($workingMemory['enemy_heroes'], [], 'enemy');

        // 5. Interpretasi dan Komparasi Power Spike
        $allySpikeInterp = $this->interpretPowerSpike($workingMemory['ally_power_spikes'], $workingMemory['ally_count']);
        $enemySpikeInterp = $this->interpretPowerSpike($workingMemory['enemy_power_spikes'], $workingMemory['enemy_count']);
        $spikeComparison = $this->comparePowerSpikes(
            $workingMemory['ally_power_spikes'],
            $workingMemory['ally_count'],
            $workingMemory['enemy_power_spikes'],
            $workingMemory['enemy_count']
        );

        // 6. Evaluasi Identitas Draft & Gaya Bermain / Counter Taktik
        $allyIdentity = $this->evaluateDraftIdentity($workingMemory['ally_heroes'], 'ally');
        $enemyIdentity = $this->evaluateDraftIdentity($workingMemory['enemy_heroes'], 'enemy');

        // 7. RBR Ringan (Draft Strategy Engine): 4 Output Terstruktur
        $draftStrategy = $this->evaluateDraftStrategyEngine($workingMemory['ally_heroes'], $teamDna);

        // 8. Match Mirip (Similar Matches) Turnamen MPL ID Season 18
        $similarMatches = $this->teamDnaService->getSimilarMatches($allyTeam, $normalizedTeam, 3);

        return [
            'working_memory'      => $workingMemory,
            'rule_evaluations'    => $ruleEvaluations,
            'recommendations'     => $candidates,
            'composition_alerts'  => [
                'ally'  => $allyAlerts,
                'enemy' => $enemyAlerts,
            ],
            'power_spikes'        => [
                'ally'       => $allySpikeInterp,
                'enemy'      => $enemySpikeInterp,
                'comparison' => $spikeComparison,
            ],
            'draft_identity'      => [
                'ally'              => array_merge($allyIdentity, $draftStrategy),
                'enemy'             => $enemyIdentity,
                'draft_identity'    => $draftStrategy['draft_identity'],
                'power_spike'       => $draftStrategy['power_spike'],
                'playstyle'         => $draftStrategy['playstyle'],
                'winning_condition' => $draftStrategy['winning_condition'],
            ],
            'draft_strategy'      => $draftStrategy,
            'similar_matches'     => $similarMatches,
            'team_dna'            => $teamDna ? [
                'team_name'             => $teamDna['team_name'],
                'team_logo'             => $teamDna['team_logo'],
                'total_games'           => $teamDna['total_games'],
                'wins'                  => $teamDna['wins'],
                'losses'                => $teamDna['losses'],
                'win_rate'              => $teamDna['win_rate'],
                'win_rate_formatted'    => $teamDna['win_rate_formatted'],
                'top_heroes'            => $teamDna['top_heroes'],
                'top_bans'              => $teamDna['top_bans'],
                'playstyle_tag'         => $teamDna['playstyle_tag'],
                'playstyle_frequency'   => $teamDna['playstyle_frequency'],
                'playstyle_description' => $teamDna['playstyle_description'],
                'composition_breakdown' => $teamDna['composition_breakdown'],
            ] : null,
        ];
    }

    /**
     * 1. Membangun Working Memory (State Draft Lengkap)
     */
    protected function buildWorkingMemory(
        array $allyTeam,
        array $enemyTeam,
        array $bannedHeroes,
        array $allyLanes,
        array $enemyLanes,
        ?string $map,
        Collection $allHeroes,
        array $powerSpikesMap
    ): array {
        $cleanAlly = array_values(array_filter(array_map('trim', $allyTeam)));
        $cleanEnemy = array_values(array_filter(array_map('trim', $enemyTeam)));
        $cleanBans = array_values(array_filter(array_map('trim', $bannedHeroes)));

        $allyHeroes = [];
        $allyLanesFilled = [];
        $allyRolesFilled = [];
        $allyPhysCount = 0;
        $allyMagCount = 0;
        $allyFrontlineCount = 0;
        $allyCcCount = 0;
        $allySpikes = ['early' => 0, 'mid' => 0, 'late' => 0];

        foreach ($cleanAlly as $idx => $name) {
            $h = $this->findHero($allHeroes, $name);
            $assignedLane = !empty($allyLanes[$idx]) ? strtoupper(trim($allyLanes[$idx])) : ($h ? $this->resolveDefaultLane($h) : 'FLEX');
            $dmgType = $h ? $this->resolveDamageType($h) : 'Physical';
            $spike = $h ? $this->resolvePowerSpike($h, $powerSpikesMap) : 'Mid Game';

            if ($dmgType === 'Magic') $allyMagCount++;
            elseif ($dmgType === 'Mixed') { $allyPhysCount += 0.5; $allyMagCount += 0.5; }
            else $allyPhysCount++;

            if ($h) {
                $role = strtolower($h->primary_class ?: $h->class ?: '');
                $allyRolesFilled[] = $role;
                if (str_contains($role, 'tank') || str_contains($role, 'fighter') || $assignedLane === 'ROAM') {
                    $allyFrontlineCount++;
                }
                if (in_array(strtolower($h->hero_name), ['tigreal', 'atlas', 'carmilla', 'akai', 'khufra', 'ruby', 'kaja', 'franco', 'chou'])) {
                    $allyCcCount++;
                }
            }

            if (str_contains($spike, 'Early')) $allySpikes['early']++;
            elseif (str_contains($spike, 'Late')) $allySpikes['late']++;
            else $allySpikes['mid']++;

            $allyLanesFilled[] = $assignedLane;
            $allyHeroes[] = [
                'name'         => $h ? $h->hero_name : $name,
                'assigned_lane'=> $assignedLane,
                'damage_type'  => $dmgType,
                'power_spike'  => $spike,
                'role'         => $h ? ($h->primary_class ?: $h->class) : 'Hero',
                'object'       => $h,
            ];
        }

        $enemyHeroes = [];
        $enemyPhysCount = 0;
        $enemyMagCount = 0;
        $enemySpikes = ['early' => 0, 'mid' => 0, 'late' => 0];

        foreach ($cleanEnemy as $idx => $name) {
            $h = $this->findHero($allHeroes, $name);
            $assignedLane = !empty($enemyLanes[$idx]) ? strtoupper(trim($enemyLanes[$idx])) : ($h ? $this->resolveDefaultLane($h) : 'FLEX');
            $dmgType = $h ? $this->resolveDamageType($h) : 'Physical';
            $spike = $h ? $this->resolvePowerSpike($h, $powerSpikesMap) : 'Mid Game';

            if ($dmgType === 'Magic') $enemyMagCount++;
            elseif ($dmgType === 'Mixed') { $enemyPhysCount += 0.5; $enemyMagCount += 0.5; }
            else $enemyPhysCount++;

            if (str_contains($spike, 'Early')) $enemySpikes['early']++;
            elseif (str_contains($spike, 'Late')) $enemySpikes['late']++;
            else $enemySpikes['mid']++;

            $enemyHeroes[] = [
                'name'         => $h ? $h->hero_name : $name,
                'assigned_lane'=> $assignedLane,
                'damage_type'  => $dmgType,
                'power_spike'  => $spike,
                'role'         => $h ? ($h->primary_class ?: $h->class) : 'Hero',
                'object'       => $h,
            ];
        }

        $allyTotalDmg = max(1, $allyPhysCount + $allyMagCount);
        $enemyTotalDmg = max(1, $enemyPhysCount + $enemyMagCount);

        $excludedNames = array_map('strtolower', array_merge($cleanAlly, $cleanEnemy, $cleanBans));

        return [
            'ally_heroes'          => $allyHeroes,
            'enemy_heroes'         => $enemyHeroes,
            'banned_heroes'        => $cleanBans,
            'ally_count'           => count($cleanAlly),
            'enemy_count'          => count($cleanEnemy),
            'banned_count'         => count($cleanBans),
            'ally_lanes_filled'    => $allyLanesFilled,
            'ally_roles_filled'    => $allyRolesFilled,
            'ally_damage'          => [
                'phys_count' => $allyPhysCount,
                'mag_count'  => $allyMagCount,
                'phys_pct'   => count($cleanAlly) > 0 ? round(($allyPhysCount / $allyTotalDmg) * 100, 1) : 0,
                'mag_pct'    => count($cleanAlly) > 0 ? round(($allyMagCount / $allyTotalDmg) * 100, 1) : 0,
            ],
            'enemy_damage'         => [
                'phys_count' => $enemyPhysCount,
                'mag_count'  => $enemyMagCount,
                'phys_pct'   => count($cleanEnemy) > 0 ? round(($enemyPhysCount / $enemyTotalDmg) * 100, 1) : 0,
                'mag_pct'    => count($cleanEnemy) > 0 ? round(($enemyMagCount / $enemyTotalDmg) * 100, 1) : 0,
            ],
            'ally_power_spikes'    => $allySpikes,
            'enemy_power_spikes'   => $enemySpikes,
            'ally_frontline_count' => $allyFrontlineCount,
            'ally_cc_count'        => $allyCcCount,
            'map_variant'          => $map,
            'excluded_heroes'      => $excludedNames,
        ];
    }

    /**
     * 2. Evaluasi Semua Rule RBR terhadap Working Memory (Forward Chaining)
     */
    protected function evaluateRules(array $wm): array
    {
        $evaluations = [];
        $allyCount = $wm['ally_count'];
        $enemyCount = $wm['enemy_count'];
        $allyMagPct = $wm['ally_damage']['mag_pct'];
        $allyMagCount = $wm['ally_damage']['mag_count'];
        $hasAllyMage = in_array('mid', array_map('strtolower', $wm['ally_lanes_filled'])) || $allyMagCount >= 1;

        // Rule RBR-COMP-01: Aturan Komposisi Mutlak (Mage Wajib)
        // Menyala jika slot ally >= 3 dan kekurangan damage magic (<15%) atau belum ada Mage sama sekali
        $comp01Fired = ($allyCount >= 3 && (!$hasAllyMage || $allyMagPct < config('metascout.thresholds.magic_deficit_threshold', 15.0))) ||
                       ($allyCount >= 2 && $wm['ally_damage']['phys_pct'] >= 100.0 && $allyMagCount == 0);
        $evaluations['RBR-COMP-01'] = [
            'rule_id'             => 'RBR-COMP-01',
            'nama'                => 'Aturan Komposisi Mutlak (Mage Wajib)',
            'kondisi'             => "Ally memiliki {$allyCount} hero, rasio Magic: {$allyMagPct}% (Mage: " . ($hasAllyMage ? 'Ada' : '0') . ")",
            'fired'               => $comp01Fired,
            'bobot'               => 0.98,
            'kontribusi_skor'     => 50.0,
            'alasan_tidak_menyala'=> $comp01Fired ? null : 'Tim sekutu sudah memiliki hero Magic Damage atau Midlaner',
        ];

        // Rule RBR-COMP-02: Aturan Komposisi Frontline (Tank/Frontline Durability)
        $comp02Fired = ($allyCount >= 2 && $wm['ally_frontline_count'] === 0);
        $evaluations['RBR-COMP-02'] = [
            'rule_id'             => 'RBR-COMP-02',
            'nama'                => 'Aturan Komposisi Garis Depan (Frontline Wajib)',
            'kondisi'             => "Ally memiliki {$allyCount} hero dengan {$wm['ally_frontline_count']} hero Frontline",
            'fired'               => $comp02Fired,
            'bobot'               => 0.94,
            'kontribusi_skor'     => 40.0,
            'alasan_tidak_menyala'=> $comp02Fired ? null : 'Tim sekutu sudah memiliki Tank atau Fighter pelindung garis depan',
        ];

        // Rule RBR-CTR-01: Direct Counter Assassin Mobilitas Tinggi
        $mobileEnemies = [];
        if ($enemyCount > 0) {
            foreach ($wm['enemy_heroes'] as $eh) {
                $eName = strtolower($eh['name']);
                if (in_array($eName, ['ling', 'fanny', 'joy', 'wanwan', 'hayabusa', 'nolan', 'lancelot', 'benedetta'])) {
                    $mobileEnemies[] = $eh['name'];
                }
            }
        }
        $ctr01Fired = count($mobileEnemies) > 0;
        $evaluations['RBR-CTR-01'] = [
            'rule_id'             => 'RBR-CTR-01',
            'nama'                => 'Penjinak Mobilitas Lawan (Anti-Dash & Suppression)',
            'kondisi'             => $ctr01Fired ? "Musuh memilih hero mobilitas tinggi: " . implode(', ', $mobileEnemies) : 'Tidak ada assassin mobilitas tinggi terpilih pada tim musuh',
            'fired'               => $ctr01Fired,
            'bobot'               => 0.96,
            'kontribusi_skor'     => 35.0,
            'alasan_tidak_menyala'=> $ctr01Fired ? null : 'Tim musuh belum memilih hero bertipe mobilitas tinggi',
            'target_enemies'      => $mobileEnemies,
        ];

        // Rule RBR-CTR-02: Direct Counter Tank Sustain / HP Regen Tebal
        $sustainEnemies = [];
        if ($enemyCount > 0) {
            foreach ($wm['enemy_heroes'] as $eh) {
                $eName = strtolower($eh['name']);
                if (in_array($eName, ['fredrinn', 'baxia', 'hylos', 'uranus', 'gloo', 'belerick', 'akai', 'tigreal', 'barats'])) {
                    $sustainEnemies[] = $eh['name'];
                }
            }
        }
        $ctr02Fired = count($sustainEnemies) > 0;
        $evaluations['RBR-CTR-02'] = [
            'rule_id'             => 'RBR-CTR-02',
            'nama'                => 'Penembus Pertahanan Tebal (True Damage / HP Shredder)',
            'kondisi'             => $ctr02Fired ? "Musuh memilih tank sustain tinggi: " . implode(', ', $sustainEnemies) : 'Tidak ada tank sustain tebal pada tim musuh',
            'fired'               => $ctr02Fired,
            'bobot'               => 0.91,
            'kontribusi_skor'     => 35.0,
            'alasan_tidak_menyala'=> $ctr02Fired ? null : 'Tim musuh belum memilih hero berkategori tank sustain HP tebal',
            'target_enemies'      => $sustainEnemies,
        ];

        // Rule RBR-SYN-01: Sinergi Proteksi Carry (UBE / Protect The Hypercarry)
        $hasEnabler = false;
        $hasHardCarry = false;
        if ($allyCount > 0) {
            foreach ($wm['ally_heroes'] as $ah) {
                $aName = strtolower($ah['name']);
                if (in_array($aName, ['floryn', 'angela', 'rafaela', 'mathilda', 'estes', 'diggie'])) $hasEnabler = true;
                if (in_array($aName, ['bruno', 'claude', 'moskov', 'karrie', 'granger', 'beatrix', 'harith', 'roger'])) $hasHardCarry = true;
            }
        }
        $syn01Fired = ($hasEnabler || $hasHardCarry) && $allyCount >= 1;
        $evaluations['RBR-SYN-01'] = [
            'rule_id'             => 'RBR-SYN-01',
            'nama'                => 'Sinergi Proteksi Carry (Formasi UBE / Hypercarry)',
            'kondisi'             => $syn01Fired ? "Sekutu memiliki hero enabler ({$hasEnabler}) atau carry scaling ({$hasHardCarry})" : 'Belum ada kombinasi Support Enabler atau Carry scaling pada tim sekutu',
            'fired'               => $syn01Fired,
            'bobot'               => 0.95,
            'kontribusi_skor'     => 30.0,
            'alasan_tidak_menyala'=> $syn01Fired ? null : 'Aturan sinergi carry memerlukan minimal 1 hero sekutu terkait',
        ];

        // Rule RBR-SYN-02: Sinergi Wiping Area Combo (CC Area + AoE Burst)
        $hasAoeCc = false;
        if ($allyCount > 0) {
            foreach ($wm['ally_heroes'] as $ah) {
                $aName = strtolower($ah['name']);
                if (in_array($aName, ['tigreal', 'atlas', 'carmilla', 'akai', 'terizla', 'gatotkaca', 'grock'])) $hasAoeCc = true;
            }
        }
        $syn02Fired = $hasAoeCc && $allyCount >= 1;
        $evaluations['RBR-SYN-02'] = [
            'rule_id'             => 'RBR-SYN-02',
            'nama'                => 'Sinergi Rantai Crowd Control & Burst Area Wiping',
            'kondisi'             => $syn02Fired ? "Sekutu memiliki inisiator Crowd Control area" : 'Belum ada inisiator Crowd Control area pada tim sekutu',
            'fired'               => $syn02Fired,
            'bobot'               => 0.92,
            'kontribusi_skor'     => 30.0,
            'alasan_tidak_menyala'=> $syn02Fired ? null : 'Aturan combo area memerlukan inisiator CC massal di tim sekutu',
        ];

        // Rule RBR-POW-01: Kurva Kekuatan Awal (Early Game Snowball Tempo)
        $pow01Fired = $wm['ally_power_spikes']['early'] >= 1 && $allyCount >= 1;
        $evaluations['RBR-POW-01'] = [
            'rule_id'             => 'RBR-POW-01',
            'nama'                => 'Kurva Kekuatan Awal (Early Game 0–5m)',
            'kondisi'             => "Ally memiliki {$wm['ally_power_spikes']['early']} hero ber-power spike Early Game (0–5m)",
            'fired'               => $pow01Fired,
            'bobot'               => 0.89,
            'kontribusi_skor'     => 25.0,
            'alasan_tidak_menyala'=> $pow01Fired ? null : 'Tim belum difokuskan pada strategi laning & invasi early game (0–5m)',
        ];

        // Rule RBR-POW-02: Kurva Kekuatan Akhir (Late Game Macro Scaling)
        $pow02Fired = $wm['ally_power_spikes']['late'] >= 1 && $allyCount >= 1;
        $evaluations['RBR-POW-02'] = [
            'rule_id'             => 'RBR-POW-02',
            'nama'                => 'Kurva Kekuatan Akhir (Late Game 12+m / Trinity MM)',
            'kondisi'             => "Ally memiliki {$wm['ally_power_spikes']['late']} hero ber-power spike Late Game (12+m / Trinity MM)",
            'fired'               => $pow02Fired,
            'bobot'               => 0.90,
            'kontribusi_skor'     => 25.0,
            'alasan_tidak_menyala'=> $pow02Fired ? null : 'Tim belum difokuskan pada scaling carry 12+m (fase item Trinity MM & Evolved Lord)',
        ];

        // Rule RBR-MAP-01: Sinergi Varian Map (Vision & River Bush Control)
        $mapStr = strtolower($wm['map_variant'] ?? '');
        $map01Fired = str_contains($mapStr, 'wisps') || str_contains($mapStr, 'grass');
        $evaluations['RBR-MAP-01'] = [
            'rule_id'             => 'RBR-MAP-01',
            'nama'                => 'Sinergi Varian Map: Vision Control & River Ambush',
            'kondisi'             => "Map pertandingan aktif: " . ($wm['map_variant'] ?: 'Standar'),
            'fired'               => $map01Fired,
            'bobot'               => 0.88,
            'kontribusi_skor'     => 20.0,
            'alasan_tidak_menyala'=> $map01Fired ? null : 'Pertandingan tidak dimainkan pada arena Revealing Wisps / Dangerous Grass',
        ];

        // Rule RBR-GEN-01: Aturan Dasar Meta Turnamen MPL S18 (Aktif saat draft awal/kosong)
        $gen01Fired = ($allyCount === 0 && $enemyCount === 0);
        $evaluations['RBR-GEN-01'] = [
            'rule_id'             => 'RBR-GEN-01',
            'nama'                => 'Prioritas Awal Meta Turnamen MPL ID S18',
            'kondisi'             => 'Draft dalam kondisi awal (0 hero terpilih)',
            'fired'               => $gen01Fired,
            'bobot'               => 0.85,
            'kontribusi_skor'     => 45.0,
            'alasan_tidak_menyala'=> $gen01Fired ? null : 'Draft sudah memiliki hero sekutu atau musuh terpilih',
        ];

        return $evaluations;
    }

    /**
     * 3. Evaluasi Skor Hybrid per Hero Kandidat (ARM & RBR)
     * Mendukung pembobotan Draft DNA spesifik tim dengan mekanisme fallback
     */
    protected function evaluateCandidates(
        Collection $allHeroes,
        array $wm,
        array $ruleEvals,
        array $tournamentStats,
        array $powerSpikesMap,
        ?array $teamDna = null
    ): array {
        $k = (int) config('metascout.bayesian.k', 10);
        $prior = (float) config('metascout.bayesian.prior', 0.50);
        $wData = (float) config('metascout.weights.data', 0.50);
        $wRule = (float) config('metascout.weights.rule', 0.50);
        $lowDataThreshold = (int) config('metascout.thresholds.low_data_n', 10);

        $statsMap = [];
        foreach ($tournamentStats as $s) {
            if (!empty($s['clean_key'])) $statsMap[$s['clean_key']] = $s;
            if (!empty($s['name'])) $statsMap[strtolower($s['name'])] = $s;
        }

        $hasTeamDna = ($teamDna !== null && !empty($teamDna['team_name']));
        $teamHeroPoolKeys = $hasTeamDna ? ($teamDna['hero_pool_keys'] ?? []) : [];
        $teamPlaystyle = $hasTeamDna ? ($teamDna['playstyle_tag'] ?? null) : null;

        $results = [];

        foreach ($allHeroes as $h) {
            $name = $h->hero_name;
            $nameLower = strtolower($name);

            // 6. Eksklusi: Hero yang sudah terpilih di Sekutu/Musuh atau di-Ban
            if (in_array($nameLower, $wm['excluded_heroes'])) {
                continue;
            }

            $stat = $statsMap[$h->clean_key] ?? ($statsMap[$nameLower] ?? null);
            $n = $stat ? (int) $stat['picks'] : 0;
            $wins = $stat ? (int) $stat['wins'] : 0;
            $rawWr = ($stat && $n > 0) ? round((float) $stat['wr'], 1) : 50.0;

            // Bayesian Smoothing (M-Estimate / Shrinkage) Overall MPL ID S18
            // formula: smoothed_wr = ((wins + k * prior) / (n + k)) * 100
            $smoothedWrOverall = round((($wins + ($k * $prior)) / ($n + $k)) * 100.0, 2);

            // 1. Integrasi Draft DNA Tim: Prioritaskan Data Historis Tim
            $teamHeroStat = $hasTeamDna ? ($teamDna['hero_stats'][$nameLower] ?? null) : null;
            $teamPicks = $teamHeroStat ? (int) $teamHeroStat['picks'] : 0;
            $teamWins = $teamHeroStat ? (int) $teamHeroStat['wins'] : 0;
            $isHeroInPool = in_array($nameLower, $teamHeroPoolKeys);

            if ($hasTeamDna && $teamPicks > 0) {
                // Gunakan prior win rate tim dengan faktor peredam k_team = 4
                $teamPrior = ($teamDna['win_rate'] > 0) ? ($teamDna['win_rate'] / 100.0) : $prior;
                $teamK = 4;
                $teamSmoothedWr = round((($teamWins + ($teamK * $teamPrior)) / ($teamPicks + $teamK)) * 100.0, 2);
                $effectiveSmoothedWr = $teamSmoothedWr;
                $effectiveRawWr = round(($teamWins / $teamPicks) * 100.0, 1);
                $effectiveN = $teamPicks;
                $wrSource = 'team_historical';
            } else {
                // Fallback ke statistik keseluruhan turnamen MPL ID S18
                $effectiveSmoothedWr = $smoothedWrOverall;
                $effectiveRawWr = $rawWr;
                $effectiveN = $n;
                $wrSource = 'overall_mpl_s18';
            }

            // 2. Evaluasi Sinergi (Prioritas Sinergi Tim -> Fallback Association Rule Mining CSV)
            $synergyList = [];
            $synergyScore = 0.0;
            if ($wm['ally_count'] > 0) {
                $candidateSyns = !empty($h->synergies) ? array_map(function($item) {
                    return strtolower($item['heroname'] ?? '');
                }, $h->synergies) : [];

                foreach ($wm['ally_heroes'] as $ah) {
                    $aLower = strtolower($ah['name']);
                    $synKey1 = $nameLower . '|' . $aLower;
                    $synKey2 = $aLower . '|' . $nameLower;
                    $teamSyn = $hasTeamDna ? ($teamDna['synergies'][$synKey1] ?? ($teamDna['synergies'][$synKey2] ?? null)) : null;

                    if ($teamSyn && $teamSyn['picks'] > 0) {
                        // Data sinergi tim empiris
                        $val = round(8.0 + min(12.0, max(0.0, ($teamSyn['win_rate'] - 45.0) * 0.4)) + min(4.0, $teamSyn['picks'] * 0.8), 1);
                        $synergyScore += $val;
                        $synergyList[] = [
                            'dengan'   => $ah['name'],
                            'nilai'    => $val,
                            'source'   => 'team_dna',
                            'picks'    => $teamSyn['picks'],
                            'win_rate' => $teamSyn['win_rate'] . '%',
                        ];
                    } elseif (in_array($aLower, $candidateSyns)) {
                        // Fallback ke Sinergi ARM / CSV
                        $val = 12.0;
                        $synergyScore += $val;
                        $synergyList[] = [
                            'dengan' => $ah['name'],
                            'nilai'  => $val,
                            'source' => $hasTeamDna ? 'overall_meta_fallback' : 'overall_meta',
                        ];
                    }
                }
            }

            // 3. Evaluasi Counter (Prioritas Head-to-Head Tim -> Fallback Overall MPL ID S18)
            // Sesuai aturan: jika musuh belum pernah dihadapi tim (n = 0), gunakan fallback statistik
            // overall MPL, namun tetap prioritaskan hero yang masuk dalam top_heroes (Hero Pool) tim.
            $counterList = [];
            $counterScore = 0.0;
            if ($wm['enemy_count'] > 0) {
                $candidateCtrs = !empty($h->counters) ? array_map(function($item) {
                    return strtolower($item['heroname'] ?? '');
                }, $h->counters) : [];

                foreach ($wm['enemy_heroes'] as $eh) {
                    $eLower = strtolower($eh['name']);
                    $h2hKey = $nameLower . '|vs|' . $eLower;
                    $teamH2h = $hasTeamDna ? ($teamDna['counters'][$h2hKey] ?? null) : null;
                    $isMetaCounter = in_array($eLower, $candidateCtrs);

                    if ($teamH2h && $teamH2h['matches'] > 0) {
                        // Tim pernah menghadapi musuh ini di turnamen
                        $h2hN = $teamH2h['matches'];
                        $h2hWr = $teamH2h['win_rate'];
                        $val = round(8.0 + min(12.0, max(0.0, ($h2hWr - 40.0) * 0.35)) + min(4.0, $h2hN * 1.0), 1);
                        $counterScore += $val;
                        $counterList[] = [
                            'vs'       => $eh['name'],
                            'nilai'    => $val,
                            'source'   => 'team_h2h',
                            'matches'  => $h2hN,
                            'win_rate' => $h2hWr . '%',
                        ];
                    } else {
                        // Mekanisme Fallback: Musuh belum pernah dihadapi oleh tim (n = 0)
                        if ($isMetaCounter) {
                            $val = 14.0;
                            // Prioritaskan hero yang masuk dalam top_heroes (Hero Pool) tim
                            if ($isHeroInPool) {
                                $heroPoolBonus = 6.0;
                                $val += $heroPoolBonus;
                                $counterList[] = [
                                    'vs'     => $eh['name'],
                                    'nilai'  => $val,
                                    'source' => 'overall_meta_fallback_hero_pool',
                                    'note'   => "Fallback MPL + Hero Pool Priority ({$teamDna['team_name']})",
                                ];
                            } else {
                                $counterList[] = [
                                    'vs'     => $eh['name'],
                                    'nilai'  => $val,
                                    'source' => $hasTeamDna ? 'overall_meta_fallback' : 'overall_meta',
                                ];
                            }
                            $counterScore += $val;
                        } elseif ($hasTeamDna && $isHeroInPool) {
                            // Hero signature tim mendapat comfort priority saat menghadapi matchup belum terpetakan
                            $comfortVal = 8.0;
                            $counterScore += $comfortVal;
                            $counterList[] = [
                                'vs'     => $eh['name'],
                                'nilai'  => $comfortVal,
                                'source' => 'hero_pool_comfort_fallback',
                                'note'   => "Hero Pool Signature ({$teamDna['team_name']}) vs Unfaced Matchup",
                            ];
                        }
                    }
                }
            }

            // 4. Playstyle Alignment Bonus jika Team DNA aktif
            $playstyleBonus = 0.0;
            $primaryRole = strtolower($h->primary_class ?: $h->class ?: '');
            $dmgType = $this->resolveDamageType($h);
            $spike = $this->resolvePowerSpike($h, $powerSpikesMap);

            if ($teamPlaystyle) {
                if ($teamPlaystyle === 'Double Fighter' && str_contains($primaryRole, 'fighter')) {
                    $playstyleBonus = 4.0;
                } elseif ($teamPlaystyle === 'Tank Jungler' && (str_contains($primaryRole, 'tank') || in_array($nameLower, ['fredrinn', 'baxia', 'akai', 'barats', 'hylos', 'gloo', 'belerick']))) {
                    $playstyleBonus = 4.0;
                } elseif ($teamPlaystyle === 'Assassin Snowball' && (str_contains($primaryRole, 'assassin') || in_array($nameLower, ['ling', 'fanny', 'hayabusa', 'nolan', 'joy', 'lancelot', 'suyou']))) {
                    $playstyleBonus = 4.0;
                } elseif ($teamPlaystyle === 'Late Game Scaling' && (str_contains($primaryRole, 'marksman') || str_contains($spike, 'Late'))) {
                    $playstyleBonus = 4.0;
                } elseif ($teamPlaystyle === 'Support Utility (UBE)' && str_contains($primaryRole, 'support')) {
                    $playstyleBonus = 4.0;
                } elseif ($teamPlaystyle === 'Early Game Aggro' && str_contains($spike, 'Early')) {
                    $playstyleBonus = 4.0;
                }
            }

            // Data Score (0 - 100)
            $dataScore = min(100.0, max(10.0, $effectiveSmoothedWr + min(25.0, $synergyScore) + min(30.0, $counterScore) + $playstyleBonus));

            // Evaluasi Kesesuaian Hero terhadap Fired Rules (9 Aturan RBR tetap berjalan normal)
            $firedRulesForHero = [];
            $notFiredRulesForHero = [];
            $ruleScoreRaw = 0.0;

            foreach ($ruleEvals as $code => $r) {
                if ($r['fired']) {
                    $applies = false;
                    $multiplier = 1.0;

                    if ($code === 'RBR-COMP-01' && ($dmgType === 'Magic' || str_contains($primaryRole, 'mage'))) {
                        $applies = true;
                        $multiplier = 1.5; // Pengali prioritas mutlak Mage
                    } elseif ($code === 'RBR-COMP-02' && (str_contains($primaryRole, 'tank') || str_contains($primaryRole, 'fighter'))) {
                        $applies = true;
                    } elseif ($code === 'RBR-CTR-01' && in_array($nameLower, ['khufra', 'phoveus', 'kaja', 'franco', 'ruby', 'minsitthar', 'saber', 'akai'])) {
                        $applies = true;
                    } elseif ($code === 'RBR-CTR-02' && in_array($nameLower, ['karrie', 'lunox', 'claude', 'dyrroth', 'lesley', 'alpha'])) {
                        $applies = true;
                    } elseif ($code === 'RBR-SYN-01' && (str_contains($primaryRole, 'marksman') || str_contains($primaryRole, 'support'))) {
                        $applies = true;
                    } elseif ($code === 'RBR-SYN-02' && (str_contains($primaryRole, 'mage') || in_array($nameLower, ['yve', 'pharsa', 'odette', 'vexana', 'kadita', 'luo yi']))) {
                        $applies = true;
                    } elseif ($code === 'RBR-POW-01' && str_contains($spike, 'Early')) {
                        $applies = true;
                    } elseif ($code === 'RBR-POW-02' && str_contains($spike, 'Late')) {
                        $applies = true;
                    } elseif ($code === 'RBR-MAP-01' && in_array($nameLower, ['hilda', 'natalia', 'selena', 'mathilda', 'chou'])) {
                        $applies = true;
                    } elseif ($code === 'RBR-GEN-01') {
                        $applies = true;
                        $multiplier = min(1.0, max(0.4, $effectiveRawWr / 60.0));
                    }

                    if ($applies) {
                        $contrib = $r['kontribusi_skor'] * $multiplier;
                        $ruleScoreRaw += $contrib;
                        $firedRulesForHero[] = array_merge($r, ['kontribusi_skor' => $contrib]);
                    } else {
                        $notFiredRulesForHero[] = array_merge($r, [
                            'alasan_tidak_menyala' => "Karakteristik hero ({$h->hero_name}) tidak memenuhi kriteria aksi aturan {$code}"
                        ]);
                    }
                } else {
                    $notFiredRulesForHero[] = $r;
                }
            }

            // Rule Score dinormalisasi (0 - 100)
            $ruleScore = min(100.0, max(15.0, ($ruleScoreRaw / 85.0) * 100.0));

            // Total Score Hybrid (0 - 100)
            $totalScore = round(($wData * $dataScore) + ($wRule * $ruleScore), 1);

            // 4. Perhitungan Confidence Dinamis
            // reliability = n / (n + k)
            $reliability = $effectiveN / ($effectiveN + ($hasTeamDna ? 4 : $k));
            $lowData = ($effectiveN < ($hasTeamDna ? 3 : $lowDataThreshold));

            $ruleWeightSum = 0.0;
            $ruleWeightTotal = 0.0;
            foreach ($firedRulesForHero as $fr) {
                $ruleWeightSum += ($fr['bobot'] * $fr['kontribusi_skor']);
                $ruleWeightTotal += $fr['kontribusi_skor'];
            }
            $ruleStrength = $ruleWeightTotal > 0 ? ($ruleWeightSum / $ruleWeightTotal) : 0.85;

            // Variasi matematis confidence berdasarkan kedalaman data dan kepastian rule
            $calcConfidence = round(((0.45 * $ruleStrength) + (0.55 * (0.55 + (0.45 * $reliability)))) * 100.0, 1);
            $calcConfidence = min(98.8, max(68.5, $calcConfidence));

            // Flex Lane array
            $laneFlex = !empty($h->lanes_array) ? $h->lanes_array : (!empty($h->laning) ? array_map('trim', explode('•', $h->laning)) : ['Flex']);

            // 7. Pembuatan Alasan Ringkas (1 kalimat dinamis berbasis rule & data)
            $alasanRingkas = $this->buildAlasanRingkas(
                $h,
                $wm,
                $firedRulesForHero,
                $synergyList,
                $counterList,
                $dmgType,
                $spike,
                $teamDna,
                $isHeroInPool
            );

            $results[] = [
                'hero'          => $name,
                'nama'          => $name,
                'hero_id'       => (string) ($h->hero_id ?? $h->id ?? $h->clean_key),
                'role'          => $h->primary_class ?: $h->class ?: 'Hero',
                'lane_flex'     => array_values($laneFlex),
                'lane'          => strtolower($laneFlex[0] ?? 'flex'),
                'total_score'   => $totalScore,
                'score'         => number_format($totalScore / 20.0, 1),
                'confidence'    => $calcConfidence . '%',
                'sample_size'   => $effectiveN,
                'low_data'      => $lowData,
                'portrait'      => $h->portrait,
                'damage_type'   => $dmgType,
                'power_spike'   => $spike,
                'team_dna'      => $hasTeamDna ? [
                    'team_name'        => $teamDna['team_name'],
                    'in_hero_pool'     => $isHeroInPool,
                    'team_picks'       => $teamPicks,
                    'team_wins'        => $teamWins,
                    'team_wr'          => $teamPicks > 0 ? $teamHeroStat['win_rate'] : null,
                    'team_smoothed_wr' => $teamPicks > 0 ? $teamSmoothedWr : null,
                    'playstyle_fit'    => $playstyleBonus > 0,
                    'wr_source'        => $wrSource,
                ] : null,
                'breakdown'     => [
                    'data' => [
                        'score'    => round($dataScore, 1),
                        'win_rate' => $effectiveRawWr,
                        'n'        => $effectiveN,
                        'synergy'  => $synergyList,
                        'counter'  => $counterList,
                    ],
                    'rule' => [
                        'score'     => round($ruleScore, 1),
                        'fired'     => $firedRulesForHero,
                        'not_fired' => $notFiredRulesForHero,
                    ],
                ],
                'alasan_ringkas' => $alasanRingkas,
            ];
        }

        // Urutkan kandidat berdasarkan total_score tertinggi
        usort($results, fn($a, $b) => $b['total_score'] <=> $a['total_score']);

        return $results;
    }

    /**
     * Membangun 1 kalimat alasan ringkas yang jujur dan dinamis
     */
    protected function buildAlasanRingkas(
        $hero,
        array $wm,
        array $firedRules,
        array $synergies,
        array $counters,
        string $dmgType,
        string $spike,
        ?array $teamDna = null,
        bool $isHeroInPool = false
    ): string {
        // Cek apakah rule komposisi mutlak menyala untuk hero ini (Prioritas Utama RBR)
        foreach ($firedRules as $r) {
            if ($r['rule_id'] === 'RBR-COMP-01' && $dmgType === 'Magic') {
                return "Aturan Komposisi Mutlak: Wajib mengamankan magic damage/midlaner agar musuh tidak menumpuk Armor Fisik.";
            }
            if ($r['rule_id'] === 'RBR-COMP-02') {
                return "Aturan Komposisi Frontline: Menyediakan ketahanan garis depan penting untuk kontes objektif Turtle dan Lord.";
            }
        }

        // Kasus: Draft Kosong (0 hero)
        if ($wm['ally_count'] === 0 && $wm['enemy_count'] === 0) {
            if ($teamDna && $isHeroInPool) {
                return "Hero signature {$teamDna['team_name']} dengan kecocokan tinggi terhadap playstyle {$teamDna['playstyle_tag']}.";
            }
            return "Prioritas awal (meta S18) berdasarkan kestabilan win rate dan fleksibilitas fase turnamen.";
        }

        // Cek kombinasi Counter dan Synergy
        if (!empty($counters) && !empty($synergies)) {
            $eName = $counters[0]['vs'];
            $aName = $synergies[0]['dengan'];
            return "Direct counter efektif melumpuhkan {$eName} sekaligus memiliki sinergi kombo taktis dengan {$aName}.";
        }

        // Cek Counter musuh
        if (!empty($counters)) {
            $eName = $counters[0]['vs'];
            if (!empty($counters[0]['note'])) {
                return "{$counters[0]['note']} efektif meredam pergerakan {$eName}.";
            }
            return "Direct counter efektif terhadap {$eName} berdasarkan keunggulan mekanik skill pelumpuh lawan.";
        }

        // Cek Sinergi sekutu
        if (!empty($synergies)) {
            $aName = $synergies[0]['dengan'];
            if (!empty($synergies[0]['source']) && $synergies[0]['source'] === 'team_dna') {
                return "Duet andalan {$teamDna['team_name']} bersama {$aName} ({$synergies[0]['picks']} game, {$synergies[0]['win_rate']} WR).";
            }
            return "Membentuk sinergi taktis yang kuat bersama {$aName} untuk penguasaan teamfight objektif.";
        }

        // Cek Rule Mobilitas / True Damage
        foreach ($firedRules as $r) {
            if ($r['rule_id'] === 'RBR-CTR-01') {
                return "Penjinak mobilitas lawan dengan crowd control pengunci pergerakan lincah hero musuh.";
            }
            if ($r['rule_id'] === 'RBR-CTR-02') {
                return "Penembus armor tebal lawan dengan true damage persentase HP shredder.";
            }
        }

        if ($teamDna && $isHeroInPool) {
            return "Hero signature andalan {$teamDna['team_name']} untuk memperkuat formasi {$teamDna['playstyle_tag']}.";
        }

        return "Pilihan meta turnamen solid dengan keunggulan adaptasi power spike {$spike}.";
    }

    /**
     * Helper mencari objek hero dari koleksi
     */
    protected function findHero(Collection $allHeroes, string $name)
    {
        return $allHeroes->first(function($item) use ($name) {
            return strcasecmp($item->hero_name, $name) === 0 || strcasecmp($item->clean_key, $name) === 0;
        });
    }

    /**
     * Helper menentukan tipe damage hero
     */
    protected function resolveDamageType($hero): string
    {
        $nameLower = strtolower($hero->hero_name ?? '');
        $classLower = strtolower($hero->class ?? $hero->primary_class ?? '');

        if (in_array($nameLower, $this->knownMagic) || str_contains($classLower, 'mage') || str_contains($classLower, 'support')) {
            return 'Magic';
        }
        if (in_array($nameLower, $this->knownMixed)) {
            return 'Mixed';
        }
        return 'Physical';
    }

    /**
     * Helper menentukan kurva power spike empiris
     */
    protected function resolvePowerSpike($hero, array $powerSpikesMap): string
    {
        $name = $hero->hero_name ?? '';
        if (isset($powerSpikesMap[$name]['empirical_spike'])) {
            return $powerSpikesMap[$name]['empirical_spike'];
        }

        $classLower = strtolower($hero->class ?? $hero->primary_class ?? '');
        if (str_contains($classLower, 'marksman')) return 'Late Game Scaler';
        if (str_contains($classLower, 'tank')) return 'Early Game';
        return 'Mid Game';
    }

    /**
     * Helper menentukan default lane
     */
    protected function resolveDefaultLane($hero): string
    {
        $laning = $hero->laning ?? '';
        if (!empty($laning)) {
            $parts = explode('•', $laning);
            return strtoupper(trim($parts[0]));
        }
        $classLower = strtolower($hero->class ?? $hero->primary_class ?? '');
        if (str_contains($classLower, 'tank') || str_contains($classLower, 'support')) return 'ROAM';
        if (str_contains($classLower, 'mage')) return 'MID';
        if (str_contains($classLower, 'marksman')) return 'GOLD';
        if (str_contains($classLower, 'assassin')) return 'JGL';
        return 'EXP';
    }

    /**
     * Evaluasi alert komposisi rule-based untuk suatu tim (sekutu atau musuh)
     */
    public function evaluateCompositionAlerts(array $teamHeroes, array $teamLanes = [], string $teamType = 'ally'): array
    {
        $count = count($teamHeroes);
        if ($count === 0) {
            return [];
        }

        $alerts = [];
        $physCount = 0;
        $magCount = 0;
        $frontlineCount = 0;
        $ccCount = 0;
        $hasMage = false;

        $hardCcInitiators = [
            'tigreal', 'atlas', 'carmilla', 'akai', 'khufra', 'ruby', 'terizla', 'kaja',
            'franco', 'chou', 'gatotkaca', 'belerick', 'gloo', 'lolita', 'minotaur',
            'badang', 'guinevere', 'jawhead', 'edith', 'hylos', 'mathilda', 'silvanna',
            'valir', 'aurora', 'nana', 'vexana'
        ];

        foreach ($teamHeroes as $idx => $hero) {
            $name = strtolower($hero['name'] ?? '');
            $dmg = $hero['damage_type'] ?? 'Physical';
            $role = strtolower($hero['role'] ?? '');
            $lane = !empty($teamLanes[$idx]) ? strtoupper($teamLanes[$idx]) : strtoupper($hero['assigned_lane'] ?? '');

            if ($dmg === 'Magic') $magCount++;
            elseif ($dmg === 'Mixed') { $physCount += 0.5; $magCount += 0.5; }
            else $physCount++;

            if (str_contains($role, 'mage') || $lane === 'MID' || in_array($name, $this->knownMagic)) {
                $hasMage = true;
            }

            if (str_contains($role, 'tank') || str_contains($role, 'fighter') || $lane === 'ROAM') {
                $frontlineCount++;
            }

            if (in_array($name, $hardCcInitiators)) {
                $ccCount++;
            }
        }

        $totalDmg = max(1, $physCount + $magCount);
        $physPct = round(($physCount / $totalDmg) * 100, 1);
        $magPct = round(($magCount / $totalDmg) * 100, 1);

        $targetLabel = ($teamType === 'ally') ? 'Tim musuh' : 'Tim sekutu';

        // 1. RBR-COMP-01: Wajib Mage / Magic Damage
        if ($count >= 3 && (!$hasMage || $magPct < config('metascout.thresholds.magic_deficit_threshold', 15.0))) {
            $alerts[] = [
                'rule_id'     => 'RBR-COMP-01',
                'severity'    => 'critical',
                'title'       => 'Defisit Magic Damage (Belum ada Mage)',
                'message'     => "Belum ada Mage / Magic Damage ({$magPct}%): {$targetLabel} dapat menumpuk Physical Defense secara bebas.",
                'action'      => 'Prioritaskan hero Mage dengan zoning area',
            ];
        } elseif ($count >= 2 && $physPct >= 100.0 && $magCount == 0) {
            $alerts[] = [
                'rule_id'     => 'RBR-COMP-01',
                'severity'    => 'warning',
                'title'       => 'Peringatan Komposisi Magic',
                'message'     => "Belum ada Magical Damage ({$magPct}%): Rawan dicounter stack armor fisik.",
                'action'      => 'Siapkan opsi pick magical damage',
            ];
        }

        // 2. RBR-COMP-02: Wajib Frontliner / Tank
        if ($count >= 3 && $frontlineCount === 0) {
            $alerts[] = [
                'rule_id'     => 'RBR-COMP-02',
                'severity'    => 'critical',
                'title'       => 'Tidak Ada Frontliner / Tank',
                'message'     => "Tidak ada frontliner / Tank: Garis depan rentan ditembus dan kontes Lord berisiko tinggi.",
                'action'      => 'Wajib ambil Tank atau Fighter berdurabilitas tebal',
            ];
        }

        // 3. RBR-CC-01: Inisiator Crowd Control (CC)
        if ($count >= 3 && $ccCount === 0) {
            $alerts[] = [
                'rule_id'     => 'RBR-CC-01',
                'severity'    => 'warning',
                'title'       => 'Minim Crowd Control (CC)',
                'message'     => "Tidak ada inisiator Crowd Control (CC): Minim kemampuan penguncian target lincah dan disengage teamfight.",
                'action'      => 'Tambahkan hero inisiator dengan stun/suppress',
            ];
        }

        // 4. RBR-DMG-01: Damage 100% Physical
        if ($count >= 2 && $physPct >= 100.0) {
            $alerts[] = [
                'rule_id'     => 'RBR-DMG-01',
                'severity'    => 'warning',
                'title'       => 'Damage 100% Physical',
                'message'     => "Damage 100% Physical: Mudah dilawan stack armor Antique Cuirass & Blade Armor.",
                'action'      => 'Tambahkan sumber damage Magic atau True Damage',
            ];
        }

        // 5. RBR-DMG-02: Damage 100% Magic
        if ($count >= 2 && $magPct >= 100.0) {
            $alerts[] = [
                'rule_id'     => 'RBR-DMG-02',
                'severity'    => 'warning',
                'title'       => 'Damage 100% Magic',
                'message'     => "Damage 100% Magic: Mudah dilawan stack magic defense Radiant Armor & Athena's Shield.",
                'action'      => 'Tambahkan hero physical carry untuk push turret',
            ];
        }

        // 6. RBR-COMP-OK: Komposisi Seimbang
        if ($count >= 3 && empty(array_filter($alerts, fn($a) => $a['severity'] === 'critical'))) {
            if ($physPct > 0 && $magPct > 0 && $frontlineCount > 0) {
                $alerts[] = [
                    'rule_id'     => 'RBR-COMP-OK',
                    'severity'    => 'info',
                    'title'       => 'Komposisi Seimbang',
                    'message'     => "Komposisi seimbang ({$physPct}% Phys / {$magPct}% Mag) dengan frontliner & CC aktif.",
                    'action'      => 'Pertahankan koordinasi teamfight dan kuasai objektif',
                ];
            }
        }

        return $alerts;
    }

    /**
     * Interpretasi kurva power spike untuk suatu tim
     */
    public function interpretPowerSpike(array $spikes, int $totalCount): array
    {
        if ($totalCount === 0) {
            return [
                'text'     => 'Belum ada data',
                'dominant' => null,
            ];
        }

        $early = $spikes['early'] ?? 0;
        $mid = $spikes['mid'] ?? 0;
        $late = $spikes['late'] ?? 0;

        if ($mid > 0 && $late == 0 && $early <= $mid) {
            $text = "Unggul di Mid, lemah di Late.";
            $dom = 'Mid';
        } elseif ($early > 0 && $late == 0 && $mid == 0) {
            $text = "Dominan di Early, lemah di Late.";
            $dom = 'Early';
        } elseif ($early > $mid && $early > $late) {
            $text = "Dominan di Early tempo (0–5m), prioritaskan invasi & snowball outer turret shield.";
            $dom = 'Early';
        } elseif ($mid > $early && $mid >= $late) {
            $text = "Unggul di Mid game (5–12m), puncak rotasi gank & perebutan objektif Turtle/Lord.";
            $dom = 'Mid';
        } elseif ($late > $early && $late >= $mid) {
            $text = "Unggul di Late game (12+m / Trinity MM), pertahankan wave untuk scaling penuh hingga item Trinity jadi.";
            $dom = 'Late';
        } else {
            $text = "Kurva power spike stabil di seluruh fase permainan.";
            $dom = 'Balanced';
        }

        return [
            'text'     => $text,
            'dominant' => $dom,
        ];
    }

    /**
     * Komparasi power spike dan tempo pertandingan antar Tim Sekutu dan Tim Musuh
     */
    public function comparePowerSpikes(array $allySpikes, int $allyCount, array $enemySpikes, int $enemyCount): string
    {
        if ($allyCount === 0 && $enemyCount === 0) {
            return "Menunggu pemilihan hero untuk membandingkan tempo dan kurva power spike kedua tim.";
        }
        if ($allyCount === 0) {
            return "Menunggu pemilihan hero Tim Sekutu untuk analisis komparasi tempo pertandingan.";
        }
        if ($enemyCount === 0) {
            return "Menunggu pemilihan hero Tim Musuh untuk analisis komparasi tempo pertandingan.";
        }

        $aEarly = $allySpikes['early'] ?? 0;
        $aMid   = $allySpikes['mid'] ?? 0;
        $aLate  = $allySpikes['late'] ?? 0;

        $eEarly = $enemySpikes['early'] ?? 0;
        $eMid   = $enemySpikes['mid'] ?? 0;
        $eLate  = $enemySpikes['late'] ?? 0;

        if ($aEarly > $eEarly && $eLate > $aLate) {
            return "Kesimpulan: Tim Sekutu unggul di Early tempo (0–5m); Tim Musuh mendominasi Late scaling (12+m / Trinity MM). Sekutu wajib selesaikan game cepat sebelum carry musuh scaling!";
        }
        if ($eEarly > $aEarly && $aLate > $eLate) {
            return "Kesimpulan: Tim Musuh menekan di Early game (0–5m); Tim Sekutu mendominasi Late scaling (12+m / Trinity MM). Bertahan di bawah turret hingga Marksman menyelesaikan Trinity build!";
        }
        if ($aMid > $eMid && $eLate > $aLate) {
            return "Kesimpulan: Tim Sekutu unggul di Mid game peak (5–12m); waspadai lonjakan kekuatan Tim Musuh saat memasuki Late game (12+m / Trinity MM).";
        }
        if ($eMid > $aMid && $aLate > $eLate) {
            return "Kesimpulan: Tim Musuh kuat di perebutan Lord menit 8–12; Tim Sekutu unggul saat game memasuki Late game (12+m / Trinity MM).";
        }
        if ($aEarly > $eEarly && $aMid >= $eMid) {
            return "Kesimpulan: Tim Sekutu mendominasi tempo Early & Mid game, pegang kendali Turtle dan Lord pertama!";
        }
        if ($eEarly > $aEarly && $eMid >= $aMid) {
            return "Kesimpulan: Tim Musuh mendominasi tempo Early & Mid game, waspadai invasi agresif ke jungle sekutu.";
        }
        if ($aLate > $eLate) {
            return "Kesimpulan: Tim Sekutu unggul di Late game (12+m / Trinity MM); formasi teamfight 5v5 sangat mematikan di fase akhir saat carry mencapai item optimal.";
        }
        if ($eLate > $aLate) {
            return "Kesimpulan: Tim Musuh unggul di Late game (12+m / Trinity MM); hindari memperpanjang pertandingan setelah menit 12 jika objektif turret belum diamankan.";
        }

        return "Kesimpulan: Kurva power spike kedua tim relatif seimbang; hasil laga ditentukan oleh eksekusi teamfight dan draft counter.";
    }

    /**
     * Evaluasi Draft Identity & Taktik Cara Bermain / Counter Play
     */
    public function evaluateDraftIdentity(array $teamHeroes, string $teamType): array
    {
        $count = count($teamHeroes);
        if ($count === 0) {
            return [
                'archetype'       => $teamType === 'ally' ? 'Draft Belum Terbentuk' : 'Draft Lawan Belum Terdeteksi',
                'badge'           => 'Draft Kosong',
                'badge_color'     => 'bg-gray-100 text-gray-600 border-gray-200',
                'icon'            => '',
                'status'          => 'Pilih hero untuk mengaktifkan analisis identitas draft dan rekomendasi taktik.',
                'win_condition'   => 'Menunggu pemilihan hero...',
                'game_plan'       => 'Pilih minimal 1 hero untuk melihat cara bermain dan objektif utama tim.',
                'counter_advice'  => 'Pilih minimal 1 hero lawan untuk menganalisis kelemahan dan cara melawannya.',
                'key_strengths'   => [],
                'key_weaknesses'  => [],
            ];
        }

        $ccHeroes = ['tigreal', 'atlas', 'carmilla', 'akai', 'khufra', 'ruby', 'kaja', 'franco', 'chou', 'minotaur', 'lolita', 'gloo', 'fredrinn', 'arlott', 'belerick', 'gatotkaca'];
        $aoeHeroes = ['yve', 'pharsa', 'xavier', 'vexana', 'cecilion', 'lylia', 'claude', 'hanabi', 'odette', 'aurora', 'gord', 'luoyi'];
        $pickoffHeroes = ['saber', 'kaja', 'franco', 'chou', 'ling', 'fanny', 'hayabusa', 'helcurt', 'natalia', 'gusion', 'lancelot', 'suyou', 'harley', 'selena', 'nolan', 'yin', 'karina', 'gusion', 'aamon'];
        $sustainHeroes = ['estes', 'floryn', 'angela', 'rafaela', 'minotaur', 'carmilla', 'baxia', 'hylos', 'barats', 'uranus', 'fredrinn', 'ruby', 'yuzhong', 'terizla', 'esmeralda', 'hilda'];
        $pokeHeroes = ['pharsa', 'xavier', 'novaria', 'beatrix', 'brody', 'change', 'selena', 'valir', 'lesley', 'izhuxin'];
        $earlyHeroes = ['martis', 'hilda', 'grock', 'jawhead', 'fanny', 'selena', 'dyrroth', 'terizla', 'thamuz', 'freya', 'paquito'];
        $lateHeroes = ['claude', 'karrie', 'moskov', 'miya', 'layla', 'bruno', 'irithel', 'cecilion', 'aldous', 'natan', 'lesley'];

        $ccCount = 0;
        $aoeCount = 0;
        $pickoffCount = 0;
        $sustainCount = 0;
        $pokeCount = 0;
        $earlyCount = 0;
        $lateCount = 0;

        foreach ($teamHeroes as $h) {
            $nameLow = preg_replace('/[^a-z0-9]/', '', strtolower($h['name'] ?? ''));
            if (in_array($nameLow, $ccHeroes)) $ccCount++;
            if (in_array($nameLow, $aoeHeroes)) $aoeCount++;
            if (in_array($nameLow, $pickoffHeroes)) $pickoffCount++;
            if (in_array($nameLow, $sustainHeroes)) $sustainCount++;
            if (in_array($nameLow, $pokeHeroes)) $pokeCount++;
            if (in_array($nameLow, $earlyHeroes)) $earlyCount++;
            if (in_array($nameLow, $lateHeroes)) $lateCount++;
        }

        if ($ccCount >= 2 && $aoeCount >= 1) {
            $archetype = 'AoE Teamfight (Wombo Combo)';
            $badgeColor = 'bg-purple-100 text-purple-900 border-purple-200';
            $icon = '';
            $winCondition = 'Dominasi pertarungan 5v5 di ruang terbuka pada kontes Turtle & Lord menit 10+.';
            $gamePlan = 'Buka inisiasi menggunakan crowd control area garis depan, lalu tumpuk seluruh burst skill area secara serempak. Hindari tersplit.';
            $counterAdvice = 'Hindari berkerumun di lorong sempit. Gunakan Battle Spell Purify atau hero anti-CC (Diggie) dan pancing inisiasi mereka dengan bait.';
            $strengths = ['Teamfight 5v5 mematikan', 'Kontrol objektif Lord tinggi', 'Zone control superior'];
            $weaknesses = ['Rentan split-push jika cooldown skill ultimate habis', 'Ketergantungan tinggi pada inisiasi hero frontliner'];
        } elseif ($pickoffCount >= 2 || ($pickoffCount >= 1 && $ccCount >= 1)) {
            $archetype = 'Pick-Off & Single Target Ambush';
            $badgeColor = 'bg-rose-100 text-rose-900 border-rose-200';
            $icon = '';
            $winCondition = 'Eliminasi instan carry/mage musuh di semi-river jungle sebelum objektif dimulai.';
            $gamePlan = 'Kuasai vision semak-semak dan jalankan rotasi 2-3 pemain untuk menyergap musuh yang overextend di side lane.';
            $counterAdvice = 'Jaga jarak formasi dari semak tanpa vision. Beli item physical defense (Antique Cuirass) dan jangan berjalan sendirian tanpa roamer.';
            $strengths = ['Burst damage instan', 'Mobilitas rotasi cepat', 'Mampu menciptakan surplus jumlah (5v4)'];
            $weaknesses = ['Lemah dalam frontal war 5v5 murni jika target utama gagal di-burst instan'];
        } elseif ($sustainCount >= 2) {
            $archetype = 'Sustain & Frontline Brawl';
            $badgeColor = 'bg-emerald-100 text-emerald-900 border-emerald-200';
            $icon = '';
            $winCondition = 'Paksa pertempuran berkepanjangan (extended fight) hingga sumber daya & mana musuh terkuras.';
            $gamePlan = 'Bergerak rapat bersama hero regen/healer. Biarkan frontliner menyerap damage musuh sambil perlahan mendesak turret dan lord.';
            $counterAdvice = 'Wajib beli item anti-heal (Sea Halberd / Glowing Wand / Dominance Ice) seawal mungkin. Hindari trade damage tanpa burst instan.';
            $strengths = ['Ketahanan perang panjang sangat tinggi', 'Pemberian cover backline stabil', 'Sangat kuat merebut area turret'];
            $weaknesses = ['Rentan terhadap true damage (Karrie, Lesley) dan item anti-heal'];
        } elseif ($pokeCount >= 2) {
            $archetype = 'Poke & High-Ground Siege';
            $badgeColor = 'bg-amber-100 text-amber-900 border-amber-200';
            $icon = '';
            $winCondition = 'Kikis HP musuh dari jarak aman sebelum objektif, lalu hancurkan base turret tanpa kontak jarak dekat.';
            $gamePlan = 'Gunakan skill jarak jauh untuk membersihkan wave minion dan mencicil formasi musuh. Jaga formasi tetap di belakang batas river.';
            $counterAdvice = 'Gunakan hero assassin atau inisiator penyelam (dive-in) seperti Ling, Joy, atau Chou untuk menerobos langsung ke backline.';
            $strengths = ['Jangkauan serangan jauh', 'Defensif base sangat kokoh', 'Mencegah musuh mendekati lord'];
            $weaknesses = ['Sangat rapuh bila didekati hero berjarak serang dekat (close combat burst)'];
        } elseif ($earlyCount >= 2) {
            $archetype = 'Early Tempo & Jungle Snowball';
            $badgeColor = 'bg-orange-100 text-orange-900 border-orange-200';
            $icon = '';
            $winCondition = 'Amankan keunggulan net worth di fase laning menit 0-5 dan runtuhkan turret sebelum Marksman musuh menyelesaikan Trinity core item.';
            $gamePlan = 'Lakukan invasi buff jungle musuh sejak level 1-2, amankan seluruh turtle, dan hancurkan outer turret secepatnya.';
            $counterAdvice = 'Bermain bertahan di bawah turret pada 5 menit awal (manfaatkan shield turret). Hindari kontes turtle berisiko dan stall game menuju fase late game menit 12+.';
            $strengths = ['Kekuatan duel level awal sangat tinggi', 'Snowball ekonomi pesat'];
            $weaknesses = ['Potensi power spike menurun jika pertandingan melewati menit 12 (fase Evolved Lord & Trinity MM)'];
        } elseif ($lateCount >= 2) {
            $archetype = 'Late-Game Hypercarry Scaling';
            $badgeColor = 'bg-indigo-100 text-indigo-900 border-indigo-200';
            $icon = '';
            $winCondition = 'Lindungi carry hingga menyelesaikan build Trinity (Corrosion, DHS, Golden Staff) untuk membalikkan teamfight menit 12+.';
            $gamePlan = 'Farming aman, minimalkan kematian dini, dan hanya lakukan teamfight bila benar-benar menguntungkan secara posisi.';
            $counterAdvice = 'Gank carry musuh secara terus menerus di early game. Rebut turret gold lane dan akhiri game sebelum late-game tercapai.';
            $strengths = ['Damage per second (DPS) tak tertandingi di late game'];
            $weaknesses = ['Early game sangat lemah dan bergantung pada ruang farming tim'];
        } else {
            $archetype = 'Flex & Balanced Tournament Meta';
            $badgeColor = 'bg-blue-100 text-blue-900 border-blue-200';
            $icon = '';
            $winCondition = 'Eksekusi rotasi lane standar, pertahankan keseimbangan map, dan menangkan war berbasis disiplin posisi.';
            $gamePlan = 'Jalankan rotasi proaktif ke side lane, jaga formasi segitiga (frontliner di depan, dps di belakang), dan prioritaskan turtle.';
            $counterAdvice = 'Eksploitasi hero musuh yang terpisah dari formasi atau pancing pertarungan saat skill pelarian mereka sedang cooldown.';
            $strengths = ['Fleksibel di segala fase permainan', 'Tidak memiliki kelemahan komposisi yang fatal'];
            $weaknesses = ['Memerlukan koordinasi tim yang disiplin untuk menentukan fokus objektif'];
        }

        return [
            'archetype'       => $archetype,
            'badge'           => $archetype,
            'badge_color'     => $badgeColor,
            'icon'            => $icon,
            'status'          => $teamType === 'ally' ? "Identitas: {$archetype}" : "Gaya Main Musuh: {$archetype}",
            'win_condition'   => $winCondition,
            'game_plan'       => $gamePlan,
            'counter_advice'  => $counterAdvice,
            'key_strengths'   => $strengths,
            'key_weaknesses'  => $weaknesses,
        ];
    }

    /**
     * RBR Ringan (Draft Strategy Engine)
     * Mengganti aturan RBR lama yang kaku dengan aturan IF-THEN ringan yang membaca
     * komposisi hero tim (dari hero yang sudah di-pick di simulator + identitas tim yang dipilih)
     * untuk menghasilkan 4 output terstruktur:
     * 1. draft_identity: "Double Fighter / Bruiser", "Marksman Late Scaling", "Pick-off / High Mobility", "Balanced Teamfight", dll.
     * 2. power_spike: "Early Game (0–5m)", "Early-to-Mid (0–12m)", atau "Mid-to-Late (12+m)"
     * 3. playstyle: "Teamfight / War", "Pick-off / Ganking", atau "Split Push / Objective"
     * 4. winning_condition: 1–2 kalimat taktis konkret
     *
     * @param array $teamHeroes Array hero sekutu di working memory (atau array nama hero)
     * @param array|null $teamDna Profil Draft DNA tim yang dipilih
     * @return array [draft_identity, power_spike, playstyle, winning_condition]
     */
    public function evaluateDraftStrategyEngine(array $teamHeroes = [], ?array $teamDna = null): array
    {
        $allyCount = count($teamHeroes);
        $teamTag = $teamDna['playstyle_tag'] ?? '';

        // Kasus 1: Belum ada hero yang di-pick di simulator (draft awal)
        // Gunakan profil identitas dasar dari Draft DNA tim turnamen yang dipilih
        if ($allyCount === 0) {
            if ($teamTag === 'Double Fighter') {
                return [
                    'draft_identity'    => 'Double Fighter / Bruiser',
                    'power_spike'       => 'Early-to-Mid (0–12m)',
                    'playstyle'         => 'Pick-off / Ganking',
                    'winning_condition' => 'Wajib snowball dan amankan objektif Turtle/Lord di early-to-mid game; hindari late game karena minim hitter turret.',
                ];
            } elseif ($teamTag === 'Late Game Scaling') {
                return [
                    'draft_identity'    => 'Marksman Late Scaling',
                    'power_spike'       => 'Mid-to-Late (12+m)',
                    'playstyle'         => 'Teamfight / War',
                    'winning_condition' => 'Fokus proteksi Goldlaner hingga 2-3 core item selesai; stall game hingga menit 12+ untuk mendominasi war Evolved Lord.',
                ];
            } elseif ($teamTag === 'Assassin Snowball') {
                return [
                    'draft_identity'    => 'Pick-off / High Mobility',
                    'power_spike'       => 'Early Game (0–5m)',
                    'playstyle'         => 'Pick-off / Ganking',
                    'winning_condition' => 'Kuasai vision semak river untuk mengisolasi carry musuh sebelum kontes objektif; hindari teamfight 5v5 frontal terbuka.',
                ];
            } elseif ($teamTag === 'Tank Jungler') {
                return [
                    'draft_identity'    => 'Double Fighter / Bruiser',
                    'power_spike'       => 'Early-to-Mid (0–12m)',
                    'playstyle'         => 'Teamfight / War',
                    'winning_condition' => 'Manfaatkan durabilitas tinggi frontliner untuk merebut kendali Turtle & Lord; perang panjang berkelanjutan menguntungkan formasi tim.',
                ];
            } elseif ($teamTag === 'Healer / Sustain') {
                return [
                    'draft_identity'    => 'Healer / Sustain Comp',
                    'power_spike'       => 'Mid-to-Late Game (5–12+m)',
                    'playstyle'         => 'Teamfight / War (Grouping)',
                    'winning_condition' => 'Wajib grouping rapat (5v5) saat kontes Turtle/Lord untuk memaksimalkan sustain heal dalam war berkepanjangan; hindari bermain terpisah agar tidak mudah di-pick-off.',
                ];
            } else {
                return [
                    'draft_identity'    => 'Flexible / Adaptive Meta',
                    'power_spike'       => 'Early-to-Mid (0–12m)',
                    'playstyle'         => 'Teamfight / War',
                    'winning_condition' => 'Gaya main fleksibel dan adaptif; komposisi dan win condition menyesuaikan hero yang sedang di-pick untuk mengeksploitasi celah lawan.',
                ];
            }
        }

        // Kasus 2: Minimal 1 hero terpilih di simulator
        // Lookup role & atribut hero dinamis dari CsvDataService (tanpa ketergantungan array hardcode)
        $allCatalog = $this->csvService->getHeroes()->keyBy(fn($h) => preg_replace('/[^a-z0-9]/', '', strtolower($h->hero_name)));

        $fighterCount = 0;
        $tankCount = 0;
        $assassinCount = 0;
        $marksmanCount = 0;
        $mageCount = 0;
        $supportCount = 0;
        $earlyCount = 0;
        $lateCount = 0;
        $splitCount = 0;

        $knownLate = ['claude', 'karrie', 'moskov', 'miya', 'layla', 'bruno', 'irithel', 'cecilion', 'aldous', 'natan', 'lesley', 'wanwan', 'ixia', 'harith'];
        $knownEarly = ['martis', 'hilda', 'grock', 'jawhead', 'fanny', 'selena', 'dyrroth', 'terizla', 'thamuz', 'freya', 'paquito', 'chou', 'saber', 'mathilda'];
        $knownSplit = ['zilong', 'sun', 'hayabusa', 'masha', 'benedetta', 'argus', 'ling', 'chou'];
        $knownHealers = ['estes', 'floryn', 'angela', 'rafaela', 'mathilda', 'diggie', 'carmilla'];

        foreach ($teamHeroes as $h) {
            $name = is_array($h) ? ($h['name'] ?? '') : (is_string($h) ? $h : '');
            $cleanName = preg_replace('/[^a-z0-9]/', '', strtolower($name));
            $hCatalog = $allCatalog[$cleanName] ?? null;

            $role = $hCatalog ? strtolower($hCatalog->class ?: $hCatalog->primary_class) : (is_array($h) ? strtolower($h['role'] ?? '') : '');
            $spike = is_array($h) ? strtolower($h['power_spike'] ?? '') : '';

            if (str_contains($role, 'fighter')) $fighterCount++;
            if (str_contains($role, 'tank')) $tankCount++;
            if (str_contains($role, 'assassin')) $assassinCount++;
            if (str_contains($role, 'marksman')) $marksmanCount++;
            if (str_contains($role, 'mage')) $mageCount++;
            if (str_contains($role, 'support') || in_array($cleanName, $knownHealers)) $supportCount++;

            if (str_contains($spike, 'early') || in_array($cleanName, $knownEarly)) $earlyCount++;
            if (str_contains($spike, 'late') || in_array($cleanName, $knownLate)) $lateCount++;
            if (in_array($cleanName, $knownSplit)) $splitCount++;
        }

        // Aturan IF-THEN Ringan:
        // 1. Healer / Sustain Comp
        if ($supportCount >= 1) {
            return [
                'draft_identity'    => 'Healer / Sustain Comp',
                'power_spike'       => 'Mid-to-Late Game (5–12+m)',
                'playstyle'         => 'Teamfight / War (Grouping)',
                'winning_condition' => 'Wajib grouping rapat (5v5) saat kontes Turtle/Lord untuk memaksimalkan sustain heal dalam war berkepanjangan; hindari bermain terpisah agar tidak mudah di-pick-off.',
            ];
        }

        // 2. Double Fighter / Bruiser
        if ($fighterCount >= 2 || ($fighterCount >= 1 && $tankCount >= 1 && ($teamTag === 'Double Fighter' || $teamTag === 'Tank Jungler'))) {
            return [
                'draft_identity'    => 'Double Fighter / Bruiser',
                'power_spike'       => 'Early-to-Mid (0–12m)',
                'playstyle'         => 'Pick-off / Ganking',
                'winning_condition' => 'Wajib snowball dan amankan objektif Turtle/Lord di early-to-mid game; hindari late game karena minim hitter turret.',
            ];
        }

        // 3. Marksman Late Scaling
        if ($marksmanCount >= 1 && ($lateCount >= 1 || $tankCount >= 1 || $supportCount >= 1 || $teamTag === 'Late Game Scaling')) {
            return [
                'draft_identity'    => 'Marksman Late Scaling',
                'power_spike'       => 'Mid-to-Late (12+m)',
                'playstyle'         => 'Teamfight / War',
                'winning_condition' => 'Fokus proteksi Goldlaner hingga 2-3 core item selesai; stall game hingga menit 12+ untuk mendominasi war Evolved Lord.',
            ];
        }

        // 4. Pick-off / High Mobility
        if ($assassinCount >= 1 || $splitCount >= 1 || $teamTag === 'Assassin Snowball') {
            return [
                'draft_identity'    => 'Pick-off / High Mobility',
                'power_spike'       => ($earlyCount >= 2) ? 'Early Game (0–5m)' : 'Early-to-Mid (0–12m)',
                'playstyle'         => 'Pick-off / Ganking',
                'winning_condition' => 'Kuasai vision semak river untuk mengisolasi carry musuh sebelum kontes objektif; hindari teamfight 5v5 frontal terbuka.',
            ];
        }

        // 5. Split Push / Objective
        if ($splitCount >= 1 && $allyCount >= 2) {
            return [
                'draft_identity'    => 'Split Push / Objective',
                'power_spike'       => 'Mid-to-Late (12+m)',
                'playstyle'         => 'Split Push / Objective',
                'winning_condition' => 'Berikan tekanan di side lane berlawanan saat Lord muncul; tarik 2-3 hero lawan sebelum tim sekutu mengamankan objektif utama.',
            ];
        }

        // 6. Balanced Teamfight (Default)
        return [
            'draft_identity'    => 'Balanced Teamfight',
            'power_spike'       => ($earlyCount > $lateCount) ? 'Early-to-Mid (0–12m)' : (($lateCount > 0) ? 'Mid-to-Late (12+m)' : 'Early-to-Mid (0–12m)'),
            'playstyle'         => 'Teamfight / War',
            'winning_condition' => 'Pertahankan formasi segitiga saat kontes Turtle; tunggu inisiasi Tank sebelum backline melakukan burst damage.',
        ];
    }

    /**
     * Proxy ke TeamDraftDnaService untuk mencari 2-3 pertandingan asli MPL ID S18 yang paling mirip
     */
    public function getSimilarMatches(array $currentPicks = [], ?string $teamName = 'Alter Ego', int $limit = 3): array
    {
        return $this->teamDnaService->getSimilarMatches($currentPicks, $teamName, $limit);
    }
}

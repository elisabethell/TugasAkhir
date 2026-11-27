<?php

namespace Tests\Unit;

use App\Models\Hero;
use App\Models\MatchDataset;
use App\Services\CsvDataService;
use App\Services\DraftRecommendationService;
use App\Services\TeamDraftDnaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftRecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DraftRecommendationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        CsvDataService::clearCache();
        TeamDraftDnaService::clearCache();
        $this->service = app(DraftRecommendationService::class);
    }

    public function test_saw_constants_and_shrinkage_are_exposed(): void
    {
        $this->assertEqualsWithDelta(0.40, DraftRecommendationService::ALPHA, 1e-6);
        $this->assertEqualsWithDelta(0.35, DraftRecommendationService::BETA, 1e-6);
        $this->assertEqualsWithDelta(0.25, DraftRecommendationService::GAMMA, 1e-6);
        $this->assertSame(5, DraftRecommendationService::M_SHRINKAGE);
    }

    public function test_hybrid_saw_bayesian_shrinkage_filters_and_scores_top_10(): void
    {
        // Seed 12 hero dengan variasi 5 atribut Coach
        $heroes = [
            ['hero_id' => 'h01', 'hero_name' => 'Tigreal',  'sustainability' => 9, 'attack_power' => 3, 'crowd_control' => 10, 'mobility' => 4, 'lane_control' => 6],
            ['hero_id' => 'h02', 'hero_name' => 'Harith',   'sustainability' => 4, 'attack_power' => 9, 'crowd_control' => 3,  'mobility' => 9, 'lane_control' => 8],
            ['hero_id' => 'h03', 'hero_name' => 'Paquito',  'sustainability' => 7, 'attack_power' => 8, 'crowd_control' => 6,  'mobility' => 8, 'lane_control' => 7],
            ['hero_id' => 'h04', 'hero_name' => 'Pharsa',   'sustainability' => 3, 'attack_power' => 9, 'crowd_control' => 5,  'mobility' => 6, 'lane_control' => 9],
            ['hero_id' => 'h05', 'hero_name' => 'Claude',   'sustainability' => 3, 'attack_power' => 9, 'crowd_control' => 2,  'mobility' => 8, 'lane_control' => 8],
            ['hero_id' => 'h06', 'hero_name' => 'Khufra',   'sustainability' => 9, 'attack_power' => 3, 'crowd_control' => 10, 'mobility' => 6, 'lane_control' => 5],
            ['hero_id' => 'h07', 'hero_name' => 'Fanny',    'sustainability' => 4, 'attack_power' => 9, 'crowd_control' => 2,  'mobility' => 10,'lane_control' => 6],
            ['hero_id' => 'h08', 'hero_name' => 'Ling',     'sustainability' => 4, 'attack_power' => 9, 'crowd_control' => 3,  'mobility' => 10,'lane_control' => 6],
            ['hero_id' => 'h09', 'hero_name' => 'Belerick', 'sustainability' => 10,'attack_power' => 4, 'crowd_control' => 8,  'mobility' => 3, 'lane_control' => 5],
            ['hero_id' => 'h10', 'hero_name' => 'Zhuxin',   'sustainability' => 5, 'attack_power' => 7, 'crowd_control' => 9,  'mobility' => 5, 'lane_control' => 8],
            ['hero_id' => 'h11', 'hero_name' => 'Fredrinn', 'sustainability' => 9, 'attack_power' => 5, 'crowd_control' => 8,  'mobility' => 4, 'lane_control' => 6],
            ['hero_id' => 'h12', 'hero_name' => 'Moskov',   'sustainability' => 3, 'attack_power' => 9, 'crowd_control' => 4,  'mobility' => 7, 'lane_control' => 7],
        ];

        foreach ($heroes as $h) {
            Hero::create($h);
        }

        // Seed pertandingan MPL ID S18: Alter Ego sering menang bersama Tigreal & Harith melawan Fanny
        $match = MatchDataset::create([
            'match_id'     => 'm_saw_1',
            'team_1_name'  => 'Alter Ego',
            'team_2_name'  => 'RRQ',
            'team_1_score' => 2,
            'team_2_score' => 0,
            'stage'        => 'Regular Season',
        ]);

        $match->games()->createMany([
            [
                'match_id'      => 'm_saw_1',
                'game_number'   => 1,
                'team_1_picks'  => ['Tigreal', 'Harith', 'Paquito', 'Pharsa', 'Claude'],
                'team_2_picks'  => ['Fanny', 'Ling', 'Belerick', 'Zhuxin', 'Moskov'],
                'team_1_status' => 'W',
                'team_2_status' => 'L',
            ],
            [
                'match_id'      => 'm_saw_1',
                'game_number'   => 2,
                'team_1_picks'  => ['Tigreal', 'Harith', 'Paquito', 'Pharsa', 'Claude'],
                'team_2_picks'  => ['Fanny', 'Fredrinn', 'Belerick', 'Zhuxin', 'Moskov'],
                'team_1_status' => 'W',
                'team_2_status' => 'L',
            ],
        ]);

        // 1. Uji kondisi ally & enemy kosong -> S_comp & S_counter nonaktif, bobot S_dna ternormalisasi menjadi 1.0
        $emptyDraft = $this->service->recommend([], [], ['Ling'], 'Alter Ego', 10);
        $this->assertCount(10, $emptyDraft['recommendations']);
        $this->assertFalse($emptyDraft['active_components']['s_comp']);
        $this->assertFalse($emptyDraft['active_components']['s_counter']);
        $this->assertEqualsWithDelta(1.0, $emptyDraft['weights']['alpha'], 1e-6);
        $this->assertEqualsWithDelta(0.0, $emptyDraft['weights']['beta'], 1e-6);
        $this->assertEqualsWithDelta(0.0, $emptyDraft['weights']['gamma'], 1e-6);

        $namesEmpty = array_column($emptyDraft['recommendations'], 'hero');
        $this->assertNotContains('Ling', $namesEmpty);

        // 2. Uji kondisi dengan ally_picks & enemy_picks terisi -> semua 3 komponen aktif (total bobot 1.0)
        $activeDraft = $this->service->recommend(
            allyPicks: ['Harith'],
            enemyPicks: ['Fanny'],
            bannedHeroes: ['Ling'],
            teamName: 'Alter Ego',
            limit: 10
        );

        $this->assertCount(7, $activeDraft['recommendations']); // 12 total - 3 excluded (Harith, Fanny, Ling) - 2 duplicate Mid Lane (Pharsa, Zhuxin) = 7
        $this->assertTrue($activeDraft['active_components']['s_dna']);
        $this->assertTrue($activeDraft['active_components']['s_comp']);
        $this->assertTrue($activeDraft['active_components']['s_counter']);
        $this->assertEqualsWithDelta(0.40, $activeDraft['weights']['alpha'], 1e-6);
        $this->assertEqualsWithDelta(0.35, $activeDraft['weights']['beta'], 1e-6);
        $this->assertEqualsWithDelta(0.25, $activeDraft['weights']['gamma'], 1e-6);

        $namesActive = array_column($activeDraft['recommendations'], 'hero');
        $this->assertNotContains('Harith', $namesActive);
        $this->assertNotContains('Fanny', $namesActive);
        $this->assertNotContains('Ling', $namesActive);
        $this->assertNotContains('Claude', $namesActive, 'Claude terfilter karena Harith sudah mengisi Gold Lane');
        $this->assertNotContains('Moskov', $namesActive, 'Moskov terfilter karena Harith sudah mengisi Gold Lane');

        // Verifikasi struktur transparan & urutan descending
        $top = $activeDraft['recommendations'][0];
        $this->assertArrayHasKey('total_score', $top);
        $this->assertArrayHasKey('s_dna', $top);
        $this->assertArrayHasKey('s_comp', $top);
        $this->assertArrayHasKey('s_counter', $top);
        $this->assertArrayHasKey('coach_attributes', $top);
        $this->assertCount(5, $top['coach_attributes']['normalized']);

        for ($i = 1; $i < count($activeDraft['recommendations']); $i++) {
            $this->assertGreaterThanOrEqual(
                $activeDraft['recommendations'][$i]['saw_score'],
                $activeDraft['recommendations'][$i - 1]['saw_score']
            );
        }
    }

    public function test_weight_renormalization_when_only_ally_or_only_enemy_is_present(): void
    {
        Hero::create(['hero_id' => 'h1', 'hero_name' => 'Alpha', 'sustainability' => 8, 'attack_power' => 7, 'crowd_control' => 6, 'mobility' => 5, 'lane_control' => 7]);
        Hero::create(['hero_id' => 'h2', 'hero_name' => 'Beta',  'sustainability' => 4, 'attack_power' => 9, 'crowd_control' => 3, 'mobility' => 8, 'lane_control' => 6]);
        Hero::create(['hero_id' => 'h3', 'hero_name' => 'Gamma', 'sustainability' => 5, 'attack_power' => 5, 'crowd_control' => 5, 'mobility' => 5, 'lane_control' => 5]);

        // Hanya ally_picks -> ALPHA (0.40) + BETA (0.35) dinormalisasi terhadap 0.75
        $allyOnly = $this->service->recommend(['Alpha'], [], [], null, 10);
        $this->assertEqualsWithDelta(0.40 / 0.75, $allyOnly['weights']['alpha'], 1e-4);
        $this->assertEqualsWithDelta(0.35 / 0.75, $allyOnly['weights']['beta'], 1e-4);
        $this->assertEqualsWithDelta(0.0, $allyOnly['weights']['gamma'], 1e-4);

        // Hanya enemy_picks -> ALPHA (0.40) + GAMMA (0.25) dinormalisasi terhadap 0.65
        $enemyOnly = $this->service->recommend([], ['Alpha'], [], null, 10);
        $this->assertEqualsWithDelta(0.40 / 0.65, $enemyOnly['weights']['alpha'], 1e-4);
        $this->assertEqualsWithDelta(0.0, $enemyOnly['weights']['beta'], 1e-4);
        $this->assertEqualsWithDelta(0.25 / 0.65, $enemyOnly['weights']['gamma'], 1e-4);

        // Ketika semua nilai mentah sama (max == min), Min-Max mengembalikan 0.5 tanpa division by zero
        $this->assertEqualsWithDelta(0.5, $enemyOnly['recommendations'][0]['s_dna']['norm'], 1e-6);
    }

    public function test_quantitative_rbr_explanation_layer_detects_deficits_identity_and_winning_condition(): void
    {
        Hero::create(['hero_id' => 'h1', 'hero_name' => 'Tigreal', 'sustainability' => 9, 'attack_power' => 3, 'crowd_control' => 10, 'mobility' => 4, 'lane_control' => 5]);
        Hero::create(['hero_id' => 'h2', 'hero_name' => 'Atlas',   'sustainability' => 8, 'attack_power' => 4, 'crowd_control' => 9,  'mobility' => 5, 'lane_control' => 4]);
        Hero::create(['hero_id' => 'h3', 'hero_name' => 'Fanny',   'sustainability' => 3, 'attack_power' => 9, 'crowd_control' => 2,  'mobility' => 10,'lane_control' => 5]);
        Hero::create(['hero_id' => 'h4', 'hero_name' => 'Ling',    'sustainability' => 3, 'attack_power' => 9, 'crowd_control' => 2,  'mobility' => 10,'lane_control' => 5]);
        Hero::create(['hero_id' => 'h5', 'hero_name' => 'Zilong',  'sustainability' => 8, 'attack_power' => 5, 'crowd_control' => 4,  'mobility' => 6, 'lane_control' => 9]);

        // Target pemenang T_k memiliki crowd_control = 0.60 (6.0), attack_power = 0.70 (7.0)
        $target = [
            'sustainability' => 0.70,
            'attack_power'   => 0.70,
            'crowd_control'  => 0.60,
            'mobility'       => 0.60,
            'lane_control'   => 0.60,
        ];

        // 1. Sustainability + Crowd Control dominan -> Teamfight / Front-to-Back + menang CC/Sus vs Fanny+Ling -> paksa teamfight 5v5
        $teamfight = $this->service->evaluateQuantitativeStrategyExplanation(['Tigreal', 'Atlas'], ['Fanny', 'Ling'], $target);
        $this->assertTrue($teamfight['has_ally']);
        $this->assertEquals('Teamfight / Front-to-Back', $teamfight['draft_identity']);
        $this->assertStringContainsString('Paksa teamfight 5v5', $teamfight['winning_condition']);
        // Attack Power rata-rata (3.5) vs target (7.0) -> selisih 3.5 > 1.5 -> warning dengan angka nyata
        $atkWarn = collect($teamfight['attribute_warnings'])->firstWhere('attribute', 'attack_power');
        $this->assertNotNull($atkWarn);
        $this->assertStringContainsString('3.5', $atkWarn['message']);
        $this->assertStringContainsString('7.0', $atkWarn['message']);

        // 2. Mobility + Attack Power dominan -> Pick-off / Skirmish
        $pickoff = $this->service->evaluateQuantitativeStrategyExplanation(['Fanny', 'Ling'], [], $target);
        $this->assertEquals('Pick-off / Skirmish', $pickoff['draft_identity']);
        $ccWarn = collect($pickoff['attribute_warnings'])->firstWhere('attribute', 'crowd_control');
        $this->assertNotNull($ccWarn);
        $this->assertStringContainsString('2.0', $ccWarn['message']);
        $this->assertStringContainsString('6.0', $ccWarn['message']);

        // 3. Lane Control + Sustainability dominan -> Macro / Split Push + unggul Lane Control vs musuh
        $macro = $this->service->evaluateQuantitativeStrategyExplanation(['Zilong'], ['Fanny'], $target);
        $this->assertEquals('Macro / Split Push', $macro['draft_identity']);
    }

    public function test_empty_lane_hard_constraint_filter_in_layer_1(): void
    {
        // 1. Verifikasi konstanta STANDARD_LANES
        $expectedLanes = ['EXP Lane', 'Jungle', 'Mid Lane', 'Gold Lane', 'Roaming'];
        $this->assertSame($expectedLanes, DraftRecommendationService::STANDARD_LANES);

        // 2. Verifikasi fungsi normalizeLaneName
        $this->assertSame('EXP Lane', $this->service->normalizeLaneName('exp lane'));
        $this->assertSame('Jungle', $this->service->normalizeLaneName('jungle'));
        $this->assertSame('Mid Lane', $this->service->normalizeLaneName('mid lane'));
        $this->assertSame('Gold Lane', $this->service->normalizeLaneName('gold lane'));
        $this->assertSame('Roaming', $this->service->normalizeLaneName('roam'));
        $this->assertSame('Roaming', $this->service->normalizeLaneName('roaming'));

        // 3. Seed 6 hero dengan variasi lane_1 & lane_2
        Hero::create(['hero_id' => 'h_roam_1', 'hero_name' => 'Tigreal', 'lane_1' => 'Roaming',  'lane_2' => null,       'sustainability' => 9, 'attack_power' => 3, 'crowd_control' => 10, 'mobility' => 4, 'lane_control' => 6]);
        Hero::create(['hero_id' => 'h_roam_2', 'hero_name' => 'Khufra',  'lane_1' => 'Roaming',  'lane_2' => null,       'sustainability' => 9, 'attack_power' => 3, 'crowd_control' => 10, 'mobility' => 6, 'lane_control' => 5]);
        Hero::create(['hero_id' => 'h_mid',    'hero_name' => 'Pharsa',   'lane_1' => 'Mid Lane', 'lane_2' => null,       'sustainability' => 3, 'attack_power' => 9, 'crowd_control' => 5,  'mobility' => 6, 'lane_control' => 9]);
        Hero::create(['hero_id' => 'h_gold',   'hero_name' => 'Claude',   'lane_1' => 'Gold Lane','lane_2' => null,       'sustainability' => 3, 'attack_power' => 9, 'crowd_control' => 2,  'mobility' => 8, 'lane_control' => 8]);
        Hero::create(['hero_id' => 'h_jgl',    'hero_name' => 'Fanny',    'lane_1' => 'Jungle',   'lane_2' => null,       'sustainability' => 4, 'attack_power' => 9, 'crowd_control' => 2,  'mobility' => 10,'lane_control' => 6]);
        Hero::create(['hero_id' => 'h_flex',   'hero_name' => 'Paquito',  'lane_1' => 'EXP Lane', 'lane_2' => 'Jungle',   'sustainability' => 7, 'attack_power' => 8, 'crowd_control' => 6,  'mobility' => 8, 'lane_control' => 7]);

        // Skenario A: Draft awal kosong (0 ally picks)
        // Semua 5 lane dibutuhkan, semua kandidat hero lolos filter
        $emptyResult = $this->service->recommend([], [], [], 'Alter Ego', 10);
        $this->assertSame($expectedLanes, $emptyResult['needed_lanes']);
        $this->assertEmpty($emptyResult['filled_lanes']);
        $this->assertCount(6, $emptyResult['recommendations']);

        // Skenario B: Ally sudah pick Tigreal (lane_1: Roaming)
        // Roaming sudah terisi -> needed_lanes tersisa ['EXP Lane', 'Jungle', 'Mid Lane', 'Gold Lane']
        // Khufra (hanya Roaming) harus TERFILTER / TIDAK LOLOS
        // Pharsa (Mid Lane), Claude (Gold Lane), Fanny (Jungle), Paquito (EXP Lane / Jungle) HARUS LOLOS
        $roamPickedResult = $this->service->recommend(['Tigreal'], [], [], 'Alter Ego', 10);
        $this->assertContains('Roaming', $roamPickedResult['filled_lanes']);
        $this->assertNotContains('Roaming', $roamPickedResult['needed_lanes']);
        $this->assertCount(4, $roamPickedResult['needed_lanes']);

        $recNames = array_column($roamPickedResult['recommendations'], 'hero');
        $this->assertNotContains('Khufra', $recNames, 'Khufra harus terfilter karena lane Roaming sudah terisi oleh Tigreal');
        $this->assertContains('Pharsa', $recNames);
        $this->assertContains('Claude', $recNames);
        $this->assertContains('Fanny', $recNames);
        $this->assertContains('Paquito', $recNames);

        // Skenario C: Skenario Fallback Keamanan jika semua 5 hero sudah di-pick
        $fiveAllyResult = $this->service->recommend(['Tigreal', 'Pharsa', 'Claude', 'Fanny', 'Paquito'], [], [], 'Alter Ego', 10);
        $this->assertEmpty($fiveAllyResult['needed_lanes']);
        // Khufra tetap bisa muncul sebagai fallback daripada array kosong
        $fiveRecNames = array_column($fiveAllyResult['recommendations'], 'hero');
        $this->assertContains('Khufra', $fiveRecNames);
    }
}

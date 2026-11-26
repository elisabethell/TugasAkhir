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

        $this->assertCount(9, $activeDraft['recommendations']); // 12 total - 3 excluded = 9
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
}

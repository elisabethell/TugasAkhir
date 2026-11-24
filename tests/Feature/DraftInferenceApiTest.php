<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MatchDataset;
use App\Models\MatchGame;
use App\Services\TeamDraftDnaService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DraftInferenceApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        TeamDraftDnaService::clearCache();

        // Buat data sampel match MPL ID S18 untuk pengujian Draft DNA
        $match = MatchDataset::create([
            'match_id'     => 'm_ae_rrq',
            'team_1_name'  => 'Alter Ego',
            'team_2_name'  => 'RRQ',
            'team_1_score' => 2,
            'team_2_score' => 1,
            'stage'        => 'Regular Season',
            'match_date'   => now(),
        ]);

        $match->games()->createMany([
            [
                'match_id'      => $match->match_id,
                'game_number'   => 1,
                'team_1_picks'  => ['Paquito', 'Zhuxin', 'Belerick', 'Claude', 'Nolan'],
                'team_2_picks'  => ['Hirara', 'Aulus', 'Atlas', 'Freya', 'Gloo'],
                'team_1_bans'   => ['Fanny', 'Ling'],
                'team_2_bans'   => ['Hayabusa', 'Valentina'],
                'team_1_status' => 'W',
                'team_2_status' => 'L',
            ],
            [
                'match_id'      => $match->match_id,
                'game_number'   => 2,
                'team_1_picks'  => ['Paquito', 'Zhuxin', 'Belerick', 'Claude', 'Nolan'],
                'team_2_picks'  => ['Hirara', 'Aulus', 'Atlas', 'Freya', 'Gloo'],
                'team_1_bans'   => ['Fanny', 'Ling'],
                'team_2_bans'   => ['Hayabusa', 'Valentina'],
                'team_1_status' => 'W',
                'team_2_status' => 'L',
            ],
            [
                'match_id'      => $match->match_id,
                'game_number'   => 3,
                'team_1_picks'  => ['Paquito', 'Zhuxin', 'Belerick', 'Claude', 'Nolan'],
                'team_2_picks'  => ['Hirara', 'Aulus', 'Atlas', 'Freya', 'Gloo'],
                'team_1_bans'   => ['Fanny', 'Ling'],
                'team_2_bans'   => ['Hayabusa', 'Valentina'],
                'team_1_status' => 'L',
                'team_2_status' => 'W',
            ],
        ]);

        $match2 = MatchDataset::create([
            'match_id'     => 'm_onic_rrq',
            'team_1_name'  => 'ONIC',
            'team_2_name'  => 'RRQ',
            'team_1_score' => 2,
            'team_2_score' => 0,
            'stage'        => 'Playoffs',
            'match_date'   => now(),
        ]);
        $match2->games()->create([
            'match_id'      => $match2->match_id,
            'game_number'   => 1,
            'team_1_picks'  => ['Ling', 'Angela', 'Fredrinn', 'Claude', 'Pharsa'],
            'team_2_picks'  => ['Hirara', 'Paquito', 'Atlas', 'Zhuxin', 'Aulus'],
            'team_1_bans'   => ['Fanny', 'Gloo'],
            'team_2_bans'   => ['Hayabusa', 'Valentina'],
            'team_1_status' => 'W',
            'team_2_status' => 'L',
        ]);
    }
    /**
     * Test endpoint web page /rekomendasi-draft status 200
     */
    public function test_rekomendasi_draft_page_renders_successfully(): void
    {
        $response = $this->get('/rekomendasi-draft');
        $response->assertStatus(200);
        $response->assertSee('Rekomendasi Sistem Pakar');
        $response->assertSee('Engine RBR: Aktif');
        $response->assertSee('Rekomendasi Pick');
        $response->assertSee('Rekomendasi Ban');
        $response->assertSee('Jejak Inferensi');
        $response->assertSee('inferenceTraceModal');
        $response->assertSee('recLoadingBar');
        $response->assertSee('Basis data:');
        $response->assertSee('MPL ID S18');
        $response->assertSee('game');
        $response->assertSee('Diperbarui');
        $response->assertSee('Breakdown Skor Hybrid');
        $response->assertSee('powerSpikeComparisonSummary');
        $response->assertSee('allyCompositionAlerts');
        $response->assertSee('enemyCompositionAlerts');
        $response->assertSee('Belum ada data');
        $response->assertSee('Skenario Demo');
        $response->assertSee('allyIdentityBadge');
        $response->assertSee('enemyIdentityBadge');
        $response->assertSee('allyWinConditionText');
        $response->assertSee('enemyCounterAdviceText');
        $response->assertSee('heroPickerCard');
        $response->assertDontSeeText('undefined');
        $response->assertDontSee('OP-01 PICKS');
        $response->assertDontSee('OP-02 BANS');
        $response->assertDontSee('Early: 0');
        $response->assertDontSee('Mid: 0');
        $response->assertDontSee('Late: 0');
    }

    /**
     * Test endpoint API POST /api/analyze-draft returns required contract
     */
    public function test_analyze_draft_api_returns_structured_inference_contract(): void
    {
        $payload = [
            'my_team'     => ['Tigreal', 'Harith'],
            'enemy_team'  => ['Ling', 'Fredrinn'],
            'banned'      => ['Fanny', 'Gloo'],
            'my_lanes'    => ['ROAM', 'MID'],
            'enemy_lanes' => ['JGL', 'EXP'],
        ];

        $response = $this->postJson('/api/analyze-draft', $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'working_memory' => [
                'ally_heroes',
                'enemy_heroes',
                'banned_heroes',
                'ally_damage',
                'enemy_damage',
                'ally_power_spikes',
            ],
            'rule_evaluations',
            'composition_alerts' => [
                'ally',
                'enemy',
            ],
            'power_spikes' => [
                'ally',
                'enemy',
                'comparison',
            ],
            'recommendations' => [
                '*' => [
                    'hero_id',
                    'nama',
                    'role',
                    'lane_flex',
                    'total_score',
                    'confidence',
                    'sample_size',
                    'low_data',
                    'breakdown' => [
                        'data' => ['score', 'win_rate', 'n', 'synergy', 'counter'],
                        'rule' => ['score', 'fired', 'not_fired'],
                    ],
                    'alasan_ringkas',
                ]
            ]
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['recommendations']);
        $first = $data['recommendations'][0];

        // Ensure picked and banned heroes are excluded
        $recNames = array_column($data['recommendations'], 'nama');
        $this->assertNotContains('Tigreal', $recNames);
        $this->assertNotContains('Harith', $recNames);
        $this->assertNotContains('Ling', $recNames);
        $this->assertNotContains('Fredrinn', $recNames);
        $this->assertNotContains('Fanny', $recNames);
        $this->assertNotContains('Gloo', $recNames);

        // Ensure confidence has % format
        $this->assertMatchesRegularExpression('/^[0-9]+(\.[0-9]+)?%$/', $first['confidence']);
    }

    /**
     * Test endpoint API GET /api/teams/{team}/dna mengembalikan profil Draft DNA tim lengkap
     */
    public function test_get_team_draft_dna_endpoint_returns_expected_structure_and_stats(): void
    {
        // 1. Uji akses spesifik tim Alter Ego (termasuk alias case-insensitive 'ae')
        $response = $this->getJson('/api/teams/Alter%20Ego/dna');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'team_name',
                'team_logo',
                'total_games',
                'wins',
                'losses',
                'win_rate',
                'top_heroes' => [
                    '*' => ['hero', 'picks', 'wins', 'losses', 'win_rate', 'pick_rate', 'portrait', 'role']
                ],
                'top_bans' => [
                    '*' => ['hero', 'bans', 'ban_rate', 'portrait']
                ],
                'playstyle_tag',
                'playstyle_frequency',
                'playstyle_description',
                'composition_breakdown',
            ]
        ]);

        $data = $response->json('data');
        $this->assertEquals('Alter Ego', $data['team_name']);
        $this->assertGreaterThan(0, $data['total_games']);
        $this->assertNotEmpty($data['top_heroes']);
        $this->assertLessThanOrEqual(8, count($data['top_heroes']));
        $this->assertNotEmpty($data['top_bans']);
        $this->assertLessThanOrEqual(5, count($data['top_bans']));
        $this->assertNotEmpty($data['playstyle_tag']);

        // 2. Uji menggunakan alias huruf kecil 'ae'
        $responseAlias = $this->getJson('/api/teams/ae/dna');
        $responseAlias->assertStatus(200);
        $this->assertEquals('Alter Ego', $responseAlias->json('data.team_name'));

        // 3. Uji tim yang tidak ada
        $responseNotFound = $this->getJson('/api/teams/TimKhayalan/dna');
        $responseNotFound->assertStatus(404);
        $responseNotFound->assertJson(['status' => 'error']);
    }

    /**
     * Test endpoint API GET /api/team-dna mengembalikan daftar ringkasan seluruh tim MPL ID S18
     */
    public function test_get_all_teams_dna_list_endpoint(): void
    {
        $response = $this->getJson('/api/team-dna');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'teams' => [
                '*' => ['team_name', 'team_logo', 'total_games', 'win_rate', 'playstyle_tag', 'top_hero_names']
            ]
        ]);

        $teams = $response->json('teams');
        $this->assertNotEmpty($teams);
        $teamNames = array_column($teams, 'team_name');
        $this->assertContains('Alter Ego', $teamNames);
        $this->assertContains('RRQ', $teamNames);
        $this->assertContains('ONIC', $teamNames);
    }

    /**
     * Test POST /api/analyze-draft dengan parameter team_name:
     * - Mengembalikan objek 'team_dna' di root response dan tiap rekomendasi
     * - Menghitung smoothed WR & synergy/counter tim
     * - Fallback MPL overall jika musuh unfaced (n=0) dengan prioritas hero pool
     */
    public function test_analyze_draft_with_team_name_incorporates_team_draft_dna_and_fallback(): void
    {
        $payload = [
            'team_name'   => 'Alter Ego',
            'my_team'     => ['Paquito'],
            'enemy_team'  => ['Layla'], // Layla tidak pernah dihadapi di MPL S18 (n = 0)
            'banned'      => ['Fanny'],
            'my_lanes'    => ['EXP'],
            'enemy_lanes' => ['GOLD'],
        ];

        $response = $this->postJson('/api/analyze-draft', $payload);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertNotNull($json['team_dna'], 'Response harus memuat profil team_dna');
        $this->assertEquals('Alter Ego', $json['team_dna']['team_name']);
        $this->assertNotEmpty($json['team_dna']['top_heroes']);
        $this->assertNotEmpty($json['team_dna']['playstyle_tag']);

        // Pastikan rekomendasi memiliki metadata team_dna
        $recs = $json['recommendations'];
        $this->assertNotEmpty($recs);
        $this->assertArrayHasKey('team_dna', $recs[0]);

        // Cari hero yang masuk dalam top_heroes Alter Ego (misal Zhuxin, Hirara, atau Paquito)
        $topPoolHero = null;
        foreach ($recs as $r) {
            if (!empty($r['team_dna']['in_hero_pool'])) {
                $topPoolHero = $r;
                break;
            }
        }
        $this->assertNotNull($topPoolHero, 'Harus ada hero rekomendasi yang masuk dalam Hero Pool Alter Ego');
        $this->assertTrue($topPoolHero['team_dna']['in_hero_pool']);

        // Verifikasi mekanisme fallback: periksa apakah ada counter yang menggunakan fallback dengan priority
        $hasHeroPoolFallback = false;
        foreach ($recs as $r) {
            foreach ($r['breakdown']['data']['counter'] as $ctr) {
                if (($ctr['source'] ?? '') === 'hero_pool_comfort_fallback' || ($ctr['source'] ?? '') === 'overall_meta_fallback_hero_pool') {
                    $hasHeroPoolFallback = true;
                    break 2;
                }
            }
        }
        $this->assertTrue($hasHeroPoolFallback, 'Mekanisme fallback harus memprioritaskan hero pool saat menghadapi unfaced enemy');
    }

    /**
     * Test 9 aturan Rule-Based Reasoning (RBR) tetap berjalan normal saat team_name diisi
     * (Misal draft monotype physical harus tetap menyalakan RBR-COMP-01 untuk Mage)
     */
    public function test_rbr_rules_continue_to_fire_normally_when_team_name_is_specified(): void
    {
        $payload = [
            'team_name'   => 'Alter Ego',
            'my_team'     => ['Paquito', 'Bruno', 'Chou'], // 100% physical damage
            'enemy_team'  => ['Tigreal'],
            'banned'      => [],
        ];

        $response = $this->postJson('/api/analyze-draft', $payload);
        $response->assertStatus(200);

        $json = $response->json();

        // 1. RBR-COMP-01 (Mage Wajib) harus tetap fired
        $this->assertTrue(
            $json['rule_evaluations']['RBR-COMP-01']['fired'],
            'RBR-COMP-01 harus tetap fired meskipun ada parameter team_name'
        );

        // 2. Rekomendasi teratas harus mengutamakan hero Magic damage untuk menjaga komposisi tim
        $topRec = $json['recommendations'][0];
        $isMagic = ($topRec['damage_type'] === 'Magic' || strcasecmp($topRec['role'], 'Mage') === 0);
        $this->assertTrue(
            $isMagic,
            "Rekomendasi teratas ({$topRec['nama']}) harus Magic demi keseimbangan komposisi tim (RBR-COMP-01)"
        );
        $this->assertStringContainsString('Aturan Komposisi Mutlak', $topRec['alasan_ringkas']);
    }

    /**
     * Test DraftRecommendationService dapat digunakan langsung dan mewarisi fungsionalitas DraftInferenceService
     */
    public function test_draft_recommendation_service_inherits_and_exposes_dna_methods(): void
    {
        $service = app(\App\Services\DraftRecommendationService::class);
        $dna = $service->getTeamDraftDna('Alter Ego');

        $this->assertNotNull($dna);
        $this->assertEquals('Alter Ego', $dna['team_name']);
        $this->assertGreaterThan(0, $dna['total_games']);
        $this->assertNotEmpty($dna['top_heroes']);
        $this->assertNotEmpty($dna['top_bans']);

        $allDna = $service->getAllTeamsDna();
        $this->assertNotEmpty($allDna);
    }
}

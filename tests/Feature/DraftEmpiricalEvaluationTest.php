<?php

namespace Tests\Feature;

use App\Models\MatchDataset;
use App\Models\MatchGame;
use App\Services\TeamDraftDnaService;
use App\Services\DraftRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftEmpiricalEvaluationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Artisan Command: draft:evaluate menjalankan Leave-One-Out Backtesting dengan sukses
     */
    public function test_artisan_draft_evaluate_command_runs_successfully(): void
    {
        $dataset = MatchDataset::create([
            'match_id'      => 'm_eval_1',
            'team_1_name'   => 'Alter Ego',
            'team_2_name'   => 'ONIC',
            'team_1_score'  => 2,
            'team_2_score'  => 0,
            'stage_name'    => 'Regular Season',
        ]);

        MatchGame::create([
            'match_dataset_id' => $dataset->id,
            'match_id'         => 'm_eval_1',
            'game_number'      => 1,
            'team_1_picks'     => ['Paquito', 'Ling', 'Zhuxin', 'Bruno', 'Tigreal'],
            'team_2_picks'     => ['Phoveus', 'Nolan', 'Pharsa', 'Claude', 'Hylos'],
            'team_1_bans'      => ['Fanny', 'Harith'],
            'team_2_bans'      => ['Hayabusa', 'Gloo'],
            'team_1_status'    => 'W',
            'team_2_status'    => 'L',
        ]);

        MatchGame::create([
            'match_dataset_id' => $dataset->id,
            'match_id'         => 'm_eval_1',
            'game_number'      => 2,
            'team_1_picks'     => ['Chou', 'Suyou', 'Valentina', 'Beatrix', 'Belerick'],
            'team_2_picks'     => ['Terizla', 'Baxia', 'Yve', 'Karrie', 'Mathilda'],
            'team_1_bans'      => ['Fanny', 'Harith'],
            'team_2_bans'      => ['Hayabusa', 'Zhuxin'],
            'team_1_status'    => 'W',
            'team_2_status'    => 'L',
        ]);

        $this->artisan('draft:evaluate', ['--limit' => 2])
            ->expectsOutputToContain('METASCOUT : LAND OF DAWN')
            ->expectsOutputToContain('HASIL EVALUASI HISTORICAL BACKTESTING')
            ->expectsOutputToContain('Top-1 Hit Rate (HR@1)')
            ->expectsOutputToContain('Top-3 Hit Rate (HR@3)')
            ->expectsOutputToContain('Top-5 Hit Rate (HR@5)')
            ->expectsOutputToContain('Top-10 Hit Rate (HR@10)')
            ->expectsOutputToContain('Mean Reciprocal Rank (MRR)')
            ->assertExitCode(0);
    }

    /**
     * Test Jaccard Similarity Coefficient pada getSimilarMatches: J(A, B) = |A ∩ B| / |A ∪ B|
     */
    public function test_get_similar_matches_calculates_jaccard_similarity_coefficient(): void
    {
        $dataset = MatchDataset::create([
            'match_id'      => 'm_jaccard_1',
            'team_1_name'   => 'Alter Ego',
            'team_2_name'   => 'RRQ',
            'team_1_score'  => 1,
            'team_2_score'  => 0,
            'stage_name'    => 'Regular Season',
        ]);

        // Game 1: 3 hero sama dari 4 hero simulator -> Intersection = 3, Simulator = 4, Game = 5, Union = 6 -> J = 3/6 = 50.0%
        MatchGame::create([
            'match_dataset_id' => $dataset->id,
            'match_id'         => 'm_jaccard_1',
            'game_number'      => 1,
            'team_1_picks'     => ['Paquito', 'Bruno', 'Ling', 'Pharsa', 'Tigreal'],
            'team_2_picks'     => ['Terizla', 'Claude', 'Fanny', 'Yve', 'Hylos'],
            'team_1_status'    => 'W',
            'team_2_status'    => 'L',
        ]);

        // Game 2: 1 hero sama dari 4 hero simulator -> Intersection = 1, Simulator = 4, Game = 5, Union = 8 -> J = 1/8 = 12.5%
        MatchGame::create([
            'match_dataset_id' => $dataset->id,
            'match_id'         => 'm_jaccard_1',
            'game_number'      => 2,
            'team_1_picks'     => ['Paquito', 'Beatrix', 'Baxia', 'Valentina', 'Gloo'],
            'team_2_picks'     => ['Chou', 'Moskov', 'Hayabusa', 'Cecilion', 'Kaja'],
            'team_1_status'    => 'W',
            'team_2_status'    => 'L',
        ]);

        $service = app(TeamDraftDnaService::class);
        $simulatorPicks = ['Paquito', 'Bruno', 'Ling', 'Zhuxin']; // 4 picks

        $matches = $service->getSimilarMatches($simulatorPicks, 'Alter Ego', 2);

        $this->assertCount(2, $matches);

        // Verifikasi Game 1 memiliki Jaccard tertinggi (peringkat pertama)
        $topMatch = $matches[0];
        $this->assertEquals(1, $topMatch['game_number']);
        $this->assertEquals(3, $topMatch['matched_count']); // Paquito, Bruno, Ling
        // Union: {Paquito, Bruno, Ling, Zhuxin, Pharsa, Tigreal} = 6 hero -> 3 / 6 = 0.5 (50.0%)
        $this->assertEqualsWithDelta(0.5, $topMatch['jaccard_similarity'], 0.001);
        $this->assertEquals('50%', $topMatch['similarity_percentage']);
        $this->assertStringContainsString('Kemiripan 50%', $topMatch['matched_label']);

        // Verifikasi Game 2 memiliki Jaccard lebih rendah (peringkat kedua)
        $secondMatch = $matches[1];
        $this->assertEquals(2, $secondMatch['game_number']);
        $this->assertEquals(1, $secondMatch['matched_count']); // Paquito
        // Union: {Paquito, Bruno, Ling, Zhuxin, Beatrix, Baxia, Valentina, Gloo} = 8 hero -> 1 / 8 = 0.125 (12.5%)
        $this->assertEqualsWithDelta(0.125, $secondMatch['jaccard_similarity'], 0.001);
        $this->assertEquals('12.5%', $secondMatch['similarity_percentage']);
        $this->assertStringContainsString('Kemiripan 12.5%', $secondMatch['matched_label']);

        // Verifikasi urutan Jaccard descending
        $this->assertGreaterThan($secondMatch['jaccard_similarity'], $topMatch['jaccard_similarity']);
    }

    /**
     * Test endpoint /api/analyze-draft konsisten menggunakan DraftRecommendationService (SAW + Bayesian + Empty Lane)
     */
    public function test_api_analyze_draft_returns_saw_recommendations_with_empty_lane_filtering(): void
    {
        $payload = [
            'team_name'   => 'Alter Ego',
            'my_team'     => ['Paquito'], // EXP Lane sudah terisi
            'enemy_team'  => ['Tigreal'],
            'banned'      => ['Ling'],
        ];

        $response = $this->postJson('/api/analyze-draft', $payload);
        $response->assertStatus(200);

        $json = $response->json();

        // 1. Verifikasi field SAW meta dan needed lanes tersedia
        $this->assertArrayHasKey('needed_lanes', $json);
        $this->assertArrayHasKey('filled_lanes', $json);
        $this->assertArrayHasKey('saw_meta', $json);
        $this->assertArrayHasKey('saw_recommendations', $json);

        // EXP Lane harus masuk dalam filled_lanes
        $this->assertContains('EXP Lane', $json['filled_lanes']);

        // needed_lanes tidak boleh memuat EXP Lane
        $this->assertNotContains('EXP Lane', $json['needed_lanes']);

        // 2. Rekomendasi di recommendations harus memiliki skor SAW dan coach_attributes
        $recs = $json['recommendations'];
        $this->assertNotEmpty($recs);

        $firstRec = $recs[0];
        $this->assertArrayHasKey('saw_score', $firstRec);
        $this->assertArrayHasKey('s_dna', $firstRec);
        $this->assertArrayHasKey('s_comp', $firstRec);
        $this->assertArrayHasKey('s_counter', $firstRec);
        $this->assertArrayHasKey('coach_attributes', $firstRec);

        // 3. Hero ter-ban tidak boleh ada di rekomendasi
        $recNames = array_column($recs, 'nama');
        $this->assertNotContains('Ling', $recNames);
        $this->assertNotContains('Paquito', $recNames);
        $this->assertNotContains('Tigreal', $recNames);
    }
}

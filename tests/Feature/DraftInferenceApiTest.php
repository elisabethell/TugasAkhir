<?php

namespace Tests\Feature;

use Tests\TestCase;

class DraftInferenceApiTest extends TestCase
{
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
}

<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\DraftInferenceService;
use App\Services\CsvDataService;

class DraftInferenceServiceTest extends TestCase
{
    protected DraftInferenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DraftInferenceService::class);
    }

    /**
     * Test 1: Draft kosong (0 hero)
     * - Tidak boleh mengklaim synergy atau counter.
     * - Breakdown synergy = [] dan counter = [].
     * - Alasan ringkas jujur: "Prioritas awal (meta S18)..."
     */
    public function test_draft_kosong_hanya_menggunakan_sinyal_meta_awal_tanpa_klaim_synergy_counter(): void
    {
        $result = $this->service->evaluateDraft(
            allyTeam: [],
            enemyTeam: [],
            bannedHeroes: []
        );

        $this->assertNotEmpty($result['recommendations']);
        $firstRec = $result['recommendations'][0];

        // Sinergi dan counter harus kosong pada draft kosong
        $this->assertEmpty($firstRec['breakdown']['data']['synergy']);
        $this->assertEmpty($firstRec['breakdown']['data']['counter']);

        // Alasan ringkas harus mencantumkan prioritas awal meta S18
        $this->assertStringContainsString('Prioritas awal (meta S18)', $firstRec['alasan_ringkas']);

        // Rule generic turnamen fired, rule synergy & counter tidak fired
        $this->assertTrue($result['rule_evaluations']['RBR-GEN-01']['fired']);
        $this->assertFalse($result['rule_evaluations']['RBR-SYN-01']['fired']);
        $this->assertFalse($result['rule_evaluations']['RBR-CTR-01']['fired']);
    }

    /**
     * Test 2: Draft dengan 1 sekutu
     * - Hero sekutu terpilih harus dikecualikan dari daftar rekomendasi.
     * - Rule synergy sekutu dapat dievaluasi.
     */
    public function test_draft_dengan_satu_sekutu_mengecualikan_hero_dan_mengevaluasi_sinergi(): void
    {
        $allyHero = 'Tigreal';
        $result = $this->service->evaluateDraft(
            allyTeam: [$allyHero],
            enemyTeam: [],
            bannedHeroes: []
        );

        // Tigreal tidak boleh ada di daftar rekomendasi
        $candidateNames = array_column($result['recommendations'], 'nama');
        $this->assertNotContains($allyHero, $candidateNames);

        // Rule sinergi wombo-combo area (RBR-SYN-02) harus fired karena Tigreal adalah inisiator AoE CC
        $this->assertTrue($result['rule_evaluations']['RBR-SYN-02']['fired']);
    }

    /**
     * Test 3: Draft dengan damage 100% physical
     * - Harus menyalakan rule keseimbangan damage (RBR-COMP-01).
     * - Prioritas rekomendasi teratas harus merupakan hero ber-damage Magic / Mage.
     */
    public function test_draft_monotype_physical_menyalakan_rule_keseimbangan_damage(): void
    {
        // 3 hero sekutu semuanya physical
        $allyTeam = ['Tigreal', 'Bruno', 'Ling'];
        $result = $this->service->evaluateDraft(
            allyTeam: $allyTeam,
            enemyTeam: [],
            bannedHeroes: []
        );

        // Rule RBR-COMP-01 (Mage Wajib) harus fired
        $this->assertTrue(
            $result['rule_evaluations']['RBR-COMP-01']['fired'],
            'RBR-COMP-01 harus menyala saat draft 100% physical'
        );

        // Rekomendasi teratas (#1) harus bertipe damage Magic atau role Mage
        $topRec = $result['recommendations'][0];
        $isMagic = ($topRec['damage_type'] === 'Magic' || strcasecmp($topRec['role'], 'Mage') === 0);
        $this->assertTrue(
            $isMagic,
            "Rekomendasi teratas ({$topRec['nama']}) harus bertipe Magic saat terjadi defisit magical damage."
        );
        $this->assertStringContainsString('Aturan Komposisi Mutlak', $topRec['alasan_ringkas']);
    }

    /**
     * Test 4: Hero ter-ban tidak boleh muncul dalam rekomendasi
     */
    public function test_hero_ter_ban_tidak_muncul_dalam_rekomendasi(): void
    {
        $bannedHeroes = ['Yve', 'Harith', 'Gloo'];
        $result = $this->service->evaluateDraft(
            allyTeam: ['Tigreal'],
            enemyTeam: ['Ling'],
            bannedHeroes: $bannedHeroes
        );

        $candidateNames = array_column($result['recommendations'], 'nama');
        foreach ($bannedHeroes as $banned) {
            $this->assertNotContains(
                $banned,
                $candidateNames,
                "Hero yang di-ban ({$banned}) tidak boleh muncul dalam kandidat rekomendasi."
            );
        }
    }

    /**
     * Test 5: Dua hero berbeda tidak boleh menghasilkan confidence identik pada input berbeda
     */
    public function test_dua_hero_berbeda_tidak_menghasilkan_confidence_identik_pada_input_berbeda(): void
    {
        $result = $this->service->evaluateDraft(
            allyTeam: ['Tigreal', 'Angela'],
            enemyTeam: ['Ling', 'Fredrinn'],
            bannedHeroes: []
        );

        $recs = $result['recommendations'];
        $this->assertGreaterThanOrEqual(10, count($recs));

        // Kumpulkan nilai confidence dari 10 hero teratas
        $confidences = array_column(array_slice($recs, 0, 10), 'confidence');
        $uniqueConfidences = array_unique($confidences);

        // Harus ada variasi matematis pada confidence, tidak boleh seluruh hero memiliki confidence yang sama
        $this->assertGreaterThan(
            1,
            count($uniqueConfidences),
            'Confidence harus dinamis dan bervariasi berdasarkan sample size turnamen (n) dan kekuatan rule yang menyala.'
        );
    }

    /**
     * Test 6: Evaluasi alert komposisi menghasilkan rule_id dan severity (critical/warning/info)
     */
    public function test_evaluasi_alert_komposisi_menghasilkan_rule_id_dan_severity(): void
    {
        // 3 hero marksman/assassin physical tanpa Mage dan tanpa Frontline
        $allyTeam = ['Bruno', 'Ling', 'Beatrix'];
        $result = $this->service->evaluateDraft(
            allyTeam: $allyTeam,
            enemyTeam: ['Tigreal', 'Harith'],
            bannedHeroes: []
        );

        $this->assertArrayHasKey('composition_alerts', $result);
        $this->assertArrayHasKey('ally', $result['composition_alerts']);
        $this->assertArrayHasKey('enemy', $result['composition_alerts']);

        $allyAlerts = $result['composition_alerts']['ally'];
        $this->assertNotEmpty($allyAlerts);

        // Cari alert defisit Magic (RBR-COMP-01)
        $comp01 = null;
        foreach ($allyAlerts as $a) {
            if ($a['rule_id'] === 'RBR-COMP-01') {
                $comp01 = $a;
                break;
            }
        }
        $this->assertNotNull($comp01, 'Alert RBR-COMP-01 harus dihasilkan saat tim tidak memiliki Mage.');
        $this->assertEquals('critical', $comp01['severity']);
        $this->assertStringContainsString('Belum ada Mage', $comp01['message']);

        // Cari alert defisit Frontline (RBR-COMP-02)
        $comp02 = null;
        foreach ($allyAlerts as $a) {
            if ($a['rule_id'] === 'RBR-COMP-02') {
                $comp02 = $a;
                break;
            }
        }
        $this->assertNotNull($comp02, 'Alert RBR-COMP-02 harus dihasilkan saat tim tidak memiliki frontliner.');
        $this->assertEquals('critical', $comp02['severity']);
        $this->assertStringContainsString('Tidak ada frontliner', $comp02['message']);
    }

    /**
     * Test 7: Interpretasi kurva power spike dan komparasi antar tim
     */
    public function test_power_spike_interpretasi_dan_komparasi_antar_tim(): void
    {
        // Draft kosong harus menghasilkan "Belum ada data"
        $emptyResult = $this->service->evaluateDraft([], []);
        $this->assertEquals('Belum ada data', $emptyResult['power_spikes']['ally']['text']);
        $this->assertEquals('Belum ada data', $emptyResult['power_spikes']['enemy']['text']);
        $this->assertStringContainsString('Menunggu pemilihan hero', $emptyResult['power_spikes']['comparison']);

        // Draft dengan hero: Tigreal (Early), Harith (Mid), Beatrix (Late)
        $result = $this->service->evaluateDraft(
            allyTeam: ['Tigreal', 'Harith'],
            enemyTeam: ['Ling', 'Beatrix']
        );

        $this->assertArrayHasKey('power_spikes', $result);
        $this->assertNotEquals('Belum ada data', $result['power_spikes']['ally']['text']);
        $this->assertNotEquals('Belum ada data', $result['power_spikes']['enemy']['text']);
        $this->assertStringContainsString('Kesimpulan:', $result['power_spikes']['comparison']);
    }
}

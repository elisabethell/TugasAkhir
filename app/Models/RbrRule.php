<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class RbrRule extends Model
{
    protected $primaryKey = 'id_rule';
    
    protected $fillable = [
        'rule_code',
        'category',
        'if_condition',
        'then_recommendation',
        'confidence_weight',
        'description',
        'kondisi_if',
        'kesimpulan_then',
        'status_aktif',
    ];

    protected $casts = [
        'confidence_weight' => 'float',
        'status_aktif'      => 'boolean',
    ];

    /**
     * Accessor untuk if_condition (kompatibel dengan kondisi_if lama)
     */
    public function getIfConditionAttribute($value)
    {
        return $value ?: $this->attributes['kondisi_if'] ?? '';
    }

    /**
     * Accessor untuk then_recommendation (kompatibel dengan kesimpulan_then lama)
     */
    public function getThenRecommendationAttribute($value)
    {
        return $value ?: $this->attributes['kesimpulan_then'] ?? '';
    }

    /**
     * Helper Seeder untuk memastikan tabel rbr_rules memiliki basis aturan akademik lengkap
     */
    public static function seedDefaultRules(): void
    {
        try {
            if (!Schema::hasTable('rbr_rules')) {
                return;
            }

            // Pastikan kolom-kolom baru tersedia jika migrasi belum dijalankan lewat CLI
            if (!Schema::hasColumn('rbr_rules', 'rule_code')) {
                Schema::table('rbr_rules', function ($table) {
                    $table->string('rule_code', 50)->nullable()->after('id_rule');
                    $table->string('category', 50)->default('Synergy')->after('rule_code');
                    $table->text('if_condition')->nullable()->after('category');
                    $table->text('then_recommendation')->nullable()->after('if_condition');
                    $table->decimal('confidence_weight', 4, 2)->default(0.85)->after('then_recommendation');
                    $table->text('description')->nullable()->after('confidence_weight');
                });
            }

            $legacyToSimpleCode = [
                'RBR-COMP-01' => 'R-001',
                'RBR-COMP-02' => 'R-002',
                'RBR-SYN-01'  => 'R-003',
                'RBR-SYN-02'  => 'R-004',
                'RBR-CTR-01'  => 'R-005',
                'RBR-CTR-02'  => 'R-006',
                'RBR-POW-01'  => 'R-007',
                'RBR-POW-02'  => 'R-008',
                'RBR-MAP-01'  => 'R-009',
                'RBR-MAP-02'  => 'R-010',
            ];

            $hasLegacy = self::where('rule_code', 'like', 'RBR-%')->exists();
            $hasAllClean = self::whereIn('rule_code', array_values($legacyToSimpleCode))->count() >= count($legacyToSimpleCode);
            if (!$hasLegacy && $hasAllClean) {
                return;
            }

            $defaultRules = [
                [
                    'rule_code'           => 'R-001',
                    'legacy_code'         => 'RBR-COMP-01',
                    'category'            => 'Composition',
                    'if_condition'        => 'Slot tim terisi >= 3 hero DAN total hero bertipe Magic Damage / Midlaner == 0 (atau rasio Magic Damage < 15%)',
                    'then_recommendation' => 'Prioritaskan hero bertipe Magic Damage / Midlaner dari Hero Pool tim terpilih (atau Overall Meta jika n=0) untuk menjaga keseimbangan komposisi damage.',
                    'confidence_weight'   => 0.98,
                    'description'         => 'Aturan Komposisi Mutlak (Balance Check): Mencegah draft 100% full-physical yang sangat rentan dicounter secara telak oleh item physical defense lawan.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'R-002',
                    'legacy_code'         => 'RBR-COMP-02',
                    'category'            => 'Composition',
                    'if_condition'        => 'Slot tim terisi >= 2 hero DAN jumlah hero Frontline / Tank berdurabilitas tinggi == 0',
                    'then_recommendation' => 'Prioritaskan hero Frontline / Tank dengan durabilitas tinggi dari Hero Pool tim terpilih untuk memperkuat inisiasi dan pertahanan tim.',
                    'confidence_weight'   => 0.94,
                    'description'         => 'Aturan Pertahanan Tim: Komposisi tanpa pelindung garis depan akan rentan tereliminasi cepat saat inisiasi war di area objektif Lord/Turtle.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'R-003',
                    'legacy_code'         => 'RBR-SYN-01',
                    'category'            => 'Synergy',
                    'if_condition'        => 'Tim memiliki Support Enabler / Healer DAN minimal 1 Marksman / Hypercarry Late-Scaling',
                    'then_recommendation' => 'Aktifkan strategi Protect the Hypercarry: prioritaskan hero pelindung garis depan atau enabler tambahan dari Hero Pool tim terpilih serta amankan ruang safe farming.',
                    'confidence_weight'   => 0.95,
                    'description'         => 'Sinergi Proteksi: Hero pendukung dan frontliner bertindak sebagai pembuat ruang serta pelindung carry utama hingga menyelesaikan core item.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'R-004',
                    'legacy_code'         => 'RBR-SYN-02',
                    'category'            => 'Synergy',
                    'if_condition'        => 'Tim memiliki inisiator Crowd Control Area (AoE CC) DAN hero Burst / Sustained Damage Area',
                    'then_recommendation' => 'Prioritaskan hero dengan skill Follow-up Burst Area atau CC berantai dari Hero Pool tim terpilih untuk mendominasi teamfight di lorong objektif Turtle/Lord.',
                    'confidence_weight'   => 0.92,
                    'description'         => 'Sinergi Rantai CC: Setup stun massal mengunci pergerakan lawan sekaligus agar dapat disapu bersih dengan damage area.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'R-005',
                    'legacy_code'         => 'RBR-CTR-01',
                    'category'            => 'Counter',
                    'if_condition'        => 'Tim musuh memilih hero bertipe Assassin / Marksman dengan mobilitas tinggi atau dash berantai',
                    'then_recommendation' => 'Prioritaskan hero berkarakteristik Hard Crowd Control, Anti-Dash, atau Suppression dari Hero Pool tim terpilih (atau Overall Meta jika n=0) untuk mengunci pergerakan lawan.',
                    'confidence_weight'   => 0.96,
                    'description'         => 'Aturan Penjinak Mobilitas: Efek anti-dash dan suppression membatalkan manuver skill pelarian serta dash lincah hero carry lawan seketika.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'R-006',
                    'legacy_code'         => 'RBR-CTR-02',
                    'category'            => 'Counter',
                    'if_condition'        => 'Tim musuh memilih hero Frontline / Tank dengan tingkat Sustain dan HP Regen tinggi',
                    'then_recommendation' => 'Prioritaskan hero bertipe True Damage, Max HP Shredder, atau Sustained DPS dari Hero Pool tim terpilih untuk menembus pertahanan lawan.',
                    'confidence_weight'   => 0.91,
                    'description'         => 'Aturan Penembus Armor: Serangan true damage dan persentase HP shredder efektif meruntuhkan penumpukan defense serta HP tebal lawan.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'R-007',
                    'legacy_code'         => 'RBR-POW-01',
                    'category'            => 'PowerSpike',
                    'if_condition'        => 'Komposisi tim didominasi hero bertipe Early Game Power Spike (0–5 menit) dengan potensi invasi awal tinggi',
                    'then_recommendation' => 'Prioritaskan hero tempo cepat / agresi early game dari Hero Pool tim terpilih untuk menekan jungle lawan serta mengamankan Turtle pertama.',
                    'confidence_weight'   => 0.89,
                    'description'         => 'Kurva Kekuatan Awal: Dominasi awal harus dikonversi menjadi keunggulan net worth dan objektif sebelum menit 10 agar lawan tidak sempat scaling.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'R-008',
                    'legacy_code'         => 'RBR-POW-02',
                    'category'            => 'PowerSpike',
                    'if_condition'        => 'Komposisi tim didominasi hero bertipe Late Game Scaling (12+ menit) yang bergantung pada kelengkapan core item',
                    'then_recommendation' => 'Prioritaskan hero penjaga tempo / wave clear defensif dari Hero Pool tim terpilih untuk mengawal scaling carry menuju fase Evolved Lord menit 12+.',
                    'confidence_weight'   => 0.90,
                    'description'         => 'Kurva Kekuatan Akhir: Komposisi unggul dalam pertempuran 5v5 di late game saat seluruh core equipment telah lengkap.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'R-009',
                    'legacy_code'         => 'RBR-MAP-01',
                    'category'            => 'Synergy',
                    'if_condition'        => 'Varian Map Turnamen memiliki mekanik penglihatan atau semak aktif (Revealing Wisps / Dangerous Grass)',
                    'then_recommendation' => 'Prioritaskan hero bertipe Vision Control, Poke Jarak Jauh, atau Bush Ambush dari Hero Pool tim terpilih untuk menguasai area river.',
                    'confidence_weight'   => 0.88,
                    'description'         => 'Sinergi Varian Map: Kontrol vision river dan semak lebat memperkuat taktik penyergapan serta pengamanan rotasi antarlane.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'R-010',
                    'legacy_code'         => 'RBR-MAP-02',
                    'category'            => 'Synergy',
                    'if_condition'        => 'Varian Map Turnamen memiliki mekanik struktur arena dinamis (Broken Walls / Golden Turret)',
                    'then_recommendation' => 'Prioritaskan hero dengan mobilitas lintas dinding atau tekanan objektif turret cepat dari Hero Pool tim terpilih untuk memaksimalkan rotasi map.',
                    'confidence_weight'   => 0.87,
                    'description'         => 'Sinergi Varian Map: Celah dinding terbuka dan bonus objektif turret mempercepat rotasi gank serta keunggulan gold di fase awal hingga menengah.',
                    'status_aktif'        => 1,
                ],
            ];

            foreach ($defaultRules as $r) {
                $existing = self::where('rule_code', $r['rule_code'])
                    ->orWhere('rule_code', $r['legacy_code'])
                    ->first();

                $payload = [
                    'rule_code'           => $r['rule_code'],
                    'category'            => $r['category'],
                    'if_condition'        => $r['if_condition'],
                    'then_recommendation' => $r['then_recommendation'],
                    'confidence_weight'   => $r['confidence_weight'],
                    'description'         => $r['description'],
                    'kondisi_if'          => $r['if_condition'],
                    'kesimpulan_then'     => $r['then_recommendation'],
                    'status_aktif'        => $r['status_aktif'],
                ];

                if ($existing) {
                    $existing->update($payload);
                } else {
                    self::create($payload);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('RbrRule seedDefaultRules warning: ' . $e->getMessage());
        }
    }
}

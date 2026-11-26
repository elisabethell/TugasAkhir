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

            $legacyCodes = [
                'RBR-COMP-01', 'RBR-COMP-02', 'RBR-SYN-01', 'RBR-SYN-02',
                'RBR-CTR-01', 'RBR-CTR-02', 'RBR-POW-01', 'RBR-POW-02',
                'RBR-MAP-01', 'RBR-MAP-02',
                'R-001', 'R-002', 'R-003', 'R-004', 'R-005',
                'R-006', 'R-007', 'R-008', 'R-009', 'R-010',
            ];

            $targetCodes = [
                'RULE-001', 'RULE-002', 'RULE-003', 'RULE-004', 'RULE-005',
                'RULE-006', 'RULE-007', 'RULE-008',
            ];

            $hasLegacy = self::whereIn('rule_code', $legacyCodes)->exists();
            $hasAllClean = self::whereIn('rule_code', $targetCodes)->count() >= count($targetCodes);
            if (!$hasLegacy && $hasAllClean) {
                return;
            }

            $defaultRules = [
                [
                    'rule_code'           => 'RULE-001',
                    'legacy_codes'        => ['RBR-COMP-01', 'R-001'],
                    'category'            => 'Composition',
                    'if_condition'        => 'Rata-rata atribut Crowd Control tim sekutu lebih rendah signifikan dari Target Profil pemenang T_k (selisih > 1.5 poin pada skala 0–10)',
                    'then_recommendation' => 'Tampilkan flag peringatan berbasis angka nyata (rata-rata Crowd Control tim vs target pemenang T_k) dan arahkan pemilihan hero ber-Crowd Control tinggi.',
                    'confidence_weight'   => 1.00,
                    'description'         => 'Aturan interpretasi defisit Crowd Control terhadap Target Profil T_k hasil shrinkage komposisi pemenang MPL ID S18.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RULE-002',
                    'legacy_codes'        => ['RBR-COMP-02', 'R-002'],
                    'category'            => 'Composition',
                    'if_condition'        => 'Rata-rata atribut Sustainability tim sekutu lebih rendah signifikan dari Target Profil pemenang T_k (selisih > 1.5 poin pada skala 0–10)',
                    'then_recommendation' => 'Tampilkan flag peringatan berbasis angka nyata (rata-rata Sustainability tim vs target pemenang T_k) dan sarankan tambahan hero frontline/sustain.',
                    'confidence_weight'   => 1.00,
                    'description'         => 'Aturan interpretasi ketahanan garis depan untuk memastikan komposisi mampu menahan burst dan mengamankan kontes objektif.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RULE-003',
                    'legacy_codes'        => ['RBR-CTR-01', 'R-005'],
                    'category'            => 'Composition',
                    'if_condition'        => 'Rata-rata atribut Attack Power, Mobility, atau Lane Control tim sekutu lebih rendah signifikan dari Target Profil pemenang T_k (selisih > 1.5 poin)',
                    'then_recommendation' => 'Tampilkan flag peringatan kuantitatif untuk atribut yang tertinggal agar komponen S_comp pada SAW menutup gap komposisi.',
                    'confidence_weight'   => 1.00,
                    'description'         => 'Aturan pengecekan keseimbangan damage, mobilitas rotasi, dan kontrol lane berbasis 5 atribut kuantitatif Coach.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RULE-004',
                    'legacy_codes'        => ['RBR-SYN-01', 'R-003'],
                    'category'            => 'Synergy',
                    'if_condition'        => '2 atribut tertinggi pada rata-rata komposisi tim sekutu adalah Sustainability dan Crowd Control',
                    'then_recommendation' => 'Tetapkan Draft Identity & Playstyle = "Teamfight / Front-to-Back" (fokus war 5v5 di area objektif Turtle/Lord).',
                    'confidence_weight'   => 1.00,
                    'description'         => 'Lapisan interpretasi strategi untuk komposisi dengan daya tahan dan kontrol kerumunan dominan.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RULE-005',
                    'legacy_codes'        => ['RBR-SYN-02', 'R-004'],
                    'category'            => 'Synergy',
                    'if_condition'        => '2 atribut tertinggi pada rata-rata komposisi tim sekutu adalah Mobility dan Attack Power',
                    'then_recommendation' => 'Tetapkan Draft Identity & Playstyle = "Pick-off / Skirmish" (rotasi cepat dan culik core lawan sebelum war 5v5 terbuka).',
                    'confidence_weight'   => 1.00,
                    'description'         => 'Lapisan interpretasi strategi untuk komposisi berkecepatan rotasi dan daya serang tinggi.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RULE-006',
                    'legacy_codes'        => ['RBR-CTR-02', 'R-006'],
                    'category'            => 'Synergy',
                    'if_condition'        => '2 atribut tertinggi pada rata-rata komposisi tim sekutu adalah Lane Control dan Sustainability',
                    'then_recommendation' => 'Tetapkan Draft Identity & Playstyle = "Macro / Split Push" (tarik-ulur lane, unggul kontrol map dan objektif turret).',
                    'confidence_weight'   => 1.00,
                    'description'         => 'Lapisan interpretasi strategi untuk komposisi pengendali gelombang minion dan permainan makro.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RULE-007',
                    'legacy_codes'        => ['RBR-POW-01', 'R-007'],
                    'category'            => 'Counter',
                    'if_condition'        => 'Gabungan rata-rata Crowd Control + Sustainability tim sekutu lebih unggul dari tim musuh',
                    'then_recommendation' => 'Winning Condition: Paksa teamfight 5v5 di area pit objektif dengan menyertakan perbandingan angka nyata kedua tim.',
                    'confidence_weight'   => 1.00,
                    'description'         => 'Aturan perbandingan kuantitatif komposisi Sekutu vs Musuh untuk menentukan rencana kemenangan teamfight.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RULE-008',
                    'legacy_codes'        => ['RBR-POW-02', 'R-008'],
                    'category'            => 'PowerSpike',
                    'if_condition'        => 'Rata-rata Lane Control tim sekutu lebih unggul dari tim musuh (atau Mobility + Attack Power lebih tinggi)',
                    'then_recommendation' => 'Winning Condition: Hindari war langsung dan manfaatkan keunggulan clear wave & split push (atau skirmish cepat jika unggul Mobility & Attack Power).',
                    'confidence_weight'   => 1.00,
                    'description'         => 'Aturan perbandingan kuantitatif Sekutu vs Musuh untuk strategi makro dan tempo pertandingan.',
                    'status_aktif'        => 1,
                ],
            ];

            // Hapus kode aturan usang yang tidak lagi dipakai agar hanya RULE-001..RULE-008 yang rapi
            self::whereIn('rule_code', ['RBR-MAP-01', 'RBR-MAP-02', 'R-009', 'R-010'])->delete();

            foreach ($defaultRules as $r) {
                $existing = self::where('rule_code', $r['rule_code'])
                    ->orWhereIn('rule_code', $r['legacy_codes'])
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

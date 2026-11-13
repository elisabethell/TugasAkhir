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

            if (self::count() > 0 && self::whereNotNull('rule_code')->count() >= 8) {
                return;
            }

            $defaultRules = [
                [
                    'rule_code'           => 'RBR-COMP-01',
                    'category'            => 'Composition',
                    'if_condition'        => 'Slot tim terisi >= 4 hero DAN total hero ber-role Mage / Midlaner == 0 (atau total Magic Damage < 1)',
                    'then_recommendation' => 'WAJIB PICK MAGE / MIDLANER (Yve, Pharsa, Valentina, Xavier, Zhuxin, Lylia, Vexana). Override prioritas teratas dengan Confidence 0.98',
                    'confidence_weight'   => 0.98,
                    'description'         => 'Aturan Komposisi Mutlak (Balance Check): Mencegah draft 100% full-physical yang sangat rentan dicounter secara telak oleh item physical defense lawan (Blade Armor & Antique Cuirass).',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RBR-COMP-02',
                    'category'            => 'Composition',
                    'if_condition'        => 'Skor Frontline Tim < 30 ATAU tidak ada satupun hero ber-role Tank / Sidelane tebal',
                    'then_recommendation' => 'Prioritaskan Hero Frontline / Tank Murni (Tigreal, Akai, Fredrinn, Grock, Belerick) pada slot berikutnya',
                    'confidence_weight'   => 0.94,
                    'description'         => 'Aturan Pertahanan Tim: Tim squishy tanpa badan depan akan rentan di-burst saat inisiasi war di area pit Lord/Turtle.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RBR-SYN-01',
                    'category'            => 'Synergy',
                    'if_condition'        => 'Tim memiliki Support Enabler / Healer (Floryn, Angela, Rafaela, Mathilda) DAN 1 Hard Marksman Carry',
                    'then_recommendation' => 'Aktifkan Strategi UBE (Ultimate Bonding Experience) / Protect the Hypercarry. Amankan posisi laning safe farming.',
                    'confidence_weight'   => 0.95,
                    'description'         => 'Sinergi Proteksi: 4 hero bertindak sebagai pembuat ruang dan pelindung carry utama hingga mencapai 3 core item.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RBR-SYN-02',
                    'category'            => 'Synergy',
                    'if_condition'        => 'Tim memiliki Setup Crowd Control Area (Tigreal, Atlas, Carmilla) DAN Hero Burst AoE (Pharsa, Odette, Vexana)',
                    'then_recommendation' => 'Kombinasi Area Wiping & Choke Point Trap di lorong sempit Turtle/Lord pit.',
                    'confidence_weight'   => 0.92,
                    'description'         => 'Sinergi Rantai CC: Setup stun massal mengunci musuh sekaligus untuk disapu bersih dengan damage area.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RBR-CTR-01',
                    'category'            => 'Counter',
                    'if_condition'        => 'Musuh memilih Assassin Mobilitas Tinggi / Dash Berantai (Fanny, Ling, Joy, Wanwan)',
                    'then_recommendation' => 'Prioritaskan Pick Hard Counter Anti-Dash & Suppression (Khufra, Kaja, Franco, Phoveus, Ruby).',
                    'confidence_weight'   => 0.96,
                    'description'         => 'Aturan Penjinak Assassin: Bouncing Ball Khufra dan Suppression Kaja/Franco membatalkan lintasan kabel baja atau dash lincah lawan seketika.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RBR-CTR-02',
                    'category'            => 'Counter',
                    'if_condition'        => 'Musuh memilih Tank Sustain / HP Regen Tinggi (Uranus, Baxia, Hylos, Gloo, Fredrinn)',
                    'then_recommendation' => 'Prioritaskan Hero Anti-Tank True Damage & Max HP Shredder (Karrie, Lunox, Claude, Dyrroth).',
                    'confidence_weight'   => 0.91,
                    'description'         => 'Aturan Penembus Armor: True damage dan persentase HP shredder menghancurkan penumpukan armor dan darah tebal lawan.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RBR-POW-01',
                    'category'            => 'PowerSpike',
                    'if_condition'        => 'Skor Early Game Tim >= 50% DAN memiliki hero invasi awal (Martis, Dyrroth, Hilda, Selena)',
                    'then_recommendation' => 'Fokus Strategi Snowball Menit 0-8: Rusak ritme buff jungler musuh dan amankan First Blood serta Turtle pertama.',
                    'confidence_weight'   => 0.89,
                    'description'         => 'Kurva Kekuatan Awal: Dominasi awal harus dikonversi menjadi keunggulan objektif sebelum menit 10 agar lawan tidak sempat scaling.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RBR-POW-02',
                    'category'            => 'PowerSpike',
                    'if_condition'        => 'Skor Late Game Tim >= 50% DAN memiliki hypercarry penskala item (Claude, Moskov, Cecilion, Karrie)',
                    'then_recommendation' => 'Fokus Laning Defensif & Macro Scaling: Hindari war berisiko di early game, kunci kemenangan lewat Lord pit menit 12+.',
                    'confidence_weight'   => 0.90,
                    'description'         => 'Kurva Kekuatan Akhir: Komposisi unggul dalam pertempuran 5v5 di late game saat seluruh core equipment telah lengkap.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RBR-MAP-01',
                    'category'            => 'Synergy',
                    'if_condition'        => 'Map Turnamen: Revealing Wisps ATAU Dangerous Grass',
                    'then_recommendation' => 'Prioritaskan Hero Vision Control & Ambush Bush (Hilda, Natalia, Chou, Selena, Mathilda).',
                    'confidence_weight'   => 0.88,
                    'description'         => 'Sinergi Varian Map: Semak lebat dan wisps river memperkuat taktik penyergapan serta kontrol rotasi antarlane.',
                    'status_aktif'        => 1,
                ],
                [
                    'rule_code'           => 'RBR-MAP-02',
                    'category'            => 'Synergy',
                    'if_condition'        => 'Map Turnamen: Broken Walls ATAU Golden Turret',
                    'then_recommendation' => 'Prioritaskan Hero Interaksi Dinding (Grock, Moskov, Badang, Fanny) dan Penekan Turret Cepat.',
                    'confidence_weight'   => 0.87,
                    'description'         => 'Sinergi Varian Map: Celah dinding terbuka melipatgandakan efek tabrakan tembok dan keuntungan gold turret awal.',
                    'status_aktif'        => 1,
                ],
            ];

            foreach ($defaultRules as $r) {
                self::updateOrCreate(
                    ['rule_code' => $r['rule_code']],
                    [
                        'category'            => $r['category'],
                        'if_condition'        => $r['if_condition'],
                        'then_recommendation' => $r['then_recommendation'],
                        'confidence_weight'   => $r['confidence_weight'],
                        'description'         => $r['description'],
                        'kondisi_if'          => $r['if_condition'],
                        'kesimpulan_then'     => $r['then_recommendation'],
                        'status_aktif'        => $r['status_aktif'],
                    ]
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('RbrRule seedDefaultRules warning: ' . $e->getMessage());
        }
    }
}

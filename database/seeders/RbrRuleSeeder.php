<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RbrRule;

class RbrRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'kondisi_if' => 'IF: Tim memiliki >= 2 hero Assassin / Single-Target Lock DAN minim AoE besar',
                'kesimpulan_then' => 'THEN: Archetype = "Pick-Off (Culik Lawan)". Prioritaskan hero eksekutor target tunggal dari Hero Pool tim terpilih dan kuasai vision bush sebelum objektif Turtle atau Lord.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki >= 2 hero AoE Crowd Control / Frontline Initiator DAN Mage Area Burst/Sustained',
                'kesimpulan_then' => 'THEN: Archetype = "Teamfight / AoE Wiping". Prioritaskan hero follow-up burst area dari Hero Pool tim terpilih dan pancing pertarungan di lorong sempit sekitar pit objektif.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki hero High Mobility / Fast Wave Clear di Side Lane',
                'kesimpulan_then' => 'THEN: Archetype = "Split Push & Macro Pressure". Prioritaskan hero penekan side lane berkecepatan tinggi dari Hero Pool tim terpilih sambil menahan mid lane dengan 4 hero.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki >= 2 hero Long-Range Poke / Artileri',
                'kesimpulan_then' => 'THEN: Archetype = "Poke & Siege (Cicil & Dobrak)". Prioritaskan hero poke jarak jauh dan penjaga jarak aman dari Hero Pool tim terpilih sebelum melakukan siege turret.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki Late-Game Hypercarry DAN Healer / Enabler Support',
                'kesimpulan_then' => 'THEN: Archetype = "Protect the Hypercarry (UBE Strategy)". Prioritaskan hero frontliner pelindung dari Hero Pool tim terpilih untuk memberi ruang farming hingga core item carry selesai.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki >= 2 hero Late-Game Scaling',
                'kesimpulan_then' => 'THEN: Power Spike = "Late Game Focus (12+ Menit)". Prioritaskan hero wave clear dan defensive control dari Hero Pool tim terpilih agar scaling carry terjamin hingga fase Evolved Lord.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki hero Early Dominance dengan potensi invasi awal tinggi',
                'kesimpulan_then' => 'THEN: Power Spike = "Early Game Snowball (0 - 8 Menit)". Prioritaskan hero agresi tempo awal dari Hero Pool tim terpilih untuk menekan jungle lawan dan merebut Turtle pertama.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Hero inti memiliki power spike pada level 4 dan 1-2 item inti (Mid Game Peak)',
                'kesimpulan_then' => 'THEN: Power Spike = "Mid Game Peak (8 - 14 Menit)". Prioritaskan hero rotasi cepat dari Hero Pool tim terpilih untuk memaksimalkan transisi menit 8-12 dan menghancurkan outer turret.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki hero Roam/Support Healer',
                'kesimpulan_then' => 'THEN: Tactic Note = "Sustained Teamfight". Prioritaskan hero frontline berdurabilitas tinggi dari Hero Pool tim terpilih untuk memaksimalkan pertarungan berkepanjangan.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim tidak memiliki Tank murni atau Fighter berdaya tahan tinggi (Frontline Durability Rendah)',
                'kesimpulan_then' => 'THEN: Warning = "Kekurangan Frontline / Badan!". Prioritaskan hero Frontline / Tank dengan durabilitas tinggi dari Hero Pool tim terpilih untuk memperkuat inisiasi dan pertahanan tim.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Seluruh hero terpilih berjenis Physical Damage tanpa Magic Burst/DPS',
                'kesimpulan_then' => 'THEN: Warning = "Draft Full Physical!". Prioritaskan hero bertipe Magic Damage / Midlaner dari Hero Pool tim terpilih (atau Overall Meta jika n=0) untuk menjaga keseimbangan komposisi damage.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki Hard Crowd Control < 2 skill penentu',
                'kesimpulan_then' => 'THEN: Warning = "Kekurangan Hard Crowd Control!". Prioritaskan hero bertipe Hard CC / Suppression dari Hero Pool tim terpilih untuk menahan mobilitas lawan.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Varian Map = "Broken Walls" DAN Tim membutuhkan pemanfaatan obstacle dinding',
                'kesimpulan_then' => 'THEN: Map Advantage = "Sinergi Map Broken Walls". Prioritaskan hero dengan mekanik interaksi dinding atau mobilitas lintas obstacle dari Hero Pool tim terpilih.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Varian Map = "Dangerous Grass" DAN Tim memprioritaskan Bush Ambush',
                'kesimpulan_then' => 'THEN: Map Advantage = "Sinergi Map Dangerous Grass". Prioritaskan hero dengan burst ambush atau vision control semak dari Hero Pool tim terpilih.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Varian Map = "Expanding Rivers" DAN Tim memprioritaskan Mobilitas Sungai',
                'kesimpulan_then' => 'THEN: Map Advantage = "Sinergi Map Expanding Rivers". Prioritaskan hero dengan kecepatan rotasi antarlane tinggi dari Hero Pool tim terpilih.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Varian Map = "Flying Clouds" DAN Tim memprioritaskan Movement Speed / Kiting',
                'kesimpulan_then' => 'THEN: Map Advantage = "Sinergi Map Flying Clouds". Prioritaskan hero bertipe kiting atau akselerasi tim dari Hero Pool tim terpilih.',
                'status_aktif' => 1,
            ],
        ];

        foreach ($rules as $r) {
            RbrRule::updateOrCreate(
                ['kondisi_if' => $r['kondisi_if']],
                [
                    'kesimpulan_then' => $r['kesimpulan_then'],
                    'status_aktif'    => $r['status_aktif'],
                ]
            );
        }
    }
}

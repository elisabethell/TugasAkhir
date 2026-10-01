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
                'kondisi_if' => 'IF: Tim memiliki >= 2 hero Assassin / Single-Target Lock (Saber, Chou, Kaja, Franco, Hayabusa, Natalia, Ling, Helcurt, Selena, Gusion) DAN minim AoE besar',
                'kesimpulan_then' => 'THEN: Archetype = "Pick-Off (Culik Lawan)". Winning Condition: Jangan pernah memaksakan war terbuka 5v5 di ruang luas. Kuasai semak-semak (bush control), tunggu musuh lewat sendirian, dan lakukan eliminasi 1 target sebelum objektif Turtle atau Lord agar tercipta situasi 5v4.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki >= 2 hero AoE Crowd Control / Frontline Initiator (Tigreal, Atlas, Terizla, Minotaur, Gatotkaca, Ruby, Belerick, Yu Zhong) DAN Mage Area Burst/Sustained (Pharsa, Yve, Xavier, Cecilion, Lylia, Gord)',
                'kesimpulan_then' => 'THEN: Archetype = "Teamfight / AoE Wiping". Winning Condition: Pancing musuh berkumpul di lorong sempit sekitar pit Turtle atau Lord. Biarkan initiator membuka war dengan Crowd Control berantai, lalu timpakan burst damage area untuk meratakan seluruh tim lawan.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki hero High Mobility / Fast Wave Clear di Side Lane (Benedetta, Sun, Zilong, Masha, Ling, Fanny, Paquito, Chou)',
                'kesimpulan_then' => 'THEN: Archetype = "Split Push & Macro Pressure". Winning Condition: Tekan dua jalur sekaligus. Biarkan 4 hero menahan musuh di mid lane atau pit objektif, sementara 1 hero mobile mendobrak turret samping. Hindari war 5v5 jika formasi musuh masih utuh.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki >= 2 hero Long-Range Poke / Artileri (Novaria, Xavier, Pharsa, Brody, Beatrix, Clint, Gord, Nana)',
                'kesimpulan_then' => 'THEN: Archetype = "Poke & Siege (Cicil & Dobrak)". Winning Condition: Jaga jarak aman dan cicil HP musuh sebelum objektif dibuka atau saat mengepung base turret musuh. Jangan lakukan dive sembrono sampai HP musuh berada di bawah 40%.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki Late-Game Hypercarry (Claude, Karrie, Moskov, Wanwan, Miya, Layla, Irithel) DAN Healer / Enabler Support (Angela, Rafaela, Estes, Floryn, Mathilda, Diggie)',
                'kesimpulan_then' => 'THEN: Archetype = "Protect the Hypercarry (UBE Strategy)". Winning Condition: 4 hero bertugas sebagai pelindung dan pemberi ruang farming (space maker) bagi carry utama di early-mid game. Setelah carry memiliki minimal 3 item core, barulah berkumpul di belakang garis pertahanan untuk memenangkan war penentuan.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki >= 2 hero Late-Game Scaling (Marksman Attack Speed, Cecilion, Aldous, Alice, Claude, Karrie, Moskov)',
                'kesimpulan_then' => 'THEN: Power Spike = "Late Game Focus (12+ Menit)". Winning Condition: Tim ini sangat kuat di late game! Early game prioritaskan scaling dan amankan wave minions. Jangan ambil risiko perang terbuka jika posisi gold tertinggal, pertahankan inhibitor turret hingga item carry selesai.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki hero Early Dominance (Martis, Dyrroth, Hilda, Fanny, Selena, Jawhead, Chou, Fredrinn)',
                'kesimpulan_then' => 'THEN: Power Spike = "Early Game Snowball (0 - 8 Menit)". Winning Condition: Wajib bermain agresif sejak menit pertama! Invasi buff lawan, amankan First Blood, dan rebut Turtle pertama pada menit 2:00. Bangun keunggulan snowball dan selesaikan game sebelum menit ke-15.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Hero inti memiliki power spike pada level 4 dan 1-2 item inti (Gusion, Paquito, Hayabusa, Lancelot, Harith, Granger)',
                'kesimpulan_then' => 'THEN: Power Spike = "Mid Game Peak (8 - 14 Menit)". Winning Condition: Maksimalkan tempo transisi di menit 8-12 saat Turtle ketiga dan Lord pertama muncul. Tumbangkan outer dan inner turret musuh untuk mempersempit area farming lawan.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki hero Roam/Support Healer (Floryn, Estes, Angela, Rafaela)',
                'kesimpulan_then' => 'THEN: Tactic Note = "Sustained Teamfight". Tim memiliki durabilitas regenerasi tinggi; lakukan teamfight berkepanjangan (skirmish) dan hindari burst instan dari lawan.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim tidak memiliki Tank murni atau Fighter berdaya tahan tinggi (Frontline Durability Rendah)',
                'kesimpulan_then' => 'THEN: Warning = "Kekurangan Frontline / Badan!". Tim sangat rentan pecah jika terkena inisiasi mendadak. Roamer atau EXP Laner wajib membangun defense item dan tidak boleh face-check bush sendirian.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Seluruh 5 hero berjenis Physical Damage (tanpa adanya Mage Burst/Magic DPS)',
                'kesimpulan_then' => 'THEN: Warning = "Draft Full Physical!". Lawan akan dengan mudah membeli Antique Cuirass dan Blade Armor untuk mereduksi seluruh damage tim. Wajib beli Malefic Roar secepatnya.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Tim memiliki Hard Crowd Control < 2 skill penentu',
                'kesimpulan_then' => 'THEN: Warning = "Kekurangan Hard Crowd Control!". Assassin atau Fighter musuh yang lincah (seperti Fanny, Ling, Joy, Harith) akan sangat leluasa bergerak tanpa ancaman stun.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Varian Map = "Broken Walls" DAN Tim memiliki hero pemanfaatan obstacle (Fanny, Moskov, Grock, Badang, Ling, Khufra)',
                'kesimpulan_then' => 'THEN: Map Advantage = "Sinergi Map Broken Walls". Lorong sempit dan dinding pecahan map ini melipatgandakan efektivitas stun tembok Moskov/Grock/Badang serta kabel Fanny.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Varian Map = "Dangerous Grass" DAN Tim memiliki hero Bush Ambush (Hilda, Natalia, Kadita, Franco, Selena, Saber)',
                'kesimpulan_then' => 'THEN: Map Advantage = "Sinergi Map Dangerous Grass". Semak lebat memberikan ruang penguasaan pick-off yang sangat tinggi. Selalu pasang perangkap dan ganking dari semak sungai.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Varian Map = "Expanding Rivers" DAN Tim memiliki hero Mobilitas Sungai (Baxia, Kadita, Johnson, Helcurt)',
                'kesimpulan_then' => 'THEN: Map Advantage = "Sinergi Map Expanding Rivers". Area sungai yang luas mempercepat rotasi gank antara Gold Lane dan EXP Lane dalam hitungan detik.',
                'status_aktif' => 1,
            ],
            [
                'kondisi_if' => 'IF: Varian Map = "Flying Clouds" DAN Tim memiliki hero Movement Speed / Kiting (Mathilda, Rafaela, Wanwan, Claude)',
                'kesimpulan_then' => 'THEN: Map Advantage = "Sinergi Map Flying Clouds". Aliran angin awan memberikan akselerasi mobilitas saat mengejar musuh atau mundur dari war yang tidak menguntungkan.',
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

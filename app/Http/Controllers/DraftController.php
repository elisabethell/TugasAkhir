<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvDataService;
use App\Services\RbrInferenceEngine;

class DraftController extends Controller
{
    protected CsvDataService $csvService;
    protected RbrInferenceEngine $engine;

    public function __construct(CsvDataService $csvService, RbrInferenceEngine $engine)
    {
        $this->csvService = $csvService;
        $this->engine = $engine;
    }

    public function counterPicks()
    {
        // 1. Ambil seluruh data 133 hero dari database/data/data_hero.csv
        $heroes = $this->csvService->getHeroes()->sortBy('hero_name')->values();

        // 2. Mapping hero portraits
        $heroPortraits = [];
        foreach ($heroes as $h) {
            $heroPortraits[$h->clean_key] = $h->portrait;
        }

        // Data JSON picker hero untuk frontend JS
        $pickerHeroes = $heroes->map(function($h) {
            return [
                'name'     => $h->hero_name,
                'initial'  => $h->tag,
                'lane'     => strtolower($h->laning),
                'role'     => strtolower($h->primary_class),
                'portrait' => $h->portrait,
            ];
        });

        $defaultEnemyTeam = [];

        // Rekomendasi hero meta turnamen dari dataset
        $defaultRecommendations = [
            [
                'hero' => 'Granger',
                'tier' => 'TIER S',
                'win_rate' => '54.7%',
                'win_rate_label' => 'TOURNAMENT WIN RATE',
                'role' => 'Marksman / Assassin',
                'lane' => 'Jungle / Gold',
                'impact_score' => 4.7,
                'impact_progress' => 94,
                'accent_border' => true,
                'portrait' => $this->csvService->resolveHeroPortrait('Granger'),
                'initial' => 'GR',
                'rule_text' => 'Prioritas meta turnamen: mobilitas burst tinggi & kontrol objektif kuat',
                'rule_icon' => 'check',
                'samples' => '69 MATCHES SAMPLE',
            ],
            [
                'hero' => 'Yve',
                'tier' => 'TIER S',
                'win_rate' => '78.3%',
                'win_rate_label' => 'TOURNAMENT WIN RATE',
                'role' => 'Mage',
                'lane' => 'Mid Lane',
                'impact_score' => 4.6,
                'impact_progress' => 92,
                'accent_border' => false,
                'portrait' => $this->csvService->resolveHeroPortrait('Yve'),
                'initial' => 'YV',
                'rule_text' => 'Prioritas meta turnamen: zoning Real World Manipulation & slow area dominan',
                'rule_icon' => 'check',
                'samples' => '23 MATCHES SAMPLE',
            ],
            [
                'hero' => 'Belerick',
                'tier' => 'TIER A',
                'win_rate' => '70.4%',
                'win_rate_label' => 'TOURNAMENT WIN RATE',
                'role' => 'Tank',
                'lane' => 'Roam',
                'impact_score' => 4.4,
                'impact_progress' => 88,
                'accent_border' => false,
                'portrait' => $this->csvService->resolveHeroPortrait('Belerick'),
                'initial' => 'BL',
                'rule_text' => 'Prioritas meta turnamen: frontline kokoh pembalik burst proyektil dan taunt area',
                'rule_icon' => 'check',
                'samples' => '27 MATCHES SAMPLE',
            ],
            [
                'hero' => 'Carmilla',
                'tier' => 'TIER S',
                'win_rate' => '100.0%',
                'win_rate_label' => 'TOURNAMENT WIN RATE',
                'role' => 'Support / Tank',
                'lane' => 'Roam',
                'impact_score' => 4.5,
                'impact_progress' => 90,
                'accent_border' => false,
                'portrait' => $this->csvService->resolveHeroPortrait('Carmilla'),
                'initial' => 'CR',
                'rule_text' => 'Prioritas meta turnamen: Curse of Blood membagi burst damage tim secara instan',
                'rule_icon' => 'check',
                'samples' => '5 MATCHES SAMPLE',
            ],
            [
                'hero' => 'Sora',
                'tier' => 'TIER S',
                'win_rate' => '68.8%',
                'win_rate_label' => 'TOURNAMENT WIN RATE',
                'role' => 'Fighter / Assassin',
                'lane' => 'Exp Lane',
                'impact_score' => 4.3,
                'impact_progress' => 86,
                'accent_border' => false,
                'portrait' => $this->csvService->resolveHeroPortrait('Sora'),
                'initial' => 'SR',
                'rule_text' => 'Prioritas meta turnamen: fleksibilitas Thunder/Torrent & sustain duel lane tinggi',
                'rule_icon' => 'check',
                'samples' => '16 MATCHES SAMPLE',
            ],
            [
                'hero' => 'Kaja',
                'tier' => null,
                'win_rate' => '51.2%',
                'win_rate_label' => 'TOURNAMENT WIN RATE',
                'role' => 'Support / Fighter',
                'lane' => 'Roam / Mid',
                'impact_score' => 3.8,
                'impact_progress' => 76,
                'accent_border' => false,
                'portrait' => $this->csvService->resolveHeroPortrait('Kaja'),
                'initial' => 'KJ',
                'rule_text' => 'Suppression pick-off cepat untuk melumpuhkan core dan inisiator musuh',
                'rule_icon' => 'lock',
                'samples' => '48 MATCHES SAMPLE',
            ],
            [
                'hero' => 'Irithel',
                'tier' => null,
                'win_rate' => '48.9%',
                'win_rate_label' => '7-DAY WIN RATE',
                'role' => 'Marksman',
                'lane' => 'Gold Lane',
                'impact_score' => 3.5,
                'impact_progress' => 70,
                'accent_border' => false,
                'portrait' => $this->csvService->resolveHeroPortrait('Irithel'),
                'initial' => 'IR',
                'rule_text' => 'Mobilitas tembak terus menerus (kite while shooting) & debuff armor area',
                'rule_icon' => 'arrow',
                'samples' => '52 MATCHES SAMPLE',
            ],
            [
                'hero' => 'Khufra',
                'tier' => null,
                'win_rate' => '52.4%',
                'win_rate_label' => 'TOURNAMENT WIN RATE',
                'role' => 'Tank / Roam',
                'lane' => 'Roamer',
                'impact_score' => 3.4,
                'impact_progress' => 68,
                'accent_border' => false,
                'portrait' => $this->csvService->resolveHeroPortrait('Khufra'),
                'initial' => 'KF',
                'rule_text' => 'Bouncing Ball cc menghentikan seluruh skill dash/blink lincah musuh',
                'rule_icon' => 'ban',
                'samples' => '39 MATCHES SAMPLE',
            ],
        ];

        // Pemetaan data counter dan statistik per hero untuk analisis dinamis di frontend
        $heroCounterMap = [];
        $tournamentStats = $this->csvService->getTournamentStatistics();
        $heroStatsLookup = [];

        foreach ($heroes as $h) {
            $counters = [];
            if (!empty($h->counters)) {
                foreach ($h->counters as $c) {
                    if (!empty($c['heroname'])) {
                        $counters[] = $c['heroname'];
                    }
                }
            }
            $heroCounterMap[$h->hero_name] = $counters;

            $stat = $tournamentStats[$h->clean_key] ?? null;
            $wrVal = ($stat && $stat['picks'] > 0) ? round($stat['wr'], 1) : 50.0;
            $heroStatsLookup[$h->hero_name] = [
                'hero'            => $h->hero_name,
                'role'            => $h->class ?: $h->primary_class,
                'lane'            => $h->laning ?: 'Flex',
                'portrait'        => $h->portrait,
                'initial'         => $h->tag,
                'win_rate'        => $wrVal . '%',
                'win_rate_label'  => ($stat && $stat['picks'] > 0) ? 'TOURNAMENT WIN RATE' : 'OVERALL WIN RATE',
                'tier'            => $wrVal >= 60 ? 'TIER S' : ($wrVal >= 52 ? 'TIER A' : null),
                'impact_score'    => min(5.0, round(2.5 + ($wrVal / 25), 1)),
                'impact_progress' => min(100, (int)($wrVal * 1.3)),
                'samples'         => (($stat && $stat['picks'] > 0) ? $stat['picks'] : 30) . ' MATCHES SAMPLE',
            ];
        }

        // Aturan spesifik counter interaktif (Rule-Based Reasoning)
        $specificRules = [
            'Beatrix' => [
                'Granger' => ['rule' => 'counters Beatrix (out-ranges sniper, high burst mobility)', 'icon' => 'check', 'tier' => 'TIER S', 'impact' => 4.7],
                'Kaja'    => ['rule' => 'counters Beatrix (suppression halts ultimate channeling)', 'icon' => 'lock', 'tier' => null, 'impact' => 3.8],
                'Irithel' => ['rule' => 'counters Beatrix (continuous kite while shooting, evades rocket skill)', 'icon' => 'arrow', 'tier' => null, 'impact' => 3.2],
                'Khufra'  => ['rule' => 'counters Beatrix (cancels dash with bouncing ball cc)', 'icon' => 'ban', 'tier' => null, 'impact' => 2.9],
                'Natalia' => ['rule' => 'counters Beatrix (silence and burst eliminates sniper positioning)', 'icon' => 'check', 'tier' => null, 'impact' => 4.0],
                'Lolita'  => ['rule' => 'counters Beatrix (shield blocks all sniper and rocket bullets)', 'icon' => 'check', 'tier' => 'TIER A', 'impact' => 4.5],
            ],
            'Fanny' => [
                'Khufra'  => ['rule' => 'counters Fanny (bouncing ball membatalkan lintasan kabel baja)', 'icon' => 'ban', 'tier' => 'TIER S', 'impact' => 4.9],
                'Franco'  => ['rule' => 'counters Fanny (suppression iron hook menghentikan rotasi terbang)', 'icon' => 'lock', 'tier' => 'TIER A', 'impact' => 4.4],
                'Kaja'    => ['rule' => 'counters Fanny (suppression pick-off instan saat mendekat)', 'icon' => 'lock', 'tier' => 'TIER A', 'impact' => 4.3],
                'Saber'   => ['rule' => 'counters Fanny (airborne lock & burst combo instan)', 'icon' => 'check', 'tier' => null, 'impact' => 4.1],
            ],
            'Ling' => [
                'Khufra'  => ['rule' => 'counters Ling (bouncing ball menjatuhkan ling saat melompat dinding)', 'icon' => 'ban', 'tier' => 'TIER S', 'impact' => 4.8],
                'Kaja'    => ['rule' => 'counters Ling (suppression instan sebelum tempest of blades)', 'icon' => 'lock', 'tier' => 'TIER A', 'impact' => 4.5],
                'Ruby'    => ['rule' => 'counters Ling (stun dan hook konstan membatalkan mobilitas pedang)', 'icon' => 'check', 'tier' => null, 'impact' => 4.2],
            ],
            'Wanwan' => [
                'Phoveus' => ['rule' => 'counters Wanwan (dash konstan memicu lompatan ultimate tiada henti)', 'icon' => 'check', 'tier' => 'TIER S', 'impact' => 4.8],
                'Khufra'  => ['rule' => 'counters Wanwan (bouncing ball mengunci lompatan pasif)', 'icon' => 'ban', 'tier' => 'TIER A', 'impact' => 4.4],
                'Lolita'  => ['rule' => 'counters Wanwan (shield memblokir proyektil jarum dan ultimate)', 'icon' => 'check', 'tier' => 'TIER A', 'impact' => 4.6],
            ],
            'Claude' => [
                'Belerick' => ['rule' => 'counters Claude (blazing duet memicu deadly thorns bertubi-tubi)', 'icon' => 'check', 'tier' => 'TIER S', 'impact' => 4.9],
                'Lolita'   => ['rule' => 'counters Claude (shield menahan semburan tembakan blazing duet)', 'icon' => 'check', 'tier' => 'TIER S', 'impact' => 4.7],
                'Khufra'   => ['rule' => 'counters Claude (bouncing ball membatalkan teleportasi battle mirror image)', 'icon' => 'ban', 'tier' => 'TIER A', 'impact' => 4.4],
            ],
            'Tigreal' => [
                'Diggie'   => ['rule' => 'counters Tigreal (time journey menghapus seluruh efek implosion)', 'icon' => 'check', 'tier' => 'TIER S', 'impact' => 5.0],
                'Valir'    => ['rule' => 'counters Tigreal (burst fireball & knockback menggagalkan inisiasi)', 'icon' => 'ban', 'tier' => 'TIER A', 'impact' => 4.3],
            ],
        ];

        // Daftar spell dan map untuk rekomendasi lanjutan
        $spells = [
            'Flicker'     => $this->csvService->getSpellImage('Flicker'),
            'Retribution' => $this->csvService->getSpellImage('Retribution'),
            'Purify'      => $this->csvService->getSpellImage('Purify'),
            'Revitalize'  => $this->csvService->getSpellImage('Revitalize'),
            'Vengeance'   => $this->csvService->getSpellImage('Vengeance'),
            'Inspire'     => $this->csvService->getSpellImage('Inspire'),
        ];

        $maps = [
            'Broken Walls'     => $this->csvService->getMapImage('Broken Walls'),
            'Dangerous Grass'  => $this->csvService->getMapImage('Dangerous Grass'),
            'Expanding Rivers' => $this->csvService->getMapImage('Expanding Rivers'),
            'Flying Clouds'    => $this->csvService->getMapImage('Flying Clouds'),
        ];

        return view('counter_picks', compact(
            'heroes',
            'pickerHeroes',
            'heroPortraits',
            'defaultEnemyTeam',
            'defaultRecommendations',
            'heroCounterMap',
            'heroStatsLookup',
            'specificRules',
            'spells',
            'maps'
        ));
    }

    /**
     * Halaman Rekomendasi Draft Pick & Ban (Sinergi & Counter Ancaman)
     */
    public function draftRecommendation()
    {
        // 1. Ambil seluruh data 133 hero dari CSV
        $heroes = $this->csvService->getHeroes()->sortBy('hero_name')->values();

        $heroPortraits = [];
        foreach ($heroes as $h) {
            $heroPortraits[$h->clean_key] = $h->portrait;
        }

        // Data JSON picker hero untuk frontend JS
        $pickerHeroes = $heroes->map(function($h) {
            return [
                'name'     => $h->hero_name,
                'initial'  => $h->tag,
                'lane'     => strtolower($h->laning),
                'role'     => strtolower($h->primary_class),
                'portrait' => $h->portrait,
            ];
        });

        // 2. Pemetaan Counter (Ancaman) & Sinergi per Hero dari data_hero.csv
        $heroCounterMap = [];
        $heroSynergyMap = [];
        $tournamentStats = $this->csvService->getTournamentStatistics();
        $heroStatsLookup = [];

        foreach ($heroes as $h) {
            $counters = [];
            if (!empty($h->counters)) {
                foreach ($h->counters as $c) {
                    if (!empty($c['heroname'])) {
                        $counters[] = $c['heroname'];
                    }
                }
            }
            $heroCounterMap[$h->hero_name] = $counters;

            $synergies = [];
            if (!empty($h->synergies)) {
                foreach ($h->synergies as $s) {
                    if (!empty($s['heroname'])) {
                        $synergies[] = $s['heroname'];
                    }
                }
            }
            $heroSynergyMap[$h->hero_name] = $synergies;

            $stat = $tournamentStats[$h->clean_key] ?? null;
            $wrVal = ($stat && $stat['picks'] > 0) ? round($stat['wr'], 1) : 50.0;
            $banVal = ($stat && $stat['bans'] > 0) ? round($stat['ban_rate'], 1) : 0.0;

            $heroStatsLookup[$h->hero_name] = [
                'hero'            => $h->hero_name,
                'role'            => $h->class ?: $h->primary_class,
                'primary_role'    => $h->primary_class,
                'lane'            => $h->laning ?: 'Flex',
                'portrait'        => $h->portrait,
                'initial'         => $h->tag,
                'win_rate'        => $wrVal . '%',
                'ban_rate'        => $banVal . '%',
                'tier'            => $wrVal >= 60 ? 'TIER S' : ($wrVal >= 52 ? 'TIER A' : 'TIER B'),
                'impact_score'    => min(5.0, round(2.5 + ($wrVal / 25), 1)),
                'ban_score'       => min(10.0, round(max(2.0, $banVal / 10), 1)),
            ];
        }

        // 3. Default OP-01 PICKS (14 hero meta rekomendasi dasar saat draft kosong)
        $defaultPicks = [
            [
                'hero'          => 'Tigreal',
                'role'          => 'Tank',
                'lane'          => 'roam',
                'score'         => '2.6',
                'priority'      => 'P3',
                'progress'      => 85,
                'portrait'      => $this->csvService->resolveHeroPortrait('Tigreal'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Anchor Crowd Control',
            ],
            [
                'hero'          => 'Akai',
                'role'          => 'Tank',
                'lane'          => 'roam',
                'score'         => '2.5',
                'priority'      => 'P3',
                'progress'      => 80,
                'portrait'      => $this->csvService->resolveHeroPortrait('Akai'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Heavy Spin Objective',
            ],
            [
                'hero'          => 'Zetian',
                'role'          => 'Mage',
                'lane'          => 'mid lane',
                'score'         => '1.2',
                'priority'      => 'P3',
                'progress'      => 60,
                'portrait'      => $this->csvService->resolveHeroPortrait('Zetian'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'High Burst Area',
            ],
            [
                'hero'          => 'Lolita',
                'role'          => 'Support',
                'lane'          => 'roam',
                'score'         => '1.2',
                'priority'      => 'P3',
                'progress'      => 60,
                'portrait'      => $this->csvService->resolveHeroPortrait('Lolita'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Guardian Shield Defense',
            ],
            [
                'hero'          => 'Khaleed',
                'role'          => 'Fighter',
                'lane'          => 'exp lane',
                'score'         => '1.1',
                'priority'      => 'P3',
                'progress'      => 55,
                'portrait'      => $this->csvService->resolveHeroPortrait('Khaleed'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Early Skirmish Duelist',
            ],
            [
                'hero'          => 'Kalea',
                'role'          => 'Support',
                'lane'          => 'roam',
                'score'         => '1.1',
                'priority'      => 'P3',
                'progress'      => 55,
                'portrait'      => $this->csvService->resolveHeroPortrait('Kalea'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Protective Sustain Buff',
            ],
            [
                'hero'          => 'Gloo',
                'role'          => 'Tank',
                'lane'          => 'exp lane',
                'score'         => '0.8',
                'priority'      => 'P3',
                'progress'      => 45,
                'portrait'      => $this->csvService->resolveHeroPortrait('Gloo'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Sticky Harass Disruption',
            ],
            [
                'hero'          => 'Martis',
                'role'          => 'Fighter',
                'lane'          => 'jungle',
                'score'         => '0.7',
                'priority'      => 'P3',
                'progress'      => 40,
                'portrait'      => $this->csvService->resolveHeroPortrait('Martis'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Immunity Rush Finisher',
            ],
            [
                'hero'          => 'Granger',
                'role'          => 'Marksman',
                'lane'          => 'gold lane',
                'score'         => '4.7',
                'priority'      => 'P1',
                'progress'      => 94,
                'portrait'      => $this->csvService->resolveHeroPortrait('Granger'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Rhapsody Burst Gold',
            ],
            [
                'hero'          => 'Yve',
                'role'          => 'Mage',
                'lane'          => 'mid lane',
                'score'         => '4.6',
                'priority'      => 'P1',
                'progress'      => 92,
                'portrait'      => $this->csvService->resolveHeroPortrait('Yve'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Real World Manipulation',
            ],
            [
                'hero'          => 'Belerick',
                'role'          => 'Tank',
                'lane'          => 'roam',
                'score'         => '4.4',
                'priority'      => 'P2',
                'progress'      => 88,
                'portrait'      => $this->csvService->resolveHeroPortrait('Belerick'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Deadly Thorns Taunt',
            ],
            [
                'hero'          => 'Carmilla',
                'role'          => 'Support',
                'lane'          => 'roam',
                'score'         => '4.5',
                'priority'      => 'P2',
                'progress'      => 90,
                'portrait'      => $this->csvService->resolveHeroPortrait('Carmilla'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Curse of Blood Link',
            ],
            [
                'hero'          => 'Sora',
                'role'          => 'Fighter',
                'lane'          => 'exp lane',
                'score'         => '4.3',
                'priority'      => 'P2',
                'progress'      => 86,
                'portrait'      => $this->csvService->resolveHeroPortrait('Sora'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Torrent Duelist Lane',
            ],
            [
                'hero'          => 'Harith',
                'role'          => 'Mage',
                'lane'          => 'gold lane',
                'score'         => '4.2',
                'priority'      => 'P2',
                'progress'      => 84,
                'portrait'      => $this->csvService->resolveHeroPortrait('Harith'),
                'subtitle_type' => 'META',
                'subtitle_hero' => 'Chrono Dash Flexibility',
            ],
        ];

        // 4. Default OP-02 BANS (Hero prioritas ban turnamen tertinggi)
        $defaultBans = [
            [
                'hero'          => 'Gloo',
                'role'          => 'Tank',
                'score'         => '8.7',
                'priority'      => 'P1',
                'progress'      => 87,
                'portrait'      => $this->csvService->resolveHeroPortrait('Gloo'),
                'subtitle_type' => 'THR',
                'subtitle_hero' => 'High Tournament Ban (84.1%)',
            ],
            [
                'hero'          => 'Hayabusa',
                'role'          => 'Assassin',
                'score'         => '6.2',
                'priority'      => 'P2',
                'progress'      => 62,
                'portrait'      => $this->csvService->resolveHeroPortrait('Hayabusa'),
                'subtitle_type' => 'THR',
                'subtitle_hero' => 'High Tournament Ban (72.5%)',
            ],
            [
                'hero'          => 'Harith',
                'role'          => 'Mage / Marksman',
                'score'         => '9.1',
                'priority'      => 'P1',
                'progress'      => 91,
                'portrait'      => $this->csvService->resolveHeroPortrait('Harith'),
                'subtitle_type' => 'THR',
                'subtitle_hero' => 'High Tournament Ban (91.3%)',
            ],
            [
                'hero'          => 'Fanny',
                'role'          => 'Assassin',
                'score'         => '6.5',
                'priority'      => 'P2',
                'progress'      => 65,
                'portrait'      => $this->csvService->resolveHeroPortrait('Fanny'),
                'subtitle_type' => 'THR',
                'subtitle_hero' => 'High Tournament Ban (65.2%)',
            ],
            [
                'hero'          => 'Ling',
                'role'          => 'Assassin',
                'score'         => '5.9',
                'priority'      => 'P2',
                'progress'      => 59,
                'portrait'      => $this->csvService->resolveHeroPortrait('Ling'),
                'subtitle_type' => 'THR',
                'subtitle_hero' => 'High Tournament Ban (59.4%)',
            ],
            [
                'hero'          => 'Nolan',
                'role'          => 'Assassin',
                'score'         => '5.5',
                'priority'      => 'P2',
                'progress'      => 55,
                'portrait'      => $this->csvService->resolveHeroPortrait('Nolan'),
                'subtitle_type' => 'THR',
                'subtitle_hero' => 'High Tournament Ban (55.1%)',
            ],
            [
                'hero'          => 'Mathilda',
                'role'          => 'Support / Assassin',
                'score'         => '5.2',
                'priority'      => 'P2',
                'progress'      => 52,
                'portrait'      => $this->csvService->resolveHeroPortrait('Mathilda'),
                'subtitle_type' => 'THR',
                'subtitle_hero' => 'High Tournament Ban (52.2%)',
            ],
            [
                'hero'          => 'Valentina',
                'role'          => 'Mage',
                'score'         => '5.0',
                'priority'      => 'P2',
                'progress'      => 50,
                'portrait'      => $this->csvService->resolveHeroPortrait('Valentina'),
                'subtitle_type' => 'THR',
                'subtitle_hero' => 'High Tournament Ban (50.7%)',
            ],
        ];

        return view('draft_recommendation', compact(
            'heroes',
            'pickerHeroes',
            'heroPortraits',
            'heroCounterMap',
            'heroSynergyMap',
            'heroStatsLookup',
            'defaultPicks',
            'defaultBans'
        ));
    }

    public function index()
    {
        return $this->draftRecommendation();
    }

    public function analyze(Request $request)
    {
        $enemyTeam = $request->input('enemy_team', []);
        $lane = $request->input('lane', 'all');
        $role = $request->input('role', 'all');

        $result = $this->engine->analyze([], $enemyTeam, null);

        return response()->json($result);
    }
}

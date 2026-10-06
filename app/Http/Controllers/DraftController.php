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

    public function index()
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

        $defaultEnemyTeam = ['Beatrix'];

        // Rekomendasi hero counter dengan foto dan statistik riil
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
                'rule_text' => 'counters Beatrix (out-ranges sniper, high burst mobility)',
                'rule_icon' => 'check',
                'samples' => '69 MATCHES SAMPLE',
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
                'rule_text' => 'counters Beatrix (suppression halts ultimate channeling)',
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
                'impact_score' => 3.2,
                'impact_progress' => 64,
                'accent_border' => false,
                'portrait' => $this->csvService->resolveHeroPortrait('Irithel'),
                'initial' => 'IR',
                'rule_text' => 'counters Beatrix (continuous kite while shooting, evades rocket skill)',
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
                'impact_score' => 2.9,
                'impact_progress' => 58,
                'accent_border' => false,
                'portrait' => $this->csvService->resolveHeroPortrait('Khufra'),
                'initial' => 'KF',
                'rule_text' => 'counters Beatrix (cancels dash with bouncing ball cc)',
                'rule_icon' => 'ban',
                'samples' => '39 MATCHES SAMPLE',
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

        return view('draft_analyzer', compact(
            'heroes',
            'pickerHeroes',
            'heroPortraits',
            'defaultEnemyTeam',
            'defaultRecommendations',
            'spells',
            'maps'
        ));
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

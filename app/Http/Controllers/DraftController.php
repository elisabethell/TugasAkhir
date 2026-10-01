<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hero;
use App\Models\Dataset;
use App\Services\RbrInferenceEngine;

class DraftController extends Controller
{
    protected RbrInferenceEngine $engine;

    public function __construct(RbrInferenceEngine $engine)
    {
        $this->engine = $engine;
    }

    public function index()
    {
        // 1. Ambil semua data hero untuk picker
        $heroes = Hero::orderBy('hero_name')->get();

        // 2. Mapping hero portraits (URL resmi Moonton + lokal untuk Sora, Marcel, Hirara)
        $heroPortraits = [];
        foreach ($heroes as $h) {
            $cleanKey = preg_replace('/[^a-z0-9]/', '', strtolower($h->hero_name));
            if (!empty($h->portrait) && str_starts_with($h->portrait, 'http') && !str_contains($h->portrait, 'deviantart') && !str_contains($h->portrait, 'mobilelegends.com')) {
                $heroPortraits[$cleanKey] = $h->portrait;
            } else {
                $localFile = strtolower(str_replace(' ', '_', $h->hero_name)) . '.png';
                if (file_exists(public_path('images/heroes/' . $localFile))) {
                    $heroPortraits[$cleanKey] = asset('images/heroes/' . $localFile);
                } else {
                    $heroPortraits[$cleanKey] = $h->portrait;
                }
            }
        }

        // 3. Kelompokkan hero berdasarkan role/laning
        $heroesByLane = [
            'exp'    => $heroes->filter(fn($h) => str_contains(strtolower($h->laning ?? ''), 'exp'))->values(),
            'jungle' => $heroes->filter(fn($h) => str_contains(strtolower($h->laning ?? ''), 'jungle'))->values(),
            'mid'    => $heroes->filter(fn($h) => str_contains(strtolower($h->laning ?? ''), 'mid'))->values(),
            'gold'   => $heroes->filter(fn($h) => str_contains(strtolower($h->laning ?? ''), 'gold'))->values(),
            'roam'   => $heroes->filter(fn($h) => str_contains(strtolower($h->laning ?? ''), 'roam'))->values(),
        ];

        // 4. Default preset untuk demo langsung (Contoh: Team Vitality Grand Final Draft)
        $defaultMyTeam = [
            'exp'    => 'Terizla',
            'jungle' => 'Baxia',
            'mid'    => 'Yve',
            'gold'   => 'Claude',
            'roam'   => 'Tigreal',
        ];

        $defaultEnemyTeam = [
            'exp'    => 'Ruby',
            'jungle' => 'Fanny',
            'mid'    => 'Novaria',
            'gold'   => 'Karrie',
            'roam'   => 'Franco',
        ];

        $defaultMap = 'Dangerous Grass';

        // 5. Analisis awal menggunakan RBR
        $initialAnalysis = $this->engine->analyze(
            array_values($defaultMyTeam),
            array_values($defaultEnemyTeam),
            $defaultMap
        );

        $maps = [
            'Broken Walls'     => 'broken_wall.webp',
            'Dangerous Grass'  => 'dangerous_grass.webp',
            'Expanding Rivers' => 'expanding_river.webp',
            'Flying Clouds'    => 'flying_cloud.webp',
        ];

        return view('draft_analyzer', compact(
            'heroes',
            'heroesByLane',
            'heroPortraits',
            'defaultMyTeam',
            'defaultEnemyTeam',
            'defaultMap',
            'initialAnalysis',
            'maps'
        ));
    }

    public function analyze(Request $request)
    {
        $myTeam = $request->input('my_team', []);
        $enemyTeam = $request->input('enemy_team', []);
        $map = $request->input('map', null);

        $result = $this->engine->analyze($myTeam, $enemyTeam, $map);

        return response()->json($result);
    }
}

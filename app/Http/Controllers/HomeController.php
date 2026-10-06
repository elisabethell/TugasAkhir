<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvDataService;

class HomeController extends Controller
{
    protected CsvDataService $csvService;

    public function __construct(CsvDataService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function index()
    {
        $summary = $this->csvService->getTournamentSummary();

        $totalGames = $summary['totalGames'];
        $fastestFormatted = $summary['fastestFormatted'];
        $longestFormatted = $summary['longestFormatted'];
        $avgFormatted = $summary['avgFormatted'];
        $bluePct = $summary['bluePct'];
        $redPct = $summary['redPct'];
        $blueCount = $summary['blueWins'];
        $redCount = $summary['redWins'];

        // Ambil 4 hero terkontes teratas langsung dari MWI_X_EWC_2026.csv & data_hero.csv
        $topStats = $this->csvService->getTournamentStatistics();
        $topHeroesRaw = array_slice($topStats, 0, 4);

        $topHeroes = array_map(function($h, $idx) {
            $highlight = match($idx) {
                0 => 'ban',
                1 => 'pick',
                2 => 'win',
                default => 'normal',
            };
            return [
                'initial'   => $h['initial'],
                'name'      => $h['name'],
                'portrait'  => $h['portrait'],
                'role'      => strtoupper($h['role'] ?: 'HERO'),
                'ban_rate'  => number_format($h['ban_rate'], 1) . '%',
                'pick_rate' => number_format($h['pick_rate'], 1) . '%',
                'win_rate'  => number_format($h['wr'], 1) . '%',
                'highlight' => $highlight,
            ];
        }, $topHeroesRaw, array_keys($topHeroesRaw));

        // 4. Varian Map Aktif dengan gambar dari public/images/maps/
        $mapVariants = [
            [
                'name'  => 'DANGEROUS GRASS',
                'desc'  => 'Ekspansi semak area River',
                'image' => asset('images/maps/dangerous_grass.webp'),
                'type'  => 'grass',
            ],
            [
                'name'  => 'BROKEN WALLS',
                'desc'  => 'Celah dinding buff jungle',
                'image' => asset('images/maps/broken_wall.webp'),
                'type'  => 'walls',
            ],
            [
                'name'  => 'EXPANDING RIVERS',
                'desc'  => 'Akselerasi zona kontes Turtle',
                'image' => asset('images/maps/expanding_river.webp'),
                'type'  => 'river',
            ],
            [
                'name'  => 'FLYING CLOUDS',
                'desc'  => 'Zone speed boost Lord pit',
                'image' => asset('images/maps/flying_cloud.webp'),
                'type'  => 'cloud',
            ],
        ];

        return view('home', compact(
            'totalGames',
            'fastestFormatted',
            'longestFormatted',
            'avgFormatted',
            'bluePct',
            'redPct',
            'blueCount',
            'redCount',
            'topHeroes',
            'mapVariants'
        ));
    }
}

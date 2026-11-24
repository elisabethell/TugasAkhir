<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvDataService;
use App\Services\LiquipediaApiService;
use App\Models\MatchDataset;
use App\Models\MatchGame;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HomeController extends Controller
{
    protected CsvDataService $csvService;
    protected LiquipediaApiService $liquipediaService;

    public function __construct(CsvDataService $csvService, LiquipediaApiService $liquipediaService)
    {
        $this->csvService = $csvService;
        $this->liquipediaService = $liquipediaService;
    }

    public function landing()
    {
        $totalHeroes = 133;
        $totalGames = 0;
        $totalMatches = 0;

        try {
            if (Schema::hasTable('match_games')) {
                $totalGames = MatchGame::count();
            }
            if (Schema::hasTable('match_datasets')) {
                $totalMatches = MatchDataset::count();
            }
        } catch (\Throwable $e) {
            // Safe fallback
        }

        return view('landing', compact('totalHeroes', 'totalGames', 'totalMatches'));
    }

    public function index()
    {
        // 1. Inisialisasi database lokal jika tabel match dataset masih kosong
        try {
            if (Schema::hasTable('match_datasets') && Schema::hasTable('match_games')) {
                if (MatchDataset::count() === 0) {
                    $this->liquipediaService->seedMockupSampleMatch();
                }
            }
        } catch (\Throwable $e) {
            // Lanjutkan jika ada kendala koneksi awal
        }

        // 2. Ambil ringkasan turnamen dari database lokal / CsvDataService
        $summary = $this->csvService->getTournamentSummary();

        $totalGames = $summary['totalGames'] ?? 0;
        $fastestFormatted = $summary['fastestFormatted'] ?? '11:00';
        $longestFormatted = $summary['longestFormatted'] ?? '29:28';
        $avgFormatted = $summary['avgFormatted'] ?? '16:10';
        $bluePct = $summary['bluePct'] ?? 50.0;
        $redPct = $summary['redPct'] ?? 50.0;
        $blueCount = $summary['blueWins'] ?? 0;
        $redCount = $summary['redWins'] ?? 0;

        // 3. Tanggal Terakhir Diperbarui (Dinamis dari MatchDataset atau Waktu Terkini)
        $lastUpdatedDate = '09 November 2026';
        $lastUpdatedTime = '00:30 WIB';

        try {
            if (Schema::hasTable('match_datasets')) {
                $latestMatch = MatchDataset::orderBy('match_date', 'desc')->first();
                if ($latestMatch && $latestMatch->match_date) {
                    $dt = $latestMatch->match_date instanceof Carbon 
                        ? $latestMatch->match_date->copy()->setTimezone('Asia/Jakarta')
                        : Carbon::parse($latestMatch->match_date)->setTimezone('Asia/Jakarta');
                    $lastUpdatedDate = $dt->locale('id')->isoFormat('D MMMM Y');
                    $lastUpdatedTime = $dt->format('H:i') . ' WIB';
                } else {
                    $lastUpdatedDate = Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('D MMMM Y');
                    $lastUpdatedTime = Carbon::now('Asia/Jakarta')->format('H:i') . ' WIB';
                }
            }
        } catch (\Throwable $e) {
            $lastUpdatedDate = Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('D MMMM Y');
            $lastUpdatedTime = Carbon::now('Asia/Jakarta')->format('H:i') . ' WIB';
        }

        // 4. Versi Patch Aktif Turnamen (Patch 1.9.14)
        $patchVersion = '1.9.14';

        // 5. 6 Hero Terkontes Teratas (Pick + Ban)
        $topStats = $this->csvService->getTournamentStatistics();
        $topHeroesRaw = array_slice($topStats, 0, 6);

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

        // 6. Varian Map Aktif Turnamen Resmi (7 Varian Map termasuk Revealing Wisps, Healing Turtle, Golden Turret)
        $mapVariants = [
            [
                'name'   => 'REVEALING WISPS',
                'desc'   => 'Vision wisps mengungkap area river & semak rotasi',
                'image'  => asset('images/maps/revealing_wisps.webp'),
                'type'   => 'wisps',
                'is_new' => true,
            ],
            [
                'name'   => 'HEALING TURTLE',
                'desc'   => 'Regenerasi HP & shield protektif area turtle pit',
                'image'  => asset('images/maps/healing_turtle.webp'),
                'type'   => 'turtle',
                'is_new' => true,
            ],
            [
                'name'   => 'GOLDEN TURRET',
                'desc'   => 'Bonus gold ekstra & pertahanan outer lane turret',
                'image'  => asset('images/maps/golden_turret.webp'),
                'type'   => 'turret',
                'is_new' => true,
            ],
            [
                'name'   => 'DANGEROUS GRASS',
                'desc'   => 'Ekspansi semak lebat untuk manuver ganking tim',
                'image'  => asset('images/maps/dangerous_grass.webp'),
                'type'   => 'grass',
                'is_new' => false,
            ],
            [
                'name'   => 'BROKEN WALLS',
                'desc'   => 'Celah dinding terbuka di koridor buff jungle',
                'image'  => asset('images/maps/broken_wall.webp'),
                'type'   => 'walls',
                'is_new' => false,
            ],
            [
                'name'   => 'EXPANDING RIVERS',
                'desc'   => 'Akselerasi movement speed di perlintasan river',
                'image'  => asset('images/maps/expanding_river.webp'),
                'type'   => 'river',
                'is_new' => false,
            ],
            [
                'name'   => 'FLYING CLOUDS',
                'desc'   => 'Zone speed boost koridor Lord pit & river crossing',
                'image'  => asset('images/maps/flying_cloud.webp'),
                'type'   => 'cloud',
                'is_new' => false,
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
            'lastUpdatedDate',
            'lastUpdatedTime',
            'patchVersion',
            'topHeroes',
            'mapVariants'
        ));
    }
}

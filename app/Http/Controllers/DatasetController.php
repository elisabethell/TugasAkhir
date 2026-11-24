<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchDataset;
use App\Services\LiquipediaApiService;
use App\Services\CsvDataService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DatasetController extends Controller
{
    protected LiquipediaApiService $liquipediaService;
    protected CsvDataService $csvService;

    public function __construct(LiquipediaApiService $liquipediaService, CsvDataService $csvService)
    {
        $this->liquipediaService = $liquipediaService;
        $this->csvService = $csvService;
    }

    /**
     * Halaman Dataset Pertandingan (Publik & Terbuka untuk Umum)
     * Mengambil HANYA data riil dari database turnamen MPL ID Season 18.
     */
    public function index(Request $request)
    {
        $seriesMatches = collect();
        $matches = collect();
        $dbConnected = false;
        $dbErrorMessage = null;

        try {
            $hasDatabaseTable = Schema::hasTable('match_datasets') && Schema::hasTable('match_games');

            if ($hasDatabaseTable) {
                $dbConnected = true;

                // Pastikan pembeda babak Grand Final vs Lower Bracket Final tersimpan akurat
                MatchDataset::where('match_id', 'like', '%R03-M002%')->update(['stage_name' => 'Lower Bracket Final']);
                MatchDataset::where('match_id', 'like', '%R04-M001%')->update(['stage_name' => 'Grand Final Playoffs']);

                // Query murni relasi database: MatchDataset beserta games (hanya game valid yang dimainkan) dan details
                $matchesQuery = function() {
                    return MatchDataset::with(['games' => function($q) {
                        $q->where(function($sub) {
                            $sub->whereNotNull('team_1_picks')
                                ->where('team_1_picks', '!=', '[]')
                                ->where('team_1_picks', '!=', '');
                        })->orWhere(function($sub) {
                            $sub->whereNotNull('team_2_picks')
                                ->where('team_2_picks', '!=', '[]')
                                ->where('team_2_picks', '!=', '');
                        });
                    }, 'games.details'])->orderBy('match_date', 'desc')->get();
                };

                $matches = $matchesQuery();

                // Jika tabel masih kosong atau belum lengkap (<10 seri), sinkronkan dataset lengkap MPL ID Season 18 (215+ games)
                if ($matches->isEmpty() || $matches->count() < 10) {
                    try {
                        $this->liquipediaService->seedCompleteMplSeason18Dataset();
                        $matches = $matchesQuery();
                    } catch (\Throwable $seedEx) {
                        \Illuminate\Support\Facades\Log::warning('Gagal auto-seed match dataset: ' . $seedEx->getMessage());
                    }
                }
                $seriesMatches = $matches;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('DatasetController index DB error: ' . $e->getMessage());
            $dbConnected = false;
            $dbErrorMessage = $e->getMessage();
        }

        // Summary metrics
        $totalSeries = $matches->count();
        $totalGames = $matches->sum(fn($m) => $m->games ? $m->games->count() : 0);

        // Hitung pembagian Regular Season vs Playoffs
        $regularCount = $matches->filter(fn($m) => stripos($m->stage_name, 'regular') !== false)->count();
        $playoffsCount = $matches->filter(fn($m) => stripos($m->stage_name, 'playoff') !== false || stripos($m->stage_name, 'final') !== false)->count();

        $activeStage = strtolower($request->query('stage', 'regular'));
        if (!in_array($activeStage, ['regular', 'playoffs', 'all'])) {
            $activeStage = 'regular';
        }

        // Fallback CSV data untuk perbandingan historis
        $csvSummary = $this->csvService->getTournamentSummary();
        $heroPortraitsMap = $this->csvService->getCsvPortraitMap();

        return view('dataset_list', compact(
            'matches',
            'seriesMatches',
            'totalSeries',
            'totalGames',
            'regularCount',
            'playoffsCount',
            'activeStage',
            'csvSummary',
            'dbConnected',
            'dbErrorMessage',
            'heroPortraitsMap'
        ));
    }

    /**
     * Endpoint untuk Tombol Sinkronisasi Data Liquipedia di Panel Admin
     */
    public function syncLiquipedia(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Sesi login tidak sah.');
        }

        $pageName = $request->input('pagename', 'MPL/Indonesia/Season_18');
        $limit = (int) $request->input('limit', 1000);

        $result = $this->liquipediaService->syncTournamentMatches($pageName, $limit);

        if ($result['success']) {
            return redirect()->route('matches')->with('success', $result['message']);
        } else {
            return redirect()->route('matches')->with('error', $result['message']);
        }
    }

    /**
     * Endpoint Seed Mockup Match Alter Ego vs ONIC (Sesuai Screenshot User)
     */
    public function seedMockup()
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('login');
        }

        $this->liquipediaService->seedMockupSampleMatch();
        return redirect()->route('matches')->with('success', 'Sample data mockup Alter Ego 3 - 1 ONIC berhasil disinkronkan ke database lokal.');
    }
}

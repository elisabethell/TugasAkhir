<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchDataset;
use App\Services\LiquipediaApiService;
use App\Services\CsvDataService;
use Illuminate\Support\Facades\Schema;

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
     * Halaman Dataset Pertandingan (Khusus Admin setelah Login)
     */
    public function index()
    {
        // 1. Proteksi otentikasi admin: Hanya bisa diakses setelah login
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Akses terbatas. Anda harus login sebagai Admin untuk mengakses Dataset Pertandingan.');
        }

        $seriesMatches = collect();
        $dbConnected = false;
        $dbErrorMessage = null;

        try {
            $hasDatabaseTable = Schema::hasTable('match_datasets') && Schema::hasTable('match_games');

            if ($hasDatabaseTable) {
                $dbConnected = true;

                // Pastikan pembeda babak Grand Final vs Lower Bracket Final tersimpan akurat
                MatchDataset::where('match_id', 'like', '%R03-M002%')->update(['stage_name' => 'Lower Bracket Final']);
                MatchDataset::where('match_id', 'like', '%R04-M001%')->update(['stage_name' => 'Grand Final Playoffs']);

                // Ambil dari database lokal relasional (On-Demand Local Store)
                $seriesMatches = MatchDataset::with(['games.details'])->orderBy('match_date', 'desc')->get();

                // Jika tabel masih kosong, seed sample matchup Alter Ego vs ONIC (sesuai referensi mockup user)
                if ($seriesMatches->isEmpty()) {
                    try {
                        $this->liquipediaService->seedMockupSampleMatch();
                        $seriesMatches = MatchDataset::with(['games.details'])->orderBy('match_date', 'desc')->get();
                    } catch (\Throwable $seedEx) {
                        \Illuminate\Support\Facades\Log::warning('Gagal auto-seed mockup match: ' . $seedEx->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('DatasetController index DB error: ' . $e->getMessage());
            $dbConnected = false;
            $dbErrorMessage = $e->getMessage();
        }

        // Summary metrics
        $totalSeries = $seriesMatches->count();
        $totalGames = $seriesMatches->sum(fn($m) => $m->games ? $m->games->count() : 0);

        // Fallback CSV data untuk perbandingan historis
        $csvSummary = $this->csvService->getTournamentSummary();
        $heroPortraitsMap = $this->csvService->getCsvPortraitMap();

        return view('dataset_list', compact(
            'seriesMatches',
            'totalSeries',
            'totalGames',
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
        $limit = (int) $request->input('limit', 200);

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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvDataService;

class DatasetController extends Controller
{
    protected CsvDataService $csvService;

    public function __construct(CsvDataService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function index()
    {
        // Proteksi otentikasi admin: Hanya bisa diakses setelah login
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Akses terbatas. Anda harus login sebagai Admin untuk mengakses Dataset Pertandingan.');
        }

        // Ambil seluruh 69 pertandingan turnamen riil dari database/data/MWI_X_EWC_2026.csv
        $matches = $this->csvService->getMatches();
        $summary = $this->csvService->getTournamentSummary();

        $totalGames = $summary['totalGames'];
        $avgDuration = $summary['avgFormatted'];
        $fastestDuration = $summary['fastestFormatted'];
        $longestDuration = $summary['longestFormatted'];

        return view('dataset_list', compact(
            'matches',
            'totalGames',
            'avgDuration',
            'fastestDuration',
            'longestDuration'
        ));
    }
}
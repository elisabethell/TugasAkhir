<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvDataService;

class AdminController extends Controller
{
    protected CsvDataService $csvService;

    public function __construct(CsvDataService $csvService)
    {
        $this->csvService = $csvService;
    }

    /**
     * Dashboard Utama Admin Console (Protected - Hanya untuk Admin)
     */
    public function dashboard(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Akses terbatas. Anda harus login sebagai Admin untuk mengakses Dashboard Utama.');
        }

        $summary = $this->csvService->getTournamentSummary();
        $matches = $this->csvService->getMatches();
        $heroes = $this->csvService->getHeroes();

        $totalGames = $summary['totalGames'] ?: 69;
        $fastestDuration = $summary['fastestFormatted'] ?: '09:18';
        $longestDuration = $summary['longestFormatted'] ?: '24:45';
        $totalHeroes = 124; // Sesuai data konsol turnamen MWI 2026

        return view('admin_dashboard', compact(
            'matches',
            'summary',
            'totalGames',
            'fastestDuration',
            'longestDuration',
            'totalHeroes',
            'heroes'
        ));
    }

    /**
     * Manajemen Data Hero Admin Console (Protected - Hanya untuk Admin)
     */
    public function heroes(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Akses terbatas. Anda harus login sebagai Admin untuk mengakses Manajemen Data Hero.');
        }

        $heroes = $this->csvService->getHeroes()->sortBy('hero_name')->values();
        $tournamentStats = $this->csvService->getTournamentStatistics();
        $statsLookup = [];
        foreach ($tournamentStats as $stat) {
            $statsLookup[strtolower($stat['name'])] = $stat;
        }

        return view('admin_heroes', compact('heroes', 'statsLookup'));
    }
}

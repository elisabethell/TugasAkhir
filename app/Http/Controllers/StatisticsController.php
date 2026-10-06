<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvDataService;

class StatisticsController extends Controller
{
    protected CsvDataService $csvService;

    public function __construct(CsvDataService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function index()
    {
        // Ambil data statistik turnamen resmi dari MWI_X_EWC_2026.csv & data_hero.csv
        $heroRows = $this->csvService->getTournamentStatistics();
        $totalGames = count($this->csvService->getMatches()) ?: 69;

        return view('hero_statistics', compact('heroRows', 'totalGames'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvDataService;

class HeroController extends Controller
{
    protected CsvDataService $csvService;

    public function __construct(CsvDataService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function index()
    {
        // Ambil seluruh 133 hero langsung dari database/data/data_hero.csv
        // dengan resolusi foto dari public/images/heroes/ untuk Marcel, Hirara, Sora
        $heroes = $this->csvService->getHeroes()->sortBy('hero_name')->values();

        return view('hero_list', compact('heroes'));
    }
}
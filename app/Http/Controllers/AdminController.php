<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvDataService;
use App\Models\MatchDataset;
use App\Models\Hero;
use Illuminate\Support\Facades\Schema;

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

        $seriesMatches = collect();
        if (Schema::hasTable('match_datasets') && Schema::hasTable('match_games')) {
            try {
                // Pastikan pembeda babak Grand Final vs Lower Bracket Final tersimpan akurat
                MatchDataset::where('match_id', 'like', '%R03-M002%')->update(['stage_name' => 'Lower Bracket Final']);
                MatchDataset::where('match_id', 'like', '%R04-M001%')->update(['stage_name' => 'Grand Final Playoffs']);

                // Ambil data murni relasional dari database lokal
                $seriesMatches = MatchDataset::with(['games.details'])->orderBy('match_date', 'desc')->get();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('AdminController DB error: ' . $e->getMessage());
            }
        }

        $totalGames = $seriesMatches->isNotEmpty() 
            ? $seriesMatches->sum(fn($m) => $m->games ? $m->games->count() : 0)
            : ($summary['totalGames'] ?: ($matches->count() ?: 90));
            
        $fastestDuration = $summary['fastestFormatted'] ?: '11:40';
        $longestDuration = $summary['longestFormatted'] ?: '29:28';
        $totalHeroes = $heroes->count() ?: 133;
        $heroPortraitsMap = $this->csvService->getCsvPortraitMap();

        return view('admin_dashboard', compact(
            'matches',
            'seriesMatches',
            'summary',
            'totalGames',
            'fastestDuration',
            'longestDuration',
            'totalHeroes',
            'heroes',
            'heroPortraitsMap'
        ));
    }

    /**
     * Tabel RU (Read & Update) Atribut Hero
     * Menampilkan daftar hero resmi untuk penyesuaian atribut bila terjadi perubahan patch meta.
     */
    public function heroes(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Akses terbatas. Anda harus login sebagai Admin.');
        }

        // Pastikan tabel database heroes terisi jika kosong
        if (Schema::hasTable('heroes') && Hero::count() === 0) {
            try {
                (new \Database\Seeders\HeroSeeder())->run();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal auto-seed HeroSeeder: ' . $e->getMessage());
            }
        }

        $heroes = Hero::orderBy('hero_name', 'asc')->get();
        $heroPortraitsMap = $this->csvService->getCsvPortraitMap();

        return view('admin_heroes', compact('heroes', 'heroPortraitsMap'));
    }

    /**
     * Update Atribut Hero (Role, Lane, Specialty, Portrait)
     */
    public function updateHero(Request $request, $id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Sesi login admin tidak sah.');
        }

        $hero = Hero::findOrFail($id);

        $request->validate([
            'class'     => 'required|string|max:100',
            'laning'    => 'required|string|max:150',
            'specialty' => 'nullable|string|max:200',
            'portrait'  => 'nullable|string|max:500',
        ]);

        $hero->update([
            'class'     => $request->input('class'),
            'laning'    => $request->input('laning'),
            'specialty' => $request->input('specialty', $hero->specialty),
            'portrait'  => $request->input('portrait', $hero->portrait),
        ]);

        return redirect()->route('admin.heroes')->with('success', "Atribut hero {$hero->hero_name} berhasil diperbarui.");
    }
}

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\RbrRuleController;

/*
|--------------------------------------------------------------------------
| Web Routes - MetaScout: Land of Dawn
|--------------------------------------------------------------------------
| Sistem Analisis Data Turnamen Pro & Rekomendasi Draft MLBB
| Menggunakan Metode Rule-Based Reasoning (RBR)
*/

// 1. Homepage Utama (Sesuai Desain Mockup)
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Fitur Rekomendasi Draft & Analisis Gameplay (RBR Engine)
Route::get('/rekomendasi-draft', [DraftController::class, 'index'])->name('draft.analyzer');
Route::post('/api/analyze-draft', [DraftController::class, 'analyze'])->name('draft.analyze.api');

// 3. Statistik Pro Hero & Tier List (Liquipedia-Style Academic View)
Route::get('/statistics', [StatisticsController::class, 'index'])->name('hero.statistics');

// 4. Riwayat Pertandingan Seri Turnamen (Tournament Best-of Series)
Route::get('/datasets', [DatasetController::class, 'index'])->name('matches');

// 5. Kamus Data Hero
Route::get('/heroes', [HeroController::class, 'index'])->name('heroes');

// 6. Knowledge Base Aturan RBR (Transparansi Akademik)
Route::get('/rules', [RbrRuleController::class, 'index'])->name('rules');

// 7. Route Admin Placeholder
Route::get('/login', function() {
    return redirect()->route('home');
})->name('login');
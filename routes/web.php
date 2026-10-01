<?php

use Illuminate\Support\Facades\Route;
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

// 1. Homepage: Draft Recommendation & Gameplay Analyzer (Tanpa Login!)
Route::get('/', [DraftController::class, 'index'])->name('draft.analyzer');
Route::post('/api/analyze-draft', [DraftController::class, 'analyze'])->name('draft.analyze.api');

// 2. Statistik Hero Pro Turnamen (Liquipedia-Style Academic View)
Route::get('/statistics', [StatisticsController::class, 'index'])->name('hero.statistics');

// 3. Riwayat Pertandingan Turnamen (Tournament Series per Match)
Route::get('/datasets', [DatasetController::class, 'index'])->name('matches');

// 4. Kamus Data Hero
Route::get('/heroes', [HeroController::class, 'index'])->name('heroes');

// 5. Knowledge Base Aturan RBR (Transparansi Akademik)
Route::get('/rules', [RbrRuleController::class, 'index'])->name('rules');
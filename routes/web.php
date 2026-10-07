<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\RbrRuleController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes - MetaScout: Land of Dawn
|--------------------------------------------------------------------------
| Sistem Analisis Data Turnamen Pro & Rekomendasi Draft MLBB
| Menggunakan Metode Rule-Based Reasoning (RBR)
*/

// 1. Homepage Utama (Sesuai Desain Mockup)
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Counter Picks & Analisis Lawan (Pilih 1–5 Hero Musuh)
Route::get('/counter-picks', [DraftController::class, 'counterPicks'])->name('counter.picks');

// 3. Rekomendasi Draft (Pick & Ban Sinergi Tim & Counter Ancaman)
Route::get('/rekomendasi-draft', [DraftController::class, 'draftRecommendation'])->name('draft.analyzer');
Route::get('/draft-recommendation', [DraftController::class, 'draftRecommendation'])->name('draft.recommendation');
Route::post('/api/analyze-draft', [DraftController::class, 'analyze'])->name('draft.analyze.api');

// 4. Statistik Pro Hero & Tier List (Liquipedia-Style Academic View)
Route::get('/statistics', [StatisticsController::class, 'index'])->name('hero.statistics');

// 5. Kamus Data Hero
Route::get('/heroes', [HeroController::class, 'index'])->name('heroes');

// 6. Knowledge Base Aturan RBR (Transparansi Akademik)
Route::get('/rules', [RbrRuleController::class, 'index'])->name('rules');

// 7. Autentikasi Admin Console
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout.post');

// 8. Riwayat Pertandingan Seri Turnamen (KHUSUS ADMIN - Protected)
Route::get('/admin/datasets', [DatasetController::class, 'index'])->name('matches');
Route::get('/datasets', function() {
    return redirect()->route('matches');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\RbrRuleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes - MetaScout: Land of Dawn
|--------------------------------------------------------------------------
| Sistem Analisis Data Turnamen Pro & Rekomendasi Draft MLBB
| Menggunakan Metode Rule-Based Reasoning (RBR)
*/

// ==========================================
// 1. PUBLIC ROUTES (Dapat diakses tanpa login)
// ==========================================

// Landing Page Utama (Gateway & Pengenalan Sistem)
Route::get('/', [HomeController::class, 'landing'])->name('landing');

// Homepage / Dashboard (Ringkasan Data & Quick Access)
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

// Rekomendasi Draft (Pick & Ban Sinergi Tim & Counter Ancaman)
Route::get('/rekomendasi-draft', [DraftController::class, 'draftRecommendation'])->name('draft.recommendation');
Route::get('/draft-recommendation', [DraftController::class, 'draftRecommendation'])->name('draft.analyzer');
Route::post('/api/analyze-draft', [DraftController::class, 'analyze'])->name('draft.analyze.api');

// Draft DNA Tim Turnamen MPL ID S18 (Team-Specific Drafting Profile)
Route::get('/api/teams/{team}/dna', [DraftController::class, 'getTeamDraftDna'])->name('api.team.dna');
Route::get('/api/team-dna', [DraftController::class, 'getTeamDraftDna'])->name('api.team.dna.query');

// Match Mirip Turnamen MPL ID S18 (Similar Matches)
Route::get('/api/teams/{team}/similar-matches', [DraftController::class, 'getSimilarMatches'])->name('api.team.similar');
Route::get('/api/similar-matches', [DraftController::class, 'getSimilarMatches'])->name('api.team.similar.query');

// Counter Picks & Analisis Lawan (Dialihkan langsung ke Fitur Utama Rekomendasi Draft)
Route::redirect('/counter-picks', '/rekomendasi-draft', 301)->name('counter.picks');

// List Hero (Direktori Kamus Data Hero)
Route::get('/heroes', [HeroController::class, 'index'])->name('heroes');

// Statistik Hero (Statistik Turnamen Pro & Tier List)
Route::get('/statistics', [StatisticsController::class, 'index'])->name('hero.statistics');

// Knowledge Base Aturan RBR (Transparansi Akademik)
Route::get('/rules', [RbrRuleController::class, 'index'])->name('rules');

// Dataset Match (Daftar Pertandingan Turnamen Pro MPL ID Season 18 - Publik)
Route::get('/matches', [DatasetController::class, 'index'])->name('matches');


// ==========================================
// 2. AUTHENTICATION & ACCESS REQUEST ROUTES
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/admin/request-access', [AuthController::class, 'requestAccess'])->name('admin.request.access');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout.post');


// ==========================================
// 3. ADMIN ROUTES (HANYA BISA DIAKSES MELALUI LOGIN ADMIN)
// ==========================================
Route::prefix('admin')->middleware([\App\Http\Middleware\AdminAuthMiddleware::class])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/datasets/sync', [DatasetController::class, 'syncLiquipedia'])->name('admin.datasets.sync');
    Route::post('/datasets/seed-mockup', [DatasetController::class, 'seedMockup'])->name('admin.datasets.seed');
    // RU Table Hero (Read & Update Atribut Hero saat terjadi Patch Meta)
    Route::get('/heroes', [AdminController::class, 'heroes'])->name('admin.heroes');
    Route::post('/heroes/{id}/update', [AdminController::class, 'updateHero'])->name('admin.heroes.update');
});

// Redirects
Route::get('/admin', function() {
    return redirect()->route('admin.dashboard');
});
Route::get('/datasets', function() {
    return redirect()->route('matches');
});
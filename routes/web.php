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

// Homepage Utama
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rekomendasi Draft (Pick & Ban Sinergi Tim & Counter Ancaman)
Route::get('/rekomendasi-draft', [DraftController::class, 'draftRecommendation'])->name('draft.recommendation');
Route::get('/draft-recommendation', [DraftController::class, 'draftRecommendation'])->name('draft.analyzer');
Route::post('/api/analyze-draft', [DraftController::class, 'analyze'])->name('draft.analyze.api');

// Counter Picks & Analisis Lawan (Pilih 1–5 Hero Musuh)
Route::get('/counter-picks', [DraftController::class, 'counterPicks'])->name('counter.picks');

// List Hero (Direktori Kamus Data Hero)
Route::get('/heroes', [HeroController::class, 'index'])->name('heroes');

// Statistik Hero (Statistik Turnamen Pro & Tier List)
Route::get('/statistics', [StatisticsController::class, 'index'])->name('hero.statistics');

// Knowledge Base Aturan RBR (Transparansi Akademik)
Route::get('/rules', [RbrRuleController::class, 'index'])->name('rules');


// ==========================================
// 2. AUTHENTICATION ROUTES (Admin Login)
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout.post');


// ==========================================
// 3. ADMIN ROUTES (HANYA BISA DIAKSES MELALUI LOGIN ADMIN)
// ==========================================
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/datasets', [DatasetController::class, 'index'])->name('matches');
    Route::get('/heroes', [AdminController::class, 'heroes'])->name('admin.heroes');
});

// Redirects
Route::get('/admin', function() {
    return redirect()->route('admin.dashboard');
});
Route::get('/datasets', function() {
    return redirect()->route('matches');
});
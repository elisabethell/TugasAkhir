<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/hero-statistics', [DashboardController::class, 'heroStatistics'])->name('hero.statistics');
Route::get('/tier-list', [DashboardController::class, 'tierList'])->name('tier.list');
Route::get('/draft-analyzer', [DashboardController::class, 'draftAnalyzer'])->name('draft.analyzer');
Route::post('/draft-analyze', [DashboardController::class, 'analyzeDraft'])->name('draft.analyze');
Route::get('/hero/{name}', [DashboardController::class, 'heroDetail'])->name('hero.detail');
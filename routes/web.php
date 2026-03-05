<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

// Nama route harus pas dengan yang dipanggil di Navbar
Route::get('/', [AnalyticsController::class, 'dashboard'])->name('dashboard');
Route::get('/hero-statistics', [AnalyticsController::class, 'dashboard'])->name('hero.statistics');
Route::get('/tier-list', [AnalyticsController::class, 'tierList'])->name('tier.list');
Route::get('/draft-analyzer', [AnalyticsController::class, 'dashboard'])->name('draft.analyzer');
<?php

use App\Models\Hero;
use App\Models\MatchDataset;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('matches page is accessible by public without login', function () {
    $response = $this->get('/matches');

    $response->assertStatus(200);
    $response->assertSee('REGULAR SEASON');
    $response->assertSee('PLAYOFFS');
    $response->assertSee('DATASET PERTANDINGAN MATCH');
    // Sinkronisasi button should NOT be visible to public
    $response->assertDontSee('Sinkronisasi Data Liquipedia');
});

test('datasets route redirects to matches', function () {
    $response = $this->get('/datasets');
    $response->assertRedirect('/matches');
});

test('admin can see sync liquipedia button on matches page', function () {
    $response = $this->withSession(['admin_logged_in' => true])->get('/matches');

    $response->assertStatus(200);
    $response->assertSee('Sinkronisasi Data Liquipedia');
});

test('admin heroes page requires login', function () {
    $response = $this->get('/admin/heroes');
    $response->assertRedirect('/login');
});

test('admin can access hero ru table and update hero attributes', function () {
    // Pastikan minimal ada 1 hero
    $hero = Hero::first();
    if (!$hero) {
        $hero = Hero::create([
            'hero_id' => 'h999',
            'hero_name' => 'TestHero',
            'class' => 'Mage',
            'laning' => 'Mid Lane',
            'specialty' => 'Burst',
        ]);
    }

    $response = $this->withSession(['admin_logged_in' => true])->get('/admin/heroes');
    $response->assertStatus(200);
    $response->assertSee('ATRIBUT HERO');
    $response->assertSee($hero->hero_name);

    // Update hero attribute
    $updateResponse = $this->withSession(['admin_logged_in' => true])->post("/admin/heroes/{$hero->id}/update", [
        'class' => 'Fighter / Tank',
        'laning' => 'Exp Lane',
        'specialty' => 'Initiator, Crowd Control',
        'portrait' => $hero->portrait,
    ]);

    $updateResponse->assertRedirect('/admin/heroes');
    $updateResponse->assertSessionHas('success');

    $hero->refresh();
    expect($hero->class)->toBe('Fighter / Tank');
    expect($hero->laning)->toBe('Exp Lane');
});

test('modal header has right margin on stage badge to prevent overlap with close button', function () {
    $response = $this->get('/matches');
    $response->assertStatus(200);
    // Badge stage memiliki margin kanan mr-10 / mr-12 agar tidak tertimpa tombol close X
    $response->assertSee('mr-10 sm:mr-12');
});

test('matches page excludes ghost games with empty picks from games relation', function () {
    $match = MatchDataset::create([
        'match_id' => 'GHOST-CHECK-001',
        'tournament_name' => 'MPL Indonesia Season 18',
        'match_date' => now(),
        'team_1_name' => 'Team Alpha',
        'team_2_name' => 'Team Beta',
        'team_1_score' => 2,
        'team_2_score' => 0,
        'best_of' => 'Bo3',
        'stage_name' => 'Regular Season',
    ]);

    // Game 1: valid
    \App\Models\MatchGame::create([
        'match_id' => 'GHOST-CHECK-001',
        'match_dataset_id' => $match->id,
        'game_number' => 1,
        'winner' => 'Team Alpha',
        'winner_team' => 'Team Alpha',
        'team_1_picks' => ['Fanny', 'Miya'],
        'team_2_picks' => ['Tigreal', 'Nana'],
    ]);

    // Game 2: valid
    \App\Models\MatchGame::create([
        'match_id' => 'GHOST-CHECK-001',
        'match_dataset_id' => $match->id,
        'game_number' => 2,
        'winner' => 'Team Alpha',
        'winner_team' => 'Team Alpha',
        'team_1_picks' => ['Ling', 'Layla'],
        'team_2_picks' => ['Franco', 'Gord'],
    ]);

    // Game 3: ghost game (empty picks / unplayed)
    \App\Models\MatchGame::create([
        'match_id' => 'GHOST-CHECK-001',
        'match_dataset_id' => $match->id,
        'game_number' => 3,
        'team_1_picks' => [],
        'team_2_picks' => [],
    ]);

    $response = $this->get('/matches');
    $response->assertStatus(200);

    // Dapatkan data matches dari view
    $matches = $response->viewData('matches');
    $checkMatch = $matches->firstWhere('match_id', 'GHOST-CHECK-001');

    expect($checkMatch)->not->toBeNull();
    // Hanya 2 game yang lolos relasi (game 3 kosong diabaikan)
    expect($checkMatch->games->count())->toBe(2);
});


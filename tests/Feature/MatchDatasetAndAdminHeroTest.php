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

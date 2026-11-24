<?php

use App\Models\MatchDataset;
use App\Models\MatchGame;
use App\Models\MatchDetail;
use App\Services\LiquipediaApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('fallback palsu getMetaFallbackHeroes dan generatePlaceholderGames telah dihapus', function () {
    expect(method_exists(LiquipediaApiService::class, 'getMetaFallbackHeroes'))->toBeFalse()
        ->and(method_exists(LiquipediaApiService::class, 'generatePlaceholderGames'))->toBeFalse();
});

test('game dengan picks bans kosong menyimpan array kosong dan tidak mengarang hero', function () {
    $item = [
        'match2id' => 'empty-picks-match',
        'match2opponents' => [
            ['name' => 'Team Alpha'],
            ['name' => 'Team Beta'],
        ],
        'extradata' => [],
        'match2games' => [
            [
                'match2gameid' => 1,
                'winner' => 1,
                'length' => '15:20',
                'extradata' => [],
            ],
        ],
    ];

    app(LiquipediaApiService::class)->parseAndSaveMatchSeries($item);

    $game = MatchGame::firstWhere('match_id', 'empty-picks-match');
    expect($game)->not->toBeNull()
        ->and($game->team_1_picks)->toBe([])
        ->and($game->team_2_picks)->toBe([])
        ->and($game->team_1_bans)->toBe([])
        ->and($game->team_2_bans)->toBe([]);
});

test('halaman statistik hero menghitung win rate, pick rate, dan ban rate secara dinamis dari database', function () {
    // 1. Buat dataset dan match_games riil di database
    $dataset = MatchDataset::create([
        'match_id'        => 'STAT-TEST-001',
        'tournament_name' => 'MPL Indonesia Season 18',
        'match_date'      => now(),
        'team_1_name'     => 'Team Alpha',
        'team_2_name'     => 'Team Beta',
        'team_1_score'    => 2,
        'team_2_score'    => 0,
        'best_of'         => 'Bo3',
        'stage_name'      => 'Playoffs',
        'map_summary'     => 'Healing Turtle',
    ]);

    // Game 1: Team 1 Win
    MatchGame::create([
        'match_dataset_id' => $dataset->id,
        'match_id'         => 'STAT-TEST-001',
        'game_number'      => 1,
        'duration'         => '14:30',
        'map_type'         => 'Healing Turtle',
        'winner'           => 'Team Alpha',
        'winner_team'      => 'Team Alpha',
        'team_1_status'    => 'W',
        'team_2_status'    => 'L',
        'team_1_side'      => 'Blue',
        'team_2_side'      => 'Red',
        'team_1_picks'     => ['Freya', 'Zhuxin', 'Atlas', 'Nolan', 'Sora'],
        'team_2_picks'     => ['Alice', 'Paquito', 'Selena', 'Clint', 'Gloo'],
        'team_1_bans'      => ['Masha', 'Kalea', 'Marcel', 'Fanny', 'Yu Zhong'],
        'team_2_bans'      => ['Esmeralda', 'Lesley', 'Belerick', 'Chou', 'Bruno'],
    ]);

    // Game 2: Team 1 Win (Freya menang lagi, Alice kalah lagi)
    MatchGame::create([
        'match_dataset_id' => $dataset->id,
        'match_id'         => 'STAT-TEST-001',
        'game_number'      => 2,
        'duration'         => '16:10',
        'map_type'         => 'Revealing Wisps',
        'winner'           => 'Team Alpha',
        'winner_team'      => 'Team Alpha',
        'team_1_status'    => 'W',
        'team_2_status'    => 'L',
        'team_1_side'      => 'Red',
        'team_2_side'      => 'Blue',
        'team_1_picks'     => ['Freya', 'Lylia', 'Carmilla', 'Bruno', 'Phoveus'],
        'team_2_picks'     => ['Alice', 'Sora', 'Zhuxin', 'Brody', 'Atlas'],
        'team_1_bans'      => ['Masha', 'Kalea', 'Marcel', 'Nolan', 'Gloo'],
        'team_2_bans'      => ['Fanny', 'Yu Zhong', 'Esmeralda', 'Paquito', 'Selena'],
    ]);

    // Akses halaman statistik hero
    $response = $this->get('/statistics');
    $response->assertStatus(200);

    $heroRows = $response->viewData('heroRows');
    $totalGames = $response->viewData('totalGames');

    expect($totalGames)->toBe(2);

    $freya = collect($heroRows)->firstWhere('name', 'Freya');
    expect($freya)->not->toBeNull()
        ->and($freya['picks'])->toBe(2)
        ->and($freya['wins'])->toBe(2)
        ->and($freya['wr'])->toBe(100.0) // 2 wins / 2 picks = 100%
        ->and($freya['pick_rate'])->toBe(100.0); // 2 picks / 2 games = 100%

    $alice = collect($heroRows)->firstWhere('name', 'Alice');
    expect($alice)->not->toBeNull()
        ->and($alice['picks'])->toBe(2)
        ->and($alice['wins'])->toBe(0)
        ->and($alice['wr'])->toBe(0.0) // 0 wins / 2 picks = 0%
        ->and($alice['pick_rate'])->toBe(100.0);

    $masha = collect($heroRows)->firstWhere('name', 'Masha');
    expect($masha)->not->toBeNull()
        ->and($masha['bans'])->toBe(2)
        ->and($masha['ban_rate'])->toBe(100.0); // 2 bans / 2 games = 100%

    // Verifikasi data terurut dari Win Rate tertinggi (descending)
    $firstRow = $heroRows[0];
    expect($firstRow['wr'])->toBe(100.0);
});

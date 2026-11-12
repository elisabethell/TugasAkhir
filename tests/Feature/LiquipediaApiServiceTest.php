<?php

use App\Models\MatchDataset;
use App\Models\MatchGame;
use App\Services\LiquipediaApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Liquipedia array payload and map values are stored as JSON strings', function () {
    $item = [
        'match2id' => 'array-map-match',
        'match2opponents' => [
            ['name' => 'Team Alpha'],
            ['name' => 'Team Beta'],
        ],
        'extradata' => [
            'mvp' => [
                'players' => [
                    '1' => ['name' => 'Player One', 'team' => 'Team Alpha'],
                ],
                'points' => 1,
            ],
        ],
        'match2games' => [
            [
                'game' => 1,
                'winner' => 1,
                'map' => ['name' => 'Healing Turtle', 'variant' => 'Night'],
                'extradata' => [],
            ],
        ],
    ];

    app(LiquipediaApiService::class)->parseAndSaveMatchSeries($item);

    $dataset = MatchDataset::firstWhere('match_id', 'array-map-match');
    $game = MatchGame::firstWhere('match_id', 'array-map-match');

    expect(json_decode($dataset->raw_payload, true))->toBe($item)
        ->and(json_decode($dataset->series_mvp, true))->toBe($item['extradata']['mvp'])
        ->and(json_decode($dataset->map_summary, true))->toBe($item['match2games'][0]['map'])
        ->and(json_decode($game->map_type, true))->toBe($item['match2games'][0]['map']);
});

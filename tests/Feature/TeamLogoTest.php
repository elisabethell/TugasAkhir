<?php

test('getTeamLogo mappings match local PNG assets correctly', function () {
    $expected = [
        // EVOS
        'EVOS'                  => '/images/teams/evos.png',
        'EVOS Glory'            => '/images/teams/evos.png',
        'evos glory'            => '/images/teams/evos.png',
        // RRQ
        'RRQ'                   => '/images/teams/rrq.png',
        'RRQ Hoshi'             => '/images/teams/rrq.png',
        'rrq hoshi'             => '/images/teams/rrq.png',
        // NAVI
        'NAVI'                  => '/images/teams/navi.png',
        'Natus Vincere'         => '/images/teams/navi.png',
        'natus vincere'         => '/images/teams/navi.png',
        // AE
        'AE'                    => '/images/teams/ae.png',
        'Alter Ego'             => '/images/teams/ae.png',
        'alter ego esports'     => '/images/teams/ae.png',
        // TLID
        'TLID'                  => '/images/teams/tlid.png',
        'Team Liquid ID'        => '/images/teams/tlid.png',
        'team liquid'           => '/images/teams/tlid.png',
        'liquid'                => '/images/teams/tlid.png',
        // GEEK
        'GEEK'                  => '/images/teams/geek.png',
        'Geek Fam'              => '/images/teams/geek.png',
        'geek fam id'           => '/images/teams/geek.png',
        // DEWA
        'DEWA'                  => '/images/teams/dewa.png',
        'Dewa United'           => '/images/teams/dewa.png',
        'dewa united esports'   => '/images/teams/dewa.png',
        // BTR
        'BTR'                   => '/images/teams/btr.png',
        'Bigetron'              => '/images/teams/btr.png',
        'Bigetron by Vitality'  => '/images/teams/btr.png',
        'bigetron by vitality'  => '/images/teams/btr.png',
        // ONIC
        'ONIC'                  => '/images/teams/onic.png',
        'ONIC Esports'          => '/images/teams/onic.png',
        'fnatic onic'           => '/images/teams/onic.png',
    ];

    foreach ($expected as $team => $path) {
        expect(getTeamLogo($team))->toBe($path)
            ->and(str_ends_with(getTeamLogo($team), '.svg'))->toBeFalse()
            ->and(str_contains(getTeamLogo($team), 'ui-avatars'))->toBeFalse();

        // Pastikan file PNG lokal benar-benar ada di public disk
        $localFile = public_path(ltrim($path, '/'));
        expect(file_exists($localFile))->toBeTrue("File {$localFile} harus ada di public disk");
    }
});

test('MatchDataset returns clean PNG logo via accessor', function () {
    $match = new \App\Models\MatchDataset([
        'team_1_name' => 'Bigetron by Vitality',
        'team_2_name' => 'Natus Vincere',
    ]);

    expect($match->team_1_logo)->toBe('/images/teams/btr.png')
        ->and($match->team_2_logo)->toBe('/images/teams/navi.png');
});

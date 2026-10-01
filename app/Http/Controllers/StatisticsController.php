<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dataset;
use App\Models\Hero;

class StatisticsController extends Controller
{
    public function index()
    {
        $allData = Dataset::all();
        $totalGames = 69;

        // 1. Hero Portraits Mapping
        $heroes = Hero::all()->keyBy(function($h) {
            return preg_replace('/[^a-z0-9]/', '', strtolower($h->hero_name));
        });

        $heroPortraits = [];
        foreach ($heroes as $key => $h) {
            if (!empty($h->portrait) && str_starts_with($h->portrait, 'http') && !str_contains($h->portrait, 'deviantart') && !str_contains($h->portrait, 'mobilelegends.com')) {
                $heroPortraits[$key] = $h->portrait;
            } else {
                $localFile = strtolower(str_replace(' ', '_', $h->hero_name)) . '.png';
                if (file_exists(public_path('images/heroes/' . $localFile))) {
                    $heroPortraits[$key] = asset('images/heroes/' . $localFile);
                } else {
                    $heroPortraits[$key] = $h->portrait;
                }
            }
        }

        // 2. Hitung statistik hero dari Dataset turnamen resmi
        $stats = [];
        $blueSideWins = 0;
        $redSideWins = 0;

        foreach ($allData as $row) {
            $heroName = trim($row->hero);
            $cleanKey = preg_replace('/[^a-z0-9]/', '', strtolower($heroName));

            if (!isset($stats[$cleanKey])) {
                $hModel = $heroes->get($cleanKey);
                $stats[$cleanKey] = [
                    'name'        => $heroName,
                    'portrait'    => $heroPortraits[$cleanKey] ?? asset('images/heroes/' . strtolower(str_replace(' ', '_', $heroName)) . '.png'),
                    'class'       => $hModel ? $hModel->class : '-',
                    'laning'      => $hModel ? $hModel->laning : '-',
                    'picks'       => 0,
                    'wins'        => 0,
                    'losses'      => 0,
                    'blue_picks'  => 0,
                    'blue_wins'   => 0,
                    'blue_losses' => 0,
                    'red_picks'   => 0,
                    'red_wins'    => 0,
                    'red_losses'  => 0,
                    'bans'        => 0,
                ];
            }

            $isWin = $row->win_lose === 'Win';
            $stats[$cleanKey]['picks']++;
            if ($isWin) {
                $stats[$cleanKey]['wins']++;
            } else {
                $stats[$cleanKey]['losses']++;
            }

            if ($row->side === 'Blue') {
                $stats[$cleanKey]['blue_picks']++;
                if ($isWin) {
                    $stats[$cleanKey]['blue_wins']++;
                } else {
                    $stats[$cleanKey]['blue_losses']++;
                }
            } else {
                $stats[$cleanKey]['red_picks']++;
                if ($isWin) {
                    $stats[$cleanKey]['red_wins']++;
                } else {
                    $stats[$cleanKey]['red_losses']++;
                }
            }
        }

        // 3. Hitung bans per game
        for ($i = 0; $i < count($allData); $i += 10) {
            $gameChunk = $allData->slice($i, 10);
            $gameBans = $gameChunk->pluck('hero_ban')->filter()->unique();
            foreach ($gameBans as $b) {
                $bName = trim($b);
                $bKey = preg_replace('/[^a-z0-9]/', '', strtolower($bName));
                if (!isset($stats[$bKey])) {
                    $hModel = $heroes->get($bKey);
                    $stats[$bKey] = [
                        'name'        => $bName,
                        'portrait'    => $heroPortraits[$bKey] ?? asset('images/heroes/' . strtolower(str_replace(' ', '_', $bName)) . '.png'),
                        'class'       => $hModel ? $hModel->class : '-',
                        'laning'      => $hModel ? $hModel->laning : '-',
                        'picks'       => 0,
                        'wins'        => 0,
                        'losses'      => 0,
                        'blue_picks'  => 0,
                        'blue_wins'   => 0,
                        'blue_losses' => 0,
                        'red_picks'   => 0,
                        'red_wins'    => 0,
                        'red_losses'  => 0,
                        'bans'        => 0,
                    ];
                }
                $stats[$bKey]['bans']++;
            }

            // Hitung total kemenangan per side game
            $blueWinRow = $gameChunk->where('side', 'Blue')->where('win_lose', 'Win')->first();
            if ($blueWinRow) {
                $blueSideWins++;
            } else {
                $redSideWins++;
            }
        }

        // 4. Hitung persentase & contested metrics
        foreach ($stats as &$s) {
            $s['wr'] = $s['picks'] > 0 ? round(($s['wins'] / $s['picks']) * 100, 2) : 0;
            $s['pick_rate'] = round(($s['picks'] / $totalGames) * 100, 2);
            $s['blue_wr'] = $s['blue_picks'] > 0 ? round(($s['blue_wins'] / $s['blue_picks']) * 100, 2) : 0;
            $s['red_wr'] = $s['red_picks'] > 0 ? round(($s['red_wins'] / $s['red_picks']) * 100, 2) : 0;
            $s['ban_rate'] = round(($s['bans'] / $totalGames) * 100, 2);
            $s['contest_count'] = $s['picks'] + $s['bans'];
            $s['contest_rate'] = round(($s['contest_count'] / $totalGames) * 100, 2);
        }
        unset($s);

        // Urutkan berdasarkan total pick terbanyak (seperti Liquipedia)
        uasort($stats, fn($a, $b) => $b['picks'] <=> $a['picks'] ?: $b['bans'] <=> $a['bans']);

        $summary = [
            'total_games'   => $totalGames,
            'blue_wins'     => $blueSideWins,
            'blue_losses'   => $totalGames - $blueSideWins,
            'blue_wr'       => round(($blueSideWins / $totalGames) * 100, 2),
            'red_wins'      => $redSideWins,
            'red_losses'    => $totalGames - $redSideWins,
            'red_wr'        => round(($redSideWins / $totalGames) * 100, 2),
        ];

        return view('hero_statistics', compact('stats', 'summary'));
    }
}

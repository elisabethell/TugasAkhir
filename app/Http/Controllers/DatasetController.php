<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dataset;
use App\Models\Hero;

class DatasetController extends Controller
{
    public function index()
    {
        // 1. Ambil data kamus hero untuk mapping portrait (URL resmi Moonton & file lokal untuk Sora, Marcel, Hirara)
        $heroes = Hero::all();
        $heroPortraits = [];
        foreach ($heroes as $h) {
            $cleanKey = preg_replace('/[^a-z0-9]/', '', strtolower($h->hero_name));
            
            if (!empty($h->portrait) && str_starts_with($h->portrait, 'http') && !str_contains($h->portrait, 'deviantart') && !str_contains($h->portrait, 'mobilelegends.com')) {
                $heroPortraits[$cleanKey] = $h->portrait;
            } else {
                $localFile = strtolower(str_replace(' ', '_', $h->hero_name)) . '.png';
                if (file_exists(public_path('images/heroes/' . $localFile))) {
                    $heroPortraits[$cleanKey] = asset('images/heroes/' . $localFile);
                } else {
                    $heroPortraits[$cleanKey] = $h->portrait;
                }
            }
        }

        // 2. Ambil semua dataset pertandingan
        $allData = Dataset::all();

        // 3. Kelompokkan setiap 10 baris pemain menjadi 1 pertandingan (Game) utuh
        $games = [];
        for ($i = 0; $i < count($allData); $i += 10) {
            $chunk = $allData->slice($i, 10)->values();
            if (count($chunk) === 10) {
                $games[] = $chunk;
            }
        }

        // 4. Kelompokkan Game ke dalam Series Pertandingan (Tanggal + Bracket + Pasangan Tim)
        $seriesList = [];
        foreach ($games as $gRows) {
            $sample = $gRows->first();
            $teams = [trim($sample->team), trim($sample->opponent)];
            sort($teams);
            $pair = implode('_vs_', $teams);
            $seriesKey = $sample->date . '_' . $sample->bracket . '_' . $pair;

            if (!isset($seriesList[$seriesKey])) {
                $seriesList[$seriesKey] = [
                    'key'     => $seriesKey,
                    'date'    => $sample->date,
                    'bracket' => $sample->bracket,
                    'team1'   => $sample->team,
                    'team2'   => $sample->opponent,
                    'games'   => [],
                ];
            }
            $seriesList[$seriesKey]['games'][] = $gRows;
        }

        // 5. Hitung skor agregat akhir seri (misal 2 - 0, 2 - 1) dan tentukan pemenang seri
        foreach ($seriesList as $key => &$series) {
            $t1 = $series['team1'];
            $t2 = $series['team2'];
            $score1 = 0;
            $score2 = 0;

            foreach ($series['games'] as $g) {
                $winRow = $g->where('win_lose', 'Win')->first();
                if ($winRow) {
                    if ($winRow->team === $t1) {
                        $score1++;
                    } else {
                        $score2++;
                    }
                }
            }

            // Tampilkan tim pemenang seri di sisi pertama agar ringkasan jelas
            if ($score2 > $score1) {
                $series['team1'] = $t2;
                $series['team2'] = $t1;
                $series['score1'] = $score2;
                $series['score2'] = $score1;
                $series['winner'] = $t2;
            } else {
                $series['score1'] = $score1;
                $series['score2'] = $score2;
                $series['winner'] = $t1;
            }
        }
        unset($series);

        $seriesList = array_values($seriesList);

        return view('dataset_list', compact('seriesList', 'heroPortraits'));
    }
}
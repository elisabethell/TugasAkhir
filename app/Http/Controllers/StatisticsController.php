<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvDataService;
use App\Models\MatchGame;

class StatisticsController extends Controller
{
    protected CsvDataService $csvService;

    public function __construct(CsvDataService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function index()
    {
        // 1. Ambil katalog seluruh 133 hero resmi dari data_hero.csv
        $heroes = $this->csvService->getHeroes();

        // 2. Hitung statistik 100% dinamis langsung dari tabel match_games / match_details di database
        $totalGames = MatchGame::count();

        $statsMap = [];
        foreach ($heroes as $h) {
            $key = $h->clean_key;
            $statsMap[$key] = [
                'name'        => $h->hero_name,
                'clean_key'   => $key,
                'portrait'    => $h->portrait,
                'role'        => $h->class ?: ($h->primary_class ?: 'Hero'),
                'primary_role'=> $h->primary_class ?: 'Hero',
                'lane'        => $h->lanes_array ?? [],
                'speciality'  => $h->specialties ?? [],
                'picks'       => 0,
                'wins'        => 0,
                'losses'      => 0,
                'bans'        => 0,
                'wr'          => 0.0,
                'pick_rate'   => 0.0,
                'ban_rate'    => 0.0,
                'wr_tag'      => 'LOW',
                'pick_tag'    => 'RARE',
                'ban_tag'     => 'LOW',
                'initial'     => strtoupper(substr($h->hero_name, 0, 1)),
                'initial_bg'  => 'bg-[#FCECEE] text-[#700B1A]',
            ];
        }

        if ($totalGames > 0) {
            $games = MatchGame::with('details')->get();

            foreach ($games as $game) {
                $t1Win = ($game->team_1_status === 'W');
                $t2Win = ($game->team_2_status === 'W');

                // Ekstraksi picks & bans per game
                $t1Picks = is_array($game->team_1_picks) ? $game->team_1_picks : (json_decode($game->team_1_picks, true) ?: []);
                $t2Picks = is_array($game->team_2_picks) ? $game->team_2_picks : (json_decode($game->team_2_picks, true) ?: []);
                $t1Bans  = is_array($game->team_1_bans) ? $game->team_1_bans : (json_decode($game->team_1_bans, true) ?: []);
                $t2Bans  = is_array($game->team_2_bans) ? $game->team_2_bans : (json_decode($game->team_2_bans, true) ?: []);

                // Jika kolom JSON kosong, gunakan relasi match_details
                if (empty($t1Picks) && $game->relationLoaded('details') && $game->details->isNotEmpty()) {
                    $t1Picks = $game->details->where('team_position', 1)->where('type', 'pick')->pluck('hero_name')->all();
                    $t2Picks = $game->details->where('team_position', 2)->where('type', 'pick')->pluck('hero_name')->all();
                    $t1Bans  = $game->details->where('team_position', 1)->where('type', 'ban')->pluck('hero_name')->all();
                    $t2Bans  = $game->details->where('team_position', 2)->where('type', 'ban')->pluck('hero_name')->all();
                }

                foreach ($t1Picks as $hName) {
                    $k = preg_replace('/[^a-z0-9]/', '', strtolower($hName));
                    if (!isset($statsMap[$k])) {
                        $statsMap[$k] = $this->createEmptyStatItem($hName);
                    }
                    $statsMap[$k]['picks']++;
                    if ($t1Win) {
                        $statsMap[$k]['wins']++;
                    } else {
                        $statsMap[$k]['losses']++;
                    }
                }

                foreach ($t2Picks as $hName) {
                    $k = preg_replace('/[^a-z0-9]/', '', strtolower($hName));
                    if (!isset($statsMap[$k])) {
                        $statsMap[$k] = $this->createEmptyStatItem($hName);
                    }
                    $statsMap[$k]['picks']++;
                    if ($t2Win) {
                        $statsMap[$k]['wins']++;
                    } else {
                        $statsMap[$k]['losses']++;
                    }
                }

                foreach (array_merge($t1Bans, $t2Bans) as $hName) {
                    $k = preg_replace('/[^a-z0-9]/', '', strtolower($hName));
                    if (!isset($statsMap[$k])) {
                        $statsMap[$k] = $this->createEmptyStatItem($hName);
                    }
                    $statsMap[$k]['bans']++;
                }
            }

            // Hitung rate: WR, Pick Rate, Ban Rate 100% dari database
            foreach ($statsMap as &$s) {
                $s['wr'] = $s['picks'] > 0 ? round(($s['wins'] / $s['picks']) * 100, 2) : 0.0;
                $s['pick_rate'] = round(($s['picks'] / $totalGames) * 100, 2);
                $s['ban_rate'] = round(($s['bans'] / $totalGames) * 100, 2);

                $s['wr_tag'] = $s['wr'] >= 58 ? 'HIGH' : ($s['wr'] >= 50 ? 'AVERAGE' : 'LOW');
                $s['pick_tag'] = match (true) {
                    $s['pick_rate'] >= 30 => 'CORE PICK',
                    $s['pick_rate'] >= 20 => 'POPULAR',
                    $s['pick_rate'] >= 15 => 'STANDARD',
                    $s['pick_rate'] >= 5  => 'CONTESTED',
                    $s['pick_rate'] >= 1  => 'POCKET PICK',
                    default               => 'RARE',
                };
                $s['ban_tag'] = match (true) {
                    $s['ban_rate'] >= 50 => 'OFTEN BANNED',
                    $s['ban_rate'] >= 20 => 'MODERATE',
                    default              => 'LOW',
                };
            }
            unset($s);
        }

        // 3. Sorting default: WIN RATE tertinggi descending (disertai tie-breaker picks & bans)
        $heroRows = array_values($statsMap);
        usort($heroRows, function ($a, $b) {
            if ($a['wr'] != $b['wr']) {
                return $b['wr'] <=> $a['wr'];
            }
            if ($a['picks'] != $b['picks']) {
                return $b['picks'] <=> $a['picks'];
            }
            if ($a['bans'] != $b['bans']) {
                return $b['bans'] <=> $a['bans'];
            }
            return strcmp($a['name'], $b['name']);
        });

        // Highlight hero peringkat 1
        if (!empty($heroRows)) {
            $heroRows[0]['initial_bg'] = 'bg-[#700B1A] text-white';
        }

        // Cari hero untuk insight cards
        $topBanHero = collect($heroRows)->sortByDesc('ban_rate')->first();
        $topPickHero = collect($heroRows)->sortByDesc('pick_rate')->first();
        $topWrHero = collect($heroRows)->where('picks', '>=', 2)->sortByDesc('wr')->first() ?? ($heroRows[0] ?? null);

        return view('hero_statistics', compact('heroRows', 'totalGames', 'topBanHero', 'topPickHero', 'topWrHero'));
    }

    protected function createEmptyStatItem(string $heroName): array
    {
        $portrait = $this->csvService->resolveHeroPortrait($heroName);
        return [
            'name'        => trim($heroName),
            'clean_key'   => preg_replace('/[^a-z0-9]/', '', strtolower($heroName)),
            'portrait'    => $portrait,
            'role'        => 'Hero',
            'primary_role'=> 'Hero',
            'lane'        => ['Mid'],
            'speciality'  => ['Damage'],
            'picks'       => 0,
            'wins'        => 0,
            'losses'      => 0,
            'bans'        => 0,
            'wr'          => 0.0,
            'pick_rate'   => 0.0,
            'ban_rate'    => 0.0,
            'wr_tag'      => 'LOW',
            'pick_tag'    => 'RARE',
            'ban_tag'     => 'LOW',
            'initial'     => strtoupper(substr(trim($heroName), 0, 1)),
            'initial_bg'  => 'bg-[#FCECEE] text-[#700B1A]',
        ];
    }
}

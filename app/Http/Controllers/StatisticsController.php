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

    public function index(Request $request)
    {
        // 1. Ambil katalog seluruh 133 hero resmi dari data_hero.csv
        $heroes = $this->csvService->getHeroes();

        // 2. Filter stage pertandingan (Overall / Regular Season / Playoffs) sesuai Liquipedia
        $activeStage = strtolower((string) $request->query('stage', 'overall'));
        if (!in_array($activeStage, ['overall', 'regular', 'playoffs'])) {
            $activeStage = 'overall';
        }

        // Hitung jumlah game valid per kategori stage
        $baseValidQuery = MatchGame::whereNotNull('team_1_picks')
            ->where('team_1_picks', '!=', '[]')
            ->where('team_1_picks', '!=', '');

        $totalOverallGames = (clone $baseValidQuery)->count();
        $totalRegularGames = (clone $baseValidQuery)->whereHas('dataset', fn($q) => $q->where('stage_name', 'like', '%Regular%'))->count();
        $totalPlayoffGames = (clone $baseValidQuery)->whereHas('dataset', fn($q) => $q->where('stage_name', 'like', '%Playoff%'))->count();

        // Query game yang aktif sesuai tab pilihan
        $validGamesQuery = clone $baseValidQuery;
        if ($activeStage === 'regular') {
            $validGamesQuery->whereHas('dataset', fn($q) => $q->where('stage_name', 'like', '%Regular%'));
            $totalGames = $totalRegularGames;
        } elseif ($activeStage === 'playoffs') {
            $validGamesQuery->whereHas('dataset', fn($q) => $q->where('stage_name', 'like', '%Playoff%'));
            $totalGames = $totalPlayoffGames;
        } else {
            $totalGames = $totalOverallGames;
        }

        $statsMap = [];
        foreach ($heroes as $h) {
            $key = $h->clean_key;
            $statsMap[$key] = [
                'name'            => $h->hero_name,
                'clean_key'       => $key,
                'portrait'        => $h->portrait,
                'role'            => $h->class ?: ($h->primary_class ?: 'Hero'),
                'primary_role'    => $h->primary_class ?: 'Hero',
                'lane'            => $h->lanes_array ?? [],
                'speciality'      => $h->specialties ?? [],
                'picks'           => 0,
                'wins'            => 0,
                'losses'          => 0,
                'wr'              => 0.0,
                'pick_rate'       => 0.0,
                'blue_picks'      => 0,
                'blue_wins'       => 0,
                'blue_losses'     => 0,
                'blue_wr'         => 0.0,
                'red_picks'       => 0,
                'red_wins'        => 0,
                'red_losses'      => 0,
                'red_wr'          => 0.0,
                'bans'            => 0,
                'ban_rate'        => 0.0,
                'picks_bans'      => 0,
                'picks_bans_rate' => 0.0,
                'wr_tag'          => 'LOW',
                'pick_tag'        => 'RARE',
                'ban_tag'         => 'LOW',
                'initial'         => strtoupper(substr($h->hero_name, 0, 1)),
                'initial_bg'      => 'bg-[#FCECEE] text-[#700B1A]',
            ];
        }

        if ($totalGames > 0) {
            $games = $validGamesQuery->with('details')->get();

            foreach ($games as $game) {
                $t1Win = ($game->team_1_status === 'W');
                $t2Win = ($game->team_2_status === 'W');
                $t1Side = $game->team_1_side ?: 'Blue';
                $t2Side = $game->team_2_side ?: ($t1Side === 'Blue' ? 'Red' : 'Blue');

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

                // Team 1 Picks
                foreach ($t1Picks as $hName) {
                    $k = preg_replace('/[^a-z0-9]/', '', strtolower($hName));
                    if (!isset($statsMap[$k])) {
                        $statsMap[$k] = $this->createEmptyStatItem($hName);
                    }
                    $statsMap[$k]['picks']++;
                    if ($t1Win) {
                        $statsMap[$k]['wins']++;
                        if ($t1Side === 'Blue') $statsMap[$k]['blue_wins']++; else $statsMap[$k]['red_wins']++;
                    } else {
                        $statsMap[$k]['losses']++;
                        if ($t1Side === 'Blue') $statsMap[$k]['blue_losses']++; else $statsMap[$k]['red_losses']++;
                    }
                    if ($t1Side === 'Blue') $statsMap[$k]['blue_picks']++; else $statsMap[$k]['red_picks']++;
                }

                // Team 2 Picks
                foreach ($t2Picks as $hName) {
                    $k = preg_replace('/[^a-z0-9]/', '', strtolower($hName));
                    if (!isset($statsMap[$k])) {
                        $statsMap[$k] = $this->createEmptyStatItem($hName);
                    }
                    $statsMap[$k]['picks']++;
                    if ($t2Win) {
                        $statsMap[$k]['wins']++;
                        if ($t2Side === 'Blue') $statsMap[$k]['blue_wins']++; else $statsMap[$k]['red_wins']++;
                    } else {
                        $statsMap[$k]['losses']++;
                        if ($t2Side === 'Blue') $statsMap[$k]['blue_losses']++; else $statsMap[$k]['red_losses']++;
                    }
                    if ($t2Side === 'Blue') $statsMap[$k]['blue_picks']++; else $statsMap[$k]['red_picks']++;
                }

                // Bans (Team 1 & Team 2)
                foreach (array_merge($t1Bans, $t2Bans) as $hName) {
                    $k = preg_replace('/[^a-z0-9]/', '', strtolower($hName));
                    if (!isset($statsMap[$k])) {
                        $statsMap[$k] = $this->createEmptyStatItem($hName);
                    }
                    $statsMap[$k]['bans']++;
                }
            }

            // Hitung rate: WR, Pick Rate, Blue WR, Red WR, Ban Rate, P&B Rate 100% dari database
            foreach ($statsMap as &$s) {
                $s['wr'] = $s['picks'] > 0 ? round(($s['wins'] / $s['picks']) * 100, 2) : 0.0;
                $s['pick_rate'] = round(($s['picks'] / $totalGames) * 100, 2);
                $s['blue_wr'] = $s['blue_picks'] > 0 ? round(($s['blue_wins'] / $s['blue_picks']) * 100, 2) : 0.0;
                $s['red_wr'] = $s['red_picks'] > 0 ? round(($s['red_wins'] / $s['red_picks']) * 100, 2) : 0.0;
                $s['ban_rate'] = round(($s['bans'] / $totalGames) * 100, 2);
                $s['picks_bans'] = $s['picks'] + $s['bans'];
                $s['picks_bans_rate'] = round(($s['picks_bans'] / $totalGames) * 100, 2);

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

        // 3. Sorting default Liquipedia: PICKS (Σ) tertinggi descending (disertai tie-breaker wr, bans, name)
        $heroRows = array_values($statsMap);
        usort($heroRows, function ($a, $b) {
            if ($a['picks'] !== $b['picks']) {
                return $b['picks'] <=> $a['picks'];
            }
            if ($a['wr'] != $b['wr']) {
                return $b['wr'] <=> $a['wr'];
            }
            if ($a['bans'] !== $b['bans']) {
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
        // Win rate dominan: prioritaskan hero dengan sample pick representatif (>= 10 game atau tertinggi)
        $topWrHero = collect($heroRows)->where('picks', '>=', min(10, max(2, (int)($totalGames * 0.05))))->sortByDesc('wr')->first() 
            ?? collect($heroRows)->where('picks', '>=', 2)->sortByDesc('wr')->first() 
            ?? ($heroRows[0] ?? null);

        return view('hero_statistics', compact(
            'heroRows',
            'totalGames',
            'activeStage',
            'totalOverallGames',
            'totalRegularGames',
            'totalPlayoffGames',
            'topBanHero',
            'topPickHero',
            'topWrHero'
        ));
    }

    protected function createEmptyStatItem(string $heroName): array
    {
        $portrait = $this->csvService->resolveHeroPortrait($heroName);
        return [
            'name'            => trim($heroName),
            'clean_key'       => preg_replace('/[^a-z0-9]/', '', strtolower($heroName)),
            'portrait'        => $portrait,
            'role'            => 'Hero',
            'primary_role'    => 'Hero',
            'lane'            => ['Mid'],
            'speciality'      => ['Damage'],
            'picks'           => 0,
            'wins'            => 0,
            'losses'          => 0,
            'wr'              => 0.0,
            'pick_rate'       => 0.0,
            'blue_picks'      => 0,
            'blue_wins'       => 0,
            'blue_losses'     => 0,
            'blue_wr'         => 0.0,
            'red_picks'       => 0,
            'red_wins'        => 0,
            'red_losses'      => 0,
            'red_wr'          => 0.0,
            'bans'            => 0,
            'ban_rate'        => 0.0,
            'picks_bans'      => 0,
            'picks_bans_rate' => 0.0,
            'wr_tag'          => 'LOW',
            'pick_tag'        => 'RARE',
            'ban_tag'         => 'LOW',
            'initial'         => strtoupper(substr(trim($heroName), 0, 1)),
            'initial_bg'      => 'bg-[#FCECEE] text-[#700B1A]',
        ];
    }
}

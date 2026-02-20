<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Hero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    /**
     * Dashboard utama - menampilkan overview meta terkini
     */
    public function index()
    {
        // Meta data terkini
        $currentPatch = '1.8.92';
        $lastUpdated = '21 Feb 2026';
        
        // Global Stats
        $globalStats = [
            'total_heroes' => Hero::count(),
            'total_matches' => 15420,
            'avg_game_duration' => '18:45',
            'most_picked_role' => 'Fighter',
            'most_banned_hero' => 'Joy',
            'highest_win_rate' => 'Phoveus (58.5%)'
        ];
        
        // Top heroes by role (ambil dari database)
        $topByRole = [];
        $roles = ['fighter', 'assassin', 'mage', 'marksman', 'tank', 'support'];
        foreach ($roles as $role) {
            $hero = Hero::where('role_1', $role)->inRandomOrder()->first();
            if ($hero) {
                $topByRole[ucfirst($role)] = $hero;
            }
        }
        
        // Hero Stats untuk tabel (dummy data - nanti dari API)
        $heroStats = [
            ['hero' => 'Zhuxin', 'role' => 'Mage', 'picks' => 1250, 'bans' => 845, 'win_rate' => 53.2, 'pick_rate' => 18.5, 'ban_rate' => 12.4, 'tier' => 'S'],
            ['hero' => 'Granger', 'role' => 'Marksman', 'picks' => 1180, 'bans' => 520, 'win_rate' => 52.7, 'pick_rate' => 17.2, 'ban_rate' => 7.8, 'tier' => 'S'],
            ['hero' => 'Cici', 'role' => 'Fighter', 'picks' => 1120, 'bans' => 890, 'win_rate' => 54.1, 'pick_rate' => 16.8, 'ban_rate' => 13.2, 'tier' => 'S'],
            ['hero' => 'Ling', 'role' => 'Assassin', 'picks' => 1080, 'bans' => 950, 'win_rate' => 55.3, 'pick_rate' => 15.9, 'ban_rate' => 14.1, 'tier' => 'S'],
            ['hero' => 'Joy', 'role' => 'Assassin', 'picks' => 980, 'bans' => 1120, 'win_rate' => 52.8, 'pick_rate' => 14.2, 'ban_rate' => 16.5, 'tier' => 'A'],
            ['hero' => 'Arlott', 'role' => 'Fighter', 'picks' => 950, 'bans' => 780, 'win_rate' => 53.5, 'pick_rate' => 13.8, 'ban_rate' => 11.4, 'tier' => 'A'],
            ['hero' => 'Valentina', 'role' => 'Mage', 'picks' => 890, 'bans' => 820, 'win_rate' => 51.9, 'pick_rate' => 12.7, 'ban_rate' => 12.1, 'tier' => 'A'],
            ['hero' => 'Atlas', 'role' => 'Tank', 'picks' => 850, 'bans' => 450, 'win_rate' => 54.2, 'pick_rate' => 12.1, 'ban_rate' => 6.7, 'tier' => 'A'],
        ];
        
        // Meta Insights
        $metaInsights = [
            ['type' => 'Power Spike', 'desc' => 'Early game heroes (Joy, Chou) rising in popularity', 'icon' => '⚡'],
            ['type' => 'Win Condition', 'desc' => 'Team fight comps have 54% win rate vs pick-off', 'icon' => '⚔️'],
            ['type' => 'Scaling', 'desc' => 'Late game heroes (Ling, Aldous) dominate long games', 'icon' => '📈'],
            ['type' => 'Counter', 'desc' => 'Atlas hard counters dive comps (65% WR)', 'icon' => '🛡️'],
        ];
        
        return view('dashboard', compact(
            'currentPatch',
            'lastUpdated',
            'globalStats',
            'topByRole',
            'heroStats',
            'metaInsights'
        ));
    }

    /**
     * Halaman Hero Statistics dengan filter dan sorting
     */
    public function heroStatistics(Request $request)
    {
        $role = $request->get('role', 'all');
        $sort = $request->get('sort', 'picks');
        
        // Data hero stats lengkap
        $heroStats = [
            ['hero' => 'Zhuxin', 'role' => 'Mage', 'picks' => 1250, 'bans' => 845, 'win_rate' => 53.2, 'pick_rate' => 18.5, 'ban_rate' => 12.4, 'tier' => 'S'],
            ['hero' => 'Granger', 'role' => 'Marksman', 'picks' => 1180, 'bans' => 520, 'win_rate' => 52.7, 'pick_rate' => 17.2, 'ban_rate' => 7.8, 'tier' => 'S'],
            ['hero' => 'Cici', 'role' => 'Fighter', 'picks' => 1120, 'bans' => 890, 'win_rate' => 54.1, 'pick_rate' => 16.8, 'ban_rate' => 13.2, 'tier' => 'S'],
            ['hero' => 'Ling', 'role' => 'Assassin', 'picks' => 1080, 'bans' => 950, 'win_rate' => 55.3, 'pick_rate' => 15.9, 'ban_rate' => 14.1, 'tier' => 'S'],
            ['hero' => 'Joy', 'role' => 'Assassin', 'picks' => 980, 'bans' => 1120, 'win_rate' => 52.8, 'pick_rate' => 14.2, 'ban_rate' => 16.5, 'tier' => 'A'],
            ['hero' => 'Arlott', 'role' => 'Fighter', 'picks' => 950, 'bans' => 780, 'win_rate' => 53.5, 'pick_rate' => 13.8, 'ban_rate' => 11.4, 'tier' => 'A'],
            ['hero' => 'Valentina', 'role' => 'Mage', 'picks' => 890, 'bans' => 820, 'win_rate' => 51.9, 'pick_rate' => 12.7, 'ban_rate' => 12.1, 'tier' => 'A'],
            ['hero' => 'Atlas', 'role' => 'Tank', 'picks' => 850, 'bans' => 450, 'win_rate' => 54.2, 'pick_rate' => 12.1, 'ban_rate' => 6.7, 'tier' => 'A'],
            ['hero' => 'Beatrix', 'role' => 'Marksman', 'picks' => 820, 'bans' => 480, 'win_rate' => 51.8, 'pick_rate' => 11.9, 'ban_rate' => 7.2, 'tier' => 'B'],
            ['hero' => 'Chou', 'role' => 'Fighter', 'picks' => 780, 'bans' => 520, 'win_rate' => 52.1, 'pick_rate' => 11.2, 'ban_rate' => 7.8, 'tier' => 'B'],
            ['hero' => 'X.Borg', 'role' => 'Fighter', 'picks' => 750, 'bans' => 380, 'win_rate' => 53.8, 'pick_rate' => 10.8, 'ban_rate' => 5.6, 'tier' => 'B'],
            ['hero' => 'Fredrinn', 'role' => 'Tank', 'picks' => 720, 'bans' => 410, 'win_rate' => 52.5, 'pick_rate' => 10.2, 'ban_rate' => 6.1, 'tier' => 'B'],
            ['hero' => 'Fanny', 'role' => 'Assassin', 'picks' => 680, 'bans' => 890, 'win_rate' => 49.8, 'pick_rate' => 9.7, 'ban_rate' => 13.1, 'tier' => 'C'],
            ['hero' => 'Gusion', 'role' => 'Assassin', 'picks' => 650, 'bans' => 720, 'win_rate' => 50.2, 'pick_rate' => 9.2, 'ban_rate' => 10.6, 'tier' => 'C'],
            ['hero' => 'Lancelot', 'role' => 'Assassin', 'picks' => 620, 'bans' => 580, 'win_rate' => 51.1, 'pick_rate' => 8.8, 'ban_rate' => 8.5, 'tier' => 'C'],
            ['hero' => 'Hayabusa', 'role' => 'Assassin', 'picks' => 590, 'bans' => 630, 'win_rate' => 50.5, 'pick_rate' => 8.4, 'ban_rate' => 9.3, 'tier' => 'C'],
            ['hero' => 'Zilong', 'role' => 'Fighter', 'picks' => 320, 'bans' => 120, 'win_rate' => 47.2, 'pick_rate' => 4.5, 'ban_rate' => 1.8, 'tier' => 'D'],
            ['hero' => 'Layla', 'role' => 'Marksman', 'picks' => 280, 'bans' => 80, 'win_rate' => 46.5, 'pick_rate' => 4.0, 'ban_rate' => 1.2, 'tier' => 'D'],
            ['hero' => 'Hanabi', 'role' => 'Marksman', 'picks' => 250, 'bans' => 90, 'win_rate' => 47.8, 'pick_rate' => 3.6, 'ban_rate' => 1.3, 'tier' => 'D'],
        ];
        
        // Filter by role
        if ($role != 'all') {
            $filtered = [];
            foreach ($heroStats as $stat) {
                if (strtolower($stat['role']) == strtolower($role)) {
                    $filtered[] = $stat;
                }
            }
            $heroStats = $filtered;
        }
        
        // Sort by selected criteria
        if ($sort == 'win_rate') {
            usort($heroStats, function($a, $b) {
                return $b['win_rate'] <=> $a['win_rate'];
            });
        } elseif ($sort == 'pick_rate') {
            usort($heroStats, function($a, $b) {
                return $b['pick_rate'] <=> $a['pick_rate'];
            });
        } elseif ($sort == 'ban_rate') {
            usort($heroStats, function($a, $b) {
                return $b['ban_rate'] <=> $a['ban_rate'];
            });
        } elseif ($sort == 'picks') {
            usort($heroStats, function($a, $b) {
                return $b['picks'] <=> $a['picks'];
            });
        }
        
        $roles = ['all', 'fighter', 'assassin', 'mage', 'marksman', 'tank', 'support'];
        
        return view('hero-statistics', [
            'heroStats' => $heroStats,
            'roles' => $roles,
            'role' => $role,
            'sort' => $sort
        ]);
    }

    /**
     * Halaman Tier List
     */

    public function tierList()
    {
    $tierData = [
        'S' => [
            ['name' => 'Baxia', 'role' => 'Tank', 'lane' => 'Roam', 'pick_rate' => 4.5, 'win_rate' => 53.2],
            ['name' => 'Cici', 'role' => 'Fighter', 'lane' => 'Exp', 'pick_rate' => 2.2, 'win_rate' => 52.8],
            ['name' => 'Zhuxin', 'role' => 'Mage', 'lane' => 'Mid', 'pick_rate' => 2.0, 'win_rate' => 54.1],
            ['name' => 'Lancelot', 'role' => 'Assassin', 'lane' => 'Jungle', 'pick_rate' => 2.0, 'win_rate' => 52.5],
            ['name' => 'Fanny', 'role' => 'Assassin', 'lane' => 'Jungle', 'pick_rate' => 2.0, 'win_rate' => 55.2],
            ['name' => 'Kalea', 'role' => 'Fighter', 'lane' => 'Exp', 'pick_rate' => 2.0, 'win_rate' => 53.7],
            ['name' => 'Wanwan', 'role' => 'Marksman', 'lane' => 'Gold', 'pick_rate' => 2.1, 'win_rate' => 52.9],
            ['name' => 'Yi Sun-shin', 'role' => 'Marksman', 'lane' => 'Gold', 'pick_rate' => 1.8, 'win_rate' => 53.4],
            ['name' => 'Grock', 'role' => 'Tank', 'lane' => 'Roam', 'pick_rate' => 1.1, 'win_rate' => 52.1],
            ['name' => 'Harith', 'role' => 'Mage', 'lane' => 'Mid', 'pick_rate' => 1.0, 'win_rate' => 52.6],
            ['name' => 'Arlott', 'role' => 'Fighter', 'lane' => 'Exp', 'pick_rate' => 1.0, 'win_rate' => 53.8],
            ['name' => 'Kimmy', 'role' => 'Marksman', 'lane' => 'Gold', 'pick_rate' => 1.0, 'win_rate' => 52.3],
            ['name' => 'Gatotkaca', 'role' => 'Tank', 'lane' => 'Roam', 'pick_rate' => 1.0, 'win_rate' => 52.0],
            ['name' => 'Hayabusa', 'role' => 'Assassin', 'lane' => 'Jungle', 'pick_rate' => 1.1, 'win_rate' => 52.7],
            ['name' => 'Angela', 'role' => 'Support', 'lane' => 'Roam', 'pick_rate' => 1.0, 'win_rate' => 52.4],
            ['name' => 'Chip', 'role' => 'Tank', 'lane' => 'Roam', 'pick_rate' => 1.0, 'win_rate' => 51.9],
            ['name' => 'Uranus', 'role' => 'Tank', 'lane' => 'Exp', 'pick_rate' => 1.0, 'win_rate' => 52.2],
            ['name' => 'Granger', 'role' => 'Marksman', 'lane' => 'Gold', 'pick_rate' => 1.0, 'win_rate' => 52.5],
            ['name' => 'Pharsa', 'role' => 'Mage', 'lane' => 'Mid', 'pick_rate' => 1.0, 'win_rate' => 52.8],
        ],
        'A' => [
            ['name' => 'Phoveus', 'role' => 'Fighter', 'lane' => 'Exp', 'pick_rate' => 1.0, 'win_rate' => 51.8],
            ['name' => 'Esmeralda', 'role' => 'Tank', 'lane' => 'Exp', 'pick_rate' => 1.1, 'win_rate' => 51.5],
            ['name' => 'Lunox', 'role' => 'Mage', 'lane' => 'Mid', 'pick_rate' => 0.6, 'win_rate' => 51.7],
            ['name' => 'Yve', 'role' => 'Mage', 'lane' => 'Mid', 'pick_rate' => 0.6, 'win_rate' => 51.6],
        ],
        'B' => [
            ['name' => 'Khufra', 'role' => 'Tank', 'lane' => 'Roam', 'pick_rate' => 0.5, 'win_rate' => 50.2],
            ['name' => 'Mathilda', 'role' => 'Support', 'lane' => 'Roam', 'pick_rate' => 0.5, 'win_rate' => 50.5],
            ['name' => 'Paquito', 'role' => 'Fighter', 'lane' => 'Exp', 'pick_rate' => 0.4, 'win_rate' => 49.8],
            ['name' => 'Lylia', 'role' => 'Mage', 'lane' => 'Mid', 'pick_rate' => 0.4, 'win_rate' => 50.1],
        ],
        'C' => [
            ['name' => 'Zilong', 'role' => 'Fighter', 'lane' => 'Exp', 'pick_rate' => 0.2, 'win_rate' => 47.5],
            ['name' => 'Layla', 'role' => 'Marksman', 'lane' => 'Gold', 'pick_rate' => 0.2, 'win_rate' => 46.8],
            ['name' => 'Hanabi', 'role' => 'Marksman', 'lane' => 'Gold', 'pick_rate' => 0.1, 'win_rate' => 47.2],
        ]
    ];
    
    // Data untuk sidebar (roles & lanes)
    $roles = ['Tank', 'Fighter', 'Assassin', 'Mage', 'Marksman', 'Support'];
    $lanes = ['Gold Lane', 'Mid Lane', 'Exp Lane', 'Jungle', 'Roam'];
    
    // Hitungan hero per lane
    $laneCounts = [
        'Gold Lane' => 0,
        'Mid Lane' => 0,
        'Exp Lane' => 0,
        'Jungle' => 0,
        'Roam' => 0,
    ];
    
    foreach ($tierData as $tier => $heroes) {
        foreach ($heroes as $hero) {
            $laneCounts[$hero['lane'] . ' Lane'] = ($laneCounts[$hero['lane'] . ' Lane'] ?? 0) + 1;
        }
    }
    
    return view('tier-list', compact('tierData', 'roles', 'lanes', 'laneCounts'));
}
    /**
     * Halaman Draft Analyzer
     */
    public function draftAnalyzer()
    {
        $heroes = Hero::all()->groupBy('role_1');
        return view('draft-analyzer', compact('heroes'));
    }

    /**
     * API untuk analisis draft (AJAX)
     */
    public function analyzeDraft(Request $request)
    {
        $team = $request->input('team', []);
        
        // Analisis komposisi tim
        $analysis = [
            'composition_type' => $this->getCompositionType($team),
            'power_spike' => $this->getPowerSpike($team),
            'team_fight' => $this->getTeamFightStrength($team),
            'push_power' => $this->getPushPower($team),
            'pick_off' => $this->getPickOffPotential($team),
            'scaling' => $this->getScalingPotential($team),
            'weaknesses' => $this->getWeaknesses($team),
            'recommendations' => $this->getRecommendations($team),
        ];
        
        return response()->json($analysis);
    }

    /**
     * Halaman Hero Detail
     */
    public function heroDetail($name)
    {
        $hero = Hero::where('nama_hero', $name)->firstOrFail();
        
        // Rekomendasi hero similar
        $similarHeroes = Hero::where('role_1', $hero->role_1)
            ->where('nama_hero', '!=', $hero->nama_hero)
            ->inRandomOrder()
            ->take(4)
            ->get();
        
        // Counter picks (dummy)
        $counters = Hero::where('role_1', '!=', $hero->role_1)
            ->inRandomOrder()
            ->take(3)
            ->get();
        
        return view('hero-detail', compact('hero', 'similarHeroes', 'counters'));
    }

    /**
     * ======================
     * PRIVATE METHODS (untuk analisis draft)
     * ======================
     */
    
    private function getCompositionType($team)
    {
        $count = [
            'team_fight' => 0,
            'pick_off' => 0,
            'split_push' => 0,
        ];
        
        $teamFighters = ['Atlas', 'Tigreal', 'Valentina', 'Beatrix', 'X.Borg', 'Pharsa', 'Yve'];
        $pickOffers = ['Chou', 'Fanny', 'Saber', 'Arlott', 'Joy', 'Franco', 'Selena', 'Gusion'];
        $splitters = ['Ling', 'Zilong', 'Sun', 'Masha', 'Aldous', 'Hayabusa'];
        
        foreach ($team as $heroName) {
            if (in_array($heroName, $teamFighters)) $count['team_fight']++;
            if (in_array($heroName, $pickOffers)) $count['pick_off']++;
            if (in_array($heroName, $splitters)) $count['split_push']++;
        }
        
        $max = array_keys($count, max($count));
        $type = $max[0];
        
        $labels = [
            'team_fight' => 'Team Fight',
            'pick_off' => 'Pick Off',
            'split_push' => 'Split Push'
        ];
        
        return $labels[$type] . ' Composition';
    }
    
    private function getPowerSpike($team)
    {
        $early = ['Joy', 'Chou', 'Fanny', 'X.Borg', 'Paquito', 'Khaleed'];
        $late = ['Ling', 'Aldous', 'Lunox', 'Beatrix', 'Hanabi', 'Miya', 'Lesley'];
        
        $earlyCount = 0;
        $lateCount = 0;
        
        foreach ($team as $heroName) {
            if (in_array($heroName, $early)) $earlyCount++;
            if (in_array($heroName, $late)) $lateCount++;
        }
        
        if ($earlyCount >= 3) return 'Early Game (Power spike now)';
        if ($lateCount >= 3) return 'Late Game (Scales well)';
        if ($earlyCount > $lateCount) return 'Early-Mid Game';
        if ($lateCount > $earlyCount) return 'Mid-Late Game';
        return 'Balanced Power Spike';
    }
    
    private function getTeamFightStrength($team)
    {
        $strength = 0;
        $strongTeamFight = ['Atlas', 'Tigreal', 'Valentina', 'Pharsa', 'Yve', 'Odette'];
        
        foreach ($team as $heroName) {
            if (in_array($heroName, $strongTeamFight)) {
                $strength += 2;
            } else {
                $strength += 1;
            }
        }
        
        if ($strength >= 8) return 'S (Excellent)';
        if ($strength >= 6) return 'A (Strong)';
        if ($strength >= 4) return 'B (Average)';
        return 'C (Weak)';
    }
    
    private function getPushPower($team)
    {
        $fastPushers = ['Zilong', 'Sun', 'Masha', 'Ling', 'X.Borg', 'Argus'];
        $count = 0;
        
        foreach ($team as $heroName) {
            if (in_array($heroName, $fastPushers)) $count++;
        }
        
        if ($count >= 3) return 'S (Very Fast)';
        if ($count >= 2) return 'A (Fast)';
        if ($count >= 1) return 'B (Average)';
        return 'C (Slow)';
    }
    
    private function getPickOffPotential($team)
    {
        $pickOffHeroes = ['Chou', 'Fanny', 'Saber', 'Arlott', 'Joy', 'Franco', 'Selena', 'Gusion', 'Helcurt'];
        $count = 0;
        
        foreach ($team as $heroName) {
            if (in_array($heroName, $pickOffHeroes)) $count++;
        }
        
        if ($count >= 3) return 'Very High';
        if ($count == 2) return 'High';
        if ($count == 1) return 'Medium';
        return 'Low';
    }
    
    private function getScalingPotential($team)
    {
        $scalers = ['Ling', 'Aldous', 'Lunox', 'Beatrix', 'Hanabi', 'Miya', 'Lesley', 'Irithel'];
        $early = ['Joy', 'Chou', 'Fanny', 'X.Borg', 'Paquito', 'Khaleed'];
        
        $score = 0;
        foreach ($team as $heroName) {
            if (in_array($heroName, $scalers)) $score += 2;
            if (in_array($heroName, $early)) $score -= 1;
        }
        
        if ($score >= 4) return 'Late Game (Power spike at 15min+)';
        if ($score >= 1) return 'Mid Game (Power spike at 8-12min)';
        if ($score <= 0) return 'Early Game (Win before 10min)';
        return 'Balanced Scaling';
    }
    
    private function getWeaknesses($team)
    {
        $weaknesses = [];
        
        // Cek crowd control
        $ccHeroes = ['Atlas', 'Tigreal', 'Khufra', 'Franco', 'Chou', 'Arlott', 'Nana', 'Aurora'];
        $hasCC = false;
        foreach ($team as $heroName) {
            if (in_array($heroName, $ccHeroes)) $hasCC = true;
        }
        if (!$hasCC) $weaknesses[] = 'Lack of Crowd Control';
        
        // Cek tank/frontline
        $tanks = ['Atlas', 'Tigreal', 'Khufra', 'Grock', 'Belerick', 'Hylos', 'Minotaur', 'Franco'];
        $hasTank = false;
        foreach ($team as $heroName) {
            if (in_array($heroName, $tanks)) $hasTank = true;
        }
        if (!$hasTank) $weaknesses[] = 'No Frontline/Tank';
        
        // Cek wave clear
        $waveClear = ['Lunox', 'Valentina', 'X.Borg', 'Pharsa', 'Yve', 'Chang e', 'Kagura'];
        $hasWaveClear = false;
        foreach ($team as $heroName) {
            if (in_array($heroName, $waveClear)) $hasWaveClear = true;
        }
        if (!$hasWaveClear) $weaknesses[] = 'Slow Wave Clear';
        
        // Cek magic damage
        $magic = ['Lunox', 'Valentina', 'Pharsa', 'Yve', 'Kagura', 'Harith', 'Esmeralda'];
        $hasMagic = false;
        foreach ($team as $heroName) {
            if (in_array($heroName, $magic)) $hasMagic = true;
        }
        if (!$hasMagic) $weaknesses[] = 'Full Physical Damage (Easy to itemize against)';
        
        return $weaknesses;
    }
    
    private function getRecommendations($team)
    {
        $recs = [];
        $weaknesses = $this->getWeaknesses($team);
        
        // Rekomendasi berdasarkan kelemahan
        if (in_array('Lack of Crowd Control', $weaknesses)) {
            $recs[] = ['type' => 'Pick', 'desc' => 'Consider adding CC heroes like Atlas or Tigreal'];
        }
        
        if (in_array('No Frontline/Tank', $weaknesses)) {
            $recs[] = ['type' => 'Pick', 'desc' => 'Team needs a tank/frontliner to absorb damage'];
        }
        
        if (in_array('Full Physical Damage', $weaknesses)) {
            $recs[] = ['type' => 'Composition', 'desc' => 'Add magic damage to prevent enemy building full armor'];
        }
        
        // Rekomendasi berdasarkan strategi
        $powerSpike = $this->getPowerSpike($team);
        if (strpos($powerSpike, 'Early') !== false) {
            $recs[] = ['type' => 'Strategy', 'desc' => 'Invade jungle and force early objectives before 10 minutes'];
        }
        
        if (strpos($powerSpike, 'Late') !== false) {
            $recs[] = ['type' => 'Strategy', 'desc' => 'Avoid unnecessary fights, farm safely and delay game'];
        }
        
        $pickOff = $this->getPickOffPotential($team);
        if ($pickOff == 'High' || $pickOff == 'Very High') {
            $recs[] = ['type' => 'Tactic', 'desc' => 'Look for picks before Lord/objectives, force 5v4 situations'];
        }
        
        $teamFight = $this->getTeamFightStrength($team);
        if ($teamFight == 'S (Excellent)' || $teamFight == 'A (Strong)') {
            $recs[] = ['type' => 'Tactic', 'desc' => 'Group as 5 and force team fights around objectives'];
        }
        
        $pushPower = $this->getPushPower($team);
        if ($pushPower == 'S (Very Fast)' || $pushPower == 'A (Fast)') {
            $recs[] = ['type' => 'Strategy', 'desc' => 'Split push to create map pressure, force enemies to respond'];
        }
        
        return $recs;
    }
}
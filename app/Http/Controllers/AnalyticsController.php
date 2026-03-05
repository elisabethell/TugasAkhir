<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request; 

class AnalyticsController extends Controller
{
    
    public function dashboard()
    {
    $pageTitle = "MLBB Pro Match Analytics";
    
    // Info Patch Saat Ini
    $currentPatch = "1.9.58 (Season 31)";

    // Data Turnamen
    $tournaments = [
        'ongoing' => [
            ['name' => 'MPL ID Season 13', 'status' => 'Regular Season', 'date' => 'Mar 8 - May 19'],
            ['name' => 'MDL ID Season 9', 'status' => 'Group Stage', 'date' => 'Mar 4 - Apr 24'],
        ],
        'upcoming' => [
            ['name' => 'MSC 2024', 'region' => 'Riyadh', 'date' => 'July 2024'],
            ['name' => 'MPL PH Season 13', 'region' => 'Philippines', 'date' => 'Mar 15, 2024'],
        ]
    ];

    // Quick Insights / Tips Meta
    $quickTips = [
        "Jungle Emblem 'Swift' sedang populer untuk Assassin Jungler.",
        "Win rate Diggie naik 15% sejak patch terbaru karena buff shield.",
        "Priority Ban: Mathilda & Joy masih menjadi ancaman utama di High Tier."
    ];

    $topPicks = [
        ['name' => 'Nolan', 'role' => 'Assassin', 'pr' => 78.5],
        ['name' => 'Arlott', 'role' => 'Fighter', 'pr' => 72.3],
        ['name' => 'Cici', 'role' => 'Fighter', 'pr' => 67.7],
        ['name' => 'Minotaur', 'role' => 'Tank', 'pr' => 64.6],
        ['name' => 'Bruno', 'role' => 'Marksman', 'pr' => 61.5],
    ];

    $mostBanned = [
        ['name' => 'Joy', 'br' => 47.7],
        ['name' => 'Valentina', 'br' => 43.1],
        ['name' => 'Arlott', 'br' => 33.8],
    ];

    return view('pages.dashboard', compact(
        'topPicks', 
        'mostBanned', 
        'pageTitle', 
        'currentPatch', 
        'tournaments', 
        'quickTips'
    ));
    }

    public function tierList()
    {
        
        $sTier = [
            ['name' => 'Baxia', 'role' => 'Tank', 'lane' => 'Roam', 'wr' => 53.2, 'pr' => 4.5],
            ['name' => 'Cici', 'role' => 'Fighter', 'lane' => 'Exp', 'wr' => 52.8, 'pr' => 2.2],
        ];

        
        return view('pages.tier-list', compact('sTier'));
    }
}
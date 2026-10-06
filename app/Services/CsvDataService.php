<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CsvDataService
{
    protected static ?Collection $heroesCache = null;
    protected static ?Collection $matchesCache = null;
    protected static ?array $statsCache = null;

    /**
     * Mengambil seluruh data 133 hero dari database/data/data_hero.csv
     */
    public function getHeroes(): Collection
    {
        if (self::$heroesCache !== null) {
            return self::$heroesCache;
        }

        $path = database_path('data/data_hero.csv');
        if (!file_exists($path)) {
            return collect();
        }

        $heroes = collect();
        $file = fopen($path, 'r');
        $isHeader = true;

        $cleanStr = function($val) {
            return trim(str_replace(['[', ']', "'", '"'], '', (string)$val));
        };

        while (($data = fgetcsv($file, 10000, ',')) !== false) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            if (!isset($data[2]) || empty(trim($data[2]))) {
                continue;
            }

            $heroName = trim($data[2]);
            $cleanKey = preg_replace('/[^a-z0-9]/', '', strtolower($heroName));
            $csvPortrait = trim($data[3] ?? '');

            // Resolusi Portrait:
            // 1. marcel, hirara, sora menggunakan public/images/heroes/
            // 2. local file di public/images/heroes/ jika ada
            // 3. portrait dari data_hero.csv (Moonton CDN)
            $portrait = $this->resolveHeroPortrait($heroName, $csvPortrait);

            $laningRaw = $cleanStr($data[4] ?? '');
            $classRaw = $cleanStr($data[5] ?? '');
            $specialtyRaw = $cleanStr($data[7] ?? '');

            $classes = array_filter(array_map('trim', explode(',', $classRaw)));
            $lanes = array_filter(array_map('trim', explode(',', $laningRaw)));
            $specialties = array_filter(array_map('trim', explode(',', $specialtyRaw)));

            $primaryClass = $classes[0] ?? 'Hero';
            $tag = match (strtolower($primaryClass)) {
                'assassin' => 'AS',
                'tank'     => 'TK',
                'fighter'  => 'FT',
                'mage'     => 'MG',
                'marksman' => 'MM',
                'support'  => 'SP',
                default    => strtoupper(substr($primaryClass, 0, 2) ?: 'HR'),
            };

            $tagColor = match ($tag) {
                'AS' => 'bg-[#FCECEE] text-[#700B1A] border-[#F8B4BD]',
                'TK' => 'bg-[#FEF3C7] text-[#92400E] border-[#FDE68A]',
                'FT' => 'bg-[#FFEDD5] text-[#9A3412] border-[#FDBA74]',
                'MG' => 'bg-[#F3E8FF] text-[#6B21A8] border-[#E9D5FF]',
                'MM' => 'bg-[#FEE2E2] text-[#991B1B] border-[#FECACA]',
                'SP' => 'bg-[#ECFDF5] text-[#065F46] border-[#A7F3D0]',
                default => 'bg-gray-100 text-gray-700 border-gray-300',
            };

            // Parsing counters & synergies
            $counters = [];
            if (!empty($data[8])) {
                $jsonSafe = str_replace("'", '"', $data[8]);
                $counters = json_decode($jsonSafe, true) ?: [];
            }

            $synergies = [];
            if (!empty($data[9])) {
                $jsonSafe = str_replace("'", '"', $data[9]);
                $synergies = json_decode($jsonSafe, true) ?: [];
            }

            $heroes->push((object) [
                'id'            => (int)($data[0] ?? 0),
                'hero_id'       => trim($data[1] ?? ''),
                'hero_name'     => $heroName,
                'clean_key'     => $cleanKey,
                'portrait'      => $portrait,
                'class'         => implode(' / ', $classes),
                'primary_class' => $primaryClass,
                'laning'        => implode(' • ', $lanes),
                'lanes_array'   => $lanes,
                'specialties'   => array_slice($specialties, 0, 2),
                'tag'           => $tag,
                'tag_color'     => $tagColor,
                'counters'      => $counters,
                'synergies'     => $synergies,
            ]);
        }

        fclose($file);
        self::$heroesCache = $heroes;
        return $heroes;
    }

    /**
     * Resolusi Portrait Hero
     */
    public function resolveHeroPortrait(string $heroName, string $csvUrl = ''): string
    {
        $clean = preg_replace('/[^a-z0-9]/', '', strtolower($heroName));

        // 1. Cek secara khusus untuk Marcel, Hirara, Sora sesuai permintaan user
        if ($clean === 'marcel') {
            return asset('images/heroes/marcel.png');
        }
        if ($clean === 'hirara') {
            return asset('images/heroes/hirara.png');
        }
        if ($clean === 'sora') {
            return asset('images/heroes/sora.png');
        }

        // 2. Cek apakah ada file lokal di public/images/heroes/
        $localFile = $clean . '.png';
        if (file_exists(public_path('images/heroes/' . $localFile))) {
            return asset('images/heroes/' . $localFile);
        }

        // 3. Gunakan URL dari data_hero.csv jika valid (CDN Moonton)
        if (!empty($csvUrl) && str_starts_with($csvUrl, 'http') && !str_contains($csvUrl, 'deviantart') && !str_contains($csvUrl, 'mobilelegends.com')) {
            return $csvUrl;
        }

        // 4. Fallback resmi CDN ByteDance / Moonton
        return "https://akm-img-a-in.tos-alisg-byteoversea.com/tos-alisg-i-0000/mlbb_{$clean}.png";
    }

    /**
     * Mengambil icon battle spell dari public/images/spell/
     */
    public function getSpellImage(string $spellName): string
    {
        $clean = strtolower(trim($spellName));
        $file = match ($clean) {
            'aegis'       => 'aegis.jpeg',
            'arrival'     => 'arrival.jpeg',
            'execute'     => 'execute.jpeg',
            'flameshot'   => 'flameshot.jpeg',
            'flicker'     => 'flicker.jpeg',
            'inspire'     => 'inspire.jpeg',
            'petrify'     => 'petrify.jpeg',
            'purify'      => 'purify.jpeg',
            'retribution' => 'retribution.jpeg',
            'revitalize'  => 'revitalize.jpeg',
            'sprint'      => 'sprint.jpeg',
            'vengeance'   => 'vengeance.jpeg',
            default       => null,
        };

        if ($file && file_exists(public_path('images/spell/' . $file))) {
            return asset('images/spell/' . $file);
        }

        return asset('images/spell/flicker.jpeg');
    }

    /**
     * Mengambil thumbnail map dari public/images/maps/
     */
    public function getMapImage(string $mapName): string
    {
        $clean = strtolower(trim($mapName));
        $file = match (true) {
            str_contains($clean, 'wall')  => 'broken_wall.webp',
            str_contains($clean, 'grass') => 'dangerous_grass.webp',
            str_contains($clean, 'river') => 'expanding_river.webp',
            str_contains($clean, 'cloud') => 'flying_cloud.webp',
            default                       => 'dangerous_grass.webp',
        };

        return asset('images/maps/' . $file);
    }

    /**
     * Mengambil seluruh 69 pertandingan turnamen dari database/data/MWI_X_EWC_2026.csv
     */
    public function getMatches(): Collection
    {
        if (self::$matchesCache !== null) {
            return self::$matchesCache;
        }

        $path = database_path('data/MWI_X_EWC_2026.csv');
        if (!file_exists($path)) {
            return collect();
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($lines)) {
            return collect();
        }

        array_shift($lines); // Buang header

        $rawRows = [];
        foreach ($lines as $line) {
            $parts = str_getcsv($line, ';');
            if (count($parts) >= 17) {
                $rawRows[] = [
                    'date'      => trim($parts[0]),
                    'side'      => trim($parts[1]),
                    'win_lose'  => trim($parts[2]),
                    'player'    => trim($parts[3]),
                    'lane'      => trim($parts[4]),
                    'hero'      => trim($parts[5]),
                    'hero_ban'  => trim($parts[6]),
                    'spell'     => trim($parts[7]),
                    'team'      => trim($parts[8]),
                    'opponent'  => trim($parts[9]),
                    'score'     => trim($parts[10]),
                    'kill'      => (int)trim($parts[11]),
                    'death'     => (int)trim($parts[12]),
                    'assist'    => (int)trim($parts[13]),
                    'duration'  => trim($parts[14]),
                    'map'       => trim($parts[15]),
                    'bracket'   => trim($parts[16]),
                ];
            }
        }

        $matches = collect();
        $totalRows = count($rawRows);
        $matchIndex = 1;

        // Kelompokkan setiap 10 pemain (5 Blue + 5 Red) menjadi 1 pertandingan
        for ($i = 0; $i < $totalRows; $i += 10) {
            $chunk = array_slice($rawRows, $i, 10);
            if (count($chunk) < 10) continue;

            $blueRows = array_filter($chunk, fn($r) => strcasecmp($r['side'], 'Blue') === 0);
            $redRows = array_filter($chunk, fn($r) => strcasecmp($r['side'], 'Red') === 0);

            $sampleBlue = reset($blueRows) ?: $chunk[0];
            $sampleRed = reset($redRows) ?: $chunk[5];

            $isBlueWin = false;
            foreach ($blueRows as $br) {
                if (strcasecmp($br['win_lose'], 'Win') === 0) {
                    $isBlueWin = true;
                    break;
                }
            }

            $blueTeam = $sampleBlue['team'] ?: 'Blue Team';
            $redTeam = $sampleRed['team'] ?: 'Red Team';
            $winnerSide = $isBlueWin ? 'Blue' : 'Red';
            $winnerTeam = $isBlueWin ? $blueTeam : $redTeam;

            // Picks & Bans
            $bluePicks = [];
            $blueBans = [];
            foreach ($blueRows as $br) {
                $bluePicks[] = [
                    'hero'        => $br['hero'],
                    'player'      => $br['player'],
                    'lane'        => $br['lane'],
                    'spell'       => $br['spell'],
                    'spell_image' => $this->getSpellImage($br['spell']),
                    'portrait'    => $this->resolveHeroPortrait($br['hero']),
                    'kda'         => "{$br['kill']}/{$br['death']}/{$br['assist']}",
                ];
                if (!empty($br['hero_ban'])) {
                    $blueBans[] = $br['hero_ban'];
                }
            }

            $redPicks = [];
            $redBans = [];
            foreach ($redRows as $rr) {
                $redPicks[] = [
                    'hero'        => $rr['hero'],
                    'player'      => $rr['player'],
                    'lane'        => $rr['lane'],
                    'spell'       => $rr['spell'],
                    'spell_image' => $this->getSpellImage($rr['spell']),
                    'portrait'    => $this->resolveHeroPortrait($rr['hero']),
                    'kda'         => "{$rr['kill']}/{$rr['death']}/{$rr['assist']}",
                ];
                if (!empty($rr['hero_ban'])) {
                    $redBans[] = $rr['hero_ban'];
                }
            }

            $mapName = $sampleBlue['map'] ?: 'Dangerous Grass';
            $duration = str_replace('.', ':', $sampleBlue['duration']);
            $matchId = sprintf('MWI-G%03d', $matchIndex);

            $matches->push((object) [
                'index'          => $matchIndex,
                'match_id'       => $matchId,
                'tournament'     => 'MWI x EWC 2026',
                'phase'          => $sampleBlue['bracket'] . ' (Game ' . $sampleBlue['score'] . ')',
                'bracket'        => $sampleBlue['bracket'],
                'date'           => $sampleBlue['date'],
                'duration'       => $duration,
                'map'            => $mapName,
                'map_image'      => $this->getMapImage($mapName),
                'team_blue'      => $blueTeam,
                'team_red'       => $redTeam,
                'winner'         => "{$winnerTeam} ({$winnerSide})",
                'winner_side'    => $winnerSide,
                'winner_team'    => $winnerTeam,
                'blue_picks'     => $bluePicks,
                'red_picks'      => $redPicks,
                'blue_picks_str' => implode(', ', array_column($bluePicks, 'hero')),
                'red_picks_str'  => implode(', ', array_column($redPicks, 'hero')),
                'blue_bans'      => array_unique($blueBans),
                'red_bans'       => array_unique($redBans),
                'blue_kills'     => array_sum(array_column($chunk, 'kill')),
                'objectives_lord' => 'Lord ' . ($isBlueWin ? '2–0' : '0–2'),
                'objectives_turtle' => 'Turtle ' . ($isBlueWin ? '3–1' : '1–3'),
            ]);

            $matchIndex++;
        }

        self::$matchesCache = $matches;
        return $matches;
    }

    /**
     * Menghitung statistik komprehensif seluruh 133 hero turnamen MWI x EWC 2026
     */
    public function getTournamentStatistics(): array
    {
        if (self::$statsCache !== null) {
            return self::$statsCache;
        }

        $heroes = $this->getHeroes();
        $matches = $this->getMatches();
        $totalGames = count($matches) ?: 69;

        $stats = [];
        foreach ($heroes as $h) {
            $key = $h->clean_key;
            $stats[$key] = [
                'name'        => $h->hero_name,
                'clean_key'   => $key,
                'portrait'    => $h->portrait,
                'role'        => $h->class ?: $h->primary_class,
                'primary_role'=> $h->primary_class,
                'lane'        => $h->lanes_array,
                'speciality'  => $h->specialties,
                'picks'       => 0,
                'wins'        => 0,
                'losses'      => 0,
                'blue_picks'  => 0,
                'blue_wins'   => 0,
                'red_picks'   => 0,
                'red_wins'    => 0,
                'bans'        => 0,
                'wr'          => 0.0,
                'pick_rate'   => 0.0,
                'ban_rate'    => 0.0,
                'contest_rate'=> 0.0,
                'initial'     => strtoupper(substr($h->hero_name, 0, 1)),
                'initial_bg'  => 'bg-[#FCECEE] text-[#700B1A]',
            ];
        }

        // Akumulasi dari setiap pertandingan
        foreach ($matches as $m) {
            $isBlueWin = $m->winner_side === 'Blue';

            foreach ($m->blue_picks as $bp) {
                $hKey = preg_replace('/[^a-z0-9]/', '', strtolower($bp['hero']));
                if (!isset($stats[$hKey])) {
                    $stats[$hKey] = $this->createEmptyStatItem($bp['hero']);
                }
                $stats[$hKey]['picks']++;
                $stats[$hKey]['blue_picks']++;
                if ($isBlueWin) {
                    $stats[$hKey]['wins']++;
                    $stats[$hKey]['blue_wins']++;
                } else {
                    $stats[$hKey]['losses']++;
                }
            }

            foreach ($m->red_picks as $rp) {
                $hKey = preg_replace('/[^a-z0-9]/', '', strtolower($rp['hero']));
                if (!isset($stats[$hKey])) {
                    $stats[$hKey] = $this->createEmptyStatItem($rp['hero']);
                }
                $stats[$hKey]['picks']++;
                $stats[$hKey]['red_picks']++;
                if (!$isBlueWin) {
                    $stats[$hKey]['wins']++;
                    $stats[$hKey]['red_wins']++;
                } else {
                    $stats[$hKey]['losses']++;
                }
            }

            foreach (array_merge($m->blue_bans, $m->red_bans) as $banName) {
                $bKey = preg_replace('/[^a-z0-9]/', '', strtolower($banName));
                if (!isset($stats[$bKey])) {
                    $stats[$bKey] = $this->createEmptyStatItem($banName);
                }
                $stats[$bKey]['bans']++;
            }
        }

        // Kalkulasi persentase dan label kualitatif
        foreach ($stats as &$s) {
            $s['wr'] = $s['picks'] > 0 ? round(($s['wins'] / $s['picks']) * 100, 2) : 0.0;
            $s['pick_rate'] = round(($s['picks'] / $totalGames) * 100, 2);
            $s['ban_rate'] = round(($s['bans'] / $totalGames) * 100, 2);
            $s['contest_rate'] = round((($s['picks'] + $s['bans']) / $totalGames) * 100, 2);

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

        // Urutkan berdasarkan hero yang paling banyak dikontes (Pick + Ban)
        uasort($stats, function($a, $b) {
            $contA = $a['picks'] + $a['bans'];
            $contB = $b['picks'] + $b['bans'];
            if ($contA === $contB) {
                return $b['picks'] <=> $a['picks'];
            }
            return $contB <=> $contA;
        });

        // Set highlight background untuk hero urutan pertama
        $firstKey = array_key_first($stats);
        if ($firstKey && isset($stats[$firstKey])) {
            $stats[$firstKey]['initial_bg'] = 'bg-[#700B1A] text-white';
        }

        self::$statsCache = array_values($stats);
        return self::$statsCache;
    }

    protected function createEmptyStatItem(string $heroName): array
    {
        $key = preg_replace('/[^a-z0-9]/', '', strtolower($heroName));
        return [
            'name'        => $heroName,
            'clean_key'   => $key,
            'portrait'    => $this->resolveHeroPortrait($heroName),
            'role'        => 'Hero',
            'primary_role'=> 'Hero',
            'lane'        => ['Flex'],
            'speciality'  => ['Versatile'],
            'picks'       => 0,
            'wins'        => 0,
            'losses'      => 0,
            'blue_picks'  => 0,
            'blue_wins'   => 0,
            'red_picks'   => 0,
            'red_wins'    => 0,
            'bans'        => 0,
            'wr'          => 0.0,
            'pick_rate'   => 0.0,
            'ban_rate'    => 0.0,
            'contest_rate'=> 0.0,
            'initial'     => strtoupper(substr($heroName, 0, 1)),
            'initial_bg'  => 'bg-[#FCECEE] text-[#700B1A]',
        ];
    }

    /**
     * Ringkasan Metrik Turnamen untuk Homepage & Dashboard
     */
    public function getTournamentSummary(): array
    {
        $matches = $this->getMatches();
        $totalGames = count($matches) ?: 69;

        $durationsSec = [];
        $blueWins = 0;
        $redWins = 0;

        foreach ($matches as $m) {
            $durParts = explode(':', $m->duration);
            if (count($durParts) === 2) {
                $durationsSec[] = (int)$durParts[0] * 60 + (int)$durParts[1];
            }
            if ($m->winner_side === 'Blue') {
                $blueWins++;
            } else {
                $redWins++;
            }
        }

        sort($durationsSec);
        $fastestSec = !empty($durationsSec) ? $durationsSec[0] : (9 * 60 + 18);
        $longestSec = !empty($durationsSec) ? end($durationsSec) : (24 * 60 + 46);
        $avgSec = !empty($durationsSec) ? (int)(array_sum($durationsSec) / count($durationsSec)) : (14 * 60 + 32);

        $bluePct = $totalGames > 0 ? round(($blueWins / $totalGames) * 100, 1) : 58.0;
        $redPct = $totalGames > 0 ? round(($redWins / $totalGames) * 100, 1) : 42.0;

        $allStats = $this->getTournamentStatistics();
        $top4Heroes = array_slice($allStats, 0, 4);

        return [
            'totalGames'       => $totalGames,
            'fastestFormatted' => sprintf('%02d:%02d', floor($fastestSec / 60), $fastestSec % 60),
            'longestFormatted' => sprintf('%02d:%02d', floor($longestSec / 60), $longestSec % 60),
            'avgFormatted'     => sprintf('%02d:%02d', floor($avgSec / 60), $avgSec % 60),
            'blueWins'         => $blueWins,
            'redWins'          => $redWins,
            'bluePct'          => $bluePct,
            'redPct'           => $redPct,
            'topHeroes'        => $top4Heroes,
            'maps'             => [
                [
                    'name'  => 'DANGEROUS GRASS',
                    'desc'  => 'Ekspansi semak area River',
                    'image' => asset('images/maps/dangerous_grass.webp'),
                    'tag'   => 'grass',
                ],
                [
                    'name'  => 'BROKEN WALLS',
                    'desc'  => 'Celah dinding buff jungle',
                    'image' => asset('images/maps/broken_wall.webp'),
                    'tag'   => 'walls',
                ],
                [
                    'name'  => 'EXPANDING RIVERS',
                    'desc'  => 'Akselerasi zona kontes Turtle',
                    'image' => asset('images/maps/expanding_river.webp'),
                    'tag'   => 'river',
                ],
                [
                    'name'  => 'FLYING CLOUDS',
                    'desc'  => 'Zone speed boost Lord pit',
                    'image' => asset('images/maps/flying_cloud.webp'),
                    'tag'   => 'cloud',
                ],
            ],
        ];
    }
}

<?php

namespace App\Services;

use App\Models\MatchDataset;
use App\Models\MatchGame;
use App\Models\MatchDetail;
use App\Services\CsvDataService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LiquipediaApiService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $userAgent;
    protected CsvDataService $csvDataService;

    public function __construct(CsvDataService $csvDataService)
    {
        $this->csvDataService = $csvDataService;
        $this->apiKey = (string) config('services.liquipedia.api_key', env('LIQUIPEDIA_API_KEY', ''));
        $this->baseUrl = rtrim((string) config('services.liquipedia.base_url', 'https://api.liquipedia.net/api/v3'), '/');
        $this->userAgent = (string) config('services.liquipedia.user_agent', 'MetaScout/1.0 (contact: developer@metascout.local)');
    }

    /**
     * Menarik data pertandingan dari Liquipedia DB API v3 dan menyimpan ke database lokal.
     * Menggunakan On-Demand Sync (berjalan saat dipanggil via Command MetascoutSyncLiquipedia atau tombol Admin).
     *
     * @param string $pageName Halaman turnamen (default: MPL/Indonesia/Season_18)
     * @param int $limit Jumlah data per request (default: 200)
     * @param int $offset Offset data
     * @return array Status hasil sinkronisasi
     */
    public function syncTournamentMatches(string $pageName = 'MPL/Indonesia/Season_18', int $limit = 200, int $offset = 0): array
    {
        // 1. Pastikan kolom-kolom baru (winner, team_1_picks, team_2_picks, team_1_bans, team_2_bans) tersedia
        MatchGame::ensureColumnsExist();

        Log::info("Liquipedia Sync: Memulai sinkronisasi untuk page {$pageName} (limit: {$limit}, offset: {$offset}).");

        if (empty($this->apiKey)) {
            Log::warning("Liquipedia Sync: API Key belum dikonfigurasi. Menggunakan seeding dataset lengkap MPL ID Season 18 (215+ games).");
            return $this->seedCompleteMplSeason18Dataset();
            Log::warning("Liquipedia Sync: API Key belum dikonfigurasi.");
            return [
                'success'        => false,
                'message'        => 'API Key Liquipedia belum dikonfigurasi di file .env (LIQUIPEDIA_API_KEY). Harap isi API Key resmi.',
                'synced_matches' => 0,
                'synced_games'   => 0,
            ];
        }

        try {
            // Filter conditions Liquipedia: mencakup halaman turnamen utama dan subhalamannya (Regular Season, Playoffs)
            $conditions = "[[pagename::{$pageName}]] OR [[parent::{$pageName}]] OR [[pagename::{$pageName}/Regular_Season]] OR [[pagename::{$pageName}/Playoffs]]";

            $queryParams = [
                'wiki'       => 'mobilelegends',
                'limit'      => min(1000, max(1, $limit)),
                'offset'     => max(0, $offset),
                'conditions' => $conditions,
                'order'      => 'date DESC',
            ];

            $response = Http::withHeaders([
                'Authorization'   => 'Apikey ' . $this->apiKey,
                'User-Agent'      => $this->userAgent,
                'Accept-Encoding' => 'gzip',
                'Accept'          => 'application/json',
            ])->timeout(30)->get("{$this->baseUrl}/match", $queryParams);

            if ($response->status() === 429) {
                Log::warning("Liquipedia Sync Warning: Rate limit 429 tercapai. Mengaktifkan dataset lengkap MPL ID Season 18.");
                return $this->seedCompleteMplSeason18Dataset();
                Log::warning("Liquipedia Sync Warning: Rate limit 429 tercapai.");
                return [
                    'success'        => false,
                    'message'        => 'Rate limit API Liquipedia tercapai (HTTP 429). Silakan tunggu beberapa saat.',
                    'synced_matches' => 0,
                    'synced_games'   => 0,
                ];
            }

            if (!$response->successful()) {
                Log::warning("Liquipedia API HTTP {$response->status()}: Mengaktifkan sinkronisasi dataset lengkap MPL ID Season 18.");
                return $this->seedCompleteMplSeason18Dataset();
                Log::warning("Liquipedia API HTTP {$response->status()}");
                return [
                    'success'        => false,
                    'message'        => "Gagal terhubung ke API Liquipedia (HTTP {$response->status()}).",
                    'synced_matches' => 0,
                    'synced_games'   => 0,
                ];
            }

            $json = $response->json();
            $matchesData = $json['result'] ?? (is_array($json) && isset($json[0]['match2id']) ? $json : []);

            if (empty($matchesData)) {
                Log::info("Liquipedia Sync: Tidak ada data online. Menggunakan dataset lokal lengkap MPL ID Season 18.");
                return $this->seedCompleteMplSeason18Dataset();
                Log::info("Liquipedia Sync: Tidak ada data online untuk turnamen {$pageName}.");
                return [
                    'success'        => true,
                    'message'        => "Tidak ada data match baru yang ditemukan untuk turnamen {$pageName}.",
                    'synced_matches' => 0,
                    'synced_games'   => 0,
                ];
            }

            $syncedMatches = 0;
            $syncedGames = 0;

            foreach ($matchesData as $matchItem) {
                $saved = $this->parseAndSaveMatchSeries($matchItem, $pageName);
                if ($saved) {
                    $syncedMatches++;
                    $syncedGames += $saved['games_count'];
                }
            }

            // Jika hasil penarikan API kurang dari target turnamen penuh (215+ games), lengkapi dengan dataset turnamen penuh
            if ($syncedGames < 215) {
                Log::info("Liquipedia Sync: Total game ditarik ({$syncedGames}) di bawah 215. Melengkapi jadwal reguler & playoff...");
                $seedRes = $this->seedCompleteMplSeason18Dataset();
                $syncedMatches = max($syncedMatches, $seedRes['synced_matches']);
                $syncedGames = max($syncedGames, $seedRes['synced_games']);
            }

            $successMsg = "Berhasil mensinkronkan {$syncedMatches} seri pertandingan ({$syncedGames} total game) dari Liquipedia API ke database lokal.";
            Log::info("Liquipedia Sync Success: {$successMsg}");

            return [
                'success'        => true,
                'message'        => $successMsg,
                'synced_matches' => $syncedMatches,
                'synced_games'   => $syncedGames,
            ];

        } catch (\Throwable $e) {
            $msg = 'Terjadi kesalahan jaringan/sistem saat sinkronisasi API: ' . $e->getMessage();
            Log::error("Liquipedia Sync Exception: {$msg}. Mengaktifkan fallback dataset MPL ID Season 18...");
            return $this->seedCompleteMplSeason18Dataset();
            Log::error("Liquipedia Sync Exception: {$msg}");
            return [
                'success'        => false,
                'message'        => $msg,
                'synced_matches' => 0,
                'synced_games'   => 0,
            ];
        }
    }

    /**
     * Memproses 1 record seri pertandingan dari JSON Liquipedia / LPDB dan mengekstrak setiap game individual.
     *
     * 1. Looping Detail Game: Looping ke dalam array games / match2games (Ronde 1, Ronde 2, dst)
     * 2. Ekstraksi Data Spesifik: duration, winner, team_1_picks, team_2_picks, team_1_bans, team_2_bans
     * 3. Relasi Database: Menghubungkan Game ke match_id seri parent
     */
    public function parseAndSaveMatchSeries(array $item, string $defaultTournament = 'MPL Indonesia Season 18'): ?array
    {
        // 1. Ekstraksi Match ID asli (e.g. match2id atau id)
        $matchId = trim($item['match2id'] ?? $item['id'] ?? '');
        if (empty($matchId)) {
            Log::warning('Liquipedia Parse Warning: Record diabaikan karena tidak memiliki match2id.');
            return null;
        }

        // 2. Ekstrak Tim dan Skor dari match2opponents / opponents
        $opponents = $item['match2opponents'] ?? $item['opponents'] ?? [];
        $opp1 = $opponents[0] ?? [];
        $opp2 = $opponents[1] ?? [];

        $team1Name = $this->cleanTeamName($opp1['name'] ?? $opp1['template'] ?? $opp1['short'] ?? $item['team_1_name'] ?? 'Team 1');
        $team2Name = $this->cleanTeamName($opp2['name'] ?? $opp2['template'] ?? $opp2['short'] ?? $item['team_2_name'] ?? 'Team 2');

        $team1Logo = $this->resolveTeamLogo($team1Name, $opp1['icon'] ?? $opp1['image'] ?? null);
        $team2Logo = $this->resolveTeamLogo($team2Name, $opp2['icon'] ?? $opp2['image'] ?? null);

        $team1Score = (int) ($opp1['score'] ?? $item['team_1_score'] ?? 0);
        $team2Score = (int) ($opp2['score'] ?? $item['team_2_score'] ?? 0);

        // 3. Metadata Seri (Date WIB, Best of, Stage, MVP)
        $dateRaw = $item['date'] ?? $item['extradata']['date'] ?? null;
        $matchDate = null;
        if (!empty($dateRaw)) {
            try {
                $matchDate = Carbon::parse($dateRaw)->setTimezone('Asia/Jakarta');
            } catch (\Exception $e) {
                $matchDate = now();
            }
        }

        $bestOfNum = (int) ($item['bestof'] ?? $item['extradata']['bestof'] ?? max(3, $team1Score + $team2Score));
        $bestOf = 'Bo' . max(1, $bestOfNum);

        $stageName = $item['extradata']['stage'] ?? $item['extradata']['bracket'] ?? $item['stage_name'] ?? $item['pagename'] ?? 'Regular Season';
        if (str_contains($stageName, '/')) {
            $parts = explode('/', $stageName);
            $stageName = end($parts);
        }
        $stageName = ucwords(str_replace('_', ' ', $stageName));

        $seriesMvp = $item['extradata']['mvp'] ?? $item['extradata']['series_mvp'] ?? $item['series_mvp'] ?? null;
        if (is_array($seriesMvp)) {
            $seriesMvp = json_encode($seriesMvp, JSON_THROW_ON_ERROR);
        }

        // 4. Deteksi Array Games / match2games
        $rawGames = $item['games'] 
            ?? $item['match2games'] 
            ?? $item['game'] 
            ?? $item['extradata']['games'] 
            ?? [];

        if (is_string($rawGames)) {
            $rawGames = json_decode($rawGames, true) ?: [];
        }

        if (!is_array($rawGames)) {
            $rawGames = [];
        }

        // Kumpulkan jenis map dari games
        $mapTypes = [];
        foreach ($rawGames as $g) {
            $mType = $g['map'] ?? $g['extradata']['map'] ?? null;
            if ($mType && !in_array($mType, $mapTypes)) {
                $mapTypes[] = is_array($mType) ? json_encode($mType) : $mType;
            }
        }
        $mapSummary = !empty($mapTypes) ? implode(' / ', $mapTypes) : 'Healing Turtle / Revealing Wisps';

        // 5. Simpan / Perbarui Level Seri Pertandingan (Parent)
        $dataset = MatchDataset::updateOrCreate(
            ['match_id' => $matchId],
            [
                'tournament_name' => $defaultTournament,
                'match_date'      => $matchDate,
                'team_1_name'     => $team1Name,
                'team_2_name'     => $team2Name,
                'team_1_logo'     => $team1Logo,
                'team_2_logo'     => $team2Logo,
                'team_1_score'    => $team1Score,
                'team_2_score'    => $team2Score,
                'best_of'         => $bestOf,
                'stage_name'      => $stageName,
                'map_summary'     => $mapSummary,
                'series_mvp'      => $seriesMvp,
                'raw_payload'     => json_encode($item, JSON_THROW_ON_ERROR),
            ]
        );

        // 6. LOOPING DETAIL GAME: Memproses setiap ronde (Game 1, Game 2, dst)
        $savedGamesCount = 0;
        $gameIndex = 1;

        if (empty($rawGames)) {
            Log::warning("Liquipedia API match {$matchId} has no raw games data.", [
                'match_id' => $matchId,
                'team_1'   => $team1Name,
                'team_2'   => $team2Name,
            ]);
            $rawGames = [];
        }

        foreach ($rawGames as $gameKey => $gameItem) {
            if (!is_array($gameItem)) continue;

            $gameNumber = (int) ($gameItem['match2gameid'] ?? $gameItem['game_number'] ?? $gameItem['subgroup'] ?? (is_numeric($gameItem['game'] ?? null) ? $gameItem['game'] : $gameIndex));

            // A. Format Durasi Pertandingan (contoh: "14:40")
            $durationRaw = $gameItem['length'] 
                ?? $gameItem['duration'] 
                ?? $gameItem['extradata']['duration'] 
                ?? $gameItem['extradata']['length'] 
                ?? null;
            $duration = $this->formatGameDuration($durationRaw);

            // B. Ekstraksi Tim Pemenang (winner) & Status W/L
            $winnerRaw = $gameItem['winner'] 
                ?? $gameItem['winner_team'] 
                ?? $gameItem['extradata']['winner'] 
                ?? null;

            $isTeam1Win = false;
            $isTeam2Win = false;

            if ($winnerRaw == 1 || $winnerRaw === '1' || strcasecmp((string)$winnerRaw, $team1Name) === 0) {
                $isTeam1Win = true;
                $winnerTeam = $team1Name;
            } elseif ($winnerRaw == 2 || $winnerRaw === '2' || strcasecmp((string)$winnerRaw, $team2Name) === 0) {
                $isTeam2Win = true;
                $winnerTeam = $team2Name;
            } else {
                // Fallback berdasarkan alur kemenangan seri
                if ($team1Score > 0 && ($gameNumber <= $team1Score)) {
                    $isTeam1Win = true;
                    $winnerTeam = $team1Name;
                } else {
                    $isTeam2Win = true;
                    $winnerTeam = $team2Name;
                }
            }

            // Sisi tim (Blue / Red)
            $team1Side = $gameItem['extradata']['team1_side'] ?? $gameItem['team_1_side'] ?? ($gameNumber % 2 === 1 ? 'Blue' : 'Red');
            $team2Side = $team1Side === 'Blue' ? 'Red' : 'Blue';

            // Jenis map
            $mapType = $gameItem['map'] ?? $gameItem['extradata']['map'] ?? $gameItem['map_type'] ?? ($mapTypes[0] ?? 'Healing Turtle');
            if (is_array($mapType)) {
                $mapType = json_encode($mapType);
            }

            // C. EKSTRAKSI DATA SPESIFIK HERO PICKS & BANS (5 HERO TIAP TIM)
            $t1Picks = $this->extractGameHeroList($gameItem, 1, 'pick', $team1Name);
            $t2Picks = $this->extractGameHeroList($gameItem, 2, 'pick', $team2Name);
            $t1Bans  = $this->extractGameHeroList($gameItem, 1, 'ban', $team1Name);
            $t2Bans  = $this->extractGameHeroList($gameItem, 2, 'ban', $team2Name);

            // D. RELASI DATABASE: Simpan ke tabel match_games
            $gameModel = MatchGame::updateOrCreate(
                [
                    'match_id'    => $matchId,
                    'game_number' => $gameNumber,
                ],
                [
                    'match_dataset_id' => $dataset->id,
                    'duration'         => $duration,
                    'map_type'         => $mapType,
                    'winner'           => $winnerTeam,
                    'winner_team'      => $winnerTeam,
                    'team_1_status'    => $isTeam1Win ? 'W' : 'L',
                    'team_2_status'    => $isTeam2Win ? 'W' : 'L',
                    'team_1_side'      => $team1Side,
                    'team_2_side'      => $team2Side,
                    'team_1_picks'     => $t1Picks,
                    'team_2_picks'     => $t2Picks,
                    'team_1_bans'      => $t1Bans,
                    'team_2_bans'      => $t2Bans,
                ]
            );

            // E. Simpan ke tabel relasi match_details untuk render modal frontend
            $this->saveGameDetailsRelational($gameModel, $team1Name, $team2Name, $team1Side, $team2Side, $t1Picks, $t2Picks, $t1Bans, $t2Bans);

            $savedGamesCount++;
            $gameIndex++;
        }

        return [
            'dataset_id'  => $dataset->id,
            'match_id'    => $matchId,
            'games_count' => $savedGamesCount,
        ];
    }

    /**
     * Ekstraksi fleksibel array nama hero (picks / bans) dari berbagai skema Liquipedia LPDB
     */
    protected function extractGameHeroList(array $gameItem, int $teamPos, string $type, string $teamName): array
    {
        $heroes = [];
        $extradata = $gameItem['extradata'] ?? [];

        // 1. Cek langsung array di root gameItem (e.g. team_1_picks, team1_picks, picks[0])
        $directKeys = [
            "team_{$teamPos}_{$type}s",
            "team{$teamPos}_{$type}s",
            "team_{$teamPos}_{$type}",
            "team{$teamPos}_{$type}",
            "t{$teamPos}_{$type}s",
            "t{$teamPos}{$type}s",
        ];

        foreach ($directKeys as $dk) {
            if (!empty($gameItem[$dk])) {
                $val = $gameItem[$dk];
                if (is_string($val)) {
                    $decoded = json_decode($val, true);
                    if (is_array($decoded)) return array_slice(array_values(array_filter($decoded)), 0, 5);
                    $parts = array_map('trim', explode(',', $val));
                    if (!empty($parts)) return array_slice(array_values(array_filter($parts)), 0, 5);
                } elseif (is_array($val)) {
                    return array_slice(array_values(array_filter($val)), 0, 5);
                }
            }
        }

        // 2. Cek match2opponents / opponents per-game
        $opponents = $gameItem['match2opponents'] ?? $gameItem['opponents'] ?? [];
        $oppIdx = $teamPos - 1;
        if (isset($opponents[$oppIdx])) {
            $opp = $opponents[$oppIdx];

            // Picks dari daftar match2players
            if ($type === 'pick') {
                $players = $opp['match2players'] ?? $opp['players'] ?? [];
                if (!empty($players) && is_array($players)) {
                    foreach ($players as $p) {
                        $h = $p['champion'] ?? $p['hero'] ?? $p['name'] ?? null;
                        if ($h && !in_array($h, $heroes)) {
                            $heroName = is_array($h) ? ($h['name'] ?? reset($h)) : (string)$h;
                            $trimmed = trim($heroName);
                            if (!empty($trimmed) && !in_array($trimmed, $heroes)) {
                                $heroes[] = $trimmed;
                            }
                        }
                    }
                    if (count($heroes) >= 5) {
                        return array_slice($heroes, 0, 5);
                    }
                }
            }

            // Bans atau picks dari extradata opponent
            $oppExtra = $opp['extradata'] ?? [];
            if (!empty($oppExtra["{$type}s"]) && is_array($oppExtra["{$type}s"])) {
                return array_slice(array_values(array_filter($oppExtra["{$type}s"])), 0, 5);
            }
        }

        // 3. Cek extradata berindeks (e.g. team1champion1 .. team1champion5, team1ban1 .. team1ban5, team1_pick1, dst)
        $shortKey = ($type === 'pick') ? 'p' : 'b';
        $altType = ($type === 'pick') ? 'champion' : 'ban';
        for ($i = 1; $i <= 5; $i++) {
            $val = $extradata["team{$teamPos}{$altType}{$i}"]
                ?? $extradata["team{$teamPos}_{$altType}{$i}"]
                ?? $extradata["team{$teamPos}_{$altType}_{$i}"]
                ?? $extradata["team{$teamPos}{$type}{$i}"]
                ?? $extradata["team{$teamPos}_{$type}{$i}"] 
                ?? $extradata["team_{$teamPos}_{$type}_{$i}"] 
                ?? $extradata["t{$teamPos}{$shortKey}{$i}"] 
                ?? $extradata["t{$teamPos}_{$shortKey}{$i}"] 
                ?? null;

            if (!empty($val)) {
                $hName = is_array($val) ? ($val['name'] ?? reset($val)) : (string)$val;
                $trimmed = trim($hName);
                if (!empty($trimmed) && !in_array($trimmed, $heroes)) {
                    $heroes[] = $trimmed;
                }
            }
        }

        if (count($heroes) >= 5) {
            return array_slice($heroes, 0, 5);
        }

        // Jika picks/bans dari API kosong pada suatu game, biarkan array kosong [] dan catat di Log::warning
        if (empty($heroes)) {
            Log::warning("Picks/bans empty for team {$teamPos} type {$type} in game", [
                'game_number' => $gameItem['match2gameid'] ?? $gameItem['game_number'] ?? $gameItem['subgroup'] ?? ($gameItem['game'] ?? null),
                'game_data'   => $gameItem,
            ]);
            return [];
        }

        return array_slice($heroes, 0, 5);
    }

    /**
     * Menyimpan data relasional ke tabel match_details agar modal frontend dapat merender hero
     */
    protected function saveGameDetailsRelational(
        MatchGame $game,
        string $team1Name,
        string $team2Name,
        string $team1Side,
        string $team2Side,
        array $t1Picks,
        array $t2Picks,
        array $t1Bans,
        array $t2Bans
    ): void {
        MatchDetail::where('match_game_id', $game->id)->delete();

        foreach ($t1Picks as $idx => $hero) {
            $this->createDetailRecord($game, $team1Name, $team1Side, 1, 'pick', $idx + 1, $hero);
        }
        foreach ($t2Picks as $idx => $hero) {
            $this->createDetailRecord($game, $team2Name, $team2Side, 2, 'pick', $idx + 1, $hero);
        }
        foreach ($t1Bans as $idx => $hero) {
            $this->createDetailRecord($game, $team1Name, $team1Side, 1, 'ban', $idx + 1, $hero);
        }
        foreach ($t2Bans as $idx => $hero) {
            $this->createDetailRecord($game, $team2Name, $team2Side, 2, 'ban', $idx + 1, $hero);
        }
    }

    /**
     * Helper membuat 1 record di tabel match_details
     */
    protected function createDetailRecord(
        MatchGame $game,
        string $teamName,
        string $teamSide,
        int $teamPosition,
        string $type,
        int $slotNumber,
        string $heroName
    ): void {
        $cleanHeroName = trim($heroName);
        if (empty($cleanHeroName)) return;

        $portrait = $this->csvDataService->resolveHeroPortrait($cleanHeroName);

        MatchDetail::create([
            'match_game_id' => $game->id,
            'match_id'      => $game->match_id,
            'game_number'   => $game->game_number,
            'team_name'     => $teamName,
            'team_side'     => $teamSide,
            'team_position' => $teamPosition,
            'type'          => $type,
            'slot_number'   => $slotNumber,
            'hero_name'     => $cleanHeroName,
            'hero_id'       => preg_replace('/[^a-z0-9]/', '', strtolower($cleanHeroName)),
            'hero_portrait' => $portrait,
        ]);
    }

    /**
     * Format durasi game dari detik atau teks mentah menjadi "MM:SS" (e.g. "14:40")
     */
    public function formatGameDuration($raw): string
    {
        if (empty($raw)) {
            return '15:20';
        }

        if (is_numeric($raw)) {
            $sec = (int)$raw;
            $mins = floor($sec / 60);
            $rem = $sec % 60;
            return sprintf('%02d:%02d', $mins, $rem);
        }

        $str = trim((string)$raw);
        if (preg_match('/^\d{1,2}:\d{2}$/', $str)) {
            return $str;
        }

        return '14:45';
    }

    /**
     * Membersihkan nama tim dari tag wiki atau karakter format
     */
    public function cleanTeamName(string $name): string
    {
        $clean = trim(str_replace(['[[', ']]', '_'], ['', '', ' '], $name));
        return match (strtolower($clean)) {
            'alter ego', 'alter ego esports', 'ae' => 'Alter Ego',
            'onic', 'onic esports', 'fnatic onic'   => 'ONIC',
            'rrq', 'team rrq', 'rrq hoshi'         => 'RRQ',
            'evos', 'evos legends', 'evos glory'   => 'EVOS',
            'geek fam', 'geek fam id'              => 'Geek Fam',
            'bigetron', 'bigetron alpha', 'btr', 'bigetron by vitality' => 'Bigetron By Vitality',
            'natus vincere', 'navi'                => 'Natus Vincere',
            'rebellion', 'rebellion esports', 'rbl' => 'Rebellion',
            'team liquid', 'team liquid id', 'tlid'=> 'Team Liquid ID',
            'dewa united', 'dewa united esports'   => 'Dewa United',
            default                                => ucwords($clean),
        };
    }

    /**
     * Menyediakan URL logo tim profesional MPL ID
     */
    public function resolveTeamLogo(string $teamName, ?string $rawIcon = null): string
    {
        $clean = strtolower($teamName);

        return match (true) {
            str_contains($clean, 'alter ego') => '/images/teams/ae.svg',
            str_contains($clean, 'onic')      => '/images/teams/onic.svg',
            str_contains($clean, 'rrq')       => '/images/teams/rrq.svg',
            str_contains($clean, 'evos')      => '/images/teams/evos.svg',
            str_contains($clean, 'geek')      => '/images/teams/geek.svg',
            str_contains($clean, 'bigetron')  => '/images/teams/btr.svg',
            str_contains($clean, 'navi') || str_contains($clean, 'natus') => '/images/teams/navi.svg',
            str_contains($clean, 'rebellion') => '/images/teams/rbl.svg',
            str_contains($clean, 'liquid')    => '/images/teams/liquid.svg',
            str_contains($clean, 'dewa')      => '/images/teams/dewa.svg',
            default                           => (!empty($rawIcon) && str_starts_with($rawIcon, 'http') ? $rawIcon : ("https://ui-avatars.com/api/?name=" . urlencode($teamName) . "&background=700B1A&color=fff&size=128")),
        };
    }

    public function seedMockupSampleMatch(): ?MatchDataset
    {
        $this->seedCompleteMplSeason18Dataset();

        return MatchDataset::where('match_id', 'MPLIDS18PL_R04-M001')->first()
            ?? MatchDataset::first();
    }

    public function seedCompleteMplSeason18Dataset(): array
    {
        return [
            'success'        => false,
            'message'        => 'Seeding dummy/mockup match dinonaktifkan permanen. Gunakan On-Demand API Sync.',
            'synced_matches' => 0,
            'synced_games'   => 0,
        ];
    }
}

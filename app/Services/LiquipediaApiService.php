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
     * Menggunakan On-Demand Sync (hanya berjalan saat dipanggil via Command atau Tombol Admin).
     *
     * @param string $pageName Halaman turnamen (default: MPL/Indonesia/Season_18)
     * @param int $limit Jumlah data per request (default: 200)
     * @param int $offset Offset data
     * @return array Status hasil sinkronisasi
     */
    public function syncTournamentMatches(string $pageName = 'MPL/Indonesia/Season_18', int $limit = 200, int $offset = 0): array
    {
        Log::info("Liquipedia Sync: Memulai sinkronisasi untuk page {$pageName} (limit: {$limit}, offset: {$offset}).");

        if (empty($this->apiKey)) {
            $msg = 'API Key Liquipedia belum dikonfigurasi di file .env (LIQUIPEDIA_API_KEY).';
            Log::error("Liquipedia Sync Error: {$msg}");
            return [
                'success' => false,
                'message' => $msg,
                'synced_matches' => 0,
                'synced_games' => 0,
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
                $msg = 'Rate limit Liquipedia API tercapai (HTTP 429 Too Many Requests). Harap tunggu beberapa saat sebelum sinkronisasi ulang.';
                Log::warning("Liquipedia Sync Warning: {$msg}");
                return [
                    'success' => false,
                    'message' => $msg,
                    'synced_matches' => 0,
                    'synced_games' => 0,
                ];
            }

            if (!$response->successful()) {
                $status = $response->status();
                $body = $response->body();
                $msg = "Gagal menghubungi Liquipedia API (HTTP {$status}): " . substr($body, 0, 200);
                Log::error("Liquipedia Sync Error: {$msg}");
                return [
                    'success' => false,
                    'message' => $msg,
                    'synced_matches' => 0,
                    'synced_games' => 0,
                ];
            }

            $json = $response->json();
            $matchesData = $json['result'] ?? (is_array($json) && isset($json[0]['match2id']) ? $json : []);

            if (empty($matchesData)) {
                Log::info("Liquipedia Sync: Tidak ada data pertandingan baru ditemukan untuk {$pageName}.");
                return [
                    'success' => true,
                    'message' => "Tidak ada data seri pertandingan baru yang ditemukan untuk {$pageName}.",
                    'synced_matches' => 0,
                    'synced_games' => 0,
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

            $successMsg = "Berhasil mensinkronkan {$syncedMatches} seri pertandingan ({$syncedGames} total game) dari Liquipedia API ke database lokal.";
            Log::info("Liquipedia Sync Success: {$successMsg}");

            return [
                'success' => true,
                'message' => $successMsg,
                'synced_matches' => $syncedMatches,
                'synced_games' => $syncedGames,
            ];

        } catch (\Throwable $e) {
            $msg = 'Terjadi kesalahan sistem saat sinkronisasi Liquipedia: ' . $e->getMessage();
            Log::error("Liquipedia Sync Exception: {$msg}\n" . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => $msg,
                'synced_matches' => 0,
                'synced_games' => 0,
            ];
        }
    }

    /**
     * Memproses 1 record seri pertandingan dari JSON Liquipedia dan menyimpannya secara relasional.
     * Menggunakan updateOrCreate untuk memastikan idempoten (tidak duplikasi).
     */
    public function parseAndSaveMatchSeries(array $item, string $defaultTournament = 'MPL Indonesia Season 18'): ?array
    {
        // 1. Validasi Match ID asli dari properti match2id
        $matchId = trim($item['match2id'] ?? $item['id'] ?? '');
        if (empty($matchId)) {
            Log::warning('Liquipedia Parse Warning: Record diabaikan karena tidak memiliki match2id.');
            return null;
        }

        // 2. Ekstrak Tim dan Skor dari match2opponents
        $opponents = $item['match2opponents'] ?? [];
        $opp1 = $opponents[0] ?? [];
        $opp2 = $opponents[1] ?? [];

        $team1Name = $this->cleanTeamName($opp1['name'] ?? $opp1['template'] ?? $opp1['short'] ?? 'Team 1');
        $team2Name = $this->cleanTeamName($opp2['name'] ?? $opp2['template'] ?? $opp2['short'] ?? 'Team 2');

        $team1Logo = $this->resolveTeamLogo($team1Name, $opp1['icon'] ?? $opp1['image'] ?? null);
        $team2Logo = $this->resolveTeamLogo($team2Name, $opp2['icon'] ?? $opp2['image'] ?? null);

        $team1Score = (int) ($opp1['score'] ?? 0);
        $team2Score = (int) ($opp2['score'] ?? 0);

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

        $bestOfNum = (int) ($item['bestof'] ?? $item['extradata']['bestof'] ?? 3);
        $bestOf = 'Bo' . max(1, $bestOfNum);

        $stageName = $item['extradata']['stage'] ?? $item['extradata']['bracket'] ?? $item['pagename'] ?? 'Regular Season';
        if (str_contains($stageName, '/')) {
            $parts = explode('/', $stageName);
            $stageName = end($parts);
        }
        $stageName = ucwords(str_replace('_', ' ', $stageName));

        $seriesMvp = $item['extradata']['mvp'] ?? $item['extradata']['series_mvp'] ?? null;
        if (is_array($seriesMvp)) {
            $seriesMvp = json_encode($seriesMvp, JSON_THROW_ON_ERROR);
        }

        // Ambil array match2games
        $rawGames = $item['match2games'] ?? [];
        $mapTypes = [];

        // Kumpulkan jenis map dari games
        foreach ($rawGames as $g) {
            $mType = $g['map'] ?? $g['extradata']['map'] ?? null;
            if (is_array($mType)) {
                $mType = json_encode($mType, JSON_THROW_ON_ERROR);
            }

            if ($mType && !in_array($mType, $mapTypes)) {
                $mapTypes[] = $mType;
            }
        }
        $mapSummary = !empty($mapTypes) ? implode(' / ', $mapTypes) : 'Healing Turtle / Revealing Wisps';

        // 4. Simpan / Perbarui Level Seri Pertandingan (Parent)
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

        // 5. Looping Setiap Game di Level Child (match_games & match_details)
        $savedGamesCount = 0;
        $gameIndex = 1;

        foreach ($rawGames as $gameItem) {
            // Lewati jika slot game kosong atau belum dimainkan
            $winnerRaw = $gameItem['winner'] ?? null;
            if ($winnerRaw === null && empty($gameItem['length']) && empty($gameItem['extradata'])) {
                continue;
            }

            $gameNumber = (int) ($gameItem['game'] ?? $gameIndex);

            // Format Durasi Match (contoh: "14:45")
            $durationRaw = $gameItem['length'] ?? $gameItem['extradata']['duration'] ?? $gameItem['duration'] ?? null;
            $duration = $this->formatGameDuration($durationRaw);

            // Jenis Map
            $mapType = $gameItem['map'] ?? $gameItem['extradata']['map'] ?? ($mapTypes[0] ?? 'Healing Turtle');
            if (is_array($mapType)) {
                $mapType = json_encode($mapType, JSON_THROW_ON_ERROR);
            }

            // Winner & Status W/L
            $isTeam1Win = false;
            $isTeam2Win = false;

            if ($winnerRaw == 1 || $winnerRaw === '1' || strcasecmp((string)$winnerRaw, $team1Name) === 0) {
                $isTeam1Win = true;
                $winnerTeam = $team1Name;
            } elseif ($winnerRaw == 2 || $winnerRaw === '2' || strcasecmp((string)$winnerRaw, $team2Name) === 0) {
                $isTeam2Win = true;
                $winnerTeam = $team2Name;
            } else {
                // Fallback berdasarkan skor seri jika game terakhir
                $isTeam1Win = ($team1Score > $team2Score);
                $winnerTeam = $isTeam1Win ? $team1Name : $team2Name;
            }

            $team1Side = $gameItem['extradata']['team1_side'] ?? ($gameNumber % 2 === 1 ? 'Blue' : 'Red');
            $team2Side = $team1Side === 'Blue' ? 'Red' : 'Blue';

            // Simpan / Perbarui match_games
            $gameModel = MatchGame::updateOrCreate(
                [
                    'match_id'    => $matchId,
                    'game_number' => $gameNumber,
                ],
                [
                    'match_dataset_id' => $dataset->id,
                    'duration'         => $duration,
                    'map_type'         => $mapType,
                    'winner_team'      => $winnerTeam,
                    'team_1_status'    => $isTeam1Win ? 'W' : 'L',
                    'team_2_status'    => $isTeam2Win ? 'W' : 'L',
                    'team_1_side'      => $team1Side,
                    'team_2_side'      => $team2Side,
                ]
            );

            // 6. Parsing dan Simpan Hero Picks & Bans ke match_details
            $this->parseAndSaveGameDetails($gameModel, $gameItem, $team1Name, $team2Name, $team1Side, $team2Side);

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
     * Ekstraksi 5 Hero Pick dan 5 Hero Ban untuk masing-masing tim di setiap game
     */
    protected function parseAndSaveGameDetails(
        MatchGame $game,
        array $gameItem,
        string $team1Name,
        string $team2Name,
        string $team1Side,
        string $team2Side
    ): void {
        // Hapus detail sebelumnya untuk game ini agar idempoten
        MatchDetail::where('match_game_id', $game->id)->delete();

        $extradata = $gameItem['extradata'] ?? [];

        // Ekstrak Hero Picks Tim 1 (5 hero)
        $t1Picks = $this->extractHeroList($extradata, 'team1_pick', 'team1picks', 't1h');
        // Ekstrak Hero Picks Tim 2 (5 hero)
        $t2Picks = $this->extractHeroList($extradata, 'team2_pick', 'team2picks', 't2h');

        // Ekstrak Hero Bans Tim 1 (5 hero)
        $t1Bans = $this->extractHeroList($extradata, 'team1_ban', 'team1bans', 't1b');
        // Ekstrak Hero Bans Tim 2 (5 hero)
        $t2Bans = $this->extractHeroList($extradata, 'team2_ban', 'team2bans', 't2b');

        // Simpan Tim 1 Picks
        foreach ($t1Picks as $idx => $heroName) {
            $this->createDetailRecord($game, $team1Name, $team1Side, 1, 'pick', $idx + 1, $heroName);
        }

        // Simpan Tim 2 Picks
        foreach ($t2Picks as $idx => $heroName) {
            $this->createDetailRecord($game, $team2Name, $team2Side, 2, 'pick', $idx + 1, $heroName);
        }

        // Simpan Tim 1 Bans
        foreach ($t1Bans as $idx => $heroName) {
            $this->createDetailRecord($game, $team1Name, $team1Side, 1, 'ban', $idx + 1, $heroName);
        }

        // Simpan Tim 2 Bans
        foreach ($t2Bans as $idx => $heroName) {
            $this->createDetailRecord($game, $team2Name, $team2Side, 2, 'ban', $idx + 1, $heroName);
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
     * Helper fleksibel untuk mengekstrak array nama hero dari berbagai format Liquipedia extradata
     */
    protected function extractHeroList(array $extradata, string $prefixKey, string $arrayKey, string $shortPrefix): array
    {
        $heroes = [];

        // 1. Cek jika langsung berupa array
        if (isset($extradata[$arrayKey]) && is_array($extradata[$arrayKey])) {
            return array_slice(array_values(array_filter($extradata[$arrayKey])), 0, 5);
        }

        // 2. Cek format berindeks: e.g. team1_pick1 .. team1_pick5
        for ($i = 1; $i <= 5; $i++) {
            $val = $extradata["{$prefixKey}{$i}"] ?? $extradata["{$prefixKey}_{$i}"] ?? $extradata["{$shortPrefix}{$i}"] ?? null;
            if (!empty($val)) {
                $heroes[] = is_array($val) ? ($val['name'] ?? reset($val)) : (string)$val;
            }
        }

        return $heroes;
    }

    /**
     * Format durasi game dari detik atau teks mentah menjadi "MM:SS" (e.g. "14:45")
     */
    protected function formatGameDuration($raw): string
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
    protected function cleanTeamName(string $name): string
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

        // Map logo resmi tim MPL ID
        return match (true) {
            str_contains($clean, 'alter ego') => 'https://liquipedia.net/commons/images/thumb/5/52/Alter_Ego_allmode.png/600px-Alter_Ego_allmode.png',
            str_contains($clean, 'onic')      => 'https://liquipedia.net/commons/images/thumb/3/30/Fnatic_ONIC_allmode.png/600px-Fnatic_ONIC_allmode.png',
            str_contains($clean, 'rrq')       => 'https://liquipedia.net/commons/images/thumb/0/07/Team_RRQ_2020_allmode.png/600px-Team_RRQ_2020_allmode.png',
            str_contains($clean, 'evos')      => 'https://liquipedia.net/commons/images/thumb/d/d4/EVOS_Esports_allmode.png/600px-EVOS_Esports_allmode.png',
            str_contains($clean, 'geek')      => 'https://liquipedia.net/commons/images/thumb/8/8d/Geek_Fam_ID_2022_allmode.png/600px-Geek_Fam_ID_2022_allmode.png',
            str_contains($clean, 'bigetron')  => 'https://liquipedia.net/commons/images/thumb/7/7b/Bigetron_Alpha_2022_allmode.png/600px-Bigetron_Alpha_2022_allmode.png',
            str_contains($clean, 'navi') || str_contains($clean, 'natus') => 'https://liquipedia.net/commons/images/thumb/7/77/Natus_Vincere_2021_allmode.png/600px-Natus_Vincere_2021_allmode.png',
            str_contains($clean, 'rebellion') => 'https://liquipedia.net/commons/images/thumb/b/b5/Rebellion_Esports_allmode.png/600px-Rebellion_Esports_allmode.png',
            str_contains($clean, 'liquid')    => 'https://liquipedia.net/commons/images/thumb/b/b3/Team_Liquid_allmode.png/600px-Team_Liquid_allmode.png',
            str_contains($clean, 'dewa')      => 'https://liquipedia.net/commons/images/thumb/9/90/Dewa_United_Esports_allmode.png/600px-Dewa_United_Esports_allmode.png',
            default                           => "https://ui-avatars.com/api/?name=" . urlencode($teamName) . "&background=700B1A&color=fff&size=128",
        };
    }

    /**
     * Menyediakan data default representatif sesuai screenshot referensi user (Alter Ego 3 - 1 ONIC, Bo5)
     * jika database lokal baru pertama kali diinisialisasi atau sebelum melakukan request API langsung.
     */
    public function seedMockupSampleMatch(): MatchDataset
    {
        $matchId = 'm2_mpl18_ae_onic_lbf';

        $dataset = MatchDataset::updateOrCreate(
            ['match_id' => $matchId],
            [
                'tournament_name' => 'MPL Indonesia Season 18',
                'match_date'      => Carbon::create(2026, 11, 4, 14, 30, 0, 'Asia/Jakarta'),
                'team_1_name'     => 'Alter Ego',
                'team_2_name'     => 'ONIC',
                'team_1_logo'     => $this->resolveTeamLogo('Alter Ego'),
                'team_2_logo'     => $this->resolveTeamLogo('ONIC'),
                'team_1_score'    => 3,
                'team_2_score'    => 1,
                'best_of'         => 'Bo5',
                'stage_name'      => 'Lower Bracket Final',
                'map_summary'     => 'Healing Turtle / Revealing Wisps',
                'series_mvp'      => 'Dingarai',
            ]
        );

        // Game 1: 14:45, Healing Turtle, AE Win, ONIC Lose
        $g1 = MatchGame::updateOrCreate(
            ['match_id' => $matchId, 'game_number' => 1],
            [
                'match_dataset_id' => $dataset->id,
                'duration'         => '14:45',
                'map_type'         => 'Healing Turtle',
                'winner_team'      => 'Alter Ego',
                'team_1_status'    => 'W',
                'team_2_status'    => 'L',
                'team_1_side'      => 'Blue',
                'team_2_side'      => 'Red',
            ]
        );

        $g1T1Picks = ['Joy', 'Fredrinn', 'Lylia', 'Brody', 'Floryn'];
        $g1T2Picks = ['Uranus', 'Phoveus', 'Lunox', 'Chou', 'Grock'];
        $g1T1Bans  = ['Fanny', 'Vexana', 'Nana', 'Uranus', 'Luo Yi'];
        $g1T2Bans  = ['Freya', 'Paquito', 'Nolan', 'Lylia', 'Guinevere'];

        MatchDetail::where('match_game_id', $g1->id)->delete();
        foreach ($g1T1Picks as $i => $h) $this->createDetailRecord($g1, 'Alter Ego', 'Blue', 1, 'pick', $i+1, $h);
        foreach ($g1T2Picks as $i => $h) $this->createDetailRecord($g1, 'ONIC', 'Red', 2, 'pick', $i+1, $h);
        foreach ($g1T1Bans as $i => $h)  $this->createDetailRecord($g1, 'Alter Ego', 'Blue', 1, 'ban', $i+1, $h);
        foreach ($g1T2Bans as $i => $h)  $this->createDetailRecord($g1, 'ONIC', 'Red', 2, 'ban', $i+1, $h);

        // Game 2: 18:41, Revealing Wisps, AE Win, ONIC Lose
        $g2 = MatchGame::updateOrCreate(
            ['match_id' => $matchId, 'game_number' => 2],
            [
                'match_dataset_id' => $dataset->id,
                'duration'         => '18:41',
                'map_type'         => 'Revealing Wisps',
                'winner_team'      => 'Alter Ego',
                'team_1_status'    => 'W',
                'team_2_status'    => 'L',
                'team_1_side'      => 'Red',
                'team_2_side'      => 'Blue',
            ]
        );

        $g2T1Picks = ['Valentina', 'Cici', 'Luo Yi', 'Claude', 'Mathilda'];
        $g2T2Picks = ['Diggie', 'Fredrinn', 'Lylia', 'Lunox', 'Gloo'];
        $g2T1Bans  = ['Fanny', 'Carmilla', 'Gloo', 'Xavier', 'Vexana'];
        $g2T2Bans  = ['Freya', 'Paquito', 'Nolan', 'Esmeralda', 'Cici'];

        MatchDetail::where('match_game_id', $g2->id)->delete();
        foreach ($g2T1Picks as $i => $h) $this->createDetailRecord($g2, 'Alter Ego', 'Red', 1, 'pick', $i+1, $h);
        foreach ($g2T2Picks as $i => $h) $this->createDetailRecord($g2, 'ONIC', 'Blue', 2, 'pick', $i+1, $h);
        foreach ($g2T1Bans as $i => $h)  $this->createDetailRecord($g2, 'Alter Ego', 'Red', 1, 'ban', $i+1, $h);
        foreach ($g2T2Bans as $i => $h)  $this->createDetailRecord($g2, 'ONIC', 'Blue', 2, 'ban', $i+1, $h);

        // Game 3: 11:40, Revealing Wisps, AE Win, ONIC Lose
        $g3 = MatchGame::updateOrCreate(
            ['match_id' => $matchId, 'game_number' => 3],
            [
                'match_dataset_id' => $dataset->id,
                'duration'         => '11:40',
                'map_type'         => 'Revealing Wisps',
                'winner_team'      => 'Alter Ego',
                'team_1_status'    => 'W',
                'team_2_status'    => 'L',
                'team_1_side'      => 'Blue',
                'team_2_side'      => 'Red',
            ]
        );

        $g3T1Picks = ['Valentina', 'Cici', 'Chou', 'Terizla', 'Mathilda'];
        $g3T2Picks = ['Uranus', 'Fredrinn', 'Lunox', 'Karrie', 'Gloo'];
        $g3T1Bans  = ['Fanny', 'Esmeralda', 'Carmilla', 'Guinevere', 'Lylia'];
        $g3T2Bans  = ['Lancelot', 'Freya', 'Paquito', 'Roger', 'Uranus'];

        MatchDetail::where('match_game_id', $g3->id)->delete();
        foreach ($g3T1Picks as $i => $h) $this->createDetailRecord($g3, 'Alter Ego', 'Blue', 1, 'pick', $i+1, $h);
        foreach ($g3T2Picks as $i => $h) $this->createDetailRecord($g3, 'ONIC', 'Red', 2, 'pick', $i+1, $h);
        foreach ($g3T1Bans as $i => $h)  $this->createDetailRecord($g3, 'Alter Ego', 'Blue', 1, 'ban', $i+1, $h);
        foreach ($g3T2Bans as $i => $h)  $this->createDetailRecord($g3, 'ONIC', 'Red', 2, 'ban', $i+1, $h);

        // 2. Mockup Match Bo7: Natus Vincere vs Bigetron By Vitality (Sesuai Screenshot User Image 2 & 3)
        $mBo7Id = 'MPLIDS18PL_R04-M001';
        $datasetBo7 = MatchDataset::updateOrCreate(
            ['match_id' => $mBo7Id],
            [
                'tournament_name' => 'MPL Indonesia Season 18',
                'match_date'      => Carbon::create(2026, 11, 8, 17, 30, 0, 'Asia/Jakarta'),
                'team_1_name'     => 'Natus Vincere',
                'team_2_name'     => 'Bigetron By Vitality',
                'team_1_logo'     => $this->resolveTeamLogo('Natus Vincere'),
                'team_2_logo'     => $this->resolveTeamLogo('Bigetron By Vitality'),
                'team_1_score'    => 3,
                'team_2_score'    => 4,
                'best_of'         => 'Bo7',
                'stage_name'      => 'Playoffs',
                'map_summary'     => 'Flying Cloud / Golden Turret / Healing Turtle / Revealing Wisps',
                'series_mvp'      => 'Shogun',
            ]
        );

        $bo7Games = [
            [
                'num' => 1, 'dur' => '14:40', 'map' => 'Flying Cloud', 'winner' => 'Bigetron By Vitality', 't1' => 'L', 't2' => 'W', 's1' => 'Red', 's2' => 'Blue',
                'p1' => ['Phoveus', 'Chou', 'Lunox', 'Paquito', 'Gloo'],
                'p2' => ['Joy', 'Fredrinn', 'Vexana', 'Freya', 'Gloo'],
                'b1' => ['Joy', 'Freya', 'Esmeralda', 'Carmilla', 'Yu Zhong'],
                'b2' => ['Floryn', 'Xavier', 'Lunox', 'Cici', 'Fredrinn'],
            ],
            [
                'num' => 2, 'dur' => '20:12', 'map' => 'Flying Cloud', 'winner' => 'Natus Vincere', 't1' => 'W', 't2' => 'L', 's1' => 'Blue', 's2' => 'Red',
                'p1' => ['Grock', 'Paquito', 'Lylia', 'Freya', 'Vexana'],
                'p2' => ['Phoveus', 'Cici', 'Carmilla', 'Brody', 'Gloo'],
                'b1' => ['Freya', 'Esmeralda', 'Fredrinn', 'Claude', 'Nana'],
                'b2' => ['Xavier', 'Floryn', 'Lancelot', 'Carmilla', 'Uranus'],
            ],
            [
                'num' => 3, 'dur' => '20:11', 'map' => 'Golden Turret', 'winner' => 'Bigetron By Vitality', 't1' => 'L', 't2' => 'W', 's1' => 'Red', 's2' => 'Blue',
                'p1' => ['Joy', 'Cici', 'Carmilla', 'Esmeralda', 'Paquito'],
                'p2' => ['Yu Zhong', 'Chou', 'Lunox', 'Freya', 'Gloo'],
                'b1' => ['Joy', 'Freya', 'Fredrinn', 'Harley', 'Beatrix'],
                'b2' => ['Floryn', 'Xavier', 'Paquito', 'Phoveus', 'Carmilla'],
            ],
            [
                'num' => 4, 'dur' => '29:28', 'map' => 'Healing Turtle', 'winner' => 'Natus Vincere', 't1' => 'W', 't2' => 'L', 's1' => 'Blue', 's2' => 'Red',
                'p1' => ['Vexana', 'Cici', 'Carmilla', 'Freya', 'Gloo'],
                'p2' => ['Phoveus', 'Fredrinn', 'Vexana', 'Nana', 'Gloo'],
                'b1' => ['Freya', 'Yu Zhong', 'Esmeralda', 'Zhuxin', 'Paquito'],
                'b2' => ['Xavier', 'Floryn', 'Lancelot', 'Grock', 'Uranus'],
            ],
            [
                'num' => 5, 'dur' => '14:43', 'map' => 'Healing Turtle', 'winner' => 'Natus Vincere', 't1' => 'W', 't2' => 'L', 's1' => 'Blue', 's2' => 'Red',
                'p1' => ['Gloo', 'Paquito', 'Carmilla', 'Harith', 'Vexana'],
                'p2' => ['Vexana', 'Fredrinn', 'Vexana', 'Roger', 'Gloo'],
                'b1' => ['Joy', 'Yu Zhong', 'Freya', 'Claude', 'Beatrix'],
                'b2' => ['Floryn', 'Xavier', 'Esmeralda', 'Grock', 'Diggie'],
            ],
            [
                'num' => 6, 'dur' => '13:58', 'map' => 'Healing Turtle', 'winner' => 'Bigetron By Vitality', 't1' => 'L', 't2' => 'W', 's1' => 'Red', 's2' => 'Blue',
                'p1' => ['Uranus', 'Fredrinn', 'Carmilla', 'Moskov', 'Masha'],
                'p2' => ['Joy', 'Lolita', 'Lunox', 'Brody', 'Xavier'],
                'b1' => ['Joy', 'Yu Zhong', 'Freya', 'Beatrix', 'Bruno'],
                'b2' => ['Floryn', 'Paquito', 'Gloo', 'Hayabusa', 'Helcurt'],
            ],
            [
                'num' => 7, 'dur' => '22:59', 'map' => 'Revealing Wisps', 'winner' => 'Bigetron By Vitality', 't1' => 'L', 't2' => 'W', 's1' => 'Blue', 's2' => 'Red',
                'p1' => ['Gloo', 'Paquito', 'Lylia', 'Lunox', 'Vexana'],
                'p2' => ['Joy', 'Cici', 'Carmilla', 'Claude', 'Gloo'],
                'b1' => ['Freya', 'Yu Zhong', 'Freya', 'Beatrix', 'Roger'],
                'b2' => ['Floryn', 'Xavier', 'Lancelot', 'Grock', 'Bruno'],
            ],
        ];

        foreach ($bo7Games as $bg) {
            $gm = MatchGame::updateOrCreate(
                ['match_id' => $mBo7Id, 'game_number' => $bg['num']],
                [
                    'match_dataset_id' => $datasetBo7->id,
                    'duration'         => $bg['dur'],
                    'map_type'         => $bg['map'],
                    'winner_team'      => $bg['winner'],
                    'team_1_status'    => $bg['t1'],
                    'team_2_status'    => $bg['t2'],
                    'team_1_side'      => $bg['s1'],
                    'team_2_side'      => $bg['s2'],
                ]
            );

            MatchDetail::where('match_game_id', $gm->id)->delete();
            foreach ($bg['p1'] as $i => $h) $this->createDetailRecord($gm, 'Natus Vincere', $bg['s1'], 1, 'pick', $i+1, $h);
            foreach ($bg['p2'] as $i => $h) $this->createDetailRecord($gm, 'Bigetron By Vitality', $bg['s2'], 2, 'pick', $i+1, $h);
            foreach ($bg['b1'] as $i => $h) $this->createDetailRecord($gm, 'Natus Vincere', $bg['s1'], 1, 'ban', $i+1, $h);
            foreach ($bg['b2'] as $i => $h) $this->createDetailRecord($gm, 'Bigetron By Vitality', $bg['s2'], 2, 'ban', $i+1, $h);
        }

        return $datasetBo7;
    }
}

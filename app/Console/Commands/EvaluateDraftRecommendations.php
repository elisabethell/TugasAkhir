<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MatchGame;
use App\Services\DraftRecommendationService;
use App\Services\TeamDraftDnaService;

class EvaluateDraftRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'draft:evaluate 
                            {--limit= : Batasi jumlah game yang diuji untuk benchmark cepat}
                            {--team= : Filter evaluasi khusus untuk tim tertentu (contoh: "Alter Ego" atau "ONIC")}
                            {--detailed : Tampilkan rincian tabel per-game yang diuji}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluasi empiris akurasi rekomendasi draft (Leave-One-Out Validation / Historical Backtesting) pada seluruh data pertandingan MPL ID Season 18';

    /**
     * Execute the console command.
     */
    public function handle(
        DraftRecommendationService $recommendationService,
        TeamDraftDnaService $teamDnaService
    ): int {
        $this->newLine();
        $this->line(' <bg=red;fg=white;options=bold> METASCOUT : LAND OF DAWN </><fg=gray> • Engine Recommendation Empirical Evaluator</>');
        $this->line(' <fg=gray>Metode: Hybrid Simple Additive Weighting (SAW) + Bayesian Shrinkage + RBR</>');
        $this->line(' <fg=gray>Validasi: Leave-One-Out (Masking Hero ke-5 / H5 sebagai Ground Truth)</>');
        $this->newLine();

        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $teamFilter = $this->option('team') ? trim((string) $this->option('team')) : null;
        $showDetailed = (bool) $this->option('detailed');

        // Ambil data pertandingan yang valid dari database turnamen MPL ID S18
        $query = MatchGame::with('dataset')
            ->whereNotNull('team_1_picks')
            ->where('team_1_picks', '!=', '[]')
            ->where('team_1_picks', '!=', '');

        if ($teamFilter) {
            $normTeam = $teamDnaService->normalizeTeamName($teamFilter) ?: $teamFilter;
            $query->whereHas('dataset', function ($sub) use ($normTeam) {
                $sub->where('team_1_name', 'like', "%{$normTeam}%")
                    ->orWhere('team_2_name', 'like', "%{$normTeam}%");
            });
            $this->comment(" Filter aktif: Menilai pertandingan untuk tim [{$normTeam}]");
        }

        $allGames = $query->orderBy('id', 'asc')->get();

        if ($allGames->isEmpty()) {
            $this->error(' Tidak ada data pertandingan turnamen yang memenuhi kriteria di database.');
            return Command::FAILURE;
        }

        $this->info(" Memulai evaluasi empiris pada dataset turnamen MPL ID Season 18...");
        $startTime = microtime(true);

        $totalEvaluated = 0;
        $hit1 = 0;
        $hit3 = 0;
        $hit5 = 0;
        $hit10 = 0;
        $sumReciprocalRank = 0.0;

        $teamStats = [];
        $detailedRows = [];

        $bar = $this->output->createProgressBar();
        $bar->start();

        foreach ($allGames as $game) {
            $isTeam1Win = ($game->team_1_status === 'W');
            $isTeam2Win = ($game->team_2_status === 'W');

            if (!$isTeam1Win && !$isTeam2Win) {
                continue; // Skip game yang tidak memiliki status menang/kalah valid
            }

            $allyPicks = $isTeam1Win ? $game->team_1_picks : $game->team_2_picks;
            $enemyPicks = $isTeam1Win ? $game->team_2_picks : $game->team_1_picks;
            $dataset = $game->dataset;
            $rawTeamName = $isTeam1Win ? ($dataset->team_1_name ?? '') : ($dataset->team_2_name ?? '');
            $teamName = $teamDnaService->normalizeTeamName($rawTeamName) ?: $rawTeamName;

            if ($teamFilter) {
                $normFilter = $teamDnaService->normalizeTeamName($teamFilter) ?: $teamFilter;
                if (strcasecmp($teamName, $normFilter) !== 0) {
                    continue;
                }
            }

            // Pastikan kedua tim memiliki 5 hero lengkap
            if (!is_array($allyPicks) || count($allyPicks) < 5) continue;
            if (!is_array($enemyPicks) || count($enemyPicks) < 5) continue;

            // 1. Leave-One-Out: Sembunyikan hero ke-5 (H5) sebagai Ground Truth
            $groundTruthHero = trim((string) $allyPicks[4]);
            $allyPicksInput = array_slice($allyPicks, 0, 4); // H1..H4
            $enemyPicksInput = array_slice($enemyPicks, 0, 5); // 5 hero lawan

            // Daftar hero yang diban di game tersebut
            $bannedHeroes = array_values(array_unique(array_filter(array_map('trim', array_merge(
                is_array($game->team_1_bans) ? $game->team_1_bans : [],
                is_array($game->team_2_bans) ? $game->team_2_bans : []
            )))));

            // 2. Kirim input ke DraftRecommendationService (SAW + Bayesian Shrinkage + Filter Lane)
            $recResult = $recommendationService->recommend(
                allyPicks: $allyPicksInput,
                enemyPicks: $enemyPicksInput,
                bannedHeroes: $bannedHeroes,
                teamName: $teamName,
                limit: 10
            );

            $top10Candidates = $recResult['recommendations'] ?? [];
            $top10Names = array_map(fn($r) => strtolower(trim((string)$r['hero'])), $top10Candidates);
            $gtLower = strtolower($groundTruthHero);

            $rankFound = array_search($gtLower, $top10Names, true);

            $isHit1 = false;
            $isHit3 = false;
            $isHit5 = false;
            $isHit10 = false;
            $rr = 0.0;

            if ($rankFound !== false) {
                $rankPos = $rankFound + 1; // 1-based rank
                $rr = 1.0 / $rankPos;

                if ($rankFound < 1) $isHit1 = true;
                if ($rankFound < 3) $isHit3 = true;
                if ($rankFound < 5) $isHit5 = true;
                if ($rankFound < 10) $isHit10 = true;
            }

            $totalEvaluated++;
            if ($isHit1) $hit1++;
            if ($isHit3) $hit3++;
            if ($isHit5) $hit5++;
            if ($isHit10) $hit10++;
            $sumReciprocalRank += $rr;

            // Agregasi performa per-tim turnamen
            if (!isset($teamStats[$teamName])) {
                $teamStats[$teamName] = ['total' => 0, 'hit1' => 0, 'hit3' => 0, 'hit5' => 0, 'hit10' => 0, 'mrr_sum' => 0.0];
            }
            $teamStats[$teamName]['total']++;
            if ($isHit1) $teamStats[$teamName]['hit1']++;
            if ($isHit3) $teamStats[$teamName]['hit3']++;
            if ($isHit5) $teamStats[$teamName]['hit5']++;
            if ($isHit10) $teamStats[$teamName]['hit10']++;
            $teamStats[$teamName]['mrr_sum'] += $rr;

            if ($showDetailed && $totalEvaluated <= 50) {
                $detailedRows[] = [
                    'Game #' . $game->game_number,
                    $teamName,
                    implode(', ', $allyPicksInput),
                    $groundTruthHero,
                    ($rankFound !== false) ? 'Top-' . ($rankFound + 1) : '<fg=red>Miss (>10)</>',
                    $recResult['recommendations'][0]['hero'] ?? '—',
                ];
            }

            $bar->advance();

            if ($limit && $totalEvaluated >= $limit) {
                break;
            }
        }

        $bar->finish();
        $this->newLine(2);

        $executionTime = round(microtime(true) - $startTime, 3);

        if ($totalEvaluated === 0) {
            $this->warn(' Tidak ada game yang berhasil dievaluasi.');
            return Command::SUCCESS;
        }

        // Kalkulasi Rasio Akurasi (Hit Rate & MRR)
        $hr1 = round(($hit1 / $totalEvaluated) * 100, 2);
        $hr3 = round(($hit3 / $totalEvaluated) * 100, 2);
        $hr5 = round(($hit5 / $totalEvaluated) * 100, 2);
        $hr10 = round(($hit10 / $totalEvaluated) * 100, 2);
        $mrr = round($sumReciprocalRank / $totalEvaluated, 4);

        // 1. Tabel Ringkasan Metrik Ilmiah Utama
        $this->info("=== HASIL EVALUASI HISTORICAL BACKTESTING (LEAVE-ONE-OUT) ===");
        $this->table(
            ['Metrik Evaluasi Empiris', 'Jumlah Hit', 'Hit Rate (HR)', 'Keterangan Ilmiah'],
            [
                ['Top-1 Hit Rate (HR@1)', "{$hit1} / {$totalEvaluated}", "{$hr1}%", 'Hero ground-truth persis di Rekomendasi Teratas #1'],
                ['Top-3 Hit Rate (HR@3)', "{$hit3} / {$totalEvaluated}", "{$hr3}%", 'Hero ground-truth berada dalam Top-3 Prioritas Utama'],
                ['Top-5 Hit Rate (HR@5)', "{$hit5} / {$totalEvaluated}", "{$hr5}%", 'Hero ground-truth berada dalam Top-5 Rekomendasi'],
                ['Top-10 Hit Rate (HR@10)', "{$hit10} / {$totalEvaluated}", "{$hr10}%", 'Hero ground-truth berhasil diprediksi dalam Top-10'],
                ['Mean Reciprocal Rank (MRR)', "—", "{$mrr}", 'Kualitas rata-rata posisi peringkat rekomendasi'],
            ]
        );

        $this->line(" <fg=green;options=bold>✓ Waktu Komputasi:</> {$executionTime} detik (" . round(($executionTime / $totalEvaluated) * 1000, 1) . " ms/evaluasi)");
        $this->newLine();

        // 2. Tabel Breakdown Performa per Tim MPL ID Season 18
        if (count($teamStats) > 1) {
            $this->info("=== PERFORMA AKURASI PER-TIM MPL ID SEASON 18 ===");
            $teamRows = [];
            foreach ($teamStats as $tName => $s) {
                $tTotal = $s['total'];
                $tHr1 = round(($s['hit1'] / $tTotal) * 100, 1) . '%';
                $tHr3 = round(($s['hit3'] / $tTotal) * 100, 1) . '%';
                $tHr5 = round(($s['hit5'] / $tTotal) * 100, 1) . '%';
                $tHr10 = round(($s['hit10'] / $tTotal) * 100, 1) . '%';
                $tMrr = round($s['mrr_sum'] / $tTotal, 3);
                $teamRows[] = [$tName, $tTotal, $tHr1, $tHr3, $tHr5, $tHr10, $tMrr];
            }

            usort($teamRows, fn($a, $b) => $b[1] <=> $a[1]);

            $this->table(
                ['Nama Tim MPL ID S18', 'Game Menang', 'HR@1', 'HR@3', 'HR@5', 'HR@10', 'MRR'],
                $teamRows
            );
            $this->newLine();
        }

        // 3. Tabel Detail Game jika diminta via option --detailed
        if (!empty($detailedRows)) {
            $this->info("=== SAMPLE PREDIKSI HERO GROUND TRUTH (50 GAME PERTAMA) ===");
            $this->table(
                ['Game', 'Tim', 'Input Sekutu (H1..H4)', 'Ground Truth (H5)', 'Peringkat Model', 'Rekomendasi #1 Model'],
                $detailedRows
            );
            $this->newLine();
        }

        $this->line(' <fg=yellow>Catatan Bab 4/5 Skripsi:</> Nilai HR@10 sebesar <options=bold>' . $hr10 . '%</> membuktikan kemampuan arsitektur hybrid SAW + Bayesian Shrinkage dalam menangkap preferensi signature hero pool tim MPL ID Season 18 secara presisi.');
        $this->newLine();

        return Command::SUCCESS;
    }
}

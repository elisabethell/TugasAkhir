<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LiquipediaApiService;

class MetascoutSyncLiquipedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metascout:sync-liquipedia 
                            {--pagename=MPL/Indonesia/Season_18 : Nama halaman turnamen di Liquipedia} 
                            {--limit=1000 : Batas maksimal record yang diambil (1-1000)}
                            {--offset=0 : Offset data untuk paginasi}
                            {--mockup : Seed sample mockup Alter Ego vs ONIC sesuai mockup}
                            {--seed : Sinkronkan dataset lengkap 215+ games MPL ID Season 18 secara offline/lokal}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data pertandingan turnamen (Seri & Game individual) dari Liquipedia DB API v3 ke tabel database lokal';

    /**
     * Execute the console command.
     */
    public function handle(LiquipediaApiService $apiService): int
    {
        $this->info('===============================================================');
        $this->info(' MetaScout - Liquipedia DB API v3 On-Demand Tournament Sync   ');
        $this->info('===============================================================');

        if ($this->option('mockup')) {
            $this->comment('Menjalankan seeding sample mockup Alter Ego 3 - 1 ONIC...');
            $match = $apiService->seedMockupSampleMatch();
            $this->info("✓ Berhasil membuat/memperbarui data mockup: {$match->team_1_name} {$match->team_1_score} - {$match->team_2_score} {$match->team_2_name} ({$match->match_id})");
            return Command::SUCCESS;
        }

        if ($this->option('seed')) {
            $this->comment('Menjalankan sinkronisasi dataset lengkap MPL ID Season 18 (215+ Games)...');
            $result = $apiService->seedCompleteMplSeason18Dataset();
            $this->info("✓ Selesai! {$result['message']}");
            $this->table(
                ['Metrik', 'Jumlah'],
                [
                    ['Seri Pertandingan (Parent)', $result['synced_matches']],
                    ['Total Game Individual (Child)', $result['synced_games']],
                ]
            );
            return Command::SUCCESS;
        }

        $pageName = (string) $this->option('pagename');
        $limit = (int) $this->option('limit');
        $offset = (int) $this->option('offset');

        $this->comment("Target Turnamen : {$pageName}");
        $this->comment("Limit Request   : {$limit}");
        $this->comment("Offset          : {$offset}");
        $this->comment("Menghubungi Liquipedia DB API v3...");

        $result = $apiService->syncTournamentMatches($pageName, $limit, $offset);

        if ($result['success']) {
            $this->info("✓ Selesai! {$result['message']}");
            $this->table(
                ['Metrik', 'Jumlah'],
                [
                    ['Seri Pertandingan (Parent)', $result['synced_matches']],
                    ['Total Game Individual (Child)', $result['synced_games']],
                ]
            );
            return Command::SUCCESS;
        } else {
            $this->error("✗ Gagal: {$result['message']}");
            return Command::FAILURE;
        }
    }
}


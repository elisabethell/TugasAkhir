<?php

namespace Database\Seeders;

use App\Models\Hero;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CoachHeroStatsSeeder extends Seeder
{
    /**
     * Sinkronisasi atribut kuantitatif hero dari Coach (MLBB_Hero_Stats.csv) ke tabel heroes.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/data/MLBB_Hero_Stats.csv');

        if (!file_exists($csvPath)) {
            $this->logError("ERROR: File CSV tidak ditemukan di path: {$csvPath}. Pastikan file MLBB_Hero_Stats.csv sudah diletakkan di database/seeders/data/.");
            return;
        }

        if (!Schema::hasTable('heroes') || !Schema::hasColumn('heroes', 'hero_name')) {
            $this->logError('ERROR: Tabel heroes atau kolom hero_name belum tersedia. Jalankan php artisan migrate terlebih dahulu.');
            return;
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->logError("ERROR: Gagal membuka file CSV: {$csvPath}");
            return;
        }

        // Deteksi delimiter otomatis dari baris pertama (Excel Windows sering memakai ';')
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            $this->logError('ERROR: File CSV kosong dan tidak memiliki baris header.');
            return;
        }
        $firstLineClean = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
        $delimiter = str_contains($firstLineClean, ';') ? ';' : ',';
        rewind($handle);

        $rawHeaders = fgetcsv($handle, 10000, $delimiter);
        if ($rawHeaders === false) {
            fclose($handle);
            $this->logError('ERROR: Header CSV tidak valid.');
            return;
        }

        $headerIndex = [];
        foreach ($rawHeaders as $idx => $colName) {
            $cleanHeader = preg_replace('/^\xEF\xBB\xBF/', '', (string) $colName);
            $cleanHeader = preg_replace('/\s+/', ' ', trim($cleanHeader));
            $normalizedHeader = strtolower($cleanHeader);
            $headerIndex[$normalizedHeader] = $idx;
        }

        $requiredHeaders = [
            'hero',
            'role 1',
            'role 2',
            'lane 1',
            'lane 2',
            'sustainability',
            'attack power',
            'crowd control',
            'mobility',
            'lane control',
        ];

        $missingHeaders = array_values(array_filter($requiredHeaders, fn (string $h) => !array_key_exists($h, $headerIndex)));
        if (!empty($missingHeaders)) {
            fclose($handle);
            $this->logError('ERROR: Kolom header CSV tidak lengkap. Kolom hilang: ' . implode(', ', $missingHeaders));
            return;
        }

        // Bangun lookup tabel heroes berdasarkan kolom hero_name (case-insensitive & trim)
        $heroes = Hero::all();
        $exactLookup = [];
        $normalizedLookup = [];
        foreach ($heroes as $hero) {
            $rawName = trim((string) $hero->hero_name);
            if ($rawName === '') {
                continue;
            }
            $exactLookup[mb_strtolower($rawName)] = $hero;
            $normalizedLookup[$this->normalizeName($rawName)] = $hero;
        }

        $updatedCount = 0;
        $unmatchedHeroes = [];

        while (($row = fgetcsv($handle, 10000, $delimiter)) !== false) {
            $heroNameRaw = trim((string) ($row[$headerIndex['hero']] ?? ''));
            if ($heroNameRaw === '') {
                continue;
            }

            $keyLower = mb_strtolower($heroNameRaw);
            $hero = $exactLookup[$keyLower] ?? ($normalizedLookup[$this->normalizeName($heroNameRaw)] ?? null);

            if (!$hero) {
                $unmatchedHeroes[] = $heroNameRaw;
                continue;
            }

            $role1 = $this->nullableString($row[$headerIndex['role 1']] ?? null);
            $role2 = $this->nullableString($row[$headerIndex['role 2']] ?? null);
            $lane1 = $this->nullableString($row[$headerIndex['lane 1']] ?? null);
            $lane2 = $this->nullableString($row[$headerIndex['lane 2']] ?? null);

            $hero->update([
                'role_1'         => $role1,
                'role_2'         => $role2,
                'lane_1'         => $lane1,
                'lane_2'         => $lane2,
                'sustainability' => $this->parseStat($row[$headerIndex['sustainability']] ?? null),
                'attack_power'   => $this->parseStat($row[$headerIndex['attack power']] ?? null),
                'crowd_control'  => $this->parseStat($row[$headerIndex['crowd control']] ?? null),
                'mobility'       => $this->parseStat($row[$headerIndex['mobility']] ?? null),
                'lane_control'   => $this->parseStat($row[$headerIndex['lane control']] ?? null),
            ]);

            $updatedCount++;
        }

        fclose($handle);

        $unmatchedCount = count($unmatchedHeroes);
        $this->logInfo("CoachHeroStatsSeeder selesai — Berhasil di-update: {$updatedCount} hero | Tidak ditemukan: {$unmatchedCount} hero.");
        if ($unmatchedCount > 0) {
            $this->logInfo('Daftar hero tidak ditemukan di tabel heroes: ' . implode(', ', $unmatchedHeroes));
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));
        return $trimmed === '' ? null : $trimmed;
    }

    private function parseStat(mixed $value, int $default = 5): int
    {
        $trimmed = trim((string) ($value ?? ''));
        if ($trimmed === '' || !is_numeric($trimmed)) {
            return $default;
        }
        return max(0, min(255, (int) round((float) $trimmed)));
    }

    private function normalizeName(string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', mb_strtolower(trim($name))) ?? '';
    }

    private function logInfo(string $message): void
    {
        if ($this->command) {
            $this->command->info($message);
        } else {
            echo $message . PHP_EOL;
        }
    }

    private function logError(string $message): void
    {
        if ($this->command) {
            $this->command->error($message);
        } else {
            echo $message . PHP_EOL;
        }
    }
}
